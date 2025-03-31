<?php 

  require_once 'src/_header.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] ) && ( $_SESSION['logged_in'] );

?>  
  
<div class="container-fluid">
  <?php if ( $is_admin ): ?>  
    <div class="row mt-4 justify-content-center text-center">
      <div class='col-12 col-md-9 col-lg-6'>
        <h3>SELECT COMPETITION ID</h3>
        <div class="card py-3">
          <div class="card-body text-center">
            <form action="display-noshows.php" method="POST" name="select-competition">
              <div class="row justify-content-center">
                <div class="col-md-8 mb-2 px-3">
                  <select id="competition-select" class="form-select text-center" name="competition_select">
                    <?php foreach ( $_SESSION['manageable_competitions'] as $id => $competition ): ?>
                      <?php if ( $competition['announced'] ): ?>
                        <option value="<?php echo $id ?>"><?php echo $id ?></option>
                      <?php endif ?>
                    <?php endforeach ?>
                  </select>
                </div>
                <div class="col-12 mt-3">
                  <button class="btn btn-light">Show me no-shows</button>
                </div>
                <div class="col-12 mt-5">
                  <u>Nota:</u> To detect no-shows, results of the competition must be posted and no-shows must still be on the competitors list.
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    Please sign in as admin to continue
  <?php endif ?>
</div>

<?php require_once '../src/_footer.php' ?>
