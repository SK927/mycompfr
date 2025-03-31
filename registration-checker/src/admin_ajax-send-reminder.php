<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!

  $competition_id = $_POST['competition_id'];

  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) )
  {
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions.php';

    $query_results = $conn->query( "SELECT * FROM {$db['rg']}_Main WHERE competition_id = '{$competition_id}'"); 
    
    if ( $query_results->num_rows )
    {   
      $result_row = $query_results->fetch_assoc();
      $competitors_list = from_pretty_json( $result_row['competition_registrations'] );
      $non_responding_competitors_email = '';

      foreach ( $competitors_list as $competitor )
      {
        if ( $competitor['confirmed'] == 'NA') 
        {
          $non_responding_competitors_email .= decrypt_data( $competitor['email'] ) . ';';
        }
      }
      
      if ( ! $error = send_checker_reminder( $non_responding_competitors_email, $result_row['competition_name'], $_SESSION['user_email'] ) )
      {
        $text_to_display = 'Reminder sent successfully!';
      }
      else
      {
        $text_to_display = 'Unable to send reminder...';
      }
    }
    $conn->close();  
  }
  else
  {
    $text_to_display = 'Access denied!';
    $error = 'Not authenticated';    
  }

  $response = array(
                    'text_to_display' => $text_to_display,
                    'ajax_error' => $error,
                  );

  echo json_encode( $response );

?>
