<?php

  require_once dirname( __DIR__, 2 ) . '/config/config-db.php';
  require_once dirname( __DIR__, 2 ) . '/config/localization.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/generic-functions.php';


  /**
   * retrieve_date_from_id()
   * @param (int) id: the current newsletter ID 
   * @return (array) the month and year of the current newsletter
   */

  function retrieve_date_from_id( $id )
  {
    $split_id = str_split( $id, 2 );
    $month = MONTH_STRINGS_FR[ (int)$split_id[1] ];
    $year = 2000 + (int)$split_id[0];

    return array( $month, $year );
  }


  function save_newsletter_into_db( $id, $post, $mysqli, $published = 0 )
  {
    $data = [];

    foreach ( $post as $key => $value )
    {
      $array_keys = explode( '_', $key );
      $data[ $array_keys[0] ][ $array_keys[1] ] = $value;
    }

    [ $month, $year ] = retrieve_date_from_id( $id );

    $data_formatted = mysqli_real_escape_string( $mysqli, to_pretty_json( $data ) );

    $sql = "REPLACE INTO " . DB_PREFIX_AFS . " (id, month, year, data, published) VALUES ({$id}, '{$month}', {$year}, '{$data_formatted}', $published)";

    $mysqli->query( $sql );

    return restore_newsletter_data_from_db( $id, $mysqli );
  }

  function restore_newsletter_data_from_db( $id, $mysqli )
  {
    $sql = "SELECT * FROM " . DB_PREFIX_AFS . " WHERE id = {$id}";
    $query_results = $mysqli->query( $sql );

    return $query_results->fetch_assoc();
  }

?>