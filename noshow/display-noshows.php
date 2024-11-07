<?php

  require_once 'src/layout/_header.php';

  require_once dirname( __DIR__, 1 ) . '/67gen/src/custom-functions.php';

  if ( ! empty( $_POST ) )
  {
    $competition_id = get_competition_id_from_url( $_POST['competition_url'] );
  
    [ $competition_data, $error ] = read_competition_data_from_public_wcif( $competition_id );

    $events = to_pretty_json( $competition_data['events'] );

    if ( ! $error )
    {
      foreach ( $competition_data['persons' ] as $person )
      {
        if ( $person['registration']['status'] == 'accepted' )
        {
          $pattern = "/\"personId\": {$person['registrantId']},/";
          $has_results = preg_match( $pattern, $events ); 

          if ( ! $has_results )
          {
            $person['wcaId'] = $person['wcaId'] != "" ? $person['wcaId'] : "<b>newcomer</b>";
            $noshows_list[ $person['name'] ] = array(
                                                  'wca_id' =>$person['wcaId'], 
                                                  'registrant_id' => $person['registrantId'],
                                                );
          }
        }
      }
    }
  }
  
?>
      <div class="row">
        <div class="col my-4">
          No-shows list for <b><?php echo $competition_data['name'] ?></b></b>
        </div>
      </div>
      <div class="row">
        <div class="col d-flex justify-content-center">
          <table class="table table-striped w-auto ">
            <tbody>
              <?php foreach ( $noshows_list as $person_name => $person_info ): ?>
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

  require_once 'src/layout/_header.php';

?>