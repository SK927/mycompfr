<?php 
  
  require_once '../src/sessions_handler.php'; 
  require_once 'src/_header.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] );

?>  

<script src="assets/js/index.js"></script>
<div class="container-fluid">
  <?php if( ! $is_admin ): ?>
    <sub class="text-muted">(sign in as admin to display your own competitions)</sub>
  <?php endif ?>
  <form action="#" method="GET" name="select-competition" class="row mt-3 mb-3 justify-content-center text-center">
    <div class="col-11 col-sm-8 col-md-7 col-lg-5 col-xl-4 col-xxl-3 p-3">
      <?php for ( $i = 1 ; $i <= 2 ; $i++): ?>
        <div class="form-floating my-2">
          <?php if( $is_admin ): ?>
            <select id="competition-select-<?php echo $i ?>" class="form-select text-center" name="competition_select_<?php echo $i ?>">
              <?php $o = 1 ?>
              <?php foreach ( $_SESSION['manageable_competitions'] as $id => $competition ): ?>
                <?php if( $competition['announced'] ): ?>
                  <option value="<?php echo $id ?>"<?php if( $i == $o ) echo ' selected' ?>><?php echo $id ?></option>
                  <?php $o++ ?>
                <?php endif ?>
              <?php endforeach ?>
              <option value="Other">Other competition...</option>
            </select>
            <label>Competition ID#<?php echo $i ?></label>
          <?php endif ?>
        </div> 
        <input id="other-competition-<?php echo $i ?>" class="form-control text-center mb-2<?php if( $is_admin ) echo " mt-2" ?>" type="text" name="competition_id_<?php echo $i ?>" placeholder="MyCompOpen_<?php echo $i ?>-<?php echo date( 'Y' ) ?>"<?php if( $is_admin ) echo " style=\"display:none\"" ?>></input>
      <?php endfor ?>
      <p class="mt-3">
        Select the ID of the competitions to compare competitors lists for.
      </p>
      <p class="mt-4 mb-0"> 
        <button class="btn btn-light">Show me the list</button>
      </p>
    </div>
  </form>
</div>

<?php require_once '../src/_footer.php' ?>
