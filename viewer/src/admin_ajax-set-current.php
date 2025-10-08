<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!
  require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';

  $competition_id = $_POST['id'];
 	$_SESSION['alive'] = false;

  if ( $_SESSION['logged_in'] AND in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) ) 
  {
  	$_SESSION['alive'] = true;
	  $current = $_POST['current'];
	  $next = $_POST['next'];
	  $live = $_POST['live'];
		$sql = "REPLACE INTO {$db['viewer']}_Current VALUE ('{$competition_id}', '{$live}', '{$current}', '{$next}');";
		$conn->query( $sql );
		$error = $conn->error;
	}
	else
	{
		$error = 'Access not authorized';
	}

	echo json_encode( $error ); 

	$conn->close();

?>