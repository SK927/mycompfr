<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
  require_once '../functions/competition-data-functions.php';
  require_once dirname( __DIR__, 3 ) . '/src/tcpdf/tcpdf.php';

  class CUPDF extends TCPDF
  {
    public function write_item( $html )
    {
      $this->startTransaction(); 
      $start_page = $this->getPage();                       
      $this->writeHTMLCell( 0, 0, '', '', $html, 0, 1, false, true, 'L'  );
      $end_page = $this->getPage();

      if ($end_page != $start_page) 
      {
          $this->rollbackTransaction(true); // don't forget the true
          $this->AddPage();
          $this->writeHTMLCell( 0, 0, '', '', $html, 0, 1, false, true, 'L'  );
      }
      else
      {
          $this->commitTransaction();     
      } 
    }
  }
  
  $competition_id = $_GET['id'];
  $competition_data = get_competition_data( $competition_id, $conn ); 

  // create new PDF document
  $pdf = new CUPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

  // set document information
  $pdf->setCreator( PDF_CREATOR );
  $pdf->setAuthor( PDF_AUTHOR );

  // set default header data
  $pdf->SetHeaderData( dirname( __DIR__, 2 ) . '/assets/img/favicon.png', PDF_HEADER_LOGO_WIDTH, ' Commande Utile', "  » {$competition_data['competition_name']}", array( 0,0,0 ), array( 50,50,50 ) );

  // set header and footer fonts
  $pdf->setHeaderFont( array( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
  $pdf->setFooterFont( array( PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_MAIN ) );

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

  // set margins
  $pdf->SetMargins( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
  $pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
  $pdf->SetFooterMargin( PDF_MARGIN_FOOTER );


  // set auto page breaks
  $pdf->setAutoPageBreak( TRUE, PDF_MARGIN_BOTTOM );

  // set image scale factor
  $pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

  // ---------------------------------------------------------

  // set default font subsetting mode
  $pdf->setFontSubsetting( true );

  // Set font
  // dejavusans is a UTF-8 Unicode font, if you only need to
  // print standard ASCII chars, you can use core fonts like
  // helvetica or times to reduce file size.
  $pdf->setFont( 'dejavusans', '', 10, '', true );
  $pdf->setFillColor( 0, 106, 58 );
  $pdf->setDrawColor( 0, 106, 58 );

  foreach ( from_pretty_json( $competition_data['competition_catalog'] ) as $block )
  {
    $pdf->AddPage();
    $pdf->setCellPaddings( 2, 2, 2, 2 );
    $pdf->setTextColor( 255, 255, 255 );
    $pdf->Cell( 0, 0, $block['name'], 1, 1, 'L', true );
    $pdf->setCellPaddings( 0, 0, 0, 0 );

    $pdf->setTextColor( 0, 0, 0 );

    $html = "<table cellpadding=\"10\">";

    foreach( $block['items'] as $item )
    { 
      if ( $item['options'] ) /* If item has options display info about options */
      { 
        $item['description'] .= "<br/><br/>Options :
                                        <ul>";

        foreach ( $item['options'] as $option)
        {
          $selections = '';

          foreach ( $option['selections'] as $selection )
          {
            $selections .= $selection['name'];
            if ( $selection['price'] != '0.00' ) $selections .= " (+{$selection['price']}€)";
            $selections .= ' ; '; 
          }

          $selections = rtrim( $selections, ' ; ' );
          $item['description'] .= "<li>{$option['name']} ({$selections})</li>"; 
        }

        $item['description'] .=   "</ul>";
      }

      $html = "<table cellpadding=\"10\">
                  <tr>
                    <td style=\"border-bottom:1px solid #006a3a;border-left: 1px solid #006a3a;width:20%\">{$item['name']}</td>
                    <td style=\"border-bottom:1px solid #006a3a;width:45%\">{$item['description']}</td>
                    <td style=\"border-bottom:1px solid #006a3a;width:20%\">";

      if ( $item['image'] != '.' )
      {
        $image_location = dirname( __DIR__, 2 ) . "/assets/img/icons/{$item['image']}";
        $ext = end( explode( '.', $image_location ) );
        $image = base64_encode( file_get_contents( $image_location ) );
        $html .= "<img src=\"data:image/{$ext};base64,{$image}\">";
      }

      $html .= "</td>
                <td style=\"border-bottom:1px solid #006a3a;border-right: 1px solid #006a3a;width:15%;text-align:right\">" . number_format( $item['price'], 2 ) . " €</td>
              </tr>
            </table>";

      $pdf->write_item( $html );
    }


  }

  $conn->close();

  // Close and output PDF document
  // This method has several options, check the source code documentation for more information.
  $pdf->Output( "{$competition_data['competition_name']}_Catalogue.pdf", 'I');

?>