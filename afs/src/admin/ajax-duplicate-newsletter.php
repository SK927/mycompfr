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
      $from = $_POST['from'];

      $sql = "SELECT data FROM MYCOMP_AFS_Newsletter WHERE id = {$from}";

      $query_results = $conn->query( $sql );

      $row = $query_results->fetch_assoc();

      [ $month, $year ] = retrieve_date_from_id( $id );

      $sql = "REPLACE INTO " . DB_PREFIX_AFS . " (id, month, year, data, published) VALUES ({$id}, '{$month}', {$year}, '{$row['data']}', 0)";

      $conn->query( $sql );

      $conn->close();
    }
  }

?>