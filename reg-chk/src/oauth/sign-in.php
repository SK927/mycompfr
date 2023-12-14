<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

  if ( isset( $_GET['code'] ) )
  {
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
    require_once dirname( __DIR__, 3 ) . '/src/oauth/wca-oauth.php';
    require_once '../custom-functions.php';
    require_once 'oauth-credentials.php';

    $user = null;
    
    /* Set WCA auth information */
    $wca = new WcaOauth( array(
                          'application_id' => APP_ID,
                          'application_secret' => APP_SECRET, 
                          'redirect_uri' => "https://{$_SERVER['SERVER_NAME']}/src/oauth/sign-in.php",
                         ));

    try
    {
      $wca->fetch_access_token( $_GET['code'] ); /* Get auth token */
      $user = $wca->get_user(); /* Get current user information */

      /* Set session information */
      $_SESSION['user_id'] = $user->id;
      $_SESSION['user_name'] = $user->name;
      $_SESSION['user_email'] = encrypt_data( $user->email );
      $_SESSION['user_wca_id'] = $user->wca_id;
      $_SESSION['user_token'] = encrypt_data( $wca->get_access_token() );
      $_SESSION['user_gender'] = $user->gender;
      $_SESSION['user_country'] = $user->country->name;

      $error = get_user_manageable_competitions( $_SESSION['user_token'], $conn );

      $_SESSION['logged_in'] = true;
    }
    catch ( exception $e )
    {
      $error = $e;
    }    

    $conn->close();       
  }

  if ( ! $error )
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}" );
    exit();
  }
  else 
  {
    // session_unset();
    // session_destroy();
    echo $error;
  }
  
?>