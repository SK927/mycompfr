<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['competition_id'];
  
  if( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) or $_SESSION['is_admin'] )
  {    
    require_once dirname( __FILE__ ) . '/_functions.php';
    require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';

    mysqli_open( $mysqli );
    [ $error, $emails ] = get_users_emails( $competition_id, $mysqli );
    $mysqli->close();

    if( ! $error )
    {
      $text_to_display = 'Adresses e-mails collées dans le presse-papier';
    }
    else
    {
      $text_to_display = 'Échec de la copie des e-mails...';
    }
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }
  
  $response = array( 
                'text_to_display' => $text_to_display, 
                'data' => $emails,
                'error' => $error
              );
  
  echo json_encode( $response );
  
?>
