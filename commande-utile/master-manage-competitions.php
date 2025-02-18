<?php

  require_once 'src/layout/_header.php';

  if ( $_SESSION['logged_in'] )
  {
    require_once '../src/mysql/mysql-connect.php';
    require_once 'src/functions/master-functions.php';
    require_once 'src/layout/master-manage-templates.php';

    $imported_competitions = get_all_competitions_formatted_data( $conn );
    $administrators = get_all_administrators( $conn );

?>

<script src="assets/js/master-manage-actions.js"></script> <!-- Custom JS to handle current page actions -->
<div class="container text-center">
  <div class="row">
    <?php if ( $_SESSION['can_manage'] AND $_SESSION['is_admin'] ): ?>
      <div id="competitions-list" class="col-12 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            LISTE DES COMPÉTITIONS
          </div>
          <div class="card-body col-12 text-start">
            <div class="row text-start">
              <div class="col-12 mb-4">
                <div class="row pe-3">
                  <div class="col-auto">
                    <img src="assets/img/CU-tee.png" alt="CU-tee"/>
                  </div>
                  <div class="col speech-bubble p-3">
                    Vous trouverez ici toutes les compétitions utilisant le site Commande Utile. Vous pouvez ajouter de nouvelles compétitions ou administrer les compétitions déjà existantes grâce aux boutons présents ci-dessous.
                  </div>
                </div>
              </div>
              <form id="form-competition" class="collapse col-12" action="src/master/ajax-create-new-competition" method="POST">
                <h5 class="card-title">Création d'une compétition</h5>
                <div class="form-floating">
                  <input id="competition-id" class="form-control mb-1" type="text" name="competition_id">
                  <label for="competition-id">ID de la compétition</label>
                </div>
                <div class="form-floating">
                  <input id="competition-contact-email" class="form-control mb-1" type="text" name="competition_contact_email">
                  <label for="competition-contact-email">Adresses e-mail des organisateurs</label>
                </div>
                <button class="create-conmpetition btn btn-light">Créer</button>
              </form>
              <div class="col-12 text-end mb-4">
                <button id="add-competition" class="btn btn-light my-1" data-bs-toggle="collapse" data-bs-target="#form-competition" aria-expanded="true">Ajouter une compétition</button>
              </div>  
            </div>
            <ul id="competitions" class="list-group list-group-flush">
              <script>updateCompetitionsList( <?php echo json_encode( $imported_competitions ) ?> );</script>
            </ul>
          </div>
        </div>
      </div>
      <div id="administrators-list" class="col-12 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            LISTE DES ADMINISTRATEURS
          </div>
          <div class="card-body col-12 text-start">
            <div class="row text-start">
              <div class="col-12 mb-4">
                <div class="row pe-3">
                  <div class="col-auto">
                    <img src="assets/img/CU-tee.png" alt="CU-tee"/>
                  </div>
                  <div class="col speech-bubble p-3">
                    Vous trouverez ici la liste de tous les administrateurs du site Commande Utile. Vous pouvez ajouter ou supprimer des administrateurs ainsi que regénérer leur mot de passe. Le compte "Administrator" ne peut être supprimé. L'adresse e-mail d'un administrateur doit correspondre à son adresse e-mail de compte WCA.
                  </div>
                </div>
              </div>
              <form id="form-administrator" class="collapse col-12" action="src/master/ajax-create-new-administrator" method="POST">
                <h5 class="card-title">Création d'un administrateur</h5>
                <div class="form-floating">
                  <input id="administrator-id" class="form-control mb-1" type="text" name="administrator_id">
                  <label for="administrator-id">ID de l'administrateur</label>
                </div>
                <div class="form-floating">
                  <input id="administrator-contact-email" class="form-control mb-1" type="text" name="administrator_contact_email">
                  <label for="administrator-contact-email">Adresse e-mail de l'administrateur</label>
                </div>
                <button class="create-conmpetition btn btn-light">Créer</button>
              </form>
              <div class="col-12 text-end mb-4">
                <button id="add-administrator" class="btn btn-light my-1" data-bs-toggle="collapse" data-bs-target="#form-administrator" aria-expanded="false">Ajouter un administrateur</button>
              </div>  
            </div>
            <ul id="administrators" class="list-group list-group-flush">
              <script>updateAdministratorsList( <?php echo json_encode( $administrators ) ?> );</script>
            </ul>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="col-12 mt-3">
        <div class="card section">
          <div class="card-header section-title fw-bold">
            CONNEXION À L'ESPACE ADMINISTRATEUR
          </div>
          <div class="card-body col-12 text-center">
            <div class="row justify-content-center pt-4 pb-3">
              <form id="form-credentials" class="col-12 col-lg-6" action="src/master/ajax-check-credentials" method="POST" name="form_credentials">
                <div class="form-floating">
                  <input id="administrator-id" class="form-control mb-1 text-center" type="text" name="administrator_id" required>
                  <label for="administrator-id">Identifiant</label>
                </div>
                <div class="form-floating">
                  <input id="administrator-password" class="form-control mb-1 text-center" type="password" name="administrator_password" required>
                  <label for="administrator-password">Mot de passe</label>
                </div>
                <button class="administrator-sign-in btn btn-light">Se connecter</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endif ?>        
  </div>   
</div>    

<?php 

    $conn->close();
    
    require_once '../src/layout/_status-bar.php';
  }
  else
  {
    header( "Location: https://{$_SERVER['SERVER_NAME']}/{$site_alias}" );
    exit();
  }

  require_once '../src/layout/_footer.php'; 

?>
  