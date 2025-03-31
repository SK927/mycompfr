<?php 
  
  require_once dirname( __DIR__, 1 ) . '/config/config_loader.php';
  $db = load_config_yaml( 'config-db' );

  class MySQLSessionHandler
  {
    private $connection;

    public function __construct()
    {
      $mysql = load_config_yaml( 'config-mysql' );
      
      $this->connection = new mysqli( $mysql['host'], $mysql['username'], $mysql['password'], $mysql['db_name'] );
      
      session_set_save_handler( array( $this, 'open' ), array( $this, 'close' ), array( $this, 'read' ), array( $this, 'write' ), array( $this, 'destroy' ), array( $this, 'gc' ) );
      register_shutdown_function( 'session_write_close' );
      session_start();
    }

    public function open( $save_path, $session_name )
    {
      return true;
    }

    public function close()
    {
      return true;
    }

    public function read($session_id)
    {
      global $db;
      
      $stmt = $this->connection->prepare( "SELECT session_data FROM {$db['sessions']}_Sessions WHERE session_id = '{$session_id}';" );
      $stmt->execute();
      $stmt->bind_result( $session_data );
      $stmt->fetch();

      return $session_data ? $session_data : '';
    }
    
    public function write( $session_id, $data )
    {
      global $db;
      
      $time = time();
      $data = mysqli_real_escape_string( $this->connection, $data );
      $stmt = $this->connection->prepare( "REPLACE INTO {$db['sessions']}_Sessions (session_id, created, session_data) VALUES ('{$session_id}', {$time}, '{$data}')" );

      return $stmt->execute();
    }

    public function destroy( $session_id )
    {
      global $db;
      
      $stmt = $this->connection->prepare( "DELETE FROM {$db['sessions']}_Sessions WHERE session_id = '{$session_id}'" );

      return $stmt->execute();
    }
    
    public function gc( $maxlifetime )
    {
      global $db;
      
      $past = time() - $maxlifetime;
      $stmt = $this->connection->prepare( "DELETE FROM {$db['sessions']}_Sessions WHERE created < {$past}");
      
      return $stmt->execute();
    }
  }

  $sessionHandler = new MySQLSessionHandler();
  
?>
