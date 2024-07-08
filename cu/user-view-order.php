<?php

  require_once 'src/layout/_header.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, $_SESSION['my_competitions'] ) OR $_SESSION['is_admin'] ) )
  {
    require_once '../src/mysql/mysql-connect.php';
    require_once 'src/functions/orders-functions.php';

    $competition_data = get_competition_data( $competition_id, $conn );
    [ $error, $user_order, $user_comment, $order_total, $has_been_modified ] = get_user_order( $competition_id, $_SESSION['user_id'], $conn );

?>    
      <?php if ( ! $error ) :?>
        <div class="row">
          <h1 class="col-12 text-uppercase"><?php echo $competition_data['competition_name']; ?></h1>
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
                      <h5 class="card-title">Note de l'équipe organisatrice</h5>
                      <p class="card-text">
                        <?php echo $competition_data['competition_information']; ?>
                      </p>
                    </div>
                  <?php endif; ?>
                  <div class="col-12">
                    <a class="card-link" href="https://www.worldcubeassociation.org/contact?competitionId=<?php echo $competition_id; ?>&contactRecipient=competition" target="_blank">Contacter l'équipe organisatrice</a>
                  </div>
                  <div class="col-12">
                    <a class="card-link" href="src/pdf/pdf-generate-catalog?id=<?php echo urlencode( $_GET['id'] ); ?>">Télécharger le catalogue</a>
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
                    <h5 class="card-title"><?php echo $_SESSION['user_name']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo decrypt_data( $_SESSION['user_email'] ); ?></h6>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $_SESSION['user_wca_id']; ?></h6>
                  </div>              
                  <div id="comment" class="col-12 mt-2">
                    <?php if ( $user_comment ): ?>
                      Mon commentaire : <span class="text-muted"><?php echo $user_comment; ?></span>
                    <?php endif; ?>              
                  </div>
                </div>             
              </div>
            </div>
          </div>
          <div class="col-12 mt-3">
            <div class="card section">
              <div class="card-header section-title fw-bold">
                MA COMMANDE <sub>(<?php echo number_format( $order_total, 2 ) ; ?> €)</sub>
              </div>
              <div class="card-body col-12 text-start">
                <?php if ( $user_order ): ?>
                  <?php foreach ( $user_order as $block_name => $block_items ): ?>
                    <?php unset( $block_items['given'] ); ?>
                    <?php if ( count( $block_items ) ): ?>
                      <div class="row mb-2">
                        <h4 class="col-12"><?php echo $block_name; ?></h4>
                      </div>
                      <div class="row mb-2">
                        <?php foreach ( $block_items as $item_name => $item_value ): ?>
                          <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-3">
                            <div class="ordered-item card">
                              <h5 class="card-header text-center"><?php echo $item_name; ?></h5>
                              <div class="card-body pt-0 text-left">
                                <ul class="list-group list-group-flush">
                                  <li class="list-group-item">
                                    <?php echo "Quantité&nbsp;: {$item_value['qty']}"; ?>
                                  </li>
                                  <?php if ( $item_value['options'] ): ?>
                                    <li class="list-group-item">
                                      <?php foreach ( $item_value['options'] as $index => $options ): ?>
                                        Sélection #<?php echo $index; ?>&nbsp;:
                                        <ul>
                                          <?php foreach ( $options as $option_name => $option_selected ): ?>
                                            <li>
                                              <?php echo "{$option_name}&nbsp;: {$option_selected}" ?>
                                            </li>
                                          <?php endforeach; ?>
                                        </ul>
                                      <?php endforeach; ?>
                                    </li>
                                  <?php endif; ?>
                                </ul>
                              </div>
                            </div>
                          </div>
                        <?php endforeach;?>  
                      </div>
                    <?php endif; ?>  
                  <?php endforeach;?>
                <?php else:?>
                  Aucune commande effectuée.
                <?php endif;?>
              </div>
            </div>
          </div>
        </div>
      <?php else : ?>
        <div class="row">
          Erreur lors du chargement de la commande.
        </div>
      <?php endif; ?>
<?php 

    $conn->close();
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}" );
    exit();
  }

  require_once 'src/layout/_footer.php'; 

?>
