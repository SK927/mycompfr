<?php 

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ERROR);

  require_once 'src/_header.php';

?>  

<div class="container">
  <div class="row">
    <div class="col-12 text-center mb-4">
      <h3>Bienvenue sur ta session d'entraînement !</h3>
    </div>
    <div class="col-12 col-md-9 col-lg-8 m-auto">
      <p>Ton objectif est de fournir et valider la liste des inscriptions que tu dois accepter, <b>dans le bon ordre</b>. Une explication de la solution te sera fournie une fois ta réponse soumise. Attention cependant, certains compétiteurs peuvent ne pas faire partie de la solution.</p>
      <p>Tu trouveras sur la page suivante la réplique de deux pages différentes :</p>
      <div class="row">
        <div class="col-12 col-md-6 p-1">
          <div class="alert alert-success p-4">
            <p>La page de la compétition sur le site de l'AFS.</p>
            <p>Cette page présente les inscriptions dans l'ordre de leur création. Elle vous indique le statut de chaque compétiteur vis-à-vis de l'AFS.</p>
            <p><u>Conseil :</u> n'oublie pas de bien cliquer sur les liens !</p>
          </div>
        </div>
        <div class="col-12 col-md-6 p-1">
          <div class="alert alert-success p-4">
            <p>La page de la compétition sur le site de la WCA.</p>
            <p>Cette page présente les inscriptions dans l'ordre de paiement <b>puis</b> de création. Tu trouveras sur cette partie, deux informations utiles :</p>
            <ul class="mt-2">
              <li>
                <u>La date et l'heure d'inscription</u>. Elles sont disponibles en survolant le lien <i>Editer</i> ou le texte de la colonne <i>Payé le</i> dans le cas où les frais d'inscription n'ont pas été réglés.
              </li>
              <li class="mt-2">
                <u>La date et l'heure de paiement</u>. Elles sont disponibles en survolant le texte de la colonne <i>Payé le</i> dans le cas où les frais d'inscription ont été réglés.
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 text-center mt-3 mb-5">
      <a href="study?case=1" class="btn btn-dark" role="button">Allons gérer quelques inscriptions !</a>
    </div>
  </div>
</div>

<?php require_once '../src/_footer.php' ?>
