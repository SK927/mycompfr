<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-wcif.php';
  require_once dirname( __DIR__, 2 ) . '/src/tcpdf/tcpdf.php';

  class CUPDF extends TCPDF
  {
    public function WriteHTMLTable( $html, $x, $y )
    {
      $this->startTransaction(); 
      $start_page = $this->getPage();                       
      $this->writeHTMLCell( 0, 0, $x, $y, $html, 0, 2, false, true, 'L'  );
      $end_page = $this->getPage();

      if ($end_page != $start_page) 
      {
          $this->rollbackTransaction(true); // don't forget the true
          $this->AddPage();
          $this->writeHTMLCell( 0, 0, $x, 10, $html, 0, 2, false, true, 'L'  );
      }
      else
      {
          $this->commitTransaction();     
      } 
    }

    public function AddCompetitionName( $competition_name )
    {
      $this->setXY( 1, 1 );
      $this->SetFont( 'dejavusanscondensed', '', 6, '', true );
      $this->setTextColor( 150, 150, 150 );
      $this->Cell( 190, 6, $competition_name, 0, 0, 'L', false );
      $this->setTextColor( 0, 0, 0 );
      $this->SetFont( 'dejavusanscondensed', '', 10, '', true );
    }
  }


  /**
   * create_new_pdf(): create new PDF reference
   * @return (pdf) the PDF reference created
   */

  function create_new_pdf()
  {
    $pdf = new CUPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
    $pdf->setCreator( PDF_CREATOR );
    $pdf->setAuthor( PDF_AUTHOR );
    $pdf->setPrintHeader( false );
    $pdf->setPrintFooter( false );
    $pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
    $pdf->SetMargins( 0, 0, 0, true );
    $pdf->setAutoPageBreak( TRUE, 0 );
    $pdf->setFontSubsetting( true );

    return $pdf;
  }


  /**
   * retrieve_competitors(): get list of competitors for the competition according to their status
   * @param (string) competition_id: the ID of the competition to retrieve data for
   * @param (string) user_token: the API token provided upon login, used to retrieve private data
   * @return (array) the data retrieved via WCA API and the error generated if needed
   */

  function retrieve_competitors( $competition_id, $user_token )
  {
    [ $all_competitors, $error ] = get_competitors_from_private_wcif( $competition_id, $user_token );

    if ( ! $error )
    {
      $new_competitors = array();
      $returning_competitors = array();
      
      foreach ( $all_competitors as $competitor )
      {
        if ( $competitor['registration']['status'] == 'accepted' )
        {
          if ( $competitor['wcaId'] ) 
          {
            array_push( $returning_competitors, $competitor );
          }
          else 
          {
            array_push( $new_competitors, $competitor );
          }
        }
      }
      array_multisort( array_column( $returning_competitors, 'name' ),  SORT_ASC, $returning_competitors );
      array_multisort( array_column( $new_competitors, 'name'),  SORT_ASC, $new_competitors );
    }
    return array( $returning_competitors, $new_competitors, $error );
  }


  /**
   * print_returning_competitors(): print columns containing the name of returning competitors
   * @param (pdf) pdf: the PDF reference
   * @param (array) competitors: the list of competitors to be printed
   * @return (pdf) the PDF reference
   */

  function print_returning_competitors( $pdf, $competitors )
  {
    $column = 0;

    if ( count( $competitors ) <= 82 ) // 41 competitors leave enough space to add a few newcomers on the first page if needed
    {
      $cutting_limit = ceil( count( $competitors ) / 2 );
    }
    else 
    {
      $cutting_limit = 48; // Max number of competitors that can be displayed in one column 
    }

    while ( count( $competitors ) )
    {
      $portion = array_splice( $competitors, 0, $cutting_limit );

      if ( ($column % 2) == 0 ) // Add page every two columns printed
      {
        $pdf->AddPage();
        $pdf->AddCompetitionName( $competition_id );
        $x = 10;
      }
      else
      {
        $x = 109;
      }

      $html = "<table cellpadding=\"2\">";

      foreach( $portion as $key => $competitor )
      {
        $color = $key % 2 ? '#fff' : '#eeeeee';
        $name = 29 <= strlen( $competitor['name'] ) ? substr( $competitor['name'], 0, 28 ) . '...' : $competitor['name']; // Max size of the name that fits entirely in a cell

        $html .= "<tr>";
        $html .= "<td style=\"border:1px solid #000;width:18px;background-color:{$color}\"></td>";
        $html .= "<td style=\"border:1px solid #000;width:164px;background-color:{$color}\">{$name}</td>";
        $html .= "<td style=\"border:1px solid #000;width:70px;background-color:{$color}\">{$competitor['wcaId']}</td>";
        $html .= "</tr>";
      }

      $html .= "</table>";
      $pdf->WriteHTMLTable( $html, $x, 10 );
      $column++;
    }        
    $space_left_at_bottom = ((($x == 10) || ($x !=10 && $cutting_limit != 48)) && $y <= 232);

    return array( $pdf, $space_left_at_bottom );
  }


  /**
   * print_new_competitor_td(): print a new line in the new competitors column
   * @param (array) competitor: the current competitor's info
   * @param (int) key: the competitor's index (0-based) in the printed column
   * @return (string) the text to add to current HTML
   */

  function print_new_competitor_td( $competitor, $key )
  {
    $txt = '';
    $color = $key % 2 ? '#fff' : '#eeeeee';
    $birthdate = implode( '-', array_reverse( explode( '-', $competitor['birthdate'] ) ) );

    $txt .= "<tr>";
    $txt .= "<td style=\"border:1px solid #000;width:18px;background-color:{$color}\"></td>";
    $txt .= "<td style=\"border:1px solid #000;width:198px;background-color:{$color}\">{$competitor['name']}</td>";
    $txt .= "<td style=\"border:1px solid #000;width:216px;background-color:{$color}\">{$competitor['email']}</td>";
    $txt .= "<td style=\"border:1px solid #000;width:60px;background-color:{$color}\">{$birthdate}</td>";
    $txt .= "<td style=\"border:1px solid #000;width:18px;background-color:{$color}\">{$competitor['countryIso2']}</td>";
    $txt .= "<td style=\"border:1px solid #000;width:18px;background-color:{$color}\">{$competitor['gender']}</td>";
    $txt .= "</tr>";

    return $txt;
  }


  /**
   * print_new_competitors_table(): print the table containing new competitors information
   * @param (pdf) pdf: the PDF reference
   * @param (array) competitors: the list of competitors to be printed
   * @return (pdf) the PDF reference
   */

  function print_new_competitors_table( $pdf, $competitors, $y )
  {
    $html = "<table cellpadding=\"2\">";

    foreach( $competitors as $key => $competitor )
    {
      $html .= print_new_competitor_td( $competitor, $key );
    }

    $html .= "</table>";
    $pdf->WriteHTMLTable( $html, 10, $y );

    return $pdf;
  }
  

  /**
   * print_new_competitors(): print columns containing the information of new competitors
   * @param (pdf) pdf: the PDF reference
   * @param (array) competitors: the list of competitors to be printed
   * @return (pdf) the PDF reference
   */

  function print_new_competitors( $pdf, $competitors, $space_left = false )
  {
    if ( $competitors )
    {
      if ( ! $space_left ) // if second column has been printed and columns are 48 people in height or if current cursor position is too low
      {
        $pdf->AddPage();
        $pdf->AddCompetitionName( $competition_id );
        $y = 10;
        $cutting_limit = null;
      }
      else
      {
        $y = $pdf->getY() + 10; // Add margin
        $cutting_limit = floor( (287 - $y - 10) / 5.6 ); // 287 = column max y end ; 25 = margin + title height ; 5.6 = <td> element approx. height
      }

      $pdf->setXY(10, $y );
      $pdf->SetFont( 'dejavusanscondensed', '', 20, '', true );
      $pdf->Cell( 190, 10, 'NOUVEAUX COMPÉTITEURS', 0, 2, 'L', false );
      $pdf->SetFont( 'dejavusanscondensed', '', 10, '', true );

      if ( $cutting_limit ) // If only a bunch of new competitors can be printed at the bottom of current page, print a few competitors first
      {      
        $portion = array_splice( $competitors, 0, $cutting_limit );
        $pdf = print_new_competitors_table( $pdf, $portion, $pdf->getY() );
      }
      
      if ( count( $competitors ) ) // If there are new competitors remaining to print
      {
        $pdf = print_new_competitors_table( $pdf, $competitors, $pdf->getY() );
      }
    }
    return $pdf;
  }

?>