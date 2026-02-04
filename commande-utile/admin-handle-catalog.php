<?php 

  require_once '../src/sessions_handler.php';

  $competition_id = $_GET['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {
    require_once 'src/_header.php';
    require_once 'src/_functions.php';
    require_once 'src/templates_handle-catalog.php';

?>  

<script src="assets/js/catalog.js"></script>   
<div class="container"> 
  <div class="row">
    <div class="col-12 col-xl-5 col-xxl-4">
      <div id="catalog-tools" class="mb-3 card">
        <div class="card-body">
          <p>
            Vous pouvez ajouter un catalogue directement depuis un fichier CSV ou à la main. 
            <br/>Si vous souhaitez faire un import depuis un fichier CSV, vous pouvez vous baser sur le fichier d'exemple fourni. 
            <br/>Si vous décidez de le faire à la main, vous devez fournir l'ensemble des éléments pour chaque bloc, chaque produit et chaque options éventuels. Le montant des produits ou options peut être renseigné sans virgule (ex : 1234 pour 12,34€).
          </p>
          <h2 class="card-title m-0">CSV</h2>
          <sub>
            <a href="assets/manuals/Example-Catalog.csv">(Télécharger le fichier d'exemple)</a>
          </sub>
          <form id="form-csv" class="mt-2" action="src/ajax_update-catalog-via-csv?id=<?php echo urlencode( $competition_id ) ?>" method="POST" enctype="multipart/form-data">
            <input id="file-input" class="col-12 mb-2" type="file" name="file" required />
            <button id="load-file" class="btn btn-light" name="upload">Charger le fichier</button>
          </form>
          <h2 class="card-title mt-5">MANUEL</h2>
          <div class="add-block alert alert-secondary p-2 fw-bold text-center">
            AJOUTER UN NOUVEAU BLOC
          </div>
          <div class="col-12 text-end">
            <button id="confirm-button" class="btn btn-success">Sauvegarder</button>
          </div>
        </div>
      </div>
    </div>
    <form id="form-manual" class="col-12 col-xl-7 col-xxl-8" action="src/ajax_update-catalog-manually?id=<?php echo urlencode( $competition_id ) ?>" method="POST">
      <div id="block-list" class="table">
        <script>updateList()</script>
      </div>
    </form>
  </div>
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

    
