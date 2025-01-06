<?php

  require_once dirname( __DIR__, 2 ) . '/noshow/src/custom-functions.php';

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

    foreach ( $_POST as $key =>$value )
    {  
    	if ( $value == 'on') /* If file is selected */
    	{
        $key = str_replace( '_json', '.json', $key ); /* Format file name properly */ 
    		$files = array_merge( $files, glob( "../{$key}" ) ); /* Merge filename into files array */
    	}
    }

    $header = '';
    $result_faq = '';
    $result_wl = '';

    foreach ( $files as $file )
    {
      $content = from_pretty_json( str_replace( 'ID_COMP', $competition_id, file_get_contents( $file ) ) );

      if ( $content['PRE'] != '' )
      {
        $header .= "*{$content['PRE']}*\r\n";
      }

      if ( $result_faq != '' )
      {
        $result_faq .= "\r\n---\r\n\r\n";
        $result_wl .= "\r\n\r\n---\r\n\r\n";
      }

      $result_faq .= "###[{$content['LOCALE']}]\r\n\r\n";

      foreach ( $content['FAQ']['DATA'] as $text )
      {
        $result_faq .= "**{$content['FAQ']['ALIAS']['QUESTION']}- {$text['Q']}**\r\n";
        $result_faq .= "**{$content['FAQ']['ALIAS']['ANSWER']}-** {$text['A']}\r\n\r\n";
      }

    	$result_wl .= "###[{$content['LOCALE']}]\r\n\r\n{$content['WAITING LIST']}\r\n\r\n";

      if ( $ots_accepted )
      {      
        $result_wl .= str_replace( 'CONTACT_NAME', $ots_contact, $content['OTS']['ACCEPTED'] );
      }
      else
      {
        $result_wl .= $content['OTS']['NOT ACCEPTED'];
      }
    }
	}
	else
	{
		$header = "ERREUR : ID non défini !";

	}
	
	$result = array( 
				'resulting_string_faq' => $header . "\r\n" . $result_faq,
				'resulting_string_wl' => $header . "\r\n" . $result_wl,
			);

	echo json_encode( $result );

?>
