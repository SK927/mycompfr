<?php

  require_once dirname( __DIR__, 2 ) . '/67gen/src/custom-functions.php';

  if ( ! empty( $_POST ) )
  {
    $competition_id = get_competition_id_from_url( $_POST['competition_url'] );
  
    [ $competition_data, $error ] = read_competition_data_from_public_wcif( $competition_id );

    $events = to_pretty_json( $competition_data['events'] );

    if ( ! $error )
    {
      foreach ( $competition_data['persons' ] as $person )
      {
        if ( $person['registration']['status'] == 'accepted' )
        {
          $pattern = "/\"personId\": {$person['registrantId']},/";
          $has_results = preg_match( $pattern, $events ); 

          if ( ! $has_results )
          {
            echo "{$person['name']} ({$pattern})<br/>";
          }
        }
      }
    }
  }
  

?>