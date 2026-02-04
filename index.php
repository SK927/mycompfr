<?php 
  
  require_once 'src/sessions_handler.php';
  require_once 'src/_header.php';
  
  require_once 'config/config_loader.php';
  $tools = load_config_yaml( 'config-tools' );

?>

<div class="container text-center">
  <div class="row mt-2 mb-5">
    <div class="card p-0 border-0">
      <div class="card-body py-0">
        <div class="row mt-3 gy-3 justify-content-center">
          <?php foreach ( $tools['tools'] as $tool ): ?>
            <div class="col-md-6 col-lg-4 col-xl-3 mb-2 px-3">
              <div class="card">
                <div class="card-header">
                  <?php echo $tool['name'] ?> <span class="text-uppercase">(<?php echo $tool['locale'] ?>)</span>
                </div>
                <div class="card-body">  
                  <div class="col-12">
                    <?php echo $tool['description'] ?>
                    <a href="/<?php echo $tool['folder'] ?>">(<?php echo $tools['link'][ $tool['locale'] ] ?>)</a>
                  </div>    
                </div>    
              </div>    
            </div>
          <?php endforeach ?>
        </div>    
      </div>    
    </div>    
  </div>
</div>

<?php require_once 'src/_footer.php' ?>