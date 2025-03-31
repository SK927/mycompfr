<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php'; // $db is loaded here!

  $competition_id = $_GET['id'];
  
  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {    
    require_once dirname( __DIR__, 2 ) . '/src/mysql_connect.php';
    require_once dirname( __FILE__ ) . '/_functions-catalog.php';
    require_once dirname( __FILE__ ) . '/_functions-orders.php';

    $array = array();
    $b = 0;

    while ( isset( $_POST[ "b{$b}-name" ] ) )
    {
      if ( ! empty( $_POST[ "b{$b}-name" ] ) )
      {
        $i = 0;

        $array[ "b{$b}" ] = array( 
                              'name' => sanitize_value_manual( $_POST[ "b{$b}-name" ] ),
                              'items' => array(),
                            );

        while ( isset( $_POST[ "b{$b}-i{$i}-name" ] ) )
        {
          if ( ! empty( $_POST[ "b{$b}-i{$i}-name" ] ) )
          {
            $o = 0;
            $array[ "b{$b}" ]['items'][ "i{$i}" ] = array(
                                  'name' => sanitize_value_manual( $_POST[ "b{$b}-i{$i}-name" ] ),
                                  'price' => $_POST[ "b{$b}-i{$i}-price" ],
                                  'description' => sanitize_value_manual( $_POST[ "b{$b}-i{$i}-descr" ] ),
                                  'image' => $_POST[ "b{$b}-i{$i}-image" ],
                                );

              while ( isset( $_POST[ "b{$b}-i{$i}-o{$o}-name" ] ) )
              {
                if ( ! empty( $_POST[ "b{$b}-i{$i}-o{$o}-name" ] ) )
                {
                  $s = 0;
                  $select = '';
                  $array[ "b{$b}" ]['items'][ "i{$i}" ]['options'][ "o{$o}" ] = array(
                                                                                  'name' => sanitize_value_manual( $_POST[ "b{$b}-i{$i}-o{$o}-name" ] ),
                                                                                );

                  while ( isset( $_POST[ "b{$b}-i{$i}-o{$o}-s{$s}-name" ] ) )
                  {
                    if ( ! empty( $_POST[ "b{$b}-i{$i}-o{$o}-s{$s}-name" ] ) )
                    {
                      $array[ "b{$b}" ]['items'][ "i{$i}" ]['options'][ "o{$o}" ]['selections'][ "s{$s}" ] = array(
                                                                                                                'name' => $_POST[ "b{$b}-i{$i}-o{$o}-s{$s}-name" ],
                                                                                                                'price' => $_POST[ "b{$b}-i{$i}-o{$o}-s{$s}-price" ],
                                                                                                              );
                    }
                    $s++;
                  }
                }
                $o++;
              }
            }
          $i++;
        }
      }
      $b++;
    }
    
    $json = mysqli_real_escape_string( $conn, to_pretty_json( $array ) ); 
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
                'array' => to_pretty_json( $array ),
              );

  echo json_encode( $response ); 
  
?>

