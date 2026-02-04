<?php 

  require_once '../src/sessions_handler.php'; 
  require_once 'src/_navbar.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] ) and $_SESSION['logged_in'];

?>  

<script src="assets/js/index.js"></script>
<div class="container-fluid">
  <?php if( ! $is_admin ): ?>
    <sub class="text-muted">(sign in as admin to display your own competitions)</sub>
  <?php endif ?>
  <form action="#" method="GET" name="select-competition" class="row mt-3 mb-3 justify-content-center text-center">
    <div class="col-11 col-sm-8 col-md-7 col-lg-5 col-xl-4 col-xxl-3 p-3">
      <div class="form-floating my-2">
        <?php if( $is_admin ): ?>
          <select id="competition-select" class="form-select text-center" name="competition_select">
            <?php foreach( $_SESSION['manageable_competitions'] as $id => $competition ): ?>
              <?php if( $competition['announced'] ): ?>
                <option value="<?php echo $id ?>"><?php echo $id ?></option>
              <?php endif ?>
            <?php endforeach ?>
            <option value="Other">Other competition...</option>
          </select>
          <label>Competition ID</label>
        <?php endif ?>
      </div> 
      <input id="other-competition" class="form-control text-center mb-2<?php if( $is_admin ) echo " mt-2" ?>" type="text" name="competition_id" placeholder="MyCompOpen-<?php echo date( 'Y' ) ?>"<?php if ( $is_admin ) echo " style=\"display:none\"" ?>></input>
      <p class="mt-3">
        Select the ID of the competition you want to view.
      </p>
      <p class="mt-4 mb-0"> 
        <?php if( $is_admin ): ?>
          <button class="btn btn-light show-admin">Admin page</button>
        <?php endif ?>
        <button class="btn btn-light show-viewer">Viewer</button>
      </p>
    </div>
  </form>
</div>

<?php require_once '../src/_footer.php' ?>