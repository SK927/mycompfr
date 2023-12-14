<?php
  
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';

  $competition_id = decrypt_data( $_GET['id'] );

  if ( $_SESSION['logged_in'] AND in_array( $competition_id, $_SESSION['manageable_competitions'] ) )
  {
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once '../custom-functions.php';

    $registrations = get_competition_registrations_from_db( $competition_id, $conn ); 

    if ( $registrations )
    { 
      $delimiter = ";"; 
      $file_name = $competition_id . '_Registration_Extract--' . date('Y-m-d') . '.csv'; 
      
      $f = fopen( 'php://memory', 'w' );
    
      fputcsv( $f, array('Name', 'Email', 'Confirmed?'), $delimiter); /* Insert header in buffer */
        
      foreach ( $registrations as $registration )
      {
        $registration['email'] = decrypt_data( $registration['email'] );
        fputcsv( $f, $registration, $delimiter ); /* Write each registration to buffer */
      }
      
      fseek( $f, 0 ); 
       
      header( 'Content-Type: text/csv' ); 
      header( "Content-Disposition: attachment; filename=\"{$file_name}\";" ); 
      
      fpassthru( $f ); /* Generate CSV */

    }  
    else
    {
      /* Placeholder */
    }
  }
  else
  {
    header( "Location: https:{$_SERVER['SERVER_NAME']}" );    
    exit();
  }

?>
