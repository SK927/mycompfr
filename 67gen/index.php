<?php 

  require_once 'src/_header.php';
  require_once 'src/_functions.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] ) and $_SESSION['logged_in'];
  $events = get_events();

?>  

<script src="assets/js/index.js"></script>
<div class="container-fluid">
  <?php if( ! $is_admin ): ?>
    <sub>(sign in as admin to display your own competitions)</sub>
  <?php endif?>
  <div class="row mt-4 justify-content-center text-center">
    <div class='col-12 col-md-9 col-lg-6 col-xl-5 col-xxl-4'>
      <h3>PARAMETERS</h3>
      <div class="card py-3">
        <div class="card-body text-center">
          <form action="src/pdf_generate-scorecards.php" method="POST" name="select-competition" target="_blank">
            <div class="row justify-content-center">
              <div class="col-12 mb-3 px-3">
                <?php if( $is_admin ): ?>
                  <select id="competition-select" class="form-select text-center" name="competition_select">
                    <?php foreach( $_SESSION['manageable_competitions'] as $id => $competition ): ?>
                      <?php  if( $competition['announced'] ): ?>
                        <option value="<?php echo $id ?>"><?php echo $id ?></option>
                      <?php endif?>
                    <?php endforeach?>
                    <option value="Other">Other competition...</option>
                  </select>
                <?php endif?>
                <input id="other-competition" class="form-control text-center<?php if( $is_admin ) echo " mt-2" ?>" type="text" name="competition_id" placeholder="MyCompOpen<?php echo date( 'Y' ) ?>"<?php if( $is_admin ) echo ' style="display:none"' ?>></input>
              </div>

                  <?php foreach( $events as $id => $event ): ?>
                    <div class="col-auto mb-2">
                      <input id="<?php echo $id ?>" class="form-check-input" type="checkbox" name="event_select[]" value="<?php echo $id ?>">
                      <label class="form-check-label ps-1" for="<?php echo $id ?>"><?php echo $event['display'] ?></label>
                    </div>
                  <?php endforeach?>
            
              <div class="col-12 mt-3">
                <button id="submit-button" class="btn btn-light" disabled>Generate scorecards</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../src/_footer.php' ?>
