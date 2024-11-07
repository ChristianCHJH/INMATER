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
    <link rel="stylesheet" href="css/labo_testigo_biopsia.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="jstickytableheaders.js"></script>
</head>
<body>
<div data-role="page" class="ui-responsive-panel" id="r_tanque" data-dialog="true">
    <script>
        $(document).ready(function () {
            $(".table-stripe").stickyTableHeaders(); // Cabecera flotante o fija en la tabla
        });
        function anular(id) {
            if (confirm("¿Está seguro que quiere eliminar este registro?")) {
                document.form2.conf.value = id;
                document.form2.submit();
            } else {
                return false;
            }
        }
    </script>
    <?php
        if (isset($_POST['conf']) and !empty($_POST['conf']))
        {
            require("_database/db_mantenimiento.php");
            testigoBiopsiaEliminar($_POST['conf'], mb_strtolower($login));
        }

        if (isset($_POST['nom']) and !empty($_POST['nom']))
        {
            require("_database/db_mantenimiento.php");
            testigoBiopsiaInsertar(mb_strtolower($_POST['nom']), mb_strtolower($login));
        }
	?>
    <div data-role="header">
        <a href="lista-admin.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
        <h3>MANTENIMIENTO DE TESTIGOS PARA BIOPSIA</h3>
    </div>
    <div class="ui-content" role="main">
        <form action="" method="post" data-ajax="false" name="form1">
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
        <form action="" method="post" data-ajax="false" name="form2">
            <input type="hidden" name="conf">
            <input id="filtro" data-type="search" placeholder="Filtro..">
            <table width="100%" class="table-stripe" style="font-size: small" data-filter="true" data-input="#filtro">
                <thead>
                    <tr class="ui-bar-b">
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $consulta = $db->prepare("select id, nombre from labo_testigo_biopsia where estado = 1");
                    $consulta->execute();
                    while ($item = $consulta->fetch(PDO::FETCH_ASSOC))
                    {
                        print("<tr><td>".mb_strtoupper($item["nombre"])."</td><td><a href='javascript:anular(".$item["id"].");'>Eliminar</a></td></tr>");
                    }
                ?>
                </tbody>
            </table>
        </form>
    </div>
</div>
</body>
</html>