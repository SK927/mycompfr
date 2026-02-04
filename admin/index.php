<?php 

  require_once '../src/sessions_handler.php';
  require_once 'src/_header.php';

  require_once 'src/_functions.php';
  require_once '../src/mysqli.php';

  mysqli_open( $mysqli );
  $cu = get_cu_competitions( $mysqli );
  $rg = get_rc_competitions( $mysqli );
  $mysqli->close();

  
?>

<script src="assets/js/index.js"></script>
<div class="container-fluid">
  <div class="row justify-content-center">
    <?php if( $_SESSION['can_manage'] ): ?>
      <?php if( $_SESSION['is_admin'] ): ?>
        <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6 mt-3">
          <div class="card mb-3">
            <div class="card-body">
              <div class="row">
                <h2 class="mb-3">COMMANDE UTILE</h2>
              </div>
                <?php foreach( $cu as $competition ): ?>
                  <div class="row py-3 border-top">
                    <div class="col-auto">
                      <?php echo $competition['start_date'] ?>
                    </div>
                    <div class="col">
                      <h5 class="m-0"><?php echo $competition['name'] ?></h5>
                      <span class="text-muted"><?php echo decrypt_data( $competition['contact'] ) ?></span>
                    </div>
                    <div class="col-auto">
                      <p class="m-0"><a href="../commande-utile/admin-handle-competition?id=<?php echo $competition['id'] ?>" target="_blank">Administrer</a></p>
                    </div>
                  </div> 
                <?php endforeach ?>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6 mt-3">
          <div class="card mb-3">
            <div class="card-body">
              <div class="row">
                <h2 class="mb-3">REGISTRATION CHECKER</h2>
              </div>
                <?php foreach( $rg as $competition ): ?>
                  <div class="row py-3 border-top">
                    <div class="col-auto">
                      <?php echo $competition['start_date'] ?>
                    </div>
                    <div class="col">
                      <h5 class="m-0"><?php echo $competition['name'] ?></h5>
                      <span class="text-muted"><?php echo decrypt_data( $competition['contact'] ) ?></span>
                    </div>
                    <div class="col-auto">
                      <p class="m-0"><a href="../registration-checker" target="_blank">Administrer</a></p>
                    </div>
                  </div> 
                <?php endforeach ?>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="col-12 col-md-8 col-lg-6 mt-3">
          <div class="card section text-center">
            <div class="card-header section-title fw-bold">
              SIGN AS ADMINISTRATOR
            </div>
            <div class="card-body col-12 text-center">
              <div class="row justify-content-center pt-4 pb-3">
                <form id="form-credentials" class="col-12 col-md-10 col-xl-8" action="src/ajax_check-credentials" method="POST" name="form_credentials">
                  <div class="form-floating">
                    <input id="login" class="form-control mb-1 text-center" type="text" name="login" required>
                    <label for="login">Login</label>
                  </div>
                  <div class="form-floating">
                    <input id="password" class="form-control mb-1 text-center" type="password" name="password" required>
                    <label for="password">Password</label>
                  </div>
                  <button class="sign-in btn btn-light mt-2">Sign In</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endif ?>
    <?php else: ?>
      <div class="col-12 mt-3">
        You are not allowed to administer the website!
      </div>
    <?php endif ?>
  </div>
</div>   

<?php 
  
  require_once '../src/_status-bar.php';
  require_once '../src/_footer.php';
 
?>
