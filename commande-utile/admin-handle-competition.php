<?php 

  require_once '../src/sessions_handler.php';

  $competition_id = $_GET['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {
    require_once 'src/_header.php';
    require_once 'src/_functions.php';
    
    mysqli_open( $mysqli );
    $competition = get_competition_data( $competition_id, $mysqli );
    $catalog = get_catalog( $competition_id, $mysqli ); 
    [ $all_orders, $total ] = get_all_orders( $competition_id, $mysqli );
    $items_amounts = get_items_amount( $competition_id, $mysqli );
    $mysqli->close();

?>  

<script src="assets/js/admin.js"></script>   
<div class="container"> 
  <div class="row mb-3 card mx-auto">
    <div class="card-body">
      <h2 class="card-title mb-3 pb-3 text-uppercase border-bottom"><?php echo $competition['name'] ?></h2>
      <div class="row">
        <div class="col-12 pb-3">
          <a class="" href="assets/manuals/admin-manual.pdf">Télécharger le manuel administrateur</a>
        </div>
        <div class="col-12 mb-2">    
          <a class="no-decoration" href="admin-handle-catalog?id=<?php echo urlencode( $_GET['id'] ) ?>">
            <button class="btn btn btn-light mb-2">Gérer le catalogue</button>
          </a>
          <a class="no-decoration" href="src/extract-catalog-csv?id=<?php echo urlencode( $_GET['id'] ) ?>" target="_blank">
            <button class="btn btn btn-light mb-2">Catalogue au format CSV</button>
          </a>
          <a class="no-decoration" href="src/pdf_generate-catalog?id=<?php echo urlencode( $_GET['id'] ) ?>" target="_blank">
            <button class="btn btn btn-light mb-2">Catalogue au format PDF</button>
          </a>
          <a class="no-decoration" href="src/extract-data-csv?id=<?php echo urlencode( $_GET['id'] ) ?>" target="_blank">
            <button class="btn btn btn-light mb-2">Commandes au format CSV</button>
          </a>
          <a class="no-decoration" href="src/pdf_generate-orders-list-by-competitor?id=<?php echo urlencode( $_GET['id'] ) ?>" target="_blank">
            <button class="btn btn btn-light mb-2">PDF des commandes (par compétiteurs)</button>
          </a>
          <a class="no-decoration" href="src/pdf_generate-orders-list-by-day?id=<?php echo urlencode( $_GET['id'] ) ?>" target="_blank">
            <button class="btn btn btn-light mb-2">PDF des commandes (par jours)</button>
          </a>
          <a class="no-decoration" href="src/pdf_generate-orders-list-by-item?id=<?php echo urlencode( $_GET['id'] ) ?>" target="_blank">
            <button class="btn btn btn-light mb-2">PDF des commandes (par produits)</button>
          </a>
          <button class="get-emails-list btn btn btn-light mb-2">Copier les e-mails vers le presse-papier</button>
          <button class="update-list btn btn btn-light mb-2">MAJ depuis le WCIF</button>
        </div>
        <form id="competition-info" class="col-12 mt-4" action="src/ajax_update-competition?id=<?php echo urlencode( $_GET['id'] ) ?>" method="POST">
          <div class="row">
            <div class="email col-12 col-md-6 mb-3">
              <h5 class="card-title">Adresse de contact</h5>
              <input id="competition-contact-email" class="form-control my-2" type="text" placeholder="a@a.com;b@b.com" value="<?php echo decrypt_data( $competition['contact'] ) ?>" name="competition_contact_email" required>
            </div>
            <div class="date col-12 col-md-6 mb-3">
              <h5 class="card-title">Date de clôture</h5>
              <input id="competition-orders-closing-date" class="form-control my-2" type="date" value="<?php echo $competition['orders_closing_date'] ?>" name="competition_orders_closing_date">
            </div>
            <div class="comment col-12 mb-3">
              <h5 class="card-title">Note</h5>
              <div id="note">
                <a href="#" id="add-note" class="card-link">(+) Ajouter une note</a>
                <?php if( $competition['information'] ): ?>
                  <script>$('#add-note').trigger( 'click', `<?php echo $competition['information'] ?>` )</script>
                <?php endif ?>
              </div>
            </div> 
            <div class="col-12 mb-2">
              <button class="submit-competition-info btn btn-success mt-3 mb-4 mb-lg-0">Sauvegarder</button>           
            </div>
          </div> 
        </form>
      </div>
    </div> 
  </div>
  <?php if( $items_amounts ): ?>
    <div class="row mb-3 card mx-auto">
      <div class="card-body">
        <h2 class="card-title mb-3 pb-3 border-bottom">PRODUITS COMMANDÉS</h2>
        <?php foreach( $items_amounts AS $block ): ?>
          <div class="row mb-3 g-2">
            <div class="col-12">
              <h2><?php echo $block['name'] ?></h2>
            </div>
            <?php foreach( $block['items'] AS $id => $item ): ?>
              <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card shadow-none p-2">
                  <div class="row text-center">
                    <div class="col-12">
                      <b><?php echo $item['qty'] ?></b>
                    </div>
                    <div class="col-12">
                      <?php echo $item['name'] ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach ?>
          </div>         
        <?php endforeach?>
      </div>
    </div>
  <?php endif ?>
  <?php if( $all_orders ): ?>
    <div id="all-orders" class="row mb-3 card mx-auto">
      <div class="card-body">
        <h2 class="card-title mb-3 pb-3 text-uppercase border-bottom">COMMANDES <sub>(<?php echo $total ?> €)</sub></h2>
        <div class="row">
          <div class="col-12 text-end">
            <input id="search" class="form-control mb-2" type="text" placeholder="Rechercher par nom" />
            <?php foreach( $catalog as $b_key => $b ): ?>
              <label for="<?php echo $b_key ?>"><?php echo strtoupper( $b['name'] ) ?></label>
            <input name="<?php echo $b_key ?>" class="search-checkbox mb-4 me-3" type="checkbox" />
            <?php endforeach?>
            <label for="Paid">NON PAY&Eacute;</label>
            <input name="Paid" class="search-checkbox mb-4" type="checkbox" />
          </div>
          <?php foreach( $all_orders as $id => $order ): ?>
            <div id="<?php echo $id ?>" class="placed-order col-12 col-lg-6 mb-3">
              <table id="<?php echo $id ?>-table" class="table">
                <tr>
                  <td colspan=2 class="user-name table-dark text-uppercase fw-bold">
                    <?php echo $order['user_name'] ?>
                    <?php if( ! empty( $order['user_wca_id'] ) ) echo " ({$order['user_wca_id']})" ?>    
                    <span class="float-end"><?php echo number_format( $order['order_total'], 2 ) ?> €</span>
                  </td>
                </tr>
                <?php if( $order['user_comment'] ): ?>
                  <tr>
                    <td colspan=2 class="table-warning"><?php echo $order['user_comment'] ?></td>
                  </tr>
                <?php endif ?>
                  <tr>
                    <td class="border-0">
                      <div class="row">
                      <?php foreach( $order['content'] as $b_key => $b ): ?>
                        <div class="col-12 col-md-6 col-lg-12 col-xxl-6 my-3">
                          <div class="row px-3">
                            <div id="<?php echo "{$id}_{$b_key}" ?>" class="col-12 text-uppercase fw-bold bg-light p-2">
                              <button class="<?php echo "{$b_key}-given" ?> given btn btn-sm btn-outline-<?php echo $b['given'] ? 'success' : 'danger' ?> me-1"></button>
                              <?php echo $b['name'] ?>    
                              <span class="float-end"><?php echo number_format( $b['total_cost'], 2 ) ?> €</span>
                            </div>
                            <?php foreach( $b['items'] as $i ): ?>
                              <div class="col-12 pt-2">
                                <?php echo $i['qty'] ?> x <?php echo $i['name'] ?> 
                                (<?php echo number_format( $i['total_cost'], 2 ) ?> €)
                                <?php if( $i['options'] ): ?>
                                  <ul class="m-0 text-muted">
                                    <?php foreach( $i['options'] as $o ): ?>
                                      <?php foreach( $o as $s ): ?>
                                        <li>
                                          <?php echo $s['qty'] ?> x <?php echo $s['name'] ?>
                                          <?php if( $s['total_cost'] ) : ?>
                                            (+ <?php echo number_format( $s['total_cost'], 2 ) ?> €)
                                          <?php endif ?>
                                        </li>
                                      <?php endforeach ?>
                                    <?php endforeach ?>
                                  </ul>
                                <?php endif ?>
                              </div>
                            <?php endforeach ?>
                          </div>
                        </div>
                      <?php endforeach ?>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td colspan=2 class="text-end py-3 pe-0 border-0 border-top">
                    <button class="order-is-paid btn btn-sm btn-outline-<?php echo $order['paid'] ? 'success' : 'danger' ?>" type="button"></button>
                    <button class="delete-order btn btn-sm btn-outline-danger" type="button">Supprimer</button>
                  </td>
                </tr>
                <tr>
                  <td colspan=2 class="text-end pt-0 pb-3 pe-0 border-0">
                    <a href="#" id="add-comment" class="card-link border-0">(+) Ajouter une note</a>
                    <?php if( $order['admin_comment'] ): ?>
                      <script>$('#add-comment').trigger( 'click', `<?php echo $order['admin_comment'] ?>` )</script>
                    <?php endif ?>
                  </td>
                </tr>
              </table>
            </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>
  <?php endif ?>
</div>

<?php 
  
    require_once '../src/_status-bar.php';
  }
  else
  {
    header( 'Location: index.php' );
    exit();
  }
  
  require_once '../src/_footer.php'

?>

    
