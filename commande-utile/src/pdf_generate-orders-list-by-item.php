<?php

  error_reporting( E_ERROR );
  ini_set( "display_errors", 1 );
  
  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  
  $competition_id = $_GET['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );
    $competition = get_competition_data( $competition_id, $mysqli );
    $items_amounts = get_items_amount( $competition_id, $mysqli );
    $mysqli->close();
  
    $pdf = create_new_pdf( $competition['name'] );
    
    $pdf->setFont( 'dejavusanscondensed', '', 10, '', true );
    $pdf->SetY( $pdf->getY() + 5 );

    $pdf->AddPage();

    foreach( $items_amounts as $block )
    {
      $html = "<table cellpadding=\"10\">";
      $html .= "<tr><td colspan=\"5\" style=\"border:1px solid #808080;background-color:#e4e4e4\"><b>{$block['name']}</b></td></tr>";
      $i = 0;

      foreach( $block['items'] as $item )
      {
        if( $i % 5 == 0 )
        {
          $html .= "<tr>";
        }

        $html .= "<td style=\"text-align:center;border:1px solid #808080\">{$item['name']}<br/><br/><b>{$item['qty']}</b></td>";

        if( $i % 5 == 4 or $i == (count( $block['items'] ) - 1) )
        {
          $html .= "</tr>";
        }

        $i++;        
      }

      $html .= "</table>";
      $pdf->WriteLine( $html );
      $pdf->SetY( $pdf->getY() + 5 );
    }

    ob_end_clean();
    $pdf->Output( "{$competition['name']}_Commandes(par produits)_" . date( 'Y-m-d' ) . ".pdf", 'I' );
  }
  else
  {
    echo "ERREUR : Vous n'êtes pas connecté ou vous n'êtes pas organisateur de cette compétition !";
  }

?>