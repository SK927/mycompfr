<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['id'];
 	$_SESSION['alive'] = false;

  if( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) ) 
  {
 	  require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';

 	  mysqli_open( $mysqli );

  	$_SESSION['alive'] = true;
	  $current = $_POST['current'];
	  $next = $_POST['next'];
	  $live = $_POST['live'];
		$sql = "REPLACE INTO {$db['viewer']}_Current VALUE ('{$competition_id}', '{$live}', '{$current}', '{$next}');";
		$mysqli->query( $sql );
		$error = $mysqli->error;

		$mysqli->close();
	}
	else
	{
		$error = 'Access not authorized';
	}

	echo json_encode( $error ); 

?>