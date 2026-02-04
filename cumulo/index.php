<?php require_once 'src/_header.php' ?>  
  
<script src="assets/js/cumulative.js"></script>
<div class="container">
  <div class="row mt-4 text-center">
    <div class='col-12 col-md-4 mb-3'>
      <h5>CUMULATIVE</h5>
      <input class="form-control cumulative text-center mb-1" inputmode="numeric" type="text" name="cumulative" />
    </div>
    <div class='col-12 col-md-4 mb-3'>
      <h5>TIMES</h5>
      <input class="form-control result-attempt text-center mb-1" inputmode="numeric" tabIndex="1" type="text" />
      <input class="form-control result-attempt text-center mb-1" inputmode="numeric" tabIndex="2" type="text" />
      <input class="form-control result-attempt text-center mb-1" inputmode="numeric" tabIndex="3" type="text" />
      <input class="form-control result-attempt text-center mb-1" inputmode="numeric" tabIndex="4" type="text" />
      <input class="form-control result-attempt text-center mb-1" inputmode="numeric" tabIndex="5" type="text" />
      <button class="btn btn-light">Reset</button>
    </div>
    <div class='col-12 col-md-4 mb-3'>
      <h5>REMAINING</h5>
      <input id="remaining" class="form-control remaining-time text-center mb-1" type="text" name="remaining_time" disabled />
    </div>
  </div>
</div>

<?php require_once '../src/_footer.php' ?>
