<!DOCTYPE HTML>
<html>
<head>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" id="ayuda" data-dialog="true">

        <style>
            .ui-dialog-contain {
                max-width: 1200px;
                margin: 1% auto 1%;
                padding: 0;
                position: relative;
                top: -35px;
            }
            .enlinea div {
                display: inline-block;
                vertical-align: middle;
            }
        </style>

        <script>
            $(document).ready(function () {
                $('#form1').submit(function () {
                    if ($("#archivo").val() != '') {
                        $("#cargador").popup("open", {positionTo: "window"});
                        return true;
                    }
                });
            });

            function anular(x) {
                document.form1.borra.value = x;
                document.form1.archivo.value = ""; //para que no inserte registros
                document.form1.submit();
            }
        </script>

        <?php
        if (isset($_POST['guardar']) and !empty($_POST['guardar']) and $_POST['guardar']=='CARGAR') {
            if ($_FILES['archivo']['name'] <> "") {
                if (is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                    $ruta = 'paci/' . $_FILES['archivo']['name'];
                    move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta);
                }
            }
        }

        if (isset($_POST['borra']) and !empty($_POST['borra'])) {
            unlink("paci/".$_POST['borra']);
        }

        $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
        $rUser->execute(array($login));
        $user = $rUser->fetch(PDO::FETCH_ASSOC); ?>

        <div data-role="header" data-position="fixed">
            <?php
            if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
                print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } else {
                print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } ?>
            <h1>AYUDA</h1>
        </div>
        <div class="ui-content" role="main">
            <form action="ayuda.php" method="post" enctype="multipart/form-data" data-ajax="false" id="form1" name="form1">
                <input type="hidden" name="borra">
                <?php
                if (true) { ?>
                    <div class="enlinea">
                        <input name="archivo" id="archivo" type="file" accept="video/mp4" required data-mini="true" data-inline="true"/>
                        <input name="guardar" type="Submit" id="guardar" value="CARGAR" data-icon="check" data-iconpos="left" data-inline="true" data-theme="b" data-mini="true"/>
                    </div>
                <?php } ?>

                <?php
                    $path = "paci/";
                    $files = array_diff(scandir($path), array('.', '..'));
                    print("<ul>");
                    foreach ($files as $value) {
                        if (!is_dir("paci/".$value)) {
                            print("<li><a href='"."archivos_hcpacientes.php?idPaci=".$value."' target='_blank' >".$value."</a></li>");
                        }
                    }
    
                    print("
						<li><a href='archivos_hcpacientes.php?idPaci=consulta-induccion-agenda.mp4' target='_blank' >Agendar consulta médica</a>
						<li><a href='https://drive.google.com/file/d/1mSI0lJMtQSIkWDpX19ig3UJLC8JCl_Am/view' target='_blank' >Turnos en Sala Procedimientos - Perfil Médico</a>");
                    print("<li><a href='https://drive.google.com/file/d/1wB5sb6yGyL4eFAMJwnIisIvPwL_2Eq3c/view' target='_blank' >Turnos en Sala Procedimientos - Perfil Urología</a>");
                    print("</ul>"); ?>
            </form>

            <div data-role="popup" id="cargador" data-overlay-theme="b" data-dismissible="false"><p>SUBIENDO ARCHIVO..</p></div>
        </div>
    </div>
</body>
</html>