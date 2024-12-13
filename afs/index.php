<?php 

  require_once 'src/layout/_header.php';  
  require_once dirname( __DIR__, 1 ) . '/config/config-db.php';
  require_once '../src/mysql/mysql-connect.php';

  $sql = "SELECT id, month, year FROM " . DB_PREFIX_AFS . " WHERE Published = 1 ORDER BY id DESC";
  $query_results = $conn->query( $sql );

?>  

<div class="row mt-4 justify-content-center text-center">
  <div class='col-12 col-md-6'>
    <h3>LISTE DES NEWSLETTERS</h3>
    <div class="card py-3">
      <div class="card-body text-center">
        <form action="display-compared-lists.php" method="POST" name="select-competition">
          <div class="row justify-content-center">
            <?php while ( $row = $query_results->fetch_assoc() ): ?>
              <div class="row">
                <a href="view-newsletter?id=<?php echo $row['id'] ?>">Newsletter de <?php echo "{$row['month']} {$row['year']}" ?></a>
              </div> 
            <?php endwhile ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php 

  $conn->close();

  require_once '../src/layout/_footer.php';

?>
