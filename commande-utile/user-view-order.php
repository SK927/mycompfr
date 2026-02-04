<?php

  require_once '../src/sessions_handler.php';

  $competition_id = $_GET['id'];

  if( $_SESSION['logged_in'] )
  {

    require_once 'src/_header.php';
    require_once 'src/_functions.php';

    mysqli_open( $mysqli );
    $competition = get_competition_data( $competition_id, $mysqli );
    $order = get_order( hash_data( $competition_id, $_SESSION['user_id'] ), $mysqli );
    $mysqli->close();

?> 

<div class="container"> 
  <div class="row">
    <div class="col-12 col-xl-7">
      <div class="mb-3 card">
        <div class="card-body"> 
          <h2 class="card-title mb-3">MA COMMANDE</h2>
          <table class='table'>
            <?php foreach( $order['content'] as $b ): ?>
              <tr>
                <td colspan="3" class="table-dark text-uppercase fw-bold"><?php echo $b['name'] ?></td>
              </tr>
              <?php foreach( $b['items'] as $i ): ?>
                <tr>
                  <td><?php echo $i['name'] ?></td>
                  <td class="fit text-end">x<?php echo $i['qty'] ?></td>
                  <td class="fit text-end"><?php echo number_format( $i['total_cost'], 2 ) ?> €</td>
                </tr>
                <?php foreach( $i['options'] as $o ): ?>
                  <?php foreach( $o as $s ): ?>
                    <tr>
                      <td class="ps-5 text-muted">&#8627; <?php echo $s['name'] ?></td>
                      <td class="fit text-end text-muted">x<?php echo $s['qty'] ?></td>
                      <td class="fit text-end text-muted"><?php echo $s['total_cost'] ? number_format( $s['total_cost'], 2 ) : '--' ?> €</td>
                    </tr>
                  <?php endforeach ?>
                <?php endforeach ?>
              <?php endforeach ?>
            <?php endforeach ?>
          </table>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-5">
      <div id="information-competition" class="mb-3 card mx-auto">
        <div class="card-body">
          <h2 class="card-title mb-3 text-uppercase">INFORMATIONS</h2>
          <h5 class="mb-0"><?php echo $competition['name'] ?></h5>
          <?php if( $competition['information'] ): ?>
            <div class="alert alert-danger my-2" role="alert">
              <?php echo $competition['information'] ?>
            </div>
          <?php endif ?>
          <div class="col-12 mb-3 pb-3 border-bottom">
            <a class="card-link" href="https://www.worldcubeassociation.org/contact?competitionId=<?php echo $competition_id ?>&contactRecipient=competition&message=Bonjour,%20j%E2%80%99ai%20une%20question%20sur%20Commande%20Utile." target="_blank">Contacter l'équipe organisatrice</a>
          </div>
          <h5 class="card-title"><?php echo $_SESSION['user_name'] ?></h5>
          <h6 class="card-subtitle mb-2 text-muted"><?php echo decrypt_data( $_SESSION['user_email'] ) ?></h6>
          <h6 class="card-subtitle mb-3 text-muted"><?php echo $_SESSION['user_wca_id'] ?></h6>
          <?php if( $order['user_comment'] ): ?>
            <div class="col-12 mb-3">
              <div class="alert alert-light" role="alert">
                <?php echo $order['user_comment'] ?>
              </div>
            </div>
          <?php endif ?>
          <div class="col-12 pt-3 border-top text-end">
            <h5 class="card-title">Total : <?php echo number_format( $order['order_total'], 2) ?> €</h5>
          </div>
        </div>
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

  require_once '../src/_footer.php' 

?>
