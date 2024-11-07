<!DOCTYPE HTML>
<html>
<head>
<?php
     include 'seguridad_login.php';
    
    $key=$_ENV["apikey"];
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <style>
        .scroll_h {
            overflow: auto;
        }

        #alerta {
            background-color: #FF9;
            margin: 0 auto;
            text-align: center;
            padding: 4px;
        }

        .mayuscula {
            text-transform: uppercase;
            font-size: small;
        }

        .Mostrar {
            background-color: #F0DF96 !important;
        }

        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div data-role="page" class="ui-responsive-panel">
        <div data-role="header">
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
							<?php
							if (isset($_GET["path"]) && !empty($_GET["path"])) {
								print('<a href="'.$_GET["path"].'.php" class="ui-btn ui-btn-c ui-icon-home ui-btn-icon-left" rel="external">Inicio</a>');
							} else {
								print('<a href="lista.php" class="ui-btn ui-btn-c ui-icon-home ui-btn-icon-left" rel="external">Inicio</a>');
							} ?>
            </div>
            <h1>DATA</h1>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div>
        <div class="ui-content" role="main">
            <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
            <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
            <form action="" method="post" data-ajax="false" id="form1">
                <div class="enlinea">
                    Mostrar Desde<input name="ini" type="date" id="ini" value="<?php echo $_POST['ini']; ?>" data-mini="true" required>
                    Hasta<input name="fin" type="date" id="fin" value="<?php echo $_POST['fin']; ?>" data-mini="true" required>
                    <input type="Submit" name="Mostrar" value="Mostrar" data-mini="true" data-theme="b" data-inline="true"/>
                </div>
                <?php
                if ($_POST['Mostrar'] == 'Mostrar' && $_POST['ini'] <> "" && $_POST['fin'] <> "") { ?>
                    <div class="scroll_h">
                        <table style="margin:0 auto;text-align: center; border: 1px solid;" cellpadding="5">
                            <thead>
                                <tr>
                                    <th colspan="5" bgcolor="#ffe4e1">Aspiraciones/ Pacientes Aspirados</th>
                                    <th colspan="2" bgcolor="#ffe4c4">Inseminación</th>
                                    <th colspan="2" bgcolor="#e6e6fa">Desarrollo embrionario Extras</th>
                                    <th colspan="2" bgcolor="#f5f5dc">Crio preservación</th>
                                    <th colspan="2" bgcolor="#add8e6">Transferencia</th>
                                </tr>
                                <tr align="center">
                                    <td bgcolor="#ffe4e1">Paciente</td>
                                    <td bgcolor="#ffe4e1">Donante</td>
                                    <td bgcolor="#ffe4e1">Receptora</td>
                                    <td bgcolor="#ffe4e1">Crio ovos Paciente</td>
                                    <td bgcolor="#ffe4e1">Crio ovos Donante</td>
                                    <td bgcolor="#ffe4c4">FIV</td>
                                    <td bgcolor="#ffe4c4"><?php print($_ENV["VAR_ICSI"]); ?></td>
                                    <td bgcolor="#e6e6fa">NGS</td>
                                    <td bgcolor="#e6e6fa">Embryoscope</td>
                                    <td bgcolor="#f5f5dc">Ovulos</td>
                                    <td bgcolor="#f5f5dc">Embriones</td>
                                    <td bgcolor="#add8e6">Propios</td>
                                    <td bgcolor="#add8e6">Embriodonación</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $asp2 = $asp3 = $asp4 = $asp5 = $ins1 = $ins2 = $des1 = $des2 = $cri1 = $cri2 = $tra1 = $tra2 = 0;

                                $rPaci = $db->prepare("
                                SELECT
                                sum(case when (b.tip = 'D' or a.don_todo = 1) and (a.p_cri<>1 or a.p_cri is null) then 1 else 0 end) aspiracion_donante,
                                sum(case when (b.tip = 'R') then 1 else 0 end) aspiracion_receptora,
                                sum(case when (b.tip = 'P' and a.p_cri = 1) then 1 else 0 end) aspiracion_crio_paciente,
                                sum(case when (b.tip = 'D' and a.p_cri = 1) then 1 else 0 end) aspiracion_crio_donante,
                                sum(case when (b.tip = 'P' or b.tip = 'R') and (a.p_fiv=1) then 1 else 0 end) inseminacion_fiv,
                                sum(case when (b.tip = 'P' or b.tip = 'R') and (a.p_icsi=1) then 1 else 0 end) inseminacion_icsi,
                                sum(case when (b.tip = 'P' or b.tip = 'R') and a.pago_extras ilike('%ngs%') then 1 else 0 end) desarrollo_ngs,
                                sum(case when (b.tip = 'P' or b.tip = 'R') and a.pago_extras ilike('%EMBRYOSCOPE%') then 1 else 0 end) desarrollo_embryoscope
                                FROM hc_reprod a
                                LEFT JOIN lab_aspira b ON a.id = b.rep and b.estado is true
                                WHERE a.estado = true and cancela=0 AND b.fec::date BETWEEN ? AND ?");
                                $rPaci->execute(array($_POST['ini'], $_POST['fin']));
                                $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
                                $asp2 = $paci['aspiracion_donante'];
                                $asp3 = $paci['aspiracion_receptora'];
                                $asp4 = $paci['aspiracion_crio_paciente'];
                                $asp5 = $paci['aspiracion_crio_donante'];
                                $ins1 = $paci['inseminacion_fiv'];
                                $ins2 = $paci['inseminacion_icsi'];
                                $des1 = $paci['desarrollo_ngs'];
                                $des2 = $paci['desarrollo_embryoscope'];

                                $Rcri1 = $db->prepare("
                                SELECT
                                lab_aspira_dias.pro
                                FROM lab_aspira_dias
                                INNER JOIN lab_aspira ON lab_aspira_dias.pro=lab_aspira.pro and lab_aspira.estado is true
                                WHERE lab_aspira.fec::date BETWEEN ? AND ? AND d0f_cic='C' and lab_aspira_dias.estado is true
                                GROUP BY lab_aspira_dias.pro");
                                $Rcri1->execute(array($_POST['ini'], $_POST['fin']));
                                $cri1 = $Rcri1->rowCount();

                                $Rcri2 = $db->prepare("
                                SELECT
                                lab_aspira_dias.pro
                                FROM lab_aspira_dias
                                INNER JOIN lab_aspira ON lab_aspira_dias.pro=lab_aspira.pro and lab_aspira.estado is true
                                WHERE lab_aspira.fec::date BETWEEN ? AND ? AND (d6f_cic='C' OR d5f_cic='C' OR d4f_cic='C' OR d3f_cic='C' OR d2f_cic='C') and lab_aspira_dias.estado is true
                                GROUP BY lab_aspira_dias.pro");
                                $Rcri2->execute(array($_POST['ini'], $_POST['fin']));
                                $cri2 = $Rcri2->rowCount();

                                $rTra = $db->prepare("
                                select
                                count(case when not(a.des_don is not null and a.des_dia >= 1) then true end) tra1,
                                count(case when a.des_don is not null and a.des_dia >= 1 then true end) tra2
                                from hc_reprod a
                                inner join lab_aspira b on b.rep=a.id and b.estado is true
                                inner join lab_aspira_t c on c.pro=b.pro and c.estado is true
                                where a.estado = true and b.fec::date BETWEEN ? AND ?");

                                $rTra->execute(array($_POST['ini'], $_POST['fin']));
                                $tra = $rTra->fetch(PDO::FETCH_ASSOC);
                                $tra1 = $tra['tra1'];
                                $tra2 = $tra['tra2'];
                                $total_aspirados=0; 

                                $consulta = $db->prepare("
                                    SELECT COUNT(DISTINCT a.dni) total_aspirados
                                    FROM hc_reprod a
                                    LEFT JOIN lab_aspira b ON a.id = b.rep and b.estado is true
                                    WHERE a.estado = true and a.cancela = 0 
                                        AND b.fec::date BETWEEN :fecha_inicio AND :fecha_fin 
                                        AND (
                                            (b.tip = 'P' OR b.tip = 'R') AND a.p_fiv = 1
                                            OR (b.tip = 'P' OR b.tip = 'R') AND a.p_icsi = 1
                                            OR (b.tip = 'D' OR a.don_todo = 1) AND (a.p_cri <> 1 OR a.p_cri IS NULL)
                                            OR (b.tip = 'P' AND a.p_cri = 1)
                                            OR (b.tip = 'D' AND a.p_cri = 1)
                                        )
                                ");
                                        
                                $consulta->execute(array(
                                    ':fecha_inicio' => $_POST['ini'],
                                    ':fecha_fin' => $_POST['fin']
                                ));
                                
                                $data = $consulta->fetch(PDO::FETCH_ASSOC);
                                $total_aspirados = $data['total_aspirados'];


                                // 
                                print('
                                <tr align="center">
                                    <td bgcolor="#ffe4e1">'.($ins1 + $ins2).'</td>
                                    <td bgcolor="#ffe4e1">'.$asp2.'</td>
                                    <td bgcolor="#ffe4e1">'.$asp3.'</td>
                                    <td bgcolor="#ffe4e1">'.$asp4.'</td>
                                    <td bgcolor="#ffe4e1">'.$asp5.'</td>
                                    <td bgcolor="#ffe4c4">'.$ins1.'</td>
                                    <td bgcolor="#ffe4c4">'.$ins2.'</td>
                                    <td bgcolor="#e6e6fa">'.$des1.'</td>
                                    <td bgcolor="#e6e6fa">'.$des2.'</td>
                                    <td bgcolor="#f5f5dc">'.$cri1.'</td>
                                    <td bgcolor="#f5f5dc">'.$cri2.'</td>
                                    <td bgcolor="#add8e6">'.$tra1.'</td>
                                    <td bgcolor="#add8e6">'.$tra2.'</td>
                                </tr>');
                                ?>
                                <tr>
                                    <td colspan="5" bgcolor="#ffe4e1"><?php print(($ins1 + $ins2 + $asp2 + $asp4 + $asp5)."/ ".$total_aspirados); ?></td>
                                    <td colspan="2" bgcolor="#ffe4c4"><?php print($ins1 + $ins2); ?></td>
                                    <td colspan="2" bgcolor="#e6e6fa"><?php print($des1 + $des2); ?></td>
                                    <td colspan="2" bgcolor="#f5f5dc"><?php print($cri1 + $cri2); ?></td>
                                    <td colspan="2" bgcolor="#add8e6"><?php print($tra1 + $tra2); ?></td>
                                </tr>
                            </tbody>
                        </table><br/><br/>
                        <?php
                        $rUser = $db->prepare("SELECT userX, nom FROM usuario WHERE role=1");
                        $rUser->execute();

                        while ($user = $rUser->fetch(PDO::FETCH_ASSOC)) { ?>
                            <table style="margin:0 auto;text-align: center; border: 1px solid;" cellpadding="5">
                                <thead>
                                    <tr><th colspan="13"><?php echo $user['nom']; ?></th></tr>
                                    <tr>
                                        <th colspan="5" bgcolor="#ffe4e1">Aspiraciones/ Pacientes Aspirados</th>
                                        <th colspan="2" bgcolor="#ffe4c4">Inseminación</th>
                                        <th colspan="2" bgcolor="#e6e6fa">Desarrollo embrionario Extras</th>
                                        <th colspan="2" bgcolor="#f5f5dc">Crio preservación</th>
                                        <th colspan="2" bgcolor="#add8e6">Transferencia</th>
                                    </tr>
                                    <tr align="center">
                                        <td bgcolor="#ffe4e1">Paciente</td>
                                        <td bgcolor="#ffe4e1">Donante</td>
                                        <td bgcolor="#ffe4e1">Receptora</td>
                                        <td bgcolor="#ffe4e1">Crio ovos Paciente</td>
                                        <td bgcolor="#ffe4e1">Crio ovos Donante</td>
                                        <td bgcolor="#ffe4c4">FIV</td>
                                        <td bgcolor="#ffe4c4"><?php print($_ENV["VAR_ICSI"]); ?></td>
                                        <td bgcolor="#e6e6fa">NGS</td>
                                        <td bgcolor="#e6e6fa">Embryoscope</td>
                                        <td bgcolor="#f5f5dc">Ovulos</td>
                                        <td bgcolor="#f5f5dc">Embriones</td>
                                        <td bgcolor="#add8e6">Propios</td>
                                        <td bgcolor="#add8e6">Embriodonación</td>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $asp2 = $asp3 = $asp4 = $asp5 = $ins1 = $ins2 = $des1 = $des2 = $cri1 = $cri2 = $tra1 = $tra2 = 0;

                                $rPaci = $db->prepare("
                                    SELECT
                                        SUM(CASE WHEN (b.tip = 'D' OR a.don_todo = 1) AND (a.p_cri<>1 OR a.p_cri IS NULL) THEN 1 ELSE 0 END) aspiracion_donante,
                                        SUM(CASE WHEN b.tip = 'R' THEN 1 ELSE 0 END) aspiracion_receptora,
                                        SUM(CASE WHEN b.tip = 'P' AND a.p_cri = 1 THEN 1 ELSE 0 END) aspiracion_crio_paciente,
                                        SUM(CASE WHEN b.tip = 'D' AND a.p_cri = 1 THEN 1 ELSE 0 END) aspiracion_crio_donante,
                                        SUM(CASE WHEN (b.tip = 'P' OR b.tip = 'R') AND (a.p_fiv=1) THEN 1 ELSE 0 END) inseminacion_fiv,
                                        SUM(CASE WHEN (b.tip = 'P' OR b.tip = 'R') AND (a.p_icsi=1) THEN 1 ELSE 0 END) inseminacion_icsi,
                                        SUM(CASE WHEN (b.tip = 'P' OR b.tip = 'R') AND a.pago_extras ILIKE('%ngs%') THEN 1 ELSE 0 END) desarrollo_ngs,
                                        SUM(CASE WHEN (b.tip = 'P' OR b.tip = 'R') AND a.pago_extras ILIKE('%EMBRYOSCOPE%') THEN 1 ELSE 0 END) desarrollo_embryoscope
                                    FROM hc_reprod a
                                    LEFT JOIN lab_aspira b ON a.id = b.rep and b.estado is true
                                    WHERE a.estado = true and cancela=0 AND a.med=:med AND b.fec::date BETWEEN :fecha_inicio AND :fecha_fin
                                ");
                                                        
                                $rPaci->execute(array(
                                    ':med' => $user['userx'],
                                    ':fecha_inicio' => $_POST['ini'],
                                    ':fecha_fin' => $_POST['fin']
                                ));
                                $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
                                $asp2 = $paci['aspiracion_donante'];
                                $asp3 = $paci['aspiracion_receptora'];
                                $asp4 = $paci['aspiracion_crio_paciente'];
                                $asp5 = $paci['aspiracion_crio_donante'];
                                $ins1 = $paci['inseminacion_fiv'];
                                $ins2 = $paci['inseminacion_icsi'];
                                $des1 = $paci['desarrollo_ngs'];
                                $des2 = $paci['desarrollo_embryoscope'];

                                $Rcri1 = $db->prepare("
                                    SELECT lab_aspira_dias.pro
                                    FROM hc_reprod
                                    INNER JOIN lab_aspira ON hc_reprod.id = lab_aspira.rep and lab_aspira.estado is true
                                    INNER JOIN lab_aspira_dias ON lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
                                    WHERE hc_reprod.estado = true and hc_reprod.med = :med 
                                        AND lab_aspira.fec::date BETWEEN :fecha_inicio AND :fecha_fin 
                                        AND d0f_cic = 'C'
                                    GROUP BY lab_aspira_dias.pro
                                ");

                                $Rcri1->execute(array(
                                    ':med' => $user['userx'],
                                    ':fecha_inicio' => $_POST['ini'],
                                    ':fecha_fin' => $_POST['fin']
                                ));

                                $cri1 = $Rcri1->rowCount();

                                $Rcri2 = $db->prepare("
                                    SELECT lab_aspira_dias.pro
                                    FROM hc_reprod
                                    INNER JOIN lab_aspira ON hc_reprod.id = lab_aspira.rep and lab_aspira.estado is true
                                    INNER JOIN lab_aspira_dias ON lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
                                    WHERE hc_reprod.estado = true and hc_reprod.med = :med 
                                        AND lab_aspira.fec::date BETWEEN :fecha_inicio AND :fecha_fin 
                                        AND (d6f_cic='C' OR d5f_cic='C' OR d4f_cic='C' OR d3f_cic='C' OR d2f_cic='C')
                                    GROUP BY lab_aspira_dias.pro
                                ");

                                $Rcri2->execute(array(
                                    ':med' => $user['userx'],
                                    ':fecha_inicio' => $_POST['ini'],
                                    ':fecha_fin' => $_POST['fin']
                                ));

                                $cri2 = $Rcri2->rowCount();

                                $rTra = $db->prepare("
                                    SELECT
                                        SUM(CASE WHEN a.des_don IS NULL OR a.des_dia < 1 THEN 1 ELSE 0 END) tra1,
                                        SUM(CASE WHEN a.des_don IS NOT NULL AND a.des_dia >= 1 THEN 1 ELSE 0 END) tra2
                                    FROM hc_reprod a
                                    INNER JOIN lab_aspira b ON b.rep = a.id and b.estado is true
                                    INNER JOIN lab_aspira_t c ON c.pro = b.pro and c.estado is true
                                    WHERE a.estado = true and a.med = :med AND b.fec::date BETWEEN :fecha_inicio AND :fecha_fin
                                ");

                                $rTra->execute(array(
                                    ':med' => $user['userx'],
                                    ':fecha_inicio' => $_POST['ini'],
                                    ':fecha_fin' => $_POST['fin']
                                ));

                                $tra = $rTra->fetch(PDO::FETCH_ASSOC);
                                $tra1 = $tra['tra1'];
                                $tra2 = $tra['tra2'];
                                $total_aspirados = 0;

                                $consulta = $db->prepare("
                                    SELECT COUNT(DISTINCT a.dni) total_aspirados
                                    FROM hc_reprod a
                                    LEFT JOIN lab_aspira b ON a.id = b.rep and b.estado is true
                                    WHERE a.estado = true and a.cancela = 0 
                                        AND a.med = :med 
                                        AND b.fec::date BETWEEN :fecha_inicio AND :fecha_fin 
                                        AND (
                                            (b.tip = 'P' OR b.tip = 'R') AND a.p_fiv = 1
                                            OR (b.tip = 'P' OR b.tip = 'R') AND a.p_icsi = 1
                                            OR (b.tip = 'D' OR a.don_todo = 1) AND (a.p_cri <> 1 OR a.p_cri IS NULL)
                                            OR (b.tip = 'P' AND a.p_cri = 1)
                                            OR (b.tip = 'D' AND a.p_cri = 1)
                                        )
                                ");
                                        
                                $consulta->execute(array(
                                    ':med' => $user['userx'],
                                    ':fecha_inicio' => $_POST['ini'],
                                    ':fecha_fin' => $_POST['fin']
                                ));

                                $data = $consulta->fetch(PDO::FETCH_ASSOC);
                                $total_aspirados = $data['total_aspirados'];

                                print '
                                <tr align="center">
                                    <td bgcolor="#ffe4e1">'.($ins1 + $ins2).'</td>
                                    <td bgcolor="#ffe4e1">'.$asp2.'</td>
                                    <td bgcolor="#ffe4e1">'.$asp3.'</td>
                                    <td bgcolor="#ffe4e1">'.$asp4.'</td>
                                    <td bgcolor="#ffe4e1">'.$asp5.'</td>
                                    <td bgcolor="#ffe4c4">'.$ins1.'</td>
                                    <td bgcolor="#ffe4c4">'.$ins2.'</td>
                                    <td bgcolor="#e6e6fa">'.$des1.'</td>
                                    <td bgcolor="#e6e6fa">'.$des2.'</td>
                                    <td bgcolor="#f5f5dc">'.$cri1.'</td>
                                    <td bgcolor="#f5f5dc">'.$cri2.'</td>
                                    <td bgcolor="#add8e6">'.$tra1.'</td>
                                    <td bgcolor="#add8e6">'.$tra2.'</td>
                                </tr>';
                                ?>
                                <tr>
                                    <td colspan="5" bgcolor="#ffe4e1"><?php print(($ins1 + $ins2 + $asp2 + $asp4 + $asp5)."/ ".$total_aspirados); ?></td>
                                    <td colspan="2" bgcolor="#ffe4c4"><?php echo $ins1 + $ins2; ?></td>
                                    <td colspan="2" bgcolor="#e6e6fa"><?php echo $des1 + $des2; ?></td>
                                    <td colspan="2" bgcolor="#f5f5dc"><?php echo $cri1 + $cri2; ?></td>
                                    <td colspan="2" bgcolor="#add8e6"><?php echo $tra1 + $tra2; ?></td>
                                </tr>
                                </tbody>
                            </table><br/>
                        <?php } ?>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div>
<script>
    $(function () {
        $('#alerta').delay(3000).fadeOut('slow');
    });

    $("#form1").submit(function(e) {
        var nombre_modulo="data";
        var ruta="perfil_laboratorio/data.php";
        var tipo_operacion="consulta";
        var login=$('#login').val();
        var key=$('#key').val();
				var clave='';
				var valor='';
        $.ajax({
            type: 'POST',
            dataType: "json",
            contentType: "application/json",
            url: '_api_inmater/servicio.php',
            data:JSON.stringify({ nombre_modulo: nombre_modulo, ruta: ruta,tipo_operacion:tipo_operacion,clave:clave,valor:valor,idusercreate:login,apikey:key }),
            // processData: false,  // tell jQuery not to process the data
            // contentType: false,   // tell jQuery not to set contentType
            success: function(result) {
                console.log(result);
            }
        });
    });
</script>
</body>
</html>