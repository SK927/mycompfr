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
    <div class='col-12 col-md-9 col-lg-6'>
      <h3>PARAMETERS</h3>
      <div class="card py-3">
        <div class="card-body text-center">
          <form action="src/pdf_generate-scorecards.php" method="POST" name="select-competition">
            <div class="row justify-content-center">
              <div class="col-md-8 mb-2 px-3">
                <?php if ( $is_admin ): ?>
                  <select id="competition-select" class="form-select text-center" name="competition_select">
                    <?php foreach ( $_SESSION['manageable_competitions'] as $id => $competition ): ?>
                      <?php  if ( $competition['announced'] ): ?>
                        <option value="<?php echo $id ?>"><?php echo $id ?></option>
                      <?php endif ?>
                    <?php endforeach ?>
                    <option value="Other">Other competition...</option>
                  </select>
                <?php endif ?>
                <input id="other-competition" class="form-control text-center<?php if ( $is_admin ) echo " mt-2" ?>" type="text" name="competition_id" placeholder="MyCompOpen<?php echo date( 'Y' ) ?>"<?php if ( $is_admin ) echo ' style="display:none"' ?>></input>
              </div>
              <div class="w-100"></div>
              <div class="col-auto mt-2">
              <input id="event1" class="form-check-input" type="radio" name="event_select" value="6x7" checked>
                <label class="form-check-label" for="event1">6x6 and 7x7</label>
              </div>
              <div class="w-100"></div>
              <div class="col-auto mb-4">
                <input id="event2" class="form-check-input" type="radio" name="event_select" value="bld">
                <label class="form-check-label" for="event2">4x4 and 5x5 blindfolded</label>
              </div>
              <div class="col-12 mt-3">
                <button class="btn btn-light">Generate scorecards</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../src/_footer.php' ?>
