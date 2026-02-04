<?php 

  error_reporting( E_ERROR );
  ini_set( "display_errors", 1 );

  $site_name = 'Commande Utile';
  $site_alias = 'commande-utile';

  $warning_condition = ($_SESSION['logged_in'] and ! isset( $_SESSION['user_email'] ));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title><?php echo $site_name ?></title>
    <meta name="author" content="SK927" />
    <meta name="Description" content="Site de commande pour les compétitions WCA en France" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once dirname( __DIR__, 2 ) . '/src/bootstrap_css-include.php' ?>
    <?php require_once dirname( __DIR__, 2 ) . '/src/jquery_js-include.php' ?>
    <link href="../assets/css/generic.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans&display=swap" rel="stylesheet">
  </head>

  <body>
    <?php if( $warning_condition ): ?>
      <div class="container-fluid py-3 bg-warning border-bottom text-dark">
        <b>AVERTISSEMENT :</b> Au moins un paramètre de votre profil n'a pas pu être récupéré lors de votre connexion à MyComp. Cause probable : vous vous êtes d'abord connecté à un outil (ex : <?php echo $_SESSION['captive'] ?>) n'utilisant pas ce paramètre. Merci de vous reconnecter pour éviter tout disfonctionnement lors de l'utilisation de <?php echo $site_name ?>.
      </div>
    <?php endif ?>
    <nav class="navbar navbar-default sticky-top mb-4" style="z-index:1025">
      <div class="container-fluid">
        <a class="navbar-brand text" href="/<?php echo $site_alias ?>"><?php echo $site_name ?></a>
        <span class="navbar-text text-white">
          <?php if( $_SESSION['logged_in'] ): ?>
            <?php echo $_SESSION['user_name'] ?>
            <a class="log-in-and-out" href="/src/sessions_quit">(Se&nbsp;déconnecter)</a>
          <?php else: ?>
            <a class="log-in-and-out" href="/portal?captive_for=<?php echo $site_alias ?>&locale=fr">Se&nbsp;connecter&nbsp;avec&nbsp;la&nbsp;WCA</a>
          <?php endif ?>
        </span>
      </div>
    </nav>
