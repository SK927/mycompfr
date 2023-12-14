<?php

  const JSON_ATTR = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;


  /**
   * to_pretty_json(): format data to JSON format
   * @param (multi) data: data to be formatted
   * @return (json string) formatted data as json string
   */

  function to_pretty_json( $data )
  {
    return json_encode( $data, JSON_ATTR );
  }  


  /**
   * from_pretty_json(): format JSON data to associative array
   * @param (json string) data: data to be formatted
   * @return (multi) formatted data as json string
   */

  function from_pretty_json( $data )
  {
    return json_decode( $data, true, 512, JSON_ATTR );
  }