<?php 

  require_once '../src/sessions_handler.php';
  require_once 'src/_header.php';
  require_once 'src/_functions.php';
  require_once '../src/mysqli.php'; 

  mysqli_open( $mysqli );
  $all_competitions = check_imported_competitions( $_SESSION['manageable_competitions'], $_SESSION, $mysqli );
  $mysqli->close();

  require_once 'src/templates_main.php';

?>

<script src="assets/js/index.js"></script>
<?php if( $_SESSION['logged_in'] ): ?>
  <div class="container">
    <div class="card mb-3">
      <div class="card-body">
        <div class="row">
          <h2 class="mb-3 pb-3 border-bottom">JE PARTICIPE À...</h2>
        </div>
        <div id="imported-competitions" class=" row">
        </div>
      </div>
    </div>
    <?php if( $all_competitions and ! $error ): ?>
      <div class="card mb-3">
        <div class="card-body">
          <div class="row">
          <h2 class="mb-3 pb-3 border-bottom">MES COMPÉTITIONS </h2>
        </div>
        <?php foreach( $all_competitions as $id => $competition ): ?>  
          <?php if( $competition['announced'] ): ?>  
            <div id="<?php echo $id ?>_admin" class="row my-2 py-2 px-4">
              <div class="col-12 col-sm-auto my-2 mt-sm-0 text-start">
                <p class="m-0 p-0 text-break"><?php echo $competition['name'] ?></p>
                <p class="m-0 p-0 text-muted">Du <?php echo "{$competition['start']} au {$competition['end']}" ?></p>
              </div>
              <?php if( $competition['imported_in_checker'] ): ?>
                <script>displayImported( '<?php echo addslashes( $id ) ?>' )</script>
              <?php else:?>
                <script>displayToImport( '<?php echo addslashes( $id ) ?>' )</script>
              <?php endif ?>
            </div>
          <?php endif ?>
        <?php endforeach ?>       
      </div> 
    <?php endif ?>
<?php else: ?>
  <div class="container-fluid">
    Connectez-vous pour continuer
<?php endif ?>
</div>
<script>updateCompetitionsList();</script> 

<?php 

  require_once '../src/_status-bar.php';
  require_once '../src/_footer.php';
    
?>



