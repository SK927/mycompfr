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
      $b = 0;

      while( isset( $_POST[ "b{$b}-name" ] ) )
      {
        if( ! empty( $_POST[ "b{$b}-name" ] ) )
        {
          $i = 0;

          $block = array(
                      'alias' => "b{$b}",
                      'name' => sanitize_value_manual( $_POST[ "b{$b}-name" ] ),
                    );

          $sql = "REPLACE INTO {$db['cu']}_Catalogs (competition_id, type, alias, name) VALUES ('{$competition_id}', 'Block', '{$block['alias']}', '{$block['name']}')";

          if( $mysqli->query( $sql ) )
          {
            while( isset( $_POST[ "b{$b}-i{$i}-name" ] ) )
            {
              if( ! empty( $_POST[ "b{$b}-i{$i}-name" ] ) )
              {
                $o = 0;
                $item = array(
                          'alias' => "b{$b}-i{$i}",
                          'name' => sanitize_value_manual( $_POST[ "b{$b}-i{$i}-name" ] ),
                          'price' => $_POST[ "b{$b}-i{$i}-price" ],
                          'description' => sanitize_value_manual( $_POST[ "b{$b}-i{$i}-descr" ] ),
                          'image' => empty( $_POST[ "b{$b}-i{$i}-image" ] ) ? array_diff( scandir( '../assets/img/icons' ), array( '.', '..' ) )[2] : $_POST[ "b{$b}-i{$i}-image" ],
                        );

                $sql = "INSERT INTO {$db['cu']}_Catalogs (competition_id, type, alias, name, price, description, image) VALUES ('{$competition_id}', 'Item', '{$item['alias']}', '{$item['name']}', '{$item['price']}', '{$item['description']}', '{$item['image']}')";

                if( $mysqli->query( $sql ) )
                {
                  while( isset( $_POST[ "b{$b}-i{$i}-o{$o}-name" ] ) )
                  {
                    if( ! empty( $_POST[ "b{$b}-i{$i}-o{$o}-name" ] ) )
                    {
                      $s = 0;
                      
                      $option = array(
                                  'alias' => "b{$b}-i{$i}-o{$o}",
                                  'name' => sanitize_value_manual( $_POST[ "b{$b}-i{$i}-o{$o}-name" ] ),
                                );

                      $sql = "INSERT INTO {$db['cu']}_Catalogs (competition_id, type, alias, name) VALUES ('{$competition_id}', 'Option', '{$option['alias']}', '{$option['name']}')";

                      if( $mysqli->query( $sql ) )
                      {
                        while( isset( $_POST[ "b{$b}-i{$i}-o{$o}-s{$s}-name" ] ) )
                        {
                          if( ! empty( $_POST[ "b{$b}-i{$i}-o{$o}-s{$s}-name" ] ) )
                          {
                            $selection = array(
                                            'alias' => "b{$b}-i{$i}-o{$o}-s{$s}",
                                            'name' => $_POST[ "b{$b}-i{$i}-o{$o}-s{$s}-name" ],
                                            'price' => $_POST[ "b{$b}-i{$i}-o{$o}-s{$s}-price" ],
                                          );

                            $sql = "INSERT INTO {$db['cu']}_Catalogs (competition_id, type, alias, name, price) VALUES ('{$competition_id}', 'Selection', '{$selection['alias']}', '{$selection['name']}', '{$selection['price']}')";

                            $mysqli->query( $sql );
                          }
                          $s++;
                        }
                      }
                    }
                    $o++;
                  }
                }
              }
              $i++;
            }
          }
        }
        $b++;
      }
    }
    
    $error = mysqli_error( $mysqli );
    $text_to_display = ! $error ? "Catalogue mis à jour avec succès" : "Échec de la mise à jour du catalogue...";

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

