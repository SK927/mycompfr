<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

 $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
    require_once '../functions/competition-data-functions.php';
    require_once '../functions/orders-functions.php';
    require_once dirname( __DIR__, 3 ) . '/src/tcpdf/tcpdf.php';

    class CUPDF extends TCPDF
    {
      public function write_order( $html )
      {
        $this->startTransaction(); 
        $start_page = $this->getPage();                       
        $this->writeHTMLCell( 0, 0, '', '', $html, 0, 1, false, true, 'L'  );
        $end_page = $this->getPage();
        if  ($end_page != $start_page) {
            $this->rollbackTransaction(true); // don't forget the true
            $this->AddPage();
            $this->writeHTMLCell( 0, 0, '', '', $html, 0, 1, false, true, 'L'  );
        }else{
            $this->commitTransaction();     
        } 
        $this->Cell(0, 0, '', 0, 1, 'C', 0, '', 0);
      }
    }

    $competition_data = get_competition_data( $competition_id, $conn ); 

    // create new PDF document
    $pdf = new CUPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

    // set document information
    $pdf->setCreator( PDF_CREATOR );
    $pdf->setAuthor( PDF_AUTHOR );

    // set default header data
    $pdf->SetHeaderData( dirname( __DIR__, 2 ) . '/assets/img/favicon.png', PDF_HEADER_LOGO_WIDTH, 'Commande Utile', $competition_data['competition_name'], array( 0,0,0 ), array( 50,50,50 ) );

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
    $pdf->setFont( 'dejavusanscondensed', '', 10, '', true );

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    $query_results = get_competition_orders( $competition_id, $conn );

    while( $result_row = $query_results->fetch_assoc() ) /* For each order placed */
    {
      $order = from_pretty_json( $result_row['order_data'] ); /* Decode order Json */
      $blocks = array();
      $html = '';

      foreach ( $order as $block_name => $block_value )
      {
        $text = '';

        if ( count( $block_value ) != 1 )
        {
          $text .= "<b>{$block_name}</b><ul>";
          
          foreach ( $block_value as $item_name => $item_value )
          {
            if ( isset( $item_value['qty'] ) )
            {
              $text .= "<li>{$item_value['qty']} x {$item_name}</li>"; /* Display item quantity and name */
              
              if ( isset( $item_value['options'] ) ) 
              {
                $text .= "<ul>";

                foreach ( $item_value['options'] as $option ) /* If item has options */
                {
                  $text .= "<li>"; /* Display options selected by user */

                  foreach ( $option as $index => $opt )
                  {
                    $text .= "{$opt} ; ";
                  }
                  $text .= "</li>";
                }
                $text .= "</ul>";
              }
            }
          }
          $text .= "</ul>";

          array_push( $blocks, $text );
        }
      }    
      $total = $result_row['has_been_paid'] ? 'Payé' : number_format( $result_row['order_total'], 2 ) . ' €';
      $info = "{$result_row['user_name']}<p>{$total}</p>";
      $rows = !empty( $result_row['user_comment'] ) + ceil( count( $blocks ) / 2 );

      $html = "<table cellpadding=\"10\">
              <tr>
                <td rowspan=\"{$rows}\" bgcolor=\"#e4e4e4\" style=\"border: 1px solid #808080;width:20%\">{$info}</td>";

      if ( $result_row['user_comment'] )
      {
        $html .= "<td colspan=\"2\" bgcolor=\"#fdffc9\" style=\"border: 1px solid #808080;width:80%\">{$result_row['user_comment']}</td>
                </tr>
                <tr>";
      }      
      
      while ( $cnt = count( $blocks ) )
      {
        if( $cnt < 2 )
        {
          $html .= "<td style=\"border: 1px solid #808080;width:80%\">" . array_shift( $blocks ). "</td>
                  </tr>";
        }
        else
        {
          $html .= "<td style=\"border: 1px solid #808080;width:40%\">" . array_shift( $blocks ). "</td>
                    <td style=\"border: 1px solid #808080;width:40%\">" . array_shift( $blocks ). "</td>
                  </tr>";
          
          if ( $cnt > 2 )
          {
            $html .= "<tr>";
          }
        }
      }
      
      $html .= "</table>";
      $pdf->write_order( $html );
    }
            
    $conn->close();

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output( "{$competition_data['competition_name']}_Commandes_" . date( 'Y-m-d' ) . ".pdf", 'I');

    //============================================================+
    // END OF FILE
    //============================================================+

  }
  else
  {
    echo 'Vous n\'avez pas accès à cette compétition !';
  }

?>