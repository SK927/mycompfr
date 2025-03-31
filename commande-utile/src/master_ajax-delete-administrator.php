<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!
  
  if ( $_SESSION['logged_in'] AND $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions-master.php';
    require_once dirname( __FILE__ ) . '/_functions-email.php';

    $login = $_POST['administrator_id'];
    
    if( $login != 'Administrator' )
    {
      $error = null;

      $results = $conn->query( "SELECT administrator_email FROM {$db['sessions']}_AdminCredentials WHERE administrator_login = '{$login}'" ); // Get selected competition contact email   
      $contact_email = decrypt_data( ($results->fetch_assoc())['administrator_email'] ); // Store in variable 
      
      $error = delete_administrator_from_db( $login, $conn ); // Delete competition entry from main table 
      
      if ( ! $error ) 
      {
        $other_administrators_email = get_administrators_emails( $conn, $id );
        $error = send_deletion_credentials( $login, $contact_email, $other_administrators_email ); // Send deletion confirmation to contact email  
      }
      $text_to_display = $error ? 'Échec de la suppression des identifiants...' : 'Identifiants supprimés avec succès !'; 
    }
    else
    {
      $text_to_display = 'Vous ne pouvez pas supprimer cet identifiant !';
      $error = 'Compte administrateur';
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
                'error' => $error,
              );

  echo json_encode( $response ); 
  
?>

