<?php 

  require_once '../src/sessions_handler.php';
  require_once 'src/_header.php';
  require_once 'src/_functions.php';

  mysqli_open( $mysqli );

?>  

<script src="assets/js/index.js"></script>
<div class="container">
  <?php if( $_SESSION['logged_in'] ): ?>
    <?php [ $_SESSION['cu']['imported'], $_SESSION['cu']['importable'] ] = get_user_competitions( $_SESSION, $mysqli ) ?>
    <div class="card mb-3">
      <div class="card-body">
        <div class="row">
          <h2 class="mb-3 pb-3 border-bottom">MES COMPÉTITIONS</h2>
          <?php foreach( $_SESSION['cu']['imported'] as $id => $competition ): ?>
            <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mt-3">
              <div class="timeline-item timeline-element">
                <div>
                  <span class="timeline-element-icon bounce-in">
                    <i class="badge badge-dot badge-dot-xl <?php echo $competition['orders_status_class'] ?> mt-1"> </i>
                  </span>
                  <div class="timeline-element-content bounce-in">
                    <div class="row">
                      <div class="col-auto">
                        <h5 class="m-0 text-uppercase"><?php echo $competition['competition_name'] ?></h5>
                        <p class="mb-2 text-muted"><?php echo $competition['orders_status_text'] ?></span></p>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <?php if( $competition['orders_status_class'] == 'pending' and $competition['can_manage'] and $competition['has_catalog'] ): ?>
                        <div class="col-12">
                          <a href="user-place-order?id=<?php echo urlencode( $competition['competition_id'] ) ?>">
                            Pré-commander
                          </a>
                        </div>
                      <?php endif ?>
                      <?php if( $competition['orders_status_class'] == 'open' ): ?>
                        <div class="col-12">
                          <a href="user-place-order?id=<?php echo urlencode( $competition['competition_id'] ) ?>">
                            <?php if( ! $competition['has_ordered'] ): ?>
                              Passer une commande
                            <?php else: ?>
                              Modifier ma commande
                            <?php endif ?>
                          </a>
                        </div>
                      <?php endif ?>
                      <?php if( $competition['has_ordered'] ): ?>
                        <div class="col-12">
                          <a href="user-view-order?id=<?php echo urlencode( $competition['competition_id'] ) ?>">
                            Voir ma commande
                          </a>
                        </div>
                      <?php endif ?>
                      <?php if( $competition['can_manage'] ): ?>
                        <div class="col-12">
                          <a href="admin-handle-competition?id=<?php echo urlencode( $competition['competition_id'] ) ?>">
                            Administrer la compétition
                          </a>
                        </div>
                      <?php endif ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>
    <?php if( $_SESSION['cu']['importable'] ): ?>
      <div class="card">
        <div class="card-body">
          <div class="row">
            <h2 class="mb-3 pb-3 border-bottom">COMPÉTITIONS IMPORTABLES</h2>
            <?php foreach( $_SESSION['cu']['importable'] as $id => $competition ): ?>
              <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mt-3">
                  <div class="timeline-item timeline-element">
                    <div>
                      <span class="timeline-element-icon bounce-in">
                        <i class="badge badge-dot badge-dot-xl bg-secondary"> </i>
                      </span>
                      <div class="timeline-element-content bounce-in">
                        <div class="row">
                          <div class="col-auto">
                            <h5 class="m-0 text-uppercase"><?php echo $competition['name'] ?></h5>
                            <p class="mb-2 text-muted">du <?php echo $competition['start'] ?> au <?php echo $competition['end'] ?></span></p>
                          </div>
                        </div>
                        <div class="row mb-3">                
                          <div class="col-12">
                            <a class="import-link" href="admin-import-competition?id=<?php echo $id ?>">
                              Importer la compétition
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            <?php endforeach ?>
          </div>
        </div>
      </div>
    <?php endif ?>
  <?php else: ?>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body text-center">
            Veuillez vous connecter pour continuer<br/><br/>
            <a href="assets/manuals/user-manual.pdf" target="_blank">Télécharger le manuel utilisateur</a><br/>
            <a href="assets/manuals/admin-manual.pdf" target="_blank">Télécharger le manuel administrateur</a>
          </div>
        </div>
      </div>
    </div>
  <?php endif ?>
</div>

<?php 

  $mysqli->close();
  
  require_once '../src/_footer.php'

?>

    
