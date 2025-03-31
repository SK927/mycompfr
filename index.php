<?php 

  require_once 'src/_header.php';
  
  require_once 'config/config_loader.php';
  $types = load_config_yaml( 'config-tools' );

?>

<div class="container text-center">
  <?php foreach ( $types as $locale => $data ): ?>
    <div class="row mt-2 mb-5">
      <div class="card p-0 border-0">
        <div class="card-body py-0">
          <h3 class="text-uppercase"><?php echo $data['title'] ?></h3>
          <div class="row mt-3 gy-3 justify-content-center">
            <?php foreach ( $data['description'] as $tool ): ?>
              <div class="col-md-6 col-lg-4 col-xl-3 mb-2 px-3">
                <div class="card">
                  <div class="card-header">
                    <?php echo $tool['name'] ?>
                  </div>
                  <div class="card-body">  
                    <div class="col-12">
                      <?php echo $tool['description'] ?>
                      <a href="https://<?php echo "{$_SERVER['SERVER_NAME']}/{$tool['folder']}"?>">(<?php echo $data['link'] ?>)</a>
                    </div>    
                  </div>    
                </div>    
              </div>
            <?php endforeach ?>   
          </div>    
        </div>    
      </div>    
    </div>
  <?php endforeach ?>
</div>

<?php require_once 'src/_footer.php' ?>