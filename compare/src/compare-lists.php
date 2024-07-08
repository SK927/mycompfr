<?php

  require_once dirname( __DIR__, 2 ) . '/67gen/src/custom-functions.php';

  if ( ! empty( $_POST ) )
  {
    $competition1_id = get_competition_id_from_url( $_POST['competition_url1'] );
    $competition2_id = get_competition_id_from_url( $_POST['competition_url2'] );
  
    [ $competition1_data, $error1 ] = read_competition_data_from_public_wcif( $competition1_id );
    [ $competition2_data, $error2 ] = read_competition_data_from_public_wcif( $competition2_id );

    if ( ! $error1 and ! $error2 )
    {   
      $competition1_list = array();

      foreach ( $competition1_data['persons' ] as $person )
      {
        array_push( $competition1_list, $person['name'] );
      }

      foreach ( $competition2_data['persons' ] as $person )
      {
        if ( in_array( $person['name'], $competition1_list ) )
        {
          echo "{$person['name']}<br/>";
        }
      }
    }
  }
  

?>