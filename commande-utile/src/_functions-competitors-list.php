<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-wcif.php';
  require_once dirname( __DIR__, 2 ) . '/config/config_loader.php';
  $db = load_config_yaml( 'config-db' );


  /**
   * format_competitors_list(): format the competitors list from WCA to the format used in database
   * @param (array) competitors_list: list of competitors in WCA associative array format
   * @return (string) the formatted list as a string to be stored in database
   */

  function format_competitors_list( $competitors_list )
  {
    $formatted_list = '';

    foreach ( $competitors_list as $competitor )
    {
      $formatted_list .= "[{$competitor['wcaUserId']}]";
    }

    return $formatted_list;
  }


  /**
   * get_competitors_list_via_wcif(): retrieve competitors list from WCA and updates it in database
   * @param (string) competition_id: ID of the competition to update the data for
   * @param (mysqli) mysqli: database connection object
   * @return (array) the list of all competitor and the WCIF access error
   */

  function get_competitors_list_via_wcif( $competition_id, $mysqli )
  {
    [ $competitors, $error ] = get_competitors_from_public_wcif( $competition_id );

    if ( ! $error )
    {
      $formatted_list = format_competitors_list( $competitors );
    }

    return [ $formatted_list, $error ];
  }


  /**
   * get_competitors_emails(): retrieve competitors emails from database
   * @param (string) competition_id: ID of the competition to update the data for
   * @param (mysqli) mysqli: database connection object
   * @return (string) a list of all competitors emails
   */

  function get_competitors_emails( $competition_id, $mysqli )
  {
    global $db;

    $emails = '';
    $sql = "SELECT user_email FROM {$db['cu']}_{$competition_id} WHERE 1";
    $query_results = $mysqli->query( $sql );

    while( $row = $query_results->fetch_assoc() )
    {
      $emails .= decrypt_data( $row['user_email'] ) . ' ; ';
    }

    $error = mysqli_error( $mysqli ) ? mysqli_error( $mysqli ) : null;
  
    return [ $emails, $error ];
  }

?>