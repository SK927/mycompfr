<?php

  require_once 'config.php';

  require_once dirname( __DIR__, 2 ) . '/src/functions/generic-functions.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/encrypt-functions.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/wcif-functions.php';


  /**
   * import_competition_into_db(): import selected competition into database, storing data from private WCIF
   * @param (string) competition_id: competition ID of the selected competition
   * @param (string) user_token: the API token provided upon login, used to retrieve private data
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (string) the error generated
   */

  function import_competition_into_db( $competition_id, $user_token, $mysqli )
  {
    [ $competition, $error ] = read_competition_data_from_private_wcif( $competition_id, $user_token );

    if ( ! $error )
    {
      $competition_name = addslashes( $competition['name'] );
      $competition_start_date = $competition['schedule']['startDate'];
      $competition_end_date = date( 'Y-m-d', strtotime( $competition_start_date . ' + ' . ( (int) $competition['schedule']['numberOfDays'] - 1 ) . ' days' ) );
      $competition_country = strtolower( $competition['schedule']['venues'][0]['countryIso2'] );
      $competition_registrations = [];
      
      $competition_registrations = to_pretty_json( format_wcif_persons_data( $competition['persons'] ) );

      $sql = "REPLACE INTO " . DB_PREFIX . "_Main (competition_id, competition_name, competition_start_date, competition_end_date, competition_country_iso, competition_registrations) VALUES ('{$competition_id}', '{$competition_name}', '{$competition_start_date}', '{$competition_end_date}', '{$competition_country}', '{$competition_registrations}');";
      
      if ( ! $mysqli->query( $sql ) )
      {
        $error = mysqli_error( $mysqli );
      }
    }

    return $error;
  }


  /**
   * update_competition_registrations(): update competitors list for the selected competition while keeping stored answers
   * @param (string) competition_id: competition ID of the selected competition
   * @param (array) new_registrations_list: new list of competitors to be stored in DB
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (string) the error generated
   */

  function update_competition_registrations( $competition_id, $new_registrations_list, $mysqli )
  {
    $new_registrations_list = to_pretty_json( $new_registrations_list );

    $sql = "UPDATE " . DB_PREFIX . "_Main SET competition_registrations = '$new_registrations_list' WHERE competition_id = '$competition_id';";
        
    if ( ! $mysqli->query( $sql ) )
    {
      $error = mysqli_error( $mysqli );
    }

    return $error;
  }


  /**
   * get_competition_registrations_from_db(): retrieve competitors info and answers from DB
   * @param (string) competition_id: competition ID of the selected competition
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (array) a list of all competition registrations
   */

  function get_competition_registrations_from_db( $competition_id, $mysqli )
  {
    $query_results = $mysqli->query( "SELECT competition_registrations FROM " . DB_PREFIX . "_Main WHERE competition_id = '{$competition_id}';" );

    if ( $query_results->num_rows )
    {
      $result_row = $query_results->fetch_assoc();

      if ( ! empty( $result_row['competition_registrations'] ) )
      {
        $competition_registrations = from_pretty_json( $result_row['competition_registrations'] );
        array_multisort( array_column( $competition_registrations, 'name' ), SORT_ASC, $competition_registrations); /* Sort final array */
      }
    }

    return $competition_registrations;
  }


  /**
   * get_user_manageable_competitions(): retrieve competitions where current is either organizer or delegate
   * @param (int) user_id: ID of user (not WCA ID)
   * @param (string) user_token: the API token provided upon login, used to retrieve private data
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (string) the error generated
   */ 
  
  function get_user_manageable_competitions( $user_token, $mysqli )
  {
    $manageable_competitions = array();
    $_SESSION['manageable_competitions'] = array();
    
    [ $competitions_managed_by_user, $error ] = get_competitions_managed_by_user( $user_token );
    
    if ( ! $error )
    {
      foreach ( $competitions_managed_by_user as $competition )
      { 
        $query_results = $mysqli->query( "SELECT id FROM " . DB_PREFIX . "_Main WHERE competition_id = '{$competition['id']}';" );
                
        $is_imported = $query_results->fetch_assoc()['id'] ? true : false;
          
        array_push( $_SESSION['manageable_competitions'], $competition['id'] );

        $manageable_competitions[ $competition['id'] ] = array(
                                                          'id' => encrypt_data( $competition['id'] ),
                                                          'name' => $competition['name'],
                                                          'start' => date( 'd-m-Y', strtotime( $competition['start_date'] ) ),
                                                          'end' => date( 'd-m-Y' , strtotime( $competition['end_date'] ) ),
                                                          'is_imported' => $is_imported,
                                                        ); 
      }
      
      $_SESSION['encrypted_competitions_data'] = encrypt_data( to_pretty_json( $manageable_competitions ) );

      return null ;
    }
    else
    { 
      return $error;
    }
  }


  /**
   * format_wcif_persons_data(): format competitors data to needed output
   * @param (array) persons: associative array of persons extracted from competition private WCIF
   * @return (array) a formatted list of registrations
   */

  function format_wcif_persons_data( $persons )
  {
    foreach ( $persons as $person )
    {          
      if ( $person['registration']['status'] == "accepted" ) 
      {
        $competition_registrations[ $person['wcaUserId'] ] = array( 
                                                              'name' => $person['name'], 
                                                              'email' => encrypt_data( $person['email'] ), 
                                                              'confirmed' => 'NA',
                                                            );
      }
    }
    return $competition_registrations;
  }


  /**  
   * send_checker_reminder(): send an email to remind competitors with an indefinite status to answer
   * @param (string) competitors_email: email of the competitors with an indefinite status
   * @param (string) competition_name: name of the competition the reminder is sent for
   * @param (string) admin_email: email of the orga/delegate sending the reminder
   * @return (string) the error generated
   */

  function send_checker_reminder( $competitors_email, $competition_name, $admin_email )
  {  
    $to = decrypt_data( $admin_email );
    $subject = "{$competition_name} - Please Confirm Your Registration!";
    $from = $competitors_email;

    /* To send HTML mail, the Content-type header must be set */
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
     
    /* Create email headers */
    $headers .= "From: RegistrationChecker\r\nBcc: [email]{$from}[/email]\r\n";
    $headers .= "Reply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
     
    /* Compose a simple HTML email message */
    $message = '<html><body>';
    $message .= '<p>Hi,</p>';
    $message .= '<p>This is a friendly reminder to confirm your';
    $message .= " registration for {$competition_name} as soon as possible.</p>";
    $message .= "<p>To do so, please visit the <a href=\"https://{$_SERVER['SERVER_NAME']}\">";
    $message .= 'Registration Checker</a> website.</p>';
    $message .= '<p>Not confirming your registration may result in it being deleted!</p>';
    $message .= '<p>Thank you in advance,</p>';
    $message .= '<p>----</p>';
    $message .= "<p>The organizing team for {$competition_name}</p>";
    $message .= '</body></html>';
     
    /* Sending email */
    if ( mail( $to, $subject, $message, $headers ) ) 
    {
      return null; 
    }
    else
    {
      return 'At least one competitor has not been reminded';
    }
  }

?>