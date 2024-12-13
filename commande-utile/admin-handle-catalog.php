<?php

  require_once 'src/layout/_header.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) or $_SESSION['is_admin'] ) )
  {
    require_once '../src/mysql/mysql-connect.php';
    require_once '../src/functions/generic-functions.php';
    require_once 'src/functions/competition-data-functions.php';
    require_once 'src/layout/handle-catalog-templates.php';

    $competition_data = get_competition_data( $competition_id, $conn );
    $competition_catalog_blocks = from_pretty_json( $competition_data['competition_catalog'] );

?>

<script src="assets/js/admin-catalog-actions.js"></script> <!-- Custom JS to handle current page actions -->
<div class="container text-center">
  <div class="row">
    <h1 class="col-12 text-uppercase"><?php echo $competition_data['competition_name'] ?></h1>
  </div>
  <div class="row">
    <div class="col-12 mt-3" action="src/admin/ajax-update-catalog" method="POST">
      <div class="card section">
        <div class="card-header section-title fw-bold">
          CATALOGUE
        </div>
        <div class="card-body col-12">
          <div class="row mb-5">
            <div class="col text-start">
              Vous pouvez ajouter un catalogue directement depuis un fichier CSV ou à la main. Si vous décidez de le faire à la main, le montant de chaque produit peut être rentré sans virgule (","). Les différents options doivent, elles, être séparées par des points-virgules (";").
            </div>
          </div>
          <form id="form-csv" action="src/admin/ajax-update-catalog-via-csv?id=<?php echo urlencode( $_GET['id'] ) ?>" method="POST" enctype="multipart/form-data">
            <input id="file-input" type="file" name="file" required />
            <button id="load-file" class="btn btn-light" name="upload">Charger le fichier</button>
          </form>
          <sub>
            <a href="assets/Example-Catalog.csv">(Télécharger le fichier d'exemple)</a>
          </sub>
        </div>
        <div class="card-body col-12">
          <div class="add-block alert alert-secondary p-2 fw-bold">
            AJOUTER UN NOUVEAU BLOC
          </div>
          <form id="form-manual" class="col-12" action="src/admin/ajax-update-catalog-manually?id=<?php echo urlencode( $_GET['id'] ) ?>" method="POST">
            <div id="block-list">
              <script>
                <?php foreach ( $competition_catalog_blocks as $block_name => $block_items ): ?>
                  createBlock( <?php echo json_encode( $block_name ) . ', ' . json_encode( $block_name ) ?> );
                  
                  <?php foreach ( $block_items as $item_id => $item_value ): ?>
                    createItem( <?php echo json_encode( 'item-list' . $block_name ) . ', ' . json_encode( $item_id ) . ', ' . json_encode( $item_value['item_name'] ) . ", '" . $item_value['item_price'] . "', " . json_encode( $item_value['item_descr'] ) . ", " . json_encode( $item_value['item_image'] ) ?> );  

                    <?php if ( ! empty( $item_value['options'] ) ): ?>
                      <?php foreach ( $item_value['options'] as $option_name => $option_values ): ?>
                        createOption( <?php echo json_encode( 'row' . htmlspecialchars( addslashes( $item_id ) ) ) ?>, getRandomInt(), <?php echo json_encode( htmlspecialchars( addslashes( $option_name ) ) ) . ", " . json_encode( $option_values ) ?> );
                      <?php endforeach ?>
                    <?php endif ?>
                  <?php endforeach ?>       
                <?php endforeach ?>
              </script>
            </div>
            <div class="handle-catalog row fixed-bottom px-4 py-3">
              <div class="col-12 col-md">
                <button id="confirm-button" class="btn btn-success">Sauvegarder</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php 

    $conn->close();

    require_once '../src/layout/_status-bar.php';
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}" );
    exit();
  }

  require_once '../src/layout/_footer.php'; 

?>  