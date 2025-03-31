<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
  require_once dirname( __FILE__ ) . '/_functions.php';

  $competition_id = $_POST['id'];

  if ( $_SESSION['logged_in'] AND in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) ) 
  {
    if ( isset( $_POST['live'] ) )
    {
      $stored_info = get_stored_info( $competition_id, $conn );
    	$live = "{$_POST['live']}";
      $sql = "REPLACE INTO {$db['viewer']}_Current VALUE ('{$competition_id}', '{$live}', '{$stored_info['current']}', '{$stored_info['next']}');";
    	$conn->query( $sql );
    }
  } 

	$conn->close();

?>