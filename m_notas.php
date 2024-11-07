<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="jstickytableheaders.js"></script>
    <style>
        .controlgroup-textinput {
            padding-top: .10em;
            padding-bottom: .10em;
        }

        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }

        #ser {
            font-size: 12px;
        }
        #alerta {
            background-color: #FF9;
            margin: 0 auto;
            text-align: center;
            padding: 4px;
        }
    </style>
</head>
<body>
<div data-role="page" class="ui-responsive-panel" id="r_tanque" data-dialog="true">
    <script>
        $(document).ready(function () {
            $(".table-stripe").stickyTableHeaders(); // Cabecera flotante o fija en la tabla
        });
        function anular(id) {
            document.form2.conf.value = id;
            document.form2.submit();
        }
    </script>
    <?php
        //
        if (isset($_POST['conf'])) {
            if ($_POST['conf'] <> "") {
                man_notas_eliminar($_POST['conf'], mb_strtolower($login));
            }
        }
        if (isset($_POST['nom'])) {
            if ($_POST['nom'] <> "") {
                man_notas_insertar(mb_strtolower($_POST['nom']), mb_strtolower($login));
            }
        }
    	$rPare = $db->prepare("select * from man_notas where estado = 1");
    	$rPare->execute();
	?>
    <style>
        .ui-dialog-contain {
            max-width: 1200px;
            margin: 1% auto 1%;
            padding: 0;
            position: relative;
            top: -5px;
        }
        .peke2 .ui-input-text {
            width: 60px !important;
        }
    </style>

    <div data-role="header">
        <a href="lista-admin.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
        <h3>MANTENIMIENTO DE NOTAS</h3>
    </div>
    <div class="ui-content" role="main">
        <form action="m_notas.php" method="post" data-ajax="false" name="form1">
            <input type="hidden" name="nom" id="nom">
            <div class="ui-bar ui-bar-a">
                <table style="margin: 0 auto;" width="100%">
                    <tr>
                        <td width="48%">Nombre
                            <input type="text" name="nom" id="nom" data-mini="true" required/>
                        </td>
                        <td width="7%">
                            <input type="Submit" name="agregar" value="Agregar" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
                        </td>
                    </tr>

                </table>
            </div>
        </form>
        <form action="m_notas.php" method="post" data-ajax="false" name="form2">
            <input type="hidden" name="conf">
            <input id="filtro" data-type="search" placeholder="Filtro..">
            <table width="100%" class="table-stripe" style="font-size: small" data-filter="true" data-input="#filtro">
                <thead>
                    <tr class="ui-bar-b">
                        <th>Descripción</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                <?php
	            	while ($pare = $rPare->fetch(PDO::FETCH_ASSOC)) {
	            		print("<tr><td>".mb_strtoupper($pare["nombre"])."</td><td><a href='javascript:anular(".$pare["id"].");'>Eliminar</a></td></tr>");
	                }
                ?>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    $(function () {
        $("#alerta").prependTo(".ui-content");
        $('#alerta').delay(3000).fadeOut('slow');
    });
</script>
</body>
</html>