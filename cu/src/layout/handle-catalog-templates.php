      <template id="block">
        <div id="" class="block col-12 card mt-2 p-0">
          <div class="card-header">
            <div class="row">
              <div class="col-lg-5"><input id="" type="text" class="form-control block-name mb-1 mb-xl-0 me-1" placeholder="Nom du bloc" name="" value=""></div>
              <div class="col-auto">
                <button class="add-item btn btn-sm btn-success mb-1 mb-xl-0 me-1" name="">Ajouter produit</button>
                <button class="delete-block btn btn-sm btn-sm btn-danger mb-1 mb-xl-0 me-1" name="">Suppr. bloc</button>
                <button class="clone-block btn btn-sm btn-secondary mb-1 mb-xl-0 me-1" name="">Cloner</button>
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
        <div id="" class="item col-lg-6 py-3 mb-2">
          <div class="row px-3 pb-3">
            <div class="col-md-2 m-0 text-end align-self-center">
              <button class="delete-item btn btn-sm btn-outline-danger" name=""></button>
            </div>
            <input type="hidden" class="form-control item-id" name="" value="" readonly="readonly" />
            <div class="col-md-6 my-0 p-1">
              <input type="text" class="form-control item-name" name="" placeholder="Nom du produit" value="" />
            </div>
            <div class="col-md-4 my-0 p-1">
              <input type="text" class="form-control item-price" name="" placeholder="0.00" />
            </div>
            <div class="col-md-10 offset-md-2 my-0 p-1">
              <input type="text" class="form-control item-description" name="" placeholder="Description" value="" />
            </div>
            <div class="col-md-10 offset-md-2 my-0 p-1">
              <select id="" class="form-control item-image" name="">
                <?php $list = array_diff( scandir( 'assets/img/icons' ), array( '..' ) ); ?>
                <?php foreach ( $list as $value ) { echo "<option value=\"{$value}\">{$value}</option>"; } ?> 
              </select>
            </div>
            <div class="add-option col-md-4 offset-md-2 my-0 p-1 text-start">+ Option</div>
          </div>
        </div>
      </template>
      
      <template id="option">
        <div class="col-12 option">
          <div class="row">
            <div class="col-md-2 offset-md-1 text-end align-self-center"><button class="delete-option btn btn-sm btn-outline-danger" name=""></div>
            <div class="col-md-3 my-0 p-1 text-left"><input type="text" class="form-control item-option-name" name="" placeholder="Nom option" value="" /></div>
            <div class="col-md-6 my-0 p-1 text-left"><input type="text" class="form-control item-option-value" name="" placeholder="Choix" value="" /></div>
          </div>
        </div>
      </template>
      