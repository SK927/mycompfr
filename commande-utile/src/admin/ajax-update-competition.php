<?php
  
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
    require_once '../functions/competition-data-functions.php';
  
    $competition_data = get_competition_data( $competition_id, $conn ); 

    if ( $competition_data['competition_start_date'] < $_POST['competition_orders_closing_date'] ) 
    {
      $competition_orders_closing_date = $competition_data['competition_start_date'];
      $error_date = 'Date de clôture après la date de début de la compétition';
    }
    else 
    {
      $competition_orders_closing_date = $_POST['competition_orders_closing_date'];
    }

    $contact_emails = explode( ';', $_POST['competition_contact_email'] );

    $pass = true;
    foreach ( $contact_emails as $email ) 
    {
      $pass = $pass && filter_var( $email, FILTER_VALIDATE_EMAIL );
    }

    if ( ! $pass ) 
    {
      $competition_contact_email = $competition_data['contact_email'];
      $error_email = 'Une des adresses e-mail fournies est invalide';
    }
    else
    {
      $competition_contact_email = encrypt_data( $_POST['competition_contact_email'] );
    }

    $competition_info = mysqli_real_escape_string( $conn, $_POST['competition_information'] );

    if ( ! $error_email && ! $error_date ) 
    {
      $sql = "UPDATE " . DB_PREFIX_CU . "_Main SET contact_email = '{$competition_contact_email}', orders_closing_date = '{$competition_orders_closing_date}', competition_information = '{$competition_info}' WHERE competition_id = '{$competition_id}';";

      if ( $conn->query( $sql ) ) 
      {
        $text_to_display = 'Informations mise à jour avec succès !';
      }      
      else
      {
        $text_to_display = 'Échec de l\'enregistrement des nouvelles informations...';
        $error_mysqli = mysqli_error( $conn );
      }     
    }
    else
    {
      $text_to_display = 'Échec de la mise à jour des informations...';
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
                'error_email' => $error_email,
                'error_date' => $error_date,
                'error_mysqli' => $error_mysqli,
              );

  echo json_encode( $response );

?>
