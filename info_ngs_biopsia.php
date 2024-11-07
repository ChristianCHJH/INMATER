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
    <div data-role="page" class="ui-responsive-panel" id="info_ngs_biopsia">
        <?php

            if (isset($_GET['repro']) and !empty($_GET['repro'])) {
                $repro = $_GET['repro'];
            } else {
                print("La reproducción no existe"); exit();
            }
            // 
            if (isset($_POST['guardar'])) {
                // var_dump($_POST); exit;
                require("_database/db_info_ngs_action.php");
                testigoBiopsiaDesarrolloInsertar($repro, "5", $_POST['testigodia5'], $login);
                testigoBiopsiaDesarrolloInsertar($repro, "6", $_POST['testigodia6'], $login);
                pruebaBiopsiaDesarrolloInsertar($repro, $_POST['pruebabiopsia'], $_POST['correlativobiopsia'], $_POST['observacionbiopsia'], $login);
                
                $demo = explode("|", $_POST['ovos']);
                for ($i=0; $i < $_POST['novos']; $i++) {
                    observacionBiopsiaDesarrolloInsertar($repro, $demo[$i], $_POST['observaciones'.$demo[$i]], $login);
                }
            }

            require("_database/db_info_ngs.php");
        ?>
        <div data-role="header" data-position="fixed">
            <?php print('<a href="'.$_GET["path"].'.php?id='.$_GET["pro"].'" data-icon="back" class="ui-icon-alt" data-theme="a" rel="external">volver</a>'); ?>
            <h2>Informe NGS</h2>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
            <div style="background-color:#d7e5e5; width:100%; font-size:13px; text-align:center;">
                <?php print(mb_strtoupper($datapaciente['apellidos'])." ".mb_strtoupper($datapaciente['nombres'])." (".$datapaciente['edad'].") / ".mb_strtoupper($datapareja['apellidos'])." ".mb_strtoupper($datapareja['nombres'])." (MÉDICO: ".mb_strtoupper($datamedico['nombrescompletos']).")"); ?>
            </div>
        </div>
        <?php
        $correlativobiopsia = "";
        $observacionbiopsia = "";
        if(count(pruebaBiopsiaDesarrolloListar($repro)) !== 0) {
            $correlativobiopsia = pruebaBiopsiaDesarrolloListar($repro)['correlativo'];
            $observacionbiopsia = pruebaBiopsiaDesarrolloListar($repro)['observacion'];
        } ?>
        <div class="ui-content" role="main">
            <form action="info_ngs_biopsia.php?repro=<?php print($repro); ?>&path=<?php print($_GET["path"]); ?>&pro=<?php print($_GET["pro"]); ?>" method="post">
                <table width="50%" align="center" style="margin: 0 auto;font-size:small;">
                    <tr>
                        <td width="10%">Correlativo Biopsia:</td>
                        <td width="10%">
                            <?php print('<input type="text" name="correlativobiopsia" value="'.$correlativobiopsia.'">'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="10%">Observación Biopsia:</td>
                        <td width="10%">
                            <?php print('<input type="text" name="observacionbiopsia" value="'.$observacionbiopsia.'">'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="10%">Testigo (Día 5):</td>
                        <td width="10%">
                            <select name="testigodia5" data-mini="true">
                                <option value="">SELECCIONAR</option>
                                <?php
                                foreach ($datatestigo as $item) { ?>
                                    <option value=<?php print $item['id']; if (testigoBiopsiaDesarrolloListar($repro, '5')['id'] == $item['id']) print(" selected"); ?>>
                                        <?php print(mb_strtoupper($item['nombre'])); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="10%">Testigo (Día 6):</td>
                        <td width="10%">
                            <select name="testigodia6" data-mini="true">
                                <option value="">SELECCIONAR</option>
                                <?php
                                foreach ($datatestigo as $item) { ?>
                                    <option value=<?php print $item['id']; if (testigoBiopsiaDesarrolloListar($repro, '6')['id'] == $item['id']) print(" selected"); ?>>
                                        <?php print(mb_strtoupper($item['nombre'])); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="10%">Prueba Biopsia:</td>
                        <td width="10%">
                            <select name="pruebabiopsia" data-mini="true">
                                <option value="">SELECCIONAR</option>
                                <?php
                                foreach ($dataprueba as $item) { ?>
                                    <option value=<?php print $item['id']; if (pruebaBiopsiaDesarrolloListar($repro)['id'] == $item['id']) print(" selected"); ?>>
                                        <?php print(mb_strtoupper($item['nombre'])); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <div class="scroll_h" style="background-color:rgba(189,213,211,1.00)">
                    <table width="85%" align="center" style="margin: 0 auto;font-size:small;">
                        <thead>
                            <tr>
                                <th align="center" width="25%">Indicación del Embrion<br>(Iniciales + Número):</th>
                                <th align="center" width="10%">Clasificación morfológica</th>
                                <th align="center" width="10%">Día de biopsia</th>
                                <th align="center" width="55%">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                print(detalleBiopsia($datadesarrollo['repro'], inicialesNombres($datapaciente['nombres']), true));
                            ?>
                        </tbody>
                    </table>
                </div>
                <input name="guardar" type="Submit" id="guardar" value="Guardar Informe" data-icon="check" data-iconpos="left" data-inline="true" data-theme="b" data-mini="true"/>
                <?php
                print('<br>
                <a href="info_ngs.php?repro='.$repro.'" target="_blank">
                    <img src="_images/pdf.png" height="35" width="35" alt="icon name">
                </a>') ?>
            </form>
        </div>
    </div>
</body>
</html>