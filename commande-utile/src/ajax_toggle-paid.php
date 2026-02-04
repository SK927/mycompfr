<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {    
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );
    $order_id = $_POST['order_id'];
    
    $sql = "SELECT paid FROM {$db['cu']}_Orders_Info WHERE id = '{$order_id}'";
    
    if( $result = $mysqli->query( $sql ) )
    {
      $row = $result->fetch_assoc();
      $sql = "UPDATE {$db['cu']}_Orders_Info SET paid = NOT paid WHERE id = '{$order_id}'";   

      if( $mysqli->query( $sql ) )
      {
        $text_to_display = 'Statut de paiement mis à jour avec succès !';
      }
      else
      {
        $text_to_display = '&Eacute;chec de la mise à jour du statut de paiement...';
        $error = mysqli_error( $mysqli );
      }
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
                'error' => $error,
              );
  
  echo json_encode( $response );

?>

