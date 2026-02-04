<?php

  error_reporting( E_ERROR );
  ini_set( "display_errors", 1 );

  require_once dirname( __FILE__ ) . '/_functions.php';

  $competition_id = $_GET['id'];
  mysqli_open( $mysqli );
  $competition = get_competition_data( $competition_id, $mysqli );
  $catalog = get_catalog( $competition_id, $mysqli ); 
  $mysqli->close();

  $pdf = create_new_pdf( $competition['name'] );
  $pdf->setHtmlVSpace( array( 'ul' => array( array( 'h' => 0, 'n' => 0 ), array( 'h' => 0, 'n' => 0 ) ) ) );
  $pdf->setFillColor( 0, 106, 58 );
  $pdf->setDrawColor( 0, 106, 58 );

  foreach( $catalog as $block )
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
      if( $item['options'] ) /* If item has options display info about options */
      { 
        $item['description'] .= "<br/><br/>";

        foreach( $item['options'] as $option)
        {
          $selections = '';

          foreach( $option['selections'] as $selection )
          {
            $selections .= "<li>{$selection['name']}";
            
            if( $selection['price'] != '0.00' ) 
            {
              $selections .= " (+&nbsp;{$selection['price']}&nbsp;€)";
            }
            
            $selections .= '</li>'; 
          }
          $selections = rtrim( $selections, ' ; ' );
          $item['description'] .= "{$option['name']}<ul style=\"margin:0px;padding:0px\">{$selections}</ul>"; 
        }
      }

      $html = "<table cellpadding=\"10\">
                  <tr>
                    <td style=\"border-left: 1px solid #006a3a;border-bottom:1px solid #006a3a;width:20%\">";

      if( $item['image'] != '.' )
      {
        $site_alias = explode( '/', $_SERVER['REQUEST_URI'] )[1];
        $image_location = "https://{$_SERVER['SERVER_NAME']}/{$site_alias}/assets/img/icons/{$item['image']}";
        $html .= "<img src=\"{$image_location}\">";
      }

      $html .= "</td>
                <td style=\"border-bottom:1px solid #006a3a;width:20%\">{$item['name']}</td>
                <td style=\"border-bottom:1px solid #006a3a;width:45%\">{$item['description']}</td>
                <td style=\"border-bottom:1px solid #006a3a;border-right: 1px solid #006a3a;width:15%;text-align:right\">" . number_format( $item['price'], 2 ) . " €</td>
              </tr>
            </table>";

      $pdf->WriteLine( $html );
    }
  }
  
  ob_end_clean();
  $pdf->Output( "{$competition['name']}_Catalogue.pdf", 'I');

?>