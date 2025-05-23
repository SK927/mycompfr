<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['competition_id'];
  
  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions-orders.php';

    $order_id = $_POST['order_id'];

    // Delete selected order 
    if ( $error = delete_user_order( $competition_id, $order_id, $conn ) )
      $text_to_display = 'Échec de la suppression de la commande...';
    else
      $text_to_display = 'Suppression de la commande effectuée avec succès !';
    
    $conn->close();
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }
  
  $response = array( 
                'text_to_display' => $text_to_display, 
                'error' => $error
              );
  
  echo json_encode( $response );
  
?>
