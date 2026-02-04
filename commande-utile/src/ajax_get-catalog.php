<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );
  
  if( $is_manageable or $_SESSION['is_admin'] )
  {    
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );
    $catalog = get_catalog( $competition_id, $mysqli );
    $mysqli->close();
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }
  
  $response = array(
                'text_to_display' => $text_to_display,
                'array' => $catalog,
                'error' => $error,
              );

  echo json_encode( $response ); 
  
?>

