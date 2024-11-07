<!DOCTYPE HTML>
<html>
<head>
    <?php
    include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css?v=2" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <title>Inmater Clínica de Fertilidad</title>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script>
    function PrintElem(elem) {
        var data = $(elem).html();
        var mywindow = window.open('', 'Imprimir', 'height=600,width=800');
        mywindow.document.write('<html><head><title>Imprimir</title>');
        mywindow.document.write(
            '<style> @page {margin: 0px 0px 0px 5px;} table {border-collapse: collapse;font-size:10px;} .table-stripe td {border: 1px solid black;} .tablamas2 td {border: 1px solid white;} .mas2 {display: block !important;} .noVer, .ui-table-cell-label {display: none;} a:link {pointer-events: none; cursor: default;}</style>'
        );
        mywindow.document.write('</head><body>');
        mywindow.document.write(data);
        mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
        mywindow.document.write('</body></html>');
        return true;
    }
    $(document).ready(function() {
        $(".mas2").hide();
        $(".mas").click(function() {
            var mas = $(this).attr("data");
            $("#" + mas).toggle();
        });

        $('.ui-input-search').appendTo($('.enlinea'));

        $('#agenda_med').on('change', function() {
            if (this.value)
                window.location.href = "agenda_frame.php?med=" + this.value;
            $(this).val('');
        });

        $('#med_agenda').on('change', function() {
            $(".marco_agenda").remove();
            if (this.value)
                $(".td_agenda").append(
                    '<div class="marco_agenda"><h2>REVISE LA DISPONIBILDAD DEL MEDICO</h2><iframe src="agenda.php?med=' +
                    this.value + '" width="100%" height="800" seamless></iframe></div>');
        });

        $(".ui-input-search input").attr("id", "paci_nom");

        $('.paci_insert').click(function(e) {
            $('#paci_nom').val($(this).text());
            $('#dni').val($(this).attr("dni"));
            $('#paci_nom').textinput('refresh');
            $('#paci_nom').attr('autocomplete', 'off');
            $('.fil_paci li').addClass('ui-screen-hidden');
            $('#paci_nom').focus();
            $('#med').val('');
            med = $(this).attr("med"); //nose se esta usando
        });

        $('#orden').click(function() {
            var table = $(this).parents('table').eq(0);
            var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
            this.asc = !this.asc;
            if (!this.asc) {
                rows = rows.reverse()
            }
            for (var i = 0; i < rows.length; i++) {
                table.append(rows[i])
            }
        })

        function comparer(index) {
            return function(a, b) {
                var valA = getCellValue(a, index),
                    valB = getCellValue(b, index);
                return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
            }
        }

        function getCellValue(row, index) {
            return $(row).children('td').eq(index).html()
        }
    });

    function borrarNGS(x, y) {
        if (confirm("CONFIRMA ELIMINAR?")) {
            document.form1.anu_ngs.value = x;
            document.form1.dni_ngs.value = y;
            document.form1.submit();
            return true;
        } else return false;
    }
    var tableToExcel = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template =
            '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            },
            format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            }
        return function(table, visita) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: 'reporte_' + visita || 'reporte',
                table: table.innerHTML
            }
            window.location.href = uri + base64(format(template, ctx))
        }
    });


    $(document).keyup('#listapaciente .ui-input-search', function(e) {
        var nombre_modulo = "busqueda_paciente";
        var ruta = "perfil_medico/busqueda_paciente";
        var tipo_operacion = "consulta";
        var login = $('#login').val();
        var key = $('#key').val();
        var clave = 'paciente';
        var valor = $('#listapaciente .ui-input-search :input')[0].value;
        var paciente = $('#listapaciente .ui-input-search :input')[0].value;

        if (e.which == 13) {
            $("#listapaciente .ui-input-search :input").prop("disabled", true);

            $.ajax({
                type: 'POST',
                dataType: "json",
                contentType: "application/json",
                url: '_api_inmater/servicio.php',
                data: JSON.stringify({
                    nombre_modulo: nombre_modulo,
                    ruta: ruta,
                    tipo_operacion: tipo_operacion,
                    clave: clave,
                    valor: valor,
                    idusercreate: login,
                    apikey: key
                }),
                success: function(result) {}
            });

            $.post("le_tanque.php", {
                    paciente: paciente
                }, function(data) {
                    $("#detallepaciente").html("");
                    $("#detallepaciente").append(data);
                    $('.ui-page').trigger('create');
                })
                .done(function() {
                    $("#listapaciente .ui-input-search :input").prop("disabled", false);
                    $("#listapaciente .ui-input-search :input").focus();
                });
        }
    });

    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this,
                args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };
    </script>
    <style>
    input[data-type=search]:enabled {
        background: #fcfcfc;
    }

    input[data-type=search]:disabled {
        background: #dddddd;
    }

    #alerta {
        background-color: #FF9;
        margin: 0 auto;
        text-align: center;
        padding: 4px;
    }

    .color {
        color: #F4062B !important;
    }

    .analisis .ui-btn {
        border-color: #E9A4A4 !important;
    }

    .enlinea div {
        display: inline-block;
        vertical-align: middle;
    }

    .controlgroup-textinput {
        padding-top: 0.5px;
        padding-bottom: 0.5px;
    }

    .scroll_h {
        overflow-x: scroll;
        overflow-y: hidden;
        white-space: nowrap;
    }

    #unique-span.custom-label {
        padding: 0.2em 0.6em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25em;
        background-color: red;
        
    }
    </style>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="lista">
        <?php
    $rUser = $db->prepare("SELECT role, sede_id FROM usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['btn_consulta']) and $_POST['btn_consulta'] == "AGENDAR CONSULTA" and isset($_POST['dni']) and isSet($_POST['fec']) and isSet($_POST['fec_h'])) {
        $rPaci = $db->prepare("SELECT med FROM hc_paciente WHERE dni=?");
        $rPaci->execute(array($_POST['dni']));
        $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
        if (strpos($paci['med'], $_POST['med_agenda']) == false) { // si el medico NO esta en la lista de medicos, entonces lo agrega
            $stmt = $db->prepare("UPDATE hc_paciente SET med=?, iduserupdate=?,updatex=? WHERE dni=?");
            $hora_actual = date("Y-m-d H:i:s");
            $stmt->execute(array($_POST['med_agenda'].','.$paci['med'],$login, $hora_actual, $_POST['dni']));
            $log_Paciente = $db->prepare(
                "INSERT INTO appinmater_log.hc_paciente (
                            dni, pass, sta, med, tip, nom, ape, fnac, tcel,
                            tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                            san, don, raz, talla, peso, rem, nota, fec, idsedes,
                            idusercreate, createdate, 
                            action
                    )
                SELECT 
                    dni, pass, sta, med, tip, nom, ape, fnac, tcel, 
                    tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                    san, don, raz, talla, peso, rem, nota, fec, idsedes,
                    iduserupdate,updatex, 'U'
                FROM appinmater_modulo.hc_paciente
                WHERE dni=?");
            $log_Paciente->execute(array($_POST['dni']));
        }

    } ?>

        <?php
    if ($user['role'] == 1 || $user['role'] == 11 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 14 || $user['role'] == 15) { ?>
        <div data-role="panel" id="indice_paci">
            <img src="_images/logo.jpg" />
            <ul data-role="listview" data-inset="true" data-theme="a">
                <li data-icon="user"><a href="perfil.php" rel="external">Perfil</a></li>
                <li data-icon="bars"><a href="lista.php" rel="external">Lista de Pacientes</a></li>
                <li data-icon="bars"><a href="med-betas-lista.php" rel="external">Lista Betas</a></li>
                <?php
                    if ( $user['role'] == 1 || $user['role'] == 16 ) {
                        print(' <li data-icon="bars"><a href="lista_pedido.php" rel="external">Lista de Pedidos</a></li>');
                    }
                    if ( $user['role'] == 1 || $user['role'] == 11 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
                        print('<li data-icon="plus"><a href="n_paci.php" rel="external">Nuevo Paciente</a></li>');
                    }

                    if ( $user['role'] == 1 || $user['role'] == 11) {
                        print('<li data-icon="calendar"><a href="agenda_frame.php?med=" rel="external">Agenda</a></li>');
                    }

                    if ($user['role'] == 14) {
                        print('
                        <li data-icon="calendar"><a href="repo_tracking_pacientes.php" rel="external">Tracking Pacientes</a></li>
                        <li data-icon="calendar"><a href="agenda_frame_01.php?med=" rel="external">Agenda</a></li>
                        <li data-icon="calendar"><a href="pagos_agenda.php" rel="external">Programación Sala</a></li>');
                    }

                    if ($user['role'] == 15) {
                        print('<li data-icon="calendar"><a href="lista_consulta.php" rel="external">Agenda Consulta</a></li>');
                    }

                    // agregar
                    $idpsicologia = 0;
                    switch ($login) {
                        case 'mvelit': $idpsicologia=1; break;
                        case 'eescudero': $idpsicologia=2; break;
                        case 'mascenzo': $idpsicologia=3; break;
                        case 'rbozzo': $idpsicologia=4; break;
                        case 'cbonomini': $idpsicologia=5; break;
                        case 'lbernuy': $idpsicologia=6; break;
                        case 'cosorio': $idpsicologia=7; break;
                        case 'jolivas': $idpsicologia=8; break;
                        case 'apuertas': $idpsicologia=9; break;
                        case 'jtremolada': $idpsicologia=10; break;
                        default: break;
                    }

                    if ($user['role'] == 1) {
                        print('
                        <li data-icon="bullets"><a href="r_pap.php" rel="external">Reporte PAP</a></li>
                        <li data-icon="bullets"><a href="r_parto.php" rel="external">Reporte Partos</a></li>
                        <li data-icon="bullets"><a href="repo_poseidon.php" rel="external">Reporte Poseidon</a></li>
                        <li data-icon="bullets"><a href="gra_betas.php" rel="external">Gráfica Betas</a></li>
                        <li data-icon="bullets"><a href="https://psicologia.inmater.pe/psicologia/'.$idpsicologia.'" target="_blank" rel="external">Informes Psicología</a></li>');
                    }

                    if ( $user['role'] == 1 || $user['role'] == 11 ) {
                        print('<li data-icon="bullets"><a href="https://psicologia.inmater.pe/listas/genetica" target="_blank" rel="external">Informes Genética</a></li>');
                    }

                    if ($user['role'] == 1) {
                        print('<li data-icon="info"><a href="ayuda.php" rel="external">Ayuda</a></li>');
                    }
                ?>
            </ul>
        </div>
        <?php } ?>

        <div data-role="header" data-position="fixed">
            <?php if ($user['role'] == 1 || $user['role'] == 11 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 14 || $user['role'] == 15) { ?>
            <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU</a>
            <?php } ?>
            <?php
        if ($user['role'] == 2) { ?>
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
                <a href='#popup_procedimientos' data-rel="popup" class="ui-btn ui-icon-home ui-btn-icon-left" data-transition="pop">Inicio</a>
                <a href='lista_and.php' class="ui-btn ui-btn-inline" rel="external">Andrología</a>
                <a href='agenda_frame.php' class="ui-btn ui-btn-inline" rel="external">Agenda</a>
                <a href="#popupBasic" data-rel="popup" class="ui-btn" data-transition="pop">Administración</a>
            </div>
            <div data-role="popup" id="popup_procedimientos" data-arrow="true">
                <ul data-role="listview" data-inset="true">
                    <li><a href='lista_pro.php' class="ui-btn ui-mini" rel="external">Proc. Lista</a></li>
                    <li><a href='lista_pro_8.php' class="ui-btn ui-mini" rel="external">Próximos</a></li>
                    <li><a href='lista_pro_f.php' class="ui-btn ui-mini" rel="external">Proc. Finalizados</a></li>
                    <li><a href='javascript:void(0)' class="ui-btn ui-mini" id="link_transferencia_betas" rel="external">Transferencia Betas</a></li>
                    <li><a href='seguimiento-betas.php' class="ui-btn ui-mini" rel="external">Seguimiento Betas</a></li>
                    <li><a href='lista_pro_t.php' class="ui-btn ui-mini" rel="external">Traslados</a></li>
                    <li><a href='lista_pro_x.php' class="ui-btn ui-mini" rel="external">Retiros Ov / Emb</a></li>
                    <li><a href='labo-historias-clinicas.php' class="ui-btn ui-mini" rel="external">Historias
                            Clínicas</a></li>
                </ul>
            </div>
            <div data-role="popup" id="popupBasic" data-arrow="true">
                <ul data-role="listview" data-inset="true">
                    <?php $anio_actual=date("Y"); ?>
                    <li><a href='lista_con.php' rel="external">Control de Calidad de Insumos</a></li>
                    <li><a href='pago_veri.php?x=x' rel="external">Ultimos 100 Procedimientos</a></li>
                    <li><a href='r_data.php' rel="external">DATA</a></li>
                    <li><a href='repo-data.php' rel="external">Reporte Data</a></li>
                    <?php print('<li><a href="r_data_g.php?anio='.$anio_actual.'&med=" target="_blank" rel="external">Gráficas</a></li>'); ?>
                    <li><a href='r_pro.php' rel="external">Reporte Procedimientos</a></li>
                    <li><a href='r_tanque.php' rel="external">Reporte tanque semen</a></li>
                    <li><a href='lista_emb.php' rel="external">Embriologos</a></li>
                    <li><a href='ayuda.php' rel="external">Ayuda</a></li>
                    <li><a href='pro_admin.php' rel="external">Administracion protocolos</a></li>
                    <li><a href='perfil.php' rel="external">Cambiar Contraseña</a></li>
                </ul>
            </div>
            <?php } ?>
            <?php
        if ($user['role'] == 3 || $user['role'] == 10 || $user['role'] == 19 || $user['role'] == 20) { ?>
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
                <a href="#popup_nuevo" data-rel="popup" class="ui-btn" data-transition="pop">NUEVO</a>
                <a href="#popup_consulta" data-rel="popup" class="ui-btn" data-transition="pop">CONSULTA</a>
                <a href="#popup_reportes" data-rel="popup" class="ui-btn" data-transition="pop">REPORTES</a>
                <a href="#popup_mantenimiento" data-rel="popup" class="ui-btn" data-transition="pop">MANTENIMIENTO</a>
                <a href="#popup_configuracion" data-rel="popup" class="ui-btn" data-transition="pop">CONFIGURACIÓN</a>
            </div>
            <div data-role="popup" id="popup_nuevo" data-arrow="true">
                <ul data-role="listview" data-split-icon="gear" data-split-theme="a" data-inset="true">
                    <li><a href="pago.php?id=&t=&s=1" rel="external">Reproducción Asistida</a><a href="pago_ser.php?s=1" rel="external">admin</a></li>
                    <li><a href="pago.php?id=&t=&s=2" rel="external">Andrología</a><a href="pago_ser.php?s=2" rel="external">admin</a></li>
                    <li><a href="pago.php?id=&t=&s=3" rel="external">Procedimientos Sala</a><a href="pago_ser.php?s=3" rel="external">admin</a></li>
                    <li><a href="pago.php?id=&t=&s=4" rel="external">Analisis Sangre</a><a href="pago_ser.php?s=4" rel="external">admin</a></li>
                    <li><a href="pago.php?id=&t=&s=5" rel="external">Perfiles</a><a href="pago_ser.php?s=5" rel="external">admin</a></li>
                    <li><a href="pago.php?id=&t=&s=6" rel="external">Ecografía</a><a href="pago_ser.php?s=6" rel="external">admin</a></li>
                    <li><a href="pago.php?id=&t=&s=7" rel="external">Adicionales</a><a href="pago_ser.php?s=7" rel="external">admin</a></li>
                    <li><a href='n_pacipare.php' rel="external">Paciente</a></li>
                </ul>
            </div>
            <div data-role="popup" id="popup_consulta" data-arrow="true">
                <ul data-role="listview" data-split-icon="gear" data-split-theme="a" data-inset="true">
                    <?php if ($user['role'] == 3) {print('<li><a href="traslado.php" rel="external">Traslados</a></li>');} ?>
                    <?php if ($user['role'] == 3 || $user['role'] == 10 || $user['role'] == 19 || $user['role'] == 20) {print('<li><a href="pagos_agenda.php" target="_blank" rel="external">Programación Sala</a></li>');} ?>
                </ul>
            </div>
            <div data-role="popup" id="popup_reportes" data-arrow="true">
                <ul data-role="listview" data-split-icon="gear" data-split-theme="a" data-inset="true">
                    <?php
                        if ($user['role'] == 3) {
                            print('
                            <li><a href="pago_veri.php?x=x" rel="external">Ultimos 100 Procedimientos</a></li>
                            <li><a href="r_tanque.php" rel="external">Tanque semen</a></li>');
                        }
                    ?>
                    <li><a href='repo_conta.php' target="_blank" rel="external">Ventas</a></li>
                    <li><a href='repo_pacientes.php' target="_blank" rel="external">Pacientes</a></li>
                </ul>
            </div>
            <div data-role="popup" id="popup_mantenimiento" data-arrow="true">
                <ul data-role="listview" data-inset="true">
                    <li><a href='man_ser.php?tiposervicio=1' target="_blank" rel="external">Servicios</a></li>
                </ul>
            </div>
            <div data-role="popup" id="popup_configuracion" data-arrow="true">
                <ul data-role="listview" data-inset="true">
                    <li><a href='perfil.php' rel="external">Cambiar Contraseña</a></li>
                </ul>
            </div>
            <?php } ?>
            <?php if ($user['role'] == 6) { ?>
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
                <a href='n_paci.php' class="ui-btn ui-mini ui-btn-inline" rel="external">Nuevo Paciente</a>
            </div>
            <?php }
        if ($user['role'] == 7) { ?>
            <a href="perfil.php" class="ui-btn ui-mini ui-btn-inline" rel="external">Perfil</a>
            <?php }
        if ($user['role'] == 8) { ?>
            <a href="e_analisis_tipo.php" class="ui-btn ui-mini ui-btn-inline" rel="external">Tipo de Documento</a>
            <?php } ?>
            <h1>
                <?php
                if ($user['role'] == 1 || $user['role'] == 11 || $user['role'] == 12  || $user['role'] == 13 || $user['role'] == 14 || $user['role'] == 15 ) echo "Historia Clínica - Lista de Pacientes (".$login.")";
                if ($user['role'] == 2) echo "Laboratorio";
                if ($user['role'] == 3 || $user['role'] == 10 || $user['role'] == 19 || $user['role'] == 20) echo "Facturación y Boletas";
                if ($user['role'] == 4 && $login <> 'eco') echo "Análisis Clínico (".$login.")";
                if ($user['role'] == 4 && $login == 'eco') echo "Ecografía";
                if ($user['role'] == 5) echo "Recepción";
                if ($user['role'] == 6) echo "Consultas Ginecológicas";
                if ($user['role'] == 7) echo "Consultas Urológicas (".$login.")";
                if ($user['role'] == 8) echo "Legal";
            ?>
            </h1>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div>

        <div class="ui-content" role="main">
            <form action="" method="post" data-ajax="false" name="form1" id="form1" autocomplete="off">
                <?php
            if ( $user['role'] == 1 || $user['role'] == 11 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 14 || $user['role'] == 15)
            { ?>
                <div id="listapaciente">
                    <?php
                $rPaci = $db->prepare("SELECT
                    dni, ape, nom, sta,don, san, '' m_ale, '' m_ets
                    FROM hc_paciente
                    ORDER BY ape, nom asc");
                $rPaci->execute();

                $rAnal = $db->prepare("SELECT
                    DISTINCT hc_analisis.id, hc_analisis.a_dni, hc_analisis.a_nom, hc_analisis.a_sta, hc_analisis.lab, hc_analisis.a_exa
                    FROM hc_analisis, hc_paciente
                    WHERE hc_analisis.a_dni=hc_paciente.dni AND CAST(a_fec AS DATE) = CAST(CURRENT_TIMESTAMP AS DATE) AND  unaccent(hc_analisis.a_med) ilike ?");
                $rAnal->execute(array('%'.$login.'%'));

                $rAnal_H = $db->prepare("SELECT
                    DISTINCT hc_analisis.id, hc_analisis.a_dni, hc_analisis.a_nom, hc_analisis.a_sta, hc_analisis.lab, hc_analisis.a_exa
                    FROM hc_analisis, hc_pareja, hc_pare_paci
                    WHERE hc_analisis.a_dni=hc_pareja.p_dni AND hc_pare_paci.p_dni=hc_pareja.p_dni AND CAST(a_fec AS DATE) = CAST(CURRENT_TIMESTAMP AS DATE) AND unaccent(hc_analisis.a_med) ilike ? AND hc_analisis.a_exa <> 'ESPERMACULTIVO'");
                $rAnal_H->execute(array('%'.$login.'%'));

                $rBeta = $db->prepare("SELECT beta FROM lab_aspira_t WHERE med = ? AND beta = 0 and estado is true;");
                $rBeta->execute(array($login));

                $rBetaP = $db->prepare("SELECT beta FROM lab_aspira_t WHERE med = ? AND beta not in (0, 2) and estado is true;");
                $rBetaP->execute(array($login));

                if ($rAnal->rowCount() > 0 or $rAnal_H->rowCount() > 0 or $rBeta->rowCount() > 0 or $rBetaP->rowCount()) {
                    echo '<ul data-role="listview" data-theme="a" data-inset="true" class="analisis">';
                        if ($rAnal->rowCount() > 0 or $rAnal_H->rowCount() > 0) {
                            print('
                            <li data-role="list-divider" style="background-color: #E9A4A4;">
                                <span style="display: inline;">Resultados recientes de Análisis Clínicos y/o Ecografía</span>
                                <div data-role="popup" id="popupVideo" data-overlay-theme="b" data-theme="a" data-tolerance="15,15" class="ui-content">
                                    <a href="#" data-rel="back" class="ui-btn ui-btn-b ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-left">Close</a>
                                    <b>Leyenda de Estados</b><br>
                                    <span style="background-color: #FFEBCD;">Resultado: Positivo</span>
                                    <span style="background-color: #FFFF91;">Resultado: Negativo</span>
                                </div>
                            </li>');
                        }

                        while ($anal = $rAnal->fetch(PDO::FETCH_ASSOC)) {
                            $color_estado = '';
                            switch ($anal['a_sta']) {
                                case 'Positivo': $color_estado = '#FFEBCD'; break;
                                case 'Negativo': $color_estado = '#FFFF91'; break;
                                default: $color_estado = ''; break;
                            }

                            print('
                            <li>
                                <a href="e_analisis_detalle.php?id='.$anal['id'].'" rel="external">
                                    <small>'.$anal['a_nom'].' ('.$anal['a_dni'].')</small>
                                </a>
                                <span style="background-color: '.$color_estado.';" class="ui-li-count">'.mb_strtoupper($anal['lab']).' - '.$anal['a_exa'].' - '.$anal['a_sta'].'</span>
                            </li>');
                        }

                        while ($anal = $rAnal_H->fetch(PDO::FETCH_ASSOC)) {
                            $color_estado = '';
                            switch ($anal['a_sta']) {
                                case 'Positivo': $color_estado = '#FFEBCD'; break;
                                case 'Negativo': $color_estado = '#FFFF91'; break;
                                default: $color_estado = ''; break;
                            }
                            
                            print('
                            <li>
                                <a href="e_analisis_detalle.php?id='.$anal['id'].'" rel="external">
                                    <small>'.$anal['a_nom'].' ('.$anal['a_dni'].')</small>
                                </a>
                                <span style="background-color: '.$color_estado.';" class="ui-li-count">'.mb_strtoupper($anal['lab']).' - '.$anal['a_exa'].' - '.$anal['a_sta'].'</span>
                            </li>');
                        }

                        if ($rBeta->rowCount() > 0)
                            echo '<li data-role="list-divider" style="background-color: #FFFF91;"><a href="med-betas-lista.php?beta=0&medico=' . $login . '" rel="external" style="text-decoration: none;"><h4>BETAS PENDIENTES: '.$rBeta->rowCount().'</h4></a></li>';

                        if ($rBetaP->rowCount() > 0)
                            echo '<li data-role="list-divider" style="background-color: #FFEBCD;"><a href="med-betas-lista.php?beta=1&medico=' . $login . '" rel="external" style="text-decoration: none;"><h4>BETAS POSITIVAS: '.$rBetaP->rowCount().'</h4></a></li>';


                        echo '<li data-role="list-divider" style="background-color: #f8d7da;"><a href="lista_espermacultivo.php?med=1" rel="external" style=" text-decoration: none;"><h4>RESULTADOS DE ESPERMACULTIVOS</h4></a></li>';

                    echo '</ul>';
                } $key=$_ENV["apikey"];?>

                    <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
                    <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
                    <ol id="detallepaciente" data-role="listview" data-theme="a" data-filter="true" data-filter-placeholder="Digite un minimo de 3 caracteres para iniciar la busqueda" data-inset="true">
                        <?php
                    while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
                        break;
                    ?>
                        <li>
                            <a href='<?php echo "e_paci.php?id=".$paci['dni']; ?>' rel="external">
                                <h4><?php echo $paci['ape']; ?>
                                    <small><?php echo $paci['nom'].' ('.$paci['dni'].')'; ?></small>
                                </h4>
                                <p>
                                    <?php
                                    if ($paci['sta'] <> "") { echo '('.$paci['sta'].')'; }
                                    if ($paci['m_ale'] == "Medicamentada") { echo " <b>(ALERGIA MEDICAMENTADA) </b>"; }
                                    if (strpos($paci['san'], "-") !== false) { echo " <b>(SANGRE NEGATIVA) </b>"; }
                                    if (strpos($paci['m_ets'], "VIH") !== false) { echo " <b>(VIH) </b>"; }
                                    if (strpos($paci['m_ets'], "Hepatitis C") !== false) { echo " <b>(Hepatitis C) </b>"; } ?>
                                </p>
                            </a>
                            <?php if ($paci['don'] == "D") { echo '<span class="ui-li-count">Donante</span>'; } ?>
                        </li>
                        <?php }

                    if ($rPaci->rowCount() < 1) echo '<p><h3>¡ No hay Pacientes !</h3></p>'; ?>
                    </ol>
                </div>
                <?php }
            if ($user['role'] == 2 or $user['role'] == 5) { // LAB Y AGENDA
                if(!isset($_POST['ini']) || $_POST['ini'] == "") {
                    $_POST['ini'] = date("Y-m-d");
                }

                $rRepro = $db->prepare("SELECT * FROM
            (
                select
                coalesce(lab_aspira.pro, '') pro, coalesce(lab_aspira.dias, 0) dias,
                split_part(hc_reprod.f_asp, 'T', 2) AS h_asp,
                c.nombre turno, to_char((split_part(hc_reprod.f_asp, 'T', 2)::time + c.formato_hora_minuto::interval), 'HH24:MI') horafin,
                null h_tra, ape, nom, concat(upper(ape), ' ', upper(nom)) nombres_completos, hc_reprod.id, hc_reprod.dni, hc_reprod.med, don, hc_reprod.p_dni, hc_reprod.t_mue, coalesce(hc_reprod.n_fol, 0) n_fol, hc_reprod.p_dni_het, hc_reprod.p_od, hc_reprod.p_dtri, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_cri, hc_reprod.p_iiu, hc_reprod.p_don, hc_reprod.des_don,
                hc_reprod.des_dia,
                hc_reprod.obs, hc_reprod.p_extras, hc_reprod.anestesia
                , hc_paciente.medios_comunicacion_id
                FROM hc_paciente, hc_reprod
                left join man_turno_reproduccion c on c.codigo = hc_reprod.idturno
                left join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true
                where hc_reprod.estado = true and hc_paciente.dni=hc_reprod.dni and split_part(hc_reprod.f_asp, 'T', 1) = ? and coalesce(hc_reprod.cancela, 0) <> 1

                union

                select
                coalesce(lab_aspira.pro, '') pro, coalesce(lab_aspira.dias, 0) dias,
                split_part(hc_reprod.f_asp, 'T', 2) AS h_asp,
                c.nombre turno, to_char((h_tra::time + c.formato_hora_minuto::interval), 'HH24:MI') horafin,
                h_tra, ape, nom, concat(upper(ape), ' ', upper(nom)) nombres_completos, hc_reprod.id, hc_reprod.dni,hc_reprod.med,don,hc_reprod.p_dni,hc_reprod.t_mue, coalesce(hc_reprod.n_fol, 0) n_fol, hc_reprod.p_dni_het,hc_reprod.p_od,hc_reprod.p_dtri,hc_reprod.p_cic,hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_cri, hc_reprod.p_iiu, hc_reprod.p_don, hc_reprod.des_don,
                hc_reprod.des_dia,
                hc_reprod.obs, hc_reprod.p_extras, hc_reprod.anestesia
                , hc_paciente.medios_comunicacion_id
                FROM hc_paciente, hc_reprod
                inner join man_turno_reproduccion c on c.codigo = hc_reprod.idturno_tra
                left join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true
                where hc_reprod.estado = true and hc_paciente.dni=hc_reprod.dni and hc_reprod.f_tra=? and coalesce(hc_reprod.cancela, 0) <> 1

                union

                select
                '' pro, 0 dias, CONCAT(COALESCE(nullif(hc_gineco.in_h2,''),'00'),':',COALESCE(nullif(hc_gineco.in_m2,''),'00')) AS h_asp,
                c.nombre turno, to_char((CONCAT(COALESCE(nullif(hc_gineco.in_h2,''),'00'),':',COALESCE(nullif(hc_gineco.in_m2,''),'00'))::time + c.formato_hora_minuto::interval), 'HH24:MI') horafin,
                NULL AS h_tra, ape, nom, concat(upper(ape), ' ', upper(nom)) nombres_completos, hc_gineco.id,hc_gineco.dni,hc_gineco.med,hc_gineco.in_t AS don,NULL AS p_dni, NULL AS t_mue, NULL n_fol,NULL AS p_dni_het,NULL AS p_od,NULL AS p_dtri,NULL AS p_cic,NULL AS p_fiv,NULL AS p_icsi,NULL AS p_cri,NULL AS p_iiu,NULL AS p_don,NULL AS des_don,NULL AS des_dia,NULL AS obs,NULL AS p_extras, NULL anestesia
                , hc_paciente.medios_comunicacion_id
                from hc_paciente, hc_gineco
                inner join man_turno_reproduccion c on c.codigo = hc_gineco.idturno_inter
                where hc_paciente.dni=hc_gineco.dni and hc_gineco.in_f2 = ? and hc_gineco.in_c=1 and coalesce(hc_gineco.cancela, 0) = 0

                union

                select
                '' pro, 0 dias, CONCAT(COALESCE(nullif(hc_urolo.in_h2,''),'00'),':',COALESCE(nullif(hc_urolo.in_m2,''),'00')) AS h_asp,
                c.nombre turno, to_char((CONCAT(COALESCE(nullif(hc_urolo.in_h2,''),'00'),':',COALESCE(nullif(hc_urolo.in_m2,''),'00'))::time + c.formato_hora_minuto::interval), 'HH24:MI') horafin,
                NULL AS h_tra, hc_pareja.p_ape AS ape, hc_pareja.p_nom AS nom, concat(upper(hc_pareja.p_ape), ' ', upper(hc_pareja.p_nom)) nombres_completos, hc_urolo.id,hc_urolo.p_dni AS dni,hc_urolo.med,hc_urolo.in_t AS don,NULL AS p_dni, NULL AS t_mue,NULL AS n_fol,NULL AS p_dni_het,NULL AS p_od,NULL AS p_dtri,NULL AS p_cic,NULL AS p_fiv,NULL AS p_icsi,NULL AS p_cri,NULL AS p_iiu,NULL AS p_don,NULL AS des_don,NULL AS des_dia,NULL AS obs,NULL AS p_extras, NULL anestesia
                , 1 medios_comunicacion_id
                FROM hc_pareja, hc_urolo
                inner join man_turno_reproduccion c on c.codigo = hc_urolo.idturno_inter
                WHERE hc_pareja.p_dni=hc_urolo.p_dni AND hc_urolo.in_f2 = ?
            ) as a
            order by a.horafin asc, a.don asc");

            $rRepro->execute(array($_POST['ini'] ?? '1900-01-01', $_POST['ini'] ?? '1900-01-01', $_POST['ini'] ?? '1900-01-01', $_POST['ini'] ?? '1900-01-01'));



                if ($user['role'] == 5) {
                    $rMed = $db->prepare("SELECT userx FROM usuario WHERE role=1");
                    $rMed->execute();
                } ?>
                <div id="imprime">
                    <div class="enlinea">
                        <b>Fecha </b>
                        <input name="ini" type="date" id="ini" value="<?php if(isset($_POST['ini']))echo $_POST['ini']; ?>" onchange="handleIni(event);" data-mini="true">
                        <input name="VER" type="Submit" id="VER" value="VER" data-inline="true" data-mini="true" data-theme="b" class="noVer" />
                        <a href="javascript:PrintElem('#imprime')" data-role="button" data-mini="true" data-inline="true" rel="external" class="noVer">Imprimir</a>
                        <?php if ($user['role'] == 5 and 1==2) { ?>
                        <select name="agenda_med" id="agenda_med" data-mini="true"">
                            <option value="" selected>Agenda de Médico</option>
                            <?php while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option value=" <?php echo $med['userx']; ?>"><?php echo $med['userx']; ?></option>
                            <?php } ?>
                        </select>
                        <?php } ?>
                    </div>
                    <?php
                        if ($rRepro->rowCount() > 0) { ?>
                    <h3>Programación de Sala</h3>
                    <div class="scroll_h">
                        <table style="font-size:14px;width:100%;" class="tabla-agenda-inmater table-stripe ui-responsive">
                            <thead>
                                <tr>
                                    <th align="center">Hora Inicio</th>
                                    <th align="center">Hora Fin</th>
                                    <th align="center">Turno (min)</th>
                                    <th align="center">Paciente</th>
                                    <th align="center">Procedimientos</th>
                                    <th align="center">Muestra</th>
                                    <th align="center">Foliculos</th>
                                    <th align="center">Médico</th>
                                    <?php if ($user['role'] == 2) { ?>
                                    <th align="center">Extras Médico</th>
                                    <th align="center" class="noVer"></th><?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $id_pros='';
                                    while ($rep = $rRepro->fetch(PDO::FETCH_ASSOC)) {
                                    if ($user['role'] == 2 || ($user['role'] == 5 && ($rep['des_dia'] != "0" || ($rep['des_dia'] == "0" && $rep['h_tra'] <> ''))
                                        && ($rep['p_od'] == '' || $rep['h_tra'] <> ''))) { ?>
                                <?php
                                // buscar la clase segun el tipo de paciente
                                    $class_tipo_paciente = "";
                                    if (!$rep['t_mue'] || $rep['t_mue'] == 'x') {
                                        $class_tipo_paciente = "consulta_ginecologica";
                                    }
                                    if ($rep['medios_comunicacion_id'] === 2) {
                                        $class_tipo_paciente = "programa_inmater";
                                    }
                                    if ($rep['don'] == 'D') {
                                        $class_tipo_paciente = "tipo-paciente-donante";
                                    }
                                    print("<tr class='$class_tipo_paciente'>"); ?>
                                <td align="center">
                                    <?php
                                                if (empty($rep['p_od'])) {
                                                    if ($rep['h_asp'] <> '' && $rep['h_tra'] <> '' && !empty($rep['des_dia']) && $rep['des_dia'] == 0) {
                                                        print($rep['h_tra']);
                                                    }

                                                    if ($rep['h_asp'] <> '' && $rep['h_tra'] == '' && $rep['des_dia'] !== 0) {
                                                        print($rep['h_asp']);
                                                    }

                                                    if ($rep['h_tra'] <> '' && ($rep['des_dia'] !== 0 || ($rep['des_dia'] == "0" && $rep['h_tra'] <> ''))) {
                                                        print($rep['h_tra']);
                                                    }
                                                }

                                                if (!empty($rep['p_od']) && $rep['h_tra'] <> '') {
                                                    print($rep['h_tra']);
                                                }
                                                ?>
                                </td>
                                <?php
                                            if (empty($rep['p_od']) || (!empty($rep['p_od']) && $rep['h_tra'] <> '')) {
                                                print("
                                                    <td align='center'>".$rep['horafin']."</td>
                                                    <td align='center'>".$rep['turno']."</td>");
                                            } else {
                                                print('<td></td><td></td>');
                                            } ?>
                                <!-- paciente -->
                                <td>

                                    <?php
                                        if ($user['role'] == 2) {
                                            if ($rep['n_fol']=='-')
                                                $url="e_pare.php?id=&ip=".$rep['dni'];
                                            else
                                                $url="e_paci.php?id=".$rep['dni']; ?>
                                    <?php echo $rep['nombres_completos']; ?>&nbsp;<a target="_blank" title="Ir a la Historia Clinica" rel="external" href='<?php echo $url; ?>'><i class="fas fa-external-link-alt"></i></a>
                                    <?php
                                                if ($rep['don'] == 'D') echo ' (DONANTE)';
                                                if ($rep['p_od'] <> '') echo ' (RECEPTORA)';
                                        } else {
                                            echo $rep['nombres_completos'];
                                            if ($rep['don'] == 'D') echo ' (DONANTE)';
                                        } ?>
                                       
                                </td>
                                <!-- procedimientos -->
                                <td>
                                    <?php
                                    $examen=$url="";
                                    if ($rep['h_tra'] <> '') { $examen.='TRANSFERENCIA<br>'; }

                                    if ($user['role'] == 2) {
                                        if ($rep['pro'] != "") {
                                            $url = "le_aspi".($rep['dias']-1).".php?id=".$rep['pro'];
                                        } else {
                                            if ($rep['des_dia'] === 0 || $rep['des_dia'] >= 1) {
                                                $dias = 9;
                                            } else {
                                                $dias = 0;
                                            }

                                            $url = "le_aspi".$dias.".php?rep=".$rep['id'];
                                        }
                                        
                                        echo '<a href="'.$url.'" rel="external">';
                                    }

                                    if ($rep['p_dtri'] >= 1) { $examen.="DUAL TRIGGER<br>"; }

                                    if ($rep['p_cic'] >= 1) { $examen.="CICLO NATURAL<br>"; }
                                    //laboratorio=2, agenda=5
                                    if ($rep['p_fiv'] >= 1) {
                                        if ($user['role'] == 2) {
                                            $examen.="FIV<br>";
                                        } else {
                                            $examen.="ASPIRACIÓN<br>";
                                        }
                                    }

                                    if ($rep['p_icsi'] >= 1){
                                        if ($user['role'] == 2) {
                                            $examen.=$_ENV["VAR_ICSI"] . "<br>";
                                        } else {
                                            $examen.="ASPIRACIÓN<br>";
                                        }
                                    }

                                    if ($rep['p_od'] <> '') { $examen.="OD FRESCO<br>"; }

                                    if ($rep['p_cri'] >= 1) {
                                        if ($user['role'] == 2) {
                                            $examen.="CRIO ÓVULOS<br>";
                                        } else {
                                            $examen.="ASPIRACIÓN<br>";
                                        }
                                        
                                    }

                                    if ($rep['p_iiu'] >= 1) { $examen.="IIU<br>"; }

                                    if ($rep['p_don'] == 1) {
                                        if ($user['role'] == 2) {
                                            $examen.="DONACIÓN FRESCO<br>";
                                        } else {
                                            $examen.="ASPIRACIÓN<br>";
                                        }
                                    }
                                    $var = ['0'=>"", "1" => "Procedimiento sin sedación", "2" => "Procedimiento bajo sedación"];

                                    if ($rep['des_don'] == null && $rep['des_dia'] >= 1){
                                        if ($user['role'] == 2) {
                                            $examen.="TED<br>" . $var[($rep['anestesia'] === null ? 0 : $rep['anestesia'])];
                                            
                                        } else {
                                            $examen.="TRANSFERENCIA<br>";
                                        }
                                    }

                                    if ($rep['des_don'] == null && $rep['des_dia'] === 0){
                                        if ($user['role'] == 2) {
                                            $examen.="<small>Descongelación Óvulos Propios</small><br>";
                                        } else {
                                            $examen.="<small>Descongelación Óvulos</small><br>";
                                        }
                                    }

                                    if ($rep['des_don'] <> null && $rep['des_dia'] >= 1){
                                        if ($user['role'] == 2) {
                                            $examen.="EMBRIODONACIÓN<br>";
                                        } else {
                                            $examen.="TRANSFERENCIA<br>";
                                        }
                                    }

                                    if ($rep['des_don'] <> null and $rep['des_dia'] === 0){
                                        if ($user['role'] == 2) {
                                            $examen.="<small>Descongelación Óvulos Donados</small><br>";
                                        } else {
                                            $examen.="<small>Descongelación Óvulos</small><br>";
                                        }
                                    }

                                    // verificar si muestra transferencia acompañado de otro examen, solo debe mostrar transferencia
                                    if (strpos($examen, "TRANSFERENCIA") !== false) {
                                        $examen="TRANSFERENCIA<br>" . $var[($rep['anestesia'] === null ? 0 : $rep['anestesia'])];
                                    }

                                    print($examen);
                                    if ($user['role'] == 2 && $url != "#") {
                                        echo '</a>';
                                    }

                                    // buscar orden de intervencion
                                    $stmt = $db->prepare("SELECT nombre FROM man_gineco_tipo_intervencion WHERE estado = 1 AND nombre = ?;");
                                    $stmt->execute([$rep['don']]);

                                    if ($stmt->rowCount() > 0){
                                        $data = $stmt->fetch(PDO::FETCH_ASSOC);
                                        print(mb_strtoupper($data["nombre"]));
                                    }

                                    if ($rep['don'] == "Biopsia testicular") { echo "BIOPSIA TESTICULAR"; }
                                    if ($rep['don'] == "Aspiración de epidídimo") { echo "ASPIRACIÓN DE EPIDÍDIMO"; } ?>
                                </td>
                                <!-- muestra -->
                                <td><?php
                                    $t_mue = 'No Aplica';
                                    if ($rep['t_mue'] == 1) { $t_mue = 'Fresca'; }
                                    if ($rep['t_mue'] == 2) { $t_mue = 'Congelada'; }
                                    if ($rep['t_mue'] == 4) { $t_mue = 'Banco'; }
                                    echo $t_mue; ?>
                                </td>
                                <!-- foliculos -->
                                <td align="center">
                                    <?php
                                    if (strpos($examen, "TRANSFERENCIA") !== false) {
                                        print('--');
                                    } else {
                                        print($rep['n_fol']);
                                    } ?>
                                </td>
                                <!-- medico -->
                                <td><?php echo $rep['med']; ?></td>
                                <?php if ($user['role'] == 2) { ?>
                                <td><?php echo $rep['p_extras']; ?></td>
                                <td class="noVer">
                                    <small>
                                        <?php if ($rep['obs'] <> '') { ?>
                                        <a href="#obs<?php echo $rep['id']; ?>" data-rel="popup" data-transition="pop">Obs</a>
                                        <div data-role="popup" id="obs<?php echo $rep['id']; ?>" class="ui-content" style="font-size:14px;">
                                            <?php echo $rep['obs']; ?>
                                        </div>
                                        <?php }
                                        if (isset($rep['n_fol'])) {
                                            $id_pros.=$rep['id'].'|'; ?>
                                            <a href="info_ficha.php?id=<?php echo $rep['id']; ?>|&fec=<?php echo $_POST['ini']; ?>" target="new">Ficha</a>
                                        <?php } ?>
                                    </small>
                                </td>
                                <?php } ?>
                                </tr>
                                <?php }
                                } ?>
                            </tbody>
                        </table>
                        <?php
                            if ($user['role'] == 2) { ?>
                        <a href="info_ficha.php?id=<?php echo $id_pros; ?>&fec=<?php echo $_POST['ini']; ?>" target="new" class="noVer ui-btn ui-mini ui-btn-inline">Imprimir Fichas de Laboratorio</a>
                        <?php } ?>
                    </div>
                    <?php } ?>

                    <?php
                    $Rcap = $db->prepare("SELECT
                        p_dni, iiu, pro, h_cap
                        from lab_andro_cap
                        where fec = ? and iiu > 0
                        order by fec desc;");

                    $Rcap->execute(array($_POST['ini']??'1900-01-01'));

                    if ($Rcap->rowCount() > 0) { ?>
                    <h3>Programación de Capacitaciones (IIU)</h3>
                    <div class="scroll_h">
                        <table style="font-size:14px;width:100%;" class="table-stripe ui-responsive">
                            <thead>
                                <tr>
                                    <th align="center" id="orden">Hora</th>
                                    <th align="left">Paciente</th>
                                    <th align="left">Pareja o donante</th>
                                    <th align="left">Tipo de muestra</th>
                                    <th align="center">Médico</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($cap = $Rcap->fetch(PDO::FETCH_ASSOC)) {
                                    $rIIU = $db->prepare("SELECT dni, p_dni_het, med, case when t_mue = 1 then 'fresca' else 'congelada' end tipo_muestra
                                        FROM hc_reprod WHERE estado = true and id = ?;");
                                    $rIIU->execute(array($cap['iiu']));
                                    if ($rIIU->rowCount() == 0) {
                                        continue;
                                    }
                                    $iiu = $rIIU->fetch(PDO::FETCH_ASSOC);

                                    $rMujer = $db->prepare("SELECT nom, ape, med, dni FROM hc_paciente WHERE dni = ?;");
                                    $rMujer->execute(array($iiu['dni']));
                                    $mujer = $rMujer->fetch(PDO::FETCH_ASSOC);

                                    if (empty($cap['p_dni']) || $cap['p_dni'] == 1) {
                                        $rPare = $db->prepare("SELECT p_nom, p_ape, p_med FROM hc_pareja WHERE p_dni = ?;");
                                        $rPare->execute(array($iiu['p_dni_het']));
                                        $pare = $rPare->fetch(PDO::FETCH_ASSOC);
                                    } else {
                                        $rPare = $db->prepare("SELECT p_nom, p_ape, p_med FROM hc_pareja WHERE p_dni = ?;");
                                        $rPare->execute(array($cap['p_dni']));
                                        $pare = $rPare->fetch(PDO::FETCH_ASSOC);
                                    } ?>
                                <tr>
                                    <td style="text-align: center;"><?php echo $cap['h_cap']; ?></td>
                                    <td class="mayuscula">
                                        <?php echo "<a href='e_paci.php?id=" . $mujer["dni"] . "'>" . mb_strtoupper($mujer['ape']) . ' ' . mb_strtoupper($mujer['nom']) . "</a>"; ?>
                                    </td>
                                    <!-- pareja -->
                                    <td class="mayuscula">
                                        <?php
                                            if ($pare) {
                                                echo mb_strtoupper($pare['p_ape']) . ' ' . mb_strtoupper($pare['p_nom']);
                                            } else {
                                                echo 'NO MARCADO';
                                            } ?>
                                    </td>
                                    <?php print("<td>" . ucwords(mb_strtolower($iiu["tipo_muestra"])) . "</td>"); ?>
                                    <td style="text-align: center;"><?php echo $iiu['med']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                </div>
                <?php }

            if ($user['role'] == 3 || $user['role'] == 10 || $user['role'] == 19 || $user['role'] == 20) {

                $variables = [];

                if ($user['sede_id'] == 3) {
                    $variables = [3, 8];
                } else {
                    $variables = [8];
                }
    
                $in = str_repeat('?, ',  count($variables) - 1) . '?';
                $sedeEmp = (isset($_POST['id_sede']) && !empty($_POST['id_sede'])) ? ($_POST['id_sede']) : 3;

                if (isset($_POST['VER']) && $_POST['VER'] == "VER") {
                    $apellidos_nombres = "";
                    $fecha = "";
                    $t_pag = (isset($_POST['t_pag']) && !empty($_POST['t_pag'])) ? (" and ".$_POST['t_pag']) : "";
                    $t_ser = (isset($_POST['t_ser']) && !empty($_POST['t_ser'])) ? (" and ".$_POST['t_ser']) : "";
                    $med = (isset($_POST['med']) && !empty($_POST['med'])) ? (" and ".$_POST['med']) : "";

                    if (isset($_POST['ini']) && !empty($_POST['ini']) && isset($_POST['fin']) && !empty($_POST['fin'])) {
                        $fecha = " and fec between '".$_POST['ini']."' and '".$_POST['fin']."'";
                    }

                    if (!empty($_POST['apellidos_nombres'])) {
                        $apellidos_nombres = " and unaccent(nom) ilike ('%".$_POST['apellidos_nombres']."%')";
                    }

                    $rRec = $db->prepare("SELECT *
                        FROM recibos
                        WHERE 1=1 and sede_pago_id IN ($sedeEmp) $t_pag$t_ser$med$fecha$apellidos_nombres
                        ORDER BY fec DESC, id DESC");
                    $rRec->execute();
                } else {
                    $rRec = $db->prepare("SELECT *
                    from recibos
                    where sede_pago_id IN ($sedeEmp)
                    order by fec desc, id desc
                    limit 50");
                    $rRec->execute();
                }

                if ($rRec->rowCount() > 0) {
                    $rMed = $db->prepare("SELECT DISTINCT med FROM recibos");
                    $rMed->execute(); ?>
                <input name="anu_x" type="hidden"> <input name="anu_y" type="hidden">
                <input id="filtro" data-type="search" placeholder="Filtro..">
                <a href="lista_facturacion.php" rel="external"><small>Ir a la Nueva Versión</small></a>

                        

                <div style="display: flex; margin: 10px 0 10px 0;">
                    <select name="empresa" id="empresa" data-mini="true" required>

                        <?php
                            $consulta = $db->prepare("SELECT id, nom_comercial FROM man_empresas ");
                            $consulta->execute();
                            while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
                                $selected = "";

                                if (isset($_POST['empresa']) && $_POST['empresa'] == $row['id']) {
                                    $selected = "selected";
                                }
                                if (!isset($_POST['empresa']) && $row['id'] === 4){
                                    $selected = "selected";
                                }

                                print("<option value='" . $row['id'] . "' $selected>" . $row['nom_comercial'] . "</option>");
                            }
                        ?>
                    </select>
                    <select name="id_sede" id="id_sede" data-mini="true" required>
                        <?php 
                            $idSedeEmp = isset($_POST['id_sede'])  ? $_POST['id_sede'] : 3;
                            $sede_id = $db->prepare("SELECT id, nombre FROM sedes WHERE id=?");
                            $sede_id->execute(array($idSedeEmp));
                            
                            if (isset($pop['empresa'])) {
                                $sede_id = $sede_id->fetch(PDO::FETCH_ASSOC);
                                echo '<option value="'.$sede_id['id'].'" selected>'.$sede_id['nombre'].'</option>';
                            }else { 
                        ?>
                            <option value="" selected>SELECCIONAR</option>
                        <?php 
                            }
                        ?>

                    </select>
                </div>
                <div class="enlinea">
                    <small>Ver Desde:</small><input name="ini" type="date" id="ini" value="<?php if(isset($_POST['ini']))echo $_POST['ini']; ?>" data-mini="true">
                    <small>Hasta:</small><input name="fin" type="date" id="fin" value="<?php if(isset($_POST['fin']))echo $_POST['fin']; ?>" data-mini="true">
                    <small>Apellidos o Nombres:</small><input type="text" data-mini="true" name="apellidos_nombres" id="apellidos_nombres">
                    <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                        <select name="t_pag" id="t_pag" data-mini="true">
                            <option value="" selected>Medio Pago(Todos)</option>
                            <optgroup label="Seleccionar">
                                <?php
                                                $formaPago = forPago();
                                                foreach ($formaPago as $fila) {
                                                    echo '<option value="' . $fila['codigo_facturacion'] . '">' . $fila['tipotarjeta'] . '</option>';
                                                }
                                                ?>
                            </optgroup>
                        </select>
                        <select name="t_ser" id="t_ser" data-mini="true">
                            <option value="" selected>Servicio(Todos)</option>
                            <optgroup label="Seleccionar">
                                <option value=" t_ser=1" <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=1") echo "selected"; ?>>
                                    REPRODUCCION</option>
                                <option value=" t_ser=2" <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=2") echo "selected"; ?>>
                                    ANDROLOGIA</option>
                                <option value=" t_ser=3" <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=3") echo "selected"; ?>>
                                    PROCEDIMIENTOS</option>
                                <option value=" t_ser=4" <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=4") echo "selected"; ?>>
                                    ANALISIS</option>
                                <option value=" t_ser=5" <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=5") echo "selected"; ?>>
                                    PERFILES</option>
                                <option value=" t_ser=6" <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=6") echo "selected"; ?>>
                                    ECOGRAFIA</option>
                                <option value=" t_ser=7" <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=7") echo "selected"; ?>>
                                    ADICIONALES</option>
                            </optgroup>
                        </select>
                        <select name="med" id="med" data-mini="true">
                            <option value="" selected>Medico(Todos)</option>
                            <optgroup label="Seleccionar">
                                <?php while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) {
                                    $valmed = " med='".$med['med']."'"; ?>
                                <option value="<?php echo $valmed; ?>" <?php if(isset($_POST['med']))if ($_POST['med'] == $valmed) echo "selected"; ?>><?php echo $med['med']; ?>
                                </option>
                                <?php } ?>
                            </optgroup>
                        </select>
                        <input name="VER" type="Submit" id="VER" value="VER" data-inline="true" data-mini="true" data-theme="b" />
                    </div><br>
                    <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                        <a href="lista.php?todo=1" rel="external" class="ui-btn ui-corner-all ui-btn-inline ui-mini">Ver
                            Todo</a>
                        <a href="javascript:PrintElem('#imprime')" data-role="button" data-mini="true" data-inline="true" rel="external">Imprimir</a>
                        <a href="#" onclick="tableToExcel('dvData', 'facturacion')" class="ui-btn ui-mini ui-btn-inline">Exportar</a>
                    </div>
                </div>
                <div id="imprime" class="scroll_h">
                    <table width="100%" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive" id="dvData" style="font-size: small;">
                        <thead>
                            <tr>
                                <th width="7%">Fecha</th>
                                <th style="min-width: 70px;">Usuario</th>
                                <th width="5%">N° Recibo</th>
                                <th width="5%">Tipo<br>Moneda</th>
                                <th width="1%">Tipo<br>Documento</th>
                                <th width="1%">Tipo<br>Documento<br>Identidad</th>
                                <th width="25%">Nombre de Paciente</th>
                                <th width="10%">Medico</th>
                                <th width="20%">Tipo de Servicio</th>
                                <th width="7%">Total</th>
                                <th width="5%">T. Cambio</th>
                                <th width="5%">Medio Pago 1</th>
                                <th width="5%">Medio Pago 2</th>
                                <th width="5%">Medio Pago 3</th>
                                <th width="5%">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $tot_sol = 0;
                            $tot_dolar = 0;
                            while ($rec = $rRec->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr <?php if ($rec['anu'] == 1) echo 'bgcolor="#F9CCCD"'; ?>>
                                <td><?php echo date("d-m-Y", strtotime($rec['fec'])); ?></td>
                                <td style="text-align: center;"><?php print(mb_strtoupper($rec['idusercreate'])); ?>
                                </td>
                                <td>
                                    <?php
                                        $serie="";
                                        if ($rec['tip'] == 1 or $rec['tip'] == 2) {
                                            $serie="001-";
                                        ?>
                                    <a href='<?php echo "pago.php?id=".$rec['id']."&t=".$rec['tip']."&s=".$rec['t_ser']; ?>' rel="external">
                                        <?php
                                                print ($serie.sprintf('%05d', $rec['id']));
                                               ?>
                                    </a>
                                    <?php } else {
                                            echo sprintf('%05d', $rec['id']);
                                        } ?>
                                </td>
                                <td>
                                    <?php
                                        if ($rec['t_ser'] == 1 or $rec['t_ser'] == 2 or $rec['t_ser'] == 3) {
                                            if ($rec['mon'] == 1) echo "US"; else echo "MN";
                                        } else {
                                            if ($rec['mon'] == 1) echo "MN"; else echo "US";
                                        }
                                    ?></td>
                                <td><?php if ($rec['tip'] == 1) echo "BV";
                                        if ($rec['tip'] == 2) echo "FT";
                                        if ($rec['tip'] == 3) echo "BV Fisica";
                                        if ($rec['tip'] == 4) echo "FT Fisica"; ?></td>
                                <td><?php echo $rec['dni']; ?></td>
                                <?php print("
                                    <td>
                                        <a href='e_paci.php?pop=1&id=".$rec['dni']."' target='_blank'>".$rec['nom']."</a>
                                    </td>"); ?>
                                <a href="#popupVideo" style="display: none;" data-rel="popup" data-position-to="window" class="ui-btn">Antecedentes</a>
                                <td><?php echo $rec['med']; ?></td>
                                <td><a href="#" data="<?php echo $rec['id']."_".$rec['tip']; ?>" class="mas">
                                        <?php if ($rec['t_ser'] == 1) echo 'Reproducción Asistida';
                                            if ($rec['t_ser'] == 2) echo 'Andrología';
                                            if ($rec['t_ser'] == 3) echo 'Procedimientos Sala';
                                            if ($rec['t_ser'] == 4) echo 'Analisis Sangre';
                                            if ($rec['t_ser'] == 5) echo 'Perfiles';
                                            if ($rec['t_ser'] == 6) echo 'Ecografía';
                                            if ($rec['t_ser'] == 7) echo 'Adicionales'; ?></a>
                                    <?php $anglo = '';
                                        if ($rec['anglo'] <> '') {
                                            if (strpos($rec['anglo'], "Correcto") !== false)
                                                $anglo = '<font color="orange">Enviado</font>';
                                            else {
                                                if($rec['anglo'] == 'ok') {
                                                    $anglo = '<font color="green">Resultado Entregado</font>';
                                                } else {
                                                    $anglo = '<font color="red">Otros</font>';
                                                }
                                            }
                                        }
                                        echo '<br><small>'.$anglo.'</small>'; ?>
                                    <div id="<?php echo $rec['id']."_".$rec['tip']; ?>" class="mas2">
                                        <table style="font-size:10px; background-color:#FFFFFF;width:100%;" class="tablamas2"><?php echo $rec['ser']; ?></table>
                                        <?php if ($rec['man_ini'] > '2000-01-02') echo 'Inicio:'.date("d-m-Y", strtotime($rec['man_ini'])).' Fin:'.date("d-m-Y", strtotime($rec['man_fin'])); ?>
                                    </div>
                                </td>
                                <td><?php
                                        if ($rec['t_ser'] == 1 or $rec['t_ser'] == 2 or $rec['t_ser'] == 3) {
                                            if ($rec['mon'] == 1) echo "$&nbsp;".number_format($rec['tot'] - $rec['descuento'], 2, '.', ''); else echo "S/.&nbsp;".number_format($rec['tot'] - $rec['descuento'], 2, '.', '');
                                        } else {
                                            if ($rec['mon'] == 1) echo "S/.&nbsp;".number_format($rec['tot'] - $rec['descuento'], 2, '.', ''); else echo "$&nbsp;".number_format($rec['tot'] - $rec['descuento'], 2, '.', '');
                                        }
                                        ?></td>
                                <td><?php if ($rec['mon'] > 1) echo $rec['mon']; else "-"; ?></td>

                                <td><?php
                                        $paymentTypes = [];
                                        $formaPago = forPago();
                                                foreach ($formaPago as $fila) {
                                                    $paymentTypes[$fila['codigo_facturacion']] = $fila['tipotarjeta'];
                                                }
                                        if (isset($paymentTypes[$rec['t1']])) {
                                            echo $paymentTypes[$rec['t1']] . ' ';
                                            if ($rec['anu'] <> 1) {
                                                $variableName = 'dol' . $rec['t1'];
                                                if ($rec['m1'] == 1) $$variableName += $rec['p1'];
                                                else {
                                                    $variableName = 'sol' . $rec['t1'];
                                                    $$variableName += $rec['p1'];
                                                }
                                            }
                                        }
                                        
                                        echo ($rec['m1'] == 1) ? '$' : 'S/.';
                                        echo $rec['p1']; ?></td>
                                <td><?php
                                        $paymentTypes = [];
                                        $formaPago = forPago();
                                                foreach ($formaPago as $fila) {
                                                    $paymentTypes[$fila['codigo_facturacion']] = $fila['tipotarjeta'];
                                                }
                                        if (isset($paymentTypes[$rec['t2']])) {
                                            echo $paymentTypes[$rec['t2']] . ' ';
                                            if ($rec['anu'] <> 1) {
                                                $variableName = 'dol' . $rec['t2'];
                                                if ($rec['m2'] == 1) $$variableName += $rec['p2'];
                                                else {
                                                    $variableName = 'sol' . $rec['t2'];
                                                    $$variableName += $rec['p2'];
                                                }
                                            }
                                        }
                                        
                                        echo ($rec['m2'] == 1) ? '$' : 'S/.';
                                        echo $rec['p2'];
                                        ?></td>
                                <td><?php
                                        $paymentTypes = [];
                                        $formaPago = forPago();
                                                foreach ($formaPago as $fila) {
                                                    $paymentTypes[$fila['codigo_facturacion']] = $fila['tipotarjeta'];
                                                }
                                        if (isset($paymentTypes[$rec['t3']])) {
                                            echo $paymentTypes[$rec['t3']] . ' ';
                                            if ($rec['anu'] <> 1) {
                                                $variableName = 'dol' . $rec['t3'];
                                                if ($rec['m3'] == 1) $$variableName += $rec['p3'];
                                                else {
                                                    $variableName = 'sol' . $rec['t3'];
                                                    $$variableName += $rec['p3'];
                                                }
                                            }
                                        }
                                        
                                        echo ($rec['m3'] == 1) ? '$' : 'S/.';
                                        echo $rec['p3'];
                                        ?></td>
                                <td>
                                    <?php
                                       if ($rec['anu'] <> 1) {
                                        $variableName = 'dol' . $rec['t3'];
                                        if ($rec['m3'] == 1) $$variableName += $rec['p3'];
                                        else {
                                            $variableName = 'sol' . $rec['t3'];
                                            $$variableName += $rec['p3'];
                                        }
                                    }
                                        ?>
                                </td>
                                <td style="font-size:10px">
                                    <a href='<?php echo "pago_imp.php?id=".$rec['id']."&t=".$rec['tip']; ?>' rel="external" class="noVer">Imprimir</a>
                                    <?php if ($rec['t_ser'] == 1) { ?><a href='<?php echo "pago_veri.php?id=".$rec['id']."&t=".$rec['tip']; ?>' rel="external" class="noVer">Verificar</a><?php } ?>
                                    <?php if ($rec['anu'] == 1) echo '<br> Anulado'; ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr style="font-weight: bold;vertical-align:middle;">
                                <th></th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2">MONEDA SOLES S/.<br>
                                    <?php
                                   $paymentTypes = [];
                                   $formaPago = forPago();
                                                foreach ($formaPago as $fila) {
                                                    $paymentTypes[$fila['codigo_facturacion']] = $fila['tipotarjeta'];
                                                }
                                   $total = 0;
                                   foreach ($paymentTypes as $key => $value) {
                                       $variableName = 'sol' . $key;
                                       if (isset($$variableName)) {
                                           echo $value . ': ' . number_format($$variableName, 2) . '<br>';
                                           $total += $$variableName;
                                       }
                                   }
                                   echo 'Total Soles: ' . number_format($total, 2);
                                   ?>
                                </td>
                                <td></td>
                                <td colspan="4">MONEDA DOLARES $<br>
                                    <?php
                                $paymentTypes = [];
                                $formaPago = forPago();
                                             foreach ($formaPago as $fila) {
                                                 $paymentTypes[$fila['codigo_facturacion']] = $fila['tipotarjeta'];
                                             }
                                $total = 0;
                                foreach ($paymentTypes as $key => $value) {
                                    $variableName = 'dol' . $key;
                                    if (isset($$variableName)) {
                                        echo $value . ': ' . number_format($$variableName, 2) . '<br>';
                                        $total += $$variableName;
                                    }
                                }
                                echo 'Total Dolares: ' . number_format($total, 2);    
                                ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php } else {
                    echo '<p><h3>¡ No hay recibos !</h3></p>';
                } ?>
                <?php }

            if ($user['role'] == 4) {
                if (isset($_POST['anu_ngs']) && $_POST['anu_ngs'] <> "" and $_POST['dni_ngs'] <> "") {
                    $stmt = $db->prepare("DELETE FROM hc_analisis WHERE id=?");
                    $stmt->execute(array($_POST['anu_ngs']));
                    unlink("analisis/".$_POST['anu_ngs']."_".$_POST['dni_ngs'].".pdf");
                }
                if (isset($_POST['ini']) && $_POST['ini'] <> "" and $_POST['fin'] <> "" and $_POST['Buscar'] == "Buscar") {
                    $rAnal = $db->prepare("SELECT * FROM hc_analisis WHERE lab=? AND estado = 1 AND a_mue BETWEEN ? AND ? ORDER BY a_mue DESC");
                    $rAnal->execute(array($login, $_POST['ini'], $_POST['fin']));
                } else {
                    $rAnal = $db->prepare("SELECT
                        id, archivo_id, a_dni, a_mue, upper(trim(a_nom)) a_nom, lower(trim(a_med)) a_med, a_exa, upper(trim(a_sta)) a_sta, a_obs, idf, cor, lab, a_fec, estado
                        FROM hc_analisis WHERE lab=? AND estado = 1 ORDER BY a_mue DESC LIMIT 100 offset 0;");
                    $rAnal->execute(array($login));
                } ?>
                <input name="anu_ngs" type="hidden">
                <input name="dni_ngs" type="hidden">
                <?php
                if ($login <> 'genomics') { ?>
                <div class="ui-bar ui-bar-a">
                    <table style="margin: 0 auto;" width="100%">
                        <tr>
                            <td width="57%">
                                <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <a href="e_analisis.php?id=" class="ui-btn ui-btn-inline" data-theme="a" rel="external">NUEVO
                                        <?php if ($login == 'eco') echo 'ECOGRAFIA'; else echo 'EXAMEN'; ?></a>
                                    <a href="e_analisis_tipo.php" class="ui-btn ui-btn-inline" data-theme="a" rel="external">AGREGAR TIPO
                                        <?php if ($login == 'eco') echo 'ECOGRAFIA'; else echo 'EXAMEN'; ?></a>
                                </div>
                            </td>
                            <td width="10%">Mostrar Desde</td>
                            <td width="12%">
                                <input name="ini" type="date" required id="ini" value="<?php if(isset($_POST['ini']))echo $_POST['ini']; ?>" data-mini="true">
                            </td>
                            <td width="4%">Hasta</td>
                            <td width="12%">
                                <input name="fin" type="date" required id="fin" value="<?php if(isset($_POST['fin']))echo $_POST['fin']; ?>" data-mini="true">
                            </td>
                            <td width="5%">
                                <input name="Buscar" type="Submit" id="Buscar" value="Buscar" data-icon="search" data-iconpos="left" data-inline="true" data-mini="true" />
                            </td>
                        </tr>
                    </table>
                </div>
                <?php } ?>
                <?php if ($login == 'genomics') { ?>
                <div data-role="tabs" id="tabs">
                    <div data-role="navbar">
                        <ul>
                            <li><a href="#one" data-ajax="false" class="ui-btn-active">Otros</a></li>
                            <li><a href="#two" data-ajax="false">NGS</a></li>
                        </ul>
                    </div>
                    <?php } ?>
                    <div id="one">
                        <?php if ($rAnal->rowCount() > 0) { ?>

                        <?php if ($login == 'genomics') { ?>
                        <a href="e_analisis.php?id=" class="ui-btn ui-mini ui-btn-inline" data-theme="a" rel="external">NUEVO EXAMEN</a>
                        <?php } ?>
                        <input id="filtro" data-type="search" placeholder="Filtro..">
                        <table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive">
                            <thead>
                                <tr>
                                    <th><?php if ($login == 'eco') { echo 'Ecografía'; } else { echo 'Exámen'; } ?></th>
                                    <th>Apellidos y Nombres</th>
                                    <th>Médico</th>
                                    <?php if ($login <> 'eco') { echo '<th>Resultado</th>'; } ?>
                                    <th>Informe</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                            while ($anal = $rAnal->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                    <th>
                                        <a href='<?php echo "e_analisis.php?id=".$anal['id']; ?>' rel="external"><?php echo $anal['a_exa']; ?></a>
                                    </th>
                                    <td><?php echo $anal['a_nom']; ?></td>
                                    <td><?php echo $anal['a_med']; ?></td>
                                    <?php
                                    if ($login <> 'eco') echo '<td>'.$anal['a_sta'].'</td>'; ?>
                                    <th>
                                        <a href='<?php echo "archivos_hcpacientes.php?idArchivo=".$anal['id']."_".$anal['a_dni']; ?>' target="new">Ver/Descargar</a> <?php if ($anal['a_exa'] == 'NGS') { ?>- <a href="javascript:borrarNGS(<?php echo $anal['id'].','.$anal['a_dni']; ?>);">Eliminar</a><?php } ?>
                                    </th>
                                    <td><?php echo date("d-m-Y", strtotime($anal['a_mue'])); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } else {
                        echo '<p><h3>¡ No hay Registros !</h3></p>';
                    } ?>
                    </div>
                    <?php if ($login == 'genomics') { ?>
                    <div id="two">
                        <?php $rNgs = $db->prepare("SELECT hc_paciente.dni,ape,nom,hc_reprod.med,lab_aspira.pro,lab_aspira.f_fin FROM hc_paciente,lab_aspira,hc_reprod WHERE hc_reprod.estado = true and lab_aspira.estado is true and hc_paciente.dni=lab_aspira.dni AND hc_reprod.id=lab_aspira.rep AND lab_aspira.f_fin<>'1899-12-30' AND lab_aspira.tip<>'T' AND hc_reprod.pago_extras ILIKE '%NGS%' AND lab_aspira.dias>=5 ORDER BY ABS(pro) DESC");
                        $rNgs->execute();
                        if ($rNgs->rowCount() > 0) { ?>
                        <input id="filtrongs" data-type="search" placeholder="Filtro..">
                        <table data-role="table" data-filter="true" data-input="#filtrongs" class="table-stripe ui-responsive">
                            <thead>
                                <tr>
                                    <th>ID Protocolo</th>
                                    <th>Fecha Protocolo</th>
                                    <th>Apellidos y Nombres</th>
                                    <th>Médico</th>
                                    <th>Informe</th>
                                    <th>Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($ngs = $rNgs->fetch(PDO::FETCH_ASSOC)) {
                                    $rOvo = $db->prepare("SELECT ngs1 FROM lab_aspira_dias WHERE pro=? and estado is true AND ((d5d_bio<>0 AND d5f_cic='C') OR (d6d_bio<>0 AND d6f_cic='C'))");
                                    $rOvo->execute(array($ngs['pro']));
                                    if ($rOvo->rowCount() > 0) {
                                        if (file_exists("analisis/ngs_".$ngs['pro'].".pdf")) {
                                            $res = 'Negativo';
                                            while ($ovo = $rOvo->fetch(PDO::FETCH_ASSOC)) {
                                                if ($ovo['ngs1'] == 1) {
                                                    $res = 'Positivo';
                                                    break;
                                                }
                                            }
                                            $pdf = '<a href="archivos_hcpacientes.php?idArchivo=ngs_'.$ngs['pro'].'" target="new">Ver/Descargar</a>';
                                        } else {
                                            $res = '-';
                                            $pdf = 'PENDIENTE';
                                        } ?>
                                <tr>
                                    <td><?php echo $ngs['pro']; ?></td>
                                    <td><a href='<?php echo "e_ngs.php?id=".$ngs['pro']; ?>' rel="external"><?php echo date("d-m-Y", strtotime($ngs['f_fin'])); ?></a>
                                    </td>
                                    <td><?php echo $ngs['ape'].' '.$ngs['nom']; ?></td>
                                    <td><?php echo $ngs['med']; ?></td>
                                    <th><?php echo $pdf; ?></th>
                                    <th><?php echo $res; ?></th>
                                </tr>
                                <?php }
                                } ?>
                            </tbody>
                        </table>
                        <?php } else {
                            echo '<p><h3>¡ No hay Registros !</h3></p>';
                        } ?>
                    </div>
                </div>
                <?php } ?>
                <?php }

            if ($user['role'] == 8) {
                $rGine = $db->prepare("SELECT hc_paciente.dni,ape,nom,hc_gineco.id,hc_gineco.med,hc_gineco.repro,hc_gineco.fec FROM hc_paciente,hc_gineco WHERE hc_paciente.dni=hc_gineco.dni AND hc_gineco.repro<>'' AND hc_gineco.repro<>'NINGUNA' AND hc_gineco.legal=0 ORDER BY hc_gineco.fec DESC");
                $rGine->execute();

                $rAndro = $db->prepare("SELECT hc_pare_paci.p_dni,hc_pare_paci.dni,p_nom,p_ape,hc_pare_paci.p_het FROM hc_pareja,hc_pare_paci WHERE hc_pareja.p_dni=hc_pare_paci.p_dni ORDER BY p_ape,p_nom ASC");
                $rAndro->execute();

                $rLegal = $db->prepare("SELECT * FROM hc_legal ORDER BY a_mue DESC");
                $rLegal->execute(); ?>

                <div data-role="tabs" id="tabs">

                    <div data-role="navbar">
                        <ul>
                            <li><a href="#one" data-ajax="false" class="ui-btn-active">Ginecologia (Reprod.
                                    Asistida)</a></li>
                            <li><a href="#tre" data-ajax="false">Andrologia</a></li>
                            <li><a href="#two" data-ajax="false" class="color">PACIENTES ATENDIDOS</a></li>
                        </ul>
                    </div>
                    <div id="one">
                        <?php if ($rGine->rowCount() > 0) { ?>
                        <table data-role="table" class="table-stripe ui-responsive">
                            <thead>
                                <tr>
                                    <th>Apellidos y Nombres</th>
                                    <th>Médico</th>
                                    <th>Reprod. Asistida</th>
                                    <th>Consulta ginecologica</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($gine = $rGine->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                    <td><a href='<?php echo "e_legal.php?id=&gin=".$gine['id']; ?>' rel="external"><?php echo $gine['ape'].' '.$gine['nom']; ?></a>
                                    </td>
                                    <td><?php echo $gine['med']; ?></td>
                                    <th><?php echo $gine['repro']; ?></th>
                                    <th><?php echo date("d-m-Y", strtotime($gine['fec'])); ?></th>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } else {
                            echo '<p><h3>¡ No hay Registros !</h3></p>';
                        } ?>
                    </div>
                    <div id="tre">
                        <ol data-role="listview" data-theme="a" data-filter="true" data-filter-placeholder="Filtro..." data-inset="true">
                            <?php while ($andro = $rAndro->fetch(PDO::FETCH_ASSOC)) { ?>
                            <li>
                                <a href='<?php echo "e_legal.php?id=&andro=".$andro['p_dni']; ?>' rel="external">
                                    <h4><?php echo $andro['p_ape']; ?>
                                        <small><?php echo $andro['p_nom'].' ('.$andro['p_dni'].')'; ?></small>
                                    </h4>
                                </a>
                                <?php if ($andro['p_het'] > 0) echo '<span class="ui-li-count">Donante</span>'; ?>
                            </li>

                            <?php } ?>
                        </ol>
                    </div>
                    <div id="two">
                        <?php if ($rLegal->rowCount() > 0) { ?>
                        <input id="filtro" data-type="search" placeholder="Filtro..">
                        <table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Apellidos y Nombres</th>
                                    <th>Médico</th>
                                    <th>Resultado</th>
                                    <th>Informe</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($lega = $rLegal->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr style="font-size: small;">
                                    <td><a href='<?php echo "e_legal.php?id=".$lega['id']; ?>' rel="external"><?php echo $lega['a_exa']; ?></a></td>
                                    <td><?php echo $lega['a_nom']; ?></td>
                                    <td><?php echo $lega['a_med']; ?></td>
                                    <td><?php if ($lega['a_sta'] == 0) echo '<b>ATENDIDO</b>';
                                            if ($lega['a_sta'] == 1) echo 'APTO';
                                            if ($lega['a_sta'] == 2) echo 'OBSERVADO';
                                            if ($lega['a_sta'] == 3) echo 'NO APTO'; ?></td>
                                    <td><?php $ruta = 'legal/'.$lega['id'].'_'.$lega['a_dni'].'.pdf';
                                            if (file_exists($ruta)) { ?>
                                        <a href='<?php echo "archivos_hcpacientes.php?idLegal=".$lega['id']."_".$lega['a_dni'].".pdf"; ?>' target="new">Ver/Descargar</a>
                                        <?php if ($lega['fec_doc'] <> '1899-12-30') echo '<br>'.date("d-m-Y", strtotime($lega['fec_doc']));
                                            } else echo '-'; ?>
                                    </td>
                                    <td><?php echo date("d-m-Y", strtotime($lega['a_mue'])); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } else {
                            echo '<p><h3>¡ No hay Registros !</h3></p>';
                        } ?>
                    </div>

                </div>
                <?php } ?>

                <?php if ($user['role'] == 6) {
                $rPaci = $db->prepare("SELECT dni,ape,nom,med FROM hc_paciente");
                $rPaci->execute();
                $rMed = $db->prepare("SELECT userx,nom FROM usuario WHERE role=1");
                $rMed->execute(); ?>
                <input type="hidden" name="dni" id="dni">
                <table width="100%" align="center" style="margin: 0 auto;">
                    <tr>
                        <td width="30%" valign="top">
                            <ul data-role="listview" data-theme="c" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Buscar paciente por Nombre o DNI.." data-mini="true" class="fil_paci">
                                <?php while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) { ?>
                                <li><a href="#" class="paci_insert" dni="<?php echo $paci['dni']; ?>" med="<?php echo $paci['med']; ?>"><?php echo '<small>'.$paci['ape'].' '.$paci['nom'].'</small>'; ?></a><span class="ui-li-count"><?php echo $paci['dni']; ?></span></li>
                                <?php } ?>
                            </ul>
                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                <select name="med_agenda" id="med_agenda" data-mini="true" required>
                                    <option value="" selected>Seleccione Medico</option>
                                    <?php while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $med['userx']; ?>"><?php echo $med['nom']; ?></option>
                                    <?php } ?>
                                </select>
                                <select name="cupon" id="cupon">
                                    <option value=0 selected>Seleccionar Sede</option>
                                    <option value=1>Cono NORTE</option>
                                    <option value=2>Cono SUR</option>
                                    <option value=3>Tacna</option>
                                    <option value=4>Arequipa</option>
                                    <option value=4>San Judas Tadeo</option>
                                    <option value=5>San Borja</option>
                                </select>
                            </div>
                            Fecha y Hora de la consulta:
                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                <input name="fec" type="date" id="fec" value="<?php echo date("Y-m-d"); ?>" data-wrapper-class="controlgroup-textinput ui-btn">
                                <select name="fec_h" id="fec_h" required>
                                    <option value="">Hra</option>
                                    <option value="07">07 hrs</option>
                                    <option value="08">08 hrs</option>
                                    <option value="09">09 hrs</option>
                                    <option value="10">10 hrs</option>
                                    <option value="11">11 hrs</option>
                                    <option value="12">12 hrs</option>
                                    <option value="13">13 hrs</option>
                                    <option value="14">14 hrs</option>
                                    <option value="15">15 hrs</option>
                                    <option value="16">16 hrs</option>
                                    <option value="17">17 hrs</option>
                                    <option value="18">18 hrs</option>
                                    <option value="19">19 hrs</option>
                                    <option value="20">20 hrs</option>
                                </select>
                                <select name="fec_m" id="fec_m" required>
                                    <option value="">Min</option>
                                    <option value="00">00 min</option>
                                    <option value="15">15 min</option>
                                    <option value="30">30 min</option>
                                    <option value="45">45 min</option>
                                </select>
                            </div>
                            Motivo de la Consulta:<textarea name="mot" id="mot" data-mini="true" required></textarea>
                            <input type="Submit" value="AGENDAR CONSULTA" name="btn_consulta" data-icon="check" data-mini="true" data-theme="b" data-inline="true" />
                        </td>
                        <td width="70%" align="center" valign="top" class="td_agenda"></td>
                    </tr>
                </table>
                <?php } ?>

                <?php if ($user['role'] == 7) {
                $rMiPaci = $db->prepare("SELECT DISTINCT hc_urolo.p_dni,hc_pare_paci.dni,p_nom,p_ape,p_san,p_m_ets,p_m_ale FROM hc_pareja,hc_pare_paci,hc_urolo WHERE hc_pareja.p_dni=hc_urolo.p_dni AND hc_pareja.p_dni=hc_pare_paci.p_dni AND hc_urolo.med=? ORDER BY p_ape,p_nom ASC");
                $rMiPaci->execute(array($login));
                $rPaci = $db->prepare("SELECT hc_pare_paci.p_dni,hc_pare_paci.dni,p_nom,p_ape,p_san,p_m_ets,p_m_ale FROM hc_pareja,hc_pare_paci WHERE hc_pareja.p_dni=hc_pare_paci.p_dni AND hc_pare_paci.p_het=0 ORDER BY p_ape,p_nom ASC");
                $rPaci->execute(); ?>
                <div data-role="tabs" id="tabs">

                    <div data-role="navbar">
                        <ul>
                            <li><a href="#one" data-ajax="false" class="ui-btn-active">Pacientes atendidos</a></li>
                            <li><a href="#two" data-ajax="false">Todos los pacientes</a></li>
                        </ul>
                    </div>
                    <div id="one">
                        <input id="filtro" data-type="search" placeholder="Filtro..">
                        <table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive lista_orden"><br>
                            <thead>
                                <tr>
                                    <th>APELLIDOS Y NOMBRES</th>
                                    <th>DNI/PASAPORTE</th>
                                    <th>PAREJA</th>
                                    <th>MEDICO PAREJA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($paci = $rMiPaci->fetch(PDO::FETCH_ASSOC)) {
                                if ($paci['dni'] <> "") {
                                    $rPare = $db->prepare("SELECT nom,ape,med FROM hc_paciente WHERE dni=?");
                                    $rPare->execute(array($paci['dni']));
                                    $pare = $rPare->fetch(PDO::FETCH_ASSOC);
                                } ?>
                                <tr>
                                    <th><a href='<?php echo "e_pare.php?id=".$paci['dni']."&ip=".$paci['p_dni']; ?>' rel="external"><?php echo $paci['p_ape'].' <small>'.$paci['p_nom'].'</small>'; ?></a><br>
                                        <small style="opacity:.5;"><?php if ($paci['p_m_ale'] == "Medicamentada") { echo " (ALERGIA MEDICAMENTADA)"; }
                                            if (strpos($paci['p_san'], "-") !== false) { echo " (SANGRE NEGATIVA)"; }
                                            if (strpos($paci['p_m_ets'], "VIH") !== false) { echo " (VIH)"; }
                                            if (strpos($paci['p_m_ets'], "Hepatitis C") !== false) { echo " (Hepatitis C)"; } ?></small>
                                    </th>
                                    <td><?php echo $paci['p_dni']; ?></td>
                                    <td><?php if ($paci['dni'] <> "") echo $pare['ape'].' '.$pare['nom']; else echo 'Soltero'; ?>
                                    </td>
                                    <td><?php if ($paci['dni'] <> "") echo $pare['med']; else echo 'Particular'; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="two">
                        <input id="filtro2" data-type="search" placeholder="Filtro..">
                        <table data-role="table" data-filter="true" data-input="#filtro2" class="table-stripe ui-responsive lista_orden"><br>
                            <thead>
                                <tr>
                                    <th>APELLIDOS Y NOMBRES</th>
                                    <th>DNI/PASAPORTE</th>
                                    <th>PAREJA</th>
                                    <th>MEDICO PAREJA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
                                if ($paci['dni'] <> "") {
                                    $rPare = $db->prepare("SELECT nom,ape,med FROM hc_paciente WHERE dni=?");
                                    $rPare->execute(array($paci['dni']));
                                    $pare = $rPare->fetch(PDO::FETCH_ASSOC);
                                } ?>
                                <tr>
                                    <th><a href='<?php echo "e_pare.php?id=".$paci['dni']."&ip=".$paci['p_dni']; ?>' rel="external"><?php echo $paci['p_ape'].' <small>'.$paci['p_nom'].'</small>'; ?></a><br>
                                        <small style="opacity:.5;"><?php if ($paci['p_m_ale'] == "Medicamentada") echo " (ALERGIA MEDICAMENTADA)";
                                            if (strpos($paci['p_san'], "-") !== false) echo " (SANGRE NEGATIVA)";
                                            if (strpos($paci['p_m_ets'], "VIH") !== false) echo " (VIH)";
                                            if (strpos($paci['p_m_ets'], "Hepatitis C") !== false) echo " (Hepatitis C)"; ?></small>
                                    </th>
                                    <td><?php echo $paci['p_dni']; ?></td>
                                    <td><?php if ($paci['dni'] <> "") echo $pare['ape'].' '.$pare['nom']; else echo 'Soltero'; ?>
                                    </td>
                                    <td><?php if ($paci['dni'] <> "") echo $pare['med']; else echo 'Particular'; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } ?>
            </form>
        </div>

        <?php
    if ($user['role'] == 1 || $user['role'] == 11 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 14 || $user['role'] == 15) { ?>
        <div data-role="footer" data-position="fixed" id="footer">
            <p>
                <small>Total Pacientes: <?php echo $rPaci->rowCount(); ?></small>
            </p>
        </div>
        <?php } ?>
    </div>
    <script>
    $(function() {
        $("#orden").click();
        $('#alerta').delay(3000).fadeOut('slow');
    });

    $(document).ready(function() {
        $(document).on("click", "#link_transferencia_betas", function() {
            localStorage.setItem('back_url', 'lista.php');
            location.href = "labo-betas-resumen.php";
        });
    });
    </script>

    <script src="js/lista.js?v=1"></script>

    <script>
    jQuery(window).load(function(event) {
        multiSedeEmpresa($("#empresa").val());
    });

    $("#empresa").change(function() {
        multiSedeEmpresa($(this).val());
    });

    document.getElementById("form1").addEventListener("submit", function(event) {
        if($("#ini").val() == "" && $("#fin").val() == "" && $("#apellidos_nombres").val() == ""){
            alert("Coloque el rango de fecha o el nombre del paciente")
            event.preventDefault(); // Evita que el formulario se envíe
        }
    });

    function multiSedeEmpresa(idEmpresa){
        $("#id_sede").val(null).change();

        $.ajax({
                type: "POST",
                url: "_database/pago.php",
                dataType: "json",
                data: {
                    action: "sedeEmpresa",
                    idEmpresa: idEmpresa,
                },
                success: function (data) {
                    var select = $("#id_sede");

                    select.empty();

                    select.append('<option value="" >SELECCIONAR</option>');
                    
                    var sede_id = "<?php echo $sedeEmp; ?>";

                    $.each(data, function (index, sede) {
                        console.log(sede.id)
                        if(sede_id == sede.id){
                            select.append('<option value="' + sede.id + '" selected>' + sede.nombre + '</option>');
                        }else{
                            select.append('<option value="' + sede.id + '">' + sede.nombre + '</option>');
                        }
                        
                    });
                    select.trigger('chosen:updated');
                    select.trigger('change');
                },
                error: function(jqXHR, exception) {
                    console.log(jqXHR, exception);
                    console.log('Error: '+exception);
                },
            });
    }

    </script>

</body>

</html>