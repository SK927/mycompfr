<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/config/config-rights.php'; 

  if ( $_SESSION['logged_in'] and in_array( $_SESSION['user_wca_id'], ADMINS_ID ) )
  {
    require_once dirname( __DIR__, 1 ) . '/custom-functions.php';  
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    
    if ( isset( $_GET['id'] ) )
    {  
      $id = $_GET['id'];

      if ( isset( $_POST['intro_title'] ) )
      {
        $data = save_newsletter_into_db( $id, $_POST, $conn, $_GET['published'] );    
      }
    }

    $conn->close();
  }

  header( "Location: https://{$_SERVER['SERVER_NAME']}/afs/admin-edit-newsletter" );
  exit();

?>