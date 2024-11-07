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
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <title>Inmater Clínica de Fertilidad | Reporte de Fecundacion</title>
    <script type="text/javascript">
    function PrintElem(elem) {
        var data = $(elem).html();
        var mywindow = window.open('', 'Imprimir', 'height=600,width=800');
        mywindow.document.write('<html><head><title>Imprimir</title>');
        mywindow.document.write(
            '<style> @page {margin: 0px 0px 0px 5px;} table {border-collapse: collapse;font-size:10px;} .table-stripe td {border: 1px solid black;} .tablamas2 td {border: 1px solid white;} .mas2 {display: block !important;} .noVer, .ui-table-cell-label {display: none;} a:link {pointer-events: none; cursor: default;}</style>'
        );
        mywindow.document.write("</head><body><p style='align: center'>Reporte Fecundación In Vitro</p>");
        mywindow.document.write(data);
        mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
        mywindow.document.write('</body></html>');
        return true;
    }
    </script>
</head>

<body>
    <div class="container">
        <?php require ('_includes/repolab_menu.php'); ?>
        <?php
        if ($_SESSION['role'] == "9") {
            $between = $url = $med = $embins = $ovo = $ini = $fin = "";
            if (isset($_POST) && !empty($_POST)) {
                if ( isset($_POST["edesde"]) && !empty($_POST["edesde"]) && isset($_POST["ehasta"]) && !empty($_POST["ehasta"]) ) {
                    $edesde = $_POST['edesde']*365;
                    $ehasta = $_POST['ehasta']*365;
                    $between = " and datediff(lab_aspira.fec, hc_paciente.fnac) between $edesde and $ehasta";
                    $url = "?edesde=$edesde&ehasta=$ehasta";
                }
                if ( isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
                    $ini = $_POST['ini'];
                    $fin = $_POST['fin'];
                    $between = " and CAST(lab_aspira.fec as date) between '$ini' and '$fin'";
                    // $url = "?ini=$ini&fin=$fin";
                    if ($url == "") {
                        $url .= "?ini=$ini&fin=$fin";
                    } else {
                        $url .= "&ini=$ini&fin=$fin";
                    }
                }
                if (isset($_POST["med"]) && !empty($_POST["med"])) {
                    $med = $_POST['med'];
                    $between.= " and hc_reprod.med = '$med'";
                    if ($url == "") {
                        $url .= "?med=$med";
                    } else {
                        $url .= "&med=$med";
                    }
                    // $url .= "?ini=$ini&fin=$fin&med=$med";
                }
                if (isset($_POST["embins"]) && !empty($_POST["embins"])) {
                    $embins = $_POST['embins'];
                    $between.= " and lab_aspira.emb0 = $embins";
                    if ($url == "") {
                        $url .= "?embins=$embins";
                    } else {
                        $url .= "&embins=$embins";
                    }
                }
                if (isset($_POST["ovo"]) && !empty($_POST["ovo"])) {
                    $ovo = $_POST['ovo'];
                    $between.= " and unaccent(lab_aspira.o_ovo) ilike '%$ovo%'";
                    if ($url == "") {
                        $url .= "?ovo=$ovo";
                    } else {
                        $url .= "&ovo=$ovo";
                    }
                }
                if (isset($_POST["tipa"]) && !empty($_POST["tipa"])) {
                    $tipa = $_POST['tipa'];
                    $between.= " and lab_aspira.tip = '$tipa'";
                    if ($url == "") {
                        $url .= "?tipa=$tipa";
                    } else {
                        $url .= "&tipa=$tipa";
                    }
                }
                if (isset($_POST["inc"]) && $_POST["inc"] != "") {
                    $inc = $_POST['inc'];
                    $between.= " and lab_aspira.inc = $inc";
                    if ($url == "") {
                        $url .= "?inc=$inc";
                    } else {
                        $url .= "&inc=$inc";
                    }
                }
            }
            if (empty($between)) {
                $between = " and 1 = 2";
            }
            // 
            $consulta = $db->prepare("SELECT id, nom from lab_user");
            $consulta->execute();
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $datos = $consulta->fetchAll();
            // 
            $item = $ambos =  $fiv = $icsi = $recep = $crio = $ted = $dgp = $piiu = $emb = $od = $don = $tedngs = 0;
            $aspirado = $fivaspira = $icsiaspira = 0;
            $fecundado = $fivfecun = $icsifecun = 0;
            $nofecundado = $fivnofecun = $icsinofecun = 0;
            $triploide = $fivtriploide = $icsitriploide = 0;
            $citolizado = $citofiv = $citoicsi = 0;
            $inmaduro = $fivinma = $icsiinma = 0;
            $meta1 = $fivmeta1 = $icsimeta1 = 0;
            $vg = $fivvg = $icsivg = 0;
            $atresico = $atrefiv = $atreicsi = 0;
            $betas = $fivbeta = $icsibeta = 0;
            $fivbetapen = $fivbetapos = $fivbetaneg = $fivbetabio = $fivbetaabo = 0;
            $icsibetapen = $icsibetapos = $icsibetaneg = $icsibetabio = $icsibetaabo = 0;
            $totalfivd5 = $totalicsid5 = $totalfivd6 = $totalicsid6 = 0;
            $blasd6fiv = $blasd5fiv = $blasterd5fiv = $blasterd6fiv = $blascavid5fiv = $blascavid6fiv = $blasexpd5fiv = $blasexpd6fiv = $blasinid5fiv = $blasinid6fiv = $blashatd5fiv = $blashatd6fiv = 0;
            $blasd6icsi = $blasd5icsi = $blasterd5icsi = $blasterd6icsi = $blascavid5icsi = $blascavid6icsi = $blasexpd5icsi = $blasexpd6icsi = $blasinid5icsi = $blasinid6icsi = $blashatd5icsi = $blashatd6icsi = 0;
            $fecha_inicio_band = $pos = true;
            $rPaciDet = $db->prepare("SELECT
                hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                , count( lab_aspira_dias.pro ) aspirado
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' then true end ) fecundado
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'N' and lab_aspira_dias.d1c_pol in ('0', '1', '2') and lab_aspira_dias.d1pron in ('0', '1', '2') then true end ) nofecundado
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'N' and (lab_aspira_dias.d1c_pol in ('3', '4', 'MULT') or lab_aspira_dias.d1pron in ('3', '4', 'MULT')) then true end ) triploide
                , count( case when lab_aspira_dias.d0est = 'MI' or lab_aspira_dias.d1est = 'MI' or lab_aspira_dias.d0est = 'VG' or lab_aspira_dias.d1est = 'VG' then true end ) inmaduro
                , count( case when lab_aspira_dias.d0est = 'MI' or lab_aspira_dias.d1est = 'MI' then true end ) meta1
                , count( case when (lab_aspira_dias.d0est = 'VG' or lab_aspira_dias.d1est = 'VG') then true end ) vg
                , count( case when (lab_aspira_dias.d0est = 'CT' or lab_aspira_dias.d1est = 'CT') then true end ) citolizado
                , count( case when (lab_aspira_dias.d0est = 'ATR' or lab_aspira_dias.d1est = 'ATR') then true end ) atresico
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' then true end ) fecundado
                , count( case when lab_aspira_dias.d4f_cic = 'O' then true end ) totd5
                , count( case when lab_aspira_dias.d5f_cic = 'O' then true end ) totd6
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BT' then true end ) blasterd5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BT' then true end ) blasterd6
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' then true end ) blascavid5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BC' then true end ) blascavid6
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BE' then true end ) blasexpd5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BE' then true end ) blasexpd6
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BHI' then true end ) blasinid5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BHI' then true end ) blasinid6
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BH' then true end ) blashatd5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BH' then true end ) blashatd6
                from hc_reprod
                inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
                inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'
                inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true$between
                left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
                where hc_reprod.estado = true and hc_reprod.p_fiv >= 1 or hc_reprod.p_icsi >= 1 
                group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                order by lab_aspira.fec asc");
            $rPaciDet->execute();
            while ($pacidet = $rPaciDet->fetch(PDO::FETCH_ASSOC)) {
                $aspirado+=$pacidet['aspirado'];
                $fecundado+=$pacidet['fecundado'];
                $nofecundado+=$pacidet['nofecundado'];
                $citolizado+=$pacidet['citolizado'];
                $inmaduro+=$pacidet['inmaduro'];
                $atresico+=$pacidet['atresico'];
                if ($pacidet['p_fiv'] == 1 and $pacidet['p_icsi'] == 1) {
                    $totalfivd5+=$pacidet["totd5"];
                    $totalfivd6+=$pacidet["totd6"];
                    $blasd5fiv+=$pacidet['blasterd5']+$pacidet['blascavid5']+$pacidet['blasexpd5']+$pacidet['blasinid5']+$pacidet['blashatd5'];
                    $blasd6fiv+=$pacidet['blasterd6']+$pacidet['blascavid6']+$pacidet['blasexpd6']+$pacidet['blasinid6']+$pacidet['blashatd6'];
                    $blasterd5fiv+=$pacidet['blasterd5'];
                    $blasterd6fiv+=$pacidet['blasterd6'];
                    $blascavid5fiv+=$pacidet['blascavid5'];
                    $blascavid6fiv+=$pacidet['blascavid6'];
                    $blasexpd5fiv+=$pacidet['blasexpd5'];
                    $blasexpd6fiv+=$pacidet['blasexpd6'];
                    $blasinid5fiv+=$pacidet['blasinid5'];
                    $blasinid6fiv+=$pacidet['blasinid6'];
                    $blashatd5fiv+=$pacidet['blashatd5'];
                    $blashatd6fiv+=$pacidet['blashatd6'];
                    $fivaspira+=$pacidet['aspirado'];
                    $fivfecun+=$pacidet['fecundado'];
                    $fivnofecun+=$pacidet['nofecundado'];
                    $fivtriploide+=$pacidet['triploide'];
                    $citofiv+=$pacidet['citolizado'];
                    $fivinma+=$pacidet['inmaduro'];
                    $fivmeta1+=$pacidet['meta1'];
                    $fivvg+=$pacidet['vg'];
                    $atrefiv+=$pacidet['atresico'];
                } else if ($pacidet['p_fiv'] == 1) {
                    $totalfivd5+=$pacidet["totd5"];
                    $totalfivd6+=$pacidet["totd6"];
                    $blasd5fiv+=$pacidet['blasterd5']+$pacidet['blascavid5']+$pacidet['blasexpd5']+$pacidet['blasinid5']+$pacidet['blashatd5'];
                    $blasd6fiv+=$pacidet['blasterd6']+$pacidet['blascavid6']+$pacidet['blasexpd6']+$pacidet['blasinid6']+$pacidet['blashatd6'];
                    $blasterd5fiv+=$pacidet['blasterd5'];
                    $blasterd6fiv+=$pacidet['blasterd6'];
                    $blascavid5fiv+=$pacidet['blascavid5'];
                    $blascavid6fiv+=$pacidet['blascavid6'];
                    $blasexpd5fiv+=$pacidet['blasexpd5'];
                    $blasexpd6fiv+=$pacidet['blasexpd6'];
                    $blasinid5fiv+=$pacidet['blasinid5'];
                    $blasinid6fiv+=$pacidet['blasinid6'];
                    $blashatd5fiv+=$pacidet['blashatd5'];
                    $blashatd6fiv+=$pacidet['blashatd6'];
                    $fivaspira+=$pacidet['aspirado'];
                    $fivfecun+=$pacidet['fecundado'];
                    $fivnofecun+=$pacidet['nofecundado'];
                    $fivtriploide+=$pacidet['triploide'];
                    $citofiv+=$pacidet['citolizado'];
                    $fivinma+=$pacidet['inmaduro'];
                    $fivmeta1+=$pacidet['meta1'];
                    $fivvg+=$pacidet['vg'];
                    $atrefiv+=$pacidet['atresico'];
                } else if ($pacidet['p_icsi'] == 1) {
                    $totalicsid5+=$pacidet["totd5"];
                    $totalicsid6+=$pacidet["totd6"];
                    $blasd5icsi+=$pacidet['blasterd5']+$pacidet['blascavid5']+$pacidet['blasexpd5']+$pacidet['blasinid5']+$pacidet['blashatd5'];
                    $blasd6icsi+=$pacidet['blasterd6']+$pacidet['blascavid6']+$pacidet['blasexpd6']+$pacidet['blasinid6']+$pacidet['blashatd6'];
                    $blasterd5icsi+=$pacidet['blasterd5'];
                    $blasterd6icsi+=$pacidet['blasterd6'];
                    $blascavid5icsi+=$pacidet['blascavid5'];
                    $blascavid6icsi+=$pacidet['blascavid6'];
                    $blasexpd5icsi+=$pacidet['blasexpd5'];
                    $blasexpd6icsi+=$pacidet['blasexpd6'];
                    $blasinid5icsi+=$pacidet['blasinid5'];
                    $blasinid6icsi+=$pacidet['blasinid6'];
                    $blashatd5icsi+=$pacidet['blashatd5'];
                    $blashatd6icsi+=$pacidet['blashatd6'];
                    $icsiaspira+=$pacidet['aspirado'];
                    $icsifecun+=$pacidet['fecundado'];
                    $icsinofecun+=$pacidet['nofecundado'];
                    $icsitriploide+=$pacidet['triploide'];
                    $citoicsi+=$pacidet['citolizado'];
                    $icsiinma+=$pacidet['inmaduro'];
                    $icsimeta1+=$pacidet['meta1'];
                    $icsivg+=$pacidet['vg'];
                    $atreicsi+=$pacidet['atresico'];
                }
            }
        ?>
        <div class="card mb-3" id="imprime">
            <h5 class="card-header">Filtros</h5>
            <div class="card-body">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Edad cumplida:</label>
                            <div>
                                <input class="form-control" name="edesde" type="number"
                                    value="<?php echo $_POST['edesde']??''; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Y menor a:</label>
                            <div>
                                <input class="form-control" name="ehasta" type="number"
                                    value="<?php echo $_POST['ehasta']??''; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Mostrar Desde</label>
                            <div>
                                <input class="form-control" name="ini" type="date" value="<?php echo $_POST['ini']??''; ?>"
                                    id="example-datetime-local-input">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Hasta</label>
                            <div>
                                <input class="form-control" name="fin" type="date" value="<?php echo $_POST['fin']??''; ?>"
                                    id="example-datetime-local-input">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Médico</label>
                            <?php
                            $rMed = $db->prepare("SELECT codigo from man_medico where estado=1 order by codigo asc;");
                            $rMed->execute();
                            $medd = $rMed->fetchAll(PDO::FETCH_ASSOC);?>
                            <select name='med' class="form-control">
                                <option value='' >todos</option>
                                <?php foreach ($medd as $medico) {
                                    $selected = ""; 
                                    if ($med == $medico["codigo"]) {
                                        $selected = "selected";
                                    }
                                    echo '<option value="'.$medico["codigo"].'" '.$selected.'>'.$medico["codigo"].'</option>';
                                } ?>
                                <option value='tacna' <?php if($med == "tacna") print("selected"); ?> >tacna</option>
                                <option value='cosorio' <?php if($med == "cosorio") print("selected"); ?> >cosorio</option>
                                <option value='lab' <?php if($med == "lab") print("selected"); ?> >lab</option>
                                <option value='humanidad' <?php if($med == "humanidad") print("selected"); ?> >humanidad</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Emb. Inseminación</label>
                            <select name='embins' class="form-control">
                                <option value="">todos</option>
                                <?php
                                    $embinsi="";
                                    foreach ($datos as $row) {
                                        if ($embins == $row['id']) $embinsi="selected";
                                        else $embinsi="";
                                        print("<option value='".$row['id']."' $embinsi>".strtolower($row['nom'])."</option>");
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Origen Ovocitos</label>
                            <select name='ovo' class="form-control">
                                <option value=''>todos</option>
                                <option value='fresco' <?php if($ovo == "fresco") print("selected"); ?>>fresco</option>
                                <option value='vitrificado' <?php if($ovo == "vitrificado") print("selected"); ?>>
                                    vitrificado</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Tipo Paciente</label>
                            <select name='tipa' class="form-control">
                                <option value=''>todos</option>
                                <option value='P' <?php if($tipa == "P") print("selected"); ?>>paciente</option>
                                <option value='R' <?php if($tipa == "R") print("selected"); ?>>receptora</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Incubadora</label>
                            <select name='inc' class="form-control">
                                <option value="">ninguno</option>
                                <?php
                                    $data = $db->prepare("select codigo from lab_incubadora where estado=1 and dia0=1");
                                    $data->execute();
                                    while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                        print("<option value=".$info['codigo']);
                                    if ($inc == $info['codigo'])
                                        echo " selected";
                                    print(">".$info['codigo']."</option>");
                                } ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-3">
            <h5 class="card-header">Reporte Tasa de Fecundación</h5>
            <div class="card-body mx-auto">
                <?php
                if ($rPaciDet->rowCount() === 0) { ?>
                <h5>Debes indicar un filtro para buscar datos.</h5>
                <?php } else { ?>
                <table class="table table-responsive table-bordered align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle"><b>Item</b></th>
                            <th class="text-center align-middle"><b>Tasa de Fecundacion</b></th>
                            <th class="text-center align-middle"><b>FIV</b></th>
                            <th class="text-center align-middle"><b><?php print($_ENV["VAR_ICSI"]); ?></b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center align-middle">1</td>
                            <td>Óvulos Aspirados</td>
                            <td class='text-center'>
                                <?php
                                if ($fivaspira != 0) {
                                    print($fivaspira . " (" . (($fivfecun + $fivnofecun + $fivtriploide + $citofiv) + ($fivmeta1 + $fivvg) + $atrefiv) . " - " . number_format((((($fivfecun + $fivnofecun + $fivtriploide + $citofiv) + ($fivmeta1 + $fivvg) + $atrefiv) * 100 / $fivaspira)), 2) . "%)");
                                } else {
                                    print($fivaspira . " (0%)");
                                }
                                ?>
                            </td>
                            <td class='text-center'>
                                <?php
                                if ($icsiaspira != 0) {
                                    print($icsiaspira . " (" . (($icsifecun + $icsinofecun + $icsitriploide + $citoicsi) + ($icsimeta1 + $icsivg) + $atreicsi) . " - " . number_format((((($icsifecun + $icsinofecun + $icsitriploide + $citoicsi) + ($icsimeta1 + $icsivg) + $atreicsi) * 100 / $icsiaspira)), 2) . "%)");
                                } else {
                                    print($icsiaspira . " (0%)");
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">2</td>
                            <td>Óvulos Inseminados</td>
                            <td class='text-center'>
                                <?php
                                if ($fivaspira != 0) {
                                    print(($fivfecun + $fivnofecun + $fivtriploide + $citofiv) . " (" . number_format(($fivfecun + $fivnofecun + $fivtriploide + $citofiv) * 100 / ($fivaspira), 2) . "%)");
                                } else {
                                    print(($fivfecun + $fivnofecun + $fivtriploide + $citofiv) . " (0%)");
                                }
                                ?>
                            </td>
                            <td class='text-center'>
                                <?php
                                if ($icsiaspira != 0) {
                                    print(($icsifecun + $icsinofecun + $icsitriploide + $citoicsi) . " (" . number_format(($icsifecun + $icsinofecun + $icsitriploide + $citoicsi) * 100 / ($icsiaspira), 2) . "%)");
                                } else {
                                    print(($icsifecun + $icsinofecun + $icsitriploide + $citoicsi) . " (0%)");
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Óvulos Fecundados</td>
                            <td class='text-center'>
                                <?php
                                if (($fivfecun + $fivnofecun + $fivtriploide + $citofiv) != 0) {
                                    print("<a href='repo_fiv_fecun.php$url' target='_blank'>" . $fivfecun . " (" . number_format(($fivfecun) * 100 / ($fivfecun + $fivnofecun + $fivtriploide + $citofiv), 2) . "%)</a>");
                                } else {
                                    print("<a href='repo_fiv_fecun.php$url' target='_blank'>" . $fivfecun . " (0%)</a>");
                                }
                                ?>
                            </td>
                            <td class='text-center'>
                                <?php
                                if (($icsifecun + $icsinofecun + $icsitriploide + $citoicsi) != 0) {
                                    print("<a href='repo_icsi_fecun.php$url' target='_blank'>" . $icsifecun . " (" . number_format(($icsifecun) * 100 / ($icsifecun + $icsinofecun + $icsitriploide + $citoicsi), 2) . "%)</a>");
                                } else {
                                    print("<a href='repo_icsi_fecun.php$url' target='_blank'>" . $icsifecun . " (0%)</a>");
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Óvulos No Fecundados</td>
                            <?php
                            if (($fivfecun + $fivnofecun + $fivtriploide + $citofiv) != 0) {
                                print("<td class='text-center'><a href='repo_fiv_nofecun.php$url' target='_blank'>" . $fivnofecun . " (" . number_format(($fivnofecun) * 100 / ($fivfecun + $fivnofecun + $fivtriploide + $citofiv), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_fiv_nofecun.php$url' target='_blank'>" . $fivnofecun . " (0%)</a></td>");
                            }
                            ?>
                            <?php
                            if (($icsifecun + $icsinofecun + $icsitriploide + $citoicsi) != 0) {
                                print("<td class='text-center'><a href='repo_icsi_nofecun.php$url' target='_blank'>" . $icsinofecun . " (" . number_format(($icsinofecun) * 100 / ($icsifecun + $icsinofecun + $icsitriploide + $citoicsi), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_icsi_nofecun.php$url' target='_blank'>" . $icsinofecun . " (0%)</a></td>");
                            }
                            ?>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Triploides</td>
                            <?php
                            if (($fivfecun + $fivnofecun + $fivtriploide + $citofiv) != 0) {
                                print("<td class='text-center'><a href='repo_fiv_tripo.php$url' target='_blank'>" . $fivtriploide . " (" . number_format(($fivtriploide) * 100 / ($fivfecun + $fivnofecun + $fivtriploide + $citofiv), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_fiv_tripo.php$url' target='_blank'>" . $fivtriploide . " (0%)</a></td>");
                            }
                            ?>
                            <?php
                            if (($icsifecun + $icsinofecun + $icsitriploide + $citoicsi) != 0) {
                                print("<td class='text-center'><a href='repo_icsi_tripo.php$url' target='_blank'>" . $icsitriploide . " (" . number_format(($icsitriploide) * 100 / ($icsifecun + $icsinofecun + $icsitriploide + $citoicsi), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_icsi_tripo.php$url' target='_blank'>" . $icsitriploide . " (0%)</a></td>");
                            }
                            ?>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Citolizados</td>
                            <?php
                            if (($fivfecun + $fivnofecun + $fivtriploide + $citofiv) != 0) {
                                print("<td class='text-center'><a href='repo_fiv_estadio_cito.php$url' target='_blank'>" . $citofiv . " (" . number_format(($citofiv) * 100 / ($fivfecun + $fivnofecun + $fivtriploide + $citofiv), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_fiv_estadio_cito.php$url' target='_blank'>" . $citofiv . " (0%)</a></td>");
                            }
                            ?>
                            <?php
                            if (($icsifecun + $icsinofecun + $icsitriploide + $citoicsi) != 0) {
                                print("<td class='text-center'><a href='repo_icsi_estadio_cito.php$url' target='_blank'>" . $citoicsi . " (" . number_format(($citoicsi) * 100 / ($icsifecun + $icsinofecun + $icsitriploide + $citoicsi), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_icsi_estadio_cito.php$url' target='_blank'>" . $citoicsi . " (0%)</a></td>");
                            }
                            ?>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">3</td>
                            <td>Inmaduros</td>
                            <?php
                            if ($fivaspira != 0) {
                                print("<td class='text-center'>" . ($fivmeta1 + $fivvg) . " (" . number_format(($fivmeta1 + $fivvg) * 100 / ($fivaspira), 2) . "%)</td>");
                            } else {
                                print("<td class='text-center'>" . ($fivmeta1 + $fivvg) . " (0%)</td>");
                            }
                            ?>
                            <?php
                            if ($icsiaspira != 0) {
                                print("<td class='text-center'>" . ($icsimeta1 + $icsivg) . " (" . number_format(($icsimeta1 + $icsivg) * 100 / ($icsiaspira), 2) . "%)</td>");
                            } else {
                                print("<td class='text-center'>" . ($icsimeta1 + $icsivg) . " (0%)</td>");
                            }
                            ?>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Metafase 1</td>
                            <?php
                            if ($fivaspira != 0) {
                                print("<td class='text-center'><a href='repo_fiv_estadio_mi.php$url' target='_blank'>" . $fivmeta1 . " (" . number_format(($fivmeta1) * 100 / ($fivaspira), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_fiv_estadio_mi.php$url' target='_blank'>" . $fivmeta1 . " (0%)</a></td>");
                            }
                            ?>
                            <?php
                            if ($icsiaspira != 0) {
                                print("<td class='text-center'><a href='repo_icsi_estadio_mi.php$url' target='_blank'>" . $icsimeta1 . " (" . number_format(($icsimeta1) * 100 / ($icsiaspira), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_icsi_estadio_mi.php$url' target='_blank'>" . $icsimeta1 . " (0%)</a></td>");
                            }
                            ?>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Vesícula Germinativa</td>
                            <?php
                            if ($fivaspira != 0) {
                                print("<td class='text-center'><a href='repo_fiv_estadio_vg.php$url' target='_blank'>" . $fivvg . " (" . number_format(($fivvg) * 100 / ($fivaspira), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_fiv_estadio_vg.php$url' target='_blank'>" . $fivvg . " (0%)</a></td>");
                            }
                            ?>
                            <?php
                            if ($icsiaspira != 0) {
                                print("<td class='text-center'><a href='repo_icsi_estadio_vg.php$url' target='_blank'>" . $icsivg . " (" . number_format(($icsivg) * 100 / ($icsiaspira), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_icsi_estadio_vg.php$url' target='_blank'>" . $icsivg . " (0%)</a></td>");
                            }
                            ?>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">4</td>
                            <td>Atrésicos</td>
                            <?php
                            if ($fivaspira != 0) {
                                print("<td class='text-center'><a href='repo_fiv_estadio_atre.php$url' target='_blank'>" . $atrefiv . " (" . number_format(($atrefiv) * 100 / ($fivaspira), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_fiv_estadio_atre.php$url' target='_blank'>" . $atrefiv . " (0%)</a></td>");
                            }
                            ?>
                            <?php
                            if ($icsiaspira != 0) {
                                print("<td class='text-center'><a href='repo_icsi_estadio_atre.php$url' target='_blank'>" . $atreicsi . " (" . number_format(($atreicsi) * 100 / ($icsiaspira), 2) . "%)</a></td>");
                            } else {
                                print("<td class='text-center'><a href='repo_icsi_estadio_atre.php$url' target='_blank'>" . $atreicsi . " (0%)</a></td>");
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>