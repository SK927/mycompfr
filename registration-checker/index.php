<?php 

  require_once 'src/_header.php';
  require_once 'src/_functions.php';
  require_once 'src/templates_main.php';
  require_once '../src/mysql_connect.php'; 

  $_SESSION['manageable_competitions']= check_imported_competitions( $_SESSION['manageable_competitions'], $conn );

  $conn->close();

?>

<script src="assets/js/index-actions.js"></script>
<div class="container-fluid">
  <?php if ( $_SESSION['logged_in'] ): ?>
    <div class="col-12 mt-3 mb-4">
      <div class="card">
        <div class="card-header">I WILL ATTEND...</div>
        <div id="imported-competitions" class="card-body row">
        </div>
      </div>
    </div>
    <?php if ( $_SESSION['manageable_competitions'] AND ! $error ): ?>
      <div class="col-12">
        <div class="card">
          <div class="card-header">MY COMPETITIONS</div>
          <div class="card-body"> 
            <?php foreach ( $_SESSION['manageable_competitions'] as $id => $competition ): ?>  
              <?php if ( $competition['announced'] ): ?>  
                <div id="<?php echo $id ?>_admin" class="row my-2 py-2 px-4">
                  <div class="col-12 col-sm-auto my-2 mt-sm-0 text-start">
                    <p class="m-0 p-0 text-break"><?php echo $competition['name'] ?></p>
                    <p class="m-0 p-0 text-muted"><?php echo "{$competition['start']} to {$competition['end']}" ?></p>
                  </div>
                  <?php if ( $competition['imported_in_checker'] ): ?>
                    <script>displayImported( '<?php echo addslashes( $id ) ?>' )</script>
                  <?php else:?>
                    <script>displayToImport( '<?php echo addslashes( $id ) ?>' )</script>
                  <?php endif ?>
                </div>
              <?php endif ?>
            <?php endforeach ?>       
          </div>         
        </div>
      </div> 
    <?php endif ?>
  <?php else: ?>
    Please sign in to continue
  <?php endif ?>
</div>
<script>updateCompetitionsList();</script> 

<?php 

  require_once '../src/_status-bar.php';
  require_once '../src/_footer.php';
    
?>



