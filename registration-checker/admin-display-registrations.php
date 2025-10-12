<?php 

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ERROR);

  require_once 'src/_header.php';

  $competition_id = $_GET['id'];

  if ( in_array( $competition_id, array_keys( $_SESSION['manageable_competitions'] ) ) ) 
  {    
    require_once '../src/mysql_connect.php';
    require_once 'src/_functions.php';
    
    $registrations = get_competition_registrations_from_db( $competition_id, $conn ); 

    $conn->close();

    if ( $registrations )
    {

?>

<script src="assets/js/admin-actions.js"></script>
<div class="container-fluid">
  <div class="row mt-4 justify-content-center text-center">
    <div class='col-12 col-lg-9'>
      <h3>Registrations for <b><?php echo $_SESSION['manageable_competitions'][ $competition_id ]['name'] ?></b></h3>
      <div class="row justify-content-center mt-4 p-3">
        <div class="col px-3">
          <table class="table table-striped w-auto m-auto">
            <tbody>
              <?php foreach ( $registrations as $id => $registration ): ?> 
                <?php $email = str_replace( '.', '.<wbr>', str_replace( '@', '@<wbr>', decrypt_data( $registration['email'] ) ) ) ?>
                <tr>
                  <th scope="row"><?php echo $registration['name'] ?></th>
                  <td><?php echo $id;  echo $email ?></td>
                  <td class="<?php echo $registration['confirmed'] ?>">
                    <div class="row">
                      <div class="col">
                        <?php echo $registration['confirmed'] ?>
                      </div>
                      <div class="col">
                        <button class="going btn btn-outline-secondary py-0" value="<?php echo encrypt_data( $competition_id ) . '_' . encrypt_data( $id ) ?>" name="competitor_id">&check;</button> 
                        <button class="not-going btn btn-outline-secondary py-0" value="<?php echo encrypt_data( $competition_id ) . '_' . encrypt_data( $id ) ?>" name="competitor_id">&cross;</button>
                      </div>
                  </td>
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
  
      require_once '../src/_status-bar.php';
    }
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );    
    exit(); 
  } 

  require_once '../src/_footer.php'

?>



