<?php 

  require_once '../src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/config/config-rights.php'; 
  require_once dirname( __DIR__, 3 ) . '/config/config-db.php';

  if ( $_SESSION['logged_in'] and in_array( $_SESSION['user_wca_id'], ADMINS_ID ) )
  {
    if ( isset( $_POST['id'] ) )
    {  
      require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
      require_once dirname( __DIR__, 1 ) . '/custom-functions.php';

      $id = $_POST['id'];

      $sql = "DELETE FROM " . DB_PREFIX_AFS . " WHERE id = '{$id}'";

      $conn->query( $sql );

      $conn->close();
    }
  }


?>