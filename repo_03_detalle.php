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
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Reporte FIV/ ICSI - Betas</li>
            </ol>
        </nav>
            <?php
                $fecha = $between = $med = $embins = $ovo = $ini = $fin = $ini_fivicsi = $fin_fivicsi = $tipo_transferencia = "";

                if (isset($_GET) && !empty($_GET)) {
                    if (isset($_GET['repo']) && isset($_GET['estado'])) {
                        $repo = $_GET['repo'];
                        $estado = $_GET['estado'];
                        $condicion = $condicion1 = "";

                        switch ($repo) {
                            case 'fiv':
                                $condicion = "and r.p_fiv = 1";
                                $condicion1 = "and r1.p_fiv = 1";
                                break;
                            case 'icsi':
                                $condicion = "and r.p_icsi = 1";
                                $condicion1 = "and r1.p_icsi = 1";
                                break;
                            case 'nofivicsi':
                                $condicion = "and coalesce(r.p_icsi, 0) = 0 and coalesce(r.p_fiv, 0) = 0";
                                $condicion1 = "and coalesce(r1.p_icsi, 0) = 0 and coalesce(r1.p_fiv, 0) = 0";
                                break;
                            default: break;
                        }

                        $between .= " and (
	                        (r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') $condicion and coalesce(t.beta, 0)=$estado)
	                        or (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') $condicion1 and coalesce(t.beta, 0)=$estado))";
                    }

                    if (isset($_GET["fecha"]) && !empty($_GET["fecha"]) && isset($_GET["ini"]) && !empty($_GET["ini"]) && isset($_GET["fin"]) && !empty($_GET["fin"])) {
                        $fecha = $_GET["fecha"];
                        $ini = $_GET['ini'];
                        $fin = $_GET['fin'];

                        switch ($fecha) {
                            case 'ftra':
                            $between .= " and (
                                (r.f_tra is not null and CAST(r.f_tra as date) between '$ini' and '$fin') or
                                (r.f_tra is null and CAST(r.f_iny as date) between '$ini' and '$fin'))"; break;
                            case 'fasp':
                            $between .= " and (
                                (r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and CAST(r.f_asp as date) between '$ini' and '$fin') or
                                (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and CAST(r1.f_asp as date) between '$ini' and '$fin'))"; break;
                            default: break;
                        }
                    }

                    if (isset($_GET["edad"]) && !empty($_GET["edad"]) && isset($_GET["desde"]) && !empty($_GET["desde"]) && isset($_GET["hasta"]) && !empty($_GET["hasta"])) {
                        $edad = $_GET["edad"];
                        $desde = $_GET['desde'];
                        $hasta = $_GET['hasta'];

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

                    if (isset($_GET["tipo_transferencia"]) && !empty($_GET["tipo_transferencia"])) {
                        $tipo_transferencia = $_GET['tipo_transferencia'];

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

                    if (isset($_GET["med"]) && !empty($_GET["med"])) {
                        $med = $_GET['med'];
                        $between .= " and r.med = '$med'";
                    }

                    if (isset($_GET["embins"]) && !empty($_GET["embins"])) {
                        $embins = $_GET['embins'];
                        $between .= " and ((r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and a.emb0 = '$embins') or (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and a1.emb0 = '$embins'))";
                    }

                    if (isset($_GET["ovo"]) && !empty($_GET["ovo"])) {
                        $ovo = $_GET['ovo'];
                        $between .= " and ((r.pago_extras ilike ('%TRANSFERENCIA FRESCO%') and a.o_ovo = '$ovo') or (r.pago_extras not ilike ('%TRANSFERENCIA FRESCO%') and a1.o_ovo = '$ovo'))";
                    }

                    if (isset($_GET["ngs"]) && !empty($_GET["ngs"])) {
                        $ngs = $_GET['ngs'];

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
                }

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
                        </small>'); ?>
                </h5>
                <?php
                print('
                    <table class="table table-responsive table-bordered align-middle" style="height:70vh; margin-bottom: 0 !important;">
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
                                <th width="10%" class="text-center align-middle">Beta</th>
                            </tr>
                        </thead>
                        <tbody>');
                $item = 1;
                $var0 = $var1 = $var01 = $var2 = $var3 = $var23 = $var4 = $var5 = $var45 = $var6 = $var7 = $var67 = $var8 = $var9 = $var89 = 0;

                while ($pacibet = $rPaciBet->fetch(PDO::FETCH_ASSOC)) {

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
                        <td class="text-center">');
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
                        <td></td>');
                    print('</tr>');
                }

                print('
                </tbody>
                    </table>'); ?>
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