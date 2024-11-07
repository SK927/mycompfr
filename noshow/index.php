<?php 

  require_once 'src/layout/_header.php';

?>
      <form class="row justify-content-center" action="display-noshows.php" method="POST">
        <div class="col col-md-10 col-lg-8 col-xl-6 mx-auto my-2">
          <div class="col mt-4 fw-bold">Provide competition URL to generate no-shows list</div>
          <input class="form-control text-center" type="text" name="competition_url" placeholder="https://www.worldcubeassociation.org/competitions/MyComp<?php echo date( 'Y' ); ?>"></input>
          <div class="col-auto">
            <button class="btn btn-light mt-2">Send</button>
          </div>
        </div>
      </form>
<?php 

  require_once 'src/layout/_footer.php';

?>
