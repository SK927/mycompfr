<?php

  require_once '../src/sessions_handler.php';

  $competition_id = $_GET['id'];
  $is_manageable = isset( $_SESSION['manageable_competitions'][ $competition_id ] );

  if( $is_manageable or $_SESSION['is_admin'] )
  {
    require_once 'src/_header.php';
    require_once 'src/_functions.php';

    mysqli_open( $mysqli );
    $user_email = decrypt_data( $_SESSION['user_email'] );
    $admin_emails = get_administrators_emails( $mysqli );
    $error = insert_competition_into_db( $competition_id, $user_email, $mysqli );    
    $mysqli->close();
    
    if( ! $error ) 
    {
      $error = send_creation_competition_cu( $competition_id, $user_email, $admin_emails );
      $_SESSION['manageable_competitions'][ $competition_id ]['imported_in_cu'] = 1;
    }

?>

<div class="container-md"> 
  <div class="card mb-3">
    <div class="card-body"> 
      <div class="card-body col-12 text-center">
        <?php if( $error ): ?>
          <p class="text-danger fw-bold">
            ERREUR : <?php echo $error ?>
          </p>
          <a class="btn btn-light mt-3" href="index">Retour à l'accueil</a>
        <?php else: ?>
          <p>
            La compétition <b><?php echo $_SESSION['manageable_competitions'][ $competition_id ]['name'] ?></b> a bien été importée et c'est maintenant à vous de jouer !<br/>Cliquez sur le bouton ci-dessous pour accéder à la page d'administration de la compétition.
          </p>
          <p>
            Un e-mail de confirmation a &eacute;t&eacute; envoy&eacute; à l'adresse <b><?php echo decrypt_data( $_SESSION['user_email'] ) ?></b>.
          </p>  
          <p>
            Si vous ne recevez pas cet e-mail dans les prochaines heures, merci de nous contacter rapidement.
          </p>
          <a class="btn btn-light mt-3" href="admin-handle-competition?id=<?php echo $competition_id ?>">Administrer la compétition</a> 
        <?php endif ?>
      </div>
    </div>
  </div>
</div>

<?php 
    
  }
  else
  {
    header( 'Location: index.php' );
    exit();
  }

  require_once '../src/_footer.php'; 

?>