<?php

  require_once '../src/sessions_handler.php'; 

  $competition_id = $_POST['competition_select'];

  if( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) )
  {
    require_once 'src/_header.php';
    require_once 'src/_functions.php';

    [ $noshow_list, $competition_data ] = get_noshow_list( $competition_id, $_SESSION['user_token'] );
  
?>

<div class="container-fluid">
  <div class="row mt-4 justify-content-center text-center">
    <div class='col-12 col-lg-9'>
      <h3>No-shows list for <b><?php echo $competition_data['name'] ?></b></h3>
       <div class="row justify-content-center mt-4">
          <div class="col px-3">
            <table class="table table-striped">
              <tbody>
                <?php foreach( $noshow_list as $person_name => $person_info ): ?>
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