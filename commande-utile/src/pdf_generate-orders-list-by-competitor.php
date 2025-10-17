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
    $pdf->AddPage();

    $pdf->setFont( 'dejavusanscondensed', '', 10, '', true );
    $pdf->SetY( $pdf->getY() + 5 );

    foreach ( $competition_orders as $order ) /* For each order placed */
    {
      $blocks = array();
      $html = '';
      $order_data = from_pretty_json( $order['order_data'] );

      foreach ( $catalog as $block_key => $block )
      { 
        $text = "<b>{$catalog[ $block_key ]['name']}</b>";
        
        if ( $order_data[ $block_key ]['items'] )
        {
          $text .= "<ul>";

          foreach ( $order_data[ $block_key ]['items'] as $item_key => $item )
          {
            $text .= "<li>{$item['qty']} x {$catalog[ $block_key ]['items'][ $item_key ]['name']}</li>"; /* Display item quantity and name */
            
            if ( isset( $item['options'] ) ) 
            {
              $text .= "<ul>";

              foreach ( $item['options'] as $option_key => $option ) /* If item has options */
              {
                $text .= "<li>"; /* Display options selected by user */

                foreach ( $option as $selection_key => $selection )
                {
                  $text .= "{$catalog[ $block_key ]['items'][ $item_key ]['options'][ $selection_key ]['selections'][ $selection ]['name']} ; ";
                }

                $text .= "</li>";
              }

              $text .= "</ul>";
            }
          }

          $text .= "</ul>";
        }

        array_push( $blocks, $text );
      }    

      $total = $order['has_been_paid'] ? 'Payé' : number_format( $order['order_total'], 2 ) . ' €';
      $info = "<b>{$order['user_name']}</b><p>{$total}</p>";
      $rows = ! empty( $order['user_comment'] ) + ! empty( $order['admin_comment'] ) + ceil( count( $blocks ) / 2 );

      $html = "<table cellpadding=\"10\">
              <tr>
                <td rowspan=\"{$rows}\" bgcolor=\"#e4e4e4\" style=\"border: 1px solid #808080;width:20%\">{$info}</td>";

      if ( $order['admin_comment'] )
      {
        $html .= "<td colspan=\"2\" bgcolor=\"#FFC9C9\" style=\"border: 1px solid #808080;width:80%\">{$order['admin_comment']}</td>
                </tr>
                <tr>";
      }  

      if ( $order['user_comment'] )
      {
        $html .= "<td colspan=\"2\" bgcolor=\"#fdffc9\" style=\"border: 1px solid #808080;width:80%\">{$order['user_comment']}</td>
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
      $pdf->WriteLine( $html );
      $pdf->SetY( $pdf->getY() + 5 );
    }

    $conn->close();
    $pdf->Output( "{$competition_data['competition_id']}_Commandes(par compétiteurs)_" . date( 'Y-m-d' ) . ".pdf", 'I' );
  }
  else
  {
    echo "Vous n'avez pas accès à cette compétition !";
  }

?>