<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-wcif.php';


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
   * get_competitions_managed_by_user_in_past(): retrieve competitions where current user is either organizer or delegate
   * @param (int) user_name: name of logged in user
   * @param (string) type: type of role searched for
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (array) organized competitions information
   */ 

  function get_competitions_managed_by_user_in_past( $user, $type, $mysqli )
  {
    require_once dirname( __DIR__, 2 ) . '/config/config_loader.php';
    $db = load_config_yaml( 'config-db' );
    $iso = load_config_yaml( 'config-countriesIso' );
    
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

      if ( isset( $user_competitions->locations[ $competition['latitude'] ][ $competition['longitude'] ] ) )
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

      foreach ( $events as $event )
      {
        $user_competitions->set_events_counter( $event );
      }
      
      if ( ! in_array( $competition['countryId'], $multisite ) )
      {
        $people = from_pretty_json( $competition[ $type ] );

        foreach ( $people as $user_id => $person )
        {
          if ( ($person['name'] != $user) AND ($person['wca_id'] != $user) )
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

?>