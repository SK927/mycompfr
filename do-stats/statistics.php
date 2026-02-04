<?php 
  
  require_once '../src/sessions_handler.php'; 
  require_once 'src/_header.php'; 
  require_once '../src/mysqli.php';
  require_once 'src/_functions.php'; 

  mysqli_open( $mysqli );
  $user = isset( $_GET['view_as'] ) ? $_GET['view_as'] : $_SESSION['user_wca_id'];
  $organized_competitions = get_competitions_managed_by_user_in_past( $user, 'organizer', $mysqli );
  $delegated_competitions = get_competitions_managed_by_user_in_past( $user, 'delegate', $mysqli );

?>  

<script src="assets/js/worldmap.js"></script>
<script src="assets/js/mapdata1.js"></script>
<script>var mapDelegated = simplemaps_worldmap.create()</script>
<script src="assets/js/mapdata2.js"></script>
<script>var mapOrganized = simplemaps_worldmap.create()</script>
<div class="container-fluid">
  <div class="col-12">
    <div id="stats" class="px-md-5 py-3"> 
      <div class="row text-center justify-content-center">
        <div class="col-12 mb-4">
          <div class="alert alert-warning" role="alert">
            You are viewing statistics as user <b><?php echo $user ?></b>.
            <?php if( isset( $_GET['view_as'] ) ): ?>
              These statistics may not be up to date if they have not logged in to DO Stats for a while!
            <?php endif ?>
            <br/>User lists do not account for competitions organized across multiple countries.
          </div>
        </div>
        <?php if( count( $delegated_competitions->competitions ) ): ?>
          <div class="col-12 col-sm-8 col-lg-6">
            <h2>Competitions as a Delegate (<?php echo count( $delegated_competitions->competitions ) ?>)</h2>
            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $delegated_competitions->competitions as $competition_id ): ?>
                      <?php echo "&#8226;&nbsp;{$competition_id}" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $delegated_competitions->countries as $country => $cnt ): ?>
                      <?php echo "&#8226;&nbsp;{$country}&nbsp;({$cnt})" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $delegated_competitions->years as $year => $cnt ): ?>
                      <?php echo "&#8226;&nbsp;" . str_replace( 'e', '', $year ) . "&nbsp;({$cnt})" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $delegated_competitions->events as $event => $cnt ): ?>
                      <?php echo "&#8226;&nbsp;{$event}&nbsp;({$cnt})" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $delegated_competitions->users as $co => $cnt ): ?>
                      <?php $co = str_replace( ' ', '&nbsp;', $co )?>
                      <?php $co = explode( '|', $co ) ?>
                      <?php $co[1] = $co[1] ? $co[1] : $co[0] ?>
                      <?php echo "&#8226;&nbsp;<a href=\"?view_as={$co[1]}\">{$co[0]}</a>&nbsp;({$cnt})" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <script>
              <?php $cnt = 0 ?>
              <?php foreach( $delegated_competitions->locations as $latitude => $location ): ?>
                <?php foreach( $location as $longitude => $id ): ?>
                  <?php if( $latitude or $longitude ): ?>
                      mapDelegated.mapdata['locations'][<?php echo $cnt ?>] = {
                        name: "<?php echo $id ?>",
                        lat: "<?php echo $latitude ?>",
                        lng: "<?php echo $longitude ?>",
                        color: "default",
                        description: "default",
                        url: "default"
                      };
                    <?php $cnt++ ?>
                  <?php endif ?>
                <?php endforeach ?>
              <?php endforeach ?>
            </script>

            <div id="map-delegated" class="my-4"></div>
          </div>
        <?php endif ?>

        <?php if( count( $organized_competitions->competitions ) ): ?>
          <div class="col-12 col-sm-8 col-lg-6">
            <h2>Competitions as an organizer (<?php echo count( $organized_competitions->competitions ) ?>)</h2>
            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $organized_competitions->competitions as $competition_id ): ?>
                      <?php echo "&#8226;&nbsp;{$competition_id}" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $organized_competitions->countries as $country => $cnt ): ?>
                      <?php echo "&#8226;&nbsp;{$country}&nbsp;({$cnt})" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $organized_competitions->years as $year => $cnt ): ?>
                      <?php echo "&#8226;&nbsp;" . str_replace( 'e', '', $year ) . "&nbsp;({$cnt})" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $organized_competitions->events as $event => $cnt ): ?>
                      <?php echo "&#8226;&nbsp;{$event}&nbsp;({$cnt})" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <table class="table table-striped w-auto">
              <tbody>
                <tr>
                  <td>
                    <?php foreach( $organized_competitions->users as $co => $cnt ): ?>
                      <?php $co = str_replace( ' ', '&nbsp;', $co )?>
                      <?php $co = explode( '|', $co ) ?>
                      <?php $co[1] = $co[1] ? $co[1] : $co[0] ?>
                      <?php echo "&#8226;&nbsp;<a href=\"?view_as={$co[1]}\">{$co[0]}</a>&nbsp;({$cnt})" ?>
                    <?php endforeach ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <script>
              <?php $cnt = 0 ?>
              <?php foreach( $organized_competitions->locations as $latitude => $location ): ?>
                <?php foreach( $location as $longitude => $id ): ?>
                  <?php if( $latitude or $longitude ): ?>
                      mapOrganized.mapdata['locations'][<?php echo $cnt ?>] = {
                        name: "<?php echo $id ?>",
                        lat: "<?php echo $latitude ?>",
                        lng: "<?php echo $longitude ?>",
                        color: "default",
                        description: "default",
                        url: "default"
                      };
                    <?php $cnt++ ?>
                  <?php endif ?>
                <?php endforeach ?>
              <?php endforeach ?>
            </script>

            <div id="map-organized" class="my-4"></div>
          </div>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>

<?php 

  $mysqli->close();

  require_once '../src/_footer.php';

?>



