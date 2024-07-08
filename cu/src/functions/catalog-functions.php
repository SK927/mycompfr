<?php

  /**
   * sanitize_value_manual()
   * @param (string) value: the string or value to sanitize
   * @return (string) the sanitized value
   */

  function sanitize_value_manual( $value )
  {
    $pattern = "/[<>={}]/";
    return preg_replace( $pattern, '', $value );
  }


  /**
   * sanitize_value_csv()
   * @param (string) value: the string or value to sanitize
   * @return (string) the sanitized value
   */

  function sanitize_value_csv( $value )
  {
    // Test it and see if it is UTF-8 or not
    $utf8 = \mb_detect_encoding( $value, ['UTF-8'], true );

    if ( $utf8 !== false ) {
      return $value;
    }
    else
    {
      return utf8_encode( $value );
    }
  }



?>