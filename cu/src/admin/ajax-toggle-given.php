<?php

  require_once '../config.php';
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  
  $competition_id = $_POST['competition_id'];
  
  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, $_SESSION['manageable_competitions'] ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';

    $block_name = $_POST['block_name'];
    $order_id = $_POST['order_id'];
    
    /* Get selected order data*/
    $query_results = $conn->query( "SELECT order_data FROM " . DB_PREFIX . "_{$competition_id} WHERE id = '{$order_id}';" ); 
    
    if ( $query_results )
    {
      $result_row = $query_results->fetch_assoc();
      $order_data = from_pretty_json( $result_row['order_data'] );
      $order_data[ $block_name ]['given'] = (int) ! $order_data[ $block_name ]['given']; /* Change given status for selected block to opposite*/
      
      $order_json = mysqli_real_escape_string( $conn, to_pretty_json( $order_data ) );
      
      /* Set new order data for selected order */    
      if ( $conn->query( "UPDATE " . DB_PREFIX . "_{$competition_id} SET order_data = '{$order_json}' WHERE id = '{$order_id}';" ) )
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

