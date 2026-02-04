<?php 

  require_once '../src/sessions_handler.php';
  require_once 'src/_header.php';

  $is_admin = isset( $_SESSION['manageable_competitions'] ) && $_SESSION['logged_in'];

?>  

<script src="assets/js/index.js"></script>
<div class="container-fluid">
  <form action="src/ajax-format-text.php" method="POST" name="select-competition" class="row">
    <?php if( ! $is_admin ): ?>
      <sub class="text-muted">(connecte-toi en tant qu'organisateur·rice pour afficher tes propres compétitions)</sub>
    <?php endif ?>
    <div class="col-12 col-xl-10 col-xxl-8 m-auto p-3 text-center">
      <h3>PARAMÈTRES DU PIMPAGE</h3>
      <div class="row text-start mt-3">
        <div class="col-md-6 mb-2 px-3">
          <div class="card">
            <div class="card-header bg-red">ID de la compétition</div>
            <div class="card-body">
              <?php if( $is_admin ): ?>
                <select id="competition-select" class="form-select text-center" name="competition_select">
                  <?php foreach( $_SESSION['manageable_competitions'] as $id => $data ): ?>
                    <option value="<?php echo $id ?>"><?php echo $id ?></option>
                  <?php endforeach ?>
                  <option value="Other">Autre compétition...</option>
                </select>
              <?php endif ?>
              <input id="other-competition" class="form-control text-center<?php if( $is_admin ) echo " mt-2" ?>" type="text" name="competition_id" placeholder="MyCompOpen<?php echo date( 'Y' ) ?>"<?php if( $is_admin ) echo " style=\"display:none\"" ?>></input>
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
                <?php foreach( glob( 'assets/files/01_base/*.yaml' ) as $filepath ): ?>
                  <?php $file_exploded = explode( "_", $filepath ) ?>
                  <?php $filename = $file_exploded[3] ?>
                  <div class="col-12">
                    <input id="<?php echo $filename ?>" class="ms-2" type="checkbox" name="<?php echo $filepath ?>" onclick="this.checked =! this.checked" checked /> 
                    <label class="me-2" for="<?php echo $filename ?>"><?php echo $filename ?></label>   
                  </div>
                <?php endforeach; ?>
                <?php foreach( glob( 'assets/files/02_added/*.yaml' ) as $filepath ): ?>
                  <?php $file_exploded = explode( "_", $filepath ) ?>
                  <?php $filename = $file_exploded[3] ?>
                  <div class="col-12">
                    <input id="<?php echo $filename ?>" class="ms-2" type="checkbox" name="<?php echo $filepath ?>" /> 
                    <label class="me-2" for="<?php echo $filename ?>"><?php echo $filename ?></label>   
                  </div>
                <?php endforeach ?>
              </div>
            </div>
          </div>
          <div class="col-12 mt-4 text-end">
            <button class="btn btn-light">Pimpe ma FAQ ! <img src="assets/img/xzibit.png"></button>
          </div>
        </div>
      </div>
    </div>
  </form>
  <div id="new-content" class="row mt-4">
    <div class="col-12 col-xl-10 col-xxl-8 m-auto">
      <div class="row">
        <div class="col-md px-3">
          <h3>FAQ</h3>
          <textarea id="content-faq" class="form-control mt-3 mb-2" rows="10"></textarea>
          <button class="btn btn-light mb-4" onclick="copyToClipboard( 'content-faq' )">Copier le texte</button>
        </div>
        <div class="col-md px-3">
          <h3>INSCRIPTIONS</h3>
          <textarea id="content-reg" class="form-control mt-3 mb-2" rows="10"></textarea>
          <button class="btn btn-light" onclick="copyToClipboard( 'content-reg' )">Copier le texte</button>
        </div>
      </div> 
    </div> 
  </div> 
</div>

<?php require_once '../src/_footer.php' ?>
