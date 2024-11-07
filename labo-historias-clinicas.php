<!DOCTYPE html>
<html lang="en">
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
    <div data-role="page" class="ui-responsive-panel" id="lista">
        <div data-role="header" data-position="fixed">
            <a href="lista.php"
                data-icon="back"
                rel="external"
                class="ui-icon-alt" data-theme="a">Volver</a>
            <h1>Historias Clínicas</h1>
            <a href="salir.php"
                class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power"
                rel="external">Salir</a>
        </div>

        <div class="ui-content" role="main" id="listapaciente">
            <ol id="detallepaciente"
                data-role="listview"
                data-theme="a"
                data-filter="true"
                data-filter-placeholder="Digite todo o parte de los datos del paciente y presione enter para empezar la búsqueda."
                data-inset="true">
            </ol>
        </div>

        <div data-role="footer" data-position="fixed" id="footer">
            <h4>Clínica Inmater</h4>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $(document).keydown('#listapaciente .ui-input-search', function(e){
                if (e.which == 13) {
                    var paciente = $('#listapaciente .ui-input-search :input')[0].value;
                    $("#listapaciente .ui-input-search :input").prop("disabled", true);

                    $.post("le_tanque.php", {paciente: paciente}, function (data) {
                        $("#detallepaciente").html("");
                        $("#detallepaciente").append(data);
                        $('.ui-page').trigger('create');
                    }).done(function() {
                        $("#listapaciente .ui-input-search :input").prop("disabled", false);
                        $("#listapaciente .ui-input-search :input").focus();
                    });
                }
            });
        });
    </script>
</body>
</html>