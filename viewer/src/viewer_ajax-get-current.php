<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __FILE__ ) . '/_functions.php';
  require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';

  mysqli_open( $mysqli );

	$competition_id = $_POST['id'];
  $stored_info = get_stored_info( $competition_id, $mysqli );

	$mysqli->close();

	$response = array(
								"src_live" => "{$stored_info['live']}/projector",
								"text_current" => $stored_info['current'],
								"text_next" => $stored_info['next'],
							);

	echo to_pretty_json( $response );

?>