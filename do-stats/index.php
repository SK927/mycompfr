<?php 

  require_once 'src/_header.php'; 


  if ( ! isset( $_GET['view_as'] ) )
  {
    require_once '../src/mysql_connect.php';
    require_once 'src/_functions.php'; 

    $is_admin = isset( $_SESSION['user_token'] ) && ( $_SESSION['logged_in'] );

?>  
  <div class="container-fluid">
    <?php if ( $is_admin ): ?>
      <div class="row">
        <div id="splash-screen" class="col-12 mt-3 text-center">
          <p>Loading your last competitions...</p>
          <progress value="50%" max="200">50%</progress> 
        </div>
      </div>
      <script src="assets/js/index-actions.js"></script>
    <?php else: ?>
      Please sign in as admin to continue
    <?php endif ?>
  </div>

<?php 

    $conn->close();    
  } 
  else
  {

    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}/statistics.php" );
    exit();
  }
  
  require_once '../src/_footer.php';

?>



