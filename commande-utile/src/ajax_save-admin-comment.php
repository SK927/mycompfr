<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {  
    require_once dirname( __FILE__ ) . '/_functions.php';
  
    mysqli_open( $mysqli );
    $order_id = $_POST['order_id'];
    $admin_comment = mysqli_real_escape_string( $mysqli, $_POST['comment'] );
    
    $sql = "UPDATE {$db['cu']}_Orders_Info SET admin_comment = '{$admin_comment}' WHERE id = '{$order_id}'";
    
    if( $mysqli->query( $sql ) )
    {
      $text_to_display = 'Commentaire enregistré avec succès !';
    }
    else
    {
      $text_to_display = '&Eacute;chec de l\'enregistrement du commentaire...';
      $error = mysqli_error( $mysqli );
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

