<?php session_start(); ?>
<!DOCTYPE HTML>
<html>

<head>
    <?php
        $login = $_SESSION['login'];
        $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
        if (!$login) {
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
        }
        require($_SERVER["DOCUMENT_ROOT"]."/_database/database.php");
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
        integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/shared.css?v=1">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <title>Clínica Inmater | Mantenimiento de Servicios</title>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
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
    })();
    </script>
</head>

<body>
    <div class="box container">
        <?php require ('_includes/menu_facturacion.php'); ?>
        <?php
            $stmt = $db->prepare("SELECT * FROM usuario WHERE userx=?");
            $stmt->execute(array($login));
            $data1 = $stmt->fetch(PDO::FETCH_ASSOC);

            // baja de servicios
            if (isset($_POST['conf']) && !empty($_POST['conf'])) {
                $stmt = $db->prepare("UPDATE recibo_serv set estado=0 where id=?;");
                $stmt->execute(array($_POST['conf']));
            }

            // agrega servicios
            if (isset($_POST['agregar']) && !empty($_POST['agregar'])) {
                $codigo="";
                if (isset($_POST["codigo"]) && !empty($_POST["codigo"])) {
                    $codigo=$_POST["codigo"];
                }
                $paquete="";
                if (isset($_POST["paquete"]) && !empty($_POST["paquete"])) {
                    $paquete=$_POST["paquete"];
                }
                $tiposervicio=$_POST["tiposervicio"];

                global $db;
                $stmt = $db->prepare("INSERT INTO recibo_serv (procedimiento_id, tarifario_id, conta_sub_centro_costo_id, nom, cod, idmoneda, costo, pak, tip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    intval($_POST["procedimiento_id"]),
                    intval($_POST["tarifario_id"]),
                    $_POST["subcentrocosto"],
                    mb_strtoupper(trim($_POST["nombreservicio"])),
                    $codigo,
                    $_POST["moneda"],
                    $_POST["costo"],
                    $paquete,
                    $_POST["tiposervicio"]
                ]);
                print("<div id='alerta'>Servicio guardado!</div>");
            }

            $tiposervicio="";
            if (isset($_GET["tiposervicio"]) && !empty($_GET["tiposervicio"])) {
                $tiposervicio = $_GET['tiposervicio'];
                $Rpop = $db->prepare("SELECT
                r.*, m.id idmoneda, m.codigo moneda
                , s.nombre sede, s.codigo codigo_sede
                , cco.codigo centrocosto_codigo, cco.descripcion centrocosto
                , sco.codigo subcentrocosto_codigo, sco.descripcion subcentrocosto
                , sp.nombre procedimiento, upper(tp.nombre) tarifario
                from recibo_serv r
                left join servicios_procedimiento sp on sp.id = r.procedimiento_id
                left join tarifario tp on tp.id = r.tarifario_id
                inner join moneda m on m.id = r.idmoneda
                inner join conta_sub_centro_costo sco on sco.id = r.conta_sub_centro_costo_id
                inner join conta_centro_costo cco on cco.id = sco.conta_centro_costo_id
                inner join sedes_contabilidad s on s.id = cco.sede_id
                where r.tip = ? and r.estado = 1
                order by s.codigo, tp.nombre, sp.nombre, r.nom;");
                $Rpop->execute([$tiposervicio]);
                $cuentacontablevalue=$centrocostovalue="";
                switch ($tiposervicio) {
                    case '1':
                        $cuentacontablevalue="704102";
                        $centrocostovalue="002";
                    break;
                    case '2':
                        $cuentacontablevalue="704106";
                        $centrocostovalue="007";
                    break;
                    case '6':
                        $cuentacontablevalue="704103";
                        $centrocostovalue="003";
                    break;
                    default: break;
                }
            } else {
                $cuentacontablevalue=$centrocostovalue="";
                $Rpop = $db->prepare("SELECT
                r.*, m.id idmoneda, m.codigo moneda
                from recibo_serv r
                left join moneda m on m.id=r.idmoneda
                where r.estado=1
                order by r.pak asc");
                $Rpop->execute();
            }

        ?>

        <script>
        function anular(id) {
            if (confirm("CONFIRMA LA ANULACION DEL RECIBO: " + id + " ?")) {
                document.form2.conf.value = id;
                document.form2.submit();
                return true;
            } else return false;
        }
        </script>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_facturacion.php">Inicio</a></li>
                <li class="breadcrumb-item" aria-current="page">Mantenimiento</li>
                <li class="breadcrumb-item active" aria-current="page">Servicios</li>
            </ol>
        </nav>

        <form action="" method="post" data-ajax="false" name="form2">
            <div class="card mb-3">
                <input type="hidden" name="conf">
                <h5 class="card-header"><small><b>Nuevo</b></small></h5>
                <div class="card-body">
                    <div class="row pb-2">
                        <!-- tipo de servicio -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tipo de Servicio*</span>
                                <select name='tiposervicio' id="idservicio"
                                    class="form-control form-control-sm chosen-select"
                                    onchange="this.form.action = 'man_ser.php?tiposervicio='+this.value; this.form.submit();"
                                    required>
                                    <option value="">Todos</option>
                                    <!-- <option value='0' <?php // if($tiposervicio == "0") print("selected"); ?> >Admin</option> -->
                                    <option value='1' <?php if($tiposervicio == "1") print("selected"); ?>>Reproducción
                                        Asistida</option>
                                    <option value='2' <?php if($tiposervicio == "2") print("selected"); ?>>Andrología
                                    </option>
                                    <option value='3' <?php if($tiposervicio == "3") print("selected"); ?>>
                                        Procedimientos de Sala</option>
                                    <option value='4' <?php if($tiposervicio == "4") print("selected"); ?>>Análisis de
                                        Sangre</option>
                                    <option value='5' <?php if($tiposervicio == "5") print("selected"); ?>>Perfiles
                                    </option>
                                    <option value='6' <?php if($tiposervicio == "6") print("selected"); ?>>Ecografía
                                    </option>
                                    <option value='7' <?php if($tiposervicio == "7") print("selected"); ?>>Adicionales
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!-- nombre de servicio -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Nombre de Servicio*</span>
                                <input class="form-control form-control-sm" type="text" name="nombreservicio"
                                    id="nombreservicio" required />
                            </div>
                        </div>
                        <!-- moneda y costo -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Costo*</span>
                                <select class="form-control form-control-sm chosen-select" name="moneda" id="idmoneda"
                                    required>
                                    <option value="" selected>SELECCIONAR</option>
                                    <option value="1">S/.</option>
                                    <option value="2">US$</option>
                                </select>
                                <input class="form-control-sm" type="number" step="any" min="0" name="costo"
                                    id="idcosto" data-mini="true" required />
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <!-- sub centro de costo -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Subcentro de Costo*</span>
                                <select class="form-control form-control-sm chosen-select" name="subcentrocosto"
                                    id="subcentrocosto" required>
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    $stmt = $db->prepare("SELECT id, codigo, descripcion nombre from conta_sub_centro_costo where estado = 1;");
                                    $stmt->execute();

                                    while ($subcentrocosto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                      $selected = "";
                                      if ($subcentrocosto['id'] == $data['conta_sub_centro_costo_id']) $selected = "selected";
                                      print('<option value="' . $subcentrocosto['id'] . '" '.$selected.'>'.$subcentrocosto['nombre'].' ('.$subcentrocosto['codigo'].')</option>');
                                    }
                                  ?>
                                </select>
                            </div>
                        </div>
                        <!-- tarifario -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tarifario</span>
                                <select class="form-control form-control-sm chosen-select" name="tarifario_id">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    $stmt = $db->prepare("SELECT id, upper(nombre) nombre from tarifario where eliminado = 0 order by nombre;");
                                    $stmt->execute();

                                    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        print('<option value="' . $item['id'] . '">'.$item['nombre'].'</option>');
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- procedimiento -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Procedimiento</span>
                                <select class="form-control form-control-sm chosen-select" name="procedimiento_id">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    $stmt = $db->prepare("SELECT * from servicios_procedimiento sp where estado = 1 order by nombre;");
                                    $stmt->execute();

                                    while ($procedimiento = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        print('<option value="' . $procedimiento['id'] . '">'.$procedimiento['nombre'].'</option>');
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <!-- paquete -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Paquete</span>
                                <input class="form-control form-control-sm" name="paquete" id="idpaquete"
                                    autocomplete="off">
                                <select class="form-control form-control-sm chosen-select" id="paqueteselect">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                        $Rpak = $db->prepare("SELECT distinct pak from recibo_serv where pak is not null order by pak asc");
                                        $Rpak->execute();
                                        while ($pak = $Rpak->fetch(PDO::FETCH_ASSOC)) {
                                            print('<option value="' . $pak['pak'] . '">'.$pak['pak'].'</option>');
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- codigo -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Código Anglolab</span>
                                <input class="form-control form-control-sm" type="text" name="codigo" id="idcodigo" />
                            </div>
                        </div>
                        <?php
                        if($data1["role"] == "3" or $data1["role"] == "10") { ?>
                        <!-- agregar -->
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                            <input class="form-control btn btn-danger btn-sm" type="Submit" name="agregar"
                                value="Agregar" />
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </form>

        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()"
            placeholder="Buscar texto en la tabla..." title="escribe un texto para empezar a buscar">

        <div class="card row content">
            <div class="col">
                <span>
                    <table class="table table-sm table-hover table-bordered" id="tb_servicios">
                        <thead class="thead-dark row-sticky">
                            <th class='text-center'>Id</th>
                            <th>Sede</th>
                            <th>Tarifario</th>
                            <th>Tipo de Procedimiento</th>
                            <th>Subcentro de Costo</th>
                            <th>Nombre</th>
                            <th class="text-center">Código anglolab</th>
                            <th>Moneda</th>
                            <th class="text-center">Precio</th>
                            <th>Paquete</th>
                            <?php
                            if($data1["role"] == "3" or $data1["role"] == "10") { ?>
                            <th>Acción</th>
                            <?php } ?>
                        </thead>
                        <tbody>
                            <?php
                                while ($pop = $Rpop->fetch(PDO::FETCH_ASSOC)) {
                                    if ($pop['tip'] == 1 or $pop['tip'] == 2 or $pop['tip'] == 3) {
                                        $mon = "US";
                                    } else {
                                        $mon = "MN";
                                    }

                                    print("<tr>
                                        <td class='text-center'>".$pop['id']."</td>
                                        <td class='ellipsis-content'>(".$pop["codigo_sede"].") ".mb_strtoupper($pop['sede'])."</td>
                                        <td class='ellipsis-content'>".$pop['tarifario']."</td>
                                        <td class='ellipsis-content'>".$pop['procedimiento']."</td>
                                        <td class='ellipsis-content'>(".$pop['subcentrocosto_codigo'].") ".$pop['subcentrocosto']."</td>
                                        <td class='ellipsis-content'>".$pop['nom']."</td>
                                        <td class='text-center'>".$pop['cod']."</td>
                                        <td class='text-center'>".$mon."</td>
                                        <td class='text-right'>".number_format($pop['costo'], 2)."</td>
                                        <td class='ellipsis-content'>".$pop['pak']."</td>");

                                    if($data1["role"] == "3" or $data1["role"] == "10") {
                                        print("<td class='text-center'>
                                            <a href='man_ser_edit.php?id=".$pop['id']."'><i class='far fa-edit'></i></a>
                                            <a href='javascript:anular(".$pop['id'].");'><i class='fas fa-trash-alt'></i></a>
                                        </td>");
                                    }

                                    print("</tr>");
                                }
                            if ($Rpop->rowCount() < 1)
                                echo '<p><h3 class="text_buscar">¡ No hay Servicios !</h3></p>';
                            ?>
                        </tbody>
                    </table>
                </span>
            </div>
        </div>

        <div class="row footer">@2022 Clínica Inmater</div>
    </div>
    <script>
    $(".chosen-select").chosen();
    $("#nombreservicioselect").change(function() {
        var e = document.getElementById("nombreservicioselect");
        var strUser = e.options[e.selectedIndex].value;
        var demo = e.options[e.selectedIndex].text;
        $('#nombreservicio').val(strUser);
        var fields = demo.split("-");
        $('#idcosto').val(fields[fields.length - 1]);
    });
    $("#paqueteselect").change(function() {
        var obj = document.getElementById("paqueteselect");
        // var strUser = e.options[e.selectedIndex].value;
        var texto = obj.options[obj.selectedIndex].text;
        $('#idpaquete').val(texto);
        // var fields = demo.split("-");
        // $('#idcosto').val(fields[fields.length-1]);
    });

    function myFunction() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("tb_servicios");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            var encontro = false;
            for (var j = 0; j < 10; j++) {
                td = tr[i].getElementsByTagName("td")[j];
                if (td) {
                    if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                        encontro = true;
                        break;
                    }
                }
            }

            if (encontro) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
    </script>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>