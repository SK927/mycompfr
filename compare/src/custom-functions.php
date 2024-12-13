<?php

  require_once dirname( __DIR__, 2 ) . '/src/log/log-handler.php';
  require_once dirname( __DIR__, 2 ) . '/src/functions/wcif-functions.php';


  /**
   * get_competition_id()
   * @param (multi) data: POST data to be analyzed
   * @param (string) value: numbered value of the competition to retrieve
   * @return (string) the compared list of competitors
   */
  function get_competition_id( $data, $value )
  {
    return ( ! in_array( $_POST["competition_select_{$value}"], array( '', 'Other' ) ) ) ? trim( $_POST["competition_select_{$value}"] ) : trim( $_POST["competition_id_{$value}"] );
  }
  

  /**
   * get_compared_list()
   * @param (string) competition1_id: ID of the first competition to get the competitors' list for
   * @param (string) competition2_id: ID of the second competition to get the competitors' list for
   * @return (string) the compared list of competitors
   */

	function get_compared_list( $competition1_id, $competition2_id )
  {
    [ $competition1_data, $error1 ] = read_competition_data_from_public_wcif( $competition1_id );
    [ $competition2_data, $error2 ] = read_competition_data_from_public_wcif( $competition2_id );

    $competitions_name = [ $competition1_data['name'], $competition2_data['name'] ];

    if ( ! $error1 and ! $error2 )
    {   
      if ( $competition1_data['schedule']['startDate'] > $competition2_data['schedule']['startDate'] ) /* Reorder competitions based on start date*/
      {
        $competitions_name = [ $competitions_name[1], $competitions_name[0] ];
      }

      $competition2_competitors = array_column( $competition2_data['persons'], 'name'); /* Get all competitors name from competition 2 */
      $comparison_list = array();

      foreach ( $competition1_data['persons'] as $person ) /* Get competitors name from competition 1 and save only competitors who appear in competition 2 array */
      {
        if ( in_array( $person['name'], $competition2_competitors ) )
        {
          $person['wcaId'] = $person['wcaId'] ? $person['wcaId'] : "<b>newcomer</b>";
          $comparison_list[ $person['name'] ] = array(
                                                  'wca_id' =>$person['wcaId'], 
                                                  'registrant_id' => $person['registrantId'],
                                                );
        }
      }
    }
    else
    {
      log_message( $error1 ? $error1 : $error2 );
    }

    return array( $comparison_list, $competition1_data, $competition2_data );
  }

?>