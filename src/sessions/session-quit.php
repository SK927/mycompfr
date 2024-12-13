<?php

  require_once 'session-handler.php';

  session_unset();
  session_destroy();

  header( "Location: https://{$_SERVER['SERVER_NAME']}" );    
  exit();  
  
?>