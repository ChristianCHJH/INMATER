<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php';
   require("_database/database_log.php");
    require("_database/database.php");
    ?>
      
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="jstickytableheaders.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".table-stripe").stickyTableHeaders();
        });

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
    </script>
</head>
<body>
    <?php require ('_includes/menu_sistemas.php'); ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_sistemas.php">Inicio</a></li>
                <li class="breadcrumb-item">Correcciones</li>
                <li class="breadcrumb-item active" aria-current="page">Pacientes</li>
            </ol>
        </nav>
        <?php
            $between = $tipo_observacion = "";

            if (isset($_POST) && !empty($_POST)) {
                if (isset($_POST["tipo_observacion"]) && !empty($_POST["tipo_observacion"])) {
                    $tipo_observacion = $_POST['tipo_observacion'];
                    $between .= " and a.codigo_correccion = $tipo_observacion";
                }
            
                if (isset($_POST["medico"]) && !empty($_POST["medico"])) {
                    $medico = $_POST['medico'];
                    $between .= " and unaccent(a.medico) ILIKE ('%$medico%')";
                }
            }            

            $consulta = $db->prepare("SELECT * from
            (
                SELECT
                p.tip tipo_documento, p.dni numero_documento, p.ape apellidos, p.nom nombres, p.fnac fecha_nacimiento, p.med medico, c.id codigo_correccion, c.nombre correccion
                from hc_paciente p
                inner join man_correcciones c on c.id = 1
                where length(p.dni) <> 8 and p.tip = 'dni' and p.dni <> 'xxxxx'
                union
                select
                p.tip tipo_documento, p.dni numero_documento, p.ape apellidos, p.nom nombres, p.fnac fecha_nacimiento, p.med medico, c.id codigo_correccion, c.nombre correccion
                from hc_paciente p
                inner join hc_pareja p1 on p1.p_tip = p.tip and p1.p_dni = p.dni
                inner join man_correcciones c on c.id = 2
                group by p.tip, p.dni, p.ape, p.nom, p.med
                union
                select
                p.tip tipo_documento, p.dni numero_documento, p.ape apellidos, p.nom nombres, p.fnac fecha_nacimiento, p.med medico, c.id codigo_correccion, c.nombre correccion
                from hc_paciente p
                inner join man_correcciones c on c.id = 3
                where datediff(now(), p.fnac) < 0
                union
                select
                p.tip tipo_documento, p.dni numero_documento, p.ape apellidos, p.nom nombres, p.fnac fecha_nacimiento, p.med medico, c.id codigo_correccion, c.nombre correccion
                from hc_paciente p
                inner join man_correcciones c on c.id = 4
                where year(now()) - year(p.fnac) - (date_format(now(), '%m%d') < date_format(p.fnac, '%m%d')) < 18
                union
                select
                p.tip tipo_documento, p.dni numero_documento, p.ape apellidos, p.nom nombres, p.fnac fecha_nacimiento, p.med medico, c.id codigo_correccion, c.nombre correccion
                from hc_paciente p
                inner join man_correcciones c on c.id = 5
                where p.fnac is null
            ) as a
            where 1=1".$between);
            $consulta->execute();
            $rows = $consulta->fetchAll(); ?>
        <div class="card mb-3">
            <h5 class="card-header">Filtros</h5>
            <div class="card-body">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Tipo Observación</label>
                            <select name='tipo_observacion' class="form-control form-control-sm">
                                <option value="">TODOS</option>
                                <?php
                                    $rEmb = $db->prepare("SELECT id, nombre FROM man_correcciones WHERE estado=1");
                                    $rEmb->execute();
                                    $rEmb->setFetchMode(PDO::FETCH_ASSOC);
                                    $datos1 = $rEmb->fetchAll();
                                    foreach ($datos1 as $row) {
                                        if ($tipo_observacion == $row['id']) {$selected="selected";}
                                        else {$selected="";}
                                        print("<option value='".$row['id']."' $selected>".mb_strtoupper($row['nombre'])."</option>");
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Médico</label>
                            <select name='medico' class="form-control form-control-sm">
                                <option value="">TODOS</option>
                                <?php
                                    $rEmb = $db->prepare("SELECT codigo, nombre FROM man_medico WHERE estado=1");
                                    $rEmb->execute();
                                    $rEmb->setFetchMode(PDO::FETCH_ASSOC);
                                    $datos1 = $rEmb->fetchAll();
                                    foreach ($datos1 as $row) {
                                        if ($medico == $row['codigo']) {$selected="selected";}
                                        else {$selected="";}
                                        print("<option value='".$row['codigo']."' $selected>".mb_strtoupper($row['nombre'])."</option>");
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div>
            <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                <?php
                print('
                    <small><b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'
                    <b>, Total Registros: </b>'.count($rows).'
                    <b>, Descargar: </b>
                    <a href="#" onclick="tableToExcel(\'repo_pacientes\', \'pacientes\')" class="ui-btn ui-mini ui-btn-inline">
                        <img src="_images/excel.png" height="18" width="18" alt="icon name">
                    </a></small>'); ?>
            </h5>
            <div class="card-body mx-auto">
                <form action="" method="post" data-ajax="false" name="form2">
                    <table width="100%" class='table table-responsive table-bordered align-middle table-stripe' data-filter="true" data-input="#filtro" id="repo_pacientes">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%" class="text-center">Item</th>
                                <th width="15%" class="text-center">Tipo Documento</th>
                                <th width="15%" class="text-center">N° Documento</th>
                                <th width="25%">Apellidos y Nombres</th>
                                <th width="10%" class="text-center">F. Nacimiento</th>
                                <th width="10%" class="text-center">Médico</th>
                                <th width="20%" class="text-center">Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $i=1;
                            foreach ($rows as $item)
                            {
                                print('
                                <tr>
                                    <td width="5%" class="text-center">'.$i++.'</td>
                                    <td width="15%" class="text-center">'.$item["tipo_documento"].'</td>
                                    <td width="15%" class="text-center">\''.$item["numero_documento"].'</td>
                                    <td width="25%">'.mb_strtoupper($item["apellidos"]).' '.mb_strtoupper($item["nombres"]).'</td>
                                    <td width="10%" class="text-center">'.$item["fecha_nacimiento"].'</td>
                                    <td width="10%" class="text-center">'.$item["medico"].'</td>
                                    <td width="20%" class="text-center">'.mb_strtoupper($item["correccion"]).'</td>
                                </tr>');
                            }
                        ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</body>
</html>