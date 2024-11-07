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
    <!-- <script src="js/jquery.mobile-1.4.5.min.js"></script> -->
    <script type="text/javascript">
        var tableToExcel = (function () {
            var uri = 'data:application/vnd.ms-excel;base64,'
                ,
                template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                , base64 = function (s) {
                    return window.btoa(unescape(encodeURIComponent(s)))
                }
                , format = function (s, c) {
                    return s.replace(/{(\w+)}/g, function (m, p) {
                        return c[p];
                    })
                }
            return function (table, visita) {
                if (!table.nodeType) table = document.getElementById(table)
                var ctx = {worksheet: 'reporte_' + visita || 'reporte', table: table.innerHTML}
                window.location.href = uri + base64(format(template, ctx))
            }
        })();
    </script>
    <style>
        #repo_model_01{
            height: 350px;
        }
    </style>
</head>
<body>
    <?php require ('_includes/repolab_menu.php'); ?>
    <div class='container'>
        <?php
            $between = $ini = $fin = $med = $notas = $kidscore = "";
            // var_dump( $_POST );
            if (isset($_POST) && !empty($_POST)) {
                if ( isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
                    $ini = $_POST['ini'];
                    $fin = $_POST['fin'];
                } else {
                    $ini = $fin = date('Y-m-d');
                    /*$ini = date('Y-01-01');
                    $fin = date('Y').'-12-31';*/
                }
                // medico
                if (isset($_POST["med"]) && !empty($_POST["med"])) {
                    $med = $_POST['med'];
                    $between.= " and hc_reprod.med = '$med'";
                }
                // notas
                if (isset($_POST["notas"]) && !empty($_POST["notas"])) {
                    $notas = $_POST['notas'];
                    $between.= " and unaccent(hc_reprod.pago_notas) ilike ('%$notas%')";
                }
                // kidscore
                if ( isset($_POST["kidscore"]) && !empty($_POST["kidscore"]) ) {
                    $kidscore = $_POST['kidscore'];
                    $between.= " and (lad.d5kid = $kidscore or lad.d6kid = ".(int)$_POST['kidscore'].")";
                }
            } else {
                $ini = $fin = date('Y-m-d');
                /*$ini = date('Y-01-01');
                $fin = date('Y').'-12-31';*/
            }
        ?>
        <div class="card mb-3">
            <h5 class="card-header">Filtros</h5>
            <div class="card-body">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <!-- mostrar desde hasta -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Mostrar Desde</label>
                            <div>
                                <input class="form-control" name="ini" type="date" value="<?php print($ini); ?>" id="example-datetime-local-input">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Hasta</label>
                            <div>
                                <input class="form-control" name="fin" type="date" value="<?php print($fin); ?>" id="example-datetime-local-input">
                            </div>
                        </div>
                        <!-- medico -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Médico</label>
                            <select name='med' class="form-control">
                                <option value="" selected>SELECCIONAR</option>
                                <?php
                                    $data = $db->prepare("select codigo, nombre from man_medico where estado=1");
                                    $data->execute();
                                    while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                        print("<option value=".$info['codigo']);
                                    if ($med === $info['codigo'])
                                        echo " selected";
                                    print(">".mb_strtoupper($info['nombre'])."</option>");
                                } ?>
                            </select>
                        </div>
                        <!-- notas -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Notas</label>
                            <select name='notas' class="form-control">
                                <option value="" selected>SELECCIONAR</option>
                                <?php
                                    $data = $db->prepare("select id, nombre from man_notas where estado=1");
                                    $data->execute();
                                    while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                        print("<option value='".$info['nombre']."'");
                                    if ($med === $info['nombre'])
                                        echo " selected";
                                    print(">".mb_strtoupper($info['nombre'])."</option>");
                                } ?>
                            </select>
                        </div>
                        <!-- kidscore -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Kid Score</label>
                            <select name='kidscore' class="form-control">
                                <option value="" selected>SELECCIONAR</option>
                                <option value="0" <?php if ($kidscore == "0") echo "selected"; ?> >0</option>
                                <option value="1" <?php if ($kidscore == "1") echo "selected"; ?> >1</option>
                                <option value="2" <?php if ($kidscore == "2") echo "selected"; ?> >2</option>
                                <option value="3" <?php if ($kidscore == "3") echo "selected"; ?> >3</option>
                                <option value="4" <?php if ($kidscore == "4") echo "selected"; ?> >4</option>
                                <option value="5" <?php if ($kidscore == "5") echo "selected"; ?> >5</option>
                                <option value="6" <?php if ($kidscore == "6") echo "selected"; ?> >6</option>
                            </select>
                        </div>
                        <!-- buscar -->
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        if ($login == 'adminlab') {
            $item = 0;
            $consulta = $db->prepare("
            SELECT
            lab_aspira.tip tipo_paciente, UPPER(hc_reprod.med) medico, lab_aspira.pro, lab_aspira.fec fecha,
            hc_paciente.dni, CONCAT(hc_paciente.ape, ' ', hc_paciente.nom) paciente, ROUND(EXTRACT(YEAR FROM AGE(lab_aspira.fec, hc_paciente.fnac))) edad,
            CASE 
                WHEN re.p_od IS NULL THEN CONCAT(pa.ape, ' ', pa.nom) 
                ELSE CONCAT(pod.ape, ' ', pod.nom) 
            END ovulo,
            CASE 
                WHEN re.p_od IS NULL THEN ROUND(EXTRACT(YEAR FROM AGE(la.fec, pa.fnac))) 
                ELSE ROUND(EXTRACT(YEAR FROM AGE(la.fec, pod.fnac))) 
            END edadovulo,
            lab_aspira_dias.ovo transferidos,
            UPPER(lab_aspira_dias.d5cel) celula_dia5, lab_aspira_dias.d5mci, lab_aspira_dias.d5tro,
            UPPER(lab_aspira_dias.d6cel) celula_dia6, lab_aspira_dias.d6mci, lab_aspira_dias.d6tro,
            lab_aspira_t.endo endometrio,
            hc_reprod.pago_extras extras, hc_reprod.pago_notas notas,
            CASE lab_aspira_t.beta 
                WHEN 0 THEN 'pendiente' 
                WHEN 1 THEN 'positivos' 
                WHEN 2 THEN 'negativos' 
                WHEN 3 THEN 'bioquimico' 
                WHEN 4 THEN 'aborto' 
            END beta,
            CASE 
                WHEN lab_aspira_dias.ngs1 <> 0 THEN 'si' 
                ELSE 'no' 
            END ngs,
            'hatching total',
            COALESCE(x.totalted, 0) totalted        
                from hc_reprod
                inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
                inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'
                inner join lab_aspira_t on lab_aspira_t.pro=lab_aspira.pro and lab_aspira_t.estado is true
                inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and ( lab_aspira_dias.d5f_cic = 'T' or lab_aspira_dias.d6f_cic = 'T' ) and lab_aspira_dias.estado is true
                inner join lab_aspira la on la.pro = lab_aspira_dias.pro_c and la.estado is true
                -- left join lab_aspira_dias lad on lad.pro = la.pro
                left join hc_reprod re on re.id = la.rep
                left join hc_paciente pod on pod.dni = re.p_od
                left join hc_paciente pa on pa.dni = re.dni
                left join (
                    select
                    r.dni, count(*) totalted
                    from hc_reprod r
                    inner join lab_aspira l on l.rep = r.id and l.estado is true
                    inner join lab_aspira_t t on t.pro = l.pro and t.estado is true
                    -- where r.des_don is null and r.des_dia >= 1
                    where r.estado = true
                    group by r.dni
                ) x on x.dni = hc_paciente.dni
                where hc_reprod.estado = true and lab_aspira.tip <> 'T'$between
                and lab_aspira.f_fin is not null
                -- and hc_reprod.des_don is null and hc_reprod.des_dia >= 1
                and ( hc_reprod.pago_extras ilike ('%HATCHING TOTAL%') or hc_reprod.pago_notas ilike ('%HATCHING TOTAL%') )
                and lab_aspira.fec between ? and ?
                /*group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                , lab_aspira_t.beta
                , la.pro, la.tip*/

                union

                SELECT
                lab_aspira.tip tipo_paciente, UPPER(hc_reprod.med) medico, lab_aspira.pro, lab_aspira.fec fecha,
                hc_paciente.dni, CONCAT(hc_paciente.ape, ' ', hc_paciente.nom) paciente, ROUND(EXTRACT(YEAR FROM AGE(lab_aspira.fec, hc_paciente.fnac))) edad,
                CASE 
                    WHEN re.p_od IS NULL THEN CONCAT(pa.ape, ' ', pa.nom) 
                    ELSE CONCAT(pod.ape, ' ', pod.nom) 
                END ovulo,
                CASE 
                    WHEN re.p_od IS NULL THEN ROUND(EXTRACT(YEAR FROM AGE(la.fec, pa.fnac))) 
                    ELSE ROUND(EXTRACT(YEAR FROM AGE(la.fec, pod.fnac))) 
                END edadovulo,
                lab_aspira_dias.ovo transferidos,
                UPPER(lab_aspira_dias.d5cel) celula_dia5, lab_aspira_dias.d5mci, lab_aspira_dias.d5tro,
                UPPER(lab_aspira_dias.d6cel) celula_dia6, lab_aspira_dias.d6mci, lab_aspira_dias.d6tro,
                lab_aspira_t.endo endometrio,
                hc_reprod.pago_extras extras, hc_reprod.pago_notas notas,
                CASE lab_aspira_t.beta 
                    WHEN 0 THEN 'pendiente' 
                    WHEN 1 THEN 'positivos' 
                    WHEN 2 THEN 'negativos' 
                    WHEN 3 THEN 'bioquimico' 
                    WHEN 4 THEN 'aborto' 
                END beta,
                CASE 
                    WHEN lab_aspira_dias.ngs1 <> 0 THEN 'si' 
                    ELSE 'no' 
                END ngs,
                'no hatching total',
                COALESCE(x.totalted, 0) totalted
                from hc_reprod
                inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
                inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'
                inner join lab_aspira_t on lab_aspira_t.pro=lab_aspira.pro and lab_aspira_t.estado is true
                inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and ( lab_aspira_dias.d5f_cic = 'T' or lab_aspira_dias.d6f_cic = 'T' ) and lab_aspira_dias.estado is true
                inner join lab_aspira la on la.pro = lab_aspira_dias.pro_c and la.estado is true
                -- left join lab_aspira_dias lad on lad.pro = la.pro
                left join hc_reprod re on re.id = la.rep
                left join hc_paciente pod on pod.dni = re.p_od
                left join hc_paciente pa on pa.dni = re.dni
                left join (
                    select
                    r.dni, count(*) totalted
                    from hc_reprod r
                    inner join lab_aspira l on l.rep = r.id and l.estado is true
                    inner join lab_aspira_t t on t.pro = l.pro and t.estado is true
                    -- where r.des_don is null and r.des_dia >= 1
                    where r.estado = true
                    group by r.dni
                ) x on x.dni = hc_paciente.dni
                where hc_reprod.estado = true and lab_aspira.tip <> 'T'$between
                and lab_aspira.f_fin is not null
                -- and hc_reprod.des_don is null and hc_reprod.des_dia >= 1
                and ( hc_reprod.pago_extras not ilike ('%HATCHING TOTAL%') and hc_reprod.pago_notas not ilike ('%HATCHING TOTAL%') )
                and lab_aspira.fec between ? and ?
                /*group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec
                , lab_aspira_t.beta
                , la.pro, la.tip*/");
            $consulta->execute(array($ini, $fin, $ini, $fin));
            print("
            <table class='table table-responsive table-bordered align-middle header-fixed' id='repo_model_01'>
                <thead class='thead-dark'>
                    <tr>
                        <th>Tipo Paciente</th>
                        <th>Médico</th>
                        <th>Procedimiento</th>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Edad</th>
                        <th>Óvulo</th>
                        <th>Edad Óvulo</th>
                        <th>Transferidos</th>
                        <th>Celula Dia 5</th>
                        <th>d5mci</th>
                        <th>d5tro</th>
                        <th>Célula Dia 6</th>
                        <th>d6mci</th>
                        <th>d6tro</th>
                        <th>Endometrio</th>
                        <th>Extras</th>
                        <th>Notas</th>
                        <th>Beta</th>
                        <th>NGS</th>
                        <th>NGS archivo</th>
                        <th>Hatching Total</th>
                        <th>Total Transferencias</th>
                    </tr>
                </thead>
                <tbody>
            ");
            while ($rec = $consulta->fetch(PDO::FETCH_ASSOC)) {
                // calculo de total de transferencias
                $consulta1 = $db->prepare("
                select
                r.dni, count(*) totalted
                from hc_reprod r
                inner join lab_aspira l on l.rep = r.id and l.estado is true
                inner join lab_aspira_t t on t.pro = l.pro and t.estado is true
                where r.estado = true and l.fec <= ? and r.dni = ?
                group by r.dni");
                $consulta1->execute( array($rec["fecha"], $rec["dni"]) );
                $info = $consulta1->fetch(PDO::FETCH_ASSOC);
                //ngs
                $ngs="";
                $ngsfiles="no";
                if (file_exists("analisis/ngs_".$rec['pro'].".pdf")) {
                    $ngsfiles="si";
                }
                $haching="";
                if(isset($rec['hatching total'])){
                    $haching= $rec['hatching total'];
                }
            	print("
                <tr>
                    <td>".$rec['tipo_paciente']."</td>
                    <td>".$rec['medico']."</td>
                    <td>".$rec['pro']."</td>
                    <td>".$rec['fecha']."</td>
                    <td>".mb_strtoupper($rec['paciente'])."</td>
                    <td>".$rec['edad']."</td>
                    <td>".mb_strtoupper($rec['ovulo'])."</td>
                    <td>".$rec['edadovulo']."</td>
                    <td>".$rec['transferidos']."</td>
                    <td>".$rec['celula_dia5']."</td>
                    <td>".$rec['d5mci']."</td>
                    <td>".$rec['d5tro']."</td>
                    <td>".$rec['celula_dia6']."</td>
                    <td>".$rec['d6mci']."</td>
                    <td>".$rec['d6tro']."</td>
                    <td>".$rec['endometrio']."</td>
                    <td>".$rec['extras']."</td>
                    <td>".$rec['notas']."</td>
                    <td>".$rec['beta']."</td>
                    <td>".$rec['ngs']."</td>
                    <td>$ngsfiles</td>
                    <td>".$haching."</td>
                    <!-- <td>".$rec['totalted']."</td> -->
                    <td>".$info['totalted']."</td>
        		</tr>");
            }
            ?>
            <div class="ui-content" role="main" id="imprime">
                <div style="float:right">
                    <p><b>Fecha y Hora de Reporte:</b>
                        <?php
                            date_default_timezone_set('America/Lima');
                            print(date("Y-m-d H:i:s")."<br>");
                            print("<b>Total Procedimientos:</b> ".$consulta->rowCount()."<br>");
                            print("<b>Descargar:</b> <a href='#' onclick=\"tableToExcel('repo_model_01', 'hatching_total')\" class='ui-btn ui-mini ui-btn-inline'><img src=\"_images/excel.png\" height='20' width='20' alt='excel'></a>");
                        ?>
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>