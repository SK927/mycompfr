<?php

  require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';


  /**
   * send_order_confirmation() : send an email to confirm the order has been properly stored in database
   * @param (array) competition_data: data of the competition, such as competition name
   * @param (string) order_id: ID of the order being stored in database
   * @param (string) user_email: email of the user who placed the order
   * @param (string) user_name: name of the user who placed the order
   * @param (array) order_data: data of the order being stored in database
   * @param (string) user_comment: comment made by the user who placed the order
   * @param (float) order_total: total of the order being stored in database
   * @param (bool) is_edit: value indicating if the order is being created or edited (optional)
   * @return (string) error if sending the email failed
   */

  function send_order_confirmation( $competition_data, $order_id, $user_email, $user_name, $order_data, $user_comment = null, $order_total, $is_edit = null )
  {        
    $to = decrypt_data( $user_email );
    $from = decrypt_data( $competition_data['contact_email'] );

    if ( $is_edit )
    {
      $subject = "{$competition_data['competition_name']} - Modification commande n° {$order_id}";
    }
    else
    {
      $subject = "{$competition_data['competition_name']} - Confirmation commande n° {$order_id}";
    }

    /* To send HTML mail, the Content-type header must be set */
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
     
    /* Create email headers */
    $headers .= "From: CommandeUtile\r\nBcc: [email]{$from}[/email]\r\n";
    $headers .= "Reply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
     
    /* Compose a simple HTML email message */
    $message = '<html><body>';
    $message .= "<p>Bonjour {$user_name},</p>";
    
    if ( $is_edit )
    {
      $message .= '<h3>VOTRE COMMANDE A BIEN &Eacute;T&Eacute; MODIFI&Eacute;E !</h3>';
    }
    else
    {  
      $message .= '<h3>MERCI POUR VOTRE COMMANDE !</h3>';
    }

    $message .= '<p>Vous trouverez ci-apr&egrave;s le r&eacute;capitulatif de votre commande';
    $message .= " n° <b>{$order_id}</b>.</p>";
    
    foreach ( $order_data as $block_name => $block_value )
    {
      $message .= "<p><b>{$block_name}</b>&nbsp;: ";
      unset( $block_value['given'] );

      foreach ( $block_value as $item_name => $item_value )
      { 

        $message .= "{$item_value['qty']} x {$item_name}";

        if ( isset( $item_value['options'] ) )
        {
          foreach ( $item_value['options'] as $option_name => $option_value )
          {
            $message .= ' [' . implode( ' - ', $option_value ) . ']';
          } 
        }
        $message .= ' ; ';
      
      }    
      $message .= '</p>';
    }
            
    if ( $user_comment ) 
    {
      $message .= "<p><b>Commentaire : </b>{$user_comment}</p>";
    }
    
    $order_total =number_format( $order_total, 2, '.', '' );

    $message .= "<p><b>Total : {$order_total} &euro;</b></p>";
    $message .= '<p>Merci de vous r&eacute;f&eacute;rer aux e-mails des organisateur·rice·s';
    $message .= '  pour le paiement.</p>';

    if ( $competition_data['competition_information'] != null ) 
    {
      $message .= "<p style=\"color:red\">Note de l'&eacute;quipe organisatrice : {$competition_data['competition_information']}</p>";
    }

    $closing_date = date( 'd/m/Y', strtotime( $competition_data['orders_closing_date'] ) );

    $message .= "<p>Pour modifier votre commande avant le {$closing_date},";
    $message .= " <a href=\"https://{$_SERVER['SERVER_NAME']}";
    $message .= '" target=\'_blank\'">cliquez ici</a></p>';
    $message .= '<p>Bonne journ&eacute;e et &agrave; bient&ocirc;t.</p>';
    $message .= '<p>----</p>';
    $message .= "<p>L'&eacute;quipe organisatrice du {$competition_data['competition_name']}</p>";
    $message .= '</body></html>';
              
    /* Sending email */
    if ( mail( $to, $subject, $message, $headers ) )
    {
      return null;

    } 
    else
    {
      return 'Échec de l\'envoi de l\'e-mail de confirmation';
    }
  }


  /**
   * send_order_cancellation() : send an email to confirm the order has been properly removed from the database
   * @param (array) competition_data: data of the competition, such as competition name
   * @param (string) order_id: ID of the order being removed from thedatabase
   * @param (string) user_email: email of the user who placed the order
   * @param (string) user_name: name of the user who placed the order
   * @return (string) error if sending the email failed
   */
    
  function send_order_cancellation( $competition_data, $order_id, $user_email, $user_name)
  {  
    $to = decrypt_data( $user_email );
    $from = decrypt_data( $competition_data['contact_email'] );

    $subject = "{$competition_data['competition_name']} - Annulation commande n° {$order_id}";

    /* To send HTML mail, the Content-type header must be set */
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
     
    /* Create email headers */
    $headers .= "From: CommandeUtile\r\nBcc: [email]{$from}[/email]\r\n";
    $headers .= "Reply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
     
    /* Compose a simple HTML email message */
    $message = '<html><body>';
    $message .= "<p>Bonjour {$user_name},</p>";
    $message .= "<p>Votre commande n° <b>{$order_id}</b> a bien &eacute;t&eacute;";
    $message .= ' annul&eacute;e.</p>';
    $message .= '<p>Bonne journ&eacute;e et &agrave; bient&ocirc;t.</p>';
    $message .= '<p>----</p>';
    $message .= "<p>L'&eacute;quipe organisatrice du {$competition_data['competition_name']}</p>";
    $message .= '</body></html>';
     
    /* Sending email */
    if ( mail( $to, $subject, $message, $headers ) )
    {
      return null;
    } 
    else
    {
      return 'Échec de l\'envoi de l\'e-mail de confirmation';
    }
  }


  /**
   * send_deletion_competition() : send an email to confirm the competition has been properly removed from the database
   * @param (string) competition_id: ID of the competition being removed from the database
   * @param (string) orga_email: email of the organizers of the competition
   * @param (string) all_administrators_email: email of all the website administrators
   * @return (string) error if sending the email failed
   */

  function send_deletion_competition( $competition_id, $orga_email, $all_administrators_email )
  {
    $to = $orga_email;
    $subject = "{$competition_id} - Suppression de la compétition";
    $from = $all_administrators_email;

    /* To send HTML mail, the Content-type header must be set */
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
     
    /* Create email headers */
    $headers .= "From: CommandeUtile\r\nBcc: [email]{$from}[/email]\r\n";
    $headers .= "Reply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
     
    /* Compose a simple HTML email message */
    $message = '<html><body>';
    $message .= '<p>Bonjour,</p>';
    $message .= "<p>La comp&eacute;tition ID <b>{$competition_id}</b> a bien &eacute;t&eacute;";
    $message .= " supprim&eacute;e du site <a href=\"https://{$_SERVER['SERVER_NAME']}\">Commande Utile</a>.</p>";
    $message .= '<p>Bonne journ&eacute;e et &agrave; bient&ocirc;t.</p>';
    $message .= '<p>----</p>';
    $message .= '<p>L\'&eacute;quipe d\'administration de Commande Utile</p>';
    $message .= '</body></html>';
     
    /* Sending email */
    if ( mail( $to, $subject, $message, $headers ) )
    {
      return null;
    } 
    else
    {
      return 'Échec de l\'envoi de l\'e-mail de confirmation';
    }
  }


  /**
   * send_deletion_credentials() : send an email to confirm the administrator has been properly removed from the database
   * @param (string) administrator_login: login of the administrator being removed from the database
   * @param (string) administrator_email: email of the administrator being removed from the database
   * @param (string) other_administrators_email: email of all the other website administrators
   * @return (string) error if sending the email failed
   */
  
  function send_deletion_credentials( $administrator_login, $administrator_email, $other_administrators_email )
  {
    $to = $administrator_email;
    $subject = "{$administrator_login} - Suppression des identifiants";
    $from = $other_administrators_email;

    /* To send HTML mail, the Content-type header must be set */
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
     
    /* Create email headers */
    $headers .= "From: CommandeUtile\r\nBcc: [email]{$from}[/email]\r\n";
    $headers .= "Reply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
     
    /* Compose a simple HTML email message */
    $message = '<html><body>';
    $message .= "<p>Bonjour {$administrator_login},</p>";
    $message .= '<p>Vos identifiants d\'administration ont &eacute;t&eacute;';
    $message .= " supprim&eacute;s du site <a href=\"https://{$_SERVER['SERVER_NAME']}\">Commande Utile</a>.</p>";
    $message .= '<p>Bonne journ&eacute;e et &agrave; bient&ocirc;t.</p>';
    $message .= '<p>----</p>';
    $message .= '<p>L\'&eacute;quipe d\'administration de Commande Utile</p>';
    $message .= '</body></html>';
     
    /* Sending email */
    if ( mail( $to, $subject, $message, $headers ) )
    {
      return null;
    } 
    else
    {
      return 'Échec de l\'envoi de l\'e-mail de confirmation';
    }
  }


  /**
   * send_creation_competition() : send an email to confirm the competition has been properly created in database
   * @param (string) competition_id: ID of the competition being removed from the database
   * @param (string) orga_email: email of the organizers of the competition
   * @param (string) all_administrators_email: email of all the website administrators
   * @return (string) error if sending the email failed
   */
  
  function send_creation_competition( $competition_id, $orga_email, $all_administrators_email )
  {
    $to = $orga_email;
    $subject = "{$competition_id} - Création de la compétition";
    $from = $all_administrators_email;

    /* To send HTML mail, the Content-type header must be set */
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
     
    /* Create email headers */
    $headers .= "From: CommandeUtile\r\nBcc: [email]{$from}[/email]\r\n";
    $headers .= "Reply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
     
    /* Compose a simple HTML email message */
    $message = '<html><body>';
    $message .= '<p>Bonjour,</p>';
    $message .= "<p>La comp&eacute;tition ID <b>{$competition_id}</b> a bien &eacute;t&eacute;";
    $message .= " cr&eacute;&eacute;e sur le site <a href=\"https://{$_SERVER['SERVER_NAME']}\">Commande Utile</a>.</p>";
    $message .= '<p>Vous pouvez d&eacute;sormais vous connecter en tant qu\'organisateur·rice.</p>';
    $message .= '<p>Bonne journ&eacute;e et &agrave; bient&ocirc;t.</p>';
    $message .= '<p>----</p>';
    $message .= '<p>L\'&eacute;quipe d\'administration de Commande Utile</p>';
    $message .= '</body></html>';
     
    /* Sending email */
    if ( mail( $to, $subject, $message, $headers ) )
    {
      return null;
    } 
    else
    {
      return 'Échec de l\'envoi de l\'e-mail de confirmation';
    }
  }
  

  /**
   * send_creation_credentials() : send an email to confirm the administrator has been properly created in database
   * @param (string) administrator_login: login of the administrator being removed from the database
   * @param (string) administrator_pw: generated password of the administrator being removed from the database
   * @param (string) administrator_email: email of the administrator being removed from the database
   * @param (string) other_administrators_email: email of all the other website administrators
   * @return error (string) if sending the email failed
   */

  function send_creation_credentials( $administrator_login, $administrator_pw, $administrator_email, $other_administrators_email )
  {
    $to = $administrator_email;
    $subject = "{$administrator_login} - Création des identifiants";
    $from = $other_administrators_email;

    /* To send HTML mail, the Content-type header must be set */
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
     
    /* Create email headers */
    $headers .= "From: CommandeUtile\r\n";
    $headers .= "Reply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
     
    /* Compose a simple HTML email message */
    $message = '<html><body>';
    $message .= "<p>Bonjour {$administrator_login},</p>";
    $message .= '<p>Vos identifiants de connexion admin se trouvent ci-après :</p>';
    $message .= "<ul><li><b>Login :</b> {$administrator_login}</li>";
    $message .= "<li><b>Mot de passe :</b> {$administrator_pw}</li></ul>";
    $message .= '<p>Bonne journ&eacute;e et &agrave; bient&ocirc;t.</p>';
    $message .= '<p>----</p>';
    $message .= '<p>L\'&eacute;quipe d\'administration de Commande Utile</p>';
    $message .= '</body></html>';
     
    /* Sending email */
    if ( mail( $to, $subject, $message, $headers ) )
    {
      return null;
    } 
    else
    {
      return 'Échec de l\'envoi de l\'e-mail de confirmation';
    }
  }


  /**
   * send_updated_credentials() : send an email to confirm the administrator has been properly updated in database
   * @param (string) administrator_login: login of the administrator being removed from the database
   * @param (string) administrator_pw: generated password of the administrator being removed from the database
   * @param (string) administrator_email: email of the administrator being removed from the database
   * @param (string) other_administrators_email: email of all the other website administrators
   * @return (string) error if sending the email failed
   */
  
  function send_updated_credentials( $administrator_login, $administrator_pw, $administrator_email, $other_administrators_email )
  {
    $to = decrypt_data( $administrator_email );
    $subject = "{$administrator_login} - Mise à jour des identifiants";
    $from = $other_administrators_email;

    /* To send HTML mail, the Content-type header must be set */
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
     
    /* Create email headers */
    $headers .= "From: CommandeUtile\r\n";
    $headers .= "Reply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
     
    /* Compose a simple HTML email message */
    $message = '<html><body>';
    $message .= '<p>Bonjour,</p>';
    $message .= '<p>Vos nouveaux identifiants de connexion admin se trouvent ci-après :</p>';
    $message .= "<ul><li><b>Login :</b> {$administrator_login}</li>";
    $message .= "<li><b>Mot de passe :</b> {$administrator_pw}</li></ul>";
    $message .= '<p>Bonne journ&eacute;e et &agrave; bient&ocirc;t.</p>';
    $message .= '<p>----</p>';
    $message .= '<p>L\'&eacute;quipe d\'administration de Commande Utile</p>';
    $message .= '</body></html>';
     
    /* Sending email */
    if ( mail( $to, $subject, $message, $headers ) )
    {
      return null;
    } 
    else
    {
      return 'Échec de l\'envoi de l\'e-mail de confirmation';
    }
  }
  
?>