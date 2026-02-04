<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  if( $_SESSION['logged_in'] AND ! empty ( $_POST ) )
  {
    require_once dirname( __FILE__ ) . '/_functions.php';
    require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';
    
    mysqli_open( $mysqli );

    $competition_id = $_POST['competition_id'];
    $user_id = decrypt_data( $_POST['user_id'] );
    $new_state = $_POST['new_state'];

    $results = $mysqli->query( "SELECT response FROM {$db['rg']}_Users WHERE user_id = '{$user_id}' AND competition_id = '{$competition_id}'" ); 

    if( $results )
    {
      $error = update_competition_registration( $competition_id, $user_id, $new_state, $mysqli );
      $text_to_display = $error ? 'Échec de la mise à jour du statut de participation...' : 'Statut de participation mis à jour avec succès !';      
    }
    else
    {
      $text_to_display = 'Échec de la mise à jour du statut de participation...';
      $error = 'Tu n\'es pas inscrit·e à cette compétition';
    }

    $mysqli->close();
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }
  
  $response = array(
                    'text_to_display' => $text_to_display,
                    'ajax_error' => $error,
                  );

  echo json_encode( $response );

?>

