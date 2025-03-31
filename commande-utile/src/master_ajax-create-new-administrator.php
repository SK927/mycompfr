<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
  require_once dirname( __FILE__ ) . '/_functions-master.php';
  require_once dirname( __FILE__ ) . '/_functions-email.php';

  if ( $_SESSION['logged_in'] AND $_SESSION['is_admin'] )
  {      
    $id = $_POST['administrator_id'];
    $email = $_POST['administrator_contact_email'];
    
    if ( ! $id )
    {
      $error_id = "L'ID de l'administrateur à ajouter est vide";
    }

    if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) )
    {
      $error_email = "L'adresses e-mail fournie est invalide"; 
    }
    
    if ( ! ( $error_id || $error_email ) )
    {
      [ $password, $error ] = create_administrator_in_db( $id, $email, $conn ); // Generate credentials for selected login and send to contact 
      
      if ( ! $error ) 
      {
        $other_administrators_email = get_administrators_emails( $conn, $id );
        $error = send_creation_credentials( $id, $password, $email, $other_administrators_email ); // Send deletion confirmation to contact email   
      }
    }

    if ( $error || $error_id || $error_email )
    {
      $text_to_display = "Échec de la création de l'administrateur...";
    }
    else
    {
      $text_to_display = 'Administrateur créé à jour avec succès !';
    }
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }
  
  $administrators_list = get_all_administrators( $conn );  // Retrieve all existing credentials 
  
  $conn->close();
  
  $response = array( 
                'text_to_display' => $text_to_display, 
                'error' => $error,
                'error_id' => $error_id,
                'error_email' => $error_email,
                'administrators' => $administrators_list,
              );

  echo json_encode( $response ); 
  
?>