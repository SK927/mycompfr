<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  
  if ( $_SESSION['logged_in'] AND $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once '../functions/master-functions.php';
    require_once '../functions/email-functions.php';
    
    $competition_id = $_POST['competition_id'];
    $query_results = $conn->query( "SELECT contact_email FROM ". DB_PREFIX_CU . "_Main WHERE competition_id = '{$competition_id}';" ); /* Get selected competition contact email */
    $contact_email = decrypt_data( ( $query_results->fetch_assoc() )['contact_email'] ); /* Store in variable */
    
    $error = drop_competition_table_from_db( $competition_id, $conn ); /* Drop competition specific table */
    
    if ( ! $error )
    {
      $error = delete_competition_from_main_table( $competition_id, $conn ); /* Delete competition entry from main table */
    }

    if ( ! $error ) 
    {
      $all_administrators_email = get_administrators_emails( $conn );
      $error = send_deletion_competition( $competition_id, $contact_email, $all_administrators_email ); /* Send deletion confirmation to contact email */  
    }

    $text_to_display = $error ? 'Échec de la suppression de la compétition...' : 'Compétition supprimée à jour avec succès !';

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

