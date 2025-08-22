<?

  require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; 
  require_once '_functions.php'; 

  $captive_message = '';

  if ( ! empty( $_SESSION['user_wca_id'] ) )
  {
    $last_updated = check_last_updated( $_SESSION['user_wca_id'], $conn );

    if ( strtotime( $last_updated ) <= strtotime( '-1 week' ) )
    {
      $user_competitions = get_competitions_to_update( $last_updated, $_SESSION['user_token'] );
      [ $imported_competitions, $error ] = update_competitions_in_db( $user_competitions, $conn );
      
      $captive_message .= '<p><u>These competitions were <b>imported successfully</b>:</u> ' . implode( ', ', $imported_competitions ) . '</p>';

      if ( $error )
      {
        $captive_message .= '<p style="color:red">One or more competitions have not been imported properly. Please refresh the page and contact the administrator if the problem persists.</p>';
      }
      else
      {
        update_user_last_updated( $_SESSION['user_wca_id'], $conn );
        $captive_message .= '<p>Redirecting to the <a href="statistics.php">statistics page</a> in a few seconds.</p>';
      }
    }
  }
  else
  {
    $captive_message = "You must have a WCA ID to retrieve your statistics. Participate to a competition or contact your local WCA Delegate.";
  }

  $response = array( 
               'captive' => $captive_message,
               'error' => $error,
            );
  
  echo json_encode( $response );

?>