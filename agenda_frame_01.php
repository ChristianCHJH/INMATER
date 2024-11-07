<?php
session_start();
ini_set("display_errors","1");
error_reporting(E_ALL);
?>
<!DOCTYPE HTML>
<html>
<head>
    <?php
        $login = $_SESSION['login'];
        $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
        if (!$login) {
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
        } else {
            if (isset($_GET['med']) && !empty($_GET['med']))
            {
                $login=$_GET['med'];
            }
            require("_database/db_tools.php");
        }
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
    <div data-role="page" class="ui-responsive-panel" id="agenda_frame" data-dialog="true">
        <style>
            .ui-dialog-contain {
                max-width: 900px;
                margin: 1% auto 1%;
                padding: 0;
                position: relative;
                top: -35px;
            }

            #alerta {
                background-color: #FF9;
                margin: 0 auto;
                text-align: center;
                padding: 4px;
            }
        </style>
        <?php
            //
            $medico="";
            if (isset($_GET["med"])) {
                $medico=$_GET["med"];
            }
        ?>
        <div data-role="header" data-position="fixed">
            <a href="lista.php" rel="external" class="ui-btn">Cerrar</a>
            <h1>Agenda <?php echo '(' . $login . ')'; ?></h1>
        </div>
        <div class="ui-content" role="main">
            <form action="" method="get" data-ajax="false" name="form1" id="form1">
                <table>
                    <tr>
                        <td><b>Médico: </b></td>
                        <td>
                            <select name="med" id="ini_h" data-mini="true">
                                <option value="">Seleccionar</option>
                                <?php
                                    $consulta = $db->prepare("select userx, nom from usuario where role = 1");
                                    $consulta->execute();
                                    $data = $consulta->fetchAll();
                                    foreach ($data as $info) {?>
                                        <option value=<?php echo $info['userx'];
                                        if ($medico == $info['userx']) echo " selected"; ?>><?php echo $info['nom']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><input type="submit" data-mini="true" value="Buscar"></td>
                    </tr>
                </table>
            </form>
            <iframe src="agenda.php?med=<?php print($medico); ?>" width="100%" height="800" seamless></iframe>
        </div>
        <script>
            $(function () {
                $('#alerta').delay(3000).fadeOut('slow');
            });//]]>
        </script>
    </div>
</body>
</html>