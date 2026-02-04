<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; 

  $competition_id = $_POST['competition_id'];

  if( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) )
  {
    require_once dirname( __FILE__ ) . '/_functions.php';
    require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';

    mysqli_open( $mysqli );
    [ $error, $registrations ] = get_competition_registrations_from_db( $competition_id, $mysqli );

    if( ! $error )
    {  
      [ $error, $emails ] = get_users_emails( $competition_id, $mysqli );
      
      if( ! $error )
      {
        $error = send_checker_reminder( $_SESSION['manageable_competitions'][ $competition_id ]['name'], $emails, $_SESSION['user_email'] );
      }
    }

    if( ! $error )
    {
      $text_to_display = 'Rappel envoyé avec succès !';
    }
    else
    {
      $text_to_display = 'Échec de l\'envoi du rappel...';
    }

    $mysqli->close();  
  }
  else
  {
    $text_to_display = 'Access denied!';
    $error = 'Not authenticated';    
  }

  $response = array(
                    'text_to_display' => $text_to_display,
                    'error' => $error,
                  );

  echo json_encode( $response );

?>
