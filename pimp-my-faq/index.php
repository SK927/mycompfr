<?php 

  require_once 'src/layout/_header.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] );

?>  

<div class="container<?php if ( ! $_SESSION['logged_in'] ) echo "-fluid" ?>">
<?php if ( $_SESSION['logged_in'] ): ?>  
<div class="row mt-4">
  <div class="card py-3">
    <div class="card-body">
      <h3>PARAMÈTRES DU PIMPAGE</h3>
      <form action="src/ajax-format-faq.php" method="POST" name="select-competition">
        <div class="row mt-3">
          <div class="col-md-6 mb-2 px-3">
            <div class="card">
              <div class="card-header bg-red">ID de la compétition</div>
              <div class="card-body">
                <?php if ( $is_admin ): ?>
                  <select id="competition-select" class="form-select text-center" name="competition_select">
                    <?php foreach ( $_SESSION['manageable_competitions'] as $id => $data ): ?>
                      <option value="<?php echo $id ?>"><?php echo $id ?></option>
                    <?php endforeach ?>
                    <option value="Other">Autre compétition...</option>
                  </select>
                <?php endif ?>
                <input id="other-competition" class="form-control text-center<?php if ( $is_admin ) echo " mt-2" ?>" type="text" name="competition_id" placeholder="MyCompOpen<?php echo date( 'Y' ) ?>"<?php if ( $is_admin ) echo " style=\"display:none\"" ?>></input>
              </div>
            </div>
            <div class="card mt-2">
              <div class="card-header bg-blue">Inscriptions sur place</div>
              <div class="card-body">                      
                <input id="accept-ots" class="ms-2" type="checkbox" name="accept_ots" /> 
                <label class="me-2" for="accept-ots">Accepter ?</label>  
                <div id="contact" class="row"> 
                  <div class="col-12 mt-2 fst-italic">Nom référent</div>
                  <div class="col-12">
                    <input class="form-control text-center" type="text" name="ots_contact" value="<?php echo $_SESSION['user_name'] ?>" placeholder="Xzibit">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-2 px-3">
            <div class="card">
              <div class="card-header bg-green">Langues à inclure</div>
              <div class="card-body">                      
                <div class="row">
                  <?php foreach ( glob( 'assets/files/01_base/*.json' ) as $filepath ): ?>
                    <?php $file_exploded = explode( "_", $filepath ) ?>
                    <?php $filename = $file_exploded[3] ?>
                    <div class="col-12">
                      <input id="<?php echo $filename ?>" class="ms-2" type="checkbox" name="<?php echo $filepath ?>" onclick="this.checked =! this.checked" checked /> 
                      <label class="me-2" for="<?php echo $filename ?>"><?php echo $filename ?></label>             
                    </div>
                  <?php endforeach; ?>
                  <?php foreach ( glob( 'assets/files/02_added/*.json' ) as $filepath ): ?>
                    <?php $file_exploded = explode( "_", $filepath ) ?>
                    <?php $filename = $file_exploded[3] ?>
                    <div class="col-12">
                      <input id="<?php echo $filename ?>" class="ms-2" type="checkbox" name="<?php echo $filepath ?>" /> 
                      <label class="me-2" for="<?php echo $filename ?>"><?php echo $filename ?></label>             
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <div class="col-12 mt-4 text-end">
              <button class="btn btn-light">Pimpe ma FAQ ! <img src="assets/img/xzibit.png"></button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div> 
<div id="new-content" class="row">
  <div class="col-md-6 mt-2 ps-0 pe-0 pe-md-2">
    <div class="card">
      <div class="card-body">
        <h3>TEXTE FAQ</h3>
        <textarea id="content-faq" class="form-control mt-3 mb-2" rows="10"></textarea>
        <button class="btn btn-light" onclick="copyToClipboard( 'content-faq' )">Copier le texte</button>
      </div>
    </div>
  </div>
  <div  class="col-md-6 mt-2 pe-0 ps-0 ps-md-2">
    <div class="card">
      <div class="card-body">
        <h3>TEXTE WL</h3>
        <textarea id="content-wl" class="form-control mt-3 mb-2" rows="10"></textarea>
        <button class="btn btn-light" onclick="copyToClipboard( 'content-wl' )">Copier le texte</button>
      </div>
    </div>
  </div>
</div>
<?php else: ?>
  Veuillez vous connecter pour continuer
<?php endif ?>

<?php require_once '../src/layout/_footer.php' ?>
