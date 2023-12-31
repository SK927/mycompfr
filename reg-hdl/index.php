<?php 

  require_once 'src/layout/_header.php';
  
  if ( $_SESSION['logged_in'] )
  {    
    require_once '../src/mysql/mysql-connect.php';
    require_once 'src/custom-functions.php';
    require_once 'src/layout/main-templates.php';

    $user_upcoming_manageable_competitions = from_pretty_json ( decrypt_data( $_SESSION['encrypted_competitions_data'] ) );
    
?>
      <script src="assets/js/index-actions.js"></script> <!-- Custom JS to handle current page actions -->
      <div class="col-12 mt-3 mb-4">
        <div class="card">
          <div class="card-header">REGISTER TO...</div>
          <div id="imported-competitions" class="card-body row">
          </div>
        </div>
      </div>
      <?php if ( $user_upcoming_manageable_competitions AND ! $error ): ?>
        <div class="col-12">
          <div class="card">
            <div class="card-header">MY COMPETITIONS</div>
            <div class="card-body"> 
              <?php foreach ( $user_upcoming_manageable_competitions as $competition ): ?>  
                <div id="<?php echo $competition['id']; ?>_admin" class="row my-2 px-4 py-2">
                  <div class="col-12 col-sm-auto text-left my-2 mt-sm-0">
                    <p class="m-0 p-0 text-break"><?php echo $competition['name']; ?></p>
                    <p class="m-0 p-0 text-muted"><?php echo $competition['start']." to ".$competition['end']; ?></p>
                  </div>
                  <?php if ( $competition['is_imported'] ): ?>
                    <script>displayHandle( '<?php echo addslashes( $competition['id'] ); ?>' );</script>
                  <?php else:?>
                    <script>displayToImport( '<?php echo addslashes( $competition['id'] ); ?>' );</script>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>      
              </div> 
            </div>         
          </div>
        </div> 
      <?php endif; ?>  
      <script>updateCompetitionsList();</script> 
<?php 
      
    require_once '../src/layout/_status-bar.php';
  }
  else
  {
    echo 'Please sign in to continue';
  }

  require_once 'src/layout/_footer.php';
    
?>


