<?php

  require_once 'src/layout/_header.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] ) )
  {
    require_once '../src/mysql/mysql-connect.php';
    require_once 'src/functions/orders-functions.php';
    require_once 'src/layout/admin-competition-templates.php';

    $competition_data = get_competition_data( $competition_id, $conn );
    $catalog = from_pretty_json( $competition_data['competition_catalog'] );
    $competition_orders = get_competition_orders( $competition_id, $conn );
    $items_amount = get_items_amount( $competition_id, $conn );

?>

<script src="assets/js/admin-competition-actions.js"></script> <!-- Custom JS to handle current page actions -->
<div class="container text-center">
  <div class="row">
    <h1 id="<?php echo $_GET['id']; ?>" class="competition-name col-12 text-uppercase"><?php echo $competition_data['competition_name'] ?></h1>
  </div>
  <div class="row">
    <form id="competition-info" class="col-12 col-md-6 mt-3" action="src/admin/ajax-update-competition?id=<?php echo urlencode( $_GET['id'] ) ?>" method="POST">
      <div class="card section">
        <div class="card-header section-title fw-bold">
          INFORMATIONS SUR LA COMPETITION
        </div>
        <div class="card-body col-12">
          <div class="email col-12 text-start">
            <h5 class="card-title">Adresse de contact</h5>
            <input id="competition-contact-email" class="form-control my-2" type="text" placeholder="a@a.com;b@b.com" value="<?php echo decrypt_data( $competition_data['contact_email'] ) ?>" name="competition_contact_email" required>
          </div>
          <div class="date col-12 mt-3 text-start">
            <h5 class="card-title">Date de clôture</h5>
            <input id="competition-orders-closing-date" class="form-control my-2" type="date" value="<?php echo $competition_data['orders_closing_date'] ?>" name="competition_orders_closing_date">
          </div>
          <div class="comment col-12 mt-3 text-start">
            <h5 class="card-title">Note</h5>
            <div id="comment-area">
              <?php if ( empty( $competition_data['competition_information'] ) ): ?>
                <a href="#" class="add-comment card-link">(+) Ajouter une note</a>
              <?php else: ?>
                <script>showComment( $('#comment-area'), `<?php echo htmlspecialchars( addslashes( $competition_data['competition_information'] ) ); ?>` );</script>
              <?php endif ?>
            </div>
          </div> 
          <div class="col-12 mt-4 text-center">
            <button class="submit-competition-info btn btn-light mb-2">Sauvegarder</button>           
          </div>
        </div>
      </div>
    </form>
    <div class="col-12 col-md-6 mt-3">
      <div class="card section">
        <div class="card-header section-title fw-bold">
          MES OUTILS
        </div>
        <div class="card-body col-12">
          <div class="row pt-1">
            <a class="mb-4" href="assets/admin-manual.pdf">Télécharger le manuel administrateur</a>
            <div class="col-12">
              <a href="admin-handle-catalog?id=<?php echo urlencode( $_GET['id'] ) ?>">
                <button class="btn btn btn-light mb-2">Gérer le catalogue</button>
              </a>
            </div>
            <div class="col-12">
              <a href="src/admin/admin-extract-catalog-csv?id=<?php echo urlencode( $_GET['id'] ) ?>">
                <button class="btn btn btn-light mb-2">Extraire le catalogue en CSV</button>
              </a>
            </div>
            <div class="col-12">
              <a href="src/pdf/pdf-generate-catalog?id=<?php echo urlencode( $_GET['id'] ) ?>">
                <button class="btn btn btn-light mb-2">Télécharger le catalogue en PDF</button>
              </a>
            </div>
            <div class="col-12">
              <a href="src/admin/admin-extract-data-csv?id=<?php echo urlencode( $_GET['id'] ) ?>">
                <button class="btn btn btn-light mb-2">Extraire les données en CSV</button>
              </a>
            </div>
            <div class="col-12">
              <a href="src/pdf/pdf-generate-orders-list?id=<?php echo urlencode( $_GET['id'] ) ?>">
                <button class="btn btn btn-light mb-2">Générer le PDF des commandes</button>
              </a>
            </div>
            <div class="col-12">
              <button class="update-list btn btn btn-light mb-2">MAJ depuis le WCIF</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php if ( count( $competition_orders ) ): ?>
      <div class="col-12 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            <?php echo count( $competition_orders) ?> COMMANDES ENREGISTREES <sub id="total-amount"></sub>
          </div>
          <div class="card-body col-12">
            <div class="row justify-content-end mb-2">
              <?php if ( $catalog ) :?>
                <?php foreach ( $catalog as $block_key => $block ): ?>
                  <span class="col-auto my-1 ms-3">
                    <label for="<?php echo $block_key ?>"><?php echo $block['name'] ?></label>
                    <input class="search-checkbox" type="checkbox" name="<?php echo $block_key ?>" />
                  </span>
                <?php endforeach; ?>
                <span class="col-auto my-1 ms-3">
                  <label for="Paid">NON PAY&Eacute;</label>
                  <input name="Paid" class="search-checkbox" type="checkbox" />
                </span>
                <span class="col-auto my-1">
                  <input id="search" class="form-control" type="text" placeholder="Rechercher par nom" />
                </span>
              <?php else: ?>
                <div class="col">
                  Mettez à jour le catalogue avant de pouvoir visualiser les commandes
                </div>
              <?php endif; ?>
            </div>
            <div class="row">
              <?php foreach ( $competition_orders as $order ): ?>
                <div id="<?php echo "{$_GET['id']}_{$order['id']}" ?>" class="placed-order col-12 col-md-6 col-lg-4 my-2">
                  <div class="card">
                    <div class="card-header p-3">
                      <span class="order-info fw-bold text-uppercase">
                        <span class="user-name">
                          <?php echo $order['user_name'] ?>
                        </span>
                        <sub class="order-amount">(<?php echo number_format( (float) $order['order_total'], 2, '.', '' ) ?>&nbsp;€)</sub>
                      </span>
                    </div>
                    <div class="card-body p-3 text-start">
                      <?php foreach ( from_pretty_json( $order['order_data'] ) as $block_key => $block ): ?>               
                        <div id="<?php echo $block_key ?>" class="block row mb-3<?php if ( $block['given'] ) echo ' strike' ?>">
                          <div class="col-auto">
                            <button class="given btn btn-sm btn-outline-<?php echo $block['given'] ? 'success' : 'danger' ?>"  type="button"></button>
                          </div>
                          <div class="card-text col">
                            <b class="fw-bold text-uppercase text-muted"><?php echo $catalog[ $block_key ]['name'] ?>&nbsp;: </b>
                            <ul>
                              <?php foreach ( $block['items'] as $item_key => $item ): ?> 
                                <li class="list-item m-0"><?php echo "{$item['qty']} x {$catalog[ $block_key ]['items'][ $item_key ]['name']}" ?></li>
                                <ul>
                                  <?php if ( isset( $item['options'] ) ): ?>
                                    <?php foreach ( $item['options'] as $option ): ?>
                                        <li>
                                        <?php
                                          foreach ( $option as $selection_key => $selection )
                                          {
                                            echo "{$catalog[ $block_key ]['items'][ $item_key ]['options'][ $selection_key ]['selections'][ $selection ]['name']}&nbsp;; ";
                                          }
                                        ?>
                                      </li>
                                    <?php endforeach ?>
                                  <?php endif ?>
                                </ul>
                              <?php endforeach ?>
                            </ul>
                          </div>
                        </div>
                      <?php endforeach ?>
                      <?php if ( ! empty( $order['user_comment'] ) ): ?>
                        <div class="row py-4">
                          <div class="card-text col text-end">
                            <b class="text-uppercase text-muted fw-bold">Commentaire&nbsp;: </b>
                            <?php echo $order['user_comment'] ?>
                          </div>
                        </div>
                      <?php endif; ?>
                      <div class="row justify-content-end">
                        <div class="col-auto mt-2">
                          <button class="order-is-paid btn btn-sm btn-outline-<?php echo $order['has_been_paid'] ? 'success' : 'danger' ?>" type="button"></button>
                        </div>
                        <div class="col-auto mt-2">
                          <button class="delete-order btn btn-sm btn-outline-danger" type="button">Supprimer</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif ?>

    <?php if ( $items_amount ): ?>
      <div class="col-12 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            PRODUITS
          </div>
          <div class="card-body col-12">
            <?php foreach ( $items_amount as $block_name => $item ): ?>
            <div class="row mt-1">
              <h4 class="col-12 text-start"><?php echo $block_name ?></h4>
            </div>
            <div class="row mb-1">
              <?php foreach ( $item as $item_name => $item_qty ): ?>
              <div class="col-6 col-sm-4 col-lg-3 col-xl-2 mb-3">
                <div class="card item-qty">
                  <h5 id="<?php echo "{$block_name}_{$item_name}" ?>" class="card-header"><?php echo $item_qty ?></h5>
                  <div class="card-body pt-0 pb-2 text-muted">
                    <?php echo $item_name ?>
                  </div>
                </div>
              </div> 
              <?php endforeach ?>  
            </div>
            <?php endforeach ?>
          </div>
        </div>
      </div>
    <?php endif ?>
  </div>
</div>
<script>calculateOrdersTotal();</script>

<?php 
    
    $conn->close();

    require_once '../src/layout/_status-bar.php';
  }
  else
  {
    header("Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );
    exit();
  }

  require_once '../src/layout/_footer.php'; 

?>
  