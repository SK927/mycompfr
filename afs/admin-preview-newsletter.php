<?php

  require_once '../src/sessions/session-handler.php';
  require_once '../config/config-rights.php';
  require_once '../config/localization.php';
  require_once 'src/markdown/Michelf/Markdown.inc.php';  

  use Michelf\Markdown;
  $parser = new Markdown;

  if ( $_SESSION['logged_in'] and in_array( $_SESSION['user_wca_id'], ADMINS_ID ) )
  {
    require_once 'src/custom-functions.php';  
    require_once '../src/mysql/mysql-connect.php';

    if ( isset( $_GET['id'] ) )
    {  
      $id = $_GET['id'];

      if ( isset( $_POST['intro_title'] ) )
      {
        $newsletter = save_newsletter_into_db( $id, $_POST, $conn );  
      }

      require_once 'src/layout/display-newsletter.php';
    }

    $conn->close();
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/afs" );
    exit();
  }

?>