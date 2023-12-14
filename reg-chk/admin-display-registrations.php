<?php 

  require_once 'src/layout/_header.php';
  require_once '../src/functions/encrypt-functions.php';

  $competition_id = decrypt_data( $_GET['id'] );

  if ( $_SESSION['logged_in'] AND in_array( $competition_id, $_SESSION['manageable_competitions'] ) )
  {    
    require_once '../src/mysql/mysql-connect.php';
    require_once 'src/custom-functions.php';
    
    $registrations = get_competition_registrations_from_db( $competition_id, $conn ); 

    if ( $registrations )
    {

?>
      <div class="col-12 mt-3">
        <div class="card">
          <div class="card-header">REGISTRATIONS</div>
          <div class="card-body"> 
            <table>
              <tr class="fw-bold">
                <td class="pr-2">Name</td>
                <td class="px-2">Email</td>
                <td class="px-2">Status</td>
              </tr>
              <?php foreach ( $registrations as $registration ): ?>  
                <tr>
                  <td class="pr-2"><?php echo $registration['name']; ?></td>
                  <td class="px-2"><?php echo decrypt_data( $registration['email'] ); ?></td>
                  <td class="px-2 <?php echo $registration['confirmed']; ?>"><?php echo $registration['confirmed']; ?></td>
                </tr>
              <?php endforeach; ?>       
            </table>
            <a class="extract_csv btn btn-sm btn-secondary mt-4 mb-2" href="src/admin/extract-attendance-csv?id=<?php echo urlencode( $_GET['id'] ); ?>">Extract CSV</a>
          </div>         
        </div>
      </div> 
       
<?php 
  
      require_once '../src/layout/_status-bar.php';
    }
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}" );    
    exit(); 
  } 

  require_once 'src/layout/_footer.php'; 

?>



