<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  if ( $_SESSION['logged_in'] AND ! empty ( $_POST ) )
  {
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions.php';
   
    $competition_id = decrypt_data( $_POST['competition_id'] );
    $user_id = decrypt_data( $_POST['user_id'] );
    $new_state = $_POST['new_state'];
    $query_results = $conn->query( "SELECT competition_registrations FROM {$db['rg']}_Main WHERE competition_id = '{$competition_id}'" ); 

    if ( $query_results )
    {
      $result_row = $query_results->fetch_assoc();
      $competitors_list = json_decode($result_row['competition_registrations'], true);

      $current_state = $competitors_list[ $user_id ]['confirmed'];

      if ( $current_state )
      {
        $competitors_list[ $user_id ]['confirmed'] = $new_state;
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

