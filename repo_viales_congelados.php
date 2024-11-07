<?php
session_start();
ini_set("display_errors","1");
error_reporting(E_ALL);
?>
<!DOCTYPE HTML>
<html>
<head>
    <?php
        $login = $_SESSION['login'];
        $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
        if ($_SESSION['role'] <> 2) {
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
        }
        require("_database/db_tools.php");
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <a class="breadcrumb" href="lista_and.php">
                <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
            </a>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="lista_and.php">Andrología</a></li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Viales Congelados</li>
            </ol>
        </nav>
            <?php
                $between = $ini = $fin = "";

                $ini = date('Y-m-01');
                $fin = date('Y-m-t');
                $between .= " and a.fec between '$ini' and '$fin'";

                $consulta = $db->prepare("SELECT id, nom from lab_user where sta=0");
                $consulta->execute();
                $consulta->setFetchMode(PDO::FETCH_ASSOC);
                $datos = $consulta->fetchAll();

                $item = 0;

                $consulta = $db->prepare("SELECT
                    b.n_tan tanque, a.c canister, a.v varilla, a.p posicion, tip_id fecha, c.p_tip tipo_documento, c.p_dni numero_documento
                    , concat(upper(c.p_ape), ' ', upper(c.p_nom)) nombres
                    from lab_tanque_res a
                    inner join lab_tanque b on b.tan = a.t
                    left join hc_pareja c on c.p_dni = a.sta
                    order by b.n_tan, a.c, a.v, a.p");
                $consulta->execute(); ?>

            <div class="card mb-3" id="resultados">
                <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                    <?php
                    print('
                        <small><b>Lista de Viales Congelados: </b>'.date("Y-m-d H:i:s").'
                        <b>, Total Registros: </b>'.$consulta->rowCount().'
                        <b>, Descargar: </b><a href="#" onclick="tableToExcel(\'lista_viales\', \'viales\')" class="ui-btn ui-mini ui-btn-inline"><img src="_images/excel.png" height="18" width="18" alt="icon name"></a></small>'); ?>
                    <a href="#" id="toggle_fullscreen" class="float-right"><img src="_libraries/open-iconic/svg/fullscreen-enter.svg" height="18" width="18" alt="icon name"></a>
                </h5>

                <input type="text" class="form-control" id="datos_paciente" onkeyup="datos_paciente()" placeholder="Buscar..." title="Buscar datos en la tabla">
                <?php
                print('
                    <table width="100%" class="table table-responsive table-bordered align-middle" style="margin-bottom: 0 !important;" id="lista_viales">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">Item</th>
                                <th class="text-center">Tanque</th>
                                <th class="text-center">Canister</th>
                                <th class="text-center">Varilla</th>
                                <th class="text-center">Posicion</th>
                                <th class="text-center">Tipo Documento</th>
                                <th class="text-center">Número Documento</th>
                                <th class="text-center">Nombres</th>
                                <th class="text-center">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>');
                $item = 1;

                while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
                    $var = "";
                    print("
                    <tr>
                        <td class='text-center'>".$item++."</td>
                        <td class='text-center'>".$data['tanque']."</td>
                        <td class='text-center'>".$data['canister']."</td>
                        <td class='text-center'>".$data['varilla']."</td>
                        <td class='text-center'>".$data['posicion']."</td>
                        <td class='text-center'>".$data['tipo_documento']."</td>
                        <td class='text-center'>".$data['numero_documento']."</td>
                        <td class='text-center'>".$data['nombres']."</td>
                        <td class='text-center'>".$data['fecha']."</td>");
                    print('</tr>');
                }

                print('
                </tbody>
                    </table>'); ?>
            </div>
        </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        var tableToExcel = (function () {
            var uri = 'data:application/vnd.ms-excel;base64,'
                ,
                template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                , base64 = function (s) {
                    return window.btoa(unescape(encodeURIComponent(s)))
                }
                , format = function (s, c) {
                    return s.replace(/{(\w+)}/g, function (m, p) {
                        return c[p];
                    })
                }
            return function (table, visita) {
                if (!table.nodeType) table = document.getElementById(table)
                var ctx = {worksheet: 'reporte_' + visita || 'reporte', table: table.innerHTML}
                window.location.href = uri + base64(format(template, ctx))
            }
        })();

        $(document).ready(function () {
            $("#guardar").on("click", function () {
                $("#modal_editar").modal('show')
            });

            $('#toggle_fullscreen').on('click', function(e){
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
                    $("#toggle_fullscreen img").attr("src", "_libraries/open-iconic/svg/fullscreen-enter.svg");
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
                    $("#toggle_fullscreen img").attr("src", "_libraries/open-iconic/svg/fullscreen-exit.svg");
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

        });

        function datos_paciente() {
          var input, filter, table, tr, td, i;
          input = document.getElementById("datos_paciente");
          filter = input.value.toUpperCase();
          table = document.getElementById("lista_viales");
          tr = table.getElementsByTagName("tr");

          for (i = 1; i < tr.length; i++) {
            encontro = false;
            for (j = 0; j < 10; j++) {
                td = tr[i].getElementsByTagName("td")[j];
                if (td) {
                    if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                        encontro = true; break;
                    } else {
                        encontro = false;
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
</body>
</html>