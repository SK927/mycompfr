<?php 

  require_once '../src/sessions/session-handler.php';
  require_once dirname( __DIR__, 3 ) . '/config/config-rights.php'; 
  require_once dirname( __DIR__, 3 ) . '/config/config-db.php';

  if ( $_SESSION['logged_in'] and in_array( $_SESSION['user_wca_id'], ADMINS_ID ) )
  {
    if ( isset( $_POST['id'] ) )
    {  
      require_once dirname( __DIR__, 3 ) . '/src/functions/generic-functions.php';
      require_once dirname( __DIR__, 3 ) . '/src/mysql/mysql-connect.php';
      require_once dirname( __DIR__, 1 ) . '/custom-functions.php';

      $id = $_POST['id'];

      [ $month, $year ] = retrieve_date_from_id( $id );

      $data = array( 'intro' => array( 'title' => 'Introduction',
                                        'text' => '',
                                        'section' => false,
                                         ),
                      'outro' => array( 'title' => 'Conclusion',
                                        'text' => '',
                                        'section' => false,
                                         ),
              );

      $data = to_pretty_json( $data );

      $sql = "REPLACE INTO " . DB_PREFIX_AFS . " (id, month, year, data, published) VALUES ({$id}, '{$month}', {$year}, '{$data}', 0)";

      $conn->query( $sql );

      $conn->close();
    }
  }


?>