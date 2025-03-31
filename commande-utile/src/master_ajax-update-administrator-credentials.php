<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!
  require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
  require_once dirname( __FILE__ ) . '/_functions-master.php';
  require_once dirname( __FILE__ ) . '/_functions-email.php';

  if ( $_SESSION['logged_in'] AND $_SESSION['is_admin'] )
  {      
    $id = $_POST['administrator_id'];
    
    $query_results = $conn->query( "SELECT administrator_email FROM {$db['sessions']}_AdminCredentials WHERE administrator_login = '{$id}'"); // Get selected competition contact email   
    $email = decrypt_data( ( $query_results->fetch_assoc() )['administrator_email'] ); // Store in variable 
    
    [ $password, $error ] = regenerate_administrator_password( $id, $email, $conn ); // Generate credentials for selected login and send to contact 

    if ( ! $error )
    {
      $other_administrators_email = get_administrators_emails( $conn, $id );
      $error = send_creation_credentials( $id, $password, $email, $other_administrators_email, true ); // Send deletion confirmation to contact email
    }

    $text_to_display = $error ? 'Échec de la mise à jour du mot de passe administrateur...' : 'Mot de passe administrateur mis à jour avec succès !';
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