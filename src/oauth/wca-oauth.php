<?php

  class WcaOauth
  {
    CONST ACCESS_TOKEN_URI = 'https://www.worldcubeassociation.org/oauth/token';
    CONST OAUTH_AUTHORIZE_URI = 'https://www.worldcubeassociation.org/oauth/authorize';
    CONST USER_URI = 'https://www.worldcubeassociation.org/api/v0/me';

    protected $application_id;
    protected $application_secret;
    protected $redirect_uri;
    protected $scope;

    protected static $required_options = array(
                                          'application_id',
                                          'application_secret',
                                          'redirect_uri'
                                        );

    protected $access_token;

    public function __construct( $options )
    {
      $this->set_options( $options );
    }

    protected function check_required_options_set()
    {
      foreach ( self::$required_options as $value )
      {
        if ( ! isset( $this->$value ) )
        {
          throw new Exception( "{$value} is a required option!" );
        }
      }
    }

    protected function set_options( $options )
    {
      foreach ( $options as $key => $value ) 
      {
        $this->$key = $value;
      }

      $this->check_required_options_set();
    }

    /**
     * Send request to WCA and return JSON.
     * @param (string) url: URL to send request
     * @param (array) post_params: POST data to include in the request (optional)
     * @param (array) headers: Headers to set in the request (optional)
     * @return (array) the JSON decoded array
     */

    protected function curl_json( $url, $post_params = null, $headers = null)
    {
      $ch = curl_init();

      curl_setopt( $ch, CURLOPT_URL, $url );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

      if ( $post_params )
      {
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_params );
      }

      if ( $headers )
      {
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
      }

      $result = curl_exec( $ch );

      curl_close( $ch );

      $json_result = json_decode( $result, false, 512, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

      $this->throw_if_error( $json_result );

      return $json_result;
    }

    protected function throw_if_error( $json_result )
    {
      $error = '';

      if ( isset( $json_result->error ) )
      {
        $error .= "Error: {$json_result->error}";
      }

      if ( isset( $jsonR_result->error_description ) )
      {
        $error .= " ({$json_result->error_description})";
      }

      if ($error)
      {
        throw new WcaOauthException( $error );
      }
    }

    public function generate_oauth_flow_uri()
    {
      $params = http_build_query([
                  'client_id' => $this->application_id,
                  'redirect_uri' => $this->redirect_uri,
                  'response_type' => 'code',
                  'scope' => implode( '+', $this->scope ),
                ]);

      return sprintf( '%s?%s', self::OAUTH_AUTHORIZE_URI, $params );
    }

    /**
     * Convert the code GET param to an OAuth Access Token and store it
     * @param  (string) code: the code returned by WCA API
     * @return (wca) the object
     */
    
    public function fetch_access_token( $code )
    {
      $post_params = array(
        'code' => $code,
        'grant_type' => 'authorization_code',
        'client_id' => $this->application_id,
        'client_secret' => $this->application_secret,
        'redirect_uri' => $this->redirect_uri,
      );

      $json_result = $this->curl_json( self::ACCESS_TOKEN_URI, $post_params );

      $this->access_token = $json_result->access_token;

      return $this;
    }

    public function get_access_token()
    {
      return $this->access_token;
    }

    public function get_user()
    {
      if ( ! $this->access_token )
      {
        throw new WcaOauthException( 'You must call fetch_access_token first.' );
      }

      $headers = array(
                  "Authorization: Bearer {$this->access_token}",
                );

      return $this->curl_json( self::USER_URI, null, $headers )->me;
    }
  }

  class WcaOauthException extends Exception
  {

  }
 
?>