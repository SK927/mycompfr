<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';

  $competition_id = $_GET['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {    
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );

    $sql = "DELETE FROM {$db['cu']}_Catalogs WHERE competition_id = '{$competition_id}'";

    if( $mysqli->query( $sql ) )
    {      
      $csv_array = array();
      $errors = array();
      $allowed_extensions = array( 'csv' );
      $file_name = $_FILES['file']['name'];
      $file_extension = strtolower( end( explode( '.', $file_name ) ) );
      $file_size = $_FILES['file']['size'];
      $file_temp = $_FILES['file']['tmp_name'];

      if( in_array( $file_extension, $allowed_extensions ) === false ) // If the provided file is not a CSV 
        $error = 'Extension de fichier non valide !';
      
      if( $file_size > 10485760 ) // If file size is greater than 10 Mo 
        $error = 'Taille du fichier supérieure à 10 Mo !';

      if( ! $error ) // If no error occured 
      {
        $handle = fopen( $file_temp, 'r' ); // Open file 

        while( ! feof( $handle ) ) 
        { 
          if( is_array( $row = fgetcsv( $handle, 0, ';', "\"", "\\" ) ) )
          {
            $filtered_array = array_filter( $row, function( $element ) {
              return '' !== trim( $element );
            });

            if( count( $filtered_array ) )
            {
              array_push( $csv_array, $filtered_array ); // Read each line 
            }
          }
        }

        fclose( $handle ); // Close file  
        $b = -1;

        foreach( $csv_array as $line )
        { 
          if( ! $error )
          {
            if( count( $line ) == 1 ) // If line corresponds to an item 
            {
              $b++;
              $i = 0;
              $block = array(
                      'alias' => "b{$b}",
                      'name' => sanitize_value_manual( $line[0] ),
                    );

              $sql = "REPLACE INTO {$db['cu']}_Catalogs (competition_id, type, alias, name) VALUES ('{$competition_id}', 'Block', '{$block['alias']}', '{$block['name']}')";

              $mysqli->query( $sql );
            }
            else
            {
              if( is_numeric( $price = str_replace( ',', '.', $line[1] ) ) )
              { 
                $item = array(
                          'alias' => "b{$b}-i{$i}",
                          'name' => sanitize_value_csv( $line[0] ),
                          'price' => $price,
                          'description' => sanitize_value_csv( $line[2] ),
                          'image' => empty( $line[3] ) ? array_diff( scandir( '../assets/img/icons' ), array( '.', '..' ) )[2] : sanitize_value_csv( $line[3] ),
                        );

                $sql = "INSERT INTO {$db['cu']}_Catalogs (competition_id, type, alias, name, price, description, image) VALUES ('{$competition_id}', 'Item', '{$item['alias']}', '{$item['name']}', '{$item['price']}', '{$item['description']}', '{$item['image']}')";
                
                if( $mysqli->query( $sql ) )
                {
                  if( $line[4] ) // At least one option exists 
                  {
                    $x = 4;
                    $o = 0;
                    $options = array();

                    while( $line[ 4 + $o * 2 ] )
                    {
                      $option = array(
                                  'alias' => "b{$b}-i{$i}-o{$o}",
                                  'name' => sanitize_value_csv( $line[ 4 + $o * 2  ] ),
                                );

                      $sql = "INSERT INTO {$db['cu']}_Catalogs (competition_id, type, alias, name) VALUES ('{$competition_id}', 'Option', '{$option['alias']}', '{$option['name']}')";

                      if( $mysqli->query( $sql ) )
                      {
                        $selections = explode( ';', sanitize_value_csv( str_replace( ',', '.', rtrim( $line[ 4 + $o * 2 + 1 ], ';' ) ) ) );
                        $s = 0;

                        foreach( $selections as $selection)
                        {
                          $selection = explode( '(+', $selection );
                          $price = rtrim( $selection[1], '+)');

                          $selection = array(
                                            'alias' => "b{$b}-i{$i}-o{$o}-s{$s}",
                                            'name' => sanitize_value_csv( $selection[0] ),
                                            'price' => is_numeric ( $price ) ? $price : '0.00',
                                          );

                          $sql = "INSERT INTO {$db['cu']}_Catalogs (competition_id, type, alias, name, price) VALUES ('{$competition_id}', 'Selection', '{$selection['alias']}', '{$selection['name']}', '{$selection['price']}')";

                          $mysqli->query( $sql );

                          $s++;
                        }
                        $o++;
                      }
                    }
                  }
                  $i++;
                }
                $error = $mysqli->error;
              }
              else
              {
                $text_to_display = 'Échec de la mise à jour du catalogue...';
                $error = 'Au moins un des prix de produit est mal formaté';
                break;
              }             
            }
          }  
        }
      }
      $text_to_display = ! $error ? "Catalogue mis à jour avec succès" : "Échec de la mise à jour du catalogue...";
    }
    $mysqli->close();
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

