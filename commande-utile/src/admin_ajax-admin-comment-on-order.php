<?php
  
  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!

  $competition_id = $_POST['competition_id'];

  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';
    require_once dirname( __FILE__ ) . '/_functions-competition-data.php';
  
    $order_id = mysqli_real_escape_string( $conn, $_POST['order_id'] );
    $comment = mysqli_real_escape_string( $conn, $_POST['comment'] );

    $sql = "UPDATE {$db['cu']}_{$competition_id} SET admin_comment = '{$comment}' WHERE id ='{$order_id}'";

    if ( $conn->query( $sql ) ) 
    {
      $text_to_display = 'Informations mise à jour avec succès !';
    }      
    else
    {
      $text_to_display = "Échec de l'enregistrement des nouvelles informations...";
      $error_mysqli = mysqli_error( $conn );
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
                'error' => $error_mysqli,
              );

  echo json_encode( $response );

?>
