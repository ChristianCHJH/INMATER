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
            <?php print("<h1>Análisis Clínico Histeroscopía</h1>"); ?>
            <a href="salir.php"
                class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power"
                rel="external">Salir</a>
        </div>

        <div data-role="content">
            <form action="" method="post" data-ajax="false" name="form1" id="form1">
                <input name="dni_ngs" type="hidden">

                <?php
				if (isset($_POST['dni_ngs']) and !empty($_POST['dni_ngs'])) {
                    $stmt = $db->prepare("DELETE FROM analisis_histeroscopia WHERE id = ?;");
                    $stmt->execute(array($_POST['dni_ngs']));
				} ?>

                <div id="one">
                    <a href="e_histeroscopias.php?path=lista_histeroscopias&id=" class="ui-btn ui-mini ui-btn-inline"
                        data-theme="a" rel="external">Nueva Histeroscopía</a>
                    <input id="filtro" data-type="search" placeholder="Escriba el DNI del paciente...">

                    <table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>DNI</th>
                                <th>Apellidos y Nombres</th>
                                <th>Eliminar / Editar / Generar pdf</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $stmt = $db->prepare("SELECT
                                id, fecha, tipo_analisis, dni, upper(nombre) nombre, fnac
                                , a_parrafo1, imagen1parr1, imagen2parr1, imagen3parr1, a_parrafo2, imagen1parr2, imagen2parr2, imagen3parr2
                                , idx, comentario, estado, idusercreate, iduserupdate, createdate
                                FROM analisis_histeroscopia 
                                order by fecha desc
                                limit 20 offset 0;");
                            $stmt->execute();

                            while ($anal = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                print('<tr>
                                    <td>' . date("d-m-Y", strtotime($anal['fecha'])) . '</td>
                                    <td>'.$anal['dni'].'</td>
                                    <td>'.$anal['nombre'].'</a></td>
                                    <th><a href="javascript:eliminarhisteroscopia(' . $anal['id'] . ');">Eliminar</a> / <a href="e_histeroscopias.php?path=lista_histeroscopias&id=' . $anal['id'] . '" rel="external">Editar</a> / <a href="reportes_fpdf/reporte_histeroscopias.php?id=' . $anal['id'] . '" target="_blank" rel="external">PDF</a></th>
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

    function eliminarhisteroscopia(x) {
        if (confirm("¿Confirma que desea eliminar la Histeroscopia?")) {
            document.form1.dni_ngs.value = x;
            document.form1.submit();
            return true;
        } else return false;
    }
    </script>
</body>

</html>