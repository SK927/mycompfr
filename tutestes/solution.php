<?php 

  $case = $_GET['case'];

  require_once '../src/_functions-generic.php';
  require_once 'src/_header.php';
  require_once 'src/_functions.php';

  $solution_json = file_get_contents( "assets/cases/solution_{$case}.json" );
  $data = from_pretty_json( $solution_json );
  $response = str_pad( $_POST['response'], strlen( $data['solution'] ), '-' );

?>  

<div class="container">
  <div class="row text-center">
    <?php if( $response == $data['solution'] ): ?>
      <div class="col-12 col-md-8 col-lg-7 m-auto">
        <h3>Félicitations !</h3>
        <p>
          Tu as réussi à classer les inscriptions dans le bon ordre d'acceptation. Tu trouveras une petite explication ci-dessous...
        </p>
      </div>
    <? else: ?>
      <h3>Presque...</h3>
      <p>
        Tu as commis quelques erreurs dans ta réponse :
      </p>
      <div class="row mt-2 mb-4"> 
        <div class="console col-12 col-md-8 col-lg-7 m-auto"><?php echo get_response_correctness( $response, $data['solution'] ) ?></div>
        <div class="console col-12 col-md-8 col-lg-7 m-auto"><?php echo $data['solution'] ?></div>
      </div>
    <? endif ?>
    <div class="col-12 col-md-8 col-lg-7 m-auto mt-4 text-start">
      Prenons les différents cas un par un :
      <table class="table table-bordered border-dark mt-2">
        <?php foreach ( $data['rationale'] as $competitor): ?>
          <tr class="<?php echo $competitor['class'] ?>">
            <td class="fit"><b><?php echo $competitor['name'] ?></b></td>
            <td>
              <?php foreach ( $competitor['text'] as $paragraph ): ?>
                <p><?php echo $paragraph ?></p>
              <?php endforeach ?>
            </td>
          </tr>
        <?php endforeach ?>
      </table>
    </div>
  </div>
</div>

<?php require_once '../src/_footer.php' ?>
