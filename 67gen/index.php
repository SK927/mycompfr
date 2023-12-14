<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title>67Gen</title>
    <meta name="author" content="ML" />
    <meta name="Description" content="Generate WCA competitions dual scorecards" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src='https://#BASE_URL#/assets/js/jquery-3.6.3.min.js'></script>
  </head>

  <body>
    <div class="container text-center">
      <form class="row justify-content-center" action="src/admin/generate_scorecards.php" method="POST">
        <div class="col col-md-10 col-lg-8 col-xl-6 mx-auto my-2">
          <div class="col mt-4 fw-bold">Provide competition URL to generate dual scorecards for selected events</div>
          <input class="form-control text-center" type="text" name="competition_url" placeholder="https://www.worldcubeassociation.org/competitions/MyComp<?php echo date( 'Y' ); ?>"></input>
          <div class="col-auto mt-2">
            <input id="event1" class="form-check-input" type="radio" name="event_select" value="6x7" checked>
            <label class="form-check-label" for="event1">6x6 and 7x7</label>
          </div>
          <div class="col-auto mb-4">
            <input id="event2" class="form-check-input" type="radio" name="event_select" value="bld">
            <label class="form-check-label" for="event2">4x4 and 5x5 blindfolded</label>
          </div>
          <div class="col-auto">
            <button class="btn btn-light">Send</button>
          </div>
        </div>
      </form>
      <footer class="my-4 py-3">
        <ul class="nav justify-content-center mb-3 pb-3">
          <li class="nav-item">
            <a href="https://#SUB_DOMAIN#.#BASE_URL#" class="nav-link px-2 text-muted">© 67Gen 2023-<?php echo date( 'Y' ); ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-2 text-muted" href="mailto:mlefebvre@worldcubeassociation.org">Maxime Lefebvre</a>
          </li>
          <li class="nav-item">
            <a href="https://github.com/SK927/mycompfr" class="nav-link px-2 text-muted">Github</a>
          </li>
        </ul>
      </footer>
    </div>  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
</html>
