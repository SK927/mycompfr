<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!

  $competitions_list = null;

  if ( $_SESSION['logged_in'] AND $_POST['valid'] )
  {
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';

    $user_id = $_SESSION['user_id'];
    
    $query_results = $conn->query( "SELECT * FROM {$db['rh']}_Main ORDER BY competition_start_date ASC, competition_name ASC" );

    while ( $result_row = $query_results->fetch_assoc() ) 
    {            
      $result_row['competition_id'] = $result_row['competition_id'];
      $result_row['competitor_registration'] = json_decode( $result_row['competition_registrations'], true )[ $user_id ];
      $result_row['competition_start_date'] = date( 'd-m-Y', strtotime( $result_row['competition_start_date'] ) );
      $result_row['competition_end_date'] = date( 'd-m-Y', strtotime($result_row['competition_end_date'] ) );
      $competition_list[] = $result_row;
    }
  }

  $response = array( 
                    'ajax_error' => $error, 
                    'competition_list' => $competition_list, 
                  );

  echo json_encode( $response );

?>