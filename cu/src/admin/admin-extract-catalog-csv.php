<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  
  $competition_id = $_GET['id'];
  
  if( $_SESSION['logged_in'] AND ( in_array( $competition_id, $_SESSION['manageable_competitions'] ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
    require_once '../functions/competition-data-functions.php';

    $competition_data = get_competition_data( $competition_id, $conn );
      
    $conn->close();

    if ( $competition_data )
    { 
      $competition_catalog = from_pretty_json( $competition_data['competition_catalog'] );
      
      $delimiter = ';'; 
      $filename = "{$competition_id}_Catalogue_Extract--" . date( 'Y-m-d' ) . '.csv'; 
      
      $f = fopen( 'php://memory', 'w' );
      
      foreach ( $competition_catalog as $block_name => $block_value )
      {
        fputcsv( $f, array( $block_name ), $delimiter ); /* Write block name to buffer */

        foreach ( $block_value as $item_id => $item_value )
        {           
          $row = array( $item_value['item_name'], $item_value['item_price'], $item_value['item_descr'] ); /* Create order array with base information */
          
          if ( $item_value['options'] ) 
          {
            foreach( $item_value['options'] as $option_name => $option_value)
            {
              array_push( $row, ...array( $option_name, $option_value ) ); /* Add all options and their selectable values, if options exist */
            }
          }
          fputcsv( $f, $row, $delimiter ); /* Write each created item to buffer */
        }
      } 
      
      fseek( $f, 0 ); 
       
      header( 'Content-Type: text/csv' ); 
      header( 'Content-Disposition: attachment; filename="' . $filename . '";' ); 
      ob_end_clean();
      
      fpassthru( $f ); 
    }  
    else
    {
      /* Placeholder */
    }
  }
  else
  {
    header( '' );    
    exit();
  }

?>
