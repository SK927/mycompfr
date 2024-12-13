<?php 

  require_once 'src/layout/_header.php';

  require_once '../src/mysql/mysql-connect.php';
  require_once '../src/functions/encrypt-functions.php';
  require_once 'src/functions/index-functions.php';
    
  [ $user_imported_competitions, $error ] = get_user_imported_competitions( $_SESSION['user_id'], $conn ); 
  $_SESSION['commande_utile']['my_imported_competitions'] = array_keys( $user_imported_competitions );

  $conn->close();

?>  

<div class="container text-center">
  <?php if ( $_SESSION['logged_in'] ): ?>
    <div class="row mt-3">
      <p class="col-12 fw-bold">SELECTIONNEZ VOTRE COMPETITION</p>
    </div>
    <div class="row justify-content-center">
      <?php foreach ( $user_imported_competitions as $competition_id => $competition_data ): ?>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mt-3">
          <div class="card section">
            <div class="card-header section-title text-uppercase fw-bold">
              <?php echo $competition_data['competition_name'] ?>
            </div>
            <div class="card-body py-0">
              <ul class="list-group list-group-flush">
                <li class="list-group-item text-muted">
                  <small>
                    <p class="m-0">                        
                      Dates : du <?php echo date( 'd/m/y', strtotime( $competition_data['competition_start_date'] ) ) ?> au <?php echo date( 'd/m/y', strtotime( $competition_data['competition_end_date'] ) ) ?>
                    </p>
                    <p class="m-0">
                      Limite de commande : <?php echo $competition_data['orders_closing_date'] == '0000-00-00' ? '---' : date( 'd/m/y', strtotime( $competition_data['orders_closing_date'] ) ) ?>
                    </p>
                  </small>
                </li>
                <?php if ( $competition_data['orders_closing_date'] != '0000-00-00' ): ?>
                  <?php if ( date( 'Y-m-d' ) <= $competition_data['orders_closing_date'] ): ?>
                    <li class="list-group-item">
                      <a href="user-place-order?id=<?php echo urlencode( $competition_id ) ?>">
                        <?php if ( ! $competition_data['has_ordered'] ): ?>
                          Passer une commande
                        <?php else: ?>
                          Modifier ma commande
                        <?php endif ?>
                      </a>
                    </li>
                  <?php endif ?>
                  <?php if ( $competition_data['has_ordered'] ): ?>
                    <li class="list-group-item">
                      <a href="user-view-order?id=<?php echo urlencode( $competition_id ) ?>">Voir ma commande</a>
                    </li>
                  <?php endif; ?>
                <?php endif; ?>
                <?php if ( in_array( $competition_data['competition_id'] , array_keys( $_SESSION['manageable_competitions'] ) ) or $_SESSION['is_admin'] ): ?>
                  <li class="list-group-item">
                    <a href="admin-handle-competition?id=<?php echo urlencode( $competition_id ) ?>">Administrer</a>
                  </li>
                <?php endif ?>
              </ul>
            </div>
          </div>
        </div>
      <? endforeach ?>
    </div>
  <?php else: ?>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card text-bg-custom">
          <div class="card-body">
            Veuillez vous connecter pour continuer<br/><br/>
            <a href="assets/user-manual.pdf" target="_blank">Télécharger le manuel utilisateur</a><br/>
            <a href="assets/admin-manual.pdf" target="_blank">Télécharger le manuel administrateur</a>
          </div>
        </div>
      </div>
    </div>
  <?php endif ?>
</div>

<?php require_once dirname( __DIR__, 1 ) . '/src/layout/_footer.php' ?>

    
