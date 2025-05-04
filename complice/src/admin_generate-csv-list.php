<?php

  require_once dirname( __DIR__, 2 ) . '/src/sessions_handler.php';
  
  $competition_id = $_GET['id'];
  
  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) )
  {    
    require_once dirname( __FILE__ ) . '/_functions.php';

    [ $returning_competitors, $new_competitors, $error ] = retrieve_competitors( $competition_id, $_SESSION['user_token'] );

    if ( ! $error )
    { 
      $delimiter = ';'; 
      $filename = "{$competition_id}_Liste_Compétiteurs--" . date( 'Y-m-d' ) . '.csv'; 
      $f = fopen( 'php://memory', 'w' );

      fputcsv( $f, array( 'COMPETITEURS' ), $delimiter ); 
      fputcsv( $f, array( 'Nom', 'ID WCA' ), $delimiter ); 

      foreach ( $returning_competitors as $competitor )
      {           
        fputcsv( $f, array( utf8_decode( $competitor['name'] ), $competitor['wcaId'] ), $delimiter ); 
      } 

      fputcsv( $f, array( '' ), $delimiter ); 
      fputcsv( $f, array( 'NOUVEAUX COMPETITEURS' ), $delimiter ); 
      fputcsv( $f, array( 'Nom', 'E-mail', 'Date de naissance', 'Pays', 'Genre' ), $delimiter ); 

      foreach ( $new_competitors as $competitor )
      {           
        fputcsv( $f, array( utf8_decode( $competitor['name'] ), $competitor['email'], $competitor['birthdate'], $competitor['countryIso2'], $competitor['gender'] ), $delimiter ); 
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
    echo 'Vous ne pouvez pas accéder aux informations privées de cette compétition!';
  }

?>
