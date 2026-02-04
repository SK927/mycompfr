<?php 

  require_once '../src/sessions_handler.php'; 

  if( ! isset( $_GET['view_as'] ) )
  {
    require_once 'src/_header.php'; 
    require_once 'src/_functions.php'; 

    $is_admin = isset( $_SESSION['user_token'] );

?>  
  <div class="container-fluid">
    <?php if( $is_admin ): ?>
      <div class="row">
        <div id="splash-screen" class="col-12 mt-3 text-center">
          <p>Loading your last competitions...</p>
          <progress value="50%" max="200">50%</progress> 
        </div>
      </div>
      <script src="assets/js/index.js"></script>
    <?php else: ?>
      Please sign in as admin to continue
    <?php endif ?>
  </div>

<?php 

  } 
  else
  {

    header( "Location: /{$site_alias}/statistics.php?view_as={$_GET['view_as']}" );
    exit();
  }
  
  require_once '../src/_footer.php';

?>



