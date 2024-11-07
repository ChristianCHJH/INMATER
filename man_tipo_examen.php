<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php';
    require "_database/database.php"; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>

    <style>
        .ui-dialog-contain {
            width: 100%;
            max-width: 800px;
            margin: 2% auto 15px;
            padding: 0;
            position: relative;
            top: -15px;
        }
        #alerta {
            background-color:#FF9;
            margin: 0 auto;
            text-align:center;
            padding:4px;
        }
    </style>

    <script language="JavaScript" type="text/javascript">
        function anular(id) {
            document.form2.borrar.value = id;
            document.form2.nombre.value = "";
            document.form2.submit();
        }
    </script>
</head>

<body>
    <?php
    if (!!$_GET && !!($_GET['padre_id'])) {
        $padre_id = $_GET['padre_id'];
    } else {
        print('no se encontraron datos del tipo de examen'); exit();
    } ?>
    <div data-role="page" class="ui-responsive-panel" id="e_analisis_tip" data-dialog="true">

        <div data-role="header" data-position="fixed">
            <?php
            if (isset($_GET["path"]) && !empty($_GET["path"])) {
                print('<a href="' . $_GET["path"] . '.php?dni=' . $_GET["dni"] . '" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } ?>
            <h1>Tipos de Exámenes</h1>
        </div>

        <div class="ui-content" role="main">
            <?php
            if (isset($_POST['borrar']) and !empty($_POST['borrar'])) {
                $stmt = $db->prepare("UPDATE man_tipo_examen SET estado = 0, iduserupdate = ? WHERE id = ?;");
                $stmt->execute([$login, $_POST['borrar']]);
            }

            if (isset($_POST['nombre']) and !empty($_POST['nombre'])) {
                $stmt = $db->prepare("INSERT INTO man_tipo_examen (padre_id, nombre, idusercreate) VALUES (?, ?, ?)");
                $stmt->execute([$padre_id, $_POST['nombre'], $login]);
                print("<div id='alerta'>¡Exámen Agregado!</div>");
            } ?>

            <form action="" method="post" name="form2" data-ajax="false">
                <input type="hidden" name="borrar">
                <?php print('<input type="hidden" name="padre_id" value="' . $padre_id . '">'); ?>

                <div class="ui-bar ui-bar-a">
                    <table style="margin: 0 auto;" width="100%">
                        <tr>
                            <td width="20%">Nombre de Tipo de Exámen</td>
                            <td width="67%"><input name="nombre" type="text" id="nombre" data-mini="true" required/></td>
                            <td width="13%"><input name="guardar" type="Submit" id="guardar" value="Agregar"  data-icon="check" data-iconpos="left" data-inline="true" data-mini="true"/></td>
                        </tr>
                    </table>
                </div>

                <ul data-role="listview" data-inset="true" data-mini="true" >
                    <?php
                    $stmt = $db->prepare("SELECT * FROM man_tipo_examen WHERE estado = 1 and padre_id = ? ORDER BY nombre ASC");
                    $stmt->execute([$padre_id]);

                    while ($med = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        print('
                        <li style="margin: 0 auto;width:90%;">
                            <h5>' . $med['nombre'] . '</h5>
                            <span class="ui-li-count"><a href="javascript:anular(' . $med['id'] . ');" data-theme="a">[Eliminar]</a></span>
                        </li>');
                    }

                    if ($stmt->rowCount() < 1) {
                        echo '<p><h3 class="text_buscar">¡ No hay registros !</h3></p>';
                    } ?>
                </ul>
            </form>
        </div>

        <script>
        $(function(){
            $('#alerta').delay(3000).fadeOut('slow');
        });
        </script>
    </div>

</body>
</html>