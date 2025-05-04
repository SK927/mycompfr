<?php 

  require_once 'src/_header.php'; 
  require_once '../src/mysql_connect.php';
  require_once 'src/_functions.php'; 

?>  

<div class="container-fluid">
  <script src="https://<?php echo "{$_SERVER['SERVER_NAME']}/{$site_alias}" ?>/assets/js/worldmap.js"></script>
  <script src="https://<?php echo "{$_SERVER['SERVER_NAME']}/{$site_alias}" ?>/assets/js/mapdata1.js"></script>
  <script>var mapDelegated = simplemaps_worldmap.create()</script>
  <script src="https://<?php echo "{$_SERVER['SERVER_NAME']}/{$site_alias}" ?>/assets/js/mapdata2.js"></script>
  <script>var mapOrganized = simplemaps_worldmap.create()</script>
  <?php if ( $_SESSION['logged_in'] ): ?>  
    <?php $view_as = isset( $_GET['view_as'] ) ? $_GET['view_as'] : $_SESSION['user_wca_id'] ?>
    <?php $organized_competitions = get_competitions_managed_by_user_in_past( $view_as, 'organizer', $conn ) ?>
    <?php $delegated_competitions = get_competitions_managed_by_user_in_past( $view_as, 'delegate', $conn ) ?>
    <div class="col-12">
      <div class="card">
        <div class="card-header"><b><?php echo $view_as ?></b> statistics as Delegate/Organizer</div>
        <div id="stats" class="card-body px-md-5 py-3"> 
          <div class="row text-center justify-content-center">
            <?php if ( count( $delegated_competitions->competitions ) ): ?>
              <div class="col-12 col-sm-8 col-lg-6">
                <h2>Competitions as a Delegate (<?php echo count( $delegated_competitions->competitions ) ?>)</h2>
                <table class="table table-striped w-auto">
                  <tbody>
                    <tr>
                      <td>
                        <?php foreach ( $delegated_competitions->competitions as $competition_id ): ?>
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
                        <?php foreach ( $delegated_competitions->countries as $country => $cnt ): ?>
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
                        <?php foreach ( $delegated_competitions->years as $year => $cnt ): ?>
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
                        <?php foreach ( $delegated_competitions->events as $event => $cnt ): ?>
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
                        <?php foreach ( $delegated_competitions->users as $co => $cnt ): ?>
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
                  <?php foreach ( $delegated_competitions->locations as $latitude => $location ): ?>
                    <?php foreach ( $location as $longitude => $id ): ?>
                      <?php if ( $latitude or $longitude ): ?>
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

            <?php if ( count( $organized_competitions->competitions ) ): ?>
              <div class="col-12 col-sm-8 col-lg-6">
                <h2>Competitions as an organizer (<?php echo count( $organized_competitions->competitions ) ?>)</h2>
                <table class="table table-striped w-auto">
                  <tbody>
                    <tr>
                      <td>
                        <?php foreach ( $organized_competitions->competitions as $competition_id ): ?>
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
                        <?php foreach ( $organized_competitions->countries as $country => $cnt ): ?>
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
                        <?php foreach ( $organized_competitions->years as $year => $cnt ): ?>
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
                        <?php foreach ( $organized_competitions->events as $event => $cnt ): ?>
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
                        <?php foreach ( $organized_competitions->users as $co => $cnt ): ?>
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
                  <?php foreach ( $organized_competitions->locations as $latitude => $location ): ?>
                    <?php foreach ( $location as $longitude => $id ): ?>
                      <?php if ( $latitude or $longitude ): ?>
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
            <div class="col-12 mt-4">
              <u>Nota:</u> people lists do not account for competitions organized across multiple countries.
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    Please sign in to continue
  <?php endif ?>
</div>

<?php $conn->close() ?>
      
<?php require_once '../src/_footer.php' ?>



