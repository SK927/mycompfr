<?php

  require_once '../config.php';
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';

  $competition_id = $_GET['id'];
  
  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, $_SESSION['manageable_competitions'] ) OR $_SESSION['is_admin'] ) )
  {    
    require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
    require_once '../functions/catalog-functions.php';
    require_once '../functions/orders-functions.php';
        
    /* If item list is updated manually, through the update button */
    $array = array_reverse( $_POST ); /* Item ends are detected with item-id, reverse to force order */
        
    $json_array = array();
    $block_array = array();
    $item_array = array();
    $option_array = array();
    
    foreach ( $array as $key => $value )
    {
      $value = sanitize_value_manual( $value );

      if ( ! preg_match( '/block_name/', $key ) ) /* If parameter doesn't belong to a block */
      { 
        [ $element_name, $element_id ] = explode( '--', htmlspecialchars( $key ) ); /* Remove random int at the end of each parameters */
        
        if ( $element_name == 'item_id' ) /* If end of the item data is reached */ 
        {
          $item_array['options'] = array_reverse( $option_array );
          $option_array = array();
          $block_array[ htmlspecialchars( $value ) ] = array_reverse( $item_array ); /* Store item in block array with original order */
          $item_array = array();
        }
        else /* While parameter belongs to current item */
        {
          if ( $element_name == 'option_value' ) $opt = htmlspecialchars( $value );
          elseif ( $element_name == 'option_name' ) $option_array[ $value ] = $opt;
          else $item_array[ $element_name ] = htmlspecialchars( $value ); /* Store parameter value in item array */ 
        }
      }
      else /* If parameter is the current block name */
      {
        $json_array[ htmlspecialchars( strtoupper( $value ) ) ] = array_reverse( $block_array ); /* Store block in objects array */
        $block_array = array();
      }
    }
    
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

