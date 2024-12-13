<?php 

  require_once 'src/layout/_header.php'; 
  require_once dirname( __DIR__, 1 ) . '/src/mysql/mysql-connect.php';
  require_once 'src/custom-functions.php'; 

?>  

<div class="container-fluid">
  <?php if ( $_SESSION['logged_in'] ): ?>  
    <?php $view_as = isset( $_GET['view_as'] ) ? $_GET['view_as'] : $_SESSION['user_name'] ?>
    <?php $organized_competitions = get_competitions_managed_by_user_in_past( $view_as, 'organizer', $conn ) ?>
    <?php $delegated_competitions = get_competitions_managed_by_user_in_past( $view_as, 'wcaDelegate', $conn ) ?>
    <script src="assets/js/index-actions.js"></script> <!-- Custom JS to handle current page actions -->
    <div class="col-12">
      <div class="card">
        <div class="card-header">My statistics as Delegate/Organizer</div>
        <div id="stats" class="card-body px-5 py-3"> 
          <div class="row text-center justify-content-center">
            <?php if ( count( $delegated_competitions->competitions ) ): ?>
              <div class="col-12 col-sm-8 col-lg-6">
                <h2>Competitions as a Delegate (<?php echo count( $delegated_competitions->competitions ) ?>)</h2>
                <table class="table table-striped w-auto">
                  <tbody>
                    <tr>
                      <td>
                        <?php foreach ( $delegated_competitions->competitions as $competition_id ): ?>
                          <?php echo "&#8226; {$competition_id}" ?>
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
                          <?php echo "&#8226; {$country} ({$cnt})" ?>
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
                          <?php echo "&#8226; " . str_replace( 'e', '', $year ) . " ({$cnt})" ?>
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
                          <?php echo "&#8226; <a href=\"?view_as={$co}\">{$co}</a> ({$cnt})" ?>
                        <?php endforeach ?>
                      </td>
                    </tr>
                  </tbody>
                </table>
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
                          <?php echo "&#8226; {$competition_id}" ?>
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
                          <?php echo "&#8226; {$country} ({$cnt})" ?>
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
                          <?php echo "&#8226; " . str_replace( 'e', '', $year ) . " ({$cnt})" ?>
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
                          <?php echo "&#8226; <a href=\"?view_as={$co}\">{$co}</a> ({$cnt})" ?>
                        <?php endforeach ?>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            <?php endif ?>
            <div class="col-12 mt-4">
              <u>Nota:</u> stats do not account for competitions organized across multiple countries.
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    Please sign in to continue
  <?php endif; ?>
</div>

<?php $conn->close() ?>
      
<?php require_once '../src/layout/_footer.php' ?>



