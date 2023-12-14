<?php

  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php';
  
  session_unset();
  session_destroy();
  
  header( "Location: https://{$_SERVER['SERVER_NAME']}" );    
  exit();  
  
?>