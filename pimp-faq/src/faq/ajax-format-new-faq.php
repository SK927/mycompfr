<?php

	if ( isset ( $_POST['competition_url'] ) )
	{	
	    $pattern = "/competitions\/(.*?)(?=\/|\#|$)/"; 
	    preg_match( $pattern, $_POST['competition_url'], $matches ); 
		unset( $_POST['competition_url'] );

	    $files = glob( '../../assets/basic_*.txt' );

	    foreach ( $_POST as $key =>$value )
	    {

	    	if ( $value == 'on')
	    	{
	    		$files = array_merge( $files, glob( "../../assets/{$key}*.txt" ) );
	    	}
	    }

	    $string = file_get_contents( array_shift( $files ) ) ;
	    $header = '';

	    foreach ( $files as $file )
	    {
	    	$read_string = explode( '|||', file_get_contents( $file ) );
	    	$header .= "*{$read_string[0]}*\r\n";
	    	$string .= $read_string[1];
	    }

		$result = str_replace( 'ID_COMP', $matches[1], $header . $string ); /* matches[0] is the full match of the pattern, [1] is the group we look for */

	}
	
	echo json_encode( array( 'resulting_string' => $result ) );

?>