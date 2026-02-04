<template id="block">
  <div id="" class="block mb-3 card">
    <div class="card-body">
      <div class="col-12">
        <div class="alert alert-secondary mb-3 mb-0 p-2 sticky-top">
          <div class="row bg-transparent">
            <div class="col-12 col-md-7 col-lg-8 mb-2 mb-md-0">
              <input id="" type="text" class="form-control block-name border-0 bg-transparent text-uppercase fw-bold" placeholder="Nom du bloc" name="" value="">
            </div>
            <div class="col text-end">
              <button class="add-item btn btn-success mb-1 mb-xl-0 me-1" name="" title="Ajouter un produit">&#65291;</button>
              <button class="delete-block btn btn-danger mb-1 mb-xl-0 me-1" name="" title="Supprimer le bloc">&#10007;</button>
              <button class="clone-block btn btn-secondary mb-1 mb-xl-0 me-1" name="" title="Cloner le bloc">&#8631;</button>
              <button class="move-block-up btn btn-outline-secondary mb-1 mb-xl-0 me-1" title="Monter le bloc">&uarr;</button>
              <button class="move-block-down btn btn-outline-secondary mb-1 mb-xl-0 me-1" title="Descendre le bloc">&darr;</button>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div id="" class="row item-list">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<template id="item">
  <div id="" class="item col-12 mb-2 px-4">
  <div class="row">
    <div class="col-auto m-0 text-start text-md-end p-0">
      <button class="delete-item btn btn-danger btn-block" name=""></button>
    </div>
    <div class="col pe-0">
      <div class="row g-2 text-start">
        <div class="col-md-6">
          <input type="text" class="form-control item-name" name="" placeholder="Nom du produit" value="" />
        </div>
        <div class="col">
          <select id="" class="form-control item-image" name="">
            <?php $list = array_diff( scandir( 'assets/img/icons' ), array( '.', '..' ) ); ?>
            <?php foreach( $list as $value ) { echo "<option value=\"{$value}\">{$value}</option>"; } ?> 
          </select>
        </div>
        <div class="col-auto">
          <input type="text" class="form-control item-price" name="" placeholder="0.00" />
        </div>
        <div class="col-12">
          <textarea class="form-control item-description shadow-none" name="" placeholder="Description" value=""></textarea>
        </div>
        <div class="col-md-10 mb-1">
          <a href="" class="add-option">(+) Ajouter une option</a>
        </div>
        <div id="" class="options col-auto flex-fill"></div>
      </div>
    </div>
  </div>
</template>

<template id="option">
  <div class="option row">
    <div class="col-auto m-0 text-end pe-0">
      <button class="delete-option btn btn-outline-danger" name="">
    </div>
    <div class="col text-start">
      <div class="row g-2">
        <div class="col-12">
          <input type="text" class="form-control item-option-name" name="" placeholder="Nom de l'option" value="" />
        </div>
        <div class="col-md-10 mb-1">
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
      <button class="delete-select btn btn-block btn-outline-danger" name=""></button>
    </div>
    <div class="col">
      <div class="row g-2">
        <div class="col">
          <input type="text" class="form-control item-options-select-name" name="" placeholder="Nom de la sélection" value="" />
        </div>
        <div class="col-auto">
          <input type="text" class="form-control item-options-select-price" name="" placeholder="0.00" />
        </div>
      </div>
    </div>
  </div>
</template>
