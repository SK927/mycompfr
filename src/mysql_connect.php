<?php

  require_once dirname( __DIR__, 1 ) . '/config/config_loader.php';
  $mysql = load_config_yaml( 'config-mysql' );

  mysqli_report( MYSQLI_REPORT_STRICT );
  $conn = new mysqli( $mysql['host'], $mysql['username'], $mysql['password'], $mysql['db_name'] );
   
  if ( $conn->connect_errno ) 
  {
    echo "Failed to connect to MySQL: ({$conn->connect_errno}) {$conn->connect_error}";
  }
  
  
?>