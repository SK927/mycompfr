<?php

  require_once dirname( __DIR__, 1 ) . '/config/config_loader.php';
  $encrypt = load_config_yaml( 'config-encrypt' );


  /**
   * hash_data(): hash the provided data with provided key using MD5
   * @param (string) data: data to be hashed
   * @param (string) key: key used to hash data
   * @return (string) hashed data
   */

  function hash_data( $data, $key )
  {
    return hash_hmac( 'md5', $data, $key );
  }


  /**
   * encrypt_data(): encrypt the provided data 
   * @param (string) data: data to be encrypted
   * @return (string) encrypted data
   */

  function encrypt_data( $data )
  {
    global $encrypt;

    return openssl_encrypt( $data, $encrypt['crypt_method'], $encrypt['crypt_key'], 0, $encrypt['crypt_iv'] );
  }
  

  /**
   * decrypt_data(): decrypt the provided data 
   * @param (string) data: data to be encrypted
   * @return (string) encrypted data
   */

  function decrypt_data( $data )
  {
    global $encrypt;

    return openssl_decrypt( $data, $encrypt['crypt_method'], $encrypt['crypt_key'], 0, $encrypt['crypt_iv'] );
  }
  
?>