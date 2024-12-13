<?php 

  require_once 'src/layout/_header.php';
  require_once 'config/tools.php';

  $types = array(
              'INTERNATIONAL TOOLS' => INTERNATIONAL_TOOLS,
              'FRENCH TOOLS / OUTILS EN FRANÇAIS' => FRENCH_TOOLS,
            );

?>

<div class="container text-center">
  <?php foreach ( $types as $title => $data ): ?>
    <div class="row mt-2">
      <div class="card py-3 border-0">
        <div class="card-body">
          <h3><?php echo $title ?></h3>
          <div class="row mt-3 gy-3">
            <?php foreach ( $data as $tool ): ?>
              <div class="col-md-6 mb-2 px-3">
                <div class="card">
                  <div class="card-header">
                    <?php echo $tool['name'] ?>
                  </div>
                  <div class="card-body">  
                    <div class="col-12">
                      <?php echo $tool['description'] ?>
                      <a href="https://<?php echo "{$_SERVER['SERVER_NAME']}/portal?captive_for={$tool['folder']}&locale={$tool['locale']}"?>">(<?php echo $tool['link'] ?>)</a>
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

<?php require_once 'src/layout/_footer.php' ?>