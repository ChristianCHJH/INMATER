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
    <script src="js/jquery-1.11.1.min.js"></script>
</head>
<body>
    <?php
    // verificar dni paciente
    $pro="";
    if (isset($_GET["pro"]) && !empty($_GET["pro"])) {
        $pro = $_GET["pro"];
    } else {
        print("No existe información");
        exit();
    }

    // actualizar datos
    if (isset($_POST["resultado_inicial"]) && $_POST["resultado_inicial"] != "") {
        $beta = 0;

        if (isset($_POST["resultado_final"]) && !empty($_POST["resultado_final"])) {
            $beta = (int)$_POST["resultado_final"];
        } else {
            $beta = (int)$_POST["resultado_inicial"];
        }

        $stmt = $db->prepare("UPDATE lab_aspira_t
            set beta=?, fecha_ultima_regla=?, iduserupdate = ?
            where pro=?;");
        $stmt->execute([$beta, isset($_POST['fecha_ultima_regla']) ? $_POST['fecha_ultima_regla'] : null, $login, $pro]);
    }

    // datos de consulta
    $consulta = $db->prepare("SELECT
        hc_reprod.id, hc_paciente.dni, lab_aspira_t.med, ape, nom
        , lab_aspira.pro, lab_aspira.tip, lab_aspira.dias, lab_aspira.fec2, lab_aspira.fec3, lab_aspira.fec4, lab_aspira.fec5, lab_aspira.fec6
        , lab_aspira_t.beta, lab_aspira_t.beta_rinicial, lab_aspira_t.beta_evolucion, lab_aspira_t.beta_sembarazo, lab_aspira_t.nsacos, lab_aspira_t.sembarazo_semanas, lab_aspira_t.sembarazo_nnacidos, lab_aspira_t.sembarazo_peso, lab_aspira_t.dia
        , lab_aspira_t.fecha_ultima_regla
        , case when lab_aspira_t.beta in (0, 2) then lab_aspira_t.beta else 1 end resultado_inicial
        , case when lab_aspira_t.beta not in (0, 1, 2) then lab_aspira_t.beta else 0 end resultado_final
        from hc_reprod
        inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
        inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.dni = hc_paciente.dni and lab_aspira.estado is true
        inner join lab_aspira_t on lab_aspira_t.pro = lab_aspira.pro and lab_aspira_t.estado is true
        where hc_reprod.estado = true and lab_aspira.pro = ?
    order by hc_reprod.id desc");

    $consulta->execute([$pro]);
    $data = $consulta->fetch(PDO::FETCH_ASSOC);
    $beta = $data['fec'.$data['dia']]; //la fecha del dia de transferencia

    $resultado_final_disabled = '';

    if ($data["beta"] == 0 or $data["beta"] == 2) {
        $resultado_final_disabled = 'disabled';
    }

    if ($data['dia']==2) { $dt=15; }
    if ($data['dia']==3) { $dt=14; }
    if ($data['dia']==4) { $dt=13; }
    if ($data['dia']==5) { $dt=12; }
    if ($data['dia']==6) { $dt=11; }

    $beta = date('Y-m-d', strtotime($beta.' + '.$dt.' days'));
    $medico = "";

    if (isset($_GET['med']) && !empty($_GET['med'])) {
        $medico=$_GET['med'];
    } ?>

    <div class="container">
        <nav aria-label="breadcrumb">
            <a class="breadcrumb link_back_url" style="background-color: #72a2aa;" href="javascript:void(0)">
                <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
            </a>
        </nav>

        <?php
        if (isset($_POST["resultado_inicial"]) && $_POST["resultado_inicial"] != "") {
            print('<div class="alert alert-success" role="alert">Se actualizaron los datos de la <b>beta</b>!</div>');
        }; ?>

        <div class="alert alert-warning" role="alert">Las Betas con resultado <b>positivo</b> tienen que indicar la fecha de última regla (FUR)</div>

        <div class="card mb-3">
            <h5 class="card-header">Resultado de la Beta</h5>

            <div class="card-body">
                <form action="" method="post" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Fecha</span>
                                <?php print('<input class="form-control" type="date" value="'.$beta.'" disabled>'); ?>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-6 col-lg-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Paciente</span>
                                <?php print('<input class="form-control" type="text" value="'.mb_strtoupper($data['ape']).' '.mb_strtoupper($data['nom']).'" disabled>'); ?>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Médico</span>
                                <?php print('<input class="form-control" type="text" value="'.mb_strtoupper($data['med']).'" disabled>'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Resultado</span>
                                <select class="form-control" id="resultado_inicial" name="resultado_inicial">
                                    <?php print('<option value="0" ' . ($data['resultado_inicial'] == 0 ? 'selected' : '') . '>PENDIENTE</option>'); ?>
                                    <?php print('<option value="1" ' . ($data['resultado_inicial'] == 1 ? 'selected' : '') . '>POSITIVO</option>'); ?>
                                    <?php print('<option value="2" ' . ($data['resultado_inicial'] == 2 ? 'selected' : '') . '>NEGATIVO</option>'); ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-6 col-lg-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Fecha de Última Regla</span>
                                <?php print('<input type="date" id="fecha_ultima_regla" name="fecha_ultima_regla" class="form-control" value="' . $data['fecha_ultima_regla'] . '" ' . $resultado_final_disabled . '>'); ?>
                            </div>
                        </div>
                    </div>

                    <h5><small><b>Tiempo de Gestación:</b> <em><span id="beta_semanas">0</span> semanas <span id="beta_dias">0</span> días</em></small></h5><hr>

                    <div class="row pb-2">
                        <!-- seguimiento embarazo -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">Estado</span>
                                    <?php
                                    print('<select class="form-control" id="resultado_final" name="resultado_final" ' . $resultado_final_disabled . '>');
                                    print('<option value="0" ' . ($data['resultado_final'] == 0 ? 'selected' : '') . '>EMBARAZO EN CURSO</option>');
                                    print('<option value="3" ' . ($data['resultado_final'] == 3 ? 'selected' : '') . '>BIOQUÍMICO</option>');
                                    print('<option value="4" ' . ($data['resultado_final'] == 4 ? 'selected' : '') . '>ABORTO</option>');
                                    print('<option value="5" ' . ($data['resultado_final'] == 5 ? 'selected' : '') . '>ANEMBRIONADO</option>');
                                    print('<option value="6" ' . ($data['resultado_final'] == 6 ? 'selected' : '') . '>ECTÓPICO</option>');
                                    print('</select>'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 text-center">
                            <input type="Submit" class="btn btn-danger" name="actualizar" value="Actualizar"/>
                            <a href="javascript:void(0)" class="btn btn-dark link_back_url">Regresar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        if ($("#fecha_ultima_regla").val() != "") {
            $("#beta_semanas").html(numero_semanas(document.getElementById("fecha_ultima_regla").valueAsDate, new Date()));
            $("#beta_dias").html(numero_dias_restantes(document.getElementById("fecha_ultima_regla").valueAsDate, new Date()));
        }

        $("#resultado_inicial").change(function () {
            if ($(this).val() == 1) {
                $("#fecha_ultima_regla").prop('disabled', false);
                $("#resultado_final").prop('disabled', false);
            } else {
                $("#fecha_ultima_regla").prop('disabled', 'disabled');
                $("#resultado_final").prop('disabled', 'disabled');
            }
        });

        $("#fecha_ultima_regla").change(function () {
            if ($(this).val() != '') {
                $("#beta_semanas").html(numero_semanas(document.getElementById("fecha_ultima_regla").valueAsDate, new Date()));
                $("#beta_dias").html(numero_dias_restantes(document.getElementById("fecha_ultima_regla").valueAsDate, new Date()));
            }
        });

        $("#form1").submit(function(e) {
            e.preventDefault();
            var validation = true;
            if ($("#resultado_inicial").val() == 1 && $("#fecha_ultima_regla").val() == '') {
                alert("Falta colocar la fecha de última regla.");
                return false;
            }

            var form = this;
            form.submit();
        });

        $(document).ready(function () {
            $(document).on("click", ".link_back_url", function () {
                var backUrl = localStorage.getItem('back_url');
                if (backUrl) {
                    location.href = backUrl;
                }
            });
        });

        function numero_semanas(date1, date2) {
            var semanas = 1000 * 60 * 60 * 24 * 7;
            var date1ms = date1.getTime();
            var date2ms = date2.getTime();

            return Math.floor(Math.abs(date2ms - date1ms) / semanas);
        }

        function numero_dias_restantes(date1, date2) {
            var dias = 1000 * 60 * 60 * 24;
            var date1ms = date1.getTime();
            var date2ms = date2.getTime();
            var total_dias = Math.floor(Math.abs(date2ms - date1ms) / dias);

            return total_dias % 7;
        }
    </script>
</body>
</html>
