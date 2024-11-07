<?php
// session_start(); 
// $_SESSION['username']="";
require("_database/db_tools.php");
?>
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
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script language="javascript" type="text/javascript"> 
	function validForm(form)
	{
		if (form.login.value == "") 
	    {
		   alert("Debe llenar el campo 'Usuario'");
			return false;
		}
		else if (form.pass.value == "") 
	    {
		   alert("Debe llenar el campo 'Contraseña'");
			return false;
		}
		else return true;
	}
	</script>
	<?php
	// $url = 'http://inmater.pe/inmater.intranet/public/api_login';
	$url = 'http://localhost/inmater/inmater.intranet/public/login_external';

    if (!isset($_SESSION['token'])) {
        $token = md5(uniqid(rand(), TRUE));
        // $token = bin2hex(random_bytes(32));
        $_SESSION['token'] = $token;
        $_SESSION['token_time'] = time();
    }
    else
    {
        $token = $_SESSION['token'];
    }

    $data = array('username' => 'silvana.sessarego@inmater.pe', 'password' => 'silvana123', 'X-CSRF-TOKEN' => $token);
    // $data = { username: "Some field value", _token: '{{csrf_token()}}' }

    // print($token);
    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n"."X-CSRF-TOKEN: ".$token,
            'X-CSRF-TOKEN'  => $token,
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { /* Handle error */ }
    print($result);

	/* if (($_POST['username']<>"") && authentification($_POST['username'], $_POST['password'])){
			// add_usuario_log(mb_strtolower($_POST['username']), 1, date("Y-m-d H:i:s"));
			//
			// $_SESSION['username'] = strtolower(trim(preg_replace('/\s+/',' ', $_POST['username'])));
			// $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
			if ($_POST['username'] == "adminlab") {
				echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://".$dir."/lista_adminlab.php'>";
				exit();
			} else {
				echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://".$dir."/lista.php'>";
			}
	} */
	?>
	</head>
	<body>
		<div data-role="page" class="ui-responsive-panel">
			<div data-role="header" data-position="fixed">
				<h1>INMATER</h1>
			</div>
			<div class="ui-content" role="main">
				<div class="ui-grid-b">
				    <div class="ui-block-a"></div>
				    <div class="ui-block-b">
					    <form action="http://inmater.pe/inmater.intranet/public/api/v1/login" method="post" data-ajax="false">
					    	<img src="_images/logo_login.jpg" width="100%"/>
					        <label for="login">Usuario:</label>
					        <input name="username" type="text" id="username" tabindex="1">
					        <label for="pass">Contraseña:</label>
					        <input name="password" type="text" id="password" tabindex="2">
					        <?php
					        /* if (isset($_POST['username']) && $_POST['password']) {
					        	if (!authentification($_POST['username'], $_POST['password']) and $_POST['username']<> "")
					        		echo "<font color='#FFD520'>Error de validación</font> &nbsp;&nbsp;";
					        } */
				        	?>
					        <input name="Entrar" type="Submit" onClick="return validForm(this.form)" value="Entrar" data-icon="check" data-iconpos="left" data-mini="true" data-inline="true"  data-theme="c"/>
					    </form>
				    </div>
				    <div class="ui-block-c"></div>
				</div>
			</div>
		</div>
	</body>
</html>