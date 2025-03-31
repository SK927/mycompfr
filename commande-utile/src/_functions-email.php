<?php

  require_once dirname( __DIR__, 2 ) . '/src/_functions-encrypt.php';
  require_once dirname( __DIR__, 2 ) . '/src/yaml_spyc-reader.php';
  require_once dirname( __DIR__, 2 ) . '/src/_class-email.php';


  /**
   * format_email_order() : format the order data to be displayed in an email
   * @param (array) catalog: the catalog of the competition
   * @param (array) order: data of the order being formatted
   * @param (string) user_comment: comment made by the user who placed the order
   * @param (float) order_total: total of the order being stored in database
   * @param (bool) is_edit: value indicating if the order is being created or edited (optional)
   * @return (string) error if sending the email failed
   */

  function format_email_order( $catalog, $order )
  {
    $order_text = '<ul>';

    foreach ( $order as $block_key => $block )
    {
      $order_text .= "<li><b>{$catalog[ $block_key ]['name']}</b>&nbsp;: ";

      foreach ( $block['items'] as $item_key => $item )
      {
        $order_text .= "{$item['qty']} x {$catalog[ $block_key ]['items'][ $item_key ]['name']}";
        $options = '';

        foreach ( $item['options'] as $option )
        {
          $option_text = '';

          foreach ( $option as $option_key => $selection_key ) 
          {
            $option_text .= empty( $option_text ) ? '' :  ' - ';
            $option_text .= $catalog[ $block_key ]['items'][ $item_key ]['options'][ $option_key ]['selections'][ $selection_key ]['name'];
          }

          $options .= "[{$option_text}]";
        }
        $order_text .= empty( $options ) ? ' ; ' : " {$options} ; ";
      }
      $order_text .= '</li>';
    }
    $order_text .= '</ul>';

    return $order_text;
  }

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

  function send_order_confirmation( $competition_data, $order_id, $user_email, $user_name, $user_order, $order_total, $user_comment = null, $is_edit = null )
  {       
    $to = decrypt_data( $user_email );
    $from = decrypt_data( $competition_data['contact_email'] );
    $catalog = from_pretty_json( $competition_data['competition_catalog'] );
    $user_order = format_email_order( $catalog, $user_order );
    $order_total =number_format( $order_total, 2, '.', '' );
    $closing_date = date( 'd/m/Y', strtotime( $competition_data['orders_closing_date'] ) );
    $folder =  explode( '/' , $_SERVER['REQUEST_URI'] )[1];
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_confirm_order'];

    $email = new email();
    $email->create_header( $from );

    if ( $is_edit )
    {
      $email->subject = $content['subject']['edit'];
    }
    else
    {
      $email->subject = $content['subject']['confirm'];
    }

    foreach ( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );

    if ( $competition_data['competition_information'] != null ) 
    {
      $email->concatenate_to_message( "<p style=\"color:red\">{$content['note']}</p>" );
    }

    $email->concatenate_to_message( '</body></html>' );

    $email->replace_subject_text( "{competition_name}", $competition_data['competition_name'] );
    $email->replace_subject_text( "{order_nr}", $order_id );    
    $email->replace_message_text( "{competition_name}", $competition_data['competition_name'] );
    $email->replace_message_text( "{order_nr}", $order_id );
    $email->replace_message_text( "{username}", $user_name );
    $email->replace_message_text( "{order}", $user_order );
    $email->replace_message_text( "{user_comment}", $user_comment );
    $email->replace_message_text( "{order_total}", $order_total );
    $email->replace_message_text( "{closing_date}", $closing_date );
    $email->replace_message_text( "{site}", "https://{$_SERVER['SERVER_NAME']}/{$folder}" );
    $email->replace_message_text( "{admin_note}", $competition_data['competition_information'] );

    // Send email
    if ( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
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
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_delete_order'];

    $email = new email();
    $email->create_header( $from );
    $email->subject = $content['subject'];

    foreach ( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );

    $email->replace_subject_text( "{competition_name}", $competition_data['competition_name'] );
    $email->replace_subject_text( "{order_nr}", $order_id );    
    $email->replace_message_text( "{competition_name}", $competition_data['competition_name'] );
    $email->replace_message_text( "{order_nr}", $order_id );
    $email->replace_message_text( "{username}", $user_name );

    // Send email
    if ( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
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
    $from = $all_administrators_email;
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_create_competition'];

    $email = new email();
    $email->create_header( $from );
    $email->subject = $content['subject'];

    foreach ( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );
     
    $email->replace_subject_text( "{competition_id}", $competition_id );
    $email->replace_message_text( "{competition_id}", $competition_id );    

    // Send email
    if ( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
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
    $from = $all_administrators_email;
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_delete_competition'];

    $email = new email();
    $email->create_header( $from );
    $email->subject = $content['subject'];

    foreach ( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );
     
    $email->replace_subject_text( "{competition_id}", $competition_id );
    $email->replace_message_text( "{competition_id}", $competition_id );    

    // Send email
    if ( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
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

  function send_creation_credentials( $administrator_login, $administrator_pw, $administrator_email, $other_administrators_email, $is_update = false )
  {
    $to = $administrator_email;
    $from = $other_administrators_email;
    $credentials = "<ul><li><b>Login :</b> {$administrator_login}</li>
                        <li><b>Mot de passe :</b> {$administrator_pw}</li>
                    </ul>";
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_create_credentials'];

    $email = new email();
    $email->create_header( $from );

    if ( $is_update )
    {
      $email->subject = $content['subject']['update'];
    }
    else
    {
      $email->subject = $content['subject']['create'];
    }
   
    foreach ( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );
     
    $email->replace_subject_text( "{admin}", $administrator_login );
    $email->replace_message_text( "{admin}", $administrator_login );    
    $email->replace_message_text( "{credentials}", $credentials );    

    // Send email
    if ( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
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
    $from = $other_administrators_email;
    $content = spyc_load_file( dirname( __DIR__, 1 ) . "/assets/emails.yaml" )['email_delete_credentials'];

    $email = new email();
    $email->create_header( $from );
    $email->subject = $content['subject'];

    foreach ( $content['text'] as $paragraph )
    {
      $email->concatenate_to_message( "<p>{$paragraph}</p>" );
    }

    $email->concatenate_to_message( "<p>----</p>" );
    $email->concatenate_to_message( "<p>{$content['sign']}</p>" );
    $email->concatenate_to_message( '</body></html>' );
     
    $email->replace_subject_text( "{admin}", $administrator_login );
    $email->replace_message_text( "{admin}", $administrator_login );  
    
    // Send email
    if ( mail( $to, $email->subject, $email->message, $email->header ) )
    {
      return null;
    }
    else
    {
      return "Échec de l'envoi de l'e-mail de confirmation";
    }
  }
  
?>