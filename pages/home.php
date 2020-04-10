<?php
require_once ("../config/userclass.php");

require ("./sessionRedirect.php");

    $user = new User();
    $username = $_SESSION['user_session'];
  
?>


<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home: <?php print($_SESSION['user_session']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.min.css">
  <link rel="stylesheet" href="../css/styles.css">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>

<body class="has-navbar-fixed-top">
  <?php include_once "navbar.php" ?>
  <div id="body-container" class="container is-fullhd">
    <section class="section is-fullwidth">

    </section>
    <div class="push"></div>
  </div>
  <?php include "footer.php"?>
</body>

</html>