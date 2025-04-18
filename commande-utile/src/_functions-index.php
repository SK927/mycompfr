<?php

  require_once dirname( __FILE__ ) . '/_functions-orders.php'; // $db is loaded here!
  
  
  /** 
   * get_user_imported_competitions(): retrieve all competitions stored in database for which user is a competitor or an organizer/delegate
   * @param (int) user_id: ID of the user to retrieve competitions for
   * @param (mysqli) mysqli: database connection object
   * @return (array) all the imported competitions as an associative array and the error generated by the function
   */

  function get_user_imported_competitions( $user_id, $mysqli )
  {
    global $db;
    
    $base_request = "competitors LIKE '%[{$user_id}]%' AND  competition_catalog != ''";

    if ( $_SESSION['manageable_competitions'] )
    {
      $competition_ids = "('" . implode( "', '", array_keys( $_SESSION['manageable_competitions'] ) ) . "')";
      $sql = "SELECT * FROM {$db['cu']}_Main WHERE competition_id IN {$competition_ids} OR ({$base_request})";
    }
    else
    { 
      $sql =  "SELECT * FROM {$db['cu']}_Main WHERE {$base_request}";
    }

    $query_results = $mysqli->query( $sql );
    $user_imported_competitions = [];

    if( $query_results )
    {
      while( $result_row = $query_results->fetch_assoc() )
      { 
        [ $error, $user_order ] = get_user_order( $result_row['competition_id'], $user_id, $mysqli );
        $result_row['has_ordered'] = $error ? null : (! is_null( $user_order ));
        $user_imported_competitions[ $result_row['competition_id'] ] = $result_row;
      }
    }
    else
    {
      $error = mysqli_error( $mysqli );
    }

    return [ $user_imported_competitions, $error ];
  }


  /**
   * check_imported_competitions(): check if competitions have been imported
   * @param (array) competitions: competitions to check
   * @param (mysqli) mysqli: reference to connection to DB
   * @return (array) of modified version of the competition array
   */

  function check_imported_competitions( $competitions, $mysqli )
  {
    global $db;
    
    if ( $competitions )
    {
      $sql = "SELECT competition_id FROM {$db['cu']}_Main WHERE 1";
      $query_results = $mysqli->query( $sql );

      while( $row = $query_results->fetch_assoc() )
      {
        if ( in_array( $row['competition_id'], array_keys( $competitions ) ) )
        {
          $competitions[ $row['competition_id'] ]['imported_in_cu'] = true;
        }
      }
    }

    return $competitions;
  }

?>