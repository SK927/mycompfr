<?php 

  require_once 'src/_header.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] ) && ( $_SESSION['logged_in'] );

?>  

<script src="assets/js/index-actions.js"></script>
<div class="container-fluid">
  <?php if ( $is_admin ): ?>  
    <div class="row mt-4 justify-content-center text-center">
      <div class='col-12 col-md-9 col-lg-6'>
        <h3>ID DE LA COMPETITION</h3>
        <div class="card py-3">
          <div class="card-body text-center">
            <form action="#" method="POST" name="select-competition">
              <div class="row justify-content-center">
                <div class="col-md-8 mb-2 px-3">
                  <?php if ( $is_admin ): ?>
                    <select id="competition-select" class="form-select text-center" name="competition_select">
                      <?php foreach ( $_SESSION['manageable_competitions'] as $id => $competition ): ?>
                        <?php if ( $competition['announced'] ): ?>
                          <option value="<?php echo $id ?>"><?php echo $id ?></option>
                        <?php endif ?>
                      <?php endforeach ?>
                    </select>
                  <?php endif ?>
                </div>
                <div class="col-12 mt-3">Générer : 
                  <button class="generate-pdf btn btn-light">PDF</button>
                  <button class="generate-csv btn btn-light">CSV</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    Connectez vous en tant qu'admin pour continuer
  <?php endif ?>
</div>

<?php require_once '../src/_footer.php' ?>
