<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
  require_once dirname( __FILE__ ) . '/_functions.php';

	$competition_id = $_POST['id'];
  $stored_info = get_stored_info( $competition_id, $conn );

	$response = array(
								"src_live" => "{$stored_info['live']}/projector",
								"text_current" => $stored_info['current'],
								"text_next" => $stored_info['next'],
							);

	echo to_pretty_json( $response );

	$conn->close();

?>