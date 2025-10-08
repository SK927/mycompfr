<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  require_once dirname( __DIR__, 2 ) . '/src/_functions-generic.php';
  require_once dirname( __DIR__, 2 ) . '/config/config_loader.php';
  require_once '_functions.php';

  $competition_id = get_competition_id( $_POST );

	if ( $competition_id )
	{	
    $ots_accepted = $_POST['accept_ots'] == 'on' ? true : false;
		$ots_contact = trim( $_POST['ots_contact'] );

    unset( $_POST['competition_select'] );
    unset( $_POST['competition_id'] );
    unset( $_POST['accept_ots'] );
		unset( $_POST['ots_contact'] );

   	$files = array();

    foreach ( $_POST as $key => $value )
    {  
    	if ( $value == 'on') // If file is selected
    	{
        $key = str_replace( '_yaml', '.yaml', $key ); // Format file name properly 
    		$files = array_merge( $files, glob( "../{$key}" ) ); // Merge filename into files array
    	}
    }

    $header_faq = '';
    $header_reg = '';
    $result_faq = '';
    $result_reg = '';

    foreach ( $files as $file )
    {
      $content = str_replace( '\n', ' ', to_pretty_json( array_shift( spyc_load_file( $file ) ) ) );
      $content = str_replace( 'ID_COMP', $competition_id, $content );

      if ( $ots_contact != '' )
      {
        $content = str_replace( '**TODO compléter avec nom référent**', $ots_contact, $content );
      }

      if ( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) )
      {
        $reg_close_time = "{$_SESSION['manageable_competitions'][ $competition_id ]['reg_close']} CET";
        $content = str_replace( '**TODO compléter avec date & heure**', $reg_close_time, $content );
      }

      $content = from_pretty_json( $content );
      
      if ( $content['faq'] )
      {      
        if ( $content['prepend'] != '' )
        {
          $header_faq .= "*{$content['prepend']}*\n";
        }

        if ( $result_faq )
        {
          $result_faq .= "\n---\n\n";
        }

        $result_faq .= "###[{$content['locale']}]\n\n";

        foreach ( $content['faq']['data'] as $question )
        {
          $result_faq .= "**{$content['faq']['alias']['question']} {$question['q']}**\n";
          $result_faq .= "**{$content['faq']['alias']['answer']}** ";
          $result_faq = add_paragraphs( $result_faq, $question['a'] );
          $result_faq .= "\n";
        }
      }

      if ( $content['reg'] )
      {      
        if ( $content['prepend'] != '' )
        {
          $header_reg .= "*{$content['prepend']}*\n";
        }

        if ( $result_reg )
        {
          $result_reg .= "\n---\n\n";
        }

        $result_reg .= "###[{$content['locale']}]\n\n";
        $result_reg .= "####{$content['reg']['title']}\n\n";
        $result_reg = add_paragraphs( $result_reg, $content['reg']['text'] );
        $result_reg .= "\n####{$content['wl']['title']}\n\n";
        $result_reg = add_paragraphs( $result_reg, $content['wl']['text'] );
        $result_reg .= "\n####{$content['ots']['title']}\n\n";

        if ( $ots_accepted )
        {      
          $result_reg = add_paragraphs( $result_reg, $content['ots']['text']['accepted'] );
        }
        else
        {
          $result_reg = add_paragraphs( $result_reg, $content['ots']['text']['not_accepted'] );
        }

        $result_reg .= "\n####{$content['misc']['title']}\n\n";
        $result_reg = add_paragraphs( $result_reg, $content['misc']['text'] );
      }
    }
	}
	else
	{
		$header_faq = 'ERREUR : ID non défini !';
	}
	
	$result = array( 
				'resulting_string_faq' => $header_faq . "\n" . $result_faq,
				'resulting_string_reg' => $header_reg . "\n" . $result_reg,
			);

	echo json_encode( $result );

?>