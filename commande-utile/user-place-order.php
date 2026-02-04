<?php

  require_once '../src/sessions_handler.php';

  $competition_id = $_GET['id'];
  $is_open = (($_SESSION['cu']['imported'][ $competition_id ]['orders_status_class'] == 'open') OR isset( $_SESSION['manageable_competitions'][ $competition_id ] ));
  $is_imported = ($competition_id == $_SESSION['cu']['imported'][ $competition_id ][ 'competition_id' ]);

  if( $_SESSION['logged_in'] and (($is_open and $is_imported) or $_SESSION['is_admin']) )
  {
    require_once 'src/_header.php';
    require_once 'src/_functions.php';
    
    mysqli_open( $mysqli );
    $competition = get_competition_data( $competition_id, $mysqli );
    $catalog = get_catalog( $competition_id, $mysqli ); 
    $order = get_order( hash_data( $competition_id, $_SESSION['user_id'] ), $mysqli );
    $mysqli->close();

?> 

<script>sessionStorage.setItem('catalog', `<?php echo json_encode( $catalog ) ?>` )</script>
<script src="assets/js/orders.js"></script>   
<div class="container"> 
  <form id="order-form" class="row" action="user-confirm-order.php?id=<?php echo urlencode( $competition_id ) ?>" method="POST">
    <div class="col-12 col-xl-7">
      <?php foreach( $catalog as $b_key => $b ): ?>
        <?php $items_cnt = count( $b['items'] ) ?>
        <?php $cnt = 1 ?>
        <div class="card mb-3">
          <div class="card-body pt-2">
            <div class="row sticky-top">
              <h2 class="mb-0 py-2 text-uppercase block-title"><?php echo $b['name'] ?></h2>
            </div>
            <div class="row">
              <?php foreach( $b['items'] as $i_key => $i ): ?>
                <?php $qty = $order['content'][ $b_key ]['items'][ $i_key ]['qty'] ? $order['content'][ $b_key ]['items'][ $i_key ]['qty'] : 0 ?>
                <div class="catalog-item col-12 mt-2 pt-3 pb-2 px-4<?php if( $cnt < $items_cnt ) echo " border-bottom"?>">
                  <div class="row">
                    <div class="col-auto d-none d-md-inline ps-0">
                      <img alt="<?php echo $i['image'] ?>" src="assets/img/icons/<?php echo $i['image'] ?>" />
                    </div>
                    <div class="col">
                      <b class="text-uppercase"><?php echo $i['name'] ?></b>
                      <span id="<?php echo "{$b_key}-{$i_key}" ?>-item-cost" class="text-muted">
                        (<?php echo $i['price'] ?> €)
                      </span> 
                      <p class="mb-0 text-muted">
                        <?php echo $i['description'] ?>
                      </p>
                      <?php if( $options = $catalog[ $b_key ]['items'][ $i_key ]['options'] ): ?>
                        <p class='mt-0 text-muted'>À sélectionner : 
                          <?php foreach( $options as $option ): ?>
                            <?php echo $option['name'] ?>&nbsp;;
                          <?php endforeach ?>
                        </p>
                      <?php endif ?>
                    </div>
                    <div class="col-auto text-end pe-0">
                      <div class="col-12">
                        <input class="form-control item-quantity p-1 float-end" type="number" min="0" value="<?php echo $qty ?>" name="<?php echo "{$b_key}-{$i_key}" ?>" />
                      </div>
                      <div id="<?php echo "{$b_key}-{$i_key}" ?>-item-total-cost" class="item-total-cost col-12 text-muted d-block">
                        (<?php echo number_format( $qty * $i['price'], 2 ) ?> €)
                      </div>
                    </div>
                    <div id="<?php echo "{$b_key}-{$i_key}" ?>-options" class="options col-12 mt-3 mb-4">  
                      <?php if( $options = format_options( $order['content'][ $b_key ]['items'][ $i_key ]['options'] ) ): ?>
                        <script>addUserSelections( `<?php echo json_encode( $options ) ?>`, `<?php echo $b_key ?>`, `<?php echo $i_key ?>` )</script>
                      <?php endif ?>
                    </div> 
                  </div> 
                </div>  
                <?php $cnt++ ?>
              <?php endforeach ?>
            </div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
    <div class="col-12 col-xl-5">
      <div id="information-competition" class="card">
        <div class="card-body">
          <div class="row mb-3">
            <h2 class="mb-0 pb-0">INFORMATIONS</h2>
          </div>
          <div class="row pt-3 border-top">
            <h5 class="mb-0"><?php echo $competition['name'] ?></h5>
            <?php if( $competition['information'] ): ?>
              <div class="col-12">
                <div class="alert alert-danger my-2" role="alert">
                  <?php echo $competition['information'] ?>
                </div>
              </div>
            <?php endif ?>
            <div class="col-12 mb-3 pb-3 border-bottom">
              <a class="card-link" href="https://www.worldcubeassociation.org/contact?competitionId=<?php echo urlencode( $competition_id ) ?>&contactRecipient=competition&message=Bonjour,%20j%E2%80%99ai%20une%20question%20sur%20Commande%20Utile." target="_blank">Contacter l'équipe organisatrice</a>
            </div>
            <h5 class="card-title"><?php echo $_SESSION['user_name'] ?></h5>
            <h6 class="card-subtitle mb-2 text-muted"><?php echo decrypt_data( $_SESSION['user_email'] ) ?></h6>
            <h6 class="card-subtitle mb-2 text-muted"><?php echo $_SESSION['user_wca_id'] ?></h6>
            <div id="comment" class="col-12 mb-3 pb-3 border-bottom">
              <a href="#" id="add-comment" class="card-link">(+) Ajouter un commentaire</a>
              <?php if( $order['user_comment'] ): ?>
                <script>$('#add-comment').trigger( 'click', `<?php echo $order['user_comment'] ?>` )</script>
              <?php endif ?>
            </div>
            <div class="col-12 mb-3 pb-3 border-bottom text-end">
              <h5 class="card-title">Total : <span id="order-total">0.00</span> €</h5>
            </div>
            <div class="col-12">
              <?php if( $order ): ?>
                <button id="delete-button" class="btn btn-danger my-1" name="delete">Supprimer la commande</button>
              <?php endif ?>
              <button id="confirm-button" class="btn btn-success my-1" disabled="disabled">Confirmer la commande</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<?php 
    
  }
  else
  {
    header( 'Location: index.php' );
    exit();
  }

  require_once '../src/_footer.php' 

?>
