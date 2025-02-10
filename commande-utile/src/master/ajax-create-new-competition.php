<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
  require_once '../functions/master-functions.php';
  require_once '../functions/email-functions.php';

  if ( $_SESSION['logged_in'] AND $_SESSION['is_admin'] )
  {    
    $competition_id = $_POST['competition_id'];

    if ( ! $competition_id ) 
    {
      $error_id = 'L\'ID de la compétition à ajouter est vide';
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
      $error_email = 'L\'adresse e-mail n\'est pas renseignée';
    }

    if ( ! ( $error_id || $error_email ) )
    {
      if ( ! $error )
      {
        $error = create_competition_table( $competition_id, $conn ); /* Create competition specific table */ 
      }

      if ( ! $error )
      {
        $error = add_primary_key( $competition_id, $conn ); /* Add primary key to newly created table */ 
      }
      
      if ( ! $error )
      {
         $error = insert_competition_into_db( $competition_id, $_POST['competition_contact_email'], $conn ); /* Create competition entry in main table */  
      }

      if ( ! $error ) 
      {
        $all_administrators_email = get_administrators_emails( $conn );
        $error = send_creation_competition( $competition_id, $_POST['competition_contact_email'], $all_administrators_email ); /* Send deletion confirmation to contact email */  
      }
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

