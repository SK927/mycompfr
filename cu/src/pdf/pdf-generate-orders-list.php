<?php
  
  require_once '../config.php';
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, $_SESSION['manageable_competitions'] ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
    require_once '../functions/competition-data-functions.php';
    require_once '../functions/orders-functions.php';
    require_once 'tfpdf.php';

    $competition_data = get_competition_data( $competition_id, $conn ); 
        
    // Instanciation of inherited class
    $pdf = new CustomPDF();
    $pdf->title = utf8_decode( $competition_data['competition_name'] );
    $pdf->AliasNbPages();
    
    $pdf->SetFont( 'Times', '', 14 );

    $pdf->SetFillColor( 0, 136, 88 );
    $pdf->SetDrawColor( 0, 136, 88 );
        
    $query_results = get_competition_orders( $competition_id, $conn );

    $pdf->AddPage( 'P', 'A4' );
    
    while( $result_row = $query_results->fetch_assoc() ) /* For each order placed */
    {
      $pdf->SetFont( 'Times', '', 12 );
      $pdf->SetTextColor( 0 ); 

      $pdf->SetWidths( array( 50, 60, 50, 30 ) );
            
      $order = from_pretty_json( $result_row['order_data'] ); /* Decode order Json */
      $text = '';

      foreach ( $order as $block_name => $block_value )
      {
        if( $block_value != null )
        {
          $pdf->SetFont( 'Times', 'B', 12 );
          $text .= "{$block_name}\n";
          $pdf->SetFont('Times', '', 12 );
          
          foreach ( $block_value as $item_name => $item_value )
          { 
            if ( isset( $item_value['qty'] ) )
            {
              $text .= "\t\t\t- {$item_value['qty']} x {$item_name}\n"; /* Display item quantity and name */
              
              if ( isset( $item_value['options'] ) ) 
              {
                foreach ( $item_value['options'] as $option ) /* If item has options */
                {
                  $text .= "\t\t\t\t\t\t>> "; /* Display options selected by user */
                  foreach ( $option as $index => $opt )
                  {
                    $text .= "{$opt} ; ";
                  }
                  $text .= "\n";
                }
              }
            }
          }
          $text .= "\n";
        }
      }    
      $total = $result_row['has_been_paid'] ? 'Payé' : number_format( $result_row['order_total'], 2 ) . ' EUR';
      
      if($order != null) $pdf->RowList( array( utf8_decode( $result_row['user_name'] ), utf8_decode( $text ), utf8_decode( $result_row['user_comment'] ), utf8_decode( $total ) ), false, 5 );      
    }
            
    $conn->close();

    $pdf->Output( 'D', "{$competition_id}_Commandes--" . date('Y-m-d') . '.pdf' );
  }
  else
  {
    echo 'Vous n\'avez pas accès à cette compétition !';
  }

?>