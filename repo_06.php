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
            <h2>Reporte TED Betas</h2>
        </div>
        <?php
        if ($_SESSION['role'] == "9") {
            $between = $url = $med = $embins = $ovo = $ini = $fin = $edesde = $ehasta = $tipa = $tipant = $busqueda = $p_extras = "";
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
                    $between .= " and CAST(lab_aspira.fec as date) between '$ini' and '$fin'";
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
                if (isset($_POST["tipant"]) && !empty($_POST["tipant"])) {
                    $tipant = $_POST['tipant'];
                    $between.= " and la.tip = '$tipant'";
                    if ($url == "") {
                        $url .= "?tipant=$tipant";
                    } else {
                        $url .= "&tipant=$tipant";
                    }
                }
                if (isset($_POST["p_extras"]) && !empty($_POST["p_extras"])) {
                    if ($_POST["p_extras"] == "VACIO") {
                        $p_extras = $_POST['p_extras'];
                        $busqueda.= " and (hc_reprod.p_extras = '' or hc_reprod.p_extras is null) ";
                        if ($url == "") {
                            $url .= "?p_extras=$p_extras";
                        } else {
                            $url .= "&p_extras=$p_extras";
                        }
                    }else{
                        $p_extras = $_POST['p_extras'];
                        $busqueda.= " and hc_reprod.p_extras ilike '%$p_extras%' ";
                        if ($url == "") {
                            $url .= "?p_extras=$p_extras";
                        } else {
                            $url .= "&p_extras=$p_extras";
                        }
                    }
                }
            }
            // 
            $consulta = $db->prepare("SELECT id, nom from lab_user");
            $consulta->execute();
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $datos = $consulta->fetchAll();
            //
            $total = $pendiente = $positiva = $negativa = $bioquimico = $aborto = 0;
            $totalembrio = $pendienteembrio = $positivaembrio = $negativaembrio = $bioquimicoembrio = $abortoembrio = 0;
            $fecha_inicio_band = $pos = true;
 
            $rPaci = $db->prepare("
                select
                hc_reprod.id, coalesce(hc_reprod.pago_extras, '') pago_extras, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                , lab_aspira_t.beta
                , la.pro, la.tip tipant
                from hc_reprod
                inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
                inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'
                left join lab_aspira_t on lab_aspira_t.pro=lab_aspira.pro and lab_aspira_t.estado is true
                inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
                inner join lab_aspira la on la.pro = lab_aspira_dias.pro_c and la.estado is true $between
                left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
                where hc_reprod.estado = true and hc_reprod.des_dia >= 1 $busqueda
                group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                , lab_aspira_t.beta
                , la.pro, la.tip
                order by lab_aspira.fec asc");
            $rPaci->execute();
            while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($paci['des_don'])) {
                    if ($paci['beta'] === 0) {
                        $pendienteembrio++;
                    }
                    if ($paci['beta'] == 1) {
                        $positivaembrio++;
                    }
                    if ($paci['beta'] == 2) {
                        $negativaembrio++;
                    }
                    if ($paci['beta'] == 3) {
                        $bioquimicoembrio++;
                    }
                    if ($paci['beta'] == 4) {
                        $abortoembrio++;
                    }
                    $totalembrio++;
                }
                //
                if (empty($paci['des_don'])) {
                    if ($paci['beta'] === 0) {
                        $pendiente++;
                    }
                    if ($paci['beta'] == 1) {
                        $positiva++;
                    }
                    if ($paci['beta'] == 2) {
                        $negativa++;
                    }
                    if ($paci['beta'] == 3) {
                        $bioquimico++;
                    }
                    if ($paci['beta'] == 4) {
                        $aborto++;
                    }
                    $total++;
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
                                <input class="form-control" name="edesde" type="number" value="<?php echo $_POST['edesde']??''; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Y menor a:</label>
                            <div>
                                <input class="form-control" name="ehasta" type="number" value="<?php echo $_POST['ehasta']??''; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Mostrar Desde:</label>
                            <div>
                                <input class="form-control" name="ini" type="date" value="<?php echo $_POST['ini']??''; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Hasta:</label>
                            <div>
                                <input class="form-control" name="fin" type="date" value="<?php echo $_POST['fin']??''; ?>">
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
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">T. Paciente Anterior</label>
                            <select name='tipant' class="form-control">
                                <option value='' >todos</option>
                                <option value='P' <?php if($tipant == "P") print("selected"); ?>>paciente</option>
                                <option value='D' <?php if($tipant == "D") print("selected"); ?>>donante</option>
                                <option value='R' <?php if($tipant == "R") print("selected"); ?>>receptora</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">EXTRAS</label>
                            <select name='p_extras' class="form-control">
                                <option value='' >Seleccionar</option>
                                <?php
                                    $consulta = $db->prepare("select nombre from man_extras_medico where estado = 1 or id = 17 order by nombre");
                                    $consulta->execute();
                                    while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ""; 
                                        if ($p_extras == $data['nombre']) $selected="selected";
                                        print("<option value='".$data['nombre']."' $selected>".$data['nombre']."</option>");
                                    }
                                ?>                   
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
                        <th class="text-center">Item</th>
                        <th class="text-center">Tipo de Beta</th>
                        <th class="text-center">TED</th>
                        <th class="text-center">Embriodonación</th>
                    </thead>
                    <tbody>
                        <?php
                        if ($url==""){
                            $url="repo_model_06.php?rep=";
                        } else {
                            $url="repo_model_06.php".$url."&rep=";
                        }
                        print("<tr><td class='text-center'>1</td><td>Pendiente</td>
                            <td class='text-center'><a href='".$url."betapen' target='_blank'>".$pendiente." (".number_format(($pendiente)*100/(($total != 0) ? $total : 1), 2)."%)</a></td>
                            <td class='text-center'><a href='".$url."embriopen' target='_blank'>".$pendienteembrio." (". ($totalembrio == 0 ? '0.00' : number_format(($pendienteembrio)*100/($totalembrio), 2)) ."%)</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>2</td><td>Negativa</td>
                            <td class='text-center'><a href='".$url."betaneg' target='_blank'>".$negativa." (".number_format(($negativa)*100/(($total != 0) ? $total : 1), 2)."%)</a></td>
                            <td class='text-center'><a href='".$url."embrioneg' target='_blank'>".$negativaembrio." (". ($totalembrio == 0 ? '0.00' : number_format(($negativaembrio)*100/($totalembrio), 2)) ."%)</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>3</td><td>Positiva</td>
                            <td class='text-center'><a href='".$url."betapos' target='_blank'>".$positiva." (".number_format(($positiva)*100/(($total != 0) ? $total : 1), 2)."%)</a></td>
                            <td class='text-center'><a href='".$url."embriopos' target='_blank'>".$positivaembrio." (". ($totalembrio == 0 ? '0.00' : number_format(($positivaembrio)*100/($totalembrio), 2)) ."%)</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>4</td><td>Bioquímico</td>
                            <td class='text-center'><a href='".$url."betabio' target='_blank'>".$bioquimico." (".number_format(($bioquimico)*100/(($total != 0) ? $total : 1), 2)."%)</a></td>
                            <td class='text-center'><a href='".$url."embriobio' target='_blank'>".$bioquimicoembrio." (". ($totalembrio == 0 ? '0.00' : number_format(($bioquimicoembrio)*100/($totalembrio), 2)) ."%)</a></td>
                        </tr>");
                        print("<tr><td class='text-center'>5</td><td>Aborto</td>
                            <td class='text-center'><a href='".$url."betabo' target='_blank'>".$aborto." (".number_format(($aborto)*100/(($total != 0) ? $total : 1), 2)."%)</a></td>
                            <td class='text-center'><a href='".$url."embrioabo' target='_blank'>".$abortoembrio." (". ($totalembrio == 0 ? '0.00' : number_format(($abortoembrio)*100/($totalembrio), 2)) ."%)</a></td>
                        </tr>");
                        print("<tr><td></td><td class='text-right'>Total</td>
                            <td class='text-center'>".($total)."</td>
                            <td class='text-center'>".($totalembrio)."</td>
                        </tr>");
                    ?>
                    </tbody>
                </table>
            </div>
            <div style="float:right"><p><b>Fecha y Hora de Reporte:</b> <?php
                date_default_timezone_set('America/Lima');
                print(date("Y-m-d H:i:s"));
            ?></p></div>
        </div>
    </div>
<?php } ?>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>