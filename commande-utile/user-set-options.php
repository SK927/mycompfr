<?php

  require_once 'src/layout/_header.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND $_POST AND ( in_array( $competition_id, $_SESSION['commande_utile']['my_imported_competitions'] ) OR $_SESSION['is_admin'] ) )
  {  
    require_once '../src/mysql/mysql-connect.php';
    require_once 'src/functions/orders-functions.php';
    
    $competition_data = get_competition_data( $competition_id, $conn );
    $competition_catalog_blocks = from_pretty_json( $competition_data['competition_catalog'] );
    [ $error, $user_order, $user_comment ] = get_user_order( $competition_id, $_SESSION['user_id'], $conn );

    $conn->close();

?>

<?php if ( ! $error ): ?>
  <div class="container text-center">
    <script src="assets/js/user-options-actions.js"></script> <!-- Custom JS to handle current page actions -->
    <div class="row">
      <h1 class="col-12 text-uppercase"><?php echo $competition_data['competition_name'] ?></h1>
    </div>
    <div class="row">
      <div class="col-12 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            MES OPTIONS
          </div>
          <div class="card-body col-12 text-start">
            <div class="row">
              <div class="col">
                <div class="row pe-3">
                  <div class="col-auto">
                    <img src="assets/img/CU-tee.png" alt="CU-tee"/>
                  </div>
                  <div class="col speech-bubble p-3">
                    Certains produits que vous avez sélectionnés contiennent des options que vous pouvez paramétrer comme vous le souhaitez ci-dessous. N'oubliez pas de sauvegarder votre commande pour que celle-ci soit prise en compte. 
                  </div>
                </div> 
              </div>
            </div>
            <form id="order-form" class="row" action="user-confirm-order?id=<?php echo urlencode( $_GET['id'] ) ?>" method="POST" name="order_form">
              <div class="col-12">
                <?php foreach ( $competition_catalog_blocks as $block_key => $block ): ?>
                  <?php $block_items_selected = search_for_block_items_only( $block_key, $_POST ); ?>
                  <?php $items_with_options = false ?>
                  <?php if ( $block_items_selected ): ?>
                    <div class="row mt-4 mb-2">
                      <h4 class="col-12"><?php echo $block['name'] ?></h4>
                    </div>
                    
                    <?php foreach ( $block_items_selected as $item_key => $item_qty ): ?>
                      <script>sessionStorage.setItem( '<?php echo $item_key ?>', <?php echo $item_qty ?> )</script>
                      <input id="<?php echo $item_key ?>" type="hidden" value="<?php echo $item_qty ?>" name="<?php echo $item_key ?>">

                      <?php $split_id = explode( '-', $item_key ); ?>
                      <?php if ( $block['items'][ $split_id[1] ]['options'] ): ?>
                        <?php for ( $i = 0 ; $i < $item_qty ; $i++ ): ?>

                          <div class="row mb-2">
                            <div class="col-12 text-uppercase fw-bold mb-2">
                              <?php echo "{$block['items'][ $split_id[1] ]['name']} #" . ($i + 1) ?>
                            </div> 
                            <?php foreach ( $block['items'][ $split_id[1] ]['options'] as $option_key => $option ): ?>
                              <?php $option_id = "{$i}_{$split_id[0]}-{$split_id[1]}-{$option_key}" ?>
                              <?php $selected = $user_order[ $split_id[0] ]['items'][ $split_id[1] ]['options'][ "{$i}_" ][ $option_key ] ?>
                              <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                <div class="form-floating">
                                  <select id="<?php echo $option_id ?>" class="form-select mt-1 text-center" name="<?php echo $option_id ?>">
                                    <?php foreach ( $option['selections'] as $selection_key => $selection ): ?>
                                      <?php $value = $selection['name'] ?>
                                      <?php if ( $selection['price'] != '0.00' ) $value .= " (+{$selection['price']}€)" ?>

                                      <option value="<?php echo $selection_key ?>"<?php if ( isset( $selected ) and $selected == $selection_key ) echo 'selected' ?>>
                                        <?php echo $value ?>
                                      </option>  
                                    <?php endforeach; ?>
                                  </select>
                                  <label for="<?php echo $option_id ?>"><?php echo $option['name'] ?></label>
                                </div>
                              </div>
                            <?php endforeach ?>
                          </div>

                        <?php endfor ?>     
                        <?php $items_with_options = true ?>
                      <?php endif ?>
                    <?php endforeach ?>

                    <?php if ( ! $items_with_options ): ?>
                      Vous avez sélectionné un ou plusieurs produits pour ce bloc, mais aucun d'entre eux ne nécessite la sélection d'options.
                    <?php endif ?>
                  <?php endif ?>
                <?php endforeach ?> 
                <?php if ( isset( $_POST['user_comment'] ) ): ?>
                  <input type="hidden" value="<?php echo $_POST['user_comment'] ?>" name="user_comment">
                <?php endif ?>
              </div>
              <div class="col">
                <div class="order-total row fixed-bottom px-4 py-3">
                  <div class="col-12 col-md-auto">
                    <h5>
                      Total de la commande : 
                      <span id="amount" class="fw-bold"></span>&nbsp;€
                    </h5>
                  </div>
                  <div class="col-12 col-md text-md-end">
                    <button class="button-back btn btn-danger my-1">Retour aux produits</button>
                    <button class="btn btn-success my-1">Confirmer la commande</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>   
      </div>
    </div>
  </div>
  <script>setNewAmount();</script>
<?php else : ?>
  <div class="row">
    Erreur lors du chargement de la commande.
  </div>
<?php endif ?>

<?php 

  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );
    exit();
  }

  require_once '../src/layout/_footer.php'; 

?>