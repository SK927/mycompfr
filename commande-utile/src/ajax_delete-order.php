<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {    
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );
    $competition = get_competition_data( $competition_id, $mysqli );
    $order_id = $_POST['order_id'];
    [ $error, $order_info ] = delete_order( $order_id, $mysqli );
    $mysqli->close();

    if( ! $error )
    {
      $error = send_order_cancellation( $competition, $order_info );
    }

    if( $error )
    {
      $text_to_display = 'Échec de la suppression de la commande...';
    }
    else
    {      
      $text_to_display = 'Suppression de la commande effectuée avec succès !  Rechargement de la page...';
    }
    
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }
  
  $response = array( 
                'text_to_display' => $text_to_display, 
                'error' => $error
              );
  
  echo json_encode( $response );
  
?>
