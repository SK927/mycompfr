<?php 

  require_once 'src/layout/_header.php';

  require_once '../src/mysql/mysql-connect.php';
  require_once '../src/functions/encrypt-functions.php';
  require_once 'src/functions/index-functions.php';
    
  [ $user_imported_competitions, $error ] = get_user_imported_competitions( $_SESSION['user_id'], $conn ); 
  $_SESSION['my_competitions'] = array_keys( $user_imported_competitions );

  $conn->close();

?>  
      <?php if ( $_SESSION['logged_in'] ): ?>
        <div class="row mt-3">
          <p class="col-12 fw-bold">SELECTIONNEZ VOTRE COMPETITION</p>
        </div>
        <div class="row justify-content-center">
          <?php foreach ( $user_imported_competitions as $competition_id => $competition_data ): ?>
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mt-3">
              <div class="card section">
                <div class="card-header section-title text-uppercase fw-bold">
                  <?php echo $competition_data['competition_name']; ?>
                </div>
                <div class="card-body py-0">
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item text-muted">
                      <small>                        
                        Dates : du <?php echo date( 'd/m/y', strtotime( $competition_data['competition_start_date'] ) ); ?> au <?php echo date( 'd/m/y', strtotime( $competition_data['competition_end_date'] ) ); ?>
                      </small>
                      <small>
                        Limite de commande : <?php echo $competition_data['orders_closing_date'] == '0000-00-00' ? '---' : date( 'd/m/y', strtotime( $competition_data['orders_closing_date'] ) ); ?>
                      </small>
                    </li>
                    <?php if ( $competition_data['orders_closing_date'] != '0000-00-00' ): ?>
                      <?php if ( date( 'Y-m-d' ) <= $competition_data['orders_closing_date'] ): ?>
                        <li class="list-group-item">
                          <a href="user-place-order?id=<?php echo urlencode( $competition_id ); ?>">Passer une commande</a>
                        </li>
                      <?php endif; ?>
                      <li class="list-group-item">
                        <a href="user-view-order?id=<?php echo urlencode( $competition_id ); ?>">Voir ma commande</a>
                      </li>
                    <?php endif; ?>
                    <?php if ( in_array( $competition_data['competition_id'] , $_SESSION['manageable_competitions'] ) OR $_SESSION['is_admin'] ): ?>
                      <li class="list-group-item">
                        <a href="admin-handle-competition?id=<?php echo urlencode( $competition_id ); ?>">Administrer</a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </div>
              </div>
            </div>
          <? endforeach;?>
        </div>
      <?php else :?>
        <div class="row justify-content-center mt-3">
          <div class="col-12 col-lg-6">
            <form class="card p-2" action="src/oauth/" method="POST">
              <div class="card-body">
                <div class="col-12">
                  <button class="btn btn-light">Se connecter avec la WCA</button>
                </div>
                <div class="col-12 mt-3">
                  <input id="request-orga-checkbox" type="checkbox" name="request_orga" />
                  <label id="request-orga-label" for="request-orga-checkbox">J'organise une compétition et souhaite administrer les commandes</label>
                </div>
                <div class="col-12 mt-3">
                  <sub>
                    Pour vous connecter au site, vous devez avoir un compte sur le site de la <a href="https://worldcubeassociation.org" target="_blank">World Cube Association</a>. En cliquant sur "Se connecter avec la WCA", vous serez redirigé·e vers le site de la WCA pour vous identifier, puis vers le site Commande Utile où vous serez alors connecté·e. En vous connectant au site Commande Utile vous acceptez l'utilisation des cookies nécessaires au fonctionnement du site ainsi que le traitement de vos données personnelles dans le cadre de votre commande. Ce site nécessite que JavaScript soit activé sur votre navigateur.<br/>
                    <a href="assets/user-manual.pdf" target="_blank">Télécharger le manuel utilisateur</a>
                  </sub>
                </div>
              </div>
            </form> 
          </div>
        </div>
      <?php endif; ?>  

<?php require_once 'src/layout/_footer.php'; ?>

    
