<?php

  error_reporting( E_ERROR );
  ini_set( "display_errors", 1 );
  
  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  
  $competition_id = $_GET['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {
    require_once dirname( __FILE__ ) . '/_functions.php';

    mysqli_open( $mysqli );
    $competition = get_competition_data( $competition_id, $mysqli );
    $catalog = get_catalog( $competition_id, $mysqli ); 
    $mysqli->close();

    if( $catalog )
    { 
      $delimiter = ';'; 
      $filename = "{$competition['name']}_Catalogue_Extract--" . date( 'Y-m-d' ) . '.csv'; 
      $f = fopen( 'php://memory', 'w' );
      
      foreach( $catalog as $block )
      {
        fputcsv( $f, array( utf8_decode( $block['name'] ) ), $delimiter ); // Write block name to buffer 

        foreach( $block['items'] as $item )
        {           
          $row = array( utf8_decode( $item['name'] ), $item['price'], utf8_decode( $item['description'] ), $item['image'] ); // Create order array with base information 
          
          foreach( $item['options'] as $option )
          {
            $selections = '';

            foreach( $option['selections'] as $selection )
            {
              $selections .= utf8_decode( $selection['name'] );
              
              if( $selection['price'] != '0.00' )
              {
                $selections .= "(+{$selection['price']}+)";
              }
              
              $selections .= ';';
            }

            array_push( $row, ...array( utf8_decode( $option['name'] ), rtrim( $selections, ';' ) ) ); // Add all options and their selectable values, if options exist 
          }
          fputcsv( $f, $row, $delimiter ); // Write each created item to buffer 
        }
      } 
      fseek( $f, 0 ); 
      header( 'Content-Type: text/csv' ); 
      header( 'Content-Disposition: attachment; filename="' . $filename . '";' ); 
      ob_end_clean();
      fpassthru( $f ); 
    }  
  }
  else
  {
    echo "ERREUR : Vous n'êtes pas connecté ou vous n'êtes pas organisateur de cette compétition !";
  }

?>
