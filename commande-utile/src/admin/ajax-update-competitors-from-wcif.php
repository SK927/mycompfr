<?php
  
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  
  $competition_id = $_POST['competition_id'];
  
  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
    require_once '../functions/competitors-list-functions.php';
    
    [ $list, $error ] = get_competitors_list_via_wcif( $competition_id, $conn );
    
    if ( ! $error )
    {
      $sql = "UPDATE " . DB_PREFIX_CU . "_Main SET competitors = '{$list}' WHERE competition_id = '{$competition_id}'";

      if ( $conn->query( $sql ) ) 
      {
        $text_to_display = 'Mise à jour de la liste effectuée avec succès !';
      }
      else
      {
        $text_to_display = 'Échec de la mise à jour de la liste...';
        $error = mysqli_error( $conn );
      }
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