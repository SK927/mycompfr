<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_POST['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {    
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );

    $sql = "SELECT user_email FROM {$db['cu']}_Orders_Info WHERE competition_id = '{$competition_id}'";

    if( $result = $mysqli->query( $sql ) )
    {
      $emails = '';

      while( $row = $result->fetch_assoc() )
      {
        $emails .= decrypt_data( $row['user_email'] ) . ' ; ';
      }

      $text_to_display = 'Adresses e-mails collées dans le presse-papier';
    }
    else
    {
      $text_to_display = 'Échec de la copie des e-mails';
      $error = $mysqli->error;
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
                'data' => $emails,
                'error' => $error
              );
  
  echo json_encode( $response );
  
?>
