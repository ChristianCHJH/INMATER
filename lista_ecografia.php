<!DOCTYPE html>
<html lang="es">

<head>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Inmater | Lista Ecografía</title>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" href="css/global.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <style>
    .container-demo div {
        display: inline-block;
        vertical-align: top;
    }
    </style>
</head>

<body>
    <?php
	$stmt = $db->prepare("SELECT role, sede_id FROM usuario WHERE userx=?");
	$stmt->execute(array($login));
	$data_user = $stmt->fetch(PDO::FETCH_ASSOC); ?>

    <div data-role="page" class="ui-responsive-panel" id="lista">
        <div data-role="header" data-position="fixed">
            <?php print("<h1>Análisis Clínico Ecografía</h1>"); ?>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div>

        <div data-role="content">
            <form action="" method="post" data-ajax="false" name="form1" id="form1">
                <input name="anu_ngs" type="hidden">
                <input name="dni_ngs" type="hidden">

                <?php
				if (isset($_POST['anu_ngs']) and !empty($_POST['anu_ngs']) and isset($_POST['dni_ngs']) and !empty($_POST['dni_ngs'])) {
					$stmt = $db->prepare("UPDATE hc_analisis set estado = 0 WHERE id = ?;");
					$stmt->execute(array($_POST['anu_ngs']));
				} ?>

                <div id="one">
                    <div class="container-demo">
                        <input type="date" name="fecha_ini" id="fecha_ini" data-mini="true" data-role="date" data-inline="true" required>
                        <input type="date" name="fecha_fin" id="fecha_fin" data-mini="true" data-role="date" data-inline="true" required>
                        <input type="button" value="Buscar" name="buscar" id="buscar" data-mini="true" data-icon="search">
                    </div>
                    <a href="e_analisis.php?path=lista_ecografia&id=" class="ui-btn ui-mini ui-btn-inline" data-theme="a" rel="external">Nueva Ecografía</a>
                    <a href="e_analisis_tipo.php?path=lista_ecografia" class="ui-btn ui-mini ui-btn-inline" data-theme="a" rel="external">Agregar Tipo Ecografía</a>
                    <input id="filtro" data-type="search" placeholder="Escriba los nombres o apellidos del paciente...">

                    <table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive">
                        <thead>
                            <tr>
                                <th>Ecografía</th>
                                <th>Apellidos y Nombres</th>
                                <th>Médico</th>
                                <th>Ver/ Descargar</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $stmt = $db->prepare("SELECT a.*, coalesce(ma.nombre_base, '-') nombre_base, coalesce(ma.nombre_original, '-') nombre_original
                                FROM hc_analisis a
                                left join man_archivo ma on ma.id = a.archivo_id
                                where a.estado = 1 and a.lab = ?
                                order by a.a_mue desc
                                limit 20 offset 0;"
                            );
                            $stmt->execute([$login]);

                            while ($anal = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $analisis = '';
                                $video = '';
                                $link_video = '';
                                $stmt = $db->prepare("SELECT * from google_drive_response where drive_id <> '0' and estado = 1 and tipo_procedimiento_id = 2 and procedimiento_id = ? order by id desc limit 1 offset 0;");
                                $stmt->execute([$anal['id']]);
                                if ($stmt->rowCount() > 0) {
                                    $data = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $link_video = "<a href='https://drive.google.com/open?id=" . $data['drive_id'] . "' target='new'>Vídeo</a> - ";
                                }

                               /*  if (file_exists('analisis/' . $anal['id'] . '_' . $anal['a_dni'] . '.pdf')) {
                                    $analisis = '<a href="analisis/' . $anal['id'] . '_' . $anal['a_dni'] . '.pdf" target="new">Informe</a> - ';
                                } */

                                if (file_exists('storage/analisis_archivo/' . $anal['nombre_base'])) {
                                    $video = '<a href="archivos_hcpacientes.php?idStorage=analisis_archivo/' . $anal['nombre_base'] . '" target="new">Vídeo</a> - ';
                                }

                                print('<tr>
                                    <th><a href="e_analisis.php?path=lista_ecografia&id=' . $anal['id'] . '" rel="external">' . mb_strtoupper($anal['a_exa']) . '</a></th>
                                    <td><a href="e_paci_mail.php?path=lista_ecografia&id=' . $anal['a_dni'] . '" rel="external">' . mb_strtoupper($anal['a_nom']) . '</a></td>
                                    <td>' . mb_strtoupper($anal['a_med']) . '</td>
                                    <th>' . $link_video . $video . $analisis . '<a href="javascript:eliminarAnalisis(' . $anal['id'] . ', ' . $anal['a_dni'] . ');">Eliminar</a></th>
                                    <td>' . date("d-m-Y", strtotime($anal['a_mue'])) . '</td>
                                </tr>');
                            } ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        <div data-role="footer">
            <h4>Clínica Inmater</h4>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#one > .ui-input-search').keydown(function(e) {
            if (e.keyCode == 13) {
                var buscar = $('#one > .ui-input-search > :input')[0].value;

                if (buscar.length > 3) {
                    $("#one > .ui-input-search > :input").prop("disabled", true);
                    $("#one > table > tbody").html("");

                    $.post("_operaciones/lista_ecografia.php", {
                        tipo_operacion: "visualizar",
                        buscar: buscar
                    }, function(data) {
                        $("#one > table > tbody").html("");
                        $("#one > table > tbody").append(data);
                        $("#one > table > thead").removeClass("ui-screen-hidden");
                        $("#one > table > tbody").removeClass("ui-screen-hidden");
                        $('#filtro').val("");
                    }).done(function() {
                        $("#one > .ui-input-search > :input").prop("disabled", false);
                        $("#one > .ui-input-search > :input").focus();
                        $('.ui-page').trigger('create');
                    });
                }
            }
        });

        $('#buscar').click(function(e) {
            if ($('#fecha_ini').val() == '' || $('#fecha_fin').val() == '') {
                alert('Agregue un rango de fechas');
                return false;
            }
            $.post("_operaciones/lista_ecografia.php", {
                tipo_operacion: "visualizar",
                buscar: {
                    fecha_ini: $("#fecha_ini").val(),
                    fecha_fin: $("#fecha_fin").val()
                }
            }, function(data) {
                $("#one > table > tbody").html("");
                $("#one > table > tbody").append(data);
                $("#one > table > thead").removeClass("ui-screen-hidden");
                $("#one > table > tbody").removeClass("ui-screen-hidden");
                $('#filtro').val("");
            }).done(function() {
                $("#one > .ui-input-search > :input").prop("disabled", false);
                $("#one > .ui-input-search > :input").focus();
                $('.ui-page').trigger('create');
            });
        });
    });

    function eliminarAnalisis(x, y) {
        if (confirm("¿Confirma que desea eliminar la Ecografía?")) {
            document.form1.anu_ngs.value = x;
            document.form1.dni_ngs.value = y;
            document.form1.submit();
            return true;
        } else return false;
    }
    </script>
</body>

</html>