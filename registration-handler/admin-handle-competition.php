<?php 

  require_once 'src/layout/_header.php';
  require_once '../src/functions/generic-functions.php';
  
  $competition_id = $_GET['id']; 

  if ( $_SESSION['logged_in'] AND in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) )
  {    
    require_once '../src/mysql/mysql-connect.php';
    require_once '../src/functions/encrypt-functions.php';
    require_once 'src/custom-functions.php';

    [ $competition_name, $events, $registrations ] = get_competition_data_from_db( $competition_id, $conn );
    // [ $competitors_list, $error ] = get_competitors_from_public_wcif( $competition_id );

    if ( ! $error )
    {

?>

<script src="assets/js/handle-competition-actions.js"></script> <!-- Custom JS to handle current page actions -->
<div class="container-fluid">
  <div class="row text-center mb-3">
    <h1 class="col-12 text-uppercase"><?php echo $competition_name; ?></h1>
  </div>
  <?php foreach ( $registrations as $user_id => $registration ): ?>
    <?php $formatted_data = format_registration_data( $competition_id, $user_id, $registration, $competitors_list ); ?>
    <?php if ( $formatted_data ): ?>
      <div class="card mb-2 p-4">
        <form id="<?php echo $_GET['id']; ?>" class="card-body competition-id" name="registration_form" method="POST" action="src/pdf/pdf-generate-scorecards.php">
          <input type="hidden" value="<?php echo encrypt_data( $user_id ); ?>" name="user_id" />
          <input type="hidden" value="<?php echo $formatted_data['user_data']['registrant_id']; ?>" name="user_registrant_id" />
          <div class="row text-center">
            <div class="col-12 col-md-3">
              <div class="col-12 text-uppercase font-weight-bold">
                <h3><?php echo $formatted_data['user_data']['registration_data[name]']; ?></h3>
              </div>
              <div class="col-12"> 
                <?php if ( $formatted_data['registered'] ): ?>
                  <span class="text-success">Registered &#10003;</span>
                <?php else: ?>
                  <span><a id="<?php echo encrypt_data( $user_id ); ?>_register" class="text-danger" href="<?php echo $formatted_data['registration_link']; ?>" target="_blank">(Register on WCA)</a></span>
                <?php endif; ?>
              </div>
              <div class="col-12 mb-3">
                <label id="<?php echo encrypt_data( $user_id ); ?>" class="done">
                  <input id="<?php echo encrypt_data( $user_id ); ?>_checkbox" class="done-checkbox" type="checkbox" name="printed" <?php if ( $formatted_data['printed'] ) echo "checked"; ?> />
                  Scorecards are printed?
                </label>
              </div>
              <div class="col-12">
                <button class="btn btn-secondary" name="competition_id" value="<?php echo $_GET['id']; ?>">
                  Generate scorecards
                </button>
              </div>
            </div>
            <div class="col-12 col-md-9">
              <div class="row">
                <?php foreach ( $formatted_data['events'] as $event => $group ): ?>
                  <div class="col-6 col-sm-4 col-md-3">
                    <label class="mb-1 text-uppercase" for="<?php echo $event; ?>"><?php echo substr( $event, 1 ); ?> (<?php echo $events[ $event ]['groups']; ?> grp)</label>
                    <select class="form-control mb-2" name="<?php echo $event; ?>" type="number" value="<?php echo $group; ?>">
                      <?php for ( $cnt = 1 ; $cnt <= $events[ $event ]['groups'] ; $cnt++ ): ?>
                        <option value="<?php echo $cnt; ?>"><?php echo $cnt; ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </form>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>
      
<?php

      require_once '../src/layout/_status-bar.php';
    }
    else
    {
      echo $error;
    }
  }
  else
  {
    echo "Cannot access this page!";
  }

  require_once '../src/layout/_footer.php';
    
?>