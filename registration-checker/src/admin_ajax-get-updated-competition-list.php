<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!

  $competitions_list = null;

  if ( $_SESSION['logged_in'] AND $_POST['valid'] )
  {
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions.php';

    $user_id = $_SESSION['user_id'];
    
    $query_results = $conn->query( "SELECT * FROM {$db['rg']}_Main WHERE competition_registrations LIKE '%\"{$user_id}\"%' ORDER BY competition_start_date ASC, competition_name ASC" );

    while ( $result_row = $query_results->fetch_assoc() ) 
    {            
      $result_row['competition_id'] = encrypt_data( $result_row['competition_id'] );
      $result_row['competition_name'] = addslashes( $result_row['competition_name'] );
      $result_row['competitor_id'] = encrypt_data( $user_id );
      $result_row['competitor_registration'] = json_decode( $result_row['competition_registrations'], true )[ $user_id ];
      $result_row['competition_start_date'] = date( 'd-m-Y', strtotime( $result_row['competition_start_date'] ) );
      $result_row['competition_end_date'] = date( 'd-m-Y', strtotime($result_row['competition_end_date'] ) );
      $competition_list[] = $result_row;
    }
  }
  
  $response = array( 
                    'ajax_status' => (string) $ajax_status, 
                    'text_to_display' => $text_to_display, 
                    'ajax_error' => $ajax_error, 
                    'competition_list' => $competition_list, 
                  );

  echo json_encode( $response );

?>