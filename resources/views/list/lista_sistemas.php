<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
  <head>
      <?php
          $login = $_SESSION['login'];
          $dir = $_SERVER['HTTP_HOST'].substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
          if ($login <> "sistemas") {
              print("<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>");
          }
      ?>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="icon" href="_images/favicon.png" type="image/x-icon">
      <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
      <link rel="stylesheet" type="text/css" href="css/global.css">
  </head>
  <body class="body_adminlab">
      <?php require ('_includes/menu_sistemas.php'); ?>
      <div class="container">
        <div data-role="header">
            <h3>Perfil de Sistemas</h3>
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