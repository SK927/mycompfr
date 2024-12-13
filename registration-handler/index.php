<?php 

  require_once 'src/layout/_header.php';
  
  if ( $_SESSION['logged_in'] )
  {    
    require_once 'src/custom-functions.php';
    require_once 'src/layout/main-templates.php';
    require_once '../src/mysql/mysql-connect.php'; 

    $_SESSION['manageable_competitions'] = check_imported_competitions( $_SESSION['manageable_competitions'], $conn );

    $conn->close();
    
?>

<script src="assets/js/index-actions.js"></script> <!-- Custom JS to handle current page actions -->
<div class="container-fluid">
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
          <?php foreach ( $_SESSION['manageable_competitions'] as $id => $data ): ?>  
            <div id="<?php echo $id; ?>_admin" class="row my-2 px-4 py-2">
              <div class="col-12 col-sm-auto text-left my-2 mt-sm-0">
                <p class="m-0 p-0 text-break"><?php echo "{$data['name']}"; ?></p>
                <p class="m-0 p-0 text-muted"><?php echo "{$data['start']} to {$data['end']}"; ?></p>
              </div>
              <?php if ( $data['imported_in_handler'] ): ?>
                <script>displayHandle( '<?php echo addslashes( $id ); ?>' );</script>
              <?php else:?>
                <script>displayToImport( '<?php echo addslashes( $id ); ?>' );</script>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>      
          </div> 
        </div>         
      </div>
    </div> 
  <?php endif; ?>  
</div>
<script>updateCompetitionsList();</script> 

<?php 
      
    require_once '../src/layout/_status-bar.php';
  }
  else
  {
    echo 'Please sign in to continue';
  }

  require_once '../src/layout/_footer.php';
    
?>


