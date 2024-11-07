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
    <title>Inmater Clínica de Fertilidad | Reporte de Blastulacion</title>
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
            $between = $url = $med = $embins = $ovo = $tipa = $ini = $fin = $edesde = $ehasta = $inc = "";
            if (isset($_POST) && !empty($_POST)) {
                if ( isset($_POST["edesde"]) && !empty($_POST["edesde"]) && isset($_POST["ehasta"]) && !empty($_POST["ehasta"]) ) {
                    $edesde = $_POST['edesde']*365;
                    $ehasta = $_POST['ehasta']*365;
                    $between = " and datediff(lab_aspira.fec, hc_paciente.fnac) between $edesde and $ehasta";
                    $url = "?edesde=$edesde&ehasta=$ehasta";
                }
                if ( isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
                    print($ini); print($fin);
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
                    $between.= " and lab_aspira.inc1 = $inc";
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

                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d4f_cic = 'O' then true end ) totd5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5f_cic = 'O' then true end ) totd6

                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BT' then true end ) blasterd5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BT' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) blasterd6
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' then true end ) blascavid5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BC' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) blascavid6
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BE' then true end ) blasexpd5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BE' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) blasexpd6
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BHI' then true end ) blasinid5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BHI' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) blasinid6
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BH' then true end ) blashatd5
                , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BH' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) blashatd6
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
            // print_r("<p>".$rPaciDet->__toString()."</p>");
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
            <h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="true"
                aria-controls="collapseExample">Filtros</h5>
            <div class="card-body collapse show" id="collapseExample">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Edad cumplida:</label>
                            <div>
                                <input class="form-control" name="edesde" type="number"
                                    value="<?php echo $_POST['edesde'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Y menor a:</label>
                            <div>
                                <input class="form-control" name="ehasta" type="number"
                                    value="<?php echo $_POST['ehasta']??''; ?>">
                            </div>
                        </div>
                        <!-- mostrar desde hasta -->
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
                                    $data = $db->prepare("select codigo from lab_incubadora where estado=1 and dia1=1");
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
            <h5 class="card-header">Tasa de Blastulación</h5>
            <div class="card-body mx-auto">
                <?php
                if ($rPaciDet->rowCount() === 0) { ?>
                <h5>Debes indicar un filtro para buscar datos.</h5>
                <?php } else { ?>
                <table class="table table-responsive table-bordered align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">Item</th>
                            <th class="text-center align-middle">Tasa de Blastulación</th>
                            <th class="text-center align-middle" colspan="2">FIV</th>
                            <th class="text-center align-middle" colspan="2"><?php print($_ENV["VAR_ICSI"]); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ($url==""){
                                $url1="repo_blas_model_01.php?rep=";
                            } else {
                                $url1="repo_blas_model_01.php".$url."&rep=";
                            }
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class='text-center'>Día 5</td>
                            <td class='text-center'>Día 6</td>
                            <td class='text-center'>Día 5</td>
                            <td class='text-center'>Día 6</td>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">1</td>
                            <td class="">Total Embriones (obs)</td>
                            <?php print("<td class='text-center' colspan='2'>".($fivfecun)." (100%)</td>"); ?>
                            <?php // print("<td class='text-center'>".$totalfivd6." (100%)</td>"); ?>
                            <?php print("<td class='text-center' colspan='2'>".($icsifecun)." (100%)</td>"); ?>
                            <?php // print("<td class='text-center'>".$totalicsid6." (100%)</td>"); ?>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">2</td>
                            <td class="">Total Blastocistos</td>
                            <td class="text-center">
                                <?php
                                    if ($totalfivd5 != 0) {
                                        print($blasd5fiv . " (" . number_format(($blasd5fiv) * 100 / ($totalfivd5), 2) . "%)");
                                    } else {
                                        print($blasd5fiv . " (0%)");
                                    }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($totalfivd6 != 0) {
                                    print($blasd6fiv . " (" . number_format(($blasd6fiv) * 100 / ($totalfivd6), 2) . "%)");
                                } else {
                                    print($blasd6fiv . " (0%)");
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($totalicsid5 != 0) {
                                    print($blasd5icsi . " (" . number_format(($blasd5icsi) * 100 / ($totalicsid5), 2) . "%)");
                                } else {
                                    print($blasd5icsi . " (0%)");
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($totalicsid6 != 0) {
                                    print($blasd6icsi . " (" . number_format(($blasd6icsi) * 100 / ($totalicsid6), 2) . "%)");
                                } else {
                                    print($blasd6icsi . " (0%)");
                                }
                                ?>
                            </td>

                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Blastocisto Temprano (BT)</td>
                            <td class="text-center">
                                <?php
                                if ($blasd5fiv != 0) {
                                    print("<a href='repo_fiv_blas_bt_d5.php$url' target='_blank'>" . $blasterd5fiv . " (" . number_format(($blasterd5fiv) * 100 / ($blasd5fiv), 2) . "%)</a>");
                                } else {
                                    print($blasterd5fiv . " (0%)");
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6fiv != 0) {
                                    print("<a href='repo_fiv_blas_bt_d6.php$url' target='_blank'>" . $blasterd6fiv . " (" . number_format(($blasterd6fiv) * 100 / ($blasd6fiv), 2) . "%)</a>");
                                } else {
                                    print($blasterd6fiv . " (0%)");
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd5icsi != 0) {
                                    print("<a href='repo_icsi_blas_bt_d5.php$url' target='_blank'>" . $blasterd5icsi . " (" . number_format(($blasterd5icsi) * 100 / ($blasd5icsi), 2) . "%)</a>");
                                } else {
                                    print($blasterd5icsi . " (0%)");
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6icsi != 0) {
                                    print("<a href='repo_icsi_blas_bt_d6.php$url' target='_blank'>" . $blasterd6icsi . " (" . number_format(($blasterd6icsi) * 100 / ($blasd6icsi), 2) . "%)</a>");
                                } else {
                                    print($blasterd6icsi . " (0%)");
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Blastocisto Cavitado (BC)</td>
                            <td class="text-center">
                                <?php
                                if ($blasd5fiv != 0) {
                                    print("<a href='repo_fiv_blas_bc_d5.php$url' target='_blank'>" . $blascavid5fiv . " (" . number_format(($blascavid5fiv) * 100 / ($blasd5fiv), 2) . "%)</a>");
                                } else {
                                    print($blascavid5fiv . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>fivbcd5' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6fiv != 0) {
                                    print("<a href='repo_fiv_blas_bc_d6.php$url' target='_blank'>" . $blascavid6fiv . " (" . number_format(($blascavid6fiv) * 100 / ($blasd6fiv), 2) . "%)</a>");
                                } else {
                                    print($blascavid6fiv . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>fivbcd6' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd5icsi != 0) {
                                    print("<a href='repo_icsi_blas_bc_d5.php$url' target='_blank'>" . $blascavid5icsi . " (" . number_format(($blascavid5icsi) * 100 / ($blasd5icsi), 2) . "%)</a>");
                                } else {
                                    print($blascavid5icsi . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>icsibcd5' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6icsi != 0) {
                                    print("<a href='repo_icsi_blas_bc_d6.php$url' target='_blank'>" . $blascavid6icsi . " (" . number_format(($blascavid6icsi) * 100 / ($blasd6icsi), 2) . "%)</a>");
                                } else {
                                    print($blascavid6icsi . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>icsibcd6' target='_blank'>Ver Detalle</a>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Blastocisto Expandido (BE)</td>
                            <td class="text-center">
                                <?php
                                if ($blasd5fiv != 0) {
                                    print("<a href='repo_fiv_blas_be_d5.php$url' target='_blank'>" . $blasexpd5fiv . " (" . number_format(($blasexpd5fiv) * 100 / ($blasd5fiv), 2) . "%)</a>");
                                } else {
                                    print($blasexpd5fiv . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>fivbed5' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6fiv != 0) {
                                    print("<a href='repo_fiv_blas_be_d6.php$url' target='_blank'>" . $blasexpd6fiv . " (" . number_format(($blasexpd6fiv) * 100 / ($blasd6fiv), 2) . "%)</a>");
                                } else {
                                    print($blasexpd6fiv . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>fivbed6' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd5icsi != 0) {
                                    print("<a href='repo_icsi_blas_be_d5.php$url' target='_blank'>" . $blasexpd5icsi . " (" . number_format(($blasexpd5icsi) * 100 / ($blasd5icsi), 2) . "%)</a>");
                                } else {
                                    print($blasexpd5icsi . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>icsibed5' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6icsi != 0) {
                                    print("<a href='repo_icsi_blas_be_d6.php$url' target='_blank'>" . $blasexpd6icsi . " (" . number_format(($blasexpd6icsi) * 100 / ($blasd6icsi), 2) . "%)</a>");
                                } else {
                                    print($blasexpd6icsi . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>icsibed6' target='_blank'>Ver Detalle</a>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Blastocisto Iniciando Hatching (BHI)</td>
                            <td class="text-center">
                                <?php
                                if ($blasd5fiv != 0) {
                                    print("<a href='repo_fiv_blas_bhi_d5.php$url' target='_blank'>" . $blasinid5fiv . " (" . number_format(($blasinid5fiv) * 100 / ($blasd5fiv), 2) . "%)</a>");
                                } else {
                                    print($blasinid5fiv . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>fivbhid5' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6fiv != 0) {
                                    print("<a href='repo_fiv_blas_bhi_d6.php$url' target='_blank'>" . $blasinid6fiv . " (" . number_format(($blasinid6fiv) * 100 / ($blasd6fiv), 2) . "%)</a>");
                                } else {
                                    print($blasinid6fiv . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>fivbhid6' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd5icsi != 0) {
                                    print("<a href='repo_icsi_blas_bhi_d5.php$url' target='_blank'>" . $blasinid5icsi . " (" . number_format(($blasinid5icsi) * 100 / ($blasd5icsi), 2) . "%)</a>");
                                } else {
                                    print($blasinid5icsi . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>icsibhid5' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6icsi != 0) {
                                    print("<a href='repo_icsi_blas_bhi_d6.php$url' target='_blank'>" . $blasinid6icsi . " (" . number_format(($blasinid6icsi) * 100 / ($blasd6icsi), 2) . "%)</a>");
                                } else {
                                    print($blasinid6icsi . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>icsibhid6' target='_blank'>Ver Detalle</a>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="text-right">Blastocisto Hatched (BH)</td>
                            <td class="text-center">
                                <?php
                                if ($blasd5fiv != 0) {
                                    print("<a href='repo_fiv_blas_bh_d5.php$url' target='_blank'>" . $blashatd5fiv . " (" . number_format(($blashatd5fiv) * 100 / ($blasd5fiv), 2) . "%)</a>");
                                } else {
                                    print($blashatd5fiv . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>fivbhd5' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6fiv != 0) {
                                    print("<a href='repo_fiv_blas_bh_d6.php$url' target='_blank'>" . $blashatd6fiv . " (" . number_format(($blashatd6fiv) * 100 / ($blasd6fiv), 2) . "%)</a>");
                                } else {
                                    print($blashatd6fiv . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>fivbhd6' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd5icsi != 0) {
                                    print("<a href='repo_icsi_blas_bh_d5.php$url' target='_blank'>" . $blashatd5icsi . " (" . number_format(($blashatd5icsi) * 100 / ($blasd5icsi), 2) . "%)</a>");
                                } else {
                                    print($blashatd5icsi . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>icsibhd5' target='_blank'>Ver Detalle</a>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($blasd6icsi != 0) {
                                    print("<a href='repo_icsi_blas_bh_d6.php$url' target='_blank'>" . $blashatd6icsi . " (" . number_format(($blashatd6icsi) * 100 / ($blasd6icsi), 2) . "%)</a>");
                                } else {
                                    print($blashatd6icsi . " (0%)");
                                }
                                ?>
                                <br><a href='<?php echo $url1; ?>icsibhd6' target='_blank'>Ver Detalle</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center align-middle">3</td>
                            <td class="">
                                Total Global
                            </td>
                            <td colspan='2' class='text-center'>
                                <?php
                                if ($fivfecun != 0) {
                                    print("<b><h5>" . ($blasd5fiv + $blasd6fiv) . " (" . number_format(($blasd5fiv + $blasd6fiv) / ($fivfecun) * 100, 2) . "%)</h5></b>");
                                } else {
                                    print("<b><h5>" . ($blasd5fiv + $blasd6fiv) . " (0%)</h5></b>");
                                }
                                ?>
                            </td>
                            <td colspan='2' class='text-center'>
                                <?php
                                if ($icsifecun != 0) {
                                    print("<b><h5>" . ($blasd5icsi + $blasd6icsi) . " (" . number_format(($blasd5icsi + $blasd6icsi) / ($icsifecun) * 100, 2) . "%)</h5></b>");
                                } else {
                                    print("<b><h5>" . ($blasd5icsi + $blasd6icsi) . " (0%)</h5></b>");
                                }
                                ?>
                            </td>
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