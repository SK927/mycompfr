<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-wcif.php';


  /**
   * retrieve_ordered_schedule()
   * @param (multi) competition_id: the ID of the competition to retrieve the schedule for
   * @return (array) the activities of the competition, ordered by start time
   */

  function retrieve_ordered_schedule( $competition_id )
  {
    [ $competition_data, $error ] = read_competition_data_from_public_wcif( $competition_id );

    if ( ! $error )
    {
      $ordered_schedule = array();

      foreach ( $competition_data['schedule']['venues'][0]['rooms'][0]['activities'] as $activity )
      {
        if ( $activity['childActivities'] )
        {
          foreach ( $activity['childActivities'] as $groups )
          {
            array_push( $ordered_schedule, [ 'name' => $groups['name'], 'startTime' => $groups['startTime'] ] );
          }
        }
        else
        {
          array_push( $ordered_schedule, [ 'name' => $activity['name'], 'startTime' => $activity['startTime'] ] );
        }
      }
      array_multisort( array_column( $ordered_schedule, 'startTime' ), SORT_ASC, $ordered_schedule );

      return $ordered_schedule;
    }

    return $error;
  }


  /**
   * retrieve_ordered_schedule()
   * @param (multi) competition_id: the ID of the competition to retrieve the schedule for
   * @return (array) the activities of the competition, ordered by start time
   */
  
  function get_stored_info( $competition_id, $mysqli )
  {
    require_once dirname( __DIR__, 2 ) . '/config/config_loader.php';
    $db = load_config_yaml( 'config-db' );
    
    $sql = "SELECT * FROM {$db['viewer']}_Current WHERE competition_id = '{$competition_id}'";

    $query_results = $mysqli->query( $sql );

    if ( $query_results->num_rows )
    {
      $result = $query_results->fetch_assoc();
      return $result;
    }
    
    return null;
  }
  
?>