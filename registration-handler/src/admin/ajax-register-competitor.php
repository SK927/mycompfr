<?php

  header( 'Content-Type: text/plain; charset=UTF-8' );

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

  if ( $_SESSION['logged_in'] AND ! empty( $_POST ) ) 
  {
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';

    $save = $_POST;
    $competition_id = decrypt_data( $_POST['competition_id'] );

    $query_results = $conn->query( "SELECT competition_registrations FROM " . DB_PREFIX_RH . "_Main WHERE competition_id = '{$competition_id}';" );
    
    if ( $query_results->num_rows )
    {
      $result_row = $query_results->fetch_assoc();
      $competition_registrations = from_pretty_json( $result_row['competition_registrations'] );
    }
    else
    {      
      $competition_registrations = [];
    }
    
    $competitor = array(
                    'user_data' => array(
                                    'registration_data[name]' => $_SESSION['user_name'], 
                                    'registration_data[country]' => $_SESSION['user_country'], 
                                    'registration_data[birth_date]' => $_SESSION['user_dob'], 
                                    'registration_data[gender]' => $_SESSION['user_gender'], 
                                    'registration_data[email]' => $_SESSION['user_email'], 
                                    'registration_data[wca_id]' => $_SESSION['user_wca_id']
                                  ), 
                    'printed' => false,
                  );

    parse_str( $_POST['events'], $events );

    foreach ( $events as $key => $event )
    {
      $competitor['events'][ $key ] = 1; /* Add each selected event to competitor user_data */
    }
    
    $competition_registrations[ $_SESSION['user_id'] ] = $competitor;

    $registrations_json = to_pretty_json( $competition_registrations );
    
    $sql = "UPDATE " . DB_PREFIX_RH . "_Main SET competition_registrations = '{$registrations_json}' WHERE competition_id = '{$competition_id}';";
    
    if ( $conn->query ( $sql ) )
    {
      $text_to_display = 'Registration successful !';
    }
    else
    {
      $text_to_display = "Failed to register competitor...";
      $error = mysqli_error( $conn );
    }

    $conn->close();
  }
  else
  {
    $text_to_diplay = 'Access denied!';
    $error = 'Not authenticated';
  }
  
  $response = array( 
                    'text_to_display' => $text_to_display, 
                    'ajax_error' => $error,
                  );

  echo json_encode( $response );

?>