<?php
session_start();
unset($_SESSION['login_paciente']);

error_reporting( error_reporting() & ~E_NOTICE );
/* ini_set("display_errors","1");
error_reporting(E_ALL); */
require("_database/db_login.php"); ?>
<!DOCTYPE HTML>
<html>
<head>
	<title>INMATER</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="_themes/tema_inmater.min.css" />
	<link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="css/global.css" />

	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script language="javascript" type="text/javascript"> 
		function validForm(form) {
			if (form.login.value == "") {
				alert("Debe llenar el campo 'Usuario'");
				return false;
			} else if (form.pass.value == "") {
				alert("Debe llenar el campo 'Contraseña'");
				return false;
			} else {return true;}
		}
	</script>
</head>

<body>
	<?php 
	if ((isset($_POST['login']) && !empty($_POST['login'])) && autenticar($_POST['login'], $_POST['pass'])) {
		ingresar_log($_POST['login']);
		$_SESSION['login_paciente'] = strtolower(trim(preg_replace('/\s+/',' ', $_POST['login'])));
		print("<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT'])) . "/lista_paciente.php'>");
	} ?>

	<div data-role="page" class="ui-responsive-panel">
		<div data-role="header" data-position="fixed"><h1 style="color: #fff;">Clínica Inmater</h1></div>
		<div class="ui-content" role="main">
			<div class="ui-grid-b">
				<div class="ui-block-a"></div>
				<div class="ui-block-b">
					<form action="" method="post" data-ajax="false">
						<img src="_images/logo_login_sinfondo.png" width="236" heigth="76" style="display: block; margin: 10px auto;"/>
						<label for="login">Usuario</label>
						<input name="login" type="text" id="login" tabindex="1">
						<label for="pass">Contraseña</label>
						<input name="pass" type="password" id="pass" tabindex="2">

						<?php
						if (isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['pass']) && !empty($_POST['pass'])) {
							if (!autenticar($_POST['login'], $_POST['pass'])) {
								echo '<p style="color: red;font-size: 14px;margin: 5px auto;">El usuario y/o contraseña son incorrectos!</p>';
							}
						} ?>
						<input name="Entrar" type="Submit" onClick="return validForm(this.form)" value="Entrar" data-icon="check" data-iconpos="left" data-mini="true" data-inline="true"  data-theme="c"/>
					</form>
				</div>

				<div class="ui-block-c"></div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function () {
			$("#login").focus();
		});
	</script>
</body>
</html>