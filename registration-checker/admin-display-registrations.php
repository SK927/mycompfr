<?php 

  require_once 'src/layout/_header.php';
  require_once '../src/functions/encrypt-functions.php';

  $competition_id = $_GET['id'];

  if ( $_SESSION['logged_in'] AND in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) ) 
  {    
    require_once '../src/mysql/mysql-connect.php';
    require_once 'src/custom-functions.php';
    
    $registrations = get_competition_registrations_from_db( $competition_id, $conn ); 

    $conn->close();

    if ( $registrations )
    {

?>

<div class="container-fluid">
  <div class="row mt-4 justify-content-center text-center">
    <div class='col-12 col-lg-9'>
      <h3>Registrations for <b><?php echo $_SESSION['manageable_competitions'][ $competition_id ]['name'] ?></b></h3>
      <div class="row justify-content-center mt-4 p-3">
        <div class="col px-3">
          <table class="table table-striped">
            <tbody>
              <?php foreach ( $registrations as $registration ): ?>  
                <tr>
                  <th scope="row"><?php echo $registration['name'] ?></th>
                  <td><?php echo decrypt_data( $registration['email'] ); ?></td>
                  <td class="<?php echo $registration['confirmed'] ?>"><?php echo $registration['confirmed'] ?></td>
                </tr>
              <?php endforeach ?>       
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div> 
</div> 
       
<?php 
  
      require_once '../src/layout/_status-bar.php';
    }
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );    
    exit(); 
  } 

  require_once '../src/layout/_footer.php'

?>



