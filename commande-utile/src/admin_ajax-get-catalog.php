<?php
  
  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
  require_once dirname( __FILE__ ) . '/_functions-competition-data.php';

  $competition_data = get_competition_data( $_POST['competition_id'], $conn ); 

  $response = array(
                'array' => from_pretty_json( $competition_data['competition_catalog'] ), 
              );

  echo json_encode( $response );

?>
