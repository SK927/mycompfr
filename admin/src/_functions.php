<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';
  require_once dirname( __DIR__, 2 ) . '/config/config_loader.php';
  $db = load_config_yaml( 'config-db' );
  

  /**
   * get_cu_competitions(): retrieve all the competitions currently stored in the database for Commande Utile
   * @param (mysqli) mysqli: mysqli object
   * @return (array) data for the stored competitions
   */ 

  function get_cu_competitions( $mysqli )
  {
    global $db;

    $competitions = array();

    $sql = "SELECT * FROM {$db['cu']}_Competitions ORDER BY start_date";
    $results = $mysqli->query( $sql );
    
    while( $row = $results->fetch_assoc() ) 
    {
      array_push( $competitions, $row );
    }

    return $competitions;
  }  


  /**
   * get_rc_competitions(): retrieve all the competitions currently stored in the database for Registration Checker
   * @param (mysqli) mysqli: mysqli object
   * @return (array) data for the stored competitions
   */ 

  function get_rc_competitions( $mysqli )
  {
    global $db;

    $competitions = array();

    $sql = "SELECT * FROM {$db['rg']}_Competitions ORDER BY start_date";
    $results = $mysqli->query( $sql );
    
    while( $row = $results->fetch_assoc() ) 
    {
      array_push( $competitions, $row );
    }

    return $competitions;
  }