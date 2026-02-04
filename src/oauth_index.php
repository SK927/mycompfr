<?php

  require_once 'sessions_handler.php';

  require_once dirname( __DIR__, 1 ) . '/config/config_loader.php';
  $rights = load_config_yaml( 'config-rights' );
  $oauth = load_config_yaml( 'config-oauth' );

  $redirect_uri = "https://{$_SERVER['SERVER_NAME']}/src/oauth_sign-in.php";
  $scopes = 'public';

  // Set scopes according to the selected tool requirements
  if( isset( $_GET['captive_for'] ) )
  {
    $_SESSION['captive'] = $_GET['captive_for'];

    $scopes .= in_array( $_SESSION['captive'], $rights['need_email'] ) ? '+email' : '';
    $scopes .= in_array( $_SESSION['captive'], $rights['need_dob'] ) ? '+dob' : '';
  
    if( in_array( $_SESSION['captive'], array_merge( $rights['need_admin'], $rights['force_admin'] ) ) and $_POST['request_orga'] )
    {
      $_SESSION['request_orga'] = true;
      $scopes .= '+manage_competitions'; 
    }
  }

  $target_url = "https://www.worldcubeassociation.org/oauth/authorize?response_type=code&client_id={$oauth['app_id']}&scope={$scopes}&redirect_uri={$redirect_uri}";

  header( "Location: {$target_url}" );    
  exit();
    
?>