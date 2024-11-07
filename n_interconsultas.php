<!DOCTYPE html>
<html lang="es">
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
    <?php
    if (!!$_GET && !!($_GET['dni'])) {
        $dni = $_GET['dni'];
    } else {
        print('no se encontraron datos del paciente'); exit();
    }

    $stmt = $db->prepare("SELECT ltrim(rtrim(ape)) apellidos, ltrim(rtrim(nom)) nombres FROM hc_paciente WHERE dni = ?;");
    $stmt->execute([$dni]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);    

    $stmt = $db->prepare("SELECT role, sede_id FROM usuario WHERE userx = ?;");
    $stmt->execute(array($login));
    $data_user = $stmt->fetch(PDO::FETCH_ASSOC); ?>

    <div data-role="page" class="ui-responsive-panel" id="lista">
        <div data-role="header" data-position="fixed">
            <?php
            print('<a href="e_paci.php?id=' . $_GET["dni"] . '" data-icon="back" class="ui-icon-alt" data-theme="a" rel="external">volver</a>');
            print("<h2>Informe de Interconsultas - " . $paciente['apellidos'] . " " . $paciente['nombres'] . "</h2>"); ?>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div>

        <div data-role="content">
            <form action="" method="post" data-ajax="false" name="form1" id="form1">
                <input name="anu_ngs" type="hidden">
                <input name="dni_ngs" type="hidden">

                <?php
                if (isset($_POST['anu_ngs']) and !empty($_POST['anu_ngs']) and isset($_POST['dni_ngs']) and !empty($_POST['dni_ngs'])) {
                    $stmt = $db->prepare("DELETE FROM hc_analisis WHERE id=?;");
                    $stmt->execute(array($_POST['anu_ngs']));

                    unlink("analisis/" . $_POST['anu_ngs'] . "_" . $_POST['dni_ngs'] . ".pdf");
                } ?>

                <div class="ui-bar ui-bar-a">
                    <table style="margin: 0 auto;" width="100%">
                        <tr>
                            <td width="57%">
                                <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <?php
                                    print('
                                        <a href="man_examen.php?path=n_interconsultas&dni=' . $dni . '&tipo=7&id=" class="ui-btn ui-mini ui-btn-inline" data-theme="a" rel="external">Nuevo Examen</a>
                                        <a href="man_tipo_examen.php?path=n_interconsultas&dni=' . $dni . '&padre_id=7" class="ui-btn ui-mini ui-btn-inline" data-theme="a" rel="external">Agregar Tipo Examen</a>'); ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <table data-role="table" data-filter="true" data-input="#filtrongs" class="table-stripe ui-responsive">
                    <thead>
                        <tr>
                            <th>Tipo de Examen</th>
                            <th>Tipo</th>
                            <th style="text-align: center;">Resultado</th>
                            <th style="text-align: center;">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // informes
                        $resultados = ['1' => 'POSITIVO', '2' => 'NEGATIVO', '3' => 'NO RESULTADO'];
                        $stmt = $db->prepare(
                            "SELECT
                            me.id, te.nombre tipo_examen, me.resultado_id, coalesce(a.nombre_base, '-') archivo, me.fecha, me.observacion
                            FROM man_examenes me
                            INNER JOIN hc_paciente p ON p.dni = me.paciente_id
                            INNER JOIN man_tipo_examen te ON te.id = me.tipo_examen_id
                            LEFT JOIN man_archivo a ON a.id = me.archivo_id
                            WHERE  me.paciente_id = ?;"
                        );
                        $stmt->execute([$dni]);

                        while ($aglo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            print(
                            '<tr>
                                <td>
                                    <a href="man_examen.php?path=n_interconsultas&dni=' . $dni . '&tipo=7&id=' . $aglo['id'] . '" rel="external">' . mb_strtoupper($aglo['tipo_examen']) . '</a><br>
                                    ' . (file_exists('storage/examenes/' . $aglo['archivo']) ? '<a href="archivos_hcpacientes.php?idStorage=examenes/' . $aglo['archivo'] . '" target="new">Ver/Descargar</a>' : '') . '
                                </td>
                                <td>' . mb_strtoupper($aglo['observacion']) . '</td>
                                <th style="text-align: center;">' . $resultados[$aglo['resultado_id']] . '</th>
                                <td style="text-align: center;">' . date('Y-m-d', strtotime($aglo['fecha'])) . '</td>
                            </tr>');
                        } ?>
                    </tbody>
                </table>

            </form>
        </div>

        <div data-role="footer">
            <h4>Clínica Inmater</h4>
        </div>

    </div>
    <script>
        $(".btn_consulta_resultado").on("click", function () {
            // $.mobile.changePage('#dialog', 'pop', true, true);

            $.ajax({
                type: 'POST',
                url: '_operaciones/e_paci.php',
                data: { orden: $(this).attr("data-orden") },
                success: function (result) {
                    var data = jQuery.parseJSON(result);
                    $("#popup_resultados").html(data.resultado);
                    $("#popup_resultados").popup("open");
                    // var content_width = $.mobile.activePage.find("div[data-role='content']:visible:visible").outerWidth();
                    // console.log(content_width)
                    // $('#popup_resultados').css({ 'width': content_width * 0.8 });
                }
            });
        });
    </script>
</body>
</html>