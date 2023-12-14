<?php 
  
  require_once dirname( __DIR__, 3 ) . '/src/sessions/session-handler.php'; 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title>Registration Handler</title>
    <meta name="author" content="ML" />
    <meta name="Description" content="Manage on the spot registrations for WCA competitions easily" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://#BASE_URL#/assets/js/jquery-3.6.3.min.js"></script>
    <link href="assets/css/custom.css" rel="stylesheet">
  </head>

  <body>
<body>
    <nav class="navbar navbar-default sticky-top mb-4">
      <div class="container-fluid">
        <a class="navbar-brand text" href="https://<?php echo $_SERVER['SERVER_NAME']; ?>">Registration Handler</a>
        <span class="navbar-text text-black">
          <?php if ( $_SESSION['logged_in'] ): ?>
            <?php echo $_SESSION['user_name']; ?> <a class="log-in-and-out" href="src/sessions/session-quit">(Log Out)</a>
          <?php else: ?>
            <a class="log-in-and-out" href="https://<?php echo $_SERVER['SERVER_NAME']; ?>/src/oauth/">Sign In With WCA</a>
          <?php endif; ?>
        </span>
      </div>
    </nav>
    
    <div class="container-fluid">