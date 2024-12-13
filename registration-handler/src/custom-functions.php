<?php

  require_once dirname( __DIR__, 2 ) . '/config/config-db.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/generic-functions.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/encrypt-functions.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/wcif-functions.php';


  /**
   * import_competition_into_db(): import selected competition into database, storing data from private WCIF
   * @param (string) competition_id: competition ID of the selected competition
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (string) the error generated
   */

  function import_competition_into_db( $competition_id, $mysqli )
  {
    [ $competition, $error ] = retrieve_competition_data_from_wca( $competition_id );

    if ( ! $error )
    {
      if ( ! $competition['error'] )
      {
        $competition_name = $competition['competition_name'];
        $competition_start_date = $competition['competition_start_date'];
        $competition_end_date = $competition['competition_end_date'];
        $competition_country = $competition['competition_country'];
        $competition_events = $competition['competition_events'];

        $sql = "REPLACE INTO " . DB_PREFIX_RH . "_Main (competition_id, competition_name, competition_start_date, competition_end_date, competition_country_iso, competition_events) VALUES ('$competition_id', '$competition_name', '$competition_start_date', '$competition_end_date', '$competition_country', '$competition_events');";
        
        if ( ! $mysqli->query( $sql ) )
        {
          $error = mysqli_error( $mysqli );
        }
      }
      else $error = $competition['error'];
    }

    return $error;
  }

  /**
   * check_imported_competitions(): check if competitions have been imported
   * @param (array) competitions: competitions to check
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (array) of modified version of the competition array
   */

  function check_imported_competitions( $competitions, $mysqli )
  {
    if ( $competitions )
    {
      $sql = "SELECT competition_id FROM " . DB_PREFIX_RH . "_Main WHERE 1";
      $query_results = $mysqli->query( $sql );

      while( $row = $query_results->fetch_assoc() )
      { 
        if ( in_array( $row['competition_id'], array_keys( $competitions ) ) )
        {
          $competitions[ $row['competition_id'] ]['imported_in_handler'] = true;
        }
      }
    }

    return $competitions;
  }


  /**
   * get_competition_data_from_db(): retrieve competition data stored in DB
   * @param (string) competition_id: competition ID of the selected competition
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (array) the competition name, events and registrations
   */

  function get_competition_data_from_db( $competition_id, $mysqli )
  {
    $query_results = $mysqli->query( "SELECT competition_name, competition_events, competition_registrations FROM " . DB_PREFIX_RH . "_Main WHERE competition_id = '{$competition_id}';" );

    if ( $query_results->num_rows )
    {
      $result_row = $query_results->fetch_assoc();
      $competition_events = from_pretty_json( $result_row['competition_events'] );
      $competition_registrations = from_pretty_json( $result_row['competition_registrations'] );
    }

    return array( $result_row['competition_name'], $competition_events, $competition_registrations );
  }


  /**
   * format_registration_data(): format all stored registration to check competitor status
   * @param (string) competition_id: competition ID of the selected competition
   * @param (int) user_id: ID of user (not WCA ID)
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (array) the formatted registrations data
   */

  function format_registration_data( $competition_id, $user_id, $registration, $competitors_list )
  {  
    $formatted_data = $registration;

    if ( ! $error )
    {
      $formatted_data['user_data']['registration_data[email]'] = decrypt_data( $registration['user_data']['registration_data[email]'] );
      $formatted_data['user_data']['registration_data[birth_date]'] = decrypt_data( $registration['user_data']['registration_data[birth_date]'] );
    
      $pattern = "/^$user_id$/";
      $competitor = array_values( array_filter( $competitors_list, function( $a ) use( $pattern ){ return preg_grep( $pattern, $a ); } ) ); /* Get only current user specific registrations data */

      if ( $competitor ) /* If data exists */ 
      { 
        $formatted_data['registered'] = true;
        $formatted_data['user_data']['registrant_id'] = $competitor[0]['registrantId'];
      }
      else 
      {
        $formatted_data['registered'] = false;
        $formatted_data['registration_link'] = "https://www.worldcubeassociation.org/competitions/{$competition_id}/registrations/add?";
        
        foreach ( $formatted_data['user_data'] as $key => $value ) 
        {
          $formatted_data['registration_link'] .= "&amp;$key=$value";
        }

        foreach ( $registration['events'] as $event => $group )
        {
          $formatted_data['registration_link'] .= '&amp;registration_data[event_ids][' . substr( $event, 1 ) . ']=on';
        }
      }
    }
    return $formatted_data;      
  }


  /**
   * retrieve_competition_data_from_wca(): retrieve and format competition data from WCA website
   * @param (string) competition_id: the ID of the competition we want to retrieve data for
   * @return (array) the competition data
   */

  function retrieve_competition_data_from_wca( $competition_id )
  {
    [$response, $error] = read_competition_data_from_public_wcif( $competition_id );

    if ( ! $error )
    {   
      $events = array(
                  "e333" => "3x3", 
                  "e222" => "2x2",
                  "e444" => "4x4", 
                  "e555" => "5x5", 
                  "e666" => "6x6", 
                  "e777" => "7x7", 
                  "e333oh" => "3x3 One-Handed", 
                  "e333bf" => "3x3 Blindfolded", 
                  "e333fm" => "Fewest moves", 
                  "eclock" => "Clock", 
                  "eminx" => "Megaminx", 
                  "epyram" => "Pyraminx", 
                  "eskewb" => "Skewb", 
                  "esq1" => "Square-1", 
                  "e444bf" => "4x4 Blindfolded", 
                  "e555bf" => "5x5 Blindfolded", 
                  "e333mbf" => "Multi-blind",
                );
        
      $data['competition_name'] = addslashes( $response['name'] );
      $data['competition_start_date'] = $response['schedule']['startDate'];
      $data['competition_end_date'] = date( 'Y-m-d', strtotime( $competition_start_date . ' + ' . (int)($response['schedule']['numberOfDays'] - 1) . ' days' ) );
      $data['competition_country'] = strtolower( $response['schedule']['venues'][0]['countryIso2'] );
        
      $competition_events = array();

      foreach ( $response['events'] as $key => $event)
      {         
        $cnt = "e{$event['id']}";

        $competition_events[ $cnt ] = array(
                                      'alias' => $events[ $cnt ], 
                                      'groups' => $event['rounds'][0]['scrambleSetCount'],
                                      'time_limit' => $event['rounds'][0]['timeLimit'],
                                      'cutoff' => $event['rounds'][0]['cutoff'],
                                    );
      }
      
      $data['competition_events'] = to_pretty_json( array_reverse( $competition_events ) );
    }
    
    return array( $data, $error );     
  }

?>