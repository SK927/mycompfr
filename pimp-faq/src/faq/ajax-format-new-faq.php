<?php

	if ( isset ( $_POST['competition_url'] ) )
	{	
	    $pattern = "/competitions\/(.*?)(?=\/|\#|$)/"; 
	    preg_match( $pattern, $_POST['competition_url'], $matches ); 
	
		$string = file_get_contents( '../../assets/faq-template.txt' );
		$result = str_replace( 'ID_COMP', $matches[1], $string ); /* matches[0] is the full match of the pattern, [1] is the group we look for */
	}
	
	echo json_encode( array( 'resulting_string' => $result ) );

?>