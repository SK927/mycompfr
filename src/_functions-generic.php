<?php

  require_once dirname( __DIR__, 1 ) . '/config/config_loader.php';
  $db = load_config_yaml( 'config-db' );

  define( 'JSON_ATTR', JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );


  /**
   * to_pretty_json(): format data to JSON format
   * @param (multi) data: data to be formatted
   * @return (json string) formatted data as json string
   */

  function to_pretty_json( $data, $attr = JSON_ATTR )
  {
    return json_encode( $data, $attr );
  }  


  /**
   * from_pretty_json(): format JSON data to associative array
   * @param (json string) data: data to be formatted
   * @return (multi) formatted data as json string
   */

  function from_pretty_json( $data, $attr = JSON_ATTR )
  {
    return json_decode( $data, true, 512, $attr );
  }


  /** 
   * get_administrators_emails(): retrieve administrators email address
   * @param (mysqli) mysqli: mysqli object  
   * @param (mysqli) banned_login: the login of the administrator we want to exclude from the search (optional)
   * @return (string) the administrators email list as a string
   */

  function get_administrators_emails( $mysqli, $banned_login = '' )
  {
    global $db;

    $email_list = array();
    $sql = "SELECT email FROM {$db['sessions']}_AdminCredentials WHERE login != '{$banned_login}'";
    $results = $mysqli->query( $sql );

    while( $row = $results->fetch_assoc() )
    {
      array_push( $email_list, decrypt_data( $row['email'] ) );
    }

    return implode( ';', $email_list );
  }

?>