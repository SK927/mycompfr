<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['competition_id'];
  
  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions.php';

    [ $error, $emails ] = get_competitors_emails( $competition_id, $conn );

    if ( ! $error )
    {
      $text_to_display = 'Adresses e-mails collées dans le presse-papier';
    }
    else
    {
      $text_to_display = 'Échec de la copie des e-mails';
    }
    
    $conn->close();
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
