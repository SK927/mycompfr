<?php

  require_once 'oauth-credentials.php';

  $application_id = APP_ID; 
  $redirect_url = "https://{$_SERVER['SERVER_NAME']}/src/oauth/sign-in.php"; 
  $target_url = "https://www.worldcubeassociation.org/oauth/authorize?response_type=code&client_id={$application_id}&scope=public+email+manage_competitions&redirect_uri={$redirect_url}";

  header( "Location: {$target_url}" );    
  exit();
    
?>