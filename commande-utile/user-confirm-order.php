<?php

  require_once 'src/_header.php';
 
  $competition_id = $_GET['id'];

  if ( in_array( $competition_id, $_SESSION['commande_utile']['my_imported_competitions'] ) OR $_SESSION['is_admin'] )
  {
    require_once '../src/mysql_connect.php';
    require_once 'src/_functions-orders.php';

    $competition_data = get_competition_data( $competition_id, $conn );
    $user_order_id = hash_data( $_SESSION['user_id'], $competition_id );
    [ $error, $is_edit ] = get_user_order( $competition_id, $_SESSION['user_id'], $conn );

    if ( ! $error )
    {
      $is_deletion = (bool) $_GET['delete'];

      if ( $is_deletion )
      {
        $error = delete_user_order( $competition_id, $user_order_id, $conn );
      }
      else
      {
        $error = save_user_order( $competition_id, $user_order_id, $_SESSION, $_POST, $is_edit, $conn );
      }
    }
    
?>

<div class="container text-center">
  <div class="row">
    <h1 class="col-12 text-uppercase"><?php echo $competition_data['competition_name'] ?></h1>
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
                    <?php $order_status_text = $is_edit ? 'modifiée' : ' enregistrée' ?>
                    <p>
                      La commande n° <b><?php echo $user_order_id; ?></b> au nom de <b><?php echo $_SESSION['user_name'] ?></b> a bien été <?php echo $is_deletion ? 'supprimée' : $order_status_text ?>.
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
          <a class="btn btn-light" href="index">Retour à l'accueil</a>
        </div>
      </div>   
    </div>
  </div>
</div>
<script>sessionStorage.clear()</script>

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