<!DOCTYPE html>
<html lang="en">

<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
        integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <title>Clínica Inmater | Betas</title>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-1.11.1.min.js"></script>

    <style>
    html,
    body {
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

    .breadcrumb {
        background-color: #72a2aa;
    }

    .breadcrumb a {
        color: #000;
    }

    .col0 {
        background-color: #FFFF91 !important;
    }

    .col1 {
        background-color: #FFEBCD !important;
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
        <div class="demo" style="animation: showloader ease-out 1s forwards;">
            <img src="_images/load.gif" alt="">
        </div>
    </div>
    <?php
    // filtros
    $between = $beta = $medico = $embriologo_transferencia = $tipo_paciente = $edesde = $ehasta = $ngs = "";

    if (isset($_POST) && !empty($_POST)) {
        $between="";
        if (isset($_POST['val_beta']) && $_POST['val_beta'] != "" && isset($_POST['pro_beta']) && $_POST['pro_beta'] != "") {
            if ($_POST['val_beta'] == "1") {
                header("Location: med-betas-item.php?&pro=" . $_POST['pro_beta']);
            } else {
                $stmt = $db->prepare("UPDATE lab_aspira_t SET beta = ?, fecha_ultima_regla = NULL, iduserupdate = ? where pro = ? and estado is true;");
                $stmt->execute([$_POST['val_beta'], $login, $_POST['pro_beta']]);
            }
        }

        if (isset($_POST["beta"]) and $_POST["beta"] != "") {
            $beta = (int)$_POST["beta"];
            if ($beta == 1) {
                $between .= " and lab_aspira_t.beta not in (0, 2)";
            } else {
                $between .= " and lab_aspira_t.beta = $beta";
            }
        }

        if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
            $ini = $_POST["ini"];
            $fin = $_POST["fin"];
            $between = " AND hc_reprod.f_iny BETWEEN '$ini' AND '$fin'";
        }      

        if (isset($_POST["edesde"]) && $_POST["edesde"] != '' && isset($_POST["ehasta"]) && $_POST["ehasta"] != '') {
            $edesde = intval($_POST['edesde']) * 365;
            $ehasta = intval($_POST['ehasta']) * 365;
            $between .= " AND EXTRACT(day FROM (lab_aspira.fec - hc_paciente.fnac)) BETWEEN $edesde AND $ehasta";

        }

        if (isset($_POST["tipa"]) && !empty($_POST["tipa"])) {
            $tipo_paciente = $_POST['tipa'];
            $between.= " and lab_aspira.tip = '$tipo_paciente'";
        }

        if (isset($_POST["ngs"]) and !empty($_POST["ngs"])) {
            $ngs = $_POST["ngs"];

            if ($ngs == "s") {
                $between .= " and rep.pago_extras ilike '%ngs%'";
            } else {
                $between .= " and rep.pago_extras not ilike '%ngs%'";
            }
        }

        if (isset($_POST["medico"]) and !empty($_POST["medico"])) {
            $medico = $_POST["medico"];
            $between .= " and lab_aspira_t.med = '" . $medico . "'";
        } 

        if (isset($_POST["embriologo_transferencia"]) and !empty($_POST["embriologo_transferencia"])) {
            $embriologo_transferencia = $_POST["embriologo_transferencia"];
            $between .= " and lab_aspira_t.emb = '" . $embriologo_transferencia . "'";
        }
    } else {
        $between = "";
    }
    $rPaci = $db->prepare("SELECT
        hc_reprod.id
        , hc_paciente.dni,hc_paciente.idsedes, hc_paciente.ape, hc_paciente.nom, CONCAT(UPPER(hc_paciente.ape), ' ', UPPER(hc_paciente.nom)) AS nombres, lab_aspira_t.med, hc_reprod.p_dni
        , lab_aspira.pro, lab_aspira.tip
        , lab_aspira.fec2, lab_aspira.fec3, lab_aspira.fec4, lab_aspira.fec5, lab_aspira.fec6
        , CASE WHEN lab_aspira_t.beta NOT IN (0, 1, 2) THEN 1 ELSE lab_aspira_t.beta END AS beta
        , lab_aspira_t.beta AS estado_beta
        , lab_aspira_t.dia, lab_aspira_t.fecha_ultima_regla
        FROM hc_reprod
        INNER JOIN hc_paciente ON hc_paciente.dni = hc_reprod.dni
        INNER JOIN lab_aspira ON lab_aspira.rep = hc_reprod.id AND lab_aspira.dni = hc_paciente.dni and lab_aspira.estado is true
        INNER JOIN lab_aspira_t ON lab_aspira_t.pro = lab_aspira.pro and lab_aspira_t.estado is true
        WHERE hc_reprod.estado = true and 1 = 1$between
        ORDER BY hc_reprod.id DESC");
        $rPaci->execute();
        $key=$_ENV["apikey"];
 
    ?>

    <div class="box container">
        <div class="row1 header1">
            <nav aria-label="breadcrumb">
                <a class="breadcrumb" href="lista.php"><img src="_libraries/open-iconic/svg/x.svg" height="18"
                        width="18" alt="icon name"></a>
            </nav>

            <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
            <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
            <form action="" method="post" name="form2" id="form2">
                <div class="card mb-3">
                    <input type="hidden" name="val_beta">
                    <input type="hidden" name="pro_beta">
                    <h5 class="card-header">Filtros</h5>
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Mostrar desde</span>
                                    <input class="form-control" name="ini" type="date" id="ini"
                                        value="<?php if(isset($_POST['ini']))echo $_POST['ini']; ?>">
                                    <span class="input-group-addon">Hasta</span>
                                    <input class="form-control" name="fin" type="date" id="fin"
                                        value="<?php if(isset($_POST['fin']))echo $_POST['fin']; ?>">
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Edad cumplida</span>
                                    <input class="form-control" name="edesde" type="number"
                                        value="<?php if(isset($_POST['edesde']))echo $_POST['edesde']; ?>">
                                    <span class="input-group-addon">Y menor a</span>
                                    <input class="form-control" name="ehasta" type="number"
                                        value="<?php if(isset($_POST['ehasta']))echo $_POST['ehasta']; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Tipo Paciente</span>
                                    <select class="form-control" name='tipa'>
                                        <option value=''>Todos</option>
                                        <option value='P' <?php if($tipo_paciente == "P") print("selected"); ?>>PACIENTE
                                        </option>
                                        <option value='R' <?php if($tipo_paciente == "R") print("selected"); ?>>
                                            RECEPTORA</option>
                                        <option value='D' <?php if($tipo_paciente == "D") print("selected"); ?>>DONANTE
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">NGS</span>
                                    <select class="form-control" name='ngs'>
                                        <option value=''>Seleccionar</option>
                                        <option value='s' <?php if($ngs == "s") print("selected"); ?>>SI</option>
                                        <option value='n' <?php if($ngs == "n") print("selected"); ?>>NO</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Resultado</span>
                                    <select class="form-control" name="beta">
                                        <option value="">Todos</option>
                                        <option value="0" <?php if($beta=="0") { print('selected'); } ?>>Pendiente
                                        </option>
                                        <option value="1" <?php if($beta=="1") { print('selected'); } ?>>Positivo
                                        </option>
                                        <option value="2" <?php if($beta=="2") { print('selected'); } ?>>Negativo
                                        </option>
                                        <option value="3" <?php if($beta=="3") { print('selected'); } ?>>Bioquímico
                                        </option>
                                        <option value="4" <?php if($beta=="4") { print('selected'); } ?>>Aborto</option>
                                        <option value="5" <?php if($beta=="5") { print('selected'); } ?>>Anembrionado
                                        </option>
                                        <option value="6" <?php if($beta=="6") { print('selected'); } ?>>Ectópico
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Médico</span>
                                    <select class="form-control" name="medico" id="medico">
                                        <option value="">Todos</option>
                                        <?php print('<option value="' . $login . '" ' . (empty($medico) ? '' : 'selected') . '>' . mb_strtoupper($login) . '</option>'); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Embriologo Transferencia</span>
                                    <select class="form-control" name="embriologo_transferencia"
                                        id="embriologo_transferencia">
                                        <option value="">TODOS</option>
                                        <?php
																					$data_emb = $db->prepare("SELECT id codigo, nom nombre from lab_user order by nom;");
																					$data_emb->execute();
																					while ($info = $data_emb->fetch(PDO::FETCH_ASSOC)) {
																						print("<option value=".$info['codigo'] . ($embriologo_transferencia == $info['codigo'] ? " selected": "") .">".mb_strtoupper($info['nombre'])."</option>");
                                        	} ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <input type="Submit" class="btn btn-sm btn-danger" value="Mostrar" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <span><a href="https://app.inmater.pe/genesis/#/dashboard/monitoring-betas" target="_blank"
                rel="noopener noreferrer">Ir a Seguimiento de Embarazo <i
                    class="fas fa-external-link-alt"></i></a></span>

        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Buscar.."
            title="escribe un texto para buscar en la tabla">

        <div class="row content">
            <div class="card-body">
                <table class="table table-bordered" id="myTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">Item</th>
                            <th class="text-center align-middle">Protocolo</th>
                            <th class="text-center align-middle">Paciente</th>
                            <th class="text-center align-middle">Médico</th>
                            <th class="text-center align-middle">Resultado</th>
                            <th class="text-center align-middle">Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $stmt = $db->prepare("SELECT id, upper(nombre) nombre from man_beta_rinicial where estado = 1;");
                        $stmt->execute();
                        $data_betas = $stmt->fetchAll();
                        $t_0 = 0; $t_1 = 0; $t_2 = 0; $t_3 = 0; $t_4 = 0; $i = 0;
                        $contador = 1;

                        while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
                            $color='';
                            if ($paci['beta']==0) $color='class="col0"';
                            if ($paci['beta']==1) $color='class="col1"';
                            $beta = $paci['fec'.$paci['dia']]; //la fecha del dia de transferencia
                            if($paci['dia']==2) $dt=15;
                            if($paci['dia']==3) $dt=14;
                            if($paci['dia']==4) $dt=13;
                            if($paci['dia']==5) $dt=12;
                            if($paci['dia']==6) $dt=11;
                            $beta = date('d-m-Y', strtotime($beta.' + '.$dt.' days')); ?>

                        <tr <?php print($color); ?>>
                            <?php print("<td class='text-center'>".$contador++."</td>"); ?>
                            <?php
                                print('
                                <td class="text-center" >' . $paci['tip'] . '-' . $paci['pro'] . '</td>
                                <td '. $color . '>' . $paci['nombres'] . (empty($paci["fecha_ultima_regla"]) && $paci["beta"] != 0 && $paci["beta"] != 2 ? "<span style='color: red;'>(*)</span>" : "") . '</td>
                                <td class="text-center">' . mb_strtolower($paci['med']) . '</td>'); ?>

                            <td class="text-center">
                                <select class="form-control form-control-sm"
                                    onChange="Beta(this, '<?php echo $paci['pro']; ?>')">
                                    <option value=0 <?php if($paci['estado_beta']==0) { echo 'selected'; $t_0++; } ?>>
                                        Pendiente</option>
                                    <option value=1 <?php if($paci['estado_beta']==1) { echo 'selected'; $t_1++; } ?>>
                                        Positivo</option>
                                    <option value=2 <?php if($paci['estado_beta']==2) { echo 'selected'; $t_2++; } ?>>
                                        Negativo</option>
                                    <option value=3 <?php if($paci['estado_beta']==3) { echo 'selected'; $t_3++; } ?>>
                                        Bioquímico</option>
                                    <option value=4 <?php if($paci['estado_beta']==4) { echo 'selected'; $t_4++; } ?>>
                                        Aborto</option>
                                    <option value=5 <?php if($paci['estado_beta']==5) { echo 'selected'; $t_5++; } ?>>
                                        Anembrionado</option>
                                    <option value=6 <?php if($paci['estado_beta']==6) { echo 'selected'; $t_6++; } ?>>
                                        Ectópico</option>
                                </select>
                            </td>

                            <td><?php echo $beta; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row footer">
            <?php
            print("<em>Pendientes:&nbsp</em><b>" . $t_0 . "</b>,&nbsp<em>Negativos:&nbsp</em><b>" . $t_2 ."</b>,&nbsp<em>Positivos:&nbsp</em><b>" . $t_1 . "</b>,&nbsp<em>Total:&nbsp</em> <b>" . ($t_0 + $t_1 + $t_2 + $t_3 + $t_4) . "</b>" ); ?>
        </div>
    </div>

    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
    jQuery(window).load(function(event) {
        jQuery('.loader').fadeOut(1000);
    });
    $("#form1").submit(function(e) {
        e.preventDefault();
        if ($("#ini_fec").val() == "" || $("#fin_fec").val() == "" || $("#ini_h").val() == "" || $("#ini_m")
            .val() == "" || $("#fin_h").val() == "" || $("#fin_m").val() == "") {
            alert("Falta colocar las fechas y horas de No Disponible.");
            return false;
        }

        var form = this;
        form.submit();
    });
    $("#form2").submit(function(e) {
        var nombre_modulo = "reporte_betas";
        var ruta = "perfil_medico/reporte_betas.php";
        var tipo_operacion = "consulta";
        var login = $('#login').val();
        var key = $('#key').val();
        var clave = '';
        var valor = '';
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
            // processData: false,  // tell jQuery not to process the data
            // contentType: false,   // tell jQuery not to set contentType
            success: function(result) {
                console.log(result);
            }
        });
    });

    function Beta(beta, pro) {
        localStorage.setItem('back_url', window.location.href);
        document.form2.val_beta.value = beta.value;
        document.form2.pro_beta.value = pro;
        document.form2.submit();
    }

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
</body>

</html>
<?php ob_end_flush(); ?>