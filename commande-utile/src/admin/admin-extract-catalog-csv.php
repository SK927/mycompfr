<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  
  $competition_id = $_GET['id'];
  
  if( $_SESSION['logged_in'] AND ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
    require_once dirname( __DIR__, 3 ) . '/src/functions/encrypt-functions.php';
    require_once '../functions/competition-data-functions.php';

    $competition_data = get_competition_data( $competition_id, $conn );
      
    $conn->close();

    if ( $competition_data )
    { 
      $catalog = from_pretty_json( $competition_data['competition_catalog'] );
      
      $delimiter = ';'; 
      $filename = "{$competition_id}_Catalogue_Extract--" . date( 'Y-m-d' ) . '.csv'; 
      
      $f = fopen( 'php://memory', 'w' );
      
      foreach ( $catalog as $block )
      {
        fputcsv( $f, array( $block['name'] ), $delimiter ); /* Write block name to buffer */

        foreach ( $block['items'] as $item )
        {           
          $row = array( $item['name'], $item['price'], $item['description'], $item['image'] ); /* Create order array with base information */
          
          foreach( $item['options'] as $option )
          {
            $selections = '';

            foreach( $option['selections'] as $selection )
            {
              $selections .= "{$selection['name']}";
              if ( $selection['price'] != '0.00' ) $selections .= "(+{$selection['price']}+)";
              $selections .= ';';
            }

            array_push( $row, ...array( $option['name'], rtrim( $selections, ';' ) ) ); /* Add all options and their selectable values, if options exist */
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
