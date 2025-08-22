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

    foreach ( $catalog as $block_key => $block )
    {
      $pdf->AddPage();

  	  $html = "<table cellpadding=\"10\">";
      $html .= "<tr><td colspan=\"2\" style=\"border:1px solid #808080;background-color:#e4e4e4\"><b>{$block['name']}</b></td></tr>";
      $html .= "</table>";
	  
      $pdf->WriteLine( $html );
	  
      foreach ( $competition_orders as $order )
      { 
        $order_data = from_pretty_json( $order['order_data'] );

		    if ( $order_data[ $block_key ]['items'] )
        {
    		  $html = "<table cellpadding=\"10\"><tr>";
          $html .= "<td style=\"width:20%;border:1px solid #808080\">{$order['user_name']}</td>";
    		  $html .= "<td style=\"width:80%;border:1px solid #808080\"><ul>";

    		  $order_data = from_pretty_json( $order['order_data'] );
		         
          foreach ( $order_data[ $block_key ]['items'] as $item_key => $item )
          {
            $html .= "<li>{$item['qty']} x {$catalog[ $block_key ]['items'][ $item_key ]['name']}";
            $options = array();

            foreach ( $item['options'] as $option )
            {
              foreach ( $option as $option_key => $selection_key ) 
              {
                $id = "{$option_key}_{$selection_key}";
                $options[ $id ]++;
              }
            }

            ksort( $options );

            $temp_array = array();

            foreach ( $options as $key => $qty )
            {
              [ $option_key, $selection_key ] = explode( '_', $key );
              array_push( $temp_array, "{$qty} x {$catalog[ $block_key ]['items'][ $item_key ]['options'][ $option_key ]['selections'][ $selection_key ]['name']}" );
            }

            if ( count( $temp_array ) )
            {
              $options_text = implode( ' ; ', $temp_array );
              $html .= " [{$options_text}]";
            }

            $html .= '</li>';
          }		  
          $html .= "</ul></td>";
          $html .= "</tr></table>";

          $pdf->WriteLine( $html );
        }
      }    
    }
    $conn->close();
    $pdf->Output( "{$competition_data['competition_id']}_Commandes(Jours)_" . date( 'Y-m-d' ) . ".pdf", 'I' );
  }
  else
  {
    echo "Vous n'avez pas accès à cette compétition !";
  }

?>