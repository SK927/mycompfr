<?php 

  require_once 'src/layout/_header.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] );

?>  
  
<div class="container<?php if ( ! $_SESSION['logged_in'] ) echo "-fluid" ?>">
  <?php if ( $_SESSION['logged_in'] ): ?>  
    <div class="row mt-4 justify-content-center text-center">
      <div class='col-12 col-md-9 col-lg-6'>
        <h3>SELECT COMPETITION ID</h3>
        <div class="card py-3">
          <div class="card-body text-center">
            <form action="display-noshows.php" method="POST" name="select-competition">
              <div class="row justify-content-center">
                <div class="col-md-8 mb-2 px-3">
                  <?php if ( $is_admin ): ?>
                    <select id="competition-select" class="form-select text-center" name="competition_select">
                      <?php foreach ( $_SESSION['manageable_competitions'] as $id => $data ): ?>
                        <option value="<?php echo $id ?>"><?php echo $id ?></option>
                      <?php endforeach ?>
                      <option value="Other">Other competition...</option>
                    </select>
                  <?php endif ?>
                  <input id="other-competition" class="form-control text-center<?php if ( $is_admin ) echo " mt-2" ?>" type="text" name="competition_id" placeholder="MyCompOpen<?php echo date( 'Y' ) ?>"<?php if ( $is_admin ) echo " style=\"display:none\"" ?>></input>
                </div>
                <div class="col-12 mt-3">
                  <button class="btn btn-light">Show me no-shows</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    Please sign in to continue
  <?php endif ?>
</div>

<?php require_once '../src/layout/_footer.php' ?>
