<?php 

  require_once '../src/sessions_handler.php';


  $competition_id = $_GET['id'];
  $is_open = (($_SESSION['cu']['imported'][ $competition_id ]['orders_status_class'] == 'open') OR isset( $_SESSION['manageable_competitions'][ $competition_id ] ));
  $is_imported = ($competition_id == $_SESSION['cu']['imported'][ $competition_id ][ 'competition_id' ]);

  if( $_SESSION['logged_in'] and (($is_open AND $is_imported) OR $_SESSION['is_admin']) )
  {
    require_once 'src/_header.php';
    require_once 'src/_functions.php';

    mysqli_open( $mysqli );
    $competition = get_competition_data( $competition_id, $mysqli );
    $order_id = hash_data( $competition_id, $_SESSION['user_id'] );

    if( $is_deletion = (bool) $_GET['delete'] )
    {
      [ $error, $order_info ] = delete_order( $order_id, $mysqli );

      if( ! $error )
      {
        $error = send_order_cancellation( $competition, $order_info );
        $confirmation_status = 'supprimée';
      }
    }
    else
    {
      $error = save_order( $competition_id, $_SESSION, $_POST, $mysqli );

      if( ! $error )
      {
        $order = get_order( $order_id, $mysqli );
        $error = send_order_confirmation( $competition, $order );
        $confirmation_status = 'enregistrée';
      }
    }
    $mysqli->close();  

?>  

<div class="container-md"> 
  <div class="card mb-3">
    <div class="card-body"> 
      <div class="card-body col-12 text-center">
        <?php if( $error ): ?>
          <p class="text-danger fw-bold">
            ERREUR : <?php echo $error ?>
          </p>
        <?php else: ?>
          <p>
            La commande au nom de <b><?php echo $_SESSION['user_name'] ?></b> a bien été <?php echo $confirmation_status ?>.
          </p>
          <p>
            Un e-mail de confirmation a &eacute;t&eacute; envoy&eacute; à l'adresse <b><?php echo decrypt_data( $_SESSION['user_email'] ) ?></b>.
          </p>  
          <p>
            Si vous ne recevez pas cet e-mail dans les prochaines heures, merci de nous contacter rapidement.
          </p> 
        <?php endif ?>
        <a class="btn btn-light mt-3" href="index">Retour à l'accueil</a> 
      </div>
    </div>
  </div>
</div>
<script>sessionStorage.clear()</script>

<?php 

  }
  else
  {
    header( 'Location: index.php' );
    exit();
  }

  require_once '../src/_footer.php'

?> 

    
