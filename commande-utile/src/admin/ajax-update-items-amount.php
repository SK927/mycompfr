<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  
  $competition_id = $_POST['competition_id'];
  
  if( $_SESSION['logged_in'] AND ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once '../functions/orders-functions.php';

    $item_amount = get_items_amount( $competition_id, $conn );
    
    $conn->close();
  }

  echo json_encode( $item_amount );
  
?>

