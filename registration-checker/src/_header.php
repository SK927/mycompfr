<?php 
  
  require_once dirname( __DIR__, 2 ) . "/src/sessions_handler.php";

  $site_name = 'Registration Checker';
  $site_alias = 'registration-checker'; 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title><?php echo $site_name ?></title>
    <meta name="author" content="ML" />
    <meta name="Description" content="Handle registrations confirmations to ensure competitors are coming" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once dirname( __DIR__, 2 ) . '/src/bootstrap_css-include.php' ?>
    <?php require_once dirname( __DIR__, 2 ) . '/src/jquery_js-include.php' ?>
    <link href="assets/css/custom.css" rel="stylesheet">
  </head>

  <body>
    <?php if ( ! isset( $_SESSION['user_email'] ) and $_SESSION['logged_in'] ): ?>
      <div class="container-fluid py-3 bg-warning border-bottom text-dark">
        <b>WARNING:</b> Your email address was not retrieved when you signed in to MyComp (probable cause: you first signed in to a tool that is not using the email address (<?php echo $_SESSION['captive'] ?>)). Please sign in again to avoid any malfunction when saving your response.
      </div>
    <?php endif ?>
    <nav class="navbar navbar-default sticky-top mb-4">
      <div class="container-fluid">
        <a class="navbar-brand text" href="https://<?php echo "{$_SERVER['SERVER_NAME']}/{$site_alias}" ?>"><?php echo $site_name ?></a>
        <span class="navbar-text text-white">
          <?php if ( $_SESSION['logged_in'] ): ?>
            <?php echo $_SESSION['user_name']; ?> <a class="log-in-and-out" href="https://<?php echo $_SERVER['SERVER_NAME'] ?>/src/sessions_quit">(Log out)</a>
          <?php else: ?>
            <a class="log-in-and-out" href="https://<?php echo "{$_SERVER['SERVER_NAME']}/portal?captive_for={$site_alias}" ?>">Sign in with WCA</a>
          <?php endif ?>
        </span>
      </div>
    </nav>
    