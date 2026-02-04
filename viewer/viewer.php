<?php 

  require_once '../src/sessions_handler.php'; 
  require_once 'src/_header.php';
  require_once 'src/_functions.php';
  require_once '../src/mysqli.php';

  mysqli_open( $mysqli );
  $competition_id = $_GET['id'];
  $stored_info = get_stored_info( $competition_id, $mysqli );
  $mysqli->close();

?>  

  <body>
    <script src="assets/js/viewer.js"></script>
    <div class="container-fluid flex-column min-vh-100">
      <div class="row flex-column min-vh-100">
        <div class="timeline col-12">
          <table class="table text-left align-middle">
            <tbody>
              <tr>
                <td id="td-time" class="fw-bold">00:00</td>
                <td scope="row" class="fit text-center">&#9654;</td>
                <td id="td-current" class="fw-bold"><?php echo $stored_info['current'] ?></td>
                <td scope="row" class="fit text-center">&#128343;</td>
                <td id="td-next" class="fw-bold"><?php echo $stored_info['next'] ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-12 flex-fill">
          <embed id="live" src="<?php echo $stored_info['live'] ?>/projector" style="width:100%;height: 100%">
        </div>
      </div>
    </div>
	</body>
</html>