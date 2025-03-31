<?php 

  require_once 'sessions_handler.php';

  $site_name = 'MyComp';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title><?php echo $site_name ?></title>
    <meta name="author" content="ML" />
    <meta name="Description" content="Centralized platform for WCA competitions tools" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once dirname( __FILE__ ) . '/bootstrap_css-include.php' ?>
    <?php require_once dirname( __FILE__ ) . '/jquery_js-include.php' ?>
    <link href="assets/css/custom.css" rel="stylesheet">
  </head>

  <body>
    <nav class="navbar navbar-default sticky-top mb-4">
      <div class="container-fluid">
        <a class="navbar-brand text" href="https://<?php echo $_SERVER["SERVER_NAME"] ?>"><?php echo $site_name ?></a>
        <span class="navbar-text text-white">
          <?php if ( $_SESSION['logged_in'] ): ?>
            <?php echo $_SESSION['user_name'] ?> <a class="log-in-and-out" href="src/sessions_quit">(Sign out)</a>
          <?php endif ?>
        </span>
      </div>
    </nav>
    