<?php
  
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

  require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
  require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
  require_once '../functions/competition-data-functions.php';

  $competition_data = get_competition_data( $_POST['competition_id'], $conn ); 

  $response = array(
                'array' => from_pretty_json( $competition_data['competition_catalog'] ), 
              );

  echo json_encode( $response );

?>
