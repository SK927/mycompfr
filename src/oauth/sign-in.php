<?php

  require_once dirname( __DIR__, 1 ) . '/sessions/session-handler.php';

  if ( isset( $_GET['code'] ) )
  {
    require_once dirname( __DIR__, 1 ) . '/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 1 ) . '/functions/encrypt-functions.php';
    require_once dirname( __DIR__, 1 ) . '/functions/wcif-functions.php';
    require_once dirname( __DIR__, 2 ) . '/config/config-db.php';
    require_once dirname( __DIR__, 2 ) . '/config/config-rights.php';
    require_once dirname( __DIR__, 2 ) . '/config/config-oauth.php';
    require_once 'wca-oauth.php';
    
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

      /* Set session basic information */
      $_SESSION['user_id'] = $user->id;
      $_SESSION['user_name'] = $user->name;
      $_SESSION['user_wca_id'] = $user->wca_id;
      $_SESSION['user_gender'] = $user->gender;
      $_SESSION['user_country'] = $user->country->name;
      $_SESSION['user_token'] = encrypt_data( $wca->get_access_token() );

      /* Get additional information according to the target tool */
      if ( isset( $_SESSION['captive'] ) )
      {
        if ( in_array( $_SESSION['captive'], NEED_EMAIL ) )
        {
           $_SESSION['user_email'] = encrypt_data( $user->email );
        }

        if( in_array( $_SESSION['captive'], NEED_DOB ) )
        {
          $_SESSION['user_dob'] = encrypt_data( $user->dob );
        }
      
        if ( in_array( $_SESSION['captive'], NEED_ADMIN ) and $_SESSION['request_orga'] )
        {
          [ $_SESSION['manageable_competitions'], $error ] = get_competitions_managed_by_user( $_SESSION['user_token'] );
          
          if ( isset( $_SESSION['user_email'] ) )
          {
            $sql = "SELECT 1 FROM " . DB_PREFIX_SESSION . "_AdminCredentials WHERE administrator_email ='{$_SESSION['user_email']}'";
            $_SESSION['can_manage'] = $conn->query( $sql ) ? true : false;
          }
        }
      }
      $_SESSION['logged_in'] = true;
      unset( $_SESSION['request_orga'] );

    }
    catch ( exception $e )
    {
      $error =  $e;
    }  

    $conn->close();
  }

  if ( ! $error )
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$_SESSION['captive']}" );
    exit();
  }      
  else 
  {
    session_unset();
    session_destroy();
    echo $error;
  }
  
?>