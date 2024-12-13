<?php

  require_once dirname( __DIR__, 1 ) . '/sessions/session-handler.php';
  require_once dirname( __DIR__, 2 ) . '/config/config-rights.php';
  require_once dirname( __DIR__, 2 ) . '/config/config-oauth.php';

  $application_id = APP_ID; 
  $redirect_uri = "https://{$_SERVER['SERVER_NAME']}/src/oauth/sign-in.php";
  $scopes = "public";

  /* Set scopes according to the selected tool requirements */
  if ( isset( $_GET['captive_for'] ) )
  {
    $_SESSION['captive'] = $_GET['captive_for'];

    $scopes .= in_array( $_SESSION['captive'], NEED_EMAIL ) ? '+email' : '';
    $scopes .= in_array( $_SESSION['captive'], NEED_DOB ) ? '+dob' : '';
  
    if ( in_array( $_SESSION['captive'], NEED_ADMIN ) and $_POST['request_orga'] )
    {
      $_SESSION['request_orga'] = true;
      $scopes .= '+manage_competitions'; 
    }
  }

  $target_url = "https://www.worldcubeassociation.org/oauth/authorize?response_type=code&client_id={$application_id}&scope={$scopes}&redirect_uri={$redirect_uri}";

  header( "Location: {$target_url}" );    
  exit();
    
?>