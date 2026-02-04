    <div class="container text-center">
      <footer class="py-3 my-4">
        <ul class="nav justify-content-center pb-3 mb-3">
          <li class="nav-item">
            <a href="https://<?php echo $_SERVER['SERVER_NAME'] ?>" class="nav-link px-2 text-muted">© MyComp 2021-<?php echo date( 'Y' ) ?></a>
          </li>
          <li class="nav-item">
            <a href="mailto:speedkubing927@gmail.com" class="nav-link px-2 text-muted">Maxime Lefebvre</a>
          </li>
          <li class="nav-item">
            <a href="https://github.com/SK927/mycompfr" class="nav-link px-2 text-muted">Github</a>
          </li>
          <li class="nav-item">
            <a href="/credits" class="nav-link px-2 text-muted">Credits</a>
          </li>
          <?php if( $_SESSION['can_manage'] ): ?>
            <li class="nav-item">
              <a href="/admin" class="nav-link px-2 text-muted">Admin</a>
            </li>
          <?php endif ?>
        </ul>
      </footer>
    </div>  
    <?php require_once dirname( __FILE__ ) . '/bootstrap_js-include.php' ?>
  </body>
</html>