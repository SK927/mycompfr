<?php require_once '_header.php' ?>

<body>
  <nav class="navbar navbar-default sticky-top mb-4">
    <div class="container-fluid">
      <a class="navbar-brand text" href="https://<?php echo "{$_SERVER['SERVER_NAME']}/{$site_alias}" ?>"><?php echo $site_name ?></a>
      <span class="navbar-text text-white">
        <?php if ( $_SESSION['logged_in'] ): ?>
          <?php echo $_SESSION['user_name'] ?> <a class="log-in-and-out" href="https://<?php echo $_SERVER['SERVER_NAME'] ?>/src/sessions_quit">(Sign out)</a>
        <?php else: ?>
          <a class="log-in-and-out" href="https://<?php echo "{$_SERVER['SERVER_NAME']}/portal?captive_for={$site_alias}" ?>">Sign in with WCA</a>
        <?php endif ?>
      </span>
    </div>
  </nav>