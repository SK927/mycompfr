<?php

  require_once 'sessions_handler.php'; // $db is loaded here

  if ( isset( $_GET['code'] ) )
  {
    require_once dirname( __FILE__ ) . '/_functions-wcif.php';
    require_once dirname( __FILE__ ) . '/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/oauth_wca-api.php';
    require_once dirname( __FILE__ ) . '/oauth_wca-api.php';

    require_once dirname( __DIR__, 1 ) . '/config/config_loader.php';
    $rights = load_config_yaml( 'config-rights' );
    $oauth = load_config_yaml( 'config-oauth' );
    
    $wca = new WcaOauth( array(
                          'application_id' => $oauth['app_id'],
                          'application_secret' => $oauth['app_secret'], 
                          'redirect_uri' => "https://{$_SERVER['SERVER_NAME']}/src/oauth_sign-in.php",
                         ) );

    try
    {
      $wca->fetch_access_token( $_GET['code'] ); 
      $user = $wca->get_user(); // Get current user information

      $_SESSION['user_id'] = $user->id;
      $_SESSION['user_name'] = $user->name;
      $_SESSION['user_wca_id'] = $user->wca_id;
      $_SESSION['user_gender'] = $user->gender;
      $_SESSION['user_country'] = $user->country->name;
      $_SESSION['user_token'] = encrypt_data( $wca->get_access_token() );

      // Get additional information according to the target tool
      if ( isset( $_SESSION['captive'] ) )
      {
        if ( in_array( $_SESSION['captive'], $rights['need_email'] ) )
        {
          $_SESSION['user_email'] = encrypt_data( $user->email );
        }

        if( in_array( $_SESSION['captive'], $rights['need_dob'] ) )
        {
          $_SESSION['user_dob'] = encrypt_data( $user->dob );
        }
      
        if ( in_array( $_SESSION['captive'], array_merge( $rights['need_admin'], $rights['force_admin'] ) ) and $_SESSION['request_orga'] )
        {
          [ $_SESSION['manageable_competitions'], $error ] = get_competitions_managed_by_user( $_SESSION['user_token'] );
          
          if ( isset( $_SESSION['user_email'] ) )
          {
            $sql = "SELECT 1 FROM {$db['sessions']}_AdminCredentials WHERE administrator_email ='{$_SESSION['user_email']}'";
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