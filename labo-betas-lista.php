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
    <script>
        function Beta(beta, pro) {
            localStorage.setItem('back_url_betas_lista', window.location.href);
            document.form2.val_beta.value=beta.value;
        	document.form2.pro_beta.value=pro;
        	document.form2.submit();
        }
    </script>

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
    $between = $beta = $t_med = $t_emb = $tipa = $edesde = $ehasta = $procedimiento = $ngs = $inc="";

    if (isset($_GET) && !empty($_GET)) {
        if (isset($_GET['val_beta']) && $_GET['val_beta'] != "" && isset($_GET['pro_beta']) && $_GET['pro_beta'] != "") {
            if ($_GET['val_beta'] == "1") {
                header("Location: labo-betas-item.php?&pro=" . $_GET['pro_beta']);
            } else {
                $stmt = $db->prepare("UPDATE lab_aspira_t SET beta = ?, fecha_ultima_regla = NULL, iduserupdate = ? where pro = ? and estado is true;");
                $stmt->execute([$_GET['val_beta'], $login, $_GET['pro_beta']]);
            }
        }

        if (isset($_GET["beta"]) and $_GET["beta"] != "") {
            $beta = (int)$_GET["beta"];
            if ($beta == 1) {
                $between .= " and lab_aspira_t.beta not in (0, 2)";
            } else {
                $between .= " and lab_aspira_t.beta = $beta";
            }
        }

        if (isset($_GET["ini"]) && !empty($_GET["ini"]) && isset($_GET["fin"]) && !empty($_GET["fin"])) {
            $between .= " and hc_reprod.f_iny between '".$_GET["ini"]."' and '".$_GET["fin"]."'";
        }

        if (isset($_GET["edesde"]) && !empty($_GET["edesde"])  && isset($_GET["ehasta"]) && !empty($_GET["ehasta"])) {
                $edesde_date = new DateTime($_GET['edesde']);
                $ehasta_date = new DateTime($_GET['ehasta']);

                $today = new DateTime();

                $edesde_diff = $today->diff($edesde_date);
                $ehasta_diff = $today->diff($ehasta_date);

                $edesde_days = $edesde_diff->days;
                $ehasta_days = $ehasta_diff->days;
                $between .= " AND EXTRACT(DAY FROM age(lab_aspira.fec, hc_paciente.fnac)) BETWEEN $edesde_days AND $ehasta_days";
        }

        if (isset($_GET["pro"]) && !empty($_GET["pro"])) {
            $procedimiento = $_GET["pro"];
            $between .= " and unaccent(lab_aspira.pro) ilike ('%$procedimiento%')";
        }

        if (isset($_GET["ngs"]) and !empty($_GET["ngs"])) {
            $ngs = $_GET["ngs"];

            if ($ngs == "s") {
                $between .= " and unaccent(rep.pago_extras) ilike '%ngs%'";
            } else {
                $between .= " and unaccent(rep.pago_extras) not ilike '%ngs%'";
            }
        }

        if (isset($_GET["t_med"]) and !empty($_GET["t_med"])) {
            $t_med=$_GET["t_med"];
            $between.=" and lab_aspira_t.med = '".$t_med."'";
        }

        if (isset($_GET["t_emb"]) and !empty($_GET["t_emb"])) {
            $t_emb=$_GET["t_emb"];
            $between.=" and lab_aspira_t.emb = '".$t_emb."'";
        }

        if (isset($_GET["tipa"]) && !empty($_GET["tipa"])) {
            $tipa = $_GET['tipa'];
            $between.= " and lab_aspira.tip = '$tipa'";
        }

        if (isset($_GET["inc"])) {
            if ($_GET["inc"] != "") {
                $inc = $_GET['inc'];
                $between.= " and (case when hc_reprod.des_don is null and hc_reprod.des_dia >= 1 then la.inc = $inc else lab_aspira.inc = $inc end)";
            }
        }
    } else {
        $between .= " and hc_reprod.id = 0";
    }

    $rPaci = $db->prepare("SELECT
        hc_reprod.id
        , hc_paciente.dni, hc_paciente.ape, hc_paciente.nom, concat(upper(hc_paciente.ape), ' ', upper(hc_paciente.nom)) nombres, lab_aspira_t.med, hc_reprod.p_dni
        , lab_aspira.pro, lab_aspira.tip
        , lab_aspira.fec2, lab_aspira.fec3, lab_aspira.fec4, lab_aspira.fec5, lab_aspira.fec6
        , case when lab_aspira_t.beta not in (0, 1, 2) then 1 else lab_aspira_t.beta end beta
        , lab_aspira_t.beta estado_beta
        , lab_aspira_t.dia, lab_aspira_t.fecha_ultima_regla
        , rep.pago_extras
        from hc_reprod
        inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
        inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.dni=hc_paciente.dni and lab_aspira.estado is true
        inner join lab_aspira_t on lab_aspira_t.pro=lab_aspira.pro and lab_aspira_t.estado is true
        inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
        left join lab_aspira la on la.pro = lab_aspira_dias.pro_c and la.estado is true
        left join hc_reprod rep on rep.id = la.rep
        where hc_reprod.estado = true and 1=1$between
        group by hc_reprod.id, hc_paciente.dni, ape, nom, hc_paciente.med,
        lab_aspira.pro, lab_aspira.tip, lab_aspira_t.med, lab_aspira_t.fecha_ultima_regla
        , lab_aspira.fec2, lab_aspira.fec3, lab_aspira.fec4, lab_aspira.fec5, lab_aspira.fec6
        , lab_aspira_t.beta, lab_aspira_t.dia
        , lab_aspira_dias.pro
        , lab_aspira_dias.pro_c, rep.pago_extras
        order by hc_reprod.id desc");
$rPaci->execute();

     ?>

    <div class="box container">
        <div class="row1 header1">
            <nav aria-label="breadcrumb">
                <a class="breadcrumb" href="labo-betas-resumen.php" style="background-color: #72a2aa;">
                    <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
                </a>
            </nav>

            <form action="" method="get" name="form2">
                <input type="hidden" name="val_beta">
                <input type="hidden" name="pro_beta">

                <div class="card mb-3">
                    <h5 class="card-header"><small><b>Lista de Betas</b></small></h5>
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Mostrar Desde</span>
                                    <input class="form-control" name="ini" type="date" id="ini" value="<?php if(isset($_GET['ini']))echo $_GET['ini']; ?>">
                                    <span class="input-group-addon">Hasta</span>
                                    <input class="form-control" name="fin" type="date" id="fin" value="<?php if(isset($_GET['fin']))echo $_GET['fin']; ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Edad cumplida</span>
                                    <input class="form-control" name="edesde" type="number" value="<?php if(isset($_GET['edesde']))echo $_GET['edesde']; ?>">
                                    <span class="input-group-addon">Y menor a</span>
                                    <input class="form-control" name="ehasta" type="number" value="<?php if(isset($_GET['ehasta']))echo $_GET['ehasta']; ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">N° Proc.</span>
                                    <input class="form-control" name="pro" type="text" value="<?php if(isset($procedimiento))echo $procedimiento; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Tipo Paciente</span>
                                    <select class="form-control" name='tipa'>
                                        <option value='' >Todos</option>
                                        <option value='P' <?php if($tipa == "P") print("selected"); ?>>PACIENTE</option>
                                        <option value='R' <?php if($tipa == "R") print("selected"); ?>>RECEPTOR</option>
                                        <option value='D' <?php if($tipa == "D") print("selected"); ?>>DONANTE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">NGS</span>
                                    <select class="form-control" name='ngs'>
                                        <option value='' >Seleccionar</option>
                                        <option value='s' <?php if($ngs == "s") print("selected"); ?>>SI</option>
                                        <option value='n' <?php if($ngs == "n") print("selected"); ?>>NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Incubadora</span>
                                    <select name='inc' class="form-control">
                                        <option value="" selected>Seleccionar</option>
                                        <?php
                                            $data = $db->prepare("select id, codigo from incubadora where estado=1");
                                            $data->execute();
                                            while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                                print("<option value=".$info['codigo']);
                                            if ($inc === $info['codigo'])
                                                echo " selected";
                                            print(">".$info['codigo']."</option>");
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Médico Transferencia</span>
                                    <select class="form-control" name="t_med" id="t_med">
                                        <option value="">Todos</option>
                                        <option value="mvelit" <?php if($t_med=="mvelit") { print('selected'); } ?>>mvelit</option>
                                        <option value="humanidad" <?php if($t_med=="humanidad") { print('selected'); } ?>>humanidad</option>
                                        <option value="eescudero" <?php if($t_med=="eescudero") { print('selected'); } ?>>eescudero</option>
                                        <option value="mascenzo" <?php if($t_med=="mascenzo") { print('selected'); } ?>>mascenzo</option>
                                        <option value="rbozzo" <?php if($t_med=="rbozzo") { print('selected'); } ?>>rbozzo</option>
                                        <option value="medico1" <?php if($t_med=="medico1") { print('selected'); } ?>>medico1</option>
                                        <option value="cbonomini" <?php if($t_med=="cbonomini") { print('selected'); } ?>>cbonomini</option>
                                        <option value="tacna" <?php if($t_med=="tacna") { print('selected'); } ?>>tacna</option>
                                        <option value="lbernuy" <?php if($t_med=="lbernuy") { print('selected'); } ?>>lbernuy</option>
                                        <option value="cosorio" <?php if($t_med=="cosorio") { print('selected'); } ?>>cosorio</option>
                                        <option value="jolivas" <?php if($t_med=="jolivas") { print('selected'); } ?>>jolivas</option>
                                        <option value="apuertas" <?php if($t_med=="apuertas") { print('selected'); } ?>>apuertas</option>
                                        <option value="jtremolada" <?php if($t_med=="jtremolada") { print('selected'); } ?>>jtremolada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Embriologo Transferencia</span>
																		<select class="form-control" name="t_emb" id="t_emb">
                                        <option value="">TODOS</option>
                                        <?php
																					$data_emb = $db->prepare("SELECT id codigo, nom nombre from lab_user order by nom;");
																					$data_emb->execute();
																					while ($info = $data_emb->fetch(PDO::FETCH_ASSOC)) {
																						print("<option value=".$info['codigo'] . ($t_emb == $info['codigo'] ? " selected": "") .">".mb_strtoupper($info['nombre'])."</option>");
                                        	} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Resultado</span>
                                    <select class="form-control" name="beta">
                                        <option value="">Todos</option>
                                        <option value="0" <?php if($beta=="0") { print('selected'); } ?>>Pendiente</option>
                                        <option value="1" <?php if($beta=="1") { print('selected'); } ?>>Positivo</option>
                                        <option value="2" <?php if($beta=="2") { print('selected'); } ?>>Negativo</option>
                                        <option value="3" <?php if($beta=="3") { print('selected'); } ?>>Bioquímico</option>
                                        <option value="4" <?php if($beta=="4") { print('selected'); } ?>>Aborto</option>
                                        <option value="5" <?php if($beta=="5") { print('selected'); } ?>>Anembrionado</option>
                                        <option value="6" <?php if($beta=="6") { print('selected'); } ?>>Ectópico</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center">
                                <input type="Submit" class="btn btn-danger" value="Mostrar"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Buscar datos del paciente" title="escribe los nombre o apellidos de la paciente">
        <div class="card row content">
            <div class="card-body">
                Falta la fecha de última regla <span style="color: red;">(*)</span>
                <table class="table table-bordered" id="myTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">Item</th>
                            <th class="text-center align-middle">Protocolo</th>
                            <th class="text-center align-middle">Paciente</th>
                            <th class="text-center align-middle">Médico</th>
                            <th class="text-center align-middle">Resultado Beta</th>
                            <th class="text-center align-middle">Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $stmt = $db->prepare("SELECT id, upper(nombre) nombre from man_beta_rinicial where estado = 1;");
                        $stmt->execute();
                        $data_betas = $stmt->fetchAll();
                        $t_0=0; $t_1=0; $t_2=0; $t_3=0; $t_4=0; $i = 0;
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

                            <tr>
                                <?php print("<td class='text-center'>".$contador++."</td>"); ?>
                                <?php
                                print('
                                <td class="text-center" '. $color . '>
                                    <a href="info_r.php?a=' . $paci['pro'] . '&b=' . $paci['dni'] . '&c=' . $paci['p_dni'] . '" target="_blank">' . $paci['tip'] . '-' . $paci['pro'] . '</a>
                                </td>
                                <td '. $color . '>' . $paci['nombres'] . (empty($paci["fecha_ultima_regla"]) && $paci["beta"] != 0 && $paci["beta"] != 2 ? "<span style='color: red;'>(*)</span>" : "") . '</td>
                                <td class="text-center">' . $paci['med'] . '</td>'); ?>

                                <td class="text-center">
                                    <select class="form-control form-control-sm" onChange="Beta(this, '<?php echo $paci['pro']; ?>')">
                                        <option value=0 <?php if($paci['estado_beta']==0) { echo 'selected'; $t_0++; } ?>>Pendiente</option>
                                        <option value=1 <?php if($paci['estado_beta']==1) { echo 'selected'; $t_1++; } ?>>Positivo</option>
                                        <option value=2 <?php if($paci['estado_beta']==2) { echo 'selected'; $t_2++; } ?>>Negativo</option>
                                        <option value=3 <?php if($paci['estado_beta']==3) { echo 'selected'; $t_3++; } ?>>Bioquímico</option>
                                        <option value=4 <?php if($paci['estado_beta']==4) { echo 'selected'; $t_4++; } ?>>Aborto</option>
                                        <option value=5 <?php if($paci['estado_beta']==5) { echo 'selected'; $t_5++; } ?>>Anembrionado</option>
                                        <option value=6 <?php if($paci['estado_beta']==6) { echo 'selected'; $t_6++; } ?>>Ectópico</option>
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
<?php ob_end_flush(); ?>