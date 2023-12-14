<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
 
  $competition_id = decrypt_data( $_POST['competition_id'] );

  if ( $_SESSION['logged_in'] AND $_POST['valid'] AND in_array( $competition_id, $_SESSION['manageable_competitions'] ) ) 
  {
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once '../custom-functions.php';

    if ( ! empty( $competition_id ) )
    {        
      $error = import_competition_into_db( $competition_id, $conn ); /* Create competition entry in main table */

      if ( ! $error )
      {
        $user_upcoming_manageable_competitions = from_pretty_json( decrypt_data( $_SESSION['encrypted_competitions_data'] ) );
        $user_upcoming_manageable_competitions[ $competition_id ]['is_imported'] = true;
        $_SESSION['encrypted_competitions_data'] = encrypt_data( to_pretty_json( $user_upcoming_manageable_competitions ) );
      }

      $text_to_display = $error ? 'Unable to import competition...' : 'Competition successfully imported!';
    }
    else
    {
      $error = 'Competition ID not defined...';
    }

    $conn->close();
  }
  else
  {
    $text_to_display = 'Access denied!';
    $error = 'Not authenticated...';
  }
  
  $response = array( 
                    'text_to_display' => $text_to_display, 
                    'error' => $error, 
                  );

  echo json_encode( $response );
  
?>

