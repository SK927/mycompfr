<?php 

  require_once 'src/_header.php';
  require_once 'src/_functions.php';
  require_once 'src/templates_main.php';
  require_once '../src/mysql_connect.php'; 

  $_SESSION['manageable_competitions'] = check_imported_competitions( $_SESSION['manageable_competitions'], $conn );

  $conn->close();

?>

<script src="assets/js/index-actions.js"></script>
<div class="container-fluid">
  <?php if ( $_SESSION['logged_in'] ): ?>
    <div class="col-12 mt-3 mb-4">
      <div class="card">
        <div class="card-header">REGISTER TO...</div>
        <div id="imported-competitions" class="card-body row">
        </div>
      </div>
    </div>
    <?php if ( $_SESSION['manageable_competitions'] ): ?>
      <div class="col-12">
        <div class="card">
          <div class="card-header">MY COMPETITIONS</div>
          <div class="card-body"> 
            <sub class="row ps-3 text-muted mb-5">
              (Only competitions with a start date set to today or in the past can be imported!)
            </sub>
            <?php foreach ( $_SESSION['manageable_competitions'] as $id => $competition ): ?>  
                <?php if ( $competition['announced'] && ( $competition['start'] <= date( 'Y-m-d' ) ) ): ?>  
                <div id="<?php echo $id ?>_admin" class="row my-2 px-4 py-2">
                  <div class="col-12 col-sm-auto text-left my-2 mt-sm-0">
                    <p class="m-0 p-0 text-break"><?php echo "{$competition['name']}" ?></p>
                    <p class="m-0 p-0 text-muted"><?php echo "{$competition['start']} to {$competition['end']}" ?></p>
                  </div>
                  <?php if ( $competition['imported_in_handler'] ): ?>
                    <script>displayHandle( '<?php echo addslashes( $id ) ?>' )</script>
                  <?php else:?>
                    <script>displayToImport( '<?php echo addslashes( $id ) ?>' )</script>
                  <?php endif; ?>
                </div>
              <?php endif ?>      
            <?php endforeach ?>      
            </div> 
          </div>         
        </div>
      </div> 
    <?php endif ?>  
  <?php else: ?>
    Please sign in to continue
  <?php endif ?> 
</div>
<script>updateCompetitionsList()</script> 

<?php 

  require_once '../src/_status-bar.php';
  require_once '../src/_footer.php';
    
?>


