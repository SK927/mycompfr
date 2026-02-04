<?php 

  $case = $_GET['case'];

  require_once '../src/_functions-generic.php';
  require_once 'src/_header.php';
  require_once 'src/_functions.php';

  $case_json = file_get_contents( "assets/cases/study_{$case}.json" );
  $case_by_regs = from_pretty_json( $case_json )['competitors'];
  $case_by_paid = $case_by_regs;

  $paid_at = array_column( $case_by_paid, 'paid_at');
  array_multisort( $paid_at, SORT_ASC, $case_by_paid );

?>  

<script src="assets/js/index.js"></script>
<div class="container">
  <div class="row">
    <div class="col-12">
      <h1>TUTestes Open 2050</h1>
    </div> 
  </div>
  <div class="row">
    <div class="col">
      <div class="alert alert-success">
        <p class="m-0">Nombre d'inscriptions déja acceptées : <b>1</b></p>
        <P class="m-0">Nombre maximal de place disponibles : <b>10</b></p>
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 col-lg-6">
      <h1>AFS</h1>
      <table class="table table-bordered border-dark">
        <thead class="thead-dark">
          <tr role="row" class="">
              <th class="bg-dark text-light" >Nom complet</th>
              <th class="bg-dark text-light">Statut</th>
          </tr>
        </thead>
        <?php foreach( $case_by_regs as $competitor ): ?>
          <tr class="<?php echo get_afs_color( $competitor ) ?> border-dark">
            <td class=""><?php echo $competitor['name'] ?></td>
            <td class="col-9"><?php echo get_afs_text( $competitor ) ?></td>
          </tr>
        <?php endforeach ?>
      </table>
    </div>
    <div class="col-12 col-lg-6">
      <h1>WCA</h1>
      <table class="table table-striped">
        <thead class="thead-dark">
          <tr role="row" class="">
            <th class="bg-light col"></th>
            <th class="bg-light">Nom</th>
            <th class="bg-light">Payé le</th>
          </tr>
        </thead>
        <tr style="display:none"></tr>
        <?php foreach( $case_by_paid as $competitor ): ?>
          <tr>
            <td class="col"><a class="edit" href="" data-toggle="tooltip" data-placement="top" title="<?php echo get_registered_at_tooltip( $competitor['registered_at'] ) ?>">Éditer</a></td>
            <td><?php echo $competitor['name'] ?></td>
            <td><a class="paid no-default" href="" data-toggle="tooltip" data-placement="top" title="<?php echo get_paid_at_tooltip( $competitor ) ?>"><?php echo get_wca_paid( $competitor ) ?></td>
          </tr>
        <?php endforeach ?>
      </table>
    </div>
  </div>
  <form class="row  mt-4" action="solution?case=<?php echo $case ?>" method="POST">
    <div class="col-auto" >
      <div class="form-group">
        <input type="text" class="form-control" id="response" placeholder="Réponse : ABCDEFGHIJ" name="response">
      </div>
    </div>
    <div class="col-auto align-bottom" >
      <button type="submit" class="btn btn-dark ">Confirmer</button>
    </div>
  </form>
</div>

<?php require_once '../src/_footer.php' ?>
