<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_GET['id'];

  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
    require_once dirname( __FILE__ ) . '/_functions-competition-data.php';
    require_once dirname( __FILE__ ) . '/_functions-orders.php';
    require_once dirname( __FILE__ ) . '/_functions-pdf.php';    

    $competition_data = get_competition_data( $competition_id, $conn ); 
    $catalog = from_pretty_json( $competition_data['competition_catalog'] );
    $competition_orders = get_competition_orders( $competition_id, $conn );
    $items_amount = get_items_amount( $competition_id, $conn );

    $pdf = create_new_pdf( $competition_data['competition_name'] ); 
    
    $pdf->setFont( 'dejavusanscondensed', '', 10, '', true );
    $pdf->SetY( $pdf->getY() + 5 );

    foreach ( $items_amount as $block_name => $items )
    {
      $pdf->AddPage();

      $html = "<table cellpadding=\"10\">";
      $html .= "<tr><td colspan=\"5\" style=\"border:1px solid #808080;background-color:#e4e4e4\"><b>{$block_name}</b></td></tr>";
      $i = 0;

      foreach ( $items as $item_name => $item_qty )
      {
        if ( $i % 5 == 0 )
        {
          $html .= "<tr>";
        }

        $html .= "<td style=\"text-align:center;border:1px solid #808080\">{$item_name}<br/><br/><b>{$item_qty}</b></td>";

        if ( $i % 5 == 4 OR $i == (count( $items ) - 1) )
        {
          $html .= "</tr>";
        }

        $i++;        
      }

      $html .= "</table>";
      $pdf->WriteLine( $html );
      $pdf->SetY( $pdf->getY() + 5 );
    }

    $conn->close();
    $pdf->Output( "{$competition_data['competition_id']}_Commandes_" . date( 'Y-m-d' ) . ".pdf", 'I' );
  }
  else
  {
    echo "Vous n'avez pas accès à cette compétition !";
  }

?>