<?php 

  require_once 'src/layout/_header.php';
  require_once '../config/config-db.php';
  require_once 'src/markdown/Michelf/Markdown.inc.php';  

  use Michelf\Markdown;

  if ( $_SESSION['logged_in'] and in_array( $_SESSION['user_wca_id'], ADMINS_ID ) )
  {
    require_once '../src/functions/generic-functions.php';
    require_once '../src/mysql/mysql-connect.php';
  
    $parser = new Markdown;

    if ( isset( $_GET['id'] ) )
    {
      $id = $_GET['id'];
    }
    else
    {
      $query_results = $conn->query( "SELECT MAX(id) FROM " . DB_PREFIX_AFS );
      $result_row = $query_results->fetch_assoc();
      $id = $result_row['MAX(id)'];
    }

?>

<script src="assets/js/admin-actions.js"></script> <!-- Custom JS to handle current page actions -->
<template id="section">
  <div class="section col-12 bg-light mt-4 py-2 px-3">
    <div class="form-floating my-1">
      <input id="" type="text" class="form-control" value="" name="">
      <label for="">Titre de la section</label>
    </div>
    <textarea class="col-12" rows="12" cols="96" name=""></textarea>
    <input type="submit" class="delete-section btn btn-outline-danger btn-sm mt-2" value="Supprimer la section">
    <input type="submit" class="move-section-up btn btn-outline-warning btn-sm mt-2" value="&uarr;">
    <input type="submit" class="move-section-down btn btn-outline-warning btn-sm mt-2" value="&darr;">
  </div>
</template>
<div class="container">
  <select class="select-newsletter form-control mt-4">
    <?php $query_result = $conn->query( "SELECT * FROM " . DB_PREFIX_AFS ); ?>
    <?php while ( $row = $query_result->fetch_assoc() ): ?>
      <option value="<?php echo $row['id'] ?>"<?php if ( $row['id'] == $id ) echo ' selected' ?>><?php echo "{$row['month']} {$row['year']}" ?>
      </option>
    <?php endwhile; ?>
  </select>
  <input type="submit" id="<?php echo 'create-' .  date( 'ym' ) ?>" class="handle-newsletter btn btn-light mt-2" value="Créer une nouvelle newsletter">
  <?php if ( $id != '' ): ?>
    <input type="submit" id="<?php echo 'duplicate-' .  date( 'ym' ) . '-' . $id ?>" class="handle-newsletter btn btn-light mt-2" value="Dupliquer la newsletter actuelle">
    <input type="submit" id="<?php echo 'delete-' .  $id ?>" class="delete-newsletter btn btn-light mt-2" value="Supprimer la newsletter actuelle">
  <?php endif; ?>
  <form id="newsletter-form-<?php echo $id ?>" class="row form" action="admin-preview-newsletter.php?id=<?php echo $id ?>" method="POST" name="newsletter">
    <div id="content">
      <?php $query_result = $conn->query( "SELECT * FROM " . DB_PREFIX_AFS . " WHERE id = {$id}" ); ?>
      <?php while ( $row = $query_result->fetch_assoc() ): ?>
        <?php $data = from_pretty_json( $row['data'] ); ?>
        <?php foreach ( $data as $key => $value ): ?>
          <script>  
            createSection('<?php echo $key ?>', <?php echo json_encode( htmlspecialchars( $value['title'] ) ) ?>, <?php echo json_encode( htmlspecialchars( $value['text'] ) ); if ( ! preg_match( '/section/', $key ) ) echo ', false' ?>);
          </script>
        <?php endforeach; ?> 
        <div class="col-12 mt-3 mb-5">
          <input type="submit" class="add-section btn btn-light mt-2" value="Ajouter une section">
          <input type="submit" class="btn btn-light mt-2" value="Prévisualiser">
          <?php if ( $row['published'] ): ?>
            <input id="publish-newsletter" type="submit" class="btn btn-light mt-2" value="Rendre privé" />
          <?php else: ?>
            <input id="publish-newsletter" type="submit" class="btn btn-light mt-2" value="Publier" />
          <?php endif; ?>
          <span id="published" style="display:none"><?php echo (1 - $row['published']) ?></span>
        </div>
      <? endwhile;?>
    </div>
  </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/0.3.2/marked.min.js"></script>
<script src="https://www.jquery-az.com/jquery/js/bootstrap-markdown-editor.js"></script>

<?php

    $conn->close();
  }
  else
  {
    echo $_SESSION['user_wca_id'];
    header( "Location: https://{$_SERVER['SERVER_NAME']}/afs" );
    exit();
  }

  require_once '../src/layout/_footer.php';

?>