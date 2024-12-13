<?php

  require_once 'src/layout/_header.php';
  require_once 'src/custom-functions.php';

  $competition1_id = get_competition_id( $_POST, 1 );
  $competition2_id = get_competition_id( $_POST, 2 );

  if ( $competition1_id and $competition2_id )
  {
    [ $comparison_list, $competition1_data, $competition2_data ] = get_compared_list( $competition1_id, $competition2_id );
  }
  
?>

<div class="container<?php if ( ! $_SESSION['logged_in'] ) echo "-fluid" ?>">
  <?php if ( $_SESSION['logged_in'] ): ?>  
    <div class="row mt-4 justify-content-center text-center">
      <div class='col-12 col-lg-9'>
        <h3>Comparative list for <b><?php echo $competition1_data['name'] ?></b> and <b><?php echo $competition2_data['name'] ?></b></h3>
         <div class="row justify-content-center mt-4">
            <div class="col px-3">
              <table class="table table-striped">
                <tbody>
                  <?php foreach ( $comparison_list as $person_name => $person_info ): ?>
                    <tr>
                      <th scope="row"><?php echo $person_info['wca_id'] ?></th>
                      <td><?php echo $person_name ?></td>
                      <td>(registrant id: <?php echo $person_info['registrant_id'] ?>)</td>
                    </tr>
                  <? endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    Please sign in to continue
  <?php endif ?>
</div>

<?php require_once '../src/layout/_footer.php' ?>