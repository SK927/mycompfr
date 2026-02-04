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
    [ $all_orders, $total ] = get_all_orders( $competition_id, $mysqli );
    $mysqli->close();
  
    $pdf = create_new_pdf( $competition['name'] );
    $pdf->AddPage();

    $pdf->setFont( 'dejavusanscondensed', '', 10, '', true );
    $pdf->SetY( $pdf->getY() + 5 );

    foreach( $all_orders as $order ) /* For each order placed */
    {
      $blocks = array();
      $html = '';

      foreach( $order['content'] as $block_key => $block )
      { 
        $text = "<b>{$block['name']}</b> (" . number_format( $block['total_cost'], 2 ) . ' €)';
        
        if( $block['items'] )
        {
          $text .= "<ul>";

          foreach( $block['items'] as $item_key => $item )
          {
            $text .= "<li>{$item['qty']} x {$item['name']}</li>"; /* Display item quantity and name */
            
            if( $item['options'] ) 
            {
              $text .= "<ul>";

              foreach( $item['options'] as $option_key => $option ) /* If item has options */
              {
                $text .= "<li>"; /* Display options selected by user */

                foreach( $option as $selection_key => $selection )
                {
                  $text .= "{$selection['qty']} x {$selection['name']} ; ";
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

      $order['user_name'] = strtoupper( $order['user_name'] );
      $total = $order['has_been_paid'] ? 'Payé' : number_format( $order['order_total'], 2 ) . ' €';
      $info = "<b>{$order['user_name']}</b><p>{$order['user_wca_id']}</p><p>{$total}</p>";
      $rows = ! empty( $order['user_comment'] ) + ! empty( $order['admin_comment'] ) + ceil( count( $blocks ) / 2 );

      $html = "<table cellpadding=\"10\">
              <tr>
                <td rowspan=\"{$rows}\" bgcolor=\"#e4e4e4\" style=\"border: 1px solid #808080;width:20%\">{$info}</td>";

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
      
      while( $cnt = count( $blocks ) )
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
          
          if( $cnt > 2 )
          {
            $html .= "<tr>";
          }
        }
      }
      $html .= "</table>";
      $pdf->WriteLine( $html );
      $pdf->SetY( $pdf->getY() + 5 );
    }

    ob_end_clean();
    $pdf->Output( "{$competition['name']}_Commandes(par compétiteurs)_" . date( 'Y-m-d' ) . ".pdf", 'I' );
  }
  else
  {
    echo "ERREUR : Vous n'êtes pas connecté ou vous n'êtes pas organisateur de cette compétition !";
  }

?>