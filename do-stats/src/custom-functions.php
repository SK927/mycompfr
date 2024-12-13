<?php

  require_once dirname( __DIR__, 2 ) . '/config/config-db.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/generic-functions.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/encrypt-functions.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/wcif-functions.php';

  /*
   * class user_data: class handling user competition data
   */

  class user_data
  {
    public $competitions; 
    public $countries;
    public $years;
    public $users;

    public function __construct()
    {
      $this->competitions = array();
      $this->countries = array();
      $this->years = array();
      $this->users = array();
    }

    private function set_counter( $value )
    {
      return isset( $value ) ? $value +=1 : 1; /* Set current counter */ 
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
  }  


  /**
   * get_competitions_handled_by_user_in_past(): retrieve competitions where current is either organizer or delegate
   * @param (int) user_name: name of logged in user
   * @param (string) type: type of role searched for
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (array) organized competitions information
   */ 

  function get_competitions_managed_by_user_in_past( $user_name, $type, $mysqli )
  {
    /* Retrieve all competitions where current user is part of the organizing team */
    $sql = "SELECT name, countryId, {$type}, year FROM " . DB_PREFIX_DO . "_Competitions WHERE {$type} LIKE '%{{$user_name}}%' AND cancelled = 0 AND countryId NOT IN ('XA','XE','XM','XN','XO','XS','XW') ORDER by year, month, day";
    $query_results = $mysqli->query( $sql );

    $user_competitions = new user_data();

    while ( $competition = $query_results->fetch_assoc() ) 
    {
      $user_competitions->add_competition( $competition['name'] );
      $user_competitions->set_countries_counter( $competition['countryId'] );
      $user_competitions->set_years_counter( "e{$competition['year']}" );
      
      $pattern = '/\{(.*?)\}\{/';
      preg_match_all( $pattern, $competition[ $type ], $matches ); /* Retrieve all co-organizers names */

      foreach ( $matches[1] as $organizer )
      {
        $user_competitions->set_users_counter( $organizer );
      }
    }
    
    unset( $user_competitions->users[ $user_name ] ); /* Remove value for current user */

    /* Sort array by value of counter, descending */
    arsort( $user_competitions->countries );
    arsort( $user_competitions->users );

    return $user_competitions;
  }

?>