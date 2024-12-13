<?php

  require_once '../src/sessions/session-handler.php';
  require_once '../config/localization.php';
  require_once 'src/markdown/Michelf/Markdown.inc.php';  

  use Michelf\Markdown;
  $parser = new Markdown;

  if ( isset( $_GET['id'] ) )
  {  
    require_once 'src/custom-functions.php';  
    require_once '../src/mysql/mysql-connect.php';
   
    $id = $_GET['id'];
    $newsletter = restore_newsletter_data_from_db( $id, $conn );    

    require_once 'src/layout/display-newsletter.php';

     $conn->close();
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/afs" );
    exit();
  }

?>
