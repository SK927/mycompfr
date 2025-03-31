<?php

  require_once 'src/_header.php';
  require_once 'src/_functions.php';

  $competition_id = $_POST['competition_select'];

  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) )
  {
    [ $noshow_list, $competition_data ] = get_noshow_list( $competition_id, $_SESSION['user_token'] );
  
?>

<div class="container-fluid">
  <?php if ( $_SESSION['logged_in'] ): ?>  
    <div class="row mt-4 justify-content-center text-center">
      <div class='col-12 col-lg-9'>
        <h3>No-shows list for <b><?php echo $competition_data['name'] ?></b></h3>
         <div class="row justify-content-center mt-4">
            <div class="col px-3">
              <table class="table table-striped">
                <tbody>
                  <?php foreach ( $noshow_list as $person_name => $person_info ): ?>
                    <tr>
                      <th scope="row"><?php echo $person_info['wca_id'] ?></th>
                      <td><?php echo $person_name ?></td>
                      <td>(registrant id: <?php echo $person_info['registrant_id'] ?>)</td>
                    </tr>
                  <? endforeach ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif ?>
</div>

<?php

  }
  else
  {
    header("Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );
    exit();
  }
  
  require_once '../src/_footer.php';

?>