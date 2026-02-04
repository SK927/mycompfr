<template id="to-import">
  <div class="col-12 col-sm-auto">
    <button class="import-competition btn btn-link p-0" value="" name="competition_id">
      <img src="assets/img/dl.png" width="32" />
    </button>
  </div>
</template>

<template id="imported">
  <div class="col-auto">
    <a class="extract-data btn btn-link px-0" href="" title="Afficher les inscriptions">
      <img src="assets/img/ul.png" alt="show registrations" width="32" />
    </a>
  </div>
  <div class="col-auto pl-2">
    <button class="send-reminder btn btn-link px-0" value="" name="competition_id" title="Envoyer un rappel aux personnes n'ayant pas répondu ou en attente">
      <img src="assets/img/email.png" alt="send reminder" width="32" />
    </button>
  </div>
  <div class="col-auto pl-2">
    <button class="copy-emails btn btn-link px-0" value="" name="competition_id" title="Copier l'adresse e-mail des personnes n'ayant pas répondu ou en attente">
      <img src="assets/img/copy-emails.png" alt="copy not answered emails" width="32" />
    </button>
  </div>
  <div class="col-auto pl-2">
    <button class="update-competitors btn btn-link px-0" value="" name="competition_id" title="Mettre à jour la liste des compétiteurs">
      <img src="assets/img/update.png" alt="update competitors list" width="32" />
    </button>
  </div>
</template>

<template id="displayed">
  <div class="col-12 col-md-6 mb-2 mb-md-0">
    <div id="" class="card check-attendance text-center">
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-lg-auto text-left">
            <button class="going btn btn-outline-secondary py-0" value="" name="competition_id">&check;</button> 
            <button class="maybe btn btn-outline-secondary py-0" value="" name="competition_id">&quest;</button> 
            <button class="not-going btn btn-outline-secondary py-0" value="" name="competition_id">&cross;</button>
          </div>
          <div class="col align-items-center mt-2 mt-lg-0 text-wrap text-centre text-lg-start">
            <p class="m-0 p-0">
              <span class="competition-name"></span>
            </p>
            <p class="competition-info m-0 p-0 text-muted"></p>
            <p class="competition-not-answered m-0 p-0 text-muted"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  