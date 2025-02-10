<?php

  require_once dirname( __DIR__, 3 ) . '/config/config-db.php';


  /** 
   * get_competition_data(): retrieve competition data stored in database
   * @param (string) competition_id: ID of the competition to retrieve data for
   * @param (array) mysqli: database connection object
   * @return (array) the competition data as an associative array 
   */

  function get_competition_data( $competition_id, $mysqli )
  {   
    $query_results = $mysqli->query( "SELECT * FROM " . DB_PREFIX_CU . "_Main WHERE competition_id = '{$competition_id}';" );
    $result_row = $query_results->fetch_assoc();
    
    return $result_row;
  }

?>