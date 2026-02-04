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
    $catalog = get_catalog( $competition_id, $mysqli ); 
    [ $all_orders, $total ] = get_all_orders( $competition_id, $mysqli );
    $mysqli->close();
  
    $pdf = create_new_pdf( $competition['name'] ); 

    foreach( $catalog as $block_key => $block )
    {
      $pdf->AddPage();

  	  $html = "<table cellpadding=\"10\">";
      $html .= "<tr><td colspan=\"2\" style=\"width:85%;border:1px solid #808080;background-color:#e4e4e4\"><b>{$block['name']}</b></td>";
      $html .= "<td style=\"width:15%;border:1px solid #808080;background-color:#e4e4e4\"><b>Total jour</b></td></tr>";
      $html .= "</table>";
	  
      $pdf->WriteLine( $html );

      foreach( $all_orders as $order )
      { 
        $sum = 0;
        $rows = ! empty( $order['user_comment'] ) + ! empty( $order['admin_comment'] ) + 1;


		    if( $order['content'][ $block_key ] )
        {
          $order['user_name'] = strtoupper( $order['user_name'] );
          $total = number_format( $order['order_total'], 2 );
    		  $html = "<table cellpadding=\"10\"><tr>";

          $html .= "<td rowspan=\"{$rows}\" style=\"width:20%;border:1px solid #808080\">{$order['user_name']} ({$total}&nbsp;€)</td>";

          if( $order['admin_comment'] )
          {
            $html .= "<td colspan=\"2\" bgcolor=\"#FFC9C9\" style=\"border: 1px solid #808080;width:80%\">{$order['admin_comment']}</td>
                    </tr>
                    <tr>";
          }    

          if( $order['user_comment'] )
          {
            $html .= "<td colspan=\"2\" bgcolor=\"#fdffc9\" style=\"border: 1px solid #808080;width:80%\">{$order['user_comment']}</td>
                    </tr>
                    <tr>";
          }      
        

    		  $html .= "<td style=\"width:65%;border:1px solid #808080\"><ul>";
		         
          foreach( $order['content'][ $block_key ]['items'] as $item_key => $item )
          {
            $html .= "<li>{$item['qty']} x {$item['name']}";
            $temp_array = array();

            foreach( $item['options'] as $option )
            {
              foreach( $option as $selection )
              {
                array_push( $temp_array, "{$selection['qty']} x {$selection['name']}" );
              }
            }

            if( count( $temp_array ) )
            {
              $options_text = implode( ' ; ', $temp_array );
              $html .= " [{$options_text}]";
            }

            $html .= '</li>';
          }		  
          $total = number_format( $order['content'][ $block_key ]['total_cost'], 2 );
          $html .= "</ul></td>";
          $html .= "<td style=\"width:15%;border:1px solid #808080\">{$total} €</td>";
          $html .= "</tr></table>";

          $pdf->WriteLine( $html );
        }
      }    
    }

    ob_end_clean();
    $pdf->Output( "{$competition['name']}_Commandes(par jours)_" . date( 'Y-m-d' ) . ".pdf", 'I' );
  }
  else
  {
    echo "ERREUR : Vous n'êtes pas connecté ou vous n'êtes pas organisateur de cette compétition !";
  }

?>