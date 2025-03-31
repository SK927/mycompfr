<?php 

  require_once 'src/_header.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] ) && $_SESSION['logged_in'];

?>  

<script src="assets/js/index-actions.js"></script>
<div class="container-fluid">
  <?php if ( ! $is_admin ): ?>
    <sub>(sign in as admin to display your own competitions)</sub>
  <?php endif ?>
  <div class="row mt-4 justify-content-center text-center">
    <div class='col-12 col-md-9'>
      <h3>SELECT COMPETITIONS ID</h3>
      <div class="card py-3">
        <div class="card-body text-center">
          <form action="" method="GET" name="select-competition">
            <div class="row justify-content-center">
              <?php for ( $i = 1 ; $i <= 2 ; $i++): ?>
                <div class="col-md-6 mb-4 px-3">
                  <?php if ( $is_admin ): ?>
                    <div class="row">
                      <div class="col">
                        <select id="competition-select-<?php echo $i ?>" class="form-select text-center" name="competition_select_<?php echo $i ?>">
                          <?php $o = 1 ?>
                          <?php foreach ( $_SESSION['manageable_competitions'] as $id => $competition ): ?>
                            <?php if ( $competition['announced'] ): ?>
                              <option value="<?php echo $id ?>"<?php if ( $i == $o ) echo ' selected' ?>><?php echo $id ?></option>
                              <?php $o++ ?>
                            <?php endif ?>
                          <?php endforeach ?>
                          <option value="Other">Other competition...</option>
                        </select>
                      </div>
                    </div>
                  <?php endif ?>
                  <input id="other-competition-<?php echo $i ?>" class="form-control text-center<?php if ( $is_admin ) echo " mt-2" ?>" type="text" name="competition_id_<?php echo $i ?>" placeholder="MyCompOpen_<?php echo $i ?>-<?php echo date( 'Y' ) ?>"<?php if ( $is_admin ) echo " style=\"display:none\"" ?>></input>
              </div> 
              <?php endfor ?>
              <div class="col-md-6 mt-2 mt-md-1 ">
                <button class="btn btn-light">Show me the lists</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../src/_footer.php' ?>
