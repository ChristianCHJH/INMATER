<?php
session_start();
date_default_timezone_set('America/Lima');
?>
<!DOCTYPE HTML>
<html>
<head>
    <?php
        $login = $_SESSION['login'];
        $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
        if ($_SESSION['role'] <> 2) {
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
        }
        require("_database/db_tools.php");
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
        function PrintElem(elem) {
            var data = $(elem).html();
            var mywindow = window.open('', 'Imprimir', 'height=600,width=800');
            mywindow.document.write('<html><head><title>Imprimir</title>');
            mywindow.document.write('<style> @page {margin: 0px 0px 0px 5px;} table {border-collapse: collapse;font-size:10px;} .table-stripe td {border: 1px solid black;} .tablamas2 td {border: 1px solid white;} .mas2 {display: block !important;} .noVer, .ui-table-cell-label {display: none;} a:link {pointer-events: none; cursor: default;}</style>');
            mywindow.document.write("</head><body><p style='align: center'>Reporte Ventas</p>");
            mywindow.document.write(data);
            mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
            mywindow.document.write('</body></html>');
            return true;
        }
    </script>
    <style>
        #repo_model_01{
            height: 350px;
        }
        .scroll_h {
            overflow: auto;
        }
        .mayuscula {
            text-transform: uppercase;
            font-size: small;
        }
        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class='container'>
        <?php
            $between = $ini = $fin = "";
            if (isset($_POST) && !empty($_POST)) {
                if ( isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
                    $ini = $_POST['ini'];
                    $fin = $_POST['fin'];
                } else {
                    $ini = $fin = date('Y-m-d');
                    /*$ini = date('Y-01-01');
                    $fin = date('Y').'-12-31';*/
                }
            } else {
                $ini = $fin = date('Y-m-d');
                /*$ini = date('Y-01-01');
                $fin = date('Y').'-12-31';*/
            }
        ?>
        <div class="text-right">
            <a href="javascript:window.close();">Cerrar</a>
        </div>
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
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        if ($_SESSION['role'] == 2) {
            $item = 0;
            $rRec = $db->prepare("
                select
                lab_andro_cap.mue, lab_aspira.pro, lab_aspira.fec
                , hc_paciente.fnac, hc_paciente.tip tipodocumentoidentidad, hc_paciente.dni, hc_paciente.ape, hc_paciente.nom, coalesce(hc_paciente.peso, '0') peso, coalesce(hc_paciente.talla, '0') talla
                , lab_aspira.n_ovo, lab_aspira.n_ins, count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' then true end ) fecundado
                , count( case when lab_aspira_dias.d2f_cic = 'T' then true end ) + count( case when lab_aspira_dias.d3f_cic = 'T' then true end ) + count( case when lab_aspira_dias.d3f_cic = 'T' then true end ) + count( case when lab_aspira_dias.d4f_cic = 'T' then true end ) + count( case when lab_aspira_dias.d5f_cic = 'T' then true end ) + count( case when lab_aspira_dias.d6f_cic = 'T' then true end ) transferidos
                , count( case when lab_aspira_dias.d2f_cic = 'C' then true end ) + count( case when lab_aspira_dias.d3f_cic = 'C' then true end ) + count( case when lab_aspira_dias.d3f_cic = 'C' then true end ) + count( case when lab_aspira_dias.d4f_cic = 'C' then true end ) + count( case when lab_aspira_dias.d5f_cic = 'C' then true end ) + count( case when lab_aspira_dias.d6f_cic = 'C' then true end ) crios
                , case lab_aspira_t.beta when 0 then 'pendiente' when 1 then 'positivo' when 2 then 'negativo' when 3 then 'bioquimico' when 4 then 'aborto' else 'ninguno' end beta
                , count( case when lab_aspira_dias.ngs1 = 1 then true end ) normales
                , split_part(lab_aspira.pro,'-',1) AS p1
                , split_part(lab_aspira.pro,'-',-1) AS p2
                , hc_paciente.dni, hc_paciente.ape, hc_paciente.nom, san
                -- , m_ets, don
                , hc_reprod.p_dni, hc_reprod.p_dni_het, hc_reprod.p_od
                , hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_cri, hc_reprod.p_iiu
                , hc_reprod.p_don, hc_reprod.des_don, hc_reprod.des_dia
                , hc_reprod.pago_extras, hc_reprod.med, lab_aspira.pro, lab_aspira.tip, lab_aspira.vec, lab_aspira.dias
                from hc_reprod
                inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T' and lab_aspira.f_fin<>'1899-12-30'
                inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
                left join hc_paciente on hc_paciente.dni = hc_reprod.dni
                left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
                left join lab_aspira_t on lab_aspira_t.pro = lab_aspira.pro and lab_aspira_t.estado is true
                left join lab_andro_cap on lab_andro_cap.pro = lab_aspira.pro and lab_andro_cap.eliminado is false
                where hc_reprod.estado = true and lab_aspira.fec between ? and ?
                and (hc_reprod.p_fiv >= 1 or hc_reprod.p_icsi >= 1)
                group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
                , hc_reprod.des_dia, hc_reprod.des_don
                , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
                , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
                , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
                , lab_aspira.fec, hc_paciente.fnac, hc_paciente.tip, hc_paciente.dni,lab_andro_cap.mue,lab_aspira_t.beta
                order by lab_aspira.fec asc");
            $rRec->execute(array($ini, $fin));
            print("
            <table class='table table-responsive table-bordered align-middle header-fixed' id='repo_model_01'>
                <thead class='thead-dark'>
                    <tr>
                        <th>ID Inmater</th>
                        <th>Doctor</th>
                        <th>Paciente Tipo Documento Identidad</th>
                        <th>Paciente DNI</th>
                        <th>Paciente Apellidos</th>
                        <th>Paciente Nombres</th>
                        <th>Procedimiento Inmater</th>
                        <th>Outcome</th>
                        <th>Outcome Type</th>
                        <th>Procedure</th>
                        <th>Initial Date</th>
                        <th>Chart or PIN</th>
                        <th>Date of Birth</th>
                        <th>Weight (kg)</th>
                        <th>Height (cm)</th>
                        <th>Diagnose 1</th>
                        <th>Diagnose 2</th>
                        <th>GnRH</th>
                        <th>FSH</th>
                        <th>LH</th>
                        <th>Other</th>
                        <th>Luteal Phase</th>
                        <th>Number of oocytes retrieved</th>
                        <th>Type of insemination</th>
                        <th>number of oocytes inseminated</th>
                        <th>Sperm source</th>
                        <th>Fresh Cryopreserved</th>
                        <th>Number of oocytes fertilized</th>
                        <th>NGS files</th>
                        <th>Assisted Hatching</th>
                        <th>Preimplantational Genetic Testing</th>
                        <th>Nº of biopsed</th>
                        <th>Nº of normal</th>
                        <th>Day embryo transfer </th>
                        <th>Number of embryos transferred</th>
                        <th>Number of embryos cryopreserved</th>
                        <th>Stage of embryo development at freezing</th>
                        <th>Number of gestational sac in first ultrasound</th>
                        <th>Number of gestational sac in second ultrasound</th>
                        <th>Number of newborns</th>
                        <th>Citogenic Study</th>
                        <th>Citogenic Study Response</th>
                        <th>Gestational age at delivery</th>
                        <th>Baby1 - Viability</th>
                        <th>Baby1 - Weight</th>
                        <th>Baby1 - Congenital abnormality</th>
                        <th>Baby1 - Citogenetic study</th>
                        <th>Baby2 - Viability</th>
                        <th>Baby2 - Weight</th>
                        <th>Baby2 - Congenital abnormality</th>
                        <th>Baby2 - Citogenetic study</th>
                        <th>Baby3 - Viability</th>
                        <th>Baby3 - Weight</th>
                        <th>Baby3 - Congenital abnormality</th>
                        <th>Baby3 - Citogenetic study</th>
                        <th>Baby4 - Viability</th>
                        <th>Baby4 - Weight</th>
                        <th>Baby4 - Congenital abnormality</th>
                        <th>Baby4 - Citogenetic study</th>
                        <th>Complications (OHSS)</th>
                        <th>Complications (Haemorrhage)</th>
                        <th>Complications (Infection)</th>
                        <th>Complications (Specify)</th>
                        <th>Thawing method of oocytes</th>
                        <th>Number of oocytes thawed</th>
                    </tr>
                </thead>
                <tbody>
            ");
            while ($rec = $rRec->fetch(PDO::FETCH_ASSOC)) {
                // Procedure
                $procedimiento=$procinmater="";
                if ($rec['p_fiv'] == 1 and $rec['p_icsi'] == 1) {
                    if (empty($procedimiento)) {
                        $procedimiento="FRESH";
                        $procinmater="FIV";
                    } else {
                        $procedimiento.="<br>FRESH";
                        $procinmater.="<br>FIV";
                    }
                } else if ($rec['p_fiv'] == 1) {
                    if (empty($procedimiento)) {
                        $procedimiento="FRESH";
                        $procinmater="FIV";
                    } else {
                        $procedimiento.="<br>FRESH";
                        $procinmater.="<br>FIV";
                    }
                } else if ($rec['p_icsi'] == 1) {
                    if (empty($procedimiento)) {
                        $procedimiento="FRESH";
                        $procinmater="ICSI";
                    } else {
                        $procedimiento.="<br>FRESH";
                        $procinmater.="<br>ICSI";
                    }
                }
                //
                if (empty($rec["peso"])) {
                    $rec["peso"]=0;
                }
                if (empty($rec["talla"])) {
                    $rec["talla"]=0;
                }
                // type of insemination
                $fivicsi="";
                if ($rec['p_fiv'] >= 1 && $rec['p_icsi'] >= 1) {
                    $fivicsi="FIV, ICSI";
                }
                if ($rec['p_fiv'] >= 1) {
                    $fivicsi="FIV";
                }
                if ($rec['p_icsi'] >= 1) {
                    $fivicsi="ICSI";
                }
                // capacitacion espermatica
                $spermsource=$freshcryopreserved="";
                switch ($rec['mue']) {
                    case 1: $freshcryopreserved = "Fresh"; $spermsource = "Ejaculated Sperm";  break;
                    case 2: $freshcryopreserved = "Fresh"; $spermsource = "Donated Sperm"; break;
                    case 3: $freshcryopreserved = "Cryopreserved"; $spermsource = "Ejaculated Sperm";  break;
                    case 4: $freshcryopreserved = "Cryopreserved"; $spermsource = "Donated Sperm"; break;
                    default: break;
                }
                //ngs
                $ngs=$blastocyst="";
                if (strpos($rec['pago_extras'], 'NGS') !== false or strpos($rec['pago_extras'], 'banking') !== false) {
                    $ngs="Yes";
                    $blastocyst="Blastocyst";
                } else {
                    $ngs="No";
                }
                // 
                $ngsfiles="No";
                if (file_exists("analisis/ngs_".$rec['pro'].".pdf")) {
                    $ngsfiles="Yes";
                }
            	print("
                <tr>
                    <td>".$rec["pro"]."</td>
                    <td>".$rec["med"]."</td>
                    <td>".$rec["tipodocumentoidentidad"]."</td>
                    <td>".$rec["dni"]."</td>
                    <td>".mb_strtoupper($rec["ape"])."</td>
                    <td>".mb_strtoupper($rec["nom"])."</td>
                    <td>$procinmater</td>
            		<td>-</td>
            		<td>".mb_strtoupper($rec["beta"])."</td>
            		<td>$procinmater</td>
            		<td>".substr($rec["fec"], 0, 10)."</td>
            		<td>-</td>
            		<td>".$rec["fnac"]."</td>
                    <td>".$rec["peso"]."</td>
                    <td>".$rec["talla"]."</td>
                    <td>-</td>
                    <td>-</td>
                    <td>antagonist</td>
                    <td>not available</td>
                    <td>not available</td>
                    <td></td>
                    <td>not available</td>
                    <td>".$rec["n_ovo"]."</td>
                    <td>$fivicsi</td>
                    <td>".$rec["n_ins"]."</td>
                    <td>$spermsource</td>
                    <td>$freshcryopreserved</td>
                    <td>".$rec["fecundado"]."</td>
                    <td>$ngsfiles</td>
                    <td>$ngs</td>
                    <td>$blastocyst</td>
                    <td>".$rec["crios"]."</td>
                    <td>".$rec["normales"]."</td>
                    <td>-</td>
                    <td>".$rec["transferidos"]."</td>
                    <td>".$rec["crios"]."</td>
                    <td>Blastocyst</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
        		</tr>");
            }
            ?>
            <div class="ui-content" role="main" id="imprime">
                <div style="float:right">
                    <p><b>Fecha y Hora de Reporte:</b>
                        <?php
                            date_default_timezone_set('America/Lima');
                            print(date("Y-m-d H:i:s")."<br>");
                            print("<b>Total Procedimientos:</b> ".$rRec->rowCount()."<br>");
                            print("<b>Descargar:</b> <a href='#' onclick=\"tableToExcel('repo_model_01', 'redlara_od')\" class='ui-btn ui-mini ui-btn-inline'><img src=\"_images/excel.png\" height='20' width='20' alt='excel'></a>");
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