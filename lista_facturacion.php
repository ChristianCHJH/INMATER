<!DOCTYPE HTML>
<html>

<head>
    <title>Inmater Clínica de Fertilidad | Lista de Facturación</title>
    <?php
        include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
        integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/repo_conta.css">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="stylesheet" type="text/css" href="css/lista-facturacion.css?v=1">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="_themes/tema_lista_empresa.min.css" />
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-1.11.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script>
        
    $(document).ready(function() {
        $('#toggle_fullscreen').on('click', function(e) {
            $('#resultados table').toggleClass("mh-100 h-100"); //you can list several class names
            e.preventDefault();
            // if already full screen; exit
            // else go fullscreen
            if (
                document.fullscreenElement ||
                document.webkitFullscreenElement ||
                document.mozFullScreenElement ||
                document.msFullscreenElement
            ) {
                $("#toggle_fullscreen img").attr("src",
                    "_libraries/open-iconic/svg/fullscreen-enter.svg");
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            } else {
                $("#toggle_fullscreen img").attr("src",
                    "_libraries/open-iconic/svg/fullscreen-exit.svg");
                element = $('#resultados').get(0);
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
            }
        });

        $(".mas2").hide();
        $(".mas").click(function() {
            var mas = $(this).attr("data");
            $("#" + mas).toggle();
        });

        $('.ui-input-search').appendTo($('.enlinea'));

        $(".ui-input-search input").attr("id", "paci_nom");

        $('.paci_insert').click(function(e) {
            $('#paci_nom').val($(this).text());
            $('#dni').val($(this).attr("dni"));
            $('#paci_nom').textinput('refresh');
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

    function anularNotaCredito(tipDoc = null,id = null) {
        Swal.fire({
            title: 'Confirmar',
            text: '¿Está seguro de que desea eliminar la nota de credito?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (!result.isConfirmed) {
                return false
            }

            rutaApi = '<?php echo $_ENV["ruta_node_paciente"];?>/api/facturacion/eliminarComprobanteMiFact'

            $.ajax({
                url: rutaApi,
                type: 'POST',
                data: {
                    id: id,
                    tipo_comprobante: tipDoc
                    },
                contentType: 'application/x-www-form-urlencoded', 
                headers: {
                        'Authorization': 'Bearer <?php echo $_ENV["token_node_paciente"];?>'  
                    },
                success: function(data) {
                    window.location.reload();
                },
                error: function(jqXHR, exception) {
                    $("#verificar_texto").text("Registro No Encontrado");
                },
            });
            window.location.reload();
        });
        return false;
    }

    function anular(x, y) {
        if (confirm("CONFIRMA LA ANULACION DEL RECIBO: " + x + " ?")) {
            document.form1.anu_x.value = x;
            document.form1.anu_y.value = y;
            document.form1.submit();
            return true;
        } else return false;
    }

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
    })()

    id_sede = 0;
    </script>
</head>

<body>
    <div class="loader"><img src="_images/load.gif" alt=""></div>
    <?php require('_includes/menu_facturacion.php'); ?>
    <?php
    $formaPago = forPago();
    $listPos = posList();
    $estado = $db->prepare("SELECT * FROM factu_mifac_response_status WHERE codigo=?");
    $rUser = $db->prepare("SELECT * FROM usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC); ?>
    <div class="ui-content" role="main">
        <form action="" method="post" data-ajax="false" name="form1" id="form1">
            <?php
            if ($user['role'] == 3 || $user['role'] == 10 || $user['role'] == 19 || $user['role'] == 20) {
                if (isset($_POST['anu_x']) && !empty($_POST['anu_x']) && isset($_POST['anu_y']) && !empty($_POST['anu_y'])) {
                    $id = $_POST['anu_x'];
                    $tip = $_POST['anu_y'];
                    $hora_actual = date("Y-m-d H:i:s");
                    
                    require("_database/db_facturacion_electronica.php");
                    $data = array('id' => $id, 'tip' => $tip, 'login'=>$login);
                    $response = anular_facturacion_electronica($data);
                    
                    function updateRecibos($value,$estado_documento,$login, $hora_actual,$id,$tip){
                        global $db;
                        $stmt = $db->prepare("UPDATE recibos SET anu=?,status_mi_fac=?, iduserupdate=?,updatex=? WHERE id=? AND tip=?;");
                        $stmt->execute([$value,$estado_documento,$login, $hora_actual, $id, $tip]);
    
                        $log_Recibos = $db->prepare(
                            "INSERT INTO appinmater_log.recibos (
                                        recibo_id, recibo_tip, 
                                        sede_pago_id, 
                                        cli_atencion_unica_id, 
                                        fec, 
                                        dni, nom, med, sede, correo_electronico, numero_contacto, 
                                        id_tipo_documento_facturacion, ruc, raz, direccionfiscal, 
                                        t_ser,
                                        pak, ser, mon, tot, descuento, gratuito, total_cancelar, 
                                        bolsa_plastico, t1, m1, p1, banco1, tipotarjeta1, numerocuotas1,
                                        t2, m2, p2, banco2, tipotarjeta2, numerocuotas2, 
                                        t3, m3, p3, banco3, tipotarjeta3, numerocuotas3, 
                                        anu, veri, man_ini, man_fin, anglo, userx, comentarios,
                                        comprobante_referencia, 
                                        estado, 
                                        idusercreate, createdate, 
                                        action, 
                                        pos1_id, pos2_id, pos3_id
                                )
                            SELECT 
                                id, tip, 
                                sede_pago_id, 
                                cli_atencion_unica_id, 
                                fec, 
                                dni, nom, med, sede, correo_electronico, numero_contacto, 
                                id_tipo_documento_facturacion, ruc, raz, direccionfiscal,
                                t_ser, 
                                pak, ser, mon, tot, descuento, gratuito, total_cancelar, 
                                bolsa_plastico, t1, m1, p1, banco1, tipotarjeta1, numerocuotas1,
                                t2, m2, p2, banco2, tipotarjeta2, numerocuotas2, 
                                t3, m3, p3, banco3, tipotarjeta3, numerocuotas3, 
                                anu, veri, man_ini, man_fin, anglo, userx, comentarios, 
                                comprobante_referencia, 
                                estado, 
                                iduserupdate, updatex, 
                                'U',
                                pos1_id, pos2_id, pos3_id
                            FROM appinmater_modulo.recibos
                            WHERE id=? AND tip=?");
                        $log_Recibos->execute(array($id, $tip));
                    }          
                    
                    $stmt = $db->prepare("UPDATE recibos SET anu=1,status_mi_fac='999', iduserupdate=?,updatex=? WHERE id=? AND tip=? AND ((cpe_correlativo = '' AND cpe_serie = '') or (cpe_correlativo is null AND cpe_serie is null));");
                    $stmt->execute([$login, $hora_actual, $_POST['anu_x'], $_POST['anu_y']]);

                    if ($response["estado_documento"] == "108") {
                        updateRecibos(0,$response["estado_documento"],$login, $hora_actual, $_POST['anu_x'], $_POST['anu_y']);                        
                    }elseif ($response["estado_documento"] == "105") {
                        updateRecibos(1,$response["estado_documento"],$login, $hora_actual, $_POST['anu_x'], $_POST['anu_y']);
                    }
                    if (count($response) != 0) {
                        // grabar respuesta
                        $stmt = $db->prepare("INSERT INTO facturacion_recibo_mifact_response (
                    id_recibo, tip_recibo
                    , cadena_para_codigo_qr, cdr_sunat, codigo_hash, correlativo_cpe, errors, estado_documento, pdf_bytes, serie_cpe, sunat_description, sunat_note, sunat_responsecode, ticket_sunat, tipo_cpe, url, xml_enviado
                    , idusercreate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute(array($id, $tip, $response["cadena_para_codigo_qr"], $response["cdr_sunat"], $response["codigo_hash"], $response["correlativo_cpe"], $response["errors"], $response["estado_documento"], $response["pdf_bytes"], $response["serie_cpe"], $response["sunat_description"], $response["sunat_note"], $response["sunat_responsecode"], $response["ticket_sunat"], $response["tipo_cpe"], $response["url"], $response["xml_enviado"], $login));
                    }
                } 
                            $variables = [];
                            if (isset($_POST['empresa'] ) && $_POST['empresa'] == 5) {

                                switch ($_POST['id_sede']) {
                                    case 14:
                                        $variables = [14];
                                        break;
                                    case 15:
                                        $variables = [15];
                                        break;
                                    case 16:
                                        $variables = [16];
                                        break;
                                }

                            } else {
                                $variables = [3, 8];
                            }
                            $in = str_repeat('?, ',  count($variables) - 1) . '?';

                            if (isset($_POST['VER']) && !empty($_POST['VER'])) {
                                $apellidos_nombres = "";
                                $recibo_value = "";
                                $fecha = "";
                                $t_pag = (isset($_POST['t_pag']) && !empty($_POST['t_pag'])) ? ($_POST['t_pag']) : "";
                                if ($_POST['t_pag'] > 0) {
                                    $t_pag = $_POST['t_pag'];
                                    $medioPago = " AND (r.t1 ='$t_pag' OR r.t2 ='$t_pag' OR r.t3 ='$t_pag')";
                                } else {
                                    $medioPago = "";
                                }                                

                                $t_ser = (isset($_POST['t_ser']) && !empty($_POST['t_ser'])) ? (" AND " . $_POST['t_ser']) : "";
                                $med = (isset($_POST['med']) && !empty($_POST['med'])) ? (" AND " . $_POST['med']) : "";
                                $usuario = (!!$_POST['usuario']) ? (" AND r.idusercreate = '" . $_POST['usuario'] . "'") : "";
                                if (isset($_POST['ini']) && !empty($_POST['ini']) && isset($_POST['fin']) && !empty($_POST['fin'])) {
                                    $fecha = " AND (r.fec between '" . $_POST['ini'] . "' AND '" . $_POST['fin'] . "')";
                                }
                                if (!empty($_POST['apellidos_nombres'])) {
                                    $apellidos_nombres = " AND unaccent(r.nom) ilike ('%" . $_POST['apellidos_nombres'] . "%')";
                                }
                                if (!empty($_POST['recibo_value'])) {
                                    $recibo_value = " AND r.id = " . $_POST['recibo_value'];
                                }
                                $rRec = $db->prepare("SELECT
                                    r.*, r.cpe_serie serie, r.cpe_correlativo correlativo, coalesce(nc.id, 0) nota_credito_id, nc.createdate as nota_credito_createdate
                                    FROM recibos r
                                    INNER JOIN usuario u ON u.userx = r.idusercreate
                                    LEFT JOIN factu_notacredito nc on nc.estado = 1 and nc.recibo_tip = r.tip and nc.recibo_id = r.id
                                    WHERE r.sede_pago_id IN ($in) $medioPago$med$fecha$apellidos_nombres$usuario$recibo_value$t_ser
                                    ORDER BY r.fec DESC;");
                                $rRec->execute($variables);
                            } else {
                                $ini = date('Y-m-d');
                                $fin = date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days'));
                                $in = str_repeat('?, ',  count($variables) - 1) . '?';
                                $rRec = $db->prepare("SELECT
                                    r.*, r.cpe_serie serie, r.cpe_correlativo correlativo, coalesce(nc.id, 0) nota_credito_id
                                    from recibos r
                                    inner join usuario u on u.userx = r.idusercreate
                                    left join factu_notacredito nc on nc.estado = 1 and nc.recibo_tip = r.tip and nc.recibo_id = r.id
                                    where r.sede_pago_id in ($in) and r.fec between '$ini' and '$fin'
                                    order by r.fec desc;");
                                $rRec->execute($variables);
                            }
                            $rMed = $db->prepare("SELECT DISTINCT med FROM recibos");
                            $rMed->execute(); ?>
            <input name="anu_x" type="hidden">
            <input name="anu_y" type="hidden">
            
            <div class="container1">
                <div class="card mb-3">
                    <h5 class="card-header"><small><strong>Filtros</strong></small></h5>
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Ver desde</span>
                                    <input name="ini" type="date" class="form-control form-control-sm" id="ini"
                                        value="<?php if(isset($_POST['ini'])){echo $_POST['ini'];}else {echo date("Y-m-d");}?>" data-mini="true">
                                    <span class="input-group-text">hasta</span>
                                    <input name="fin" type="date" class="form-control form-control-sm" id="fin"
                                        value="<?php if(isset($_POST['fin'])){echo $_POST['fin'];}else {echo date("Y-m-d", strtotime(date("Y-m-d") . " +1 day"));}?>" data-mini="true">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Apellidos o Nombres</span>
                                    <input type="text" class="form-control form-control-sm" name="apellidos_nombres"
                                        value="<?php if(isset($_POST['apellidos_nombres']))print($_POST["apellidos_nombres"]); ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Médico</span>
                                    <select name="med" id="med" class="form-control form-control-sm">
                                        <option value="" selected>Todos</option>
                                        <optgroup label="Seleccionar">
                                            <?php while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) {
                                                    $valmed = " med='" . $med['med'] . "'"; ?>
                                            <option value="<?php echo $valmed; ?>"
                                                <?php if(isset($_POST['med']))if ($_POST['med'] == $valmed) echo "selected"; ?>>
                                                <?php echo $med['med']; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Medio de Pago</span>
                                    <select name="t_pag" id="t_pag" class="form-control form-control-sm">
                                        <option value="" selected>Todos</option>
                                        <optgroup label="Seleccionar">
                                            <?php
                                                
                                                foreach ($formaPago as $fila) {
                                                    echo '<option value="' . $fila['codigo_facturacion'] . '">' . $fila['tipotarjeta'] . '</option>';
                                                }
                                                ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Servicio</span>
                                    <select name="t_ser" id="t_ser" class="form-control form-control-sm">
                                        <option value="" selected>Todos</option>
                                        <optgroup label="Seleccionar">
                                            <option value=" t_ser=1"
                                                <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=1") echo "selected"; ?>>
                                                REPRODUCCION
                                            </option>
                                            <option value=" t_ser=2"
                                                <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=2") echo "selected"; ?>>ANDROLOGIA
                                            </option>
                                            <option value=" t_ser=3"
                                                <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=3") echo "selected"; ?>>
                                                PROCEDIMIENTOS
                                            </option>
                                            <option value=" t_ser=4"
                                                <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=4") echo "selected"; ?>>ANALISIS
                                            </option>
                                            <option value=" t_ser=5"
                                                <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=5") echo "selected"; ?>>PERFILES
                                            </option>
                                            <option value=" t_ser=6"
                                                <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=6") echo "selected"; ?>>ECOGRAFIA
                                            </option>
                                            <option value=" t_ser=7"
                                                <?php if(isset($_POST['t_ser']))if ($_POST['t_ser'] == " t_ser=7") echo "selected"; ?>>ADICIONALES
                                            </option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Usuario</span>
                                    <select name="usuario" id="usuario" class="form-control form-control-sm">
                                        <option value="" selected>Todos</option>
                                        <optgroup label="Seleccionar">
                                            <?php
                                            $consulta = $db->prepare("SELECT userx, nom as nombres FROM usuario WHERE \"role\" IN (3, 10);");
                                            $consulta->execute();
                                            while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
                                                $selected = "";
                                            
                                                if (isset($_POST['usuario']) && $_POST['usuario'] == $row['userx']) {
                                                    $selected = "selected";
                                                }
                                            
                                                print("<option value='" . $row['userx'] . "' $selected>" . mb_strtolower($row['nombres']) . "</option>");
                                            }
                                            ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">EMPRESA</span>
                                    <select name="empresa" id="empresa" class="form-control form-control-sm">
                                        <optgroup label="Seleccionar">
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
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <?php if (isset($_POST['id_sede']) && $_POST['id_sede']) {?>
                                <script>
                                    id_sede = <?php echo $_POST['id_sede']; ?>;
                                </script>
                            <?php } ?>

                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">SEDE</span>
                                    <select name="id_sede" id="id_sede" class="form-control form-control-sm">
                                        <optgroup label="Seleccionar"></optgroup>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Id Comprobante</span>
                                    <input type="text" class="form-control form-control-sm" name="recibo_value"
                                        value="<?php if(isset($_POST['recibo_value']))print($_POST["recibo_value"]); ?>">
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
                                <div class="input-group-prepend">
                                    <input name="VER" type="Submit" id="VER" value="Filtrar"
                                        class="btn btn-danger btn-sm" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3" id="resultados">
                    <h5 class="card-header">
                        <small><b>Lista</b> <a href="javascript:PrintElem('imprime')"
                                class="ui-btn ui-mini ui-btn-inline" rel="external"><img src="_images/printer.png"
                                    height="18" width="18" alt="icon name"></a></small>

                        <a href="#" id="toggle_fullscreen" class="float-right"><img
                                src="_libraries/open-iconic/svg/fullscreen-enter.svg" height="18" width="18"
                                alt="icon name"></a>
                    </h5>
                    <div id="imprime">
                        <table class="table table-responsive table-bordered align-middle table-sm Datos" id="myTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center" style="min-width: 150px;">Fecha</th>
                                    <th class="text-center" style="min-width: 100px;">Usuario</th>
                                    <th class="text-center" style="min-width: 100px;">Id Comprobante</th>
                                    <th class="text-center">Correlativo<br>Electrónico</th>
                                    <th class="text-center">Tipo<br>Moneda</th>
                                    <th class="text-center">Tipo<br>Documento</th>
                                    <th class="text-center">Tipo<br>Documento<br>Identidad</th>
                                    <th style="min-width: 400px;">Paciente</th>
                                    <th class="text-center" style="min-width: 150px;">Medico</th>
                                    <th class="text-center" style="min-width: 180px;">Tipo de Servicio</th>
                                    <th class="text-center" style="min-width: 180px;">Detalle</th>
                                    <th class="text-center" style="min-width: 100px;">Total</th>
                                    <th class="text-center">T. Cambio</th>
                                    <th class="text-center" style="min-width: 150px;">Medio Pago 1</th>
                                    <th class="text-center" style="min-width: 150px;">Medio Pago 2</th>
                                    <th class="text-center" style="min-width: 150px;">Medio Pago 3</th>
                                    <th class="text-center" style="min-width: 150px;">IZIPAY COD COMERCIO</th>
                                    <th class="text-center" style="min-width: 150px;">NIUBIZ COD COMERCIO</th>
                                    <th class="text-center">Moneda</th>
                                    <th class="text-center" style="min-width: 150px;">Voucher<br>Referencia</th>
                                    <th class="text-center">Consulta</th>
                                    <th class="text-center" style="min-width: 80px;">NC/ ND</th>
                                    <th class="text-center">Operaciones</th>
                                    <th class="text-center"style="min-width: 10em; align='center'">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $tot_sol = 0;
                                    $tot_dolar = 0;
                                    $nDolares=0;
                                    $nSoles=0;
                                    $iDolares=0;
                                    $iSoles=0;
                                    while ($rec = $rRec->fetch(PDO::FETCH_ASSOC)) {
                                        
                                        $color = '';
                                        if ($rec['anu'] == 1) {
                                            $color = 'style="color: black;background-color:#ECBCBC"';
                                        }
                                        if ($rec["nota_credito_id"] != 0) {
                                            $color = 'style=" color:black; background-color: #5eb7b7;"';
                                        } ?>
                                <tr <?php print($color); ?> >
                                    <td class="text-center"><?php echo date("Y-m-d H:i:s", strtotime($rec['fec'])); ?>
                                    </td>
                                    <td class="text-center"><?php print(mb_strtoupper($rec['idusercreate'])); ?></td>
                                    <td class="text-center">
                                        <?php
                                                $serie = "";
                                                if ($rec['tip'] == 1 or $rec['tip'] == 2) {
                                                    $serie = "001-";
                                                ?>
                                        <a href='<?php echo "pago.php?id=" . $rec['id'] . "&t=" . $rec['tip'] . "&s=" . $rec['t_ser']; ?>'
                                            target="_blank">
                                            <?php print(sprintf('%05d', $rec['id'])); ?>
                                        </a>
                                        <?php } else {
                                                    echo sprintf('%05d', $rec['id']);
                                                } ?>
                                    </td>
                                    <?php print("<td class='text-center'>" . $rec['correlativo'] . "</td>"); ?>
                                    <td class='text-center'>
                                        <?php
                                                if ($rec['t_ser'] == 1 || $rec['t_ser'] == 2 || $rec['t_ser'] == 3) {
                                                    if ($rec['mon'] == 1) {
                                                        echo "US";
                                                    } else {
                                                        echo "MN";
                                                    }
                                                } else {
                                                    if ($rec['mon'] == 1) {
                                                        echo "MN";
                                                    } else {
                                                        echo "US";
                                                    }
                                                }
                                                ?></td>
                                    <td class='text-center'><?php
                                                                    if ($rec['tip'] == 1) {
                                                                        echo "BV";
                                                                    }
                                                                    if ($rec['tip'] == 2) {
                                                                        echo "FT";
                                                                    }
                                                                    if ($rec['tip'] == 3) {
                                                                        echo "BV Fisica";
                                                                    }
                                                                    if ($rec['tip'] == 4) {
                                                                        echo "FT Fisica";
                                                                    } ?></td>
                                    <td class='text-center'><?php echo $rec['dni']; ?></td>
                                    <?php print("
                                <td>
                                    <a href='e_paci.php?id=" . $rec['dni'] . "' target='_blank'>" . mb_strtoupper($rec['nom']) . "</a>
                                </td>"); ?>
                                    <td class='text-center'><?php print(mb_strtoupper($rec['med'])); ?></td>
                                    <td class='text-center'>
                                        <a href="#" data="<?php echo $rec['id'] . "_" . $rec['tip']; ?>" class="mas">
                                            <?php
                                                    if ($rec['t_ser'] == 1) echo 'Reproducción Asistida';
                                                    if ($rec['t_ser'] == 2) echo 'Andrología';
                                                    if ($rec['t_ser'] == 3) echo 'Procedimientos Sala';
                                                    if ($rec['t_ser'] == 4) echo 'Análisis Sangre';
                                                    if ($rec['t_ser'] == 5) echo 'Perfiles';
                                                    if ($rec['t_ser'] == 6) echo 'Ecografía';
                                                    if ($rec['t_ser'] == 7) echo 'Adicionales'; ?></a>
                                        <?php $anglo = '';
                                                if ($rec['anglo'] <> '') {
                                                    if (strpos($rec['anglo'], "Correcto") !== false)
                                                        $anglo = '<font color="orange">Enviado</font>';
                                                    else {
                                                        if ($rec['anglo'] == 'ok') {
                                                            $anglo = '<font color="green">Resultado Entregado</font>';
                                                        } else {
                                                            $anglo = '<font color="red">Otros</font>';
                                                        }
                                                    }
                                                }
                                                echo '<br><small>' . $anglo . '</small>'; ?>
                                        <div id="<?php echo $rec['id'] . "_" . $rec['tip']; ?>" class="mas2">
                                            <?php if ($rec['man_ini'] > '2000-01-01') echo 'Inicio:' . date("d-m-Y", strtotime($rec['man_ini'])) . ' Fin:' . date("d-m-Y", strtotime($rec['man_fin'])); ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                    <?php
                                        $ser = $rec['ser'];
                                        $pattern = "/<td>(.*?)<\/td><td>(.*?)<\/td>/";
                                        if (preg_match($pattern, $ser, $matches)) {
                                          $centro = $matches[2];
                                        } else {
                                          $centro = "";
                                        }
                                        ?>
                                        <table <?php print($color); ?> class="tablamas2">
                                          <tr>
                                            <td><?php echo $centro; ?> / 
                                            <?php
                                              if ($rec['man_ini'] > '2000-01-01') {
                                                echo 'Inicio:'.date("d-m-Y", strtotime($rec['man_ini'])).' Fin:'.date("d-m-Y", strtotime($rec['man_fin'])).' ';
                                              }
                                              ?>
                                            </td>
                                          </tr>
                                        </table>
                                    </td>
                                    <td class='text-center'><?php
                                                                    if ($rec['t_ser'] == 1 || $rec['t_ser'] == 2 || $rec['t_ser'] == 3) {
                                                                        if ($rec['mon'] == 1) {
                                                                            echo "$&nbsp" . number_format($rec['tot'] - $rec['descuento'], 2, '.', '');
                                                                        } else {
                                                                            echo "S/.&nbsp" . number_format($rec['tot'] - $rec['descuento'], 2, '.', '');
                                                                        }
                                                                    } else {
                                                                        if ($rec['mon'] == 1) {
                                                                            echo "S/.&nbsp" . number_format($rec['tot'] - $rec['descuento'], 2, '.', '');
                                                                        } else {
                                                                            echo "$&nbsp" . number_format($rec['tot'] - $rec['descuento'], 2, '.', '');
                                                                        }
                                                                    }
                                                                    ?></td>
                                    <td class="text-center"><?php if ($rec['mon'] > 1) echo $rec['mon'];
                                                                    else {
                                                                        "-";
                                                                    } ?>
                                    </td>

                                    <td class="text-center">
                                        <?php
                                                // defino array de banco, tipo tarjeta y numero de cuotas
                                                $bancos_array = [
                                                    "1" => "BBVA",
                                                    "2" => "BCP",
                                                    "3" => "Dinners Club",
                                                    "4" => "Interbank",
                                                    "5" => "Otros",
                                                ];

                                                $tipotarjeta_array = [];
                                                $formapago_array = [];
                                                $tipoTarjeta = tipTarjeta();
                                                foreach ($tipoTarjeta as $fila) {
                                                    $tipotarjeta_array[$fila['codigo_facturacion']] = $fila['formapago'];
                                                }
                                                foreach ($formaPago as $fila) {
                                                    $formapago_array[$fila['codigo_facturacion']] = $fila['tipotarjeta'];
                                                }
                                                !!$rec['banco1'] ? print($bancos_array[$rec['banco1']] . "<br>") : "";
                                                !!$rec['t1'] ? print($formapago_array[$rec['t1']] . "<br>") : "";
                                                !!$rec['tipotarjeta1'] ? print($tipotarjeta_array[$rec['tipotarjeta1']] . "<br>") : "";
                                                !!$rec['numerocuotas1'] ? print("Cuotas: " . $rec['numerocuotas1'] . "<br>") : "";

                                                if ($rec['m1'] == 1) echo '$';
                                                else echo 'S/.';
                                                echo $rec['p1']; ?></td>
                                    <td class="text-center">
                                        <?php
                                                !!$rec['banco2'] ? print($bancos_array[$rec['banco2']] . "<br>") : "";
                                                !!$rec['t2'] ? print($formapago_array[$rec['t2']] . "<br>") : "";
                                                !!$rec['tipotarjeta2'] ? print($tipotarjeta_array[$rec['tipotarjeta2']] . "<br>") : "";
                                                !!$rec['numerocuotas2'] ? print("Cuotas: " . $rec['numerocuotas2'] . "<br>") : "";


                                                if ($rec['m2'] == 1) echo '$';
                                                else echo 'S/.';
                                                echo $rec['p2']; ?></td>
                                    <td class="text-center">
                                        <?php
                                                !!$rec['banco3'] ? print($bancos_array[$rec['banco3']] . "<br>") : "";
                                                !!$rec['t3'] ? print($formapago_array[$rec['t3']] . "<br>") : "";
                                                !!$rec['tipotarjeta3'] ? print($tipotarjeta_array[$rec['tipotarjeta3']] . "<br>") : "";
                                                !!$rec['numerocuotas3'] ? print("Cuotas: " . $rec['numerocuotas3'] . "<br>") : "";


                                                if ($rec['m3'] == 1) echo '$';
                                                else echo 'S/.';
                                                echo $rec['p3']; ?></td>

                                    
                                    <?php 
                                                                        
                                    $pos1 = !empty($rec["pos1_id"]) ? intval($rec["pos1_id"]) : 1;
                                    $pos2 = !empty($rec["pos2_id"]) ? intval($rec["pos2_id"]) : 1;
                                    $pos3 = !empty($rec["pos3_id"]) ? intval($rec["pos3_id"]) : 1;
                                    $niuCodCom1= posCodComercio($pos1,'NIUBIZ');
                                    $niuCodCom2= posCodComercio($pos2,'NIUBIZ');
                                    $niuCodCom3= posCodComercio($pos3,'NIUBIZ');
                                    $niuCod1=$niuCodCom1[0]['codigo']??' - ';
                                    $niuCod2=$niuCodCom2[0]['codigo']??' - ';
                                    $niuCod3=$niuCodCom3[0]['codigo']??' - ';
                                    $iziCodCom1= posCodComercio($pos1,'IZIPAY');
                                    $iziCodCom2= posCodComercio($pos2,'IZIPAY');
                                    $iziCodCom3= posCodComercio($pos3,'IZIPAY');
                                    $iziCod1=$iziCodCom1[0]['codigo']??' - ';
                                    $iziCod2=$iziCodCom2[0]['codigo']??' - ';
                                    $iziCod3=$iziCodCom3[0]['codigo']??' - ';
                                    
                                    ?>
                                    <td class="text-center">
                                        <?php 
                                            echo $iziCod1.' , ',$iziCod2.' , ',$iziCod3.' , ';
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            echo $niuCod1.' , ',$niuCod2.' , ',$niuCod3.' , ';
                                        ?>
                                    </td>


                                    <td class="text-center">
                                        <?php
                                                $types = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
                                                $solVars = array("sol1", "sol2", "sol3", "sol4", "sol5", "sol6", "sol7", "sol8", "sol9");
                                                $dolVars = array("dol1", "dol2", "dol3", "dol4", "dol5", "dol6", "dol7", "dol8", "dol9");
                                                                                            
                                                foreach (array_merge($solVars, $dolVars) as $var) {
                                                    if (empty($$var)) {
                                                        $$var = 0;
                                                    }
                                                }
                                                for ($i = 0; $i < count($types); $i++) {
                                                    //medios de pago
                                                    if ($rec['t3'] == $types[$i] && $rec['anu'] <> 1 && $rec['gratuito'] <> 1) {
                                                        if ($rec['m3'] == 1) {
                                                            ${"dol" . ($i + 1)} += $rec['p3'];
                                                        } else {
                                                            ${"sol" . ($i + 1)} += $rec['p3'];
                                                        }
                                                    }
                                                    if ($rec['t2'] == $types[$i] && $rec['anu'] <> 1 && $rec['gratuito'] <> 1) {
                                                        if ($rec['m2'] == 1) {
                                                            ${"dol" . ($i + 1)} += $rec['p2'];
                                                        } else {
                                                            ${"sol" . ($i + 1)} += $rec['p2'];
                                                        }
                                                    }
                                                    if ($rec['t1'] == $types[$i] && $rec['anu'] <> 1 && $rec['gratuito'] <> 1) {
                                                        if ($rec['m1'] == 1) {
                                                            ${"dol" . ($i + 1)} += $rec['p1'];
                                                        } else {
                                                            ${"sol" . ($i + 1)} += $rec['p1'];
                                                        }
                                                    }
                                                }   
                                                
                                                foreach ($listPos as $pos) {
                                                    if ($pos['nombrepos'] == 'IZIPAY'  && $rec['anu'] <> 1 && $rec['gratuito'] <> 1) {
                                                        if ($pos['id'] == $rec['pos1_id'] ) {
                                                            $iDolares += ($rec['m1'] == 1) ? $rec['p1'] : 0;
                                                            $iSoles += ($rec['m1'] == 0) ? $rec['p1'] : 0;
                                                        }
                                                        
                                                        if ($pos['id'] == $rec['pos2_id'] ) {
                                                            $iDolares += ($rec['m2'] == 1) ? $rec['p2'] : 0;
                                                            $iSoles += ($rec['m2'] == 0) ? $rec['p2'] : 0;
                                                        }
                                                        
                                                        if ($pos['id'] == $rec['pos3_id'] ) {
                                                            $iDolares += ($rec['m3'] == 1) ? $rec['p3'] : 0;
                                                            $iSoles += ($rec['m3'] == 0) ? $rec['p3'] : 0;
                                                        }
                                                    }
                                                    if ($pos['nombrepos'] == 'NIUBIZ'  && $rec['anu'] <> 1 && $rec['gratuito'] <> 1) {
                                                        if ($pos['id'] == $rec['pos1_id'] ) {
                                                            $nDolares += ($rec['m1'] == 1) ? $rec['p1'] : 0;
                                                            $nSoles += ($rec['m1'] == 0) ? $rec['p1'] : 0;
                                                        }
                                                        
                                                        if ($pos['id'] == $rec['pos2_id'] ) {
                                                            $iDolares += ($rec['m2'] == 1) ? $rec['p2'] : 0;
                                                            $nSoles += ($rec['m2'] == 0) ? $rec['p2'] : 0;
                                                        }
                                                        
                                                        if ($pos['id'] == $rec['pos3_id'] ) {
                                                            $nDolares += ($rec['m3'] == 1) ? $rec['p3'] : 0;
                                                            $nSoles += ($rec['m3'] == 0) ? $rec['p3'] : 0;
                                                        }
                                                    }
                                                }
                                                
                                                if ($rec['t_ser'] == 1 or $rec['t_ser'] == 2 or $rec['t_ser'] == 3) {
                                                    echo '$';
                                                } else {
                                                    echo 'S/.';
                                                }
                                                ?>
                                    </td>
                                    <?php print('<td class="text-center">' . $rec["comprobante_referencia"] . '</td>'); ?>
                                    <td class="text-center">
                                        <?php
                                                if ($rec['t_ser'] == 1) {
                                                    print("<img src='_images/modal.png' height='18' width='18' title='Reproducción Asistida' class='btn_reproduccion_asistida' data-origen='reproduccion_asistida' data-tip-recibo='" . $rec['tip'] . "' data-num-recibo='" . $rec['id'] . "'>");
                                                }

                                                if ($login == 'testfacturacion') {
                                                    print("<img src='_images/invoice.png' height='18' width='18' title='Consultar Estado Comprobante' class='btn_factura_electronica' data-origen='modal_factura_electronica' data-tip-recibo='" . $rec['tip'] . "' data-num-recibo='" . $rec['id'] . "'>"); 
                                                }
                                        ?>
                                    </td>
                                    <?php
                                            print("<td class='text-center'>");
                                            if ($user['role'] == 3 || $user['role'] == 10 || $user['role'] == 19 || $user['role'] == 20) {
                                                print("<img src='_images/payment-method.png' height='18' width='18' title='NC/ ND' class='btn_documento_credito' data-origen='modal_documento_credito' data-tip-recibo='" . $rec['tip'] . "' data-num-recibo='" . $rec['id'] . "' data-monto-dif='" . (number_format($rec['tot'] - $rec['descuento'], 2, '.', '')) . "'>");
                                            }
                                            if ($rec["nota_credito_id"] != 0) {
                                                print("
                                        <a href='factu_mifact_impresion.php?" . "tip=3&id=" . $rec["nota_credito_id"] . "' target='_blank' rel='external'>
                                            <img src='_images/printer.png' title='Imprimir' height='18' width='18' alt='icon name'>
                                        </a>"); 
                                        
                                        $fecha_actual_NC = new DateTime();
                                        $fecha_emision_NC = new DateTime($rec['nota_credito_createdate']);

                                        $diferencia_NC = $fecha_actual_NC->diff($fecha_emision_NC);

                                        if($diferencia_NC != null && $diferencia_NC->days < 6) { ?>
                                            <a href="javascript:anularNotaCredito(3, <?php echo $rec['nota_credito_id']; ?>);" class="noVer"><img src="_images/block.png" title="Anular" height="18" width="18" alt="icon name"></a>

                                        <?php  
                                        }
                                        }
                                            print("</td>"); ?>
                                    <td class="text-center">
                                        <?php
                                                if (!empty($rec['correlativo'])) {
                                                    print("<a href='pago_imp_pdf.php?id=" . $rec['id'] . "&tip=" . $rec['tip'] . "' target='_blank' rel='external' class='noVer'>
                                            <img src='_images/printer.png' title='Imprimir' height='18' width='18' alt='icon name'>
                                        </a>");
                                                } else {
                                                    print("<a href='pago_imp_1.php?id=" . $rec['id'] . "&t=" . $rec['tip'] . "' target='_blank' rel='external' class='noVer'>
                                            <img src='_images/printer.png' title='Imprimir' height='18' width='18' alt='icon name'>
                                        </a>");
                                                } ?>
                                        <?php

                                        $fecha_actual = new DateTime();
                                        $fecha_emision = new DateTime($rec['fec']);

                                        $diferencia = $fecha_actual->diff($fecha_emision);

                                        if ($user['role'] == 3 || $user['role'] == 10 || $user['role'] == 19 || $user['role'] == 20) {
                                                if ($rec['anu'] == 1) {
                                                    echo 'Anulado';
                                                } else if($diferencia->days < 6 || !$rec['correlativo']) {
                                                    if ($user['role'] == 3 && $rec['status_mi_fac'] != '108') { ?>
                                                        <a href="javascript:anular(<?php echo $rec['id'] . ', ' . $rec['tip']; ?>);"
                                                        class="noVer"><img src="_images/block.png" title="Anular" height="18"
                                                        width="18" alt="icon name"></a>
                                                    <?php }
                                                }
                                        } ?>
                                    </td>
                                    <?php 
                                    $estado->execute(array($rec['status_mi_fac']));
                                    $nomEst = $estado->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <!-- ESTADO DE RECIBO EN MIFAC -->
                                    <td style="text-align: center;"><strong>
                                        <?php echo $nomEst['nombre']??'';?>
                                        </strong></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mb-3">
                    <h5 class="card-header">
                        <small><strong>Resumen</strong></small>
                    </h5>
                    <div class="mx-auto">
                        <table class="table table-responsive table-bordered align-middle table-sm"
                            style="margin-bottom: 0 !important; font-size: small;">
                            <thead class="thead-dark">
                                <tr>
                                    <th>TIPO TARJETA</th>
                                    <th style="text-align: center;">MONEDA SOLES</th>
                                    <th style="text-align: center;">MONEDA DOLARES</th>
                                </tr>
                            </thead>
                            <tbody>

                                        <?php                            
                                            $soles = [];
                                            foreach ($formaPago as $fila) {
                                                $variable = 'sol' . $fila['codigo_facturacion'];
                                                if (!isset(${$variable})) {
                                                    ${$variable} = 0;
                                                }
                                                $soles[$fila['tipotarjeta']] = ${$variable};
                                            }                                            

                                            $dolares = [];
                                            foreach ($formaPago as $fila) {
                                                $variable2 = 'dol'. $fila['codigo_facturacion'];
                                                if(!isset(${$variable2})){
                                                    ${$variable2}=0;
                                                }
                                                $dolares[$fila['tipotarjeta']] = ${$variable2};
                                            } 

                                            foreach ($soles as $key => $value) {
                                                if (isset($dolares[$key])) {
                                                    $valorSoles = $value;
                                                    $valorDolares = $dolares[$key];
                                                    
                                                echo '<tr><td>'.$key.'</td><td style="text-align: center;">'.number_format($valorSoles, 2).'</td><td style="text-align: center;">'.number_format($valorDolares, 2).'</td></tr>';
                                                }
                                            }
                                            echo '<tr><td><b>Total Soles</b></td><td style="text-align: center;"><b>'.number_format(array_sum($soles), 2).'</b></td><td style="text-align: center;"><b>'.number_format(array_sum($dolares), 2).'</b></td></tr>';
                                            echo '<tr><td>Total Niubiz</td><td style="text-align: center;">'.number_format($nSoles, 2).'</td><td style="text-align: center;">'.number_format($nDolares, 2).'</td></tr>';
                                            echo '<tr><td>Total Izipay</td><td style="text-align: center;">'.number_format($iSoles, 2).'</td><td style="text-align: center;">'.number_format($iDolares, 2).'</td></tr>';

                                            ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <a href="lista.php" rel="external"><small>Ir a la versión antigua</small></a>
            </div>

            <?php
            } ?>
        </form>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true"
        id="modal-confirm-tipo-cambio">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tipo de cambio del día</h5>
                </div>
                <form id="form-tipo-cambio">
                    <div class="modal-body">
                        <p>Debes ingresar el tipo de cambio del día de hoy.</p>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo cambio compra</span>
                                    <input class="form-control form-control-sm" name="tipo_cambio_compra" type="number"
                                        min="0" value="0" step=".001" required>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo cambio venta</span>
                                    <input class="form-control form-control-sm" name="tipo_cambio_venta" type="number"
                                        min="0" value="0" step=".001" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" id="tipo-cambio-add" name="tipo-cambio-add" form="form-tipo-cambio"
                            class="btn btn-sm btn-danger" value="Agregar">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true"
        id="ver_documento_credito">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <!-- <form method="post" name="frm_documento_credito" id="frm_documento_credito" action="#"> -->
            <div class="modal-content">
                <input type="hidden" name="recibo_tip" id="recibo_tip">
                <input type="hidden" name="recibo_id" id="recibo_id">
                <input type="hidden" name="sede_id" id="sede_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Generar NC/ ND</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo</span>
                                    <select name="comprobantetipo_id" id="comprobantetipo_id"
                                        class="form-control form-control-sm">
                                        <option value="">Seleccionar</option>
                                        <option value="3">Nota de crédito</option>
                                        <option value="4">Nota de débito</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Serie</span>
                                    <input name="serie" type="text" class="form-control form-control-sm" id="serie"
                                        data-mini="true" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Correlativo</span>
                                    <input name="correlativo" type="text" class="form-control form-control-sm"
                                        id="correlativo" data-mini="true" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Motivo</span>
                                    <select name="motivotipo_id" id="motivotipo_id"
                                        class="form-control form-control-sm">
                                        <option value="">Seleccionar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo Documento</span>
                                    <select name="documentotipo_id" id="documentotipo_id"
                                        class="form-control form-control-sm" style="pointer-events: none;">
                                        <option value="">Seleccionar</option>
                                        <option value="1">No domiciliado</option>
                                        <option value="2">DNI</option>
                                        <option value="3">Carnet Extranjería</option>
                                        <option value="4">RUC</option>
                                        <option value="5">Pasaporte</option>
                                        <option value="6">Cédula Diplomática</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">N° Documento</span>
                                    <input name="numero" type="text" class="form-control form-control-sm" id="numero"
                                        value="" data-mini="true" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Nombre</span>
                                    <input name="nombre" type="text" class="form-control form-control-sm" id="nombre"
                                        value="" data-mini="true">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-8 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Dirección</span>
                                    <input name="direccion" type="text" class="form-control form-control-sm"
                                        id="direccion" value="" data-mini="true">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Correo Electrónico</span>
                                    <input name="correo" type="text" class="form-control form-control-sm" id="correo"
                                        value="" data-mini="true">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Observaciones</span>
                                    <textarea name="observacion" cols="120" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2" id="detalle_servicios">
                            <div class="mx-auto">
                                <table class="table table-responsive table-bordered align-middle"
                                    style="margin-bottom: 0 !important; font-size: small;" id="detalle_servicios_tabla">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="text-center">Código</th>
                                            <th class="text-center">Cantidad</th>
                                            <th>Descripción</th>
                                            <th class="text-center">Cantidad agregar</th>
                                            <th class="text-center">Precio Unitario</th>
                                            <th class="text-center">Valor de venta</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-btn-si">Guardar</button>
                    <button type="button" class="btn btn-default modal-btn-no">Cerrar</button>
                </div>
            </div>
            <!-- </form> -->
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true"
        id="ver_reproduccion_asistida">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Verificar pago de recibo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">N° recibo</span>
                                <input name="numerorecibo" type="text" class="form-control form-control-sm"
                                    id="reproduccionasistida_numerorecibo" disabled>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Fecha</span>
                                <input name="fecha" type="text" class="form-control form-control-sm text-center"
                                    id="reproduccionasistida_fecha" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-8 col-lg-8 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Paciente</span>
                                <input name="paciente" type="text" class="form-control form-control-sm"
                                    id="reproduccionasistida_paciente" disabled>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Médico</span>
                                <input name="medico" type="text" class="form-control form-control-sm text-center"
                                    id="reproduccionasistida_medico" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mx-auto">
                    <table class="table table-sm table-responsive table-bordered align-middle"
                        id="reproduccionasistida_serviciospagados">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">Código</th>
                                <th>Nombre</th>
                                <th class="text-center">Precio</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="mx-auto">
                    <table class="table table-sm table-responsive table-bordered align-middle"
                        id="reproduccionasistida_detalle">
                        <thead class="thead-dark">
                            <tr>
                                <th colspan="11" class="text-center">Reproducciones Realizadas</th>
                            </tr>
                            <tr>
                                <th>Fecha</th>
                                <th>Procedimiento</th>
                                <th>Extras</th>
                                <th>Transferidos</th>
                                <th>Crio</th>
                                <th>Biopsia</th>
                                <th class="text-center">Detalle</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-btn-no">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .mx-auto {
            max-width: 100%;
            overflow-x: auto;
        }

        #ver_estado_documento_detalle {
            width: 100%;
            border-collapse: collapse;
        }

        #ver_estado_documento_detalle th, #ver_estado_documento_detalle td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            word-wrap: break-word;
            white-space: nowrap;
            overflow: hidden;
        }

        #ver_estado_documento_detalle td {
            max-width: 300px;
            overflow-x: auto;
        }

        #ver_estado_documento_detalle td:nth-child(6),
        #ver_estado_documento_detalle td:nth-child(7) {
            max-width: 400px;
            white-space: nowrap;
            overflow-x: auto;
        }

        #ver_estado_documento {
            width: 100%;
            max-width: none;
        }
    </style>

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true"
        id="ver_estado_documento">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Estado Documento Facturación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="mx-auto">
                    <table class="table table-responsive table-bordered align-middle" id="ver_estado_documento_detalle">
                    </table>
                </div>
                <div class="modal-footer">
                    <?php
                    if ($user['role'] == 3 || $user['role'] == 10 || $user['role'] == 19 || $user['role'] == 20) {
                        print('<button type="button" class="btn btn-default modal-btn-si">Verificar</button>');
                    } ?>
                    <button type="button" class="btn btn-default modal-btn-no">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/lista_facturacion.js?v=5" crossorigin="anonymous"></script>
    <script src="js/datatables.net/jquery.dataTables.min.js"></script>
    <script src="js/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="js/datatables.net/dataTables.buttons.min.js"></script>
    <script src="js/datatables.net/jszip.min.js"></script>
    <script src="js/datatables.net/buttons.html5.min.js"></script>
    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/js/facturacion_empresa.php"); ?>

    <script>
    jQuery(window).load(function(event) {
        jQuery('.loader').fadeOut(1000);
        multiSedeEmpresa($("#empresa").val());
    });

    $('.Datos').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "sProcessing": "Procesando...",
        },
        responsive: "true",
        dom: 'Bfrtilp',
        buttons: [{
            extend: 'excelHtml5',
            text: '<img src="_images/excel.png" height="18" width="18" alt="icon name"> ',
            titleAttr: 'Clic para Exportar a Excel',
            className: 'btn-excel'
        }]
    });

    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=600');
        mywindow.document.write('<html><head><title>' + document.title + '</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write('<h1>' + document.title + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close();
        mywindow.focus();

        mywindow.print();
        mywindow.close();

        return true;
    }

    $("#empresa").change(function() {
        multiSedeEmpresa($(this).val());
    });

    function multiSedeEmpresa(idEmpresa){
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

                    $.each(data, function (index, sede) {
                        if(id_sede == sede.id){
                            select.append('<option value="' + sede.id + '"selected>' + sede.nombre + '</option>');
                        }else{
                            select.append('<option value="' + sede.id + '">' + sede.nombre + '</option>');
                        }
                    });
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