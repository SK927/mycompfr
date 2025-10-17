<?php

  require_once 'src/_header.php';
  require_once '../src/_functions-encrypt.php';

  $competition_id = $_GET['id'];

  if ( in_array( $competition_id, $_SESSION['commande_utile']['my_imported_competitions'] ) OR $_SESSION['is_admin'] )
  {
    require_once '../src/mysql_connect.php';
    require_once 'src/_functions-orders.php';
    require_once 'src/templates_place-order.php';
    
    $competition_data = get_competition_data( $competition_id, $conn );

    if ( date( 'Y-m-d' ) <= $competition_data['orders_closing_date'] OR $competition_data['orders_closing_date'] == '0000-00-00' )
    {
      $catalog = from_pretty_json( $competition_data['competition_catalog'] );
      [ $error, $user_order, $admin_comment, $user_comment, $order_total, $has_been_modified ] = get_user_order( $competition_id, $_SESSION['user_id'], $conn );

?> 
   
<?php if ( ! $error ) : ?>
  <script src="assets/js/user-order-actions.js"></script>
  <div class="container text-center">
    <div class="row">
      <h1 class="col-12 text-uppercase">
        <?php echo $competition_data['competition_name'] ?>
      </h1>
    </div>
    <form id="order-form" class="row" action="user-set-options?id=<?php echo urlencode( $_GET['id'] ) ?>" method="POST" name="order_form">
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
                      <img src="assets/img/CU-tee.png" alt="CU-tee"/>
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
                <a class="card-link" href="src/pdf_generate-catalog?id=<?php echo urlencode( $_GET['id'] ) ?>" targer="_blank">Télécharger le catalogue</a>
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
              <div id="comment" class="col-12">
                <a href="#" id="add-comment" class="card-link">(+) Ajouter un commentaire</a>
                <script>
                  if ( sessionStorage.getItem( 'comment' )  )
                  {
                    addComment();
                  }
                  else if ( ! <?php echo var_export( empty( $user_comment ) ) ?> )
                  {
                    sessionStorage.setItem( 'comment', '<?php echo htmlspecialchars( addslashes( $user_comment ) ) ?>' );
                    addComment();
                  }
                </script>
              </div>
            </div>             
          </div>
        </div>
      </div> 
      <div class="col-12 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            MA COMMANDE
          </div>
          <div class="card-body col-12 text-start">
            <?php if ( $catalog ) :?>
              <?php if ( $has_been_modified ): ?>  
                <div class="row">
                  <div class="col-12">
                    <div class="alert alert-warning">
                      <b>ATTENTION :</b> le catalogue a changé et votre commande a pu être impactée (seul le prix peut avoir changé). Veuillez-vous assurer que votre commande est toujours correcte. 
                    </div>
                  </div>
                </div>
              <?php endif ?>
              <div class="row mb-4">
                <div class="col">
                  <div class="row pe-3">
                    <div class="col-auto">
                      <img src="assets/img/CU-tee.png" alt="CU-tee"/>
                    </div>
                    <div class="col speech-bubble p-3">
                      Vous pouvez sélectionner les produits à ajouter à votre commande ci-dessous. Si un ou plusieurs de vos produits possèdent des options, celles-ci seront à renseigner directement dans la suite de la commande. 
                    </div>
                  </div>
                </div>
              </div>
              <?php foreach ( $catalog as $block_key => $block ): ?>
                <div class="row mb-2">
                  <h4 class="col-12"><?php echo $block['name'] ?></h4>
                </div>
                <div class="row mb-2">
                  <?php foreach ( $block['items'] as $item_key => $item ): ?>
                    <?php $item_id = "{$block_key}-{$item_key}" ?>
                    <?php if( $user_order[ $block_key ]['items'][ $item_key ]['qty'] ): ?>
                      <script>
                        if ( ! (sessionStorage.getItem( '<?php echo $item_id; ?>' ) || sessionStorage.getItem( 'isBack' )) )
                        {
                          sessionStorage.setItem( '<?php echo $item_id ?>', <?php echo $user_order[ $block_key ]['items'][ $item_key ]['qty'] ?> ); 
                        }
                      </script>
                    <?php endif ?>
                    <div class="col-12 col-sm-6 col-md-4 col-xl-3 mb-3">
                      <div id="<?php echo $item_id ?>" class="catalog-item card">
                        <h5 class="card-header text-center"><?php echo $item['name'] ?></h5>
                        <div class="card-body pt-0 text-left">
                          <?php if ( $item['image'] != '.' ): ?>
                            <div class="col-12 my-3 text-center">
                              <img src="assets/img/icons/<?php echo $item['image'] ?>" alt="<?php echo $item['name'] ?>" />
                            </div>
                          <?php endif ?>
                          <?php if ( $item['description'] ): ?>
                            <div class="item-description col-12 mt-3 p-0">
                              <?php echo $item['description'] ?>
                            </div>
                          <?php endif ?>
                          <?php if ( $item['options'] ): ?>
                            <div class="item-includes col-12 mt-3 p-0">
                              <b>Options à sélectionner :</b>
                              <?php foreach ( $item['options'] as $option_key => $option ): ?>
                                <?php echo "{$option['name']} ; " ?>
                              <?php endforeach ?>
                            </div>
                          <?php endif ?>
                          <div class="item-price col-12 mt-3 mb-3 p-0 text-muted">
                            (<?php echo number_format( (float) $item['price'], 2, '.', '' ) ?> €)
                          </div>
                        </div>
                        <script>showAdd( '<?php echo $item_id ?>' )</script>
                      </div>
                    </div>
                  <?php endforeach ?>  
                </div>
              <?php endforeach ?>
            <?php else: ?>
              <div class="row mb-2">
                <div class="col-12">Catalogue non configuré. Merci de revenir plus tard.</div>
              </div>
            <?php endif ?>
          </div>
        </div>
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
            <?php if ( $user_order ): ?>
              <button id="delete-button" class="btn btn-danger my-1" name="delete">Supprimer la commande</button>
            <?php endif ?>
            <button id="confirm-button" class="btn btn-success my-1"></button>
          </div>
        </div>
      </div>
    </form>
  </div>
  <script>updateItemsQuantity()</script>
<?php else: ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        Erreur lors du chargement de la commande.
      </div>
    </div>
  </div>
<?php endif ?>

<?php 
      
      $conn->close();
    }
    else
    { 
      $encoded_competition_id = urlencode( $_GET['id'] );
      
      $conn->close();

      header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}/user-view-order?id={$encoded_competition_id}" );
      exit();
    }
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );
    exit();
  }
  
  require_once '../src/_footer.php'; 

?>
