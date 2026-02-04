<?php 

  require_once 'src/sessions_handler.php';
  require_once 'src/_header.php';
  
  require_once 'config/config_loader.php';
  $rights = load_config_yaml( 'config-rights' );
  $loc = load_config_yaml( 'config-localization' );

  // Make sure that any way to enter the portal will bring to the right place by retrieving the right captive_for argument
  $captive = $_GET['captive_for'];
  $argument = $captive != '' ? "?captive_for={$captive}" : '';

  // Make sure that locale is set to the right value (English by default)
  $locale = $_GET['locale'] ? $_GET['locale'] : 'en';
  
?>

<div class="container text-center">
  <?php if( $_SESSION['logged_in'] or ( ! $argument ) ): ?>
    <?php header( "Location: /{$captive}" ) ?>
    <?php exit() ?>
  <?php else: ?>
    <div class="row">
      <form class="row justify-content-center px-3" action="src/oauth_index<?php if ( $captive != "" ) echo $argument ?>" method="POST">
        <div class="col-md-12 col-lg-8 col-xl-6 mb-5">
          <p>
            <h3 class="mb-4"><?php echo $loc['portal'][ $locale ]['target'] ?> <b><?php echo $captive ?></b></h3>
            <?php echo $loc['portal'][ $locale ]['access1'] ?> <a href="https://worldcubeassociation.org" target="_blank">World Cube Association</a> <?php echo $loc['portal'][ $locale ]['access2'] ?><br/><br/>
            <?php echo $loc['portal'][ $locale ]['privacy'] ?> <a href="https://<?php echo "{$_SERVER['SERVER_NAME']}/privacy-{$locale}" ?>" target="_blank"><?php echo $loc['portal'][ $locale ]['here'] ?></a>.        
          </p>
          <div class="card text-bg-light mt-5 p-1">
            <div class="card-body">
              <?php if ( in_array( $captive, array_merge( $rights['need_admin'], $rights['force_admin'] ) ) ): ?>
                <div class="col-12 mb-3 d-flex align-items-center justify-content-center">
                  <input id="request-orga-checkbox" class="me-2" type="checkbox" name="request_orga" <?php if ( in_array( $captive, $rights['force_admin'] ) ) echo 'checked'; ?> />
                  <label id="request-orga-label" class="w-50" for="request-orga-checkbox"><?php echo $loc['portal'][ $locale ]['role'] ?></label> 
                </div>        
              <?php endif ?>
              <div class="col-12">
                <button class="btn btn-secondary">
                  <?php echo $loc['portal'][ $locale ]['signin'] ?>
                </button>
              </div>
            </div>        
          </div> 
        </div>
      </form> 
    </div>
  <?php endif ?>
</div>

<?php require_once 'src/_footer.php' ?>
