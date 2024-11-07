<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
  <head>
      <?php
          $login = $_SESSION['login'];
          $dir = $_SERVER['HTTP_HOST'].substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
          if ($login <> "admin") {
              print("<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>");
          }
      ?>
      <meta charset="UTF-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="icon" href="_images/favicon.png" type="image/x-icon">
      <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
      <link rel="stylesheet" type="text/css" href="css/global.css">
			<title>Clínica Inmater | Administración</title>
  </head>
  <body class="body_adminlab">
      <?php require ('_includes/menu-admin.php'); ?>
      <div class="container">
        <div data-role="header">
            <h3>Módulo de Administración</h3>
        </div>
        <div class="ui-content" role="main" id="imprime">
          <div class="scroll_h">
            <br/><br/>
          </div>
        </div>
      </div>
      <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
      <script src="js/popper.min.js" crossorigin="anonymous"></script>
      <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
  </body>
</html>