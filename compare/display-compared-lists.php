<?php

  require_once 'src/layout/_header.php';

  require_once dirname( __DIR__, 1 ) . '/67gen/src/custom-functions.php';

  if ( ! empty( $_POST ) )
  {
    $competition1_id = get_competition_id_from_url( $_POST['competition_url1'] );
    $competition2_id = get_competition_id_from_url( $_POST['competition_url2'] );

    [ $competition1_data, $error1 ] = read_competition_data_from_public_wcif( $competition1_id );
    [ $competition2_data, $error2 ] = read_competition_data_from_public_wcif( $competition2_id );

    $competitions_name = [ $competition1_data['name'], $competition2_data['name'] ];

    if ( ! $error1 and ! $error2 )
    {   
      if ( $competition1_data['schedule']['startDate'] > $competition2_data['schedule']['startDate'] )
      {
        $competitions_name = [ $competitions_name[1], $competitions_name[0] ];
      }

      $competition2_competitors = array_column( $competition2_data['persons'], 'name');
      $comparison_list = array();

      foreach ( $competition1_data['persons'] as $person )
      {
        if ( in_array( $person['name'], $competition2_competitors ) )
        {
          $person['wcaId'] = $person['wcaId'] != "" ? $person['wcaId'] : "<b>newcomer</b>";
          $comparison_list[ $person['name'] ] = array(
                                                  'wca_id' =>$person['wcaId'], 
                                                  'registrant_id' => $person['registrantId'],
                                                );
        }
      }
    }
  }
  
?>
      <div class="row">
        <div class="col my-4">
          Comparison between <b><?php echo $competitions_name[0] ?></b> and <b><?php echo $competitions_name[1] ?></b>
        </div>
      </div>
      <div class="row">
        <div class="col d-flex justify-content-center">
          <table class="table table-striped w-auto ">
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
<?php 

  require_once 'src/layout/_footer.php';

?>