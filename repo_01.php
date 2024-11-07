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
            <h2>Reporte Procedimientos</h2>
        </div>
        <?php
        if ($_SESSION['role'] == "9") {
            $between = $url = $med = $embins = $ovo = $ini = $fin = $edesde = $ehasta = $tipa = "";
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
                    $between .= " AND CAST(lab_aspira.fec AS DATE) BETWEEN '$ini' AND '$fin'";
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
            }
            // 
            $consulta = $db->prepare("SELECT id, nom from lab_user");
            $consulta->execute();
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $datos = $consulta->fetchAll();
            // 
            $item =  $fiv = $icsi = $recep = $ted = $dgp = $piiu = $emb = $od = $don = 0;
            $crio = $criopac = $criodon = 0;
            $fecha_inicio_band = $pos = true;
            $rPaci = $db->prepare("
                select
                hc_reprod.id, coalesce(hc_reprod.pago_extras, '') pago_extras, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                from hc_reprod
                inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
                inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'$between
                left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
                where hc_reprod.estado = true
                order by lab_aspira.fec asc");
            $rPaci->execute();
            while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
                $item++;
            	$pos = false;
                if ($fecha_inicio_band and isset($paci["fec"])) {
                    $fecha_inicio = $paci["fec"];
                    $fecha_inicio_band = false;
                }
                if ($paci['p_fiv'] == 1 and $paci['p_icsi'] == 1) {
                    $fiv++;
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
                if (!isset($paci['des_don']) && $paci['des_dia'] >= 1) {
                   $ted++;
                }
                if (strpos($paci['pago_extras'], 'NGS') !== false) {
                	$dgp++;
                }
                if ($paci['p_cri'] == 1 ) {
                   $crio++;
                }
                if ($paci['p_cri'] == 1 and $paci['tip'] == 'P') {
                   $criopac++;
                }
                if ($paci['p_cri'] == 1 and $paci['tip'] == 'D') {
                   $criodon++;
                }
                if ($paci['p_iiu'] == 1) {
                   $piiu++;
                }
                if (isset($paci['des_don']) && $paci['des_dia'] > 1) {
                   $emb++;
                }
            }
        ?>
        <div class="card mb-3">
            <h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Filtros</h5>
            <div class="card-body collapse show" id="collapseExample">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Edad cumplida:</label>
                            <div>
                                <input class="form-control" name="edesde" type="number" value="<?php echo $edesde; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Y menor a:</label>
                            <div>
                                <input class="form-control" name="ehasta" type="number" value="<?php echo $ehasta; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Mostrar Desde:</label>
                            <div>
                                <input class="form-control" name="ini" type="date" value="<?php echo $ini; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Hasta:</label>
                            <div>
                                <input class="form-control" name="fin" type="date" value="<?php echo $fin; ?>">
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
                                <option value='' >todos</option>
                                <option value='fresco' <?php if($ovo == "fresco") print("selected"); ?> >fresco</option>
                                <option value='vitrificado' <?php if($ovo == "vitrificado") print("selected"); ?>>vitrificado</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Tipo Paciente</label>
                            <select name='tipa' class="form-control">
                                <option value='' >todos</option>
                                <option value='P' <?php if($tipa == "P") print("selected"); ?>>paciente</option>
                                <option value='R' <?php if($tipa == "R") print("selected"); ?>>receptora</option>
                                <option value='D' <?php if($tipa == "D") print("selected"); ?>>donante</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" class="btn btn-primary" name="Mostrar" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-3" id="imprime">
            <h5 class="card-header">Detalle:
                <?php
                    $detalle="";
                    if ( isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
                        $detalle.="<small>periodo: ".$_POST["ini"]." - ".$_POST["fin"]."</small>, ";
                    }
                    if (isset($_POST["med"]) && !empty($_POST["med"])) {
                        $detalle.="<small>médico: ".$_POST["med"]."</small>, ";
                    }
                    if (isset($_POST["embins"]) && !empty($_POST["embins"])) {
                        foreach ($datos as $row) {
                            if ($embins == $row['id']) $detalle.="<small>emb. inseminación: ".$row['nom']."</small>, ";
                        }
                    }
                    if (isset($_POST["ovo"]) && !empty($_POST["ovo"])) {
                        $detalle.="<small>origen ovocitos: ".strtolower($_POST["ovo"])."</small>, ";
                    }
                    if($detalle!="") print(substr($detalle, 0, strlen($detalle)-2));
                ?>
                <small class="pull-right"><a href="javascript:PrintElem('#imprime')">Imprimir</a></small>
            </h5>
            <div class="card-body mx-auto" id="imprime">
                <table class="table table-responsive table-bordered align-middle">
                    <thead class="thead-dark">
                        <th>Item</th>
                        <th>Tipo de Procedimiento</th>
                        <th>Cantidad</th>
                    </thead>
                    <tbody>
                        <?php
                        if ($url==""){
                            $urlcriodet="repo_model_01_crio_det.php?rep=";
                            $urlcriogra="repo_model_01_crio_gra.php?rep=";
                            $url="repo_model_01.php?rep=";
                        } else {
                            $urlcriodet="repo_model_01_crio_det.php".$url."&rep=";
                            $urlcriogra="repo_model_01_crio_gra.php".$url."&rep=";
                            $url="repo_model_01.php".$url."&rep=";
                        }
                        print("<tr><td class='text-center'>1</td><td>Fecundación In Vitro (FIV)</td>
                            <td class='text-center'><a href='$url"."fiv' target='_blank'>".$fiv."</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>2</td><td>Inyección intracitoplasmática de espermatozoides (ICSI)</td>
                            <td class='text-center'><a href='$url"."icsi' target='_blank'>".$icsi."</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>3</td><td>Vitrificación de Óvulos</td>
                            <td class='text-center'>
                                <a href='$urlcriodet"."crio' target='_blank'>".$crio."</a><br>
                                <a href='$urlcriogra"."crio' target='_blank'>Ver Gráfica</a>
                            </td>
                        </tr>");
                        print("<tr><td class='text-center'></td><td class='text-right'>Vitrificación de Óvulos de Paciente</td>
                            <td class='text-center'>
                                <a href='$urlcriodet"."criopac' target='_blank'>".$criopac."</a><br>
                            </td>
                        </tr>");
                        print("<tr><td class='text-center'></td><td class='text-right'>Vitrificación de Óvulos de Donante</td>
                            <td class='text-center'>
                                <a href='$urlcriodet"."criodon' target='_blank'>".$criodon."</a><br>
                            </td>
                        </tr>");
                        print("<tr><td class='text-center'>4</td><td>Transferencia de Embriones criopreservados</td>
                            <td class='text-center'><a href='$url"."ted' target='_blank'>".$ted."</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>5</td><td>Embriodonación</td>
                            <td class='text-center'><a href='$url"."emb' target='_blank'>".$emb."</a></td>
                        </tr>");
                        print("<tr><td></td><td class='text-right'>Total</td>
                            <td class='text-center'>".$item."</td>
                        </tr>");
                        print("<tr><td colspan='3'></td></tr>");
                        print("<tr><td class='text-center'>A</td><td>Donación de Óvulos (OD Fresco)</td>
                            <td class='text-center'><a href='$url"."od' target='_blank'>".$od."</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>B</td><td>Donación de Óvulos (DESCONGELACIÓN OVULOS DONADOS)</td>
                            <td class='text-center'><a href='$url"."don' target='_blank'>".$don."</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>C</td><td>Diagnóstico Genético Preimplantacional (DGP)</td>
                            <td class='text-center'><a href='$url"."dgp' target='_blank'>".$dgp."</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>D</td><td>Inseminación Artificial</td>
                            <td class='text-center'><a href='$url"."iiu' target='_blank'>".$piiu."</a></td>
                        </tr>");
                    ?>
                    </tbody>
                </table>
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