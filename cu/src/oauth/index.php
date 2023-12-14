<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  require_once 'oauth-credentials.php';

  if ( $_POST['request_orga'] )
  {
    $_SESSION['request_orga'] = true;
  }

  $application_id = APP_ID; 
  $redirect_uri = "https://{$_SERVER['SERVER_NAME']}/src/oauth/sign-in.php";
  $target_url = "https://www.worldcubeassociation.org/oauth/authorize?response_type=code&client_id={$application_id}&scope=public+email+manage_competitions&redirect_uri={$redirect_uri}";
  
  header( "Location: {$target_url}" );    
  exit();
    
?>