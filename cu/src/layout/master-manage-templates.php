<template id="competition-row">
  <li class="list-group-item">
    <div class="comp-row row mb-3 pb-2">  
      <div class="col-md-4 px-4 text-start align-middle">
        <p class="competition-name m-0 text-uppercase fw-bold"></p>
        <small class="competition-dates text-muted"></small>  
      </div>
      <div class="col-md-8 competition-contact px-4 text-break align-middle"></div>
      <div class="col-12 mt-2 text-end">
        <a class='competition-handle-url' href=""><button class="btn btn-light my-1">Editer</button></a>
        <button class="delete-competition btn btn-danger my-1 me-1" name="">Suppr.</button>
      </div>
    </div>
  </li>
</template>

<template id="administrator-row">
  <li class="list-group-item">
    <div class="admin-row row mb-3 pb-2">  
      <div class="administrator-login col-md-4 px-4 fw-bold text-left align-middle"></div>
      <div class="administrator-email col-md-8 px-4 text-break align-middle"></div>
      <div class="col-12 mt-2 text-end">
        <button class="regenerate-password btn btn-light my-1" name="">Regénérer</button>
        <button class="delete-administrator btn btn-danger my-1 me-1" name="">Suppr.</button>
      </div>
    </div>
</template>