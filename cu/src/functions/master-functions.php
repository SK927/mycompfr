<?php

  require_once dirname( __DIR__, 1 ) . '/config.php';

  require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/wcif-functions.php';

  require_once 'competitors-list-functions.php';


  /** 
   * get_all_imported_competitions(): retrieve all competitions imported in the database
   * @param (mysqli) mysqli: database connection object
   * @return (mysqli) all the imported competitions as a mysqli object
   */

  function get_all_imported_competitions( $mysqli )
  {   
    $query_results = $mysqli->query( "SELECT * FROM " . DB_PREFIX . "_Main ORDER BY competition_start_date, competition_name ASC;" );

    return $query_results;
  }


  /**
   * get_all_competitions_formatted_data(): retrieve and format all competitions imported in the database
   * @param (mysqli) mysqli: database connection object
   * @return (array) all the imported competitions formatted as an associative array
   */
   
  function get_all_competitions_formatted_data( $mysqli )
  {
    $competitions = get_all_imported_competitions( $mysqli );
    $competitions_list = [];

    while ( $result_row = $competitions->fetch_assoc() )
    {
      $result_row['contact_email'] = decrypt_data( $result_row['contact_email'] );
      $result_row['competition_id'] = $result_row['competition_id'];
      $result_row['competition_start_date'] = date( 'd/m/y', strtotime( $result_row['competition_start_date'] ) );
      $result_row['competition_end_date'] = date( 'd/m/y', strtotime( $result_row['competition_end_date'] ) );
      array_push( $competitions_list, $result_row );
    }

    return $competitions_list;
  }    
  
  
  /** 
   * get_all_administrators(): retrieve all administrators created in the database
   * @param (mysqli) mysqli: database connection object
   * @return (array) the administrators list as an associative array
   */
  
  function get_all_administrators( $mysqli )
  {   
    $administrators_list = [];
    $query_results = $mysqli->query( "SELECT administrator_login, administrator_email FROM ". DB_PREFIX . "_AdminCredentials ORDER BY administrator_login ASC;" );

    while ( $result_row = $query_results->fetch_assoc() )
    {
      $result_row['administrator_email'] = decrypt_data( $result_row['administrator_email'] );
      array_push( $administrators_list, $result_row );
    }

    return $administrators_list;
  }


  /** 
   * get_administrators_emails(): retrieve administrators email address
   * @param (mysqli) mysqli: database connection object
   * @param (mysqli) banned_login: the login of the administrator we want to exclude from the search (optional)
   * @return (string) the administrators email list as a string
   */

  function get_administrators_emails( $mysqli, $banned_login = '' )
  {
    $email_list = [];

    $query_results = $mysqli->query( "SELECT administrator_email FROM ". DB_PREFIX . "_AdminCredentials WHERE administrator_login != '{$banned_login}';" );

    while ( $result_row = $query_results->fetch_assoc() )
    {
      array_push( $email_list, decrypt_data( $result_row['administrator_email'] ) );
    }

    return implode( ';', $email_list );
  }   


  /**
   * create_competition_table(): create order table for a given competition
   * @param (string) competition_id: ID of the competition to create the table for
   * @param (mysqli) mysqli: database connection object
   * @return (string) the error of the mysqli query
   */

  function create_competition_table( $competition_id, $mysqli )
  {      
    $sql = "CREATE TABLE ". DB_PREFIX . "_{$competition_id} (
              `id` char(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `user_name` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `user_wca_id` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `user_email` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `order_data` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `order_total` float NOT NULL,
              `user_comment` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `has_been_paid` tinyint(1) NOT NULL,
              `has_been_modified` tinyint(1) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci";

    if ( ! $mysqli->query( $sql ) ) 
    {
      $error = mysqli_error( $mysqli );
    }

    return $error;
  }
  

  /**
   * add_primary_key(): add primary key to previously created competition orders table
   * @param (string competition_id: ID of the competition to update the table for
   * @param (mysqli) mysqli: database connection object
   * @return (string) the error of the mysqli query
   */

  function add_primary_key( $competition_id, $mysqli )
  {    
    $sql = "ALTER TABLE " . DB_PREFIX . "_{$competition_id} ADD PRIMARY KEY (id), ADD UNIQUE KEY(id);";
            
    if ( ! $mysqli->query( $sql ) ) 
    {
      $error = mysqli_error( $mysqli );
    }
    
    return $error;
  }
  

  /**
   * insert_competition_into_db(): read competition data from WCA, format it and store in database
   * @param (string) competition_id: ID of the competition to store the data for
   * @param (string) contact_email: contact email provided by organizers/delegates
   * @param (mysqli) mysqli: database connection object
   * @return (string) the error of the mysqli query
   */

  function insert_competition_into_db( $competition_id, $contact_email, $mysqli )
  {   
    [ $competition_data, $error ] = read_competition_data_from_public_wcif( $competition_id );

    if( ! $error )
    {
      $contact_email = encrypt_data( $contact_email );
      $competition_name = addslashes( $competition_data['name'] );
      $competition_start_date = $competition_data['schedule']['startDate'];
      $competition_end_date = date( 'Y-m-d', strtotime( $competition_start_date . ' + ' . (int)($competition_data['schedule']['numberOfDays'] - 1) . ' days' ) );
    
      $competitors_list = format_competitors_list( $competition_data['persons'] );

      $sql = "REPLACE INTO " . DB_PREFIX . "_Main (competition_id, competition_name, competition_start_date, competition_end_date, contact_email, orders_closing_date,competitors, competition_catalog, competition_information) VALUES ('{$competition_id}', '{$competition_name}', '{$competition_start_date}', '{$competition_end_date}', '{$contact_email}', '0000-00-00', '{$competitors_list}', '', '');";
      
      if ( $mysqli->query( $sql ) ) 
      {
        $error = mysqli_error( $mysqli );
      }
    }
    else
    {
      drop_competition_table_from_db( $competition_id, $mysqli ); /* Drop competition specific table */
    }
  
    return $error;
  }


  /**
   * drop_competition_table_from_db(): drop the competition order table for a given competition ID
   * @param (string) competition_id: ID of the competition to drop the table for
   * @param (mysqli) mysqli: database connection object
   * @return (string) the error of the mysqli query
   */

  function drop_competition_table_from_db( $competition_id, $mysqli )
  {
    $sql = "DROP TABLE " . DB_PREFIX . "_{$competition_id};";  
      
    if ( ! $mysqli->query( $sql ) ) 
    {
      $error = mysqli_error( $mysqli );
    }

    return $error;
  }


  /**
   * delete_competition_from_main_table(): remove the specified competition row in main table of database
   * @param (string) competition_id: ID of the competition to remove the row for
   * @param (mysqli) mysqli: database connection object
   * @return (string) the error of the mysqli query
   */
  
  function delete_competition_from_main_table( $competition_id, $mysqli )
  {
    $sql = "DELETE FROM " . DB_PREFIX . "_Main WHERE competition_id = '{$competition_id}';"; 

    if ( ! $mysqli->query( $sql ) ) 
    {
      $error = mysqli_error( $mysqli );
    }

    return $error;
  }


  /**
   * generate_password(): generate a random password
   * @return (array) password and its encrypted version
   */

  function generate_password()
  {
    $password = md5( rand() ); /* Generate password */
    $encrypted_password = encrypt_data( $password ); /* Encrypt password */
    
    return [ $password, $encrypted_password ];
  }
  

  /**
   * create_administrator_in_db(): add a new administrator in database
   * @param (string) id: login used by the administrator
   * @param (string) email: email address used by the administrator, must match email address of WCA profile
   * @param (mysqli) mysqli: database connection object
   * @return (array) the error of the mysqli query and the generated password
   */
   
  function create_administrator_in_db( $id, $email, $mysqli )
  {
    [ $password, $encrypted_password ] = generate_password();

    $encrypted_email = encrypt_data( $email );
      
    $sql = "REPLACE INTO " . DB_PREFIX . "_AdminCredentials ( administrator_login, administrator_password, administrator_email ) VALUE ( '{$id}', '{$encrypted_password}', '{$encrypted_email}');";
       
    if ( ! $mysqli->query( $sql ) )
    {        
      $error = mysqli_error( $mysqli );
    }
    
    return [ $password, $error ];
  }
  

  /**
   * regenerate_administrator_password(): regenerate the password for the specified administrator
   * @param (string) id: login used by the administrator
   * @param (mysqli) mysqli: database connection object
   * @return (array) the error of the mysqli query and the generated password
   */
  
  function regenerate_administrator_password( $id, $email, $mysqli )
  {
    [ $password, $encrypted_password ] = generate_password();
      
    $sql = "UPDATE " . DB_PREFIX . "_AdminCredentials SET administrator_password = '{$encrypted_password}' WHERE administrator_login = '{$id}';";
       
    if ( ! $mysqli->query( $sql ) )
    {
      $error = mysqli_error( $mysqli );
    }
    
    return [ $password, $error ];
  }
  
  
  /**
   * delete_administrator_from_db(): delete the specified adminstrator from database
   * @param (string) id: login used by the administrator
   * @param (mysqli) mysqli: database connection object
   * @return (string) the error of the mysqli query
   */
  
  function delete_administrator_from_db( $id, $mysqli )
  {
    $sql = "DELETE FROM " . DB_PREFIX . "_AdminCredentials WHERE administrator_login = '{$id}';"; 

    if ( ! $mysqli->query( $sql ) )
    {
      $error = mysqli_error( $mysqli );
    }

    return $error;
  }

?>