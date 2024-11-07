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
    </style>
</head>

<body>
<div data-role="page" class="ui-responsive-panel" id="r_tanque" data-dialog="true">
    <script>
        $(document).ready(function () {

            $(".table-stripe").stickyTableHeaders(); // Cabecera flotante o fija en la tabla
        });

    </script>
    <?php $rPare = $db->prepare("SELECT p_dni,p_nom,p_ape,p_med FROM hc_pareja");
    $rPare->execute(); ?>
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
        <?php
            if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
                print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } else {
                print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            }
        ?>
        <h3>REPORTE TANQUE SEMEN</h3>
    </div>

    <div class="ui-content" role="main">

        <form action="" method="post" data-ajax="false" id="form2">

            <input id="filtro" data-type="search" placeholder="Filtro..">
            <table width="100%" class="table-stripe" style="font-size: small" data-filter="true" data-input="#filtro">
                <thead>
                <tr class="ui-bar-b">
                    <th>APELLIDOS Y NOMBRES</th>
                    <th>DNI</th>
                    <th>MEDICO</th>
                    <th>Congelado<br>Biopsia</th>
                    <th>Congelado<br>Crio</th>
                    <th>Descongelado<br>Biopsia</th>
                    <th>Descongelado<br>Crio</th>
                </tr>
                </thead>
                <tbody>

                <?php while ($pare = $rPare->fetch(PDO::FETCH_ASSOC)) {
                    $c_bio = 0;
                    $c_cri = 0;
                    $des_bio = 0;
                    $des_cri = 0;
                    ini_set('max_execution_time', 300);
                    $rCon = $db->prepare("SELECT tip FROM lab_tanque_res WHERE sta=?");
                    $rCon->execute(array($pare['p_dni']));
                    if ($rCon->rowCount() > 0) {
                        while ($con = $rCon->fetch(PDO::FETCH_ASSOC)) {
                            if ($con['tip'] == 1) $c_bio++;
                            if ($con['tip'] == 2) $c_cri++;
                        }
                    }

                    $rDes = $db->prepare("SELECT des,des_tip,id,pro FROM lab_andro_cap WHERE des_dni=? and des<>'' and eliminado is false ORDER BY des_tip");
                    $rDes->execute(array($pare['p_dni']));
                    if ($rDes->rowCount() > 0) {
                        while ($des = $rDes->fetch(PDO::FETCH_ASSOC)) {
                            $n_des = explode('|', $des['des']);
                            $total = count($n_des) - 1;
                            if ($des['des_tip'] == 1) $des_bio++;
                            if ($des['des_tip'] == 2) $des_cri++;
                           // if ($des['pro'] <> "" and $des['pro'] <> 0) $des_pro = " (Protocolo " . $des['pro'] . ")";
                        }
                    }

                    if ($c_bio>0 or $c_cri>0 or $des_bio>0 or $des_cri>0) {
                        echo '<tr>';
                        echo '<td>'.$pare['p_ape'].' '.$pare['p_nom'].'</td>';
                        echo '<td>'.$pare['p_dni'].'</td>';
                        echo '<td>'.$pare['p_med'].'</td>';
                        echo '<td align="center">'.$c_bio.'</td>';
                        echo '<td align="center">'.$c_cri.'</td>';
                        echo '<td align="center">'.$des_bio.'</td>';
                        echo '<td align="center">'.$des_cri.'</td>';
                        echo '</tr>';
                    }
                } ?>
                </tbody>
            </table>
        </form>

    </div><!-- /content -->

</div><!-- /page -->

</body>
</html>