<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

  if ( $_SESSION['logged_in'] )
  {
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';

    if ( $_SESSION['can_manage'] )
    {
      $administrator_login = $_POST['administrator_id'];
      $administrator_password = encrypt_data( $_POST['administrator_password'] );

      $text_to_display = 'Connexion impossible !';
      $error_credentials = 'Erreur sur l\'identifiant ou le mot de passe';

      $query_results = $conn->query( "SELECT * FROM ". DB_PREFIX_CU . "_AdminCredentials WHERE administrator_login = '{$administrator_login}';" );

      if ( $query_results->num_rows )
      {
        $result_row = $query_results->fetch_assoc();

        if ( $result_row['administrator_email'] == $_SESSION['user_email'] ) 
        {
          if ( $result_row['administrator_password'] == $administrator_password )
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
    }
    else
    {
      $text_to_display = 'Accès interdit !';
      $error = 'Vous n\'avez pas de droit d\'administration sur ce site';
    }

    $conn->close();
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