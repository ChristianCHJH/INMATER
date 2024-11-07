<!DOCTYPE HTML>
<html>
<head>
<?php
     include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-1.11.1.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="jstickytableheaders.js" crossorigin="anonymous"></script>
</head>
<body>
    <script>
        /* $(document).ready(function () {
            $(".table-stripe").stickyTableHeaders();
        }); */
    </script>
    <?php
        require ('_includes/menu_facturacion.php');

        // iniciar variables
        $between = $paciente_apellidos = $paciente_nombres = $paciente_documento = $pareja_apellidos = $pareja_nombres = $pareja_documento = $medico = "";
        //
        if (isset($_POST) && !empty($_POST)) {
            if (isset($_POST["paciente_apellidos"]) && $_POST["paciente_apellidos"] != "") {
                $paciente_apellidos = $_POST["paciente_apellidos"];
                $between .= "and pac.ape ilike ('%$paciente_apellidos%')";
            }

            if (isset($_POST["paciente_nombres"]) && $_POST["paciente_nombres"] != "") {
                $paciente_nombres = $_POST["paciente_nombres"];
                $between .= "and pac.nom ilike ('%$paciente_nombres%')";
            }

            if (isset($_POST["paciente_documento"]) && $_POST["paciente_documento"] != "") {
                $paciente_documento = $_POST["paciente_documento"];
                $between .= "and pac.dni ilike ('%$paciente_documento%')";
            }

            if (isset($_POST["pareja_apellidos"]) && $_POST["pareja_apellidos"] != "") {
                $pareja_apellidos = $_POST["pareja_apellidos"];
                $between .= "and par.p_ape ilike ('%$pareja_apellidos%')";
            }

            if (isset($_POST["pareja_nombres"]) && $_POST["pareja_nombres"] != "") {
                $pareja_nombres = $_POST["pareja_nombres"];
                $between .= "and par.p_nom ilike ('%$pareja_nombres%')";
            }

            if (isset($_POST["pareja_documento"]) && $_POST["pareja_documento"] != "") {
                $pareja_documento = $_POST["pareja_documento"];
                $between .= "and par.p_dni ilike ('%$pareja_documento%')";
            }

            if (isset($_POST["medico"]) && $_POST["medico"] != "") {
                $medico = $_POST["medico"];
                $between .= "and pac.med ilike ('%$medico%')";
            }

        } else {
            $between .= " and 1<>1";
        }

        $consulta = $db->prepare("
        select
        pac.med medico,
        b.codigo paciente_tipo_documento_identidad, pac.dni paciente_documento, pac.ape paciente_apellidos, pac.nom paciente_nombres, pac.mai paciente_correo, pac.tcel paciente_celular, pac.fnac paciente_fnacimiento, round(EXTRACT(YEAR FROM age(now(), pac.fnac))) paciente_edad,
        c.codigo pareja_tipo_documento_identidad, par.p_dni pareja_documento, par.p_ape pareja_apellidos, par.p_nom pareja_nombres, par.p_mai pareja_correo, par.p_tcel pareja_celular, par.p_fnac pareja_fnacimiento, round(EXTRACT(YEAR FROM age(now(), par.p_fnac))) pareja_edad
        from hc_paciente pac
        left join hc_pare_paci a on a.dni = pac.dni
        left join hc_pareja par on par.p_dni = a.p_dni
        left join man_tipo_documento_identidad b on b.codigo = pac.tip
        left join man_tipo_documento_identidad c on c.codigo = par.p_tip
        where 1=1 $between");
        $consulta->execute();
    ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Inicio</li>
                <li class="breadcrumb-item">Facturación</li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Pacientes</li>
            </ol>
        </nav>
        <div data-role="header">
            <div class="card mb-3">
                <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Filtro</h5>
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="card-body collapse show" id="collapseExample">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Apellidos Paciente</span>
                                    <input class="form-control" name="paciente_apellidos" value="<?php print($paciente_apellidos); ?>" type="text">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Nombres Paciente</span>
                                    <input class="form-control" name="paciente_nombres" value="<?php print($paciente_nombres); ?>" type="text">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-addon">N° documento Paciente</span>
                                    <input class="form-control" name="paciente_documento" value="<?php print($paciente_documento); ?>" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Apellidos Pareja</span>
                                    <input class="form-control" name="pareja_apellidos" value="<?php print($pareja_apellidos); ?>" type="text">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Nombres Pareja</span>
                                    <input class="form-control" name="pareja_nombres" value="<?php print($pareja_nombres); ?>" type="text">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-addon">N° documento Pareja</span>
                                    <input class="form-control" name="pareja_documento" value="<?php print($pareja_documento); ?>" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Médico</span>
                                    <input class="form-control" name="medico" value="<?php print($medico); ?>" type="text">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 col-lg-8 text-right">
                                <input type="Submit" class="btn btn-danger" id="buscar" name="buscar" value="Buscar"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card mb-3">
                <?php print('<h5 class="card-header">Lista: <small>'.$consulta->rowcount().' registros encontrados</small></h5>'); ?>
                <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Nombres Paciente.." title="escribe nombre de paciente">
                <div class="card-body collapse show" id="collapseExample">
                    <table class="table table-responsive table-bordered align-middle table-stripe" style="height: 50vh;" id="myTable">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">Item</th>
                                <th rowspan="2">Médico</th>
                                <th colspan="7" class="text-center">Paciente</th>
                                <th colspan="7" class="text-center">Pareja</th>
                            </tr>
                            <tr>
                                <th class="text-center">Tipo Documento</th>
                                <th class="text-center">N° Documento</th>
                                <th class="text-center">Nombres</th>
                                <th class="text-center">F. Nacimiento</th>
                                <th class="text-center">Correo</th>
                                <th class="text-center">Celular</th>
                                <th class="text-center">Tipo Documento</th>
                                <th class="text-center">N° Documento</th>
                                <th class="text-center">Nombres</th>
                                <th class="text-center">F. Nacimiento</th>
                                <th class="text-center">Correo</th>
                                <th class="text-center">Celular</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $i=1;
                            while ($item = $consulta->fetch(PDO::FETCH_ASSOC))
                            {
                                print('
                                <tr>
                                    <td align="center">'.$i++.'</td>
                                    <td>'.$item["medico"].'</td>
                                    <td>'.$item["paciente_tipo_documento_identidad"].'</td>
                                    <td>'.$item["paciente_documento"].'</td>
                                    <td>'.mb_strtoupper($item["paciente_apellidos"]).' '.mb_strtoupper($item["paciente_nombres"]).'</td>
                                    <td>'.$item["paciente_fnacimiento"].' ('.$item["paciente_edad"].' años)</td>
                                    <td>'.$item["paciente_correo"].'</td>
                                    <td>'.$item["paciente_celular"].'</td>
                                    <td>'.$item["pareja_tipo_documento_identidad"].'</td>
                                    <td>'.$item["pareja_documento"].'</td>
                                    <td>'.mb_strtoupper($item["pareja_apellidos"]).' '.mb_strtoupper($item["pareja_nombres"]).'</td>
                                    <td>'.$item["pareja_fnacimiento"].' ('.$item["pareja_edad"].' años)</td>
                                    <td>'.$item["pareja_correo"].'</td>
                                    <td>'.$item["pareja_celular"].'</td>
                                </tr>');
                            }
                        ?>
                        </tbody>
                    </table>
                    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" id="modal_eliminar">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Confirmar Eliminar</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">¿Realmente desea eliminar el registro?</div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" id="modal-btn-no">Cancelar</button>
                                    <button type="button" class="btn btn-dark" id="modal-btn-si">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function myFunction() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[4];

            if (td) {
            if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
            }       
        }
        }
    </script>
</body>
</html>