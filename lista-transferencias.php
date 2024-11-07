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
    <link rel="stylesheet" type="text/css" href="css/global.css">

	<script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="js/jquery-1.11.1.min.js"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        .box {
            display: flex;
            flex-flow: column;
            height: 100%;
        }
        .box .row.header {
            flex: 0 1 auto;
        }
        .box .row.content {
            flex: 1 1 auto;
            overflow-y: scroll;
            margin: 0 0 15px 0;
        }
        .box .row.footer {
            flex: 0 1 40px;
            background-color: #72a2aa;
            padding: 0 1.25rem;
            margin: 0 0 15px 0;
            border-radius: .25rem;
        }

        @media (max-width: 768px) {
            .row.footer {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="loader">
        <img src="_images/load.gif" alt="">
    </div>
    <?php
    // filtros
    $between = "";
    $paciente = "";

    if (isset($_POST) && !empty($_POST)) {
        if (isset($_POST["paciente"]) && !empty($_POST["paciente"])) {
            $paciente = $_POST["paciente"];
            $between .= "(unaccent(hp.ape) ilike ('%$paciente%') or unaccent(hp.nom) ilike ('%$paciente%') or hp.dni ilike ('%$paciente%'))";
        }
    } else {
        $between .= "hp.dni = '0'";
    }

    $stmt = $db->prepare("SELECT hp.tip documento_tipo, hp.dni documento_numero, concat(trim(upper(hp.ape)), ' ', trim(upper(hp.nom))) paciente, hp.fnac fecha_nacimiento
        from hc_paciente hp
        where 1=1 and $between");
        $stmt->execute(); ?>

    <div class="box container">
        <div class="row1 header1">
            <nav aria-label="breadcrumb">
                <a class="breadcrumb" href="labo-betas-resumen.php" style="background-color: #72a2aa;">
                    <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
                </a>
            </nav>

            <form action="" method="post" name="form2">
                <div class="card mb-3">
                    <h5 class="card-header"><small><b>Filtros</b></small></h5>
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Paciente</span>
                                    <input class="form-control" name="paciente" type="text" value="<?php echo $paciente; ?>">
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-2 col-lg-2">
                                <input type="Submit" class="form-control btn btn-sm btn-danger" value="Mostrar"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card row content">
            <div class="card-body">
                <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Buscar datos del paciente" title="escribe los nombre o apellidos de la paciente">
                <table class="table table-bordered" id="myTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">Item</th>
                            <th class="text-center align-middle">Documento</th>
                            <th class="align-middle">Paciente</th>
                            <th class="text-center align-middle">Fecha Nacimiento</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $contador = 1;
                        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            print('<tr>
                                <td class="text-center">' . $contador++ .'</td>
                                <td class="text-center">'. $data['documento_tipo'] . '-' . $data['documento_numero'] .'</td>
                                <td>'. $data['paciente'] .'</td>
                                <td class="text-center">' . $data['fecha_nacimiento'] . '</td>
                            </tr>');
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row footer">
            @2021 Clínica Inmater
        </div>
    </div>

    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
        jQuery(window).load(function (event) {
            jQuery('.loader').fadeOut(1000);
        });

        function myFunction() {
            var input, filter, table, tr, td, i;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                var encontro = false;
                for (var j = 0; j < 10; j++) {
                    td = tr[i].getElementsByTagName("td")[j];
                    if (td) {
                        if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                            encontro = true; break;
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
<?php // ob_end_flush(); ?>