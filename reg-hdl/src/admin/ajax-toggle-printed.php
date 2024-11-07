<?php

  require_once '../config.php';
  
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';

  $competition_id = $_POST['competition_id'];

  if ( $_SESSION['logged_in'] AND in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) )
  {
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once '../custom-functions.php';

    $user_id = decrypt_data( $_POST['user_id'] );
    
    /* Query all registrations stored in DB for selected competition */
    $query_results = $conn->query( "SELECT competition_registrations FROM " . DB_PREFIX . "_Main WHERE competition_id = '{$competition_id}';" );
    if ( $query_results->num_rows )
    {
      $result_row = $query_results->fetch_assoc();
      $competition_registrations = json_decode( $result_row['competition_registrations'], true );
      $competition_registrations[ $user_id ]['printed'] = ($_POST['printed'] == 'true');
      $registrations_json = to_pretty_json( $competition_registrations );
      
      $sql = "UPDATE " . DB_PREFIX . "_Main SET competition_registrations = '{$registrations_json}' WHERE competition_id = '{$competition_id}';";
      
      if ( $conn->query( $sql ) )
      {
        $text_to_display = 'Updated successfully!';
      }
      else
      {
        $text_to_display = 'Failed to update competitor...';
        $error = mysqli_error( $conn );
      }
    }
    else
    {
      $text_to_display = 'Failed to update competitor...';
      $error = 'No competition registred with given ID';
    }

    $conn->close();
  }
  else
  {
    $text_to_display = 'Access denied !';
    $error = 'Not authenticated';
  }
  
  $response = array( 
                    'text_to_display' => $text_to_display, 
                    'ajax_error' => $error, 
                  );

  echo json_encode( $response );
?>