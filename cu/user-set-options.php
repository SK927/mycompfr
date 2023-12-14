<?php

  require_once 'src/layout/_header.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND $_POST AND ( in_array( $competition_id, $_SESSION['my_competitions'] ) OR $_SESSION['is_admin'] ) )
  {  
    require_once '../src/mysql/mysql-connect.php';
    require_once 'src/functions/orders-functions.php';
    
    $competition_data = get_competition_data( $competition_id, $conn );
    $competition_catalog_blocks = from_pretty_json( $competition_data['competition_catalog'] );
    [ $error, $user_order, $user_comment ] = get_user_order( $competition_id, $_SESSION['user_id'], $conn );

?>
      <?php if ( ! $error ) :?>
        <script src="assets/js/user-options-actions.js"></script> <!-- Custom JS to handle current page actions -->
        <div class="row">
          <h1 class="col-12 text-uppercase"><?php echo $competition_data['competition_name']; ?></h1>
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
                    Certains produits que vous avez sélectionnés contiennent des options que vous pouvez paramétrer comme vous le souhaitez ci-dessous. N'oubliez pas de sauvegarder votre commande pour que celle-ci soit prise en compte. 
                  </div>
                </div>
                <form id="order-form" class="row" action="user-confirm-order?id=<?php echo urlencode( $_GET['id'] ); ?>" method="POST" name="order_form">
                  <div class="col-12">
                    <?php foreach ( $competition_catalog_blocks as $block_name => $block_value ): ?>
                      <?php $block_items = search_for_block_items_only( $block_name, $_POST ); ?>
                      <?php if ( $block_items ): ?>
                        <?php $items_with_options = false; ?>
                        <div class="row mt-4 mb-2">
                          <h4 class="col-12"><?php echo $block_name; ?></h4>
                        </div>
                      <?php endif; ?>
                      <?php foreach ( $block_items as $item_id => $qty ): ?>
                        <script>sessionStorage.setItem( '<?php echo $item_id; ?>', <?php echo $qty; ?> )</script>
                        <input id="<?php echo $item_id; ?>" type="hidden" value="<?php echo $qty; ?>" name="<?php echo $item_id; ?>">
                        <?php $info = explode( '***', $item_id ); ?>
                        <?php $block = decrypt_data( $info[0] ); ?>
                        <?php $item = $competition_catalog_blocks[ $block ][ $info[1] ]; ?>
                        <?php if ( $item['options'] ): ?>
                          <?php $items_with_options = $items_with_options || true; ?>
                          <?php $selected_options = $user_order[ $block ][ $item['item_name'] ]['options']; ?>
                          <?php for ( $i = 0 ; $i < $qty ; $i++ ): ?>             
                            <div class="row mb-2">
                              <div class="col-12 text-uppercase fw-bold mb-2"><?php echo "{$item['item_name']} #" . ($i + 1); ?></div> 
                              <?php foreach ( $item['options'] as $option_name => $option_value ):?>
                                <?php $option_id = "{$item_id}***" . encrypt_data( $option_name ) . "***{$i}"; ?>
                                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                  <div class="form-floating">
                                    <select id="<?php echo $option_id; ?>" class="form-select mt-1 text-center" name="<?php echo $option_id; ?>">
                                      <?php $options = explode(';', $option_value); ?>
                                      <?php foreach ( $options as $option ): ?>
                                        <option value="<?php echo $option; ?>"<?php if ( $selected_options[ $i ][ $option_name ] == $option ) echo ' selected'; ?>>
                                          <?php echo $option; ?>
                                        </option>  
                                      <?php endforeach; ?>
                                    </select>
                                    <label for="<?php echo $option_id; ?>"><?php echo $option_name; ?></label>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div> 
                          <?php endfor; ?>
                        <?php endif; ?>
                      <?php endforeach; ?> 
                      <?php if ( ! $items_with_options ): ?>
                        Vous avez sélectionné un ou plusieurs produits pour ce bloc, mais aucun d'entre eux ne nécessite la sélection d'options.
                      <?php endif; ?>
                    <?php endforeach; ?> 
                    <?php if ( isset( $_POST['user_comment'] ) ): ?>
                      <input type="hidden" value="<?php echo $_POST['user_comment']; ?>" name="user_comment">
                    <?php endif; ?>
                    <div class="row mt-4 text-white">
                      <div class="col-12 col-md text-md-end">
                        <button class="button-back btn btn-danger mb-1">Retour aux produits</button>
                        <button class="btn btn-success mb-1">Confirmer la commande</button>
                      </div>
                    </div>
                  </div>
                </form>
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

  $conn->close();

  require_once 'src/layout/_footer.php'; 

?>