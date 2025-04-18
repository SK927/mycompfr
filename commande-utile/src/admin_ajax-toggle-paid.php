<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!

  $competition_id = $_POST['competition_id'];

  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
    require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';

    $order_id = $_POST['order_id'];
    
    if ( $conn->query( "UPDATE {$db['cu']}_{$competition_id} SET has_been_paid = NOT has_been_paid WHERE id = '{$order_id}'" ))
    {
      $text_to_display = 'Statut de paiement mis à jour avec succès !';
    }
    else
    {
      $text_to_display = 'Échec de la mise à jour du statut de paiement...';
      $error = mysqli_error( $conn );
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

