<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-wcif.php';
  require_once dirname( __DIR__, 2 ) . '/config/config_loader.php';
  $db = load_config_yaml( 'config-db' );
  $iso = load_config_yaml( 'config-countriesIso' );


  /*
   * class user_data: class handling user competition data
   */

  class user_data
  {
    public $competitions; 
    public $countries;
    public $locations;
    public $years;
    public $users;

    public function __construct()
    {
      $this->competitions = array();
      $this->countries = array();
      $this->locations = array();
      $this->years = array();
      $this->users = array();
      $this->events = array();
    }

    private function set_counter( $value )
    {
      return isset( $value ) ? $value +=1 : 1; 
    }

    public function add_competition( $competition_name )
    {
      array_push( $this->competitions, $competition_name );
    }

    public function set_countries_counter( $country )
    {
      $this->countries[ $country ] = $this->set_counter( $this->countries[ $country ] );
    }

    public function set_years_counter( $year )
    {
      $this->years[ $year ] = $this->set_counter( $this->years[ $year ] );
    }

    public function set_users_counter( $user )
    {
      $this->users[ $user ] = $this->set_counter( $this->users[ $user ] );
    }

    public function set_events_counter( $event )
    {
      $this->events[ $event ] = $this->set_counter( $this->events[ $event ] );
    }
  }  


  /**
   * check_last_updated(): retrieve the last time user has updated their competition list
   * @param (string) wca_id: user WCA ID
   * @param (mysqli) mysqli: reference to mysqli connection to DB
   * @return (string) date of the last update
   */ 

  function check_last_updated( $wca_id, $mysqli )
  {
    global $db;

    $last_updated = null;

    $sql = "SELECT updated_at FROM {$db['do']}_Competitors WHERE wca_id = '{$wca_id}'";
    $query_results = $mysqli->query( $sql );
    
    if( $query_results->num_rows ) 
    {
      $last_updated = $query_results->fetch_assoc()['updated_at'];
    }

    return $last_updated;
  }


  /**
   * update_user_last_updated(): update the date at which the user last updated their competitions to today
   * @param (string) wca_id: user WCA ID
   * @param (mysqli) mysqli: reference to mysqli connection to DB
   * @return (string) date of the last update
   */ 

  function update_user_last_updated( $wca_id, $mysqli )
  {
    global $db;

    $today = date( 'Y-m-d' );

    $sql = "REPLACE INTO {$db['do']}_Competitors VALUES ('{$wca_id}', '{$today}')";
    $mysqli->query( $sql );
  }
  

  /**
   * get_competitions_to_update(): retrieve all competitions managed by user between last update -1 month and last week
   * @param (string) last_update: date of the last update
   * @param (string) user_token: WCA API token used to access private data from users profile
   * @return (array) the competitions which need to be updated
   */ 

  function get_competitions_to_update( $last_updated = null, $user_token )
  {
    $start_arg = '';
    $current_page = 1;
    $user_competitions = array();

    $end_date = date( 'Y-m-d', strtotime( date( 'Y-m-d' ) . '-1 week' ) ) . "T00:00:00.000Z"; // Look for competitions from last month onwards

    if(  $last_updated )
    {
      $start_date = date( 'Y-m-d', strtotime( $last_updated . '-1 month' ) ) . "T00:00:00.000Z"; // Look for competitions from last month onwards
      $start_arg = "&start={$start_date}";
    }

    do
    {
      $url = "https://www.worldcubeassociation.org/api/v0/competitions?managed_by_me=1{$start_arg}&end={$end_date}&page={$current_page}";
      [ $competitions_managed_by_user, $error ] = get_wca_data_via_api( $url, $user_token );
      $user_competitions = array_merge( $user_competitions, $competitions_managed_by_user );
      $current_page++;
    } while ( count( $competitions_managed_by_user ) == 25 );

    return $user_competitions;
  }


  /**
   * get_competitions_managed_by_user_in_past(): retrieve competitions where current user is either organizer or delegate
   * @param (int) user_name: name of logged in user
   * @param (string) type: type of role searched for
   * @param (mysqli) mysqli: reference to mysqli connection to DB
   * @return (array) organized competitions information
   */ 

  function get_competitions_managed_by_user_in_past( $user, $type, $mysqli )
  {
    require_once dirname( __DIR__, 2 ) . '/config/config_loader.php';
        global $db, $iso;

    $multisite = array( 'XA', 'XE', 'XM', 'XN', 'XO', 'XS', 'XW' );

    // Retrieve all competitions where current user is part of the organizing team
    $sql = "SELECT * FROM {$db['do']}_Competitions WHERE {$type} LIKE '%{$user}%' AND cancelled = 0 ORDER by startDate";
    $query_results = $mysqli->query( $sql );
    $user_competitions = new user_data();

    while ( $competition = $query_results->fetch_assoc() ) 
    {
      $user_competitions->add_competition( str_replace( ' ', '&nbsp;', $competition['name'] ) );
      
      $country = isset( $iso[ $competition['countryId'] ] ) ? $iso[ $competition['countryId'] ] : $competition['countryId'];
      $user_competitions->set_countries_counter( $country );

      if( isset( $user_competitions->locations[ $competition['latitude'] ][ $competition['longitude'] ] ) )
      {
        $user_competitions->locations[ $competition['latitude'] ][ $competition['longitude'] ] = "{$user_competitions->locations[ $competition['latitude'] ][ $competition['longitude'] ]}&nbsp;; {$competition['name']}";
      }
      else
      {
        $user_competitions->locations[ $competition['latitude'] ][ $competition['longitude'] ] =  $competition['name'];
      }
      
      $year = explode( '-', $competition['startDate'] )[0];
      $user_competitions->set_years_counter( "e{$year}" );
      $events = from_pretty_json( $competition['events'] );

      foreach( $events as $event )
      {
        $user_competitions->set_events_counter( $event );
      }
      
      if( ! in_array( $competition['countryId'], $multisite ) )
      {
        $people = from_pretty_json( $competition[ $type ] );

        foreach( $people as $user_id => $person )
        {
          if( ($person['name'] != $user) AND ($person['wca_id'] != $user) )
          {
            $user_competitions->set_users_counter( "{$person['name']}|{$person['wca_id']}" );
          }
        }
      }
    }

    unset( $user_competitions->users[ $user_name ] ); // Remove value for current user

    // Sort array by value of counter, descending 
    arsort( $user_competitions->countries );
    arsort( $user_competitions->users );
    arsort( $user_competitions->events );

    return $user_competitions;
  }


  /**
   * update_competitions_in_db(): update the competitions date in the database
   * @param (array) competitions: the competitions which need to be updated 
   * @param (mysqli) mysqli: reference to mysqli connection to DB
   * @return (array) the competitions which where actually imported or updated
   */ 

  function update_competitions_in_db( $competitions, $mysqli )
  {
    global $db;

    $imported_competitions = array();
    $error = false;

    foreach( $competitions as $c )
    { 
      $delegates = array();
      $organizers = array();

      foreach( $c['delegates'] as $person )
      {
        $delegates[ $person['id'] ] = array(
                                        'wca_id' => $person['wca_id'],
                                        'name' => $person['name'],
                                      );
      }

      foreach( $c['organizers'] as $person )
      {
        $organizers[ $person['id'] ] = array(
                                        'wca_id' => $person['wca_id'],
                                        'name' => $person['name'],
                                      );
      }
      
      $competition = array(
                        'id' => $c['id'], 
                        'name' => mysqli_real_escape_string( $mysqli, $c['name'] ), 
                        'countryId' => $c['country_iso2'],
                        'latitude' => sprintf( '%0.1f', $c['latitude_degrees'] ),
                        'longitude' => sprintf( '%0.1f', $c['longitude_degrees'] ),
                        'delegate' => mysqli_real_escape_string( $mysqli, to_pretty_json( $delegates ) ), 
                        'organizer' => mysqli_real_escape_string( $mysqli, to_pretty_json( $organizers ) ), 
                        'startDate' => $c['start_date'],
                        'events' => to_pretty_json( $c['event_ids'] ), 
                        'cancelled' => $c['cancelled_at'] ? 1 : 0, 
                      );

      $sql = "REPLACE INTO {$db['do']}_Competitions VALUES ('{$competition['id']}', '{$competition['name']}', '{$competition['countryId']}', {$competition['latitude']}, {$competition['longitude']}, '{$competition['delegate']}', '{$competition['organizer']}', '{$competition['startDate']}', '{$competition['events']}', {$competition['cancelled']});";

      if( ! $mysqli->query( $sql ) )
      {
        $error = true;
      }
      else
      {
        array_push( $imported_competitions, $competition['id'] );
      }
    }

    return [ $imported_competitions, $error ];
  }

?>