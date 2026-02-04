<?php

  require_once dirname( __FILE__ ) . '/_functions-generic.php';
  require_once dirname( __FILE__ ) . '/_functions-encrypt.php';


  /**
   * get_wca_data_via_api(): retrieve data stored on wca website via the provided API
   * @param (string) target_url: the API ressource we are trying to retrieve
   * @param (string) user_token: the API token provided upon login, used to retrieve private data (optional)
   * @return (array) the data retrieved via WCA API and the error generated ifneeded
   */

  function get_wca_data_via_api( $target_url, $user_token = null )
  {
    $curl = curl_init();
    curl_setopt( $curl, CURLOPT_URL, $target_url );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
     
    if( $user_token )
    { 
      $headers = array(
                  'Authorization: Bearer ' . decrypt_data( $user_token ),
                );

      curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
    }

    $curl_response = curl_exec( $curl );
    $curl_error = curl_error( $curl ) ;
    curl_close( $curl );

    if( ! $curl_error )
    {
      $response = from_pretty_json( $curl_response );
      $error = isset( $response['error'] ) ? $response['error'] : null;
    }
    else
    {
      $error = $curl_error;
    }

    return array( $response, $error ) ;
  }
  

  /**
   * read_competition_data_from_public_wcif(): retrieve competition data by reading the publicly available WCIF
   * @param (string) competition_id: the ID of the competition to retrieve data for
   * @return (array) the competition public data as an associative array and the error generated ifneeded
   */

  function read_competition_data_from_public_wcif( $competition_id )
  {
    return get_wca_data_via_api( "https://www.worldcubeassociation.org/api/v0/competitions/{$competition_id}/wcif/public" ); 
  }


  /**
   * read_competition_data_from_private_wcif(): retrieve competition data by reading the publicly available WCIF
   * @param (string) competition_id: the ID of the competition we want to retrieve data for
   * @return (array) the competition private data as an associative array and the error generated ifneeded
   */

  function read_competition_data_from_private_wcif( $competition_id, $token )
  {
    return get_wca_data_via_api( "https://www.worldcubeassociation.org/api/v0/competitions/{$competition_id}/wcif", $token ); 
  }


  /**
    * get_competitors_from_public_wcif(): retrieve competitors data from publicly available WCIF
    * @param (string) competition_id: the ID of the competition we want to retrieve data for
    * @return (array) the competitors list as an associative array and the generated error ifneeded
    */ 
  
  function get_competitors_from_public_wcif( $competition_id )
  { 
    [ $response, $error ] = read_competition_data_from_public_wcif( $competition_id );
    
    return array( $response['persons'], $error );
  }


  /**
   * get_competitors_from_private_wcif(): retrieve all competitors informations from private competition WCIF
   * @param (string) competition_id: the ID of the competition we want to retrieve data for
   * @param (string) user_token: the API token provided upon login, used to retrieve private data
   * @return (array) the competitors list as an associative array and the generated error ifneeded
   */ 

  function get_competitors_from_private_wcif( $competition_id, $user_token )
  { 
    [ $response, $error ] = read_competition_data_from_private_wcif( $competition_id, $user_token );
    
    return array( $response['persons'], $error );
  }
        
  
  /**
    * get_competitions_managed_by_user(): retrieve competitions managed by selected user
    * @param (string) user_token: the API token provided upon login, used to retrieve private data
    * @return (array) the list of competitions managed by the user
    */ 

  function get_competitions_managed_by_user( $user_token )
  {
    $manageable_competitions = array();

    $start_date = date( 'Y-m-d', strtotime( date( 'Y-m-d' ) . '-28 day' ) ) . "T00:00:00.000Z"; // Look for competitions from last month onwards

    [ $competitions_managed_by_user, $error ] = get_wca_data_via_api( "https://www.worldcubeassociation.org/api/v0/competitions?managed_by_me=1&start={$start_date}", $user_token );

    if( ! $error )
    {
      foreach( $competitions_managed_by_user as $competition )
      {
        $manageable_competitions[ $competition['id'] ] = array(
                                                            'name' => $competition['name'],
                                                            'start' => date( 'Y-m-d', strtotime( $competition['start_date'] ) ),
                                                            'end' => date( 'Y-m-d' , strtotime( $competition['end_date'] ) ),
                                                            'reg_close' => date( 'Y-m-d, H:i' , strtotime( $competition['registration_close'] ) ),
                                                            'announced' => ! is_null( $competition['announced_at'] ),
                                                          ); 
      }
    }
    return array( array_reverse( $manageable_competitions ), $error );
  }
      
?>