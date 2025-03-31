<?php 

  require_once 'src/_navbar.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] ) AND $_SESSION['logged_in'];

?>  

<script src="assets/js/index-actions.js"></script>
<div class="container-fluid">
  <div class="row mt-4 justify-content-center text-center">
    <div class='col-12 col-md-9 col-lg-6'>
      <h3>SELECT COMPETITION ID</h3>
      <div class="card py-3">
        <div class="card-body text-center">
          <form id="viewer-form" action="" method="POST" name="select-competition">
            <div class="row justify-content-center">
              <div class="col-md-8 mb-2 px-3">
                <?php if ( $is_admin ): ?>
                  <select id="competition-select" class="form-select text-center" name="competition_select">
                    <?php foreach ( $_SESSION['manageable_competitions'] as $id => $competition ): ?>
                      <?php if ( $competition['announced'] ): ?>
                        <option value="<?php echo $id ?>"><?php echo $id ?></option>
                      <?php endif ?>
                    <?php endforeach ?>
                    <option value="Other">Other competition...</option>
                  </select>
                <?php endif ?>
                <input id="other-competition" class="form-control text-center<?php if ( $is_admin ) echo " mt-2" ?>" type="text" name="competition_id" placeholder="MyCompOpen<?php echo date( 'Y' ) ?>"<?php if ( $is_admin ) echo ' style="display:none"' ?>></input>
              </div>
              <div class="col-12 mt-3">
                <?php if ( $is_admin ): ?>
                  <button class="btn btn-light show-admin">Admin page</button>
                <?php endif ?>
                <button class="btn btn-light show-viewer">Viewer</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../src/_footer.php' ?>
