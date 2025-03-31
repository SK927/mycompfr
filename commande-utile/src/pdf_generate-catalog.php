<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';
  require_once dirname( __FILE__ ) . '/_functions-competition-data.php';
  require_once dirname( __FILE__ ) . '/_functions-pdf.php';
  
  $competition_id = $_GET['id'];
  $competition_data = get_competition_data( $competition_id, $conn ); 
  $pdf = create_new_pdf( $competition_data['competition_name'] );
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
            
            if ( $selection['price'] != '0.00' ) 
            {
              $selections .= " (+{$selection['price']}€)";
            }
            
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
        $site_alias = explode( '/', $_SERVER['REQUEST_URI'] )[1];
        $image_location = "https://{$_SERVER['SERVER_NAME']}/{$site_alias}/assets/img/icons/{$item['image']}";
        $html .= "<img src=\"{$image_location}\">";
      }

      $html .= "</td>
                <td style=\"border-bottom:1px solid #006a3a;border-right: 1px solid #006a3a;width:15%;text-align:right\">" . number_format( $item['price'], 2 ) . " €</td>
              </tr>
            </table>";

      $pdf->WriteLine( $html );
    }
  }
  $conn->close();

  $pdf->Output( "{$competition_data['competition_id']}_Catalogue.pdf", 'I');

?>