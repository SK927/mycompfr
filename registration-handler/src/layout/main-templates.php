<template id="event-info">
  <div class="col-auto">
    <input type="checkbox" class="event-checkbox" name="" />
    <label class="event-label" for=""></label>
  </div>
</template>

<template id="handle-registrations">
  <a class="handle-competition col-12 col-sm-auto" href="" target="_blank">Handle new registrations</a>
</template>

<template id="to-import">
  <div class="col-12 col-sm-auto">
    <button class="import-competition btn btn-link p-0" value="" name="competition_id">
      <img src="assets/img/dl.png" width="32" />
    </button>
  </div>
</template>

<template id="displayed">
  <div class="col-12 col-md-6 col-lg-4">
    <form id="" class="card register-to text-center" action="src/admin/ajax-register-competitor" method="POST" name="">
      <div class="card-body">
        <h5 class="card-title mb-4 text-uppercase fw-bold"></h5>
        <div class="competition-events row px-5 justify-content-center" >
        </div>
      </div>
      <div class="card-footer p-0">
        <button class="confirm-registration btn btn-small btn-secondary mb-3">Register</button>
      </div>
    </form>
  </div>
</template>