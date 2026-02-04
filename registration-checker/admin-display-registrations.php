<?php 

  require_once '../src/sessions_handler.php';
  require_once 'src/_header.php';

  $competition_id = $_GET['id'];

  if( isset( $_SESSION['manageable_competitions'][ $competition_id ] ) or $_SESSION['is_admin'] ) 
  {    
    require_once 'src/_functions.php';
    require_once '../src/mysqli.php';

    mysqli_open( $mysqli );
    [ $error, $registrations ] = get_competition_registrations_from_db( $competition_id, $mysqli ); 
    $mysqli->close();

    if( $registrations )
    {

?>

<script src="assets/js/admin.js"></script>
<div class="container-fluid">
  <div class="row mt-4 justify-content-center text-center">
    <div class='col-12'>
      <h3>Inscriptions pour <b><?php echo $_SESSION['manageable_competitions'][ $competition_id ]['name'] ?></b></h3>
      <div class="row justify-content-center mt-4 p-3">
        <div class="col px-3">
          <table class="table table-striped w-auto m-auto">
            <tbody>
              <?php foreach( $registrations as $registration ): ?> 
                <?php $email = str_replace( '.', '.<wbr>', str_replace( '@', '@<wbr>', decrypt_data( $registration['user_email'] ) ) ) ?>
                <tr>
                  <th scope="row"><?php echo $registration['user_name'] ?></th>
                  <td><?php echo $email ?></td>
                  <td class="<?php echo $registration['response'] ?>">
                    <div class="row">
                      <div class="col">
                        <?php echo $registration['response'] ?>
                      </div>
                      <div class="col">
                        <button class="going btn btn-outline-secondary py-0" value="<?php echo $competition_id . '_' . encrypt_data( $registration['user_id'] ) ?>" name="competitor_id">&check;</button> 
                        <button class="maybe btn btn-outline-secondary py-0" value="<?php echo $competition_id . '_' . encrypt_data( $registration['user_id'] ) ?>" name="competitor_id">&quest;</button> 
                        <button class="not-going btn btn-outline-secondary py-0" value="<?php echo $competition_id . '_' . encrypt_data( $registration['user_id'] ) ?>" name="competitor_id">&cross;</button>
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
    header( "Location: index.php" );    
    exit(); 
  } 

  require_once '../src/_footer.php'

?>



