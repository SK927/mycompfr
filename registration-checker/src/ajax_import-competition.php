<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['competition_id'];

  if( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) )
  {
    if( ! empty( $competition_id ) )
    {        
      require_once dirname( __FILE__ ) . '/_functions.php';
      require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';
      
      mysqli_open( $mysqli );

      $error = import_competition_into_db( $competition_id, $_SESSION[ 'user_email' ], $_SESSION[ 'user_token' ], $mysqli ); /* Create competition entry in main table */

      if( ! $error )
      {
        $error = send_creation_competition_rc( $competition_id, decrypt_data( $_SESSION[ 'user_email' ] ), get_administrators_emails( $mysqli ) );
      }

      $text_to_display = $error ? 'Échec d\'importation de la compétition...' : 'Compétition importée avec succès !';

      $mysqli->close();
    }
    else
    {
      $text_to_display = 'Échec d\'importation de la compétition...';
      $error = 'Compétition introuvable !';
    }
    
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }

  $response = array( 
                    'text_to_display' => $text_to_display, 
                    'error' => $error, 
                  );

  echo json_encode( $response );
  
?>

