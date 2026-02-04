<?php require_once '_header.php' ?>

<body>
  <nav class="navbar navbar-default sticky-top mb-4" style="z-index:1025">
    <div class="container-fluid">
      <a class="navbar-brand text" href="/<?php echo $site_alias ?>"><?php echo $site_name ?></a>
      <span class="navbar-text text-white">
        <?php if( $_SESSION['logged_in'] ): ?>
          <?php echo $_SESSION['user_name'] ?>
          <a class="log-in-and-out" href="/src/sessions_quit">(Sign&nbsp;out)</a>
        <?php else: ?>
          <a class="log-in-and-out" href="/portal?captive_for=<?php echo $site_alias ?>">Sign&nbsp;in&nbsp;with&nbsp;WCA</a>
        <?php endif ?>
      </span>
    </div>
  </nav>