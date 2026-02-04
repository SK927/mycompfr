<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['id'];

  if( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) )
  {
    require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
    require_once dirname( __FILE__ ) . '/_functions.php';
    require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';

    mysqli_open( $mysqli );

    if( isset( $_POST['live'] ) )
    {
      $stored_info = get_stored_info( $competition_id, $mysqli );
    	$live = "{$_POST['live']}";
      $sql = "REPLACE INTO {$db['viewer']}_Current VALUE ('{$competition_id}', '{$live}', '{$stored_info['current']}', '{$stored_info['next']}');";
    	$mysqli->query( $sql );
    }

    $mysqli->close();
  } 


?>