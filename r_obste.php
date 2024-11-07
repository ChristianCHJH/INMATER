<!DOCTYPE HTML>
<html>

<head>
    <?php 
        include 'seguridad_login.php'
         ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="jstickytableheaders.js"></script>
    <style>
    .scroll_h {
        overflow: auto;
    }

    #alerta {
        background-color: #FF9;
        margin: 0 auto;
        text-align: center;
        padding: 4px;
    }

    .mayuscula {
        text-transform: uppercase;
        font-size: small;
    }

    .Mostrar {
        background-color: #F0DF96 !important;
    }

    .enlinea div {
        display: inline-block;
        vertical-align: middle;
    }

    #num_pro {
        color: red;
    }
    </style>
    <script>
    $(document).ready(function() {
        $(".Mostrar").click(function() {
            $("#reporte").val(1);
            $('#form1').submit();
        });

        $(".table-stripe").stickyTableHeaders(); // Cabecera flotante o fija en la tabla
    });
    </script>
</head>

<body>
    <?php
    if ($_SESSION['role'] == "9") { ?>
    <script>
    $(function() {
        <?php
                if($_POST['reporte'] <> 1) { ?>
        $('.Cheqeados :checkbox').each(function() { //loop all checkbox in dvMain div
            $(this).prop("checked", true).checkboxradio("refresh");
        });
        <?php } ?>
    });
    </script>
    <div data-role="page" class="ui-responsive-panel">
        <div data-role="header">
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
                <a href='lista_adminlab.php' class="ui-btn ui-btn-c ui-icon-home ui-btn-icon-left" rel="external">Cerrar</a>
            </div>
            <h1>Reporte Obstetricia</h1>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div>
        <div class="ui-content" role="main">
            <form action="" method="post" data-ajax="false" id="form1">
                <input type="hidden" name="reporte" id="reporte">
                <table style="margin: 0 auto;" width="100%">
                    <tr>
                        <td width="88%" align="center" valign="top">Medicos
                            <div class="Cheqeados enlinea">
                                <?php
                                    $rUser = $db->prepare("SELECT userX FROM usuario WHERE role=1");
                                    $rUser->execute();
                                    $i = 0;
                                    while ($user = $rUser->fetch(PDO::FETCH_ASSOC)) {
                                        $i++;
                                        $check = "";

                                        if (isset($_POST['u'.$i]) && !empty($_POST['u'.$i])) {
                                            $check = "checked";
                                        }

                                        echo '<input type="checkbox" data-mini="true" name="u'.$i.'" id="u'.$i.'" '.$check.' value="'.$user['userx'].'"><label for="u'.$i.'">'.$user['userx'].'</label>';
                                    } ?>
                                <input type="hidden" name="numUser" value=<?php echo $i; ?>>
                            </div>
                        </td>
                        <td width="12%" align="center" valign="top">
                            Mostrar Desde<input name="ini" type="date" id="ini" value="<?php echo $_POST['ini']; ?>" data-mini="true">
                            Hasta<input name="fin" type="date" id="fin" value="<?php echo $_POST['fin']; ?>" data-mini="true">
                            <h6>Dejar en blanco para mostrar todas las fechas</h6>
                            <p><a href="#" class="Mostrar ui-btn ui-icon-bullets ui-btn-icon-right ui-btn-inline" rel="external">Mostrar</a></p>
                        </td>
                    </tr>
                </table>
                <?php
                    if (isset($_POST['reporte']) && isset($_POST['numUser']) && $_POST['reporte'] <> "" && $_POST['numUser'] > 0) {
                        $medico = " and (";

                        for ($i = 1; $i <= $_POST['numUser']; $i++) {
                            if (isset($_POST['u'.$i]) && !empty($_POST['u'.$i])) {
                                $medico .= "hc_obste.med='".$_POST['u'.$i]."' OR ";
                            }
                        }

                        $medico .= "hc_obste.med='1')"; //es solo para cerrar la condicion y el OR no que vacio
                        $rango = "";

                        if ($_POST['ini'] <> "" && $_POST['fin'] <> "") {
                            $rango = " and hc_obste.fec between '".$_POST['ini']."' and '".$_POST['fin']."'";
                        }

                        $rPaci = $db->prepare("SELECT ape,nom,hc_obste.dni,hc_obste.fec,hc_obste.med FROM hc_paciente,hc_obste WHERE hc_paciente.dni=hc_obste.dni ".$medico.$rango." ORDER BY hc_obste.fec DESC");
                        $rPaci->execute();

                        if ($rPaci->rowCount() > 0) { ?>
                <a href="#" id="exporta" data="<?php echo $_POST['reporte']; ?>" style="display:none;" class="ui-btn ui-mini ui-btn-inline">Exportar a Excel</a>
                <div class="scroll_h">
                    <table width="100%" bordercolor="#F0DF96" style="margin:0 auto;font-size:small;" class="table-stripe tablesorter">
                        <thead>
                            <tr class="ui-bar-b">
                                <th bgcolor="#F0DF96"></th>
                                <th>DNI (<span id="num_pro"></span> Consultas)</th>
                                <th>APELLIDOS Y NOMBRES</th>
                                <th>MEDICO</th>
                                <th align="center">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                    $c = 0;

                                    while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
                                        $c++;
                                        echo '<tr bgcolor="#FFFFFF"><td>'.$c.'</td><td>'.$paci['dni'].'</td><td class="mayuscula">'.$paci['ape'].' '.$paci['nom'].'</td><td>'.$paci['med'].'</td><td>'.date("d-m-Y", strtotime($paci['fec'])).'</td></tr>';
                                    } ?>
                        </tbody>
                    </table>
                </div>
                <?php }
                    } ?>
            </form>
        </div>
    </div>
    <?php } ?>
    <script>
    $(function() {
        $('#num_pro').html(<?php echo $c; ?>);
        $('#alerta').delay(3000).fadeOut('slow');
    });
    </script>
</body>

</html>