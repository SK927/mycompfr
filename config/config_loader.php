<?php

  require_once dirname( __DIR__, 1 ) . '/src/yaml_spyc-reader.php';

  function load_config_yaml( $filename )
  {
    return array_shift( spyc_load_file( dirname( __DIR__, 1 ) . "/config/{$filename}.yaml" ) );
  }

?>
