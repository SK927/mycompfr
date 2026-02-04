<?php

  require_once dirname( __DIR__, 1 ) . '/config/config_loader.php';
  $db = load_config_yaml( 'config-db' );

  function mysqli_open( &$mysqli )
  {
    $auth = load_config_yaml( 'config-mysql' );

    mysqli_report( MYSQLI_REPORT_STRICT );
    $mysqli = new mysqli( $auth['host'], $auth['username'], $auth['password'], $auth['db_name'] );
  }
  
?>