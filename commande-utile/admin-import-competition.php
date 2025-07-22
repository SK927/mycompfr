<?php

  require_once 'src/_header.php';
 
  $competition_id = $_GET['id'];

  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) OR $_SESSION['is_admin'] )
  {
    require_once '../src/mysql_connect.php';
    require_once 'src/_functions-master.php';

    $error = import_competition_data( $competition_id, decrypt_data( $_SESSION['user_email'] ), $conn );
    
    if ( ! $error ) 
    {
      $_SESSION['manageable_competitions'][ $competition_id ]['imported_in_cu'] = 1;
    }

?>

<div class="container text-center">
  <div class="row">
    <h1 class="col-12 text-uppercase"><?php echo $_SESSION['manageable_competitions'][ $competition_id ]['name'] ?></h1>
  </div>
  <div class="row">
    <div class="col-12 mt-3">
      <div class="card section">
        <div class="card-header section-title fw-bold">
          CONFIRMATION
        </div>
        <div class="card-body col-12 py-5">
          <div class="row mb-4">
            <div class="col">
              <?php if ( $error ): ?>
                <div class="row pe-3">
                  <div class="col-auto">
                    <img src="assets/img/CU-tee.png" alt="CU-tee"/>
                  </div>
                  <div class="col speech-bubble p-3">
                     <p class="text-danger fw-bold">
                      ERREUR : <?php echo $error ?>
                    </p>
                  </div>
                </div>
              <? else : ?>
                <div class="row pe-3">
                  <div class="col-auto">
                    <img src="assets/img/CU-tee.png" alt="CU-tee"/>
                  </div>
                  <div class="col speech-bubble p-3">
                    <p>
                      La compétition <b><?php echo $_SESSION['manageable_competitions'][ $competition_id ]['name'] ?></b> a bien été importée et c'est maintenant à vous de jouer !<br/>Cliquez sur le bouton ci-dessous pour accéder à la page d'administration de la compétition.
                    </p>
                    <p>
                      Un e-mail de confirmation a &eacute;t&eacute; envoy&eacute; à l'adresse <b><?php echo decrypt_data( $_SESSION['user_email'] ) ?></b>.
                    </p>  
                    <p>
                      Si vous ne recevez pas cet e-mail dans les prochaines heures, merci de nous contacter rapidement.
                    </p> 
                  </div>
                </div>
              <?php endif ?> 
            </div>
          </div>
          <a class="btn btn-light" href="admin-handle-competition?id=<?php echo $competition_id ?>">Administrer ma compétition</a>
        </div>
      </div>   
    </div>
  </div>
</div>

<?php 
    
    $conn->close();
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );
    exit();
  }

  require_once '../src/_footer.php'; 

?>