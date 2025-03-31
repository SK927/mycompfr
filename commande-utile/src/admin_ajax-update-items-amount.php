<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  
  $competition_id = $_POST['competition_id'];
  
  if( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions-orders.php';

    $item_amount = get_items_amount( $competition_id, $conn );
    $conn->close();
  }

  echo json_encode( $item_amount );
  
?>

