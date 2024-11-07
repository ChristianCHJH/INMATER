<!DOCTYPE HTML>
<html>
<head>
    <?php
        include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <?php require ('_includes/repolab_menu.php'); ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Reporte FIV/ ICSI - Betas</li>
            </ol>
        </nav>
        <div class="card mb-3">
            <?php
                $fecha = $between = $med = $embins = $ovo = $ini = $fin = $ini_fivicsi = $fin_fivicsi = $tipo_transferencia = $url = "";

                if (isset($_POST) && !empty($_POST)) {
                    if (isset($_POST["fecha"]) && !empty($_POST["fecha"]) && isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                        $fecha = $_POST["fecha"];
                        $ini = $_POST['ini'];
                        $fin = $_POST['fin'];
                        $url .= "&fecha=$fecha&ini=$ini&fin=$fin";

                        switch ($fecha) {
                            case 'ftra':
                            $between = " and (
                                (r.f_tra is not null and CAST(r.f_tra as date) between '$ini' and '$fin') or
                                (r.f_tra is null and CAST(r.f_iny as date) between '$ini' and '$fin'))"; break;
                            case 'fasp':
                            $between = " and (
                                (r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and CAST(r.f_asp as date) between '$ini' and '$fin') or
                                (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and CAST(r1.f_asp as date) between '$ini' and '$fin'))"; break;
                            default: break;
                        }
                    }

                    if (isset($_POST["edad"]) && !empty($_POST["edad"]) && isset($_POST["desde"]) && !empty($_POST["desde"]) && isset($_POST["hasta"]) && !empty($_POST["hasta"])) {
                        $edad = $_POST["edad"];
                        $desde = $_POST['desde']*365;
                        $hasta = $_POST['hasta']*365;
                        $url .= "&edad=$edad&desde=$desde&hasta=$hasta";

                        switch ($edad) {
                            case 'edad_paciente':
                            $between .= " and datediff(now(), p.fnac) between $desde and $hasta"; break;

                            case 'edad_ovulo':
                            $between .= " and (
                                (r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and datediff(r.f_asp, p.fnac) between $desde and $hasta) or
                                (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and datediff(r1.f_asp, p.fnac) between $desde and $hasta))"; break;
                            default: break;
                        }
                    }

                    if (isset($_POST["tipo_transferencia"]) && !empty($_POST["tipo_transferencia"])) {
                        $tipo_transferencia = $_POST['tipo_transferencia'];
                        $url .= "&tipo_transferencia=$tipo_transferencia";

                        switch ($tipo_transferencia) {
                            case 'fresca':
                                $between .= " and r.pago_extras ilike ('%TRANSFERENCIA FRESCO%')";
                                break;
                            case 'descongelada':
                                $between .= " and r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%')";
                                break;
                            
                            default: break;
                        }
                    }

                    if (isset($_POST["med"]) && !empty($_POST["med"])) {
                        $med = $_POST['med'];
                        $between .= " and r.med = '$med'";
                        $url .= "&med=$med";
                    }

                    if (isset($_POST["embins"]) && !empty($_POST["embins"])) {
                        $embins = $_POST['embins'];
                        $between .= " and ((r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and a.emb0 = '$embins') or (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and a1.emb0 = '$embins'))";
                        $url .= "&embins=$embins";
                    }

                    if (isset($_POST["ovo"]) && !empty($_POST["ovo"])) {
                        $ovo = $_POST['ovo'];
                        $between .= " and ((r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and a.o_ovo = '$ovo') or (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and a1.o_ovo = '$ovo'))";
                        $url .= "&ovo=$ovo";
                    }

                    if (isset($_POST["ngs"]) && !empty($_POST["ngs"])) {
                        $ngs = $_POST['ngs'];
                        $url .= "&ngs=$ngs";

                        switch ($ngs) {
                            case 'si':
                                $between .= " and r.pago_extras ilike ('%NGS%')";
                                break;
                            case 'no':
                                $between .= " and r.pago_extras not ilike ('%NGS%')";
                                break;
                            
                            default: break;
                        }
                    }
                } else {
                    $ini = date('Y-m-01');
                    $fin = date('Y-m-t');
                    $fecha = 'ftra';
                    $between = " and (
                        (r.f_tra is not null and CAST(r.f_tra as date) between '$ini' and '$fin') or
                        (r.f_tra is null and CAST(r.f_iny as date) between '$ini' and '$fin')
                    )";
                    $url .= "&fecha=$fecha&ini=$ini&fin=$fin";
                } ?>
            <h5 class="card-header">Filtros</h5>
            <div class="card-body">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="fecha" id="exampleRadios1" value="ftra" <?php ($fecha=='ftra' ? print('checked') : '') ?>>
                            <label class="form-check-label" for="exampleRadios1">Fecha de Transferencia</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="fecha" id="exampleRadios2" value="fasp" <?php ($fecha=='fasp' ? print('checked') : '') ?>>
                            <label class="form-check-label" for="exampleRadios2">Fecha de Aspiración</label>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Desde</span>
                                <input class="form-control form-control-sm" name="ini" type="date" value="<?php echo $ini; ?>" id="ini">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Hasta</span>
                                <input class="form-control form-control-sm" name="fin" type="date" value="<?php echo $fin; ?>" id="fin">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="edad" id="exampleRadios3" value="edad_paciente" <?php (isset($_POST['edad']) && $_POST['edad']=='edad_paciente' ? print('checked') : print('')); ?>>
                            <label class="form-check-label" for="exampleRadios3">Edad de Paciente</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="edad" id="exampleRadios4" value="edad_ovulo" <?php (isset($_POST['edad']) && $_POST['edad']=='edad_ovulo' ? print('checked') : print('')); ?>>
                            <label class="form-check-label" for="exampleRadios4">Edad del Óvulo</label>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Desde</span>
                                <input class="form-control form-control-sm" name="desde" type="number" value="<?php echo $_POST['desde'] ?? '' ?>" id="desde">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Hasta</span>
                                <input class="form-control form-control-sm" name="hasta" type="number" value="<?php echo $_POST['hasta'] ?? '' ?>" id="hasta">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Médico</span>
                                <select name='med' class="form-control form-control-sm">
                                    <option value='' >todos</option>
                                    <?php
                                        $consulta = $db->prepare("SELECT codigo, nombre FROM man_medico WHERE estado=1");
                                        $consulta->execute();
                                        $consulta->setFetchMode(PDO::FETCH_ASSOC);
                                        $datos1 = $consulta->fetchAll();
                                        foreach ($datos1 as $row) {
                                            if ($med == $row['codigo']) {$selected="selected";}
                                            else {$selected="";}
                                            print("<option value='".$row['codigo']."' $selected>".strtolower($row['nombre'])."</option>");
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Emb. Inseminación</span>
                                <select name='embins' class="form-control form-control-sm">
                                    <option value="">todos</option>
                                    <?php
                                        $rEmb = $db->prepare("SELECT id,nom FROM lab_user WHERE sta=0");
                                        $rEmb->execute();
                                        $rEmb->setFetchMode(PDO::FETCH_ASSOC);
                                        $datos1 = $rEmb->fetchAll();
                                        foreach ($datos1 as $row) {
                                            if ($embins == $row['id']) {$embinsi="selected";}
                                            else {$embinsi="";}
                                            print("<option value='".$row['id']."' $embinsi>".strtolower($row['nom'])."</option>");
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Origen Ovocitos</span>
                                <select name='ovo' class="form-control form-control-sm">
                                    <option value='' >todos</option>
                                    <option value='fresco' <?php if($ovo == "fresco") {print("selected");} ?> >fresco</option>
                                    <option value='vitrificado' <?php if($ovo == "vitrificado") {print("selected");} ?>>vitrificado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tipo de Transferencia</span>
                                <select name='tipo_transferencia' class="form-control form-control-sm">
                                    <option value='' >todos</option>
                                    <option value='fresca' <?php if($tipo_transferencia == "fresca") {print("selected");} ?> >fresca</option>
                                    <option value='descongelada' <?php if($tipo_transferencia == "descongelada") {print("selected");} ?>>descongelada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">NGS</span>
                                <select name='ngs' class="form-control form-control-sm">
                                    <option value='' >todos</option>
                                    <option value='si' <?php if($ngs == "si") {print("selected");} ?>>si</option>
                                    <option value='no' <?php if($ngs == "no") {print("selected");} ?>>no</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center">
                            <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-3">
        <?php
            $consulta = $db->prepare("SELECT id, nom from lab_user where sta=0");
            $consulta->execute();
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $datos = $consulta->fetchAll();

            $item = $ambos =  $fiv = $icsi = $recep = $crio = $ted = $dgp = $piiu = $emb = $od = $don = $tedngs = 0;
            $rPaciBet = $db->prepare("SELECT
                r.id, a.pro, a1.pro pro1
                , r.p_fiv, r.p_icsi, r1.p_fiv p_fiv1, r1.p_icsi p_icsi1
                , r.pago_extras
                , r.des_don, r.des_dia, r.p_dtri, r.p_cic
                , r.p_od, r.p_cri, r.p_iiu, r.p_don
                , coalesce(t.beta, 0) beta
                , p.dni, p.ape, p.nom, p.fnac
                , p1.dni dni1, p1.ape ape1, p1.nom nom1
                , r.f_tra, r.h_tra, r.f_iny
                , a.tip, a.pro, a.vec, a.dias, r.med, r1.med med1
                , case when r.f_tra = '1899-12-30'::date then r.f_iny else r.f_tra end fectra
                , coalesce(r.f_asp, '1899-12-30') fecasp
                , coalesce(r1.f_asp, '1899-12-30') fecasp1
                , a.o_ovo, a1.o_ovo o_ovo1
                , a.emb0, a1.emb0 emb01
                from hc_reprod r
                inner join lab_aspira a on a.rep = r.id and a.estado is true
                left join lab_aspira_t t on a.pro = t.pro and t.estado is true
                inner join lab_aspira_dias d on d.pro = a.pro and d.estado is true
                inner join hc_paciente p on p.dni = r.dni
                left join lab_aspira_dias d1 on d1.pro = d.pro_c and d1.estado is true
                left join lab_aspira a1 on a1.pro = d1.pro and a1.estado is true
                left join hc_reprod r1 on r1.id = a1.rep
                left join hc_paciente p1 on p1.dni = r1.dni
                where r.estado = true and (r.des_dia >= 1 or r.h_tra <> '')$between
                group by r.id, d.pro, a1.pro, r.p_fiv, r.p_icsi, r1.p_fiv, r1.p_icsi,a.pro,t.beta,p.dni,p1.dni,r1.med,r1.f_asp");
            $rPaciBet->execute(); ?>
            <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                <?php print('<small><b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'<b>, Total Registros: </b>'.$rPaciBet->rowCount().'</small>'); ?>
            </h5>
            <div class="card-body mx-auto">
                <?php
                $item = 1;
                $var0 = $var1 = $var01 = $var2 = $var3 = $var23 = $var4 = $var5 = $var45 = $var6 = $var7 = $var67 = $var8 = $var9 = $var89 = 0;
                $repo1='fiv';
                $repo2='icsi';
                $repo3='nofivicsi';
                $estado1='0';
                $estado2='1';
                $estado3='2';
                $estado4='3';
                $estado5='4';

                while ($pacibet = $rPaciBet->fetch(PDO::FETCH_ASSOC)) {
                    if (strpos($pacibet['pago_extras'], "TRANSFERENCIA FRESCO") !== false) {
                        switch ($pacibet['beta']) {
                            case '0':
                                if ($pacibet['p_fiv'] >= 1) { $var0++; }
                                elseif ($pacibet['p_icsi'] >= 1) { $var1++; }
                                else { $var01++; }
                                break;
                            case '1':
                                if ($pacibet['p_fiv'] >= 1) { $var2++; }
                                elseif ($pacibet['p_icsi'] >= 1) { $var3++; }
                                else { $var23++; }
                                break;
                            case '2':
                                if ($pacibet['p_fiv'] >= 1) { $var4++; }
                                elseif ($pacibet['p_icsi'] >= 1) { $var5++; }
                                else { $var45++; }
                                break;
                            case '3':
                                if ($pacibet['p_fiv'] >= 1) { $var6++; }
                                elseif ($pacibet['p_icsi'] >= 1) { $var7++; }
                                else { $var67++; }
                                break;
                            case '4':
                                if ($pacibet['p_fiv'] >= 1) { $var8++; }
                                elseif ($pacibet['p_icsi'] >= 1) { $var9++; }
                                else { $var89++; }
                                break;
                            default: break;
                        }
                    } else {
                        switch ($pacibet['beta']) {
                            case '0':
                                if ($pacibet['p_fiv1'] >= 1) { $var0++; }
                                elseif ($pacibet['p_icsi1'] >= 1) { $var1++; }
                                else { $var01++; }
                                break;
                            case '1':
                                if ($pacibet['p_fiv1'] >= 1) { $var2++; }
                                elseif ($pacibet['p_icsi1'] >= 1) { $var3++; }
                                else { $var23++; }
                                break;
                            case '2':
                                if ($pacibet['p_fiv1'] >= 1) { $var4++; }
                                elseif ($pacibet['p_icsi1'] >= 1) { $var5++; }
                                else { $var45++; }
                                break;
                            case '3':
                                if ($pacibet['p_fiv1'] >= 1) { $var6++; }
                                elseif ($pacibet['p_icsi1'] >= 1) { $var7++; }
                                else { $var67++; }
                                break;
                            case '4':
                                if ($pacibet['p_fiv1'] >= 1) { $var8++; }
                                elseif ($pacibet['p_icsi1'] >= 1) { $var9++; }
                                else { $var89++; }
                                break;
                            default: break;
                        }
                    }
                } ?>
                <table class="table table-responsive table-bordered align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th>Item</th>
                            <th class="text-center align-middle"><b>Betas</b></th>
                            <th class="text-center align-middle"><b>FIV</b></th>
                            <th class="text-center align-middle"><b><?php print($_ENV["VAR_ICSI"]); ?></b></th>
                            <th class="text-center align-middle"><b>Sin marcar</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center align-middle">1</td>
                            <td>Pendientes</td>
                            <?php print("
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo1&estado=$estado1$url' target='_blank'>$var0</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo2&estado=$estado1$url' target='_blank'>$var1</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo3&estado=$estado1$url' target='_blank'>$var01</a></td>"); ?>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">2</td>
                            <td>Positivos</td>
                            <?php print("
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo1&estado=$estado2$url' target='_blank'>$var2</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo2&estado=$estado2$url' target='_blank'>$var3</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo3&estado=$estado2$url' target='_blank'>$var23</a></td>"); ?>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">3</td>
                            <td>Negativos</td>
                            <?php print("
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo1&estado=$estado3$url' target='_blank'>$var4</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo2&estado=$estado3$url' target='_blank'>$var5</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo3&estado=$estado3$url' target='_blank'>$var45</a></td>"); ?>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">4</td>
                            <td>Bioquímicos</td>
                            <?php print("
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo1&estado=$estado4$url' target='_blank'>$var6</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo2&estado=$estado4$url' target='_blank'>$var7</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo3&estado=$estado4$url' target='_blank'>$var67</a></td>"); ?>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">5</td>
                            <td>Abortos</td>
                            <?php print("
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo1&estado=$estado5$url' target='_blank'>$var8</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo2&estado=$estado5$url' target='_blank'>$var9</a></td>
                                <td class='text-center'><a href='repo_03_detalle.php?repo=$repo3&estado=$estado5$url' target='_blank'>$var89</a></td>"); ?>
                        </tr>
                        <tr>
                            <td></td>
                            <th class="text-right">Total</th>
                            <?php print("
                                <th class='text-center'>".($var0+$var2+$var4+$var6+$var8)."</th>
                                <th class='text-center'>".($var1+$var3+$var5+$var7+$var9)."</th>
                                <th class='text-center'>".($var01+$var23+$var45+$var67+$var89)."</th>"); ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $("#guardar").on("click", function () {
                $("#modal_editar").modal('show')
            });
        });
    </script>
</body>
</html>