<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!
  
  $competition_id = $_POST['competition_id'];
  
  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
    require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';

    $block_id = $_POST['block_id'];
    $order_id = $_POST['order_id'];
    
    // Get selected order data
    $query_results = $conn->query( "SELECT order_data FROM {$db['cu']}_{$competition_id} WHERE id = '{$order_id}'" ); 
    
    if ( $query_results )
    {
      $result_row = $query_results->fetch_assoc();
      $order_data = from_pretty_json( $result_row['order_data'] );
      $order_data[ $block_id ]['given'] = (int) ! $order_data[ $block_id ]['given']; // Change given status for selected block to opposite
      $order_json = mysqli_real_escape_string( $conn, to_pretty_json( $order_data ) );
      
      // Set new order data for selected order     
      if ( $conn->query( "UPDATE {$db['cu']}_{$competition_id} SET order_data = '{$order_json}' WHERE id = '{$order_id}'" ) )
      {
        $text_to_display = 'Statut de distribution mis à jour avec succès !';
      }
      else
      {
        $text_to_display = '&Eacute;chec de la mise à jour du statut de distribution...';
        $error = mysqli_error( $conn );
      }
    }
    
    $conn->close();
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }
  
  $response = array(
                'text_to_display' => $text_to_display, 
                'error' => $error,
              );

  echo json_encode( $response );

?>

