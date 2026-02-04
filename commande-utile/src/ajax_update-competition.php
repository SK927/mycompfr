<?php
  
  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_GET['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {    
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );
  
    $competition = get_competition_data( $competition_id, $mysqli );

    if( $competition['start_date'] < $_POST['competition_orders_closing_date'] ) 
    {
      $competition_orders_closing_date = '0000-00-00';
      $error_date = 'Date de clôture après la date de début de la compétition';
    }
    else 
    {
      $competition_orders_closing_date = $_POST['competition_orders_closing_date'];
    }

    $contact_emails = explode( ';', $_POST['competition_contact_email'] );
    $pass = true;

    foreach( $contact_emails as $email )
    {
      $pass = $pass && filter_var( $email, FILTER_VALIDATE_EMAIL );
    }

    if( ! $pass ) 
    {
      $competition_contact_email = $competition['contact_email'];
      $error_email = 'Au moins une des adresses e-mail fournies est invalide';
    }
    else
    {
      $competition_contact_email = encrypt_data( $_POST['competition_contact_email'] );
    }

    $competition_info = mysqli_real_escape_string( $mysqli, $_POST['admin_note'] );

    if( ! $error_email && ! $error_date ) 
    {
      $sql = "UPDATE {$db['cu']}_Competitions SET contact = '{$competition_contact_email}', orders_closing_date = '{$competition_orders_closing_date}', information = '{$competition_info}' WHERE id = '{$competition_id}'";

      if( $mysqli->query( $sql ) ) 
      {
        $text_to_display = 'Informations mise à jour avec succès !';
      }      
      else
      {
        $text_to_display = "Échec de l'enregistrement des nouvelles informations...";
        $error_mysqli = mysqli_error( $conn );
      }     
    }
    else
    {
      $text_to_display = 'Échec de la mise à jour des informations...';
    }

    $mysqli->close();
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error_mysqli = 'Not authenticated';
  }
  $response = array(
                'text_to_display' => $text_to_display, 
                'error_email' => $error_email,
                'error_date' => $error_date,
                'error_mysqli' => $error_mysqli,
              );

  echo json_encode( $response );

?>
