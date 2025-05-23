<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
 
  $competition_id = $_POST['competition_id'];

  if ( $_POST['valid'] AND in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) ) 
  {
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions.php';

    if ( ! empty( $competition_id ) )
    {        
      $error = import_competition_into_db( $competition_id, $conn ); // Create competition entry in main table
      $text_to_display = $error ? 'Unable to import competition...' : 'Competition successfully imported!';
    }
    else
    {
      $error = 'Competition ID not defined...';
    }

    $conn->close();
  }
  else
  {
    $text_to_display = 'Access denied!';
    $error = 'Not authenticated...';
  }
  
  $response = array( 
                    'text_to_display' => $text_to_display, 
                    'error' => $error, 
                  );

  echo json_encode( $response );
  
?>

