<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-wcif.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';
  require_once dirname( __DIR__, 2 ) . '/src/_class-email.php';

  require_once dirname( __DIR__, 2 ) . '/config/config_loader.php';
  $db = load_config_yaml( 'config-db' );


  /**
   * import_competition_into_db(): import selected competition into database, storing data from private WCIF
   * @param (string) competition_id: competition ID of the selected competition
   * @param (string) user_token: the API token provided upon login, used to retrieve private data
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (string) the error generated
   */

  function import_competition_into_db( $competition_id, $user_email, $user_token, $mysqli )
  {
    global $db;

    [ $competition, $error ] = read_competition_data_from_private_wcif( $competition_id, $user_token );

    if( ! $error )
    {
      $competition_name = addslashes( $competition['name'] );
      $competition_start_date = $competition['schedule']['startDate'];
      $competition_end_date = date( 'Y-m-d', strtotime( $competition_start_date . ' + ' . ( (int) $competition['schedule']['numberOfDays'] - 1 ) . ' days' ) );
      $competition_country = strtolower( $competition['schedule']['venues'][0]['countryIso2'] );

      $sql = "REPLACE INTO {$db['rg']}_Competitions (id, name, contact, country_iso, start_date, end_date) VALUES ('{$competition_id}', '{$competition_name}', '{$user_email}', '{$competition_country}', '{$competition_start_date}', '{$competition_end_date}');";
      
      if( $mysqli->query( $sql ) )
      {
        $error = insert_users_data( $competition, $mysqli );
      }
      else
      {
        $error = $mysqli->error;
      }
    }

    return $error;
  }


  /**
   * check_imported_competitions(): check if competitions have been imported
   * @param (array) competitions: competitions to check
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (array) of modified version of the competition array
   */

  function check_imported_competitions( $competitions, $user, $mysqli )
  {
    global $db;

    $sql = "SELECT * FROM {$db['rg']}_Competitions WHERE 1 ORDER BY start_date, name";
    $results = $mysqli->query( $sql );

    while( $row = $results->fetch_assoc() )
    {
      if( isset( $competitions[ $row['id'] ] ) )
      {
        $competitions[ $row['id'] ]['imported_in_checker'] = true;
      }
      else if( $user['is_admin'] )
      {
        $competitions[ $row['id'] ]['name'] = $row['name'];
        $competitions[ $row['id'] ]['start'] = $row['start_date'];
        $competitions[ $row['id'] ]['end'] = $row['end_date'];
        $competitions[ $row['id'] ]['announced'] = true;
        $competitions[ $row['id'] ]['imported_in_checker'] = true;
      }
    }

    return $competitions;
  }


  /**
   * update_competition_registrations(): update competitors list for the selected competition while keeping stored answers
   * @param (string) competition_id: competition ID of the selected competition
   * @param (array) new_registrations_list: new list of competitors to be stored in DB
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (string) the error generated
   */

  function update_competition_registration( $competition_id, $user_id, $new_state, $mysqli )
  {
    global $db;

    $sql = "UPDATE {$db['rg']}_Users SET response = '{$new_state}' WHERE user_id = '{$user_id}' AND competition_id = '{$competition_id}'";
        
    if( ! $mysqli->query( $sql ) )
    {
      $error = mysqli_error( $mysqli );
    }

    return $error;
  }

  /**
   * get_users_emails(): retrieve users emails from database
   * @param (string) competition_id: ID of the competition to update the data for
   * @param (mysqli) mysqli: database connection object
   * @return (string) a list of all users emails
   */

  function get_users_emails( $competition_id, $mysqli )
  {
    global $db;

    $emails = '';
    [ $error, $registrations ] = get_competition_registrations_from_db( $competition_id, $mysqli );

    if( ! $error )
    {
      foreach( $registrations as $registration )
      {
        if( in_array( $registration['response'], array( 'NA', 'ND' ) ) )
        {
          $emails .= decrypt_data( $registration['user_email'] ) . ' ; ';
        }
      }
    }
  
    return [ $error, $emails ];
  }


  /**
   * get_competition_registrations_from_db(): retrieve users info from database
   * @param (string) competition_id: competition ID of the selected competition
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (array) a list of all competition registrations
   */

  function get_competition_registrations_from_db( $competition_id, $mysqli )
  {
    global $db;

    $sql = "SELECT * FROM {$db['rg']}_Users WHERE competition_id = '{$competition_id}'";
    
    if( $results = $mysqli->query( $sql ) )
    {
      if( $results->num_rows )
      {
        $registrations = array();

        while( $row = $results->fetch_assoc() )
        {
          $registrations[] = $row;
        }
      
        uasort($registrations, function ( $a, $b ) { return $b['user_name'] < $a['user_name']; });
        uasort($registrations, function ( $a, $b ) { return $b['response'] < $a['response']; });
      }
    }
    else
    {
      $error = $mysqli->error;
    }

    return [ $error, $registrations ];
  }


  /**
   * insert_users_data(): insert data into database
   * @param (array) competition: associative array from competition private WCIF
   * @return (string) error
   */

  function insert_users_data( $competition, $mysqli )
  {
    global $db;

    foreach( $competition['persons'] as $person )
    {
      if( $person['registration']['status'] == "accepted" ) 
      {
        $user_id = $person['wcaUserId'];
        $user_name = $person['name'];
        $user_wca_id = $person['wcaId'];
        $user_email = encrypt_data( $person['email'] );

        $sql = "REPLACE INTO {$db['rg']}_Users (competition_id, user_id, user_name, user_wca_id, user_email) VALUES ('{$competition['id']}', '{$user_id}', '{$user_name}', '{$user_wca_id}', '{$user_email}');";

        if( ! $mysqli->query( $sql ) )
        {
          $error = mysqli_error( $mysqli );
        }
      }
    }

    return $error;
  }

  
  /**  
   * send_checker_reminder(): send an email to remind competitors with an indefinite status to answer
   * @param (string) competitors_email: email of the competitors with an indefinite status
   * @param (string) competition_name: name of the competition the reminder is sent for
   * @param (string) admin_email: email of the orga/delegate sending the reminder
   * @return (string) the error generated
   */

  function send_checker_reminder( $competition_name, $competitors_email, $admin_email )
  {  
    $to = $competitors_email;
    $from = decrypt_data( $admin_email );
    $folder =  explode( '/' , $_SERVER['REQUEST_URI'] )[1];
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['reminder'];

    $email = new email();
    $email->create_header( $from, $to );
    $email->subject = $content['subject'];

    foreach( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );
     
    $email->replace_subject_text( "{competition_name}", $competition_name );
    $email->replace_message_text( "{competition_name}", $competition_name );
    $email->replace_message_text( "{site}", "https://{$_SERVER['SERVER_NAME']}/{$folder}" );
    

    // Send email
    if( mail( "", $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return 'Au moins un compétiteur n\'a pas reçu le rappel !';
    }
  }


  /**
   * send_creation_competition_rc() : send an email to confirm the competition has been properly created in database
   * @param (string) competition_id: ID of the competition being removed from the database
   * @param (string) orga_email: email of the organizers of the competition
   * @param (string) all_administrators_email: email of all the website administrators
   * @return (string) error if sending the email failed
   */
  
  function send_creation_competition_rc( $competition_id, $orga_email, $all_administrators_email )
  {
    $to = $orga_email;
    $from = $all_administrators_email;
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_create_competition'];

    $email = new email();
    $email->create_header( $from );
    $email->subject = $content['subject'];

    foreach( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );
     
    $email->replace_subject_text( "{competition_id}", $competition_id );
    $email->replace_message_text( "{competition_id}", $competition_id );    

    // Send email
    if( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
    }
  }


  /**
   * send_deletion_competition_rc() : send an email to confirm the competition has been properly removed from the database
   * @param (string) competition_id: ID of the competition being removed from the database
   * @param (string) orga_email: email of the organizers of the competition
   * @param (string) all_administrators_email: email of all the website administrators
   * @return (string) error if sending the email failed
   */

  function send_deletion_competition_rc( $competition_id, $orga_email, $all_administrators_email )
  {
    $to = $orga_email;
    $from = $all_administrators_email;
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_delete_competition'];

    $email = new email();
    $email->create_header( $from );
    $email->subject = $content['subject'];

    foreach( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );
     
    $email->replace_subject_text( "{competition_id}", $competition_id );
    $email->replace_message_text( "{competition_id}", $competition_id );    

    // Send email
    if( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
    }
  }

?>


