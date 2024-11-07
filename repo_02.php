
<!DOCTYPE HTML>
<html>
<head>
    <?php
        include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script type="text/javascript">
        function PrintElem(elem) {
            var data = $(elem).html();
            var mywindow = window.open('', 'Imprimir', 'height=600,width=800');
            mywindow.document.write('<html><head><title>Imprimir</title>');
            mywindow.document.write('<style> @page {margin: 0px 0px 0px 5px;} table {border-collapse: collapse;font-size:10px;} .table-stripe td {border: 1px solid black;} .tablamas2 td {border: 1px solid white;} .mas2 {display: block !important;} .noVer, .ui-table-cell-label {display: none;} a:link {pointer-events: none; cursor: default;}</style>');
            mywindow.document.write("</head><body><p style='align: center'>Reporte Fecundación In Vitro</p>");
            mywindow.document.write(data);
            mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
            mywindow.document.write('</body></html>');
            return true;
        }
    </script>
</head>
<body>
    <?php require ('_includes/repolab_menu.php'); ?>
    <div class="container">
        <div class="card mb-3">
            <h2>Reporte NGS</h2>
        </div>
        <?php
        if ($_SESSION['role'] == "9") {
            $between = $url = $med = $embins = $ovo = $ini = $fin = "";
            if (isset($_POST) && !empty($_POST)) {
                if ( isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
                    $ini = $_POST['ini'];
                    $fin = $_POST['fin'];
                    $between = " and CAST(lab_aspira.fec AS date) between '$ini' and '$fin'";
                    $url = "?ini=$ini&fin=$fin";
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
            }
            // 
            $consulta = $db->prepare("SELECT id, nom from lab_user");
            $consulta->execute();
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $datos = $consulta->fetchAll();
            //
            $item = $ambos =  $fiv = $icsi = $recep = $crio = $ted = $dgp = $piiu = $emb = $od = $don = $tedngs = 0;
            $normal =  $anormal = $nr = $mosaico = 0;
            $betasngs = 0;
            $fecha_inicio_band = $pos = true;
            $rPaciBet = $db->prepare("SELECT
                hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec, count(lab_aspira_dias.pro) total
                , count( case when lab_aspira_dias.ngs1 = 1 then true end ) normal
                , count( case when lab_aspira_dias.ngs1 = 2 then true end ) anormal
                , count( case when lab_aspira_dias.ngs1 = 3 then true end ) nr
                , count( case when lab_aspira_dias.ngs1 = 4 then true end ) mosaico
                from hc_reprod
                inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T' AND lab_aspira.dias>=5 and lab_aspira.estado is true$between
                inner join lab_aspira_t on lab_aspira.pro=lab_aspira_t.pro and lab_aspira_t.estado is true
                inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
                and lab_aspira_dias.ngs1 in (1, 2, 3, 4) and (
                    (lab_aspira_dias.d5d_bio<>0 and lab_aspira_dias.d5f_cic='C') or (lab_aspira_dias.d6d_bio<>0 and lab_aspira_dias.d6f_cic='C')
                )
                left join hc_paciente on hc_paciente.dni = hc_reprod.dni
                left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
                where hc_reprod.estado = true and hc_reprod.cancela=0 and hc_reprod.pago_extras ilike '%NGS%'
                group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                order by lab_aspira.fec asc");
            $rPaciBet->execute();
            while ($pacibet = $rPaciBet->fetch(PDO::FETCH_ASSOC)) {
                $betasngs++;
            }
            $rPaciDet = $db->prepare("SELECT
                hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec, count(lab_aspira_dias.pro) total
                , count( case when lab_aspira_dias.ngs1 = 1 then true end ) normal
                , count( case when lab_aspira_dias.ngs1 = 2 then true end ) anormal
                , count( case when lab_aspira_dias.ngs1 = 3 then true end ) nr
                , count( case when lab_aspira_dias.ngs1 = 4 then true end ) mosaico
                from hc_reprod
                inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T' AND lab_aspira.dias>=5$between
                inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
                and lab_aspira_dias.ngs1 in (1, 2, 3, 4) and ((lab_aspira_dias.d5d_bio<>0 and lab_aspira_dias.d5f_cic='C') or (lab_aspira_dias.d6d_bio<>0 and lab_aspira_dias.d6f_cic='C'))
                left join hc_paciente on hc_paciente.dni = hc_reprod.dni
                left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
                where hc_reprod.estado = true and hc_reprod.cancela=0 and hc_reprod.pago_extras ilike '%NGS%'
                group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                order by lab_aspira.fec asc");
            $rPaciDet->execute();
            while ($pacidet = $rPaciDet->fetch(PDO::FETCH_ASSOC)) {
                $normal+=$pacidet['normal'];
                $anormal+=$pacidet['anormal'];
                $nr+=$pacidet['nr'];
                $mosaico+=$pacidet['mosaico'];
            }

            $rPaci = $db->prepare("
                select
                hc_reprod.id, coalesce(hc_reprod.pago_extras, '') pago_extras, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                from hc_reprod
                inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'$between
                left join hc_paciente on hc_paciente.dni = hc_reprod.dni
                left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
                where hc_reprod.estado = true
                order by lab_aspira.fec asc");
            $rPaci->execute();
            while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
            	$pos = false;
                if ($fecha_inicio_band and isset($paci["fec"])) {
                    $fecha_inicio = $paci["fec"];
                    $fecha_inicio_band = false;
                }
                if ($paci['p_fiv'] == 1 and $paci['p_icsi'] == 1) {
                    $ambos++;
                } else if ($paci['p_fiv'] == 1) {
                	$fiv++;
                } else if ($paci['p_icsi'] == 1) {
                	$icsi++;
                }
                if ($paci['tip'] == 'R' and $paci['p_od'] <> '') {
                   $od++;
                }
                if ($paci['tip'] == 'R' and ($paci['des_don'] <> null and $paci['des_dia'] === 0)) {
                   $don++;
                }
                if (!isset($paci['des_don']) && $paci['des_dia'] > 1) {
                   $ted++;
                }
                if (strpos($paci['pago_extras'], 'NGS') !== false) {
                	$dgp++;
                }
                if ($paci['p_cri'] == 1) {
                   $crio++;
                }
                if ($paci['p_iiu'] == 1) {
                   $piiu++;
                }
                if (isset($paci['des_don']) && $paci['des_dia'] > 1) {
                   $emb++;
                }
                //
                if (!isset($paci['des_don']) && $paci['des_dia'] > 1 && strpos($paci['pago_extras'], 'NGS') !== false) {
                   $tedngs++;
                }
            }
        ?>
        <div class="card mb-3" id="imprime">
            <h5 class="card-header">Filtros</h5>
            <div class="card-body">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Mostrar Desde</label>
                            <div>
                                <input class="form-control" name="ini" type="date" value="<?php echo $_POST['ini']; ?>" id="example-datetime-local-input">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Hasta</label>
                            <div>
                                <input class="form-control" name="fin" type="date" value="<?php echo $_POST['fin']; ?>" id="example-datetime-local-input">
                            </div>
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
                                <option value='' >todos</option>
                                <option value='fresco' <?php if($ovo == "fresco") print("selected"); ?> >fresco</option>
                                <option value='vitrificado' <?php if($ovo == "vitrificado") print("selected"); ?>>vitrificado</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Médico</label>
                            <select name='med' class="form-control">
                                <option value='' >todos</option>
                                <option value='mvelit' <?php if($med == "mvelit") print("selected"); ?> >mvelit</option>
                                <option value='eescudero' <?php if($med == "eescudero") print("selected"); ?> >eescudero</option>
                                <option value='mascenzo' <?php if($med == "mascenzo") print("selected"); ?> >mascenzo</option>
                                <option value='cbonomini' <?php if($med == "cbonomini") print("selected"); ?> >cbonomini</option>
                                <option value='tacna' <?php if($med == "tacna") print("selected"); ?> >tacna</option>
                                <option value='cosorio' <?php if($med == "cosorio") print("selected"); ?> >cosorio</option>
                                <option value='lab' <?php if($med == "lab") print("selected"); ?> >lab</option>
                                <option value='rbozzo' <?php if($med == "rbozzo") print("selected"); ?> >rbozzo</option>
                                <option value='apuertas' <?php if($med == "apuertas") print("selected"); ?> >apuertas</option>
                                <option value='jolivas' <?php if($med == "jolivas") print("selected"); ?> >jolivas</option>
                                <option value='humanidad' <?php if($med == "humanidad") print("selected"); ?> >humanidad</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" class="btn btn-primary" name="Mostrar" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-3">
            <h5 class="card-header">Procedimientos NGS</h5>
            <div class="card-body mx-auto">
                <table class="table table-responsive table-bordered align-middle">
                    <thead class="thead-dark">
                        <th>Item</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                    </thead>
                    <tbody>
                    	<tr><td class="text-center">1</td><td>Ciclos realizados (Transferencias)</td><td class="text-center"><?php print("<a href='repo_ted_ngs.php$url' target='_blank'>".$tedngs."</a>"); ?></td></tr>
                        <tr><td class="text-center">2</td><td>Embriones Normales</td><td class="text-center"><?php print("<a href='repo_ngs_resultado_normal.php$url' target='_blank'>".$normal."</a>"); ?></td></tr>
                        <tr><td class="text-center">3</td><td>Embriones Anormales</td><td class="text-center"><?php print("<a href='repo_ngs_resultado_anormal.php$url' target='_blank'>".$anormal."</a>"); ?></td></tr>
                        <tr><td class="text-center">4</td><td>Embriones NR</td><td class="text-center"><?php print("<a href='repo_ngs_resultado_nr.php$url' target='_blank'>".$nr."</a>"); ?></td></tr>
                        <tr><td class="text-center">5</td><td>Embriones Mosaico</td><td class="text-center"><?php print("<a href='repo_ngs_resultado_mosaico.php$url' target='_blank'>".$mosaico."</a>"); ?></td></tr>
                        <tr><td class="text-center">6</td><td>Número de Embarazos</td><td class="text-center"><?php print("<a href='repo_betas.php$url' target='_blank'>".$betasngs."</a>"); ?></td></tr>
                    </tbody>
                </table><br/><br/>
            </div>
            <div style="float:right"><p><b>Fecha y Hora de Reporte:</b> <?php
                date_default_timezone_set('America/Lima');
                print(date("Y-m-d H:m:s"));
            ?></p></div>
        </div>
    </div>
<?php } ?>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>