<?php

  require_once '../src/sessions_handler.php'; 

  $competition_id = $_GET['id'];

  if( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) or $_SESSION['is_admin'] ) 
  {
    require_once 'src/_navbar.php';  
    require_once 'src/_functions.php';
    require_once '../src/mysqli.php';

    mysqli_open( $mysqli );
    $events = retrieve_ordered_schedule( $competition_id );
    $stored_info = get_stored_info( $competition_id, $mysqli );
    $mysqli->close();

?>
    
    <script src="assets/js/admin.js"></script> 
    <div class="container-fluid text-center">
      <div class="row">
        <h1 id="competition-id" class="col-12 text-uppercase"><?php echo $competition_id ?></h1>
      </div>
      <div class="row justify-content-center">
        <div class="col-12 col-md-6 mt-5">
          <h3>Live</h3>
          <input id="live-link" class="form-control text-center" type="text" name="live_link" value="<?php echo $stored_info['live'] ?>" placeholder="https://live.worldcubeassociation.org/competitions/1234/rounds/56789"></input>
        </div>
        <div class="col-12 mt-3">
          <h3>Rounds</h3>
          <?php foreach( $events as $event ): ?>
            <button type="button" class="btn <?php echo $stored_info['current'] == $event['name'] ? 'btn-success' : 'btn-light' ?> mt-2" value="<?php echo "{$event['name']}" ?>"><?php echo $event['name'] ?></button>
          <?php endforeach ?> 
        </div>
      </div>
    </div>

<?php 

  }
  else
  {
    header("Location: index.php" );
    exit();
  }

  require_once '../src/_footer.php';  

?>
