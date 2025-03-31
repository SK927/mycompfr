<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_GET['id'];

  if ( $_FILES AND ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions-catalog.php';
    require_once dirname( __FILE__ ) . '/_functions-orders.php';
    
    $csv_array = array();
    $errors = array();
    $allowed_extensions = array( 'csv' );
    $file_name = $_FILES['file']['name'];
    $file_extension = strtolower( end( explode( '.', $file_name ) ) );
    $file_size = $_FILES['file']['size'];
    $file_temp = $_FILES['file']['tmp_name'];

    if ( in_array( $file_extension, $allowed_extensions ) === false ) // If the provided file is not a CSV 
      $error = 'Extension de fichier non valide !';
    
    if ( $file_size > 10485760 ) // If file size is greater than 10 Mo 
      $error = 'Taille du fichier supérieure à 10 Mo !';

    if ( ! $error ) // If no error occured 
    {
      $handle = fopen( $file_temp, 'r' ); // Open file 

      while ( ! feof( $handle ) ) 
      {
        $filtered_array = array_filter( fgetcsv( $handle, 0, ';' ), function( $element ) {
          return '' !== trim( $element );
        });

        if ( count( $filtered_array ) )
          array_push( $csv_array, $filtered_array ); // Read each line 
      }

      fclose( $handle ); // Close file  
      $catalog = array();
      $b = -1;

      foreach ( $csv_array as $line )
      { 
        if ( count( $line ) == 1 ) // If line corresponds to an item 
        {
          $b++;
          $i = 0;
          $catalog[ "b{$b}" ]['name'] = $line[0]; 
        }
        else
        {
          $item = array();

          if ( is_numeric( $item['price'] = str_replace( ',', '.', $line[1] ) ) )
          { 
            $item['name'] = sanitize_value_csv( $line[0] ); 
            $item['description'] = sanitize_value_csv( $line[2] );
            $item['image'] = $line[3] ? sanitize_value_csv( $line[3] ) : '.';

            if ( $line[4] ) // At least one option exists 
            {
              $x = 4;
              $o = 0;
              $options = array();

              while ( $line[ 4 + $o * 2 ] )
              {
                $options[ "o{$o}" ]['name'] = sanitize_value_csv( $line[ 4 + $o * 2  ] ); 
                $selections = explode( ';', sanitize_value_csv( str_replace( ',', '.', rtrim( $line[ 4 + $o * 2 + 1 ], ';' ) ) ) );
                $s = 0;

                foreach( $selections as $selection)
                {
                  $selection = explode( '(+', $selection );
                  $options[ "o{$o}" ]['selections'][ "s{$s}" ]['name'] = $selection[0];
                  $price = rtrim( $selection[1], '+)');
                  $options[ "o{$o}" ]['selections'][ "s{$s}" ]['price'] = is_numeric ( $price ) ? $price : '0.00';
                  $s++;
                }
                $o++;
              }
              $item['options'] = $options;
            }
            $catalog[ "b{$b}" ]['items'][ "i{$i}" ] = $item; // Generate random integer to identify item 
            $i++;
          }
          else
          {
            $text_to_display = 'Échec de la mise à jour du catalogue...';
            $error = 'Au moins un des prix de produit est mal formaté';
            $catalog = null;
            break;
          }             
        }
      }

      if ( ! $error )
      {
        $json = mysqli_real_escape_string( $conn, to_pretty_json( $catalog ) );
        $sql = "UPDATE {$db['cu']}_Main SET competition_catalog = '{$json}' WHERE competition_id = '{$competition_id}'";

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
              );

  echo json_encode( $response ); 
  
?>

