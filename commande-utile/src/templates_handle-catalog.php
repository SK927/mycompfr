<template id="block">
  <div id="" class="block col-12 card mb-2">
    <div class="card-header">
      <div class="row">
        <div class="col-lg-5"><input id="" type="text" class="form-control block-name mb-1 mb-xl-0 me-1" placeholder="Nom du bloc" name="" value=""></div>
        <div class="col-auto">
          <button class="add-item btn btn-success mb-1 mb-xl-0 me-1" name="">Ajouter produit</button>
          <button class="delete-block btn btn-danger mb-1 mb-xl-0 me-1" name="">Suppr. bloc</button>
          <button class="clone-block btn btn-secondary mb-1 mb-xl-0 me-1" name="">Cloner</button>
          <button class="move-block-up btn btn-outline-secondary mb-1 mb-xl-0 me-1">&uarr;</button>
          <button class="move-block-down btn btn-outline-secondary mb-1 mb-xl-0 me-1">&darr;</button>
        </div>
      </div>
    </div>
    <div class="col-12 card-body">
      <div id="" class="row item-list">
      </div>
    </div>
  </div>
</template>

<template id="item">
  <div id="" class="item col-lg-6 p-3">
  <div class="row px-3 pb-3">
    <div class="col-auto m-0 text-start text-md-end p-0">
      <button class="delete-item btn btn-sm btn-block btn-outline-danger" name=""></button>
    </div>
    <div class="col pe-0">
      <div class="row g-2 text-start">
        <div class="col-md-8">
          <input type="text" class="form-control item-name" name="" placeholder="Nom du produit" value="" />
        </div>
        <div class="col-md-4">
          <input type="text" class="form-control item-price" name="" placeholder="0.00" />
        </div>
        <div class="col-12">
          <textarea class="form-control item-description shadow-none" name="" placeholder="Description" value=""></textarea>
        </div>
        <div class="col-12">
          <select id="" class="form-control item-image" name="">
            <?php $list = array_diff( scandir( 'assets/img/icons' ), array( '..' ) ); ?>
            <?php foreach ( $list as $value ) { echo "<option value=\"{$value}\">{$value}</option>"; } ?> 
          </select>
        </div>
        <div class="col-md-10 mt-3 mb-1">
          <a href="" class="add-option">(+) Ajouter une option</a>
        </div>
        <div id="" class="options col-auto flex-fill"></div>
      </div>
    </div>
  </div>
</template>

<template id="option">
  <div class="option row mb-4">
    <div class="col-auto m-0 text-end pe-0">
      <button class="delete-option btn btn-sm btn-outline-danger" name="">
    </div>
    <div class="col text-start">
      <div class="row g-2">
        <div class="col-12">
          <input type="text" class="form-control item-option-name" name="" placeholder="Nom de l'option" value="" />
        </div>
        <div class="col-md-10 mt-3 mb-1">
          <a href="" class="add-select">(+) Ajouter un choix</a>
        </div>
        <div id="" class="select-list col-12">
        </div>
      </div>
    </div>
  </div>
</template>

<template id="select">
  <div class="select row mb-2">
    <div class="col-auto m-0 text-end pe-0">
      <button class="delete-select btn btn-sm btn-block btn-outline-danger" name=""></button>
    </div>
    <div class="col">
      <div class="row g-2">
        <div class="col-md-8">
          <input type="text" class="form-control item-options-select-name" name="" placeholder="Nom de la sélection" value="" />
        </div>
        <div class="col-md-4">
          <input type="text" class="form-control item-options-select-price" name="" placeholder="0.00" />
        </div>
      </div>
    </div>
  </div>
</template>
