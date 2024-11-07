<!DOCTYPE HTML>
<html>
<head>
    <?php
       include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <?php require ('_includes/repolab_menu.php'); ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Reportes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reporte FIV/ ICSI - Betas</li>
            </ol>
        </nav>
        <div class="card mb-3">
            <?php
                $fecha = $between = $med = $embins = $ovo = $ini = $fin = $ini_fivicsi = $fin_fivicsi = $tipo_transferencia = "";

                if (isset($_POST) && !empty($_POST)) {
                    if (isset($_POST["fecha"]) && !empty($_POST["fecha"])) {
                        $fecha = $_POST["fecha"];
                        switch ($fecha) {
                            case 'ftra':
                                if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                                    $ini = $_POST['ini'];
                                    $fin = $_POST['fin'];
                                    $between = " and (
                                        (r.f_tra is not null and CAST(r.f_tra as date) between '$ini' and '$fin') or
                                        (r.f_tra is null and CAST(r.f_iny as date) between '$ini' and '$fin'))";
                                }

                                break;
                            case 'fasp':
                                if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                                    $ini = $_POST['ini'];
                                    $fin = $_POST['fin'];
                                    $between = " and (
                                        (r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and CAST(r.f_asp as date) between '$ini' and '$fin') or
                                        (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and CAST(r1.f_asp as date) between '$ini' and '$fin'))";
                                }

                                break;
                            default: break;
                        }
                    }

                    if (isset($_POST["edad"]) && !empty($_POST["edad"])) {
                        switch ($_POST["edad"]) {
                            case 'edad_paciente':
                                if (isset($_POST["desde"]) && !empty($_POST["desde"]) && isset($_POST["hasta"]) && !empty($_POST["hasta"])) {
                                    $desde = $_POST['desde']*365;
                                    $hasta = $_POST['hasta']*365;
                                    $between .= " and datediff(now(), p.fnac) between $desde and $hasta";
                                }

                                break;
                            case 'edad_ovulo':
                                if (isset($_POST["desde"]) && !empty($_POST["desde"]) && isset($_POST["hasta"]) && !empty($_POST["hasta"])) {
                                    $desde = $_POST['desde']*365;
                                    $hasta = $_POST['hasta']*365;
                                    $between .= " and (
                                        (r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and datediff(r.f_asp, p.fnac) between $desde and $hasta) or
                                        (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and datediff(r1.f_asp, p.fnac) between $desde and $hasta))";
                                }

                                break;
                            
                            default: break;
                        }
                    }

                    if (isset($_POST["tipo_transferencia"]) && !empty($_POST["tipo_transferencia"])) {
                        $tipo_transferencia = $_POST['tipo_transferencia'];

                        switch ($_POST["tipo_transferencia"]) {
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
                    }

                    if (isset($_POST["embins"]) && !empty($_POST["embins"])) {
                        $embins = $_POST['embins'];
                        $between .= " and ((r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and a.emb0 = '$embins') or (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and a1.emb0 = '$embins'))";
                    }

                    if (isset($_POST["ovo"]) && !empty($_POST["ovo"])) {
                        $ovo = $_POST['ovo'];
                        $between .= " and ((r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and a.o_ovo = '$ovo') or (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and a1.o_ovo = '$ovo'))";
                    }

                    if (isset($_POST["ngs"]) && !empty($_POST["ngs"])) {
                        $ngs = $_POST['ngs'];

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
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="ini" class="">Desde</label>
                            <div>
                                <input class="form-control form-control-sm" name="ini" type="date" value="<?php echo $ini; ?>" id="ini">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="fin" class="">Hasta</label>
                            <div>
                                <input class="form-control form-control-sm" name="fin" type="date" value="<?php echo $fin; ?>" id="fin">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="edad" id="exampleRadios3" value="edad_paciente" <?php ($_POST['edad']=='edad_paciente' ? print('checked') : '') ?>>
                            <label class="form-check-label" for="exampleRadios3">Edad de Paciente</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="edad" id="exampleRadios4" value="edad_ovulo" <?php ($_POST['edad']=='edad_ovulo' ? print('checked') : '') ?>>
                            <label class="form-check-label" for="exampleRadios4">Edad del Óvulo</label>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-1">
                            <label for="desde" class="">Desde</label>
                            <div>
                                <input class="form-control form-control-sm" name="desde" type="number" value="<?php echo $_POST['desde']; ?>" id="desde">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-1">
                            <label for="hasta" class="">Hasta</label>
                            <div>
                                <input class="form-control form-control-sm" name="hasta" type="number" value="<?php echo $_POST['hasta']; ?>" id="hasta">
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Médico</label>
                            <select name='med' class="form-control form-control-sm">
                                <option value='' >todos</option>
                                <option value='mvelit' <?php if($med == "mvelit") {print("selected");} ?> >mvelit</option>
                                <option value='eescudero' <?php if($med == "eescudero") {print("selected");} ?> >eescudero</option>
                                <option value='mascenzo' <?php if($med == "mascenzo") {print("selected");} ?> >mascenzo</option>
                                <option value='cbonomini' <?php if($med == "cbonomini") {print("selected");} ?> >cbonomini</option>
                                <option value='tacna' <?php if($med == "tacna") {print("selected");} ?> >tacna</option>
                                <option value='cosorio' <?php if($med == "cosorio") {print("selected");} ?> >cosorio</option>
                                <option value='lab' <?php if($med == "lab") {print("selected");} ?> >lab</option>
                                <option value='rbozzo' <?php if($med == "rbozzo") {print("selected");} ?> >rbozzo</option>
                                <option value='apuertas' <?php if($med == "apuertas") {print("selected");} ?> >apuertas</option>
                                <option value='jolivas' <?php if($med == "jolivas") {print("selected");} ?> >jolivas</option>
                                <option value='humanidad' <?php if($med == "humanidad") {print("selected");} ?> >humanidad</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Emb. Inseminación</label>
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
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Origen Ovocitos</label>
                            <select name='ovo' class="form-control form-control-sm">
                                <option value='' >todos</option>
                                <option value='fresco' <?php if($ovo == "fresco") {print("selected");} ?> >fresco</option>
                                <option value='vitrificado' <?php if($ovo == "vitrificado") {print("selected");} ?>>vitrificado</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Tipo de Transferencia</label>
                            <select name='tipo_transferencia' class="form-control form-control-sm">
                                <option value='' >todos</option>
                                <option value='fresca' <?php if($tipo_transferencia == "fresca") {print("selected");} ?> >fresca</option>
                                <option value='descongelada' <?php if($tipo_transferencia == "descongelada") {print("selected");} ?>>descongelada</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">NGS</label>
                            <select name='ngs' class="form-control form-control-sm">
                                <option value='' >todos</option>
                                <option value='si' <?php if($ngs == "si") {print("selected");} ?>>si</option>
                                <option value='no' <?php if($ngs == "no") {print("selected");} ?>>no</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
                    , case when r.f_tra = '1899-12-30' then r.f_iny else r.f_tra end fectra
                    , coalesce(r.f_asp, '') fecasp
                    , coalesce(r1.f_asp, '') fecasp1
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
                    group by r.id, d.pro, a1.pro, r.p_fiv, r.p_icsi, r1.p_fiv, r1.p_icsi");
                $rPaciBet->execute(); ?>
                <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                    <?php
                    print('
                        <small><b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'
                        <b>, Total Registros: </b>'.$rPaciBet->rowCount().'
                        <b>, Descargar: </b>
                        <a href="#" onclick="tableToExcel(\'repo_pacientes\', \'pacientes\')" class="ui-btn ui-mini ui-btn-inline">
                            <img src="_images/excel.png" height="18" width="18" alt="icon name">
                        </a>
                        <b>, Resumen: </b>
                        <a href="#" class="navbar-brand" id="guardar"><img src="_libraries/open-iconic/svg/layers.svg" height="18" width="18" alt="icon name"></a>
                        </small>'); ?>
                </h5>
                <?php
                print('
                    <table class="table table-responsive table-bordered align-middle" style="height:40vh; margin-bottom: 0 !important;">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">Item</th>
                                <th width="25%" class="text-center align-middle">Procedimiento</th>
                                <th width="10%" class="text-center align-middle">Protocolo</th>
                                <th width="10%" class="text-center align-middle">DNI</th>
                                <th width="10%" class="text-center align-middle">Apellidos y Nombres</th>
                                <th width="30%" class="text-center align-middle">F.Transferencia</th>
                                <th width="30%" class="text-center align-middle">F.Descongelación/ F.Aspiración</th>
                                <th width="10%" class="text-center align-middle">Médico</th>
                                <th width="10%" class="text-center align-middle">FIV</th>
                                <th width="10%" class="text-center align-middle">'.$_ENV["VAR_ICSI"].'</th>
                                <th width="10%" class="text-center align-middle">Origen Ovocito</th>
                                <!-- <th width="10%" class="text-center align-middle">Emb. Inseminación</th> -->
                                <th width="10%" class="text-center align-middle">Beta</th>
                            </tr>
                        </thead>
                        <tbody>');
                $item = 1;
                $var0 = $var1 = $var01 = $var2 = $var3 = $var23 = $var4 = $var5 = $var45 = $var6 = $var7 = $var67 = $var8 = $var9 = $var89 = 0;

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
                    
                    print("
                    <tr>
                        <td width='5%' rowspan='2' class='text-center'>".$item++."</td>
                        <td width='25%'>");
                        if ($pacibet['p_dtri'] >= 1) { echo "Dual Trigger<br>"; }
                        if ($pacibet['p_cic'] >= 1) { echo "Ciclo Natural<br>"; }
                        if ($pacibet['p_fiv'] >= 1) { echo "FIV<br>"; }
                        if ($pacibet['p_icsi'] >= 1) { echo $_ENV["VAR_ICSI"] . "<br>"; }
                        if ($pacibet['p_od'] <> '') { echo "OD Fresco<br>"; }
                        if ($pacibet['p_cri'] >= 1) { echo "Crio Ovulos<br>"; }
                        if ($pacibet['p_iiu'] >= 1) { echo "IIU<br>"; }
                        if ($pacibet['p_don'] == 1) { echo "Donación Fresco<br>"; }
                        if ($pacibet['des_don'] == null && $pacibet['des_dia'] >= 1) { echo "TED<br>"; }
                        if ($pacibet['des_don'] == null && $pacibet['des_dia'] === 0) { echo "<small>Descongelación Ovulos Propios</small><br>"; }
                        if ($pacibet['des_don'] <> null && $pacibet['des_dia'] >= 1) { echo "EMBRIODONACIÓN<br>"; }
                        if ($pacibet['des_don'] <> null && $pacibet['des_dia'] === 0 && $pacibet['id']<>2192) { echo "<small>Descongelación Ovulos Donados</small><br>"; }
                        print('Extras: '.$pacibet['pago_extras']);
                        print('
                        </td>
                        <td width="10%" class="text-center">'.$pacibet['pro'].'</td>
                        <td width="10%" class="text-center">'.$pacibet['dni'].'</td>
                        <td width="10%">'.mb_strtoupper($pacibet['ape']).' '.mb_strtoupper($pacibet['nom']).' ('.date_diff(date_create($pacibet['fnac']), date_create('today'))->y.')</td>
                        <td width="20%" class="text-center">'.(empty($pacibet['fectra']) ? '' : date("d-m-Y", strtotime($pacibet['fectra']))).'</td>
                        <td width="20%" class="text-center">'.(empty($pacibet['fecasp']) ? '' : date("d-m-Y", strtotime($pacibet['fecasp']))).'</td>
                        <td width="10%" class="text-center">'.$pacibet['med'].'</td>');
                        if ($pacibet['p_fiv'] >= 1) { echo '<td width="10%" class="text-center">FIV</td>'; } else { print('<td width="10%" class="text-center"></td>'); }
                        if ($pacibet['p_icsi'] >= 1) { echo '<td width="10%" class="text-center">'.$_ENV["VAR_ICSI"].'</td>'; } else { print('<td width="10%" class="text-center"></td>'); }
                        print('
                        <td width="10%" class="text-center">'.$pacibet['o_ovo'].'</td>
                        <!-- <td width="10%" class="text-center">'.$pacibet['emb0'].'</td> --><td class="text-center">');
                        switch ($pacibet['beta']) {
                            case '0': print('Pendiente'); break;
                            case '1': print('Positivo'); break;
                            case '2': print('Negativo'); break;
                            case '3': print('Bioquímico'); break;
                            case '4': print('Aborto'); break;
                            default: break;
                        }
                    print('
                        </td>
                    </tr>
                    <tr style="color: #9692af;">
                        <td width="25%"></td>
                        <td width="10%" class="text-center">'.$pacibet['pro1'].'</td>
                        <td width="10%" class="text-center">'.$pacibet['dni1'].'</td>
                        <td width="10%">'.mb_strtoupper($pacibet['ape1']).' '.mb_strtoupper($pacibet['nom1']).'</td>
                        <td width="20%" class="text-center"></td>
                        <td width="20%" class="text-center">'.(empty($pacibet['fecasp1']) ? '' : date("d-m-Y", strtotime($pacibet['fecasp1']))).'</td>
                        <td width="10%" class="text-center">'.$pacibet['med1'].'</td>');
                        if ($pacibet['p_fiv1'] >= 1) { echo '<td width="10%" class="text-center">FIV</td>'; } else { print('<td width="10%" class="text-center"></td>'); }
                        if ($pacibet['p_icsi1'] >= 1) { echo '<td width="10%" class="text-center">'.$_ENV["VAR_ICSI"].'</td>'; } else { print('<td width="10%" class="text-center"></td>'); }
                        print('
                        <td width="10%" class="text-center">'.$pacibet['o_ovo1'].'</td>
                        <!-- <td width="10%" class="text-center">'.$pacibet['emb01'].'</td> -->
                        <td></td>');
                    print('</tr>');
                }

                print('
                </tbody>
                    </table>'); ?>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" id="modal_editar">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Resumen: FIV/ ICSI - Betas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body mx-auto">
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
                                        <td class='text-center'>$var0</td>
                                        <td class='text-center'>$var1</td>
                                        <td class='text-center'>$var01</td>"); ?>
                                </tr>
                                <tr>
                                    <td class="text-center align-middle">2</td>
                                    <td>Positivos</td>
                                    <?php print("
                                        <td class='text-center'>$var2</td>
                                        <td class='text-center'>$var3</td>
                                        <td class='text-center'>$var23</td>"); ?>
                                </tr>
                                <tr>
                                    <td class="text-center align-middle">3</td>
                                    <td>Negativos</td>
                                    <?php print("
                                        <td class='text-center'>$var4</td>
                                        <td class='text-center'>$var5</td>
                                        <td class='text-center'>$var45</td>"); ?>
                                </tr>
                                <tr>
                                    <td class="text-center align-middle">4</td>
                                    <td>Bioquímicos</td>
                                    <?php print("
                                        <td class='text-center'>$var6</td>
                                        <td class='text-center'>$var7</td>
                                        <td class='text-center'>$var67</td>"); ?>
                                </tr>
                                <tr>
                                    <td class="text-center align-middle">5</td>
                                    <td>Abortos</td>
                                    <?php print("
                                        <td class='text-center'>$var8</td>
                                        <td class='text-center'>$var9</td>
                                        <td class='text-center'>$var89</td>"); ?>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="modal-btn-no">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $("#guardar").on("click", function () {
                $("#modal_editar").modal('show')
            });
        });
    </script>
</body>
</html>