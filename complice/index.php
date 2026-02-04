<?php 

  require_once '../src/sessions_handler.php'; 
  require_once 'src/_header.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] ) and ( $_SESSION['logged_in'] );

?>  

<script src="assets/js/index.js"></script>
<div class="container-fluid">
  <?php if( $is_admin ): ?>  
    <form action="#" method="POST" name="select-competition" class="row mt-4 mb-3 justify-content-center text-center">
      <div class="col-11 col-sm-8 col-md-7 col-lg-5 col-xl-4 col-xxl-3 p-3">
        <div class="form-floating mb-2">
          <select id="competition-select" class="form-select text-center" name="competition_select">
            <?php foreach( $_SESSION['manageable_competitions'] as $id => $competition ): ?>
              <?php if( $competition['announced'] ): ?>
                <option value="<?php echo $id ?>"><?php echo $id ?></option>
              <?php endif ?>
            <?php endforeach ?>
          </select>
          <label>Competition ID</label>
        </div>        
        <p>
          Select the ID of the competition you want to generate competitors list for and click on of the following button.
        </p>
        <p class="mt-4 mb-0"> 
          Generate list as: 
          <button class="generate-pdf btn btn-light" target="_blank">PDF</button>
          <button class="generate-csv btn btn-light" target="_blank">CSV</button>
        </p>
      </div>
    </form>
  <?php else: ?>
    Please sign in as admin to continue
  <?php endif ?>
</div>

<?php require_once '../src/_footer.php' ?>
