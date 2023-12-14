<?php

  const CRYPT_METHOD = '';
  const CRYPT_KEY = '';
  const CRYPT_IV = '';

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
    return openssl_encrypt( $data, CRYPT_METHOD, CRYPT_KEY, 0, CRYPT_IV );
  }
  

  /**
   * decrypt_data(): decrypt the provided data 
   * @param (string) data: data to be encrypted
   * @return (string) encrypted data
   */

  function decrypt_data( $data )
  {
    return openssl_decrypt( $data, CRYPT_METHOD, CRYPT_KEY, 0, CRYPT_IV );
  }
  
?>
  