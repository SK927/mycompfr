<?php

  /**
   * sanitize_value_manual()
   * @param (string) value: the string or value to sanitize
   * @return (string) the sanitized value
   */

  function sanitize_value_manual( $value )
  {
    $pattern = "/[<>'={}]/";
    return preg_replace( $pattern, '', $value );
  }


  /**
   * sanitize_value_csv()
   * @param (string) value: the string or value to sanitize
   * @return (string) the sanitized value
   */

  function sanitize_value_csv($value)
  {
    return utf8_encode( sanitize_value_manual( $value ) );
  }

?>