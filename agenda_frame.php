<?php session_start(); ?>
<!DOCTYPE HTML>
<html>

<head>
    <?php
    $login = $_SESSION['login'];

    if (!$login) {
        echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT'])) . "'>";
    } else {
        if (isset($_GET['med']) and !empty($_GET['med'])) {
            $login = $_GET['med'];
        }

        require "_database/db_tools.php";
    } ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <!-- <link rel="stylesheet" href="css/global.css"/> -->

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
    <script>
    function anular(x) {
        document.form1.borrar.value = x;
        document.form1.submit();
    }
    </script>
</head>

<body>
    <div class="loader">
        <img src="_images/load.gif" alt="">
    </div>

    <div data-role="page" class="ui-responsive-panel" id="agenda_frame" data-dialog="true">
        <style>
        .loader {
            position: fixed;
            height: 100%;
            left: 0;
            top: 0;
            width: 100%;
            background: #FFF;
            z-index: 9999;
        }

        .loader img {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            -moz-transform: translate(-50%, -50%);
        }

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

        #ini_fec,
        #fin_fec {
            text-align: center;
        }

        .chosen-container {
            width: 100px !important;
        }

        .chosen-container .ui-input-text {
            margin: 0px !important;
        }

        .chosen-container b {
            margin-left: 3px;
            margin-top: 3px;
        }

        .chosen-container span {
            padding-top: 3px;
        }

        a:not([href]):not([tabindex]) {
            height: 31px;
            margin-top: 7px;
        }

        #ini_h-button,
        #ini_m-button,
        #fin_h-button,
        #fin_m-button {
            display: none;
        }
        </style>

        <?php
        if (isset($_POST['btn_consulta']) and $_POST['btn_consulta'] == "Agendar como No Disponible" and isset($_POST['med'])) {
            // print('<pre>'); print_r($_POST); print('</pre>');
            $ini = $_POST['ini_h'] . ':' . $_POST['ini_m'];
            $fin = $_POST['fin_h'] . ':' . $_POST['fin_m'];
            $ini_fec = $_POST['ini_fec'];
            $fin_fec = $_POST['fin_fec'];

            if ($ini_fec <= $fin_fec) {
                // el mismo dia
                if ($ini_fec == $fin_fec) {
                    if ($_POST['ini_h'] <= $_POST['fin_h']) {
                        if ($_POST['ini_m'] <= $_POST['fin_m']) {
                            require($_SERVER["DOCUMENT_ROOT"]."/_database/database.php");
                            global $db;
                            $stmt = $db->prepare("INSERT INTO hc_disponible (med, fec, ini, fin, obs) VALUES (?,?,?,?,?)");
                            $stmt->execute([
                                $_POST['med'],
                                $ini_fec,
                                $ini,
                                $fin,
                                $_POST['obs']
                            ]);

                            print("<div id='alerta'>Evento Agendado!</div>");
                        } else {
                            print("<div id='alerta'>Error!. Los minutos de inicio deben ser menor que los minutos de fin!</div>");
                        }
                    } else {
                        print("<div id='alerta'>Error!. La hora de inicio debe ser menor que la hora de fin!</div>");
                    }
                } else {
                    $ini_1 = strtotime($ini_fec);
                    $fin_1 = strtotime($fin_fec);
                    $dias = floor(($fin_1 - $ini_1)/3600/24);
                    $i = 1;

                    require($_SERVER["DOCUMENT_ROOT"]."/_database/database.php");
                    global $db;
                    $stmt = $db->prepare("INSERT INTO hc_disponible (med, fec, ini, fin, obs) VALUES (?,?,?,?,?)");
                    $stmt->execute([
                        $_POST['med'],
                        $ini_fec,
                        $ini,
                        '20:45',
                        $_POST['obs']
                    ]);
                    $date = $ini_fec;

                    while ($i < $dias) {
                        $date = date('Y-m-d', strtotime($date.' +1 day'));
                        $stmt = $db->prepare("INSERT INTO hc_disponible (med, fec, ini, fin, obs) VALUES (?,?,?,?,?)");
                        $stmt->execute([
                            $_POST['med'],
                            $date,
                            '07:00',
                            '20:45',
                            $_POST['obs']
                        ]);

                        $i++;
                    }

                    $stmt = $db->prepare("INSERT INTO hc_disponible (med, fec, ini, fin, obs) VALUES (?,?,?,?,?)");
                    $stmt->execute([
                        $_POST['med'],
                        $fin_fec,
                        '07:00',
                        $fin,
                        $_POST['obs']
                    ]);
                }
            } else {
                print("<div id='alerta'>Error!. La fecha de inicio debe ser menor que la fecha de fin!</div>");
            }
        }

        if (isset($_POST['borrar']) and !empty($_POST['borrar'])) {
            $stmt = $db->prepare("DELETE FROM hc_disponible WHERE id=?");
            $stmt->execute(array($_POST['borrar']));
        } ?>

        <div data-role="header" data-position="fixed">
            <a href="lista.php" rel="external" class="ui-btn">Cerrar</a>
            <h1>Agenda <?php echo '(' . $login . ')'; ?></h1>
        </div>

        <div class="ui-content" role="main">
            <?php
            if ($_SESSION['role'] <> 2) { ?>
            <form action="agenda_frame.php?med=<?php echo $login; ?>" method="post" name="form1" id="form1">
                <input type="hidden" name="borrar">
                <input type="hidden" name="med" value="<?php echo $login; ?>">

                <table width="100%" align="center" style="margin: 0 auto;">
                    <tr>
                        <td>
                            <div style="display: flex;">
                                <span style="margin-top: 14px; font-size: 14px;"><b>Desde:</b></span><br>
                                <input name="ini_fec" type="date" id="ini_fec" data-mini="true">
                                <select name="ini_h" id="ini_h" class="chosen-select" data-mini="true">
                                    <option value="">Hora</option>
                                    <option value="07">7 horas</option>
                                    <option value="08">8 horas</option>
                                    <option value="09">9 horas</option>
                                    <option value="10">10 horas</option>
                                    <option value="11">11 horas</option>
                                    <option value="12">12 horas</option>
                                    <option value="13">13 horas</option>
                                    <option value="14">14 horas</option>
                                    <option value="15">15 horas</option>
                                    <option value="16">16 horas</option>
                                    <option value="17">17 horas</option>
                                    <option value="18">18 horas</option>
                                    <option value="19">19 horas</option>
                                </select>
                                <select name="ini_m" id="ini_m" class="chosen-select" data-mini="true">
                                    <option value="">Minutos</option>
                                    <option value="00">00 minutos</option>
                                    <option value="15">15 minutos</option>
                                    <option value="30">30 minutos</option>
                                    <option value="45">45 minutos</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display: flex;">
                                <span style="margin-top: 14px; font-size: 14px;"><b>Hasta:</b></span><br>
                                <input name="fin_fec" type="date" id="fin_fec" data-mini="true">
                                <select name="fin_h" id="fin_h" class="chosen-select" data-mini="true">
                                    <option value="">Hora</option>
                                    <option value="07">7 horas</option>
                                    <option value="08">8 horas</option>
                                    <option value="09">9 horas</option>
                                    <option value="10">10 horas</option>
                                    <option value="11">11 horas</option>
                                    <option value="12">12 horas</option>
                                    <option value="13">13 horas</option>
                                    <option value="14">14 horas</option>
                                    <option value="15">15 horas</option>
                                    <option value="16">16 horas</option>
                                    <option value="17">17 horas</option>
                                    <option value="18">18 horas</option>
                                    <option value="19">19 horas</option>
                                    <option value="20">20 horas</option>
                                </select>
                                <select name="fin_m" id="fin_m" class="chosen-select" data-mini="true">
                                    <option value="">Minutos</option>
                                    <option value="00">00 minutos</option>
                                    <option value="15">15 minutos</option>
                                    <option value="30">30 minutos</option>
                                    <option value="45">45 minutos</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="margin-top: 14px; font-size: 14px;"><b>Observaciones:</b></span><br>
                            <input name="obs" type="text" id="obs" maxlength="50" data-mini="true">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="Submit" value="Agendar como No Disponible" name="btn_consulta" data-icon="check" data-mini="true" data-theme="b" data-inline="true" />
                        </td>
                    </tr>
                </table>

                <span style="font-size: 14px; margin: 0 10px;">Ver la lista de horarios no disponibles <a href="#historial" data-rel="popup" data-transition="pop">aquí.</a></span>

                <div data-role="popup" id="historial" class="ui-content">
                    <table width="100%" align="center" style="margin: 0 auto; font-size: small;">
                        <tr>
                            <th>Fecha</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Comentarios</th>
                            <th></th>
                        </tr>
                        <?php
                            $stmt=$db->prepare("SELECT * FROM hc_disponible WHERE med=? ORDER BY fec DESC LIMIT 200;");
                            $stmt->execute([$login]);

                            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?php echo date("d-m-Y", strtotime($data['fec'])); ?></td>
                            <td><?php echo $data['ini']; ?></td>
                            <td><?php echo $data['fin']; ?></td>
                            <td><?php echo $data['obs']; ?></td>
                            <td><a href="javascript:anular(<?php echo $data["id"]; ?>);">Borrar</a></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </form>

            <?php } ?>

            <iframe src="agenda.php?med=<?php (isset($_GET['med']) and !empty($_GET['med']) ? print($_GET['med']): print('')); ?>" width="100%" height="800" seamless></iframe>
        </div>

        <script>
        $("#form1").submit(function(e) {
            e.preventDefault();
            if ($("#ini_fec").val() == "" || $("#fin_fec").val() == "" || $("#ini_h").val() == "" || $("#ini_m").val() == "" || $("#fin_h").val() == "" || $("#fin_m").val() == "") {
                alert("Falta colocar las fechas y horas de No Disponible.");
                return false;
            }

            var form = this;
            form.submit();
        });

        $(".chosen-select").chosen();

        $(function() {
            $('#alerta').delay(5000);
        });

        window.addEventListener("message", receiveMessage, false);

        function receiveMessage(event) {
            if (event.data.cargando == 'no') {
                jQuery('.loader').fadeOut(1000);
            }

            if (event.data.cargando == 'si') {
                jQuery('.loader').show();
            }
        }
        </script>
    </div>

</body>

</html>