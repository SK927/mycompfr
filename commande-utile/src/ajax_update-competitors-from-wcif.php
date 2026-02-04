<?php
  
  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {    
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );
    [ $list, $error ] = get_competitors_list_via_wcif( $competition_id );
    
    if( ! $error )
    {
      $sql = "UPDATE {$db['cu']}_Competitions SET competitors_list = '{$list}' WHERE id = '{$competition_id}'";

      if( $mysqli->query( $sql ) ) 
      {
        $text_to_display = 'Mise à jour de la liste effectuée avec succès !';
      }
      else
      {
        $text_to_display = 'Échec de la mise à jour de la liste...';
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