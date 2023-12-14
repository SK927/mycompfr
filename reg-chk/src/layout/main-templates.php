      <template id="to-import">
        <div class="col-12 col-sm-auto">
          <button class="import-competition btn btn-link p-0" value="" name="competition_id">
            <img src="assets/img/dl.png" width="32" />
          </button>
        </div>
      </template>

      <template id="imported">
        <div class="col-auto">
          <a class="extract-data btn btn-link px-0" href="">
            <img src="assets/img/ul.png" width="32" />
          </a>
        </div>
        <div class="col-auto pl-2">
          <button class="send-reminder btn btn-link px-0" value="" name="competition_id">
            <img src="assets/img/email.png" width="32" />
          </button>
        </div>
        <div class="col-auto pl-2">
          <button class="update-competitors btn btn-link px-0" value="" name="competition_id">
            <img src="assets/img/update.png" width="32" />
          </button>
        </div>
      </template>
      
      <template id="displayed">
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-2 mb-md-0">
          <div id="" class="card check-attendance text-center">
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-sm-auto text-left">
                  <button class="going btn btn-outline-secondary py-0" value="" name="competition_id">&#10004;</button> 
                  <button class="not-going btn btn-outline-secondary py-0" value="" name="competition_id">&#10007;</button>
                </div>
                <div class="col align-items-center mt-2 mt-sm-0 text-wrap text-start">
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
  