<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
  require_once dirname( __FILE__ ) . '/_functions-master.php';
  require_once dirname( __FILE__ ) . '/_functions-email.php';

  if ( $_SESSION['logged_in'] AND $_SESSION['is_admin'] )
  {    
    $competition_id = $_POST['competition_id'];

    if ( ! $competition_id ) 
    {
      $error_id = "L'ID de la compétition à ajouter est vide";
    }

    if ( $_POST['competition_contact_email'] )
    {
      $contact_emails = explode( ';', $_POST['competition_contact_email'] );
      
      $pass = true;

      foreach ( $contact_emails as $email )
      {
        $pass = $pass && filter_var( $email, FILTER_VALIDATE_EMAIL );
      }
      
      if ( ! $pass )
      {
        $error_email = 'Une des adresses e-mail fournies est invalide'; 
      }
    }
    else
    {
      $error_email = "L'adresse e-mail n'est pas renseignée";
    }

    if ( ! ( $error_id || $error_email ) )
    {
      $error = import_competition_data( $competition_id, $_POST['competition_contact_email'], $conn );      
    } 
      
    if ( $error || $error_id || $error_email ) 
    {
      $text_to_display = 'Échec de la création de la compétition...';
    }
    else
    {
      $text_to_display = 'Compétition créée à jour avec succès !';
    }
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }
  
  $competitions_list = get_all_competitions_formatted_data( $conn );

  $conn->close();

  $response = array( 
                'text_to_display' => $text_to_display, 
                'error' => $error,
                'error_id' => $error_id,
                'error_email' => $error_email,
                'competitions' => $competitions_list,
              );

  echo json_encode( $response ); 
  
?>

