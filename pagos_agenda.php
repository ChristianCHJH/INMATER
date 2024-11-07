<!DOCTYPE html>
<html>

<head>
    <title>Inmater Clínica de Fertilidad | Programacion Sala de Procedimientos</title>
    <?php
     include 'seguridad_login.php';
     require("_database/database.php");
     require("config/environment.php");
    ?>
        
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
        integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css?v=2" />
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <script src="js/jquery-1.11.1.min.js"></script>
</head>

<body>
    <?php
    // consulta rol de usuario
    $rUser = $db->prepare("SELECT role from usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC);
    // datos de POST
    $ini = date('Y-m-d');
    $fin = date('Y-m-d');
    $paciente = "";
    if (isset($_POST) && !empty($_POST)) {
        if (isset($_POST["ini"]) && !empty($_POST["ini"]) ) {
            $ini = $_POST['ini'];
        }
        if (isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
            $fin = $_POST['fin'];
        }
        if (isset($_POST["paciente"]) && !empty($_POST["paciente"]) ) {
            $paciente = $_POST['paciente'];
        }
    } ?>
    <?php require('_includes/menu_facturacion.php'); ?>
    <div class="container1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_facturacion.php">Inicio</a></li>
                <li class="breadcrumb-item">Facturación</li>
                <li class="breadcrumb-item active" aria-current="page">Programación Sala</li>
            </ol>
        </nav>
        <div data-role="collapsible" id="Perfi">
            <div class="card mb-3">
                <h5 class="card-header">Programación de Sala</h5>
                <div class="card-body">
                    <form action="" method="post" data-ajax="false" id="form1">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Inicio</span>
                                    <input class="form-control form-control-sm" name="ini" type="date"
                                        value="<?php print($ini); ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Fin</span>
                                    <input class="form-control form-control-sm" name="fin" type="date"
                                        value="<?php print($fin); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Paciente</span>
                                    <input class="form-control form-control-sm" name="paciente" type="text"
                                        value="<?php print($paciente); ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <input type="Submit" class="btn btn-danger btn-sm" name="Mostrar" value="Ver" />
                            </div>
                        </div>
                    </form>
                    <table class='table table-responsive table-bordered align-middle header-fixed Datos' id='repo_model_01'>
                        <thead class='thead-dark'>
                            <tr>
                                <th class="text-center" style="min-width: 100px;">Fecha</th>
                                <th class="text-center">Hora inicio</th>
                                <th class="text-center">Hora Fin</th>
                                <th class="text-center">Turno (min)</th>
                                <th class="text-center">Programa</th>
                                <th class="text-center">Paciente</th>
                                <th class="text-center">Tipo Procedimiento</th>
                                <th class="text-center">Procedimiento</th>
                                <th class="text-center">Muestra</th>
                                <th class="text-center">Foliculos</th>
                                <th class="text-center">Médico</th>
                                <th align="center">Extras Médico</th>
                                <th align="center" class="noVer">Informe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $id_pros='';
                            // consulta de pacientes de laboratorio
                            $stmt3 = $db->prepare("SELECT * FROM consultaagendasalaprocedimientos(?, ?, ?);");
                            $stmt3->execute([$ini, $fin, $paciente]);
                            $data_sala = $stmt3->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($data_sala as $key => $rep) {
                                // buscar la clase segun el tipo de paciente
                                $class_tipo_paciente = "";
                                if ($rep['t_mue'] == 0) {
                                    $class_tipo_paciente = "consulta_ginecologica";
                                }
                                if ($rep['medios_comunicacion_id'] === 2) {
                                    $class_tipo_paciente = "programa_inmater";
                                }
                                if ($rep['don'] == 'D') {
                                    $class_tipo_paciente = "tipo-paciente-donante";
                                }
                                print("<tr class='$class_tipo_paciente'>");
                                print("<td>".$rep['fecha']."</td>"); ?>
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
                                } ?>
                            </td>
                            <?php
                                if (empty($rep['p_od']) || (!empty($rep['p_od']) && $rep['h_tra'] <> '')) {
                                    print("
                                        <td align='center'>".$rep['horafin']."</td>
                                        <td align='center'>".$rep['turno']."</td>");
                                } else {
                                    print('<td></td><td></td>');
                                } ?>


                            <!-- PROGRAMA -->
                            <?php echo '<td align=center>'.$rep['nombreprograma'].'</td>' ?>

                            <!-- paciente -->
                            <td>
                                <?php if ($rep['n_fol']=='-') $url="e_pare.php?id=&ip=".$rep['dni'];
                                    else $url="e_paci.php?id=".$rep['dni']; ?>
                                <a href='<?php echo $url; ?>' target="_blank"
                                    rel="external"><?php echo $rep['nombres_completos']; ?> <i
                                        class="fas fa-external-link-alt"></i></a>
                                <?php
                                    if ($rep['don'] == 'D') echo ' (DONANTE)';
                                    if ($rep['p_od'] <> '') echo ' (RECEPTORA)'; ?>
                            </td>
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
                                        
                                        //echo '<a href="'.$url.'" rel="external">';
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
                            <td>
                                <?php
                                    $examen=$url="";
                                    if ($rep['t_mue'] == 0) {
                                        print($rep['procedimiento']);
                                    }
                                    if ($rep['h_tra'] <> '') { $examen.='TRANSFERENCIA<br>'; }

                                    if ($rep['n_fol']<>'-' && $rep['n_fol']<>'--') {
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

                                        if ($rep['p_dtri'] >= 1) { $examen.="DUAL TRIGGER<br>"; }

                                        if ($rep['p_cic'] >= 1) { $examen.="CICLO NATURAL<br>"; }

                                        if ($rep['p_fiv'] >= 1) {
                                            $examen.="FIV<br>";
                                        }

                                        if ($rep['p_icsi'] >= 1){
                                            $examen.=$_ENV["VAR_ICSI"] . "<br>";
                                        }

                                        if ($rep['p_od'] <> '') { $examen.="OD FRESCO<br>"; }

                                        if ($rep['p_cri'] >= 1) {
                                            $examen.="CRIO ÓVULOS<br>";
                                        }

                                        if ($rep['p_iiu'] >= 1) { $examen.="IIU<br>"; }

                                        if ($rep['p_don'] == 1) {
                                            $examen.="DONACIÓN FRESCO<br>";
                                        }

                                        $var = ["1" => "Procedimiento sin sedación", "2" => "Procedimiento bajo sedación"];

                                        if ($rep['des_don'] == null && $rep['des_dia'] >= 1) {
                                            $examen.="TED<br>" . $var[$rep['anestesia']];
                                        }

                                        if ($rep['des_don'] == null && $rep['des_dia'] === 0){
                                            $examen.="<small>Descongelación Óvulos Propios</small><br>";
                                        }

                                        if ($rep['des_don'] <> null && $rep['des_dia'] >= 1){
                                            $examen.="EMBRIODONACIÓN<br>";
                                        }

                                        if ($rep['des_don'] <> null and $rep['des_dia'] === 0){
                                            $examen.="<small>Descongelación Óvulos Donados</small><br>";
                                        }

                                        // verificar si muestra transferencia acompañado de otro examen, solo debe mostrar transferencia
                                        if (strpos($examen, "TRANSFERENCIA") !== false) {
                                            $examen="TRANSFERENCIA<br>" . $var[$rep['anestesia']];
                                        }

                                        print($examen);
                                    }

                                    if ($rep['don'] == "Biopsia testicular") { echo "BIOPSIA TESTICULAR"; }
                                    if ($rep['don'] == "Aspiración de epidídimo") { echo "ASPIRACIÓN DE EPIDÍDIMO"; } ?>
                            </td>
                            <!-- muestra -->
                            <td><?php $t_mue = 'No Aplica';
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
                            <td><?php echo $rep['p_extras']; ?></td>
                            <td>
                                <?php
                                if (!!$rep['informe']) {
                                    print('<a href="'.$rep['informe'].'" target="new">Informe <i class="fas fa-external-link-alt"></i></a>');
                                } ?>
                            </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br><br>
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
            td = tr[i].getElementsByTagName("td")[2];
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
    
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables.net/jquery.dataTables.min.js"></script>
    <script src="js/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="js/datatables.net/dataTables.buttons.min.js"></script>
    <script src="js/datatables.net/jszip.min.js"></script>
    <script src="js/datatables.net/buttons.html5.min.js"></script>
   
    <script>
       
    $('.Datos').DataTable({
        "searching": false,
        "paging":   false,
        language: {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sProcessing": "Procesando...",
        },
        orderCellsTop: true,
        responsive: "true",
        "dom": '<"top"lB>rt<"bottom"ip>',
        buttons: [{
            title: 'Inmater - Programación de Sala',
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel"></i> ',
            titleAttr: 'Clic para Exportar a Excel',
            className: 'btn btn-success'
        },
    ],
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
    </script>
</body>

</html>