<?php 

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
  <?php if ( $_SESSION['logged_in'] || ( ! $argument ) ): ?>
    <?php header( "Location: https://{$_SERVER['SERVER_NAME']}/{$captive}" ) ?>
    <?php exit() ?>
  <?php else: ?>
    <div class="row">
      <div class="card py-3 border-0">
        <div class="card-body">
          <form class="row justify-content-center px-3" action="src/oauth_index<?php if ( $captive != "" ) echo $argument ?>" method="POST">
            <div class="col-md-12 col-lg-8 col-xl-6 mb-5">
              <h3 class="mb-3">
                <?php echo $loc['portal'][ $locale ]['target'] ?> <b><?php echo $captive ?></b>
              </h3>
              <?php echo $loc['portal'][ $locale ]['access1'] ?> <a href="https://worldcubeassociation.org" target="_blank">World Cube Association</a> <?php echo $loc['portal'][ $locale ]['access2'] ?><br/><br/>
              <?php echo $loc['portal'][ $locale ]['privacy'] ?> <a href="https://<?php echo "{$_SERVER['SERVER_NAME']}/privacy-{$locale}" ?>" target="_blank"><?php echo $loc['portal'][ $locale ]['here'] ?></a>.        
            </div>
            <div class="card text-bg-light p-3">
              <div class="card-body">
                <div class="col-12">
                  <button class="btn btn-secondary">
                    <?php echo $loc['portal'][ $locale ]['signin'] ?>
                  </button>
                </div>
                <?php if ( in_array( $captive, array_merge( $rights['need_admin'], $rights['force_admin'] ) ) ): ?>
                  <div class="col-12 mt-2">
                    <label id="request-orga-label" for="request-orga-checkbox">
                      <?php echo $loc['portal'][ $locale ]['role'] ?>
                    </label>
                    <input id="request-orga-checkbox" type="checkbox" name="request_orga" <?php if ( in_array( $captive, $rights['force_admin'] ) ) echo 'checked'; ?> />
                  </div>        
                <?php endif ?>
              </div>        
            </div>        
          </form> 
        </div>
      </div>
    </div>
  <?php endif ?>
</div>

<?php require_once 'src/layout/_footer.php' ?>
