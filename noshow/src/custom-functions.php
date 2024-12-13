<?php

  require_once dirname( __DIR__, 2 ) . '/src/functions/wcif-functions.php';


  /**
   * get_competition_id()
   * @param (multi) data: POST data to be analyzed
   * @param (string) value: numbered value of the competition to retrieve
   * @return (string) the compared list of competitors
   */
  function get_competition_id( $data )
  {
    return ( ! in_array( $_POST['competition_select'], array( '', 'Other' ) ) ) ? trim( $_POST['competition_select'] ) : trim( $_POST['competition_id'] );
  }
  

  /**
   * get_noshow_list()
   * @param (string) competition_id: ID of the competition to get the noshows' list for
   * @return (string) the compared list of competitors
   */

	function get_noshow_list( $competition_id )
  {
    [ $competition_data, $error ] = read_competition_data_from_public_wcif( $competition_id );

    $events = to_pretty_json( $competition_data['events'] );

    if ( ! $error )
    {
      foreach ( $competition_data['persons' ] as $person )
      {
        if ( $person['registration']['status'] == 'accepted' )
        {
          $pattern = "/\"personId\": {$person['registrantId']},/"; /* Retrieve every result for current registrant id */
          $has_results = preg_match( $pattern, $events ); 

          if ( ! $has_results ) /* If results list is empty, display competitor's name */
          {
            $person['wcaId'] = $person['wcaId'] ? $person['wcaId'] : "<b>newcomer</b>";
            $noshow_list[ $person['name'] ] = array(
                                                  'wca_id' =>$person['wcaId'], 
                                                  'registrant_id' => $person['registrantId'],
                                                );
          }
        }
      }
    }
    return array( $noshow_list, $competition_data );
  }

?>