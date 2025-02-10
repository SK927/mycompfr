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
              <div class="row pe-3">
                <div class="col-auto">
                  <img src="assets/img/CUtie.png" alt="CUtie"/>
                </div>
                <div class="col speech-bubble p-3">
                  Vous pouvez ajouter un catalogue directement depuis un fichier CSV ou à la main. Si vous souhaitez faire un import depuis un fichier CSV, vous pouvez vous baser sur le fichier d'exemple fourni.Si vous décidez de le faire à la main, vous devez fournir l'ensemble des éléments pour chaque bloc, chaque produit et chaque options éventuelle. Le montant des produits ou options peut être renseigné sans virgule (ex : 1234 pour 12,34€).
                </div>
              </div>
            </div>
          </div>
          <form id="form-csv" action="src/admin/ajax-update-catalog-via-csv?id=<?php echo urlencode( $_GET['id'] ) ?>" method="POST" enctype="multipart/form-data">
            <input id="file-input" class="mb-2" type="file" name="file" required />
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
                updateList();
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
    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );
    exit();
  }

  require_once '../src/layout/_footer.php'; 

?>  