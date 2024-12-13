<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

  if ( $_SESSION['logged_in'] AND ! empty ( $_POST ) )
  {
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
    require_once '../custom-functions.php';
   
    $competition_id = decrypt_data( $_POST['competition_id'] );
    $new_state = $_POST['new_state'];
    
    /* Get selected order data*/
    $query_results = $conn->query( "SELECT competition_registrations FROM " . DB_PREFIX_RG . "_Main WHERE competition_id = '{$competition_id}';" ); 

    if ( $query_results )
    {
      $result_row = $query_results->fetch_assoc();
      $competitors_list = json_decode($result_row['competition_registrations'], true);

      $current_state = $competitors_list[ $_SESSION['user_id'] ]['confirmed'];

      if ( $current_state)
      {
        $competitors_list[ $_SESSION['user_id'] ]['confirmed'] = $current_state == $new_state ? 'NA' : $new_state;
     
        $error = update_competition_registrations( $competition_id, $competitors_list, $conn );
        
        $text_to_display = $error ? 'Unable to update registration status...' : 'Registration status updated successfully!';
      }
      else
      {
        $text_to_display = 'Unable to update registration status...';
        $error = 'You are not participating in that competition.';
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

