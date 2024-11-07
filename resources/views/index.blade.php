<?php
if (!class_exists('Password')) {
    class Password {
        public static function hash($password) {
            return hash('sha512', $password);
        }
        public static function verify($password, $hash) {
            return ($hash == self::hash($password));
        }
    }
}
if (session_status() == PHP_SESSION_NONE) {
            session_start(); 
}
unset($_SESSION['login']);


error_reporting( error_reporting() & ~E_NOTICE ); 

if (isset($_POST) && !empty($_POST)) {
	if (isset($_POST["recordarme"]) && !empty($_POST["recordarme"])) {
		$params = session_get_cookie_params();
		setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60*24*30, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	} else {
		$params = session_get_cookie_params();
		setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60*2, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	}
} ?>
<!DOCTYPE HTML>
<html>
	<head>
	<title>INMATER - test </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="_themes/tema_inmater.min.css" />
	<link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
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
	<?php  

	
	 ?>
	</head>
	<body> {{ $name }}
		<div data-role="page" class="ui-responsive-panel">
			<div data-role="header" data-position="fixed"><h1>Especialistas en Medicina Reproductiva S.A.C.</h1></div>
			<div class="ui-content" role="main">
				<div class="ui-grid-b">
				    <div class="ui-block-a"></div>
				    <div class="ui-block-b">
					    <form action="validar-login" method="post" data-ajax="false">
					    	<img src="_images/logo_login_sinfondo.png"
								alt="logo-inmater"
								width="236"
								heigth="76"
								style="display: block; margin: 10px auto;"/>
					        <label for="login">Usuario</label>
					        <input name="login" type="text" id="login" tabindex="1">
					        <label for="pass">Contraseña</label>
					        <input name="pass" type="password" id="pass" tabindex="2">
					        <?php
									if (isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['pass']) && !empty($_POST['pass'])) {
										if (!authentification($_POST['login'], Password::hash($_POST['pass']))) {
											echo '<p style="color: red;font-size: 14px;margin: 5px auto;">El usuario y/o contraseña son incorrectos!</p>';
										}
									} ?>
							    <input type="checkbox" name="recordarme" id="recordarme" data-mini="true">
							    <label for="recordarme">No cerrar la sesión</label>
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