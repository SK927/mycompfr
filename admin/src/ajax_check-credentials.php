<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; 

  if ( $_SESSION['logged_in'] )
  {
    if ( $_SESSION['can_manage'] )
    {
      require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';
      require_once dirname( __DIR__, 2 ) . '/src/mysqli.php';

      mysqli_open( $mysqli );

      $administrator_login = $_POST['login'];
      $administrator_password = encrypt_data( $_POST['password'] );
      $text_to_display = 'Connexion impossible !';
      $error_credentials = "Erreur sur l'identifiant ou le mot de passe";

      $results = $mysqli->query( "SELECT * FROM {$db['sessions']}_AdminCredentials WHERE login = '{$administrator_login}'" );

      if ( $results->num_rows )
      {
        $row = $results->fetch_assoc();

        if ( $row['email'] == $_SESSION['user_email'] ) 
        {
          if ( $row['password'] == $administrator_password )
          {
            $_SESSION['is_admin'] = true;
            $text_to_display = null;
            $error_credentials = null;
          }
        }
        else
        {
          $text_to_display = 'Connexion impossible !';
          $error_login = 'Cet identifiant ne correspond pas à votre adresse e-mail';
        }
      }

      $mysqli->close();
    }
    else
    {
      $text_to_display = 'Accès interdit !';
      $error = "Vous n'avez pas de droit d'administration sur ce site";
    }
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }

  $response = array( 
                'text_to_display' => $text_to_display, 
                'error' => $error,
                'error_login' => $error_login,
                'error_credentials' => $error_credentials,
              );

  echo json_encode( $response ); 

?>