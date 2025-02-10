<?php

  require_once 'src/layout/_header.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, $_SESSION['commande_utile']['my_imported_competitions'] ) OR $_SESSION['is_admin'] ) )
  {
    require_once '../src/mysql/mysql-connect.php';
    require_once 'src/functions/orders-functions.php';

    $competition_data = get_competition_data( $competition_id, $conn );
    $catalog = from_pretty_json( $competition_data['competition_catalog'] );
    [ $error, $user_order, $user_comment, $order_total, $has_been_modified ] = get_user_order( $competition_id, $_SESSION['user_id'], $conn );

?>    

<?php if ( ! $error ): ?>
  <div class="container text-center">
    <div class="row">
      <h1 class="col-12 text-uppercase"><?php echo $competition_data['competition_name'] ?></h1>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            INFORMATIONS GENERALES
          </div>
          <div class="card-body col-12 text-start">
            <div class="row text-left">
              <?php if ( $competition_data['competition_information'] ): ?>
                <div class="col-12 mb-4">
                  <div class="row pe-3">
                    <div class="col-auto">
                      <img src="assets/img/CUtie.png" alt="CUtie"/>
                    </div>
                    <div class="col speech-bubble p-3">
                      L'équipe organisatrice m'a demandé de transmettre ce message : <i><?php echo $competition_data['competition_information'] ?></i>
                    </div>
                  </div>
                </div>
              <?php endif ?>
              <div class="col-12">
                <a class="card-link" href="https://www.worldcubeassociation.org/contact?competitionId=<?php echo $competition_id ?>&contactRecipient=competition" target="_blank">Contacter l'équipe organisatrice</a>
              </div>
              <div class="col-12">
                <a class="card-link" href="src/pdf/pdf-generate-catalog?id=<?php echo urlencode( $_GET['id'] ) ?>">Télécharger le catalogue</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            UTILISATEUR
          </div>
          <div class="card-body col-12 text-start">
            <div class="row">
              <div class="col-12">
                <h5 class="card-title"><?php echo $_SESSION['user_name'] ?></h5>
                <h6 class="card-subtitle mb-2 text-muted"><?php echo decrypt_data( $_SESSION['user_email'] ) ?></h6>
                <h6 class="card-subtitle mb-2 text-muted"><?php echo $_SESSION['user_wca_id'] ?></h6>
              </div>              
              <div id="comment" class="col-12 mt-2">
                <?php if ( $user_comment ): ?>
                  Mon commentaire : <span class="text-muted"><?php echo $user_comment ?></span>
                <?php endif; ?>              
              </div>
            </div>             
          </div>
        </div>
      </div>
      <div class="col-12 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            MA COMMANDE <sub>(<?php echo number_format( $order_total, 2 ) ?> €)</sub>
          </div>
          <div class="card-body col-12 text-start">
            <?php if ( $user_order ): ?>
              <?php foreach ( $user_order as $block_key => $block ): ?>
                <?php unset( $block['given'] ) ?>
                <div class="row mb-2">
                  <h4 class="col-12"><?php echo $catalog[ $block_key ]['name'] ?></h4>
                </div>
                <div class="row mb-2">
                  <?php foreach ( $block['items'] as $item_key => $item ): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-3">
                      <div class="ordered-item card">
                        <h5 class="card-header text-center"><?php echo $catalog[ $block_key ]['items'][ $item_key ]['name'] ?></h5>
                        <div class="card-body pt-0 text-left">
                          <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                              <?php echo "Quantité&nbsp;: {$item['qty']}" ?>
                            </li>
                            <?php if ( $item['options'] ): ?>
                              <li class="list-group-item">
                                <?php foreach ( $item['options'] as $option_key => $option ): ?>
                                  Sélection #<?php echo str_replace( '_', '', $option_key ) ?>&nbsp;:
                                  <ul>
                                    <?php foreach ( $option as $selection_key => $selection ): ?>
                                      <li>
                                        <?php echo "{$catalog[ $block_key ]['items'][ $item_key ]['options'][ $selection_key ]['name']}&nbsp;: {$catalog[ $block_key ]['items'][ $item_key ]['options'][ $selection_key ]['selections'][ $selection ]['name']}" ?>
                                      </li>
                                    <?php endforeach ?>
                                  </ul>
                                <?php endforeach ?>
                              </li>
                            <?php endif ?>
                          </ul>
                        </div>
                      </div>
                    </div>
                  <?php endforeach ?>  
                </div>  
              <?php endforeach ?>
            <?php else: ?>
              Aucune commande effectuée.
            <?php endif ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="row">
    Erreur lors du chargement de la commande.
  </div>
<?php endif ?>

<?php 

    $conn->close();
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );
    exit();
  }

  require_once '../src/layout/_footer.php'; 

?>
