<?php

  require_once '../config.php';
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND $_FILES AND ( in_array( $competition_id, $_SESSION['manageable_competitions'] ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once '../functions/catalog-functions.php';
    require_once '../functions/orders-functions.php';
    
    $csv_array = array();
    $errors = array();
    $allowed_extensions = array( 'csv' );

    $file_name = $_FILES['file']['name'];
    $file_extension = strtolower( end( explode( '.', $file_name ) ) );
    $file_size = $_FILES['file']['size'];
    $file_temp = $_FILES['file']['tmp_name'];

    if ( in_array( $file_extension, $allowed_extensions ) === false ) /* If the provided file is not a CSV */
    {
      $error = 'Extension de fichier non valide !';
    }
    if ( $file_size > 10485760 ) /* If file size is greater than 10 Mo */
    {
      $error = 'Taille du fichier supérieure à 10 Mo !';
    }

    if ( ! $error ) /* If no error occured */
    {
      $handle = fopen( $file_temp, 'r' ); /* Open file */

      while ( ! feof( $handle ) ) 
      {
        $filtered_array = array_filter( fgetcsv( $handle, 0, ';' ), function( $element ) {
          return '' !== trim( $element );
        });

        if ( count( $filtered_array ) )
        {
          array_push( $csv_array, $filtered_array ); /* Read each line */
        }
      }
    
      fclose( $handle ); /* Close file */ 
      
      $csv_array = array_reverse( $csv_array ); /* Block are detected by name, reverse to force order */
      
      $block_array = array();

      foreach ( $csv_array as $line )
      {
        if ( count( $line ) > 1 ) /* If line corresponds to an item */
        {
          $item = array();
          
          if ( is_numeric( $item['item_price'] = str_replace( ',', '.', $line[1] ) ) )
          { 
            $item['item_name'] = sanitize_value_csv( $line[0] ); 
            $item['item_descr'] = sanitize_value_csv( $line[2] );
            
            if ( $line[3] ) /* At least one option exists */
            {
              $i = 3;
              $options = array();
              while ( $line[$i] )
              {
                $options[ sanitize_value_csv( $line[ $i ] ) ] = sanitize_value_csv( $line[ $i + 1 ] );
                $i = $i + 2;
              }
              $item['options'] = $options;
            }
            
            $block_array[ '--' . rand( 0,1000000000000 ) ] = $item; /* Generate random integer to identify item */
          }
          else
          {
            $text_to_display = 'Échec de la mise à jour du catalogue...';
            $error = 'Au moins un des prix de produit est mal formaté';
            $json_array = null;
            break;
          }
        }
        else
        {
          $json_array[ sanitize_value_csv( $line[0] ) ] = array_reverse( $block_array ); /* Store item in block array with original order */
          $block_array = array();
        }
      }

      if ( ! $error )
      {
        $json_array = array_reverse( $json_array ); /* Reverse the object array to retrieve original order*/
        $json = mysqli_real_escape_string( $conn, to_pretty_json( $json_array ) );

        $sql = "UPDATE " . DB_PREFIX . "_Main SET competition_catalog = '{$json}' WHERE competition_id = '{$competition_id}';";      

        if ( $conn->query( $sql ) )
        {      
          $text_to_display = 'Catalogue mis à jour avec succès !';    
        }    
        else    
        {      
          $text_to_display = 'Échec de la mise à jour du catalogue...';
          $error = mysqli_error( $conn ); 
        }   
      }
    }
    else
    {
      $displayText = 'Échec du chargement du fichier...';
    }
    
    $conn->close();
  }
  else
  {
    $text_to_display = 'Accès interdit !';
    $error = 'Not authenticated';
  }
  $response = array(
                'text_to_display' => $text_to_display, 
                'error' => $error, 
                'array' => json_encode( $json_array ),
              );

  echo json_encode( $response ); 
  
?>

