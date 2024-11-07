<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>

    <script>
        function Beta(beta,pro) {
            document.form2.val_beta.value=beta.value;
        	document.form2.pro_beta.value=pro;
        	document.form2.submit();
        }
    </script>

    <style>
        .controlgroup-textinput {
            padding-top: .10em;
            padding-bottom: .10em;
        }
        .sin-cita {
            background-color: #FFEBCD!important;
            cursor: pointer;
        }

        .con-cita {
            background: #EEC584!important;
            cursor: pointer;
        }

        .con-ecografia {
            background: #B6CB9E!important;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" data-dialog="true">
    <?php
    if (isset($_POST['boton_datos']) && $_POST['boton_datos'] == "AGENDAR CONSULTA" and isset($_POST['dni']) and isset($_POST['fec']) and isset($_POST['fec_h'])) {
        insertBetaCitaEco($_POST['dni'], $_POST['pro'], $_POST['fec'], $login, $_POST['fec_h'], $_POST['fec_m'], $_POST['mot']);
    }

    if (isset($_POST['val_beta']) && !empty($_POST['val_beta']) && isset($_POST['pro_beta']) && !empty($_POST['pro_beta'])) {
        $stmt = $db->prepare("UPDATE lab_aspira_t SET beta=?, iduserupdate=? where pro=? and estado is true and estado is true");
        $stmt->execute(array($_POST['val_beta'], $login, $_POST['pro_beta']));
        print("<div id='alerta'> BETA Guardado! </div>");
    }	

    $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC);

    $porMed = " and lab_aspira_t.med='" . $login . "'";

    $between=$t_med=$t_emb=$tipa=$edesde=$ehasta=$ngs="";

    if (isset($_POST) && !empty($_POST)) {
        if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
            $between.="
            and case lab_aspira_t.dia
                when 6 then lab_aspira.fec6
                when 5 then lab_aspira.fec5
                when 4 then lab_aspira.fec4
                when 3 then lab_aspira.fec3
                when 2 then lab_aspira.fec2
                else null end between '".$_POST["ini"]."' and '".$_POST["fin"]."'
            ";
        }

        if ( isset($_POST["edesde"]) && !empty($_POST["edesde"]) && isset($_POST["ehasta"]) && !empty($_POST["ehasta"]) ) {
            $edesde = $_POST['edesde']*365;
            $ehasta = $_POST['ehasta']*365;
            $between = " and datediff(lab_aspira.fec, hc_paciente.fnac) between $edesde and $ehasta";
            $url = "?edesde=$edesde&ehasta=$ehasta";
        }

        if (isset($_POST["ngs"]) and !empty($_POST["ngs"])) {
            $ngs=$_POST["ngs"];
            if ($ngs=="s") {
                $between.=" and hc_reprod.pago_extras ilike '%ngs%'";
            } else {
                $between.=" and hc_reprod.pago_extras not ilike '%ngs%'";
            }
        }

        if (isset($_POST["t_med"]) and !empty($_POST["t_med"])) {
            $t_med=$_POST["t_med"];
            $between.=" and lab_aspira_t.med = '".$t_med."'";
        }

        if (isset($_POST["t_emb"]) and !empty($_POST["t_emb"])) {
            $t_emb=$_POST["t_emb"];
            $between.=" and lab_aspira_t.emb = '".$t_emb."'";
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

        if( isset($_POST['estado_beta']) and !empty( $_POST['estado_beta'] ) ) {
            switch ($_POST['estado_beta']) {
                case 'nuevo':
                    $between .= " and hc_eco_beta_positivo.fec is null";
                    break;
                case 'con-cita':
                    $between .= " and hc_eco_beta_positivo.efec = '1899-12-30'";
                    break;
                case 'con-ecografia':
                    $between .= " and hc_eco_beta_positivo.efec != '1899-12-30' and hc_eco_beta_positivo.efec is not null";
                    break;
                
                default:
                    break;
            }
        }
    }

    $rPaci = $db->prepare("SELECT
        hc_paciente.dni, ape, nom, hc_paciente.med,
        lab_aspira.pro, lab_aspira.tip, lab_aspira.dias,
        lab_aspira.fec2, lab_aspira.fec3, lab_aspira.fec4, lab_aspira.fec5, lab_aspira.fec6,
        lab_aspira_t.beta, lab_aspira_t.dia,
        CONCAT(hc_eco_beta_positivo.fec, ' ', hc_eco_beta_positivo.fec_h, ':', hc_eco_beta_positivo.fec_m) as cita_eco_beta,
        hc_eco_beta_positivo.id as idecobetapositivo,
        hc_eco_beta_positivo.sacogestacional as sacogestacional,
        hc_eco_beta_positivo.siguiente_ecografia as siguiente_ecografia,
        hc_eco_beta_positivo.condicion as condicion,
        hc_eco_beta_positivo.efec as fechaecografia,
        hc_eco_beta_positivo.confirmacion as confirmacion
        FROM hc_paciente,lab_aspira,hc_reprod,lab_aspira_t
        LEFT JOIN hc_eco_beta_positivo ON lab_aspira_t.pro = hc_eco_beta_positivo.pro
        WHERE hc_reprod.estado = true and lab_aspira.estado is true and hc_paciente.dni=lab_aspira.dni and hc_reprod.id=lab_aspira.rep and lab_aspira_t.beta = 1 and lab_aspira_t.pro=lab_aspira.pro" . $porMed . $between . "
        and IF(hc_eco_beta_positivo.id is null or (hc_eco_beta_positivo.sacogestacional > 0 or hc_eco_beta_positivo.efec = '1899-12-30'), 1, 0) = 1
        GROUP BY lab_aspira_t.pro
        ORDER BY lab_aspira_t.beta ASC
        ");
    $rPaci->execute();
    $medico="";

    if (isset($_GET['med']) && !empty($_GET['med'])) {
        $medico=$_GET['med'];
    } ?>

    <style>
        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }
        .ui-dialog-contain {
          	max-width: 1000px;
        	margin: 1% auto 1%;
        	padding: 0;
        	position: relative;
        	top: -35px;
        }
        .col0 { background-color:#FFFF91 !important; }
        .col1 { background-color:#FFEBCD !important; }
        #alerta { background-color:#FF9;margin: 0 auto; text-align:center; padding:4px;}
    </style>

    <div data-role="header" data-position="fixed">
        <a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
        <h2>Betas Positivo</h2>
    </div>
    
    <div class="ui-content" role="main">
        <div data-role="tabs">
            <div data-role="navbar">
                <ul>
                    <li><a id="tab-lista" href="#one" data-ajax="false" class="ui-btn-active ui-btn-icon-left ui-icon-bullets">Lista</a></li>
                    <li><a id="tab-agendar" href="#two" data-ajax="false" class="ui-btn-icon-left ui-icon-edit ui-disabled">Consulta</a></li>
                </ul>
            </div>

            <form id="one" action="" method="post" data-ajax="false" name="form2">
                <input type="hidden" name="val_beta">
                <input type="hidden" name="pro_beta">
                <div class="enlinea">
                    Mostrar Desde<input name="ini" type="date" id="ini" value="<?php echo $_POST['ini'] ?>" data-mini="true">
                    Hasta<input name="fin" type="date" id="fin" value="<?php echo $_POST['fin'] ?>" data-mini="true">

                    <br><div class="col-12 col-sm-12 col-md-12 col-lg-2">
                        Edad cumplida:
                        <div>
                            <input class="form-control" name="edesde" type="number" value="<?php echo $_POST['edesde'] ?>" data-mini="true">
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                        Y menor a:
                        <div>
                            <input class="form-control" name="ehasta" type="number" value="<?php echo $_POST['ehasta'] ?>" data-mini="true">
                        </div>
                    </div>

                    <br><div class="col-12 col-sm-12 col-md-12 col-lg-2">
                        Tipo Paciente
                        <select name='tipa' data-mini="true">
                            <option value='' >todos</option>
                            <option value='P' <?php if($tipa == "P") print("selected") ?>>Paciente</option>
                            <option value='R' <?php if($tipa == "R") print("selected") ?>>Receptor</option>
                            <option value='D' <?php if($tipa == "D") print("selected") ?>>Donante</option>
                        </select>
                    </div>


                    <br><div class="col-12 col-sm-12 col-md-12 col-lg-2">
                        Realizó NGS
                        <select name='ngs' data-mini="true">
                            <option value='' >Seleccionar</option>
                            <option value='s' <?php if($ngs == "s") print("selected") ?>>Si</option>
                            <option value='n' <?php if($ngs == "n") print("selected") ?>>No</option>
                        </select>
                    </div>

                    <br>Médico Transferencia
                    <select name="t_med" id="t_med" data-mini="true">
                        <option value="">Todos</option>
                        <option value="mvelit" <?php if($t_med=="mvelit") { print('selected'); } ?>>mvelit</option>
                        <option value="humanidad" <?php if($t_med=="humanidad") { print('selected'); } ?>>humanidad</option>
                        <option value="eescudero" <?php if($t_med=="eescudero") { print('selected'); } ?>>eescudero</option>
                        <option value="mascenzo" <?php if($t_med=="mascenzo") { print('selected'); } ?>>mascenzo</option>
                        <option value="rbozzo" <?php if($t_med=="rbozzo") { print('selected'); } ?>>rbozzo</option>
                        <option value="medico1" <?php if($t_med=="medico1") { print('selected'); } ?>>medico1</option>
                        <option value="cbonomini" <?php if($t_med=="cbonomini") { print('selected'); } ?>>cbonomini</option>
                        <option value="tacna" <?php if($t_med=="tacna") { print('selected'); } ?>>tacna</option>
                        <option value="lbernuy" <?php if($t_med=="lbernuy") { print('selected'); } ?>>lbernuy</option>
                        <option value="cosorio" <?php if($t_med=="cosorio") { print('selected'); } ?>>cosorio</option>
                        <option value="jolivas" <?php if($t_med=="jolivas") { print('selected'); } ?>>jolivas</option>
                        <option value="apuertas" <?php if($t_med=="apuertas") { print('selected'); } ?>>apuertas</option>
                        <option value="jtremolada" <?php if($t_med=="jtremolada") { print('selected'); } ?>>jtremolada</option>
                    </select>
                    <br>Embriologo Transferencia
										<select class="form-control" name="t_emb" id="t_emb">
												<option value="">TODOS</option>
												<?php
													$data_emb = $db->prepare("SELECT id codigo, nom nombre from lab_user order by nom;");
													$data_emb->execute();
													while ($info = $data_emb->fetch(PDO::FETCH_ASSOC)) {
														print("<option value=".$info['codigo'] . ($t_emb == $info['codigo'] ? " selected": "") .">".mb_strtoupper($info['nombre'])."</option>");
													} ?>
										</select>
                    <br>Estado
                    <select name="estado_beta" id="estado_beta" data-mini="true">
                        <option value=""> Todos</option>
                        <option value="nuevo" <?php if (isset($_POST['estado_beta']) and $_POST['estado_beta']=="nuevo") { print('selected'); } ?> class="sin-cita">Nuevos</option>
                        <option value="con-cita" <?php if (isset($_POST['estado_beta']) and $_POST['estado_beta']=="con-cita") { print('selected'); } ?> class="con-cita">Con cita</option>
                        <option value="con-ecografia" <?php if (isset($_POST['estado_beta']) and $_POST['estado_beta']=="con-ecografia") { print('selected'); } ?> class="con-ecografia">1era ecografía</option>
                    </select>
                    <input type="Submit" name="Mostrar" value="Mostrar" data-mini="true" data-theme="b" data-inline="true"/>
                </div>
                <div class="demo" style="position: relative; padding-top: 70px">
                    <div style="width:100%;display:inline-block;">
                        <ul data-role="listview" data-theme="a" data-filter="true" data-filter-placeholder="Filtro..." data-inset="true">
                            <?php
                            $t_0=0; $t_1=0; $t_2=0; $t_3=0; $t_4=0; $i = 0;
                            while($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
                                $color='';
                                if ($paci['beta']==0) $color='class="col0"';
                                if ($paci['beta']==1) $color='class="col1"';
                                $beta = $paci['fec'.$paci['dia']]; //la fecha del dia de transferencia
                                if($paci['dia']==2) $dt=15;
                                if($paci['dia']==3) $dt=14;
                                if($paci['dia']==4) $dt=13;
                                if($paci['dia']==5) $dt=12;
                                if($paci['dia']==6) $dt=11;
                                $beta = date('d-m-Y', strtotime($beta.' + '.$dt.' days')) ?>
                                <?php
                                if ($paci['beta']==0) {
                                ?>
                                <li <?php echo $color ?>>
                                    <?php echo '('.$paci['tip'].'-'.$paci['pro'].')' ?> <small><?php echo $paci['ape'].' '.$paci['nom'] ?></small>
                                    <span class="ui-li-count">
                                    <?php echo $paci['med'] ?>

                                    <select style="display: none;" data-role="none" name="beta<?php echo $paci['pro'] ?>" data-mini="true" onChange="Beta(this,'<?php echo $paci['pro'] ?>')">
                                        <option value=0 <?php if($paci['beta']==0) { echo 'selected'; $t_0++; } ?>>Pendiente</option>
                                        <option value=1 <?php if($paci['beta']==1) { echo 'selected'; $t_1++; } ?>>Positivo</option>
                                        <option value=2 <?php if($paci['beta']==2) { echo 'selected'; $t_2++; } ?>>Negativo</option>
                                        <option value=3 <?php if($paci['beta']==3) { echo 'selected'; $t_3++; } ?>>Bioquimico</option>
                                        <option value=4 <?php if($paci['beta']==4) { echo 'selected'; $t_4++; } ?>>Aborto</option>
                                        <option value=5 <?php if($paci['beta']==5) { echo 'selected'; $t_5++; } ?>>Anembrionado</option>
                                        <option value=6 <?php if($paci['beta']==6) { echo 'selected'; $t_6++; } ?>>Ectópico</option>
                                    </select>
                                    <?php echo $beta ?>
                                    </span>
                                </li>
                                <?php
                                } else {
                                    $datos[$i] = array($paci['fec'.$paci['dia']], $color, '('.$paci['tip'].'-'.$paci['pro'].')', $paci['ape'].' '.$paci['nom'], $paci['med'], $paci['pro'],$paci['beta'], $paci['dni'], $paci['cita_eco_beta'], $paci['idecobetapositivo'], $paci['fechaecografia'], $paci['sacogestacional'], $paci['condicion'], $paci['siguiente_ecografia'], $paci['confirmacion']);
                                    $i++;
                                }
                            }
                            usort($datos, function($a1, $a2) {
                                $v1 = strtotime($a1[0]); // 0 = 1er valor del arrary osea: $beta
                                $v2 = strtotime($a2[0]);
                                return $v2 - $v1; // $v1 - $v2 to reverse direction
                            });
                            // print_r($datos);
                            foreach ($datos as $boy) {
                                $clase = !$boy[8] ? 'sin-cita' : ($boy[10] == '0000-00-00' && !$boy[14] ? 'con-cita' : 'con-ecografia');
                                ?>
                                <li class="<?php echo $clase ?>" data-idecobetapositivo=<?php echo $boy[9] ?>>
                                    <?php echo $boy[2] ?>
                                    <small class="paciente" data-dni="<?php echo $boy[7] ?>">
                                        <?php echo $boy[3] ?>
                                        <?php if ($boy[14]): ?>
                                            <b>(Bioquímico)</b>
                                            <?php $t_3++ ?>
                                        <?php endif ?>
                                        
                                    </small>

                                    <span class="ui-li-count">
                                        <?php echo $boy[4] ?> 
                                            <select  class="pro" id="proestado" data-pro="<?php echo $boy[5] ?>" data-role="none" name="beta<?php echo $boy[5] ?>" data-mini="true" onChange="Beta(this,'<?php echo $boy[5] ?>')">
                                                <option value="">Positivo</option>
                                                <option value=3>Bioquimico con ecografía</option>
                                                <option value=3>Bioquimico sin ecografía</option>
                                            </select>
                                        <span class="fecha"><?php echo $boy[0] ?></span>
                                    
                                    </span>
                                    <?php if( !!$boy[8] && $boy[10] == '0000-00-00'   ): ?>
                                        <?php if ($boy[14]): ?>
                                            <p>Fecha próxima ecografía de confirmación: <b><?php echo $boy[8] ?></b>    
                                                <?php else: ?>
                                                    <p>Fecha cita: <b><?php echo $boy[8] ?></b></p>
                                        <?php endif ?>
                                        
                                    <?php elseif( !!$boy[8] && $boy[10] != '0000-00-00' ): ?>
                                            
                                        <?php if(!$boy[14]): ?>
                                            <p>Fecha próxima ecografía: <b><?php echo $boy[13] ?></b>
                                        <?php endif ?>
                                        </p>
                                    <?php endif ?>
                                </li>
                                <?php
                            }
                            if ($rPaci->rowCount()<1)  echo '<p><h3>¡ No hay registros !</h3></p>';
                        ?>
                        </ul>
                    </div>
                    <?php 
                        $bioquimicos = $db->prepare("SELECT pro FROM lab_aspira_t WHERE beta = 3 and estado is true and pro in (SELECT pro FROM hc_eco_beta_positivo WHERE sacogestacional = 0 GROUP BY pro)");

                        $bioquimicos->execute();
                     ?>
                    <div style="width:200px; position: absolute; top: 0;">
                        <br>
                        TOTAL: <?php echo $t_1 ?>
                        <br>
                        BIOQUÍMICOS: <?php echo $t_3 + $bioquimicos->rowCount() ?>
                    </div>
                </div>
            </form>

            <div id="two">
                <form id="one" action="" method="post" data-ajax="false" name="form3">
                    <p><b>Nombre:</b> <span id="nombre-paciente"></span></p>
                    <p><b>Fecha beta:</b> <span id="fecha-beta"></span></p>
                    <input type="hidden" id="pro" name="pro">
                    <input type="hidden" id="dni" name="dni">
                    <table width="100%" align="center" style="margin: 0 auto;">
                        <tr>
                            <td>Fecha:</td>
                            <td>
                                <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <input name="fec" type="date" id="fec" value="<?php echo date("Y-m-d") ?>"
                                           data-wrapper-class="controlgroup-textinput ui-btn" >
                                    <select name="fec_h" id="fec_h">
                                        <option value="">Hra</option>
                                        <option value="07">07 hrs</option>
                                        <option value="08">08 hrs</option>
                                        <option value="09">09 hrs</option>
                                        <option value="10">10 hrs</option>
                                        <option value="11">11 hrs</option>
                                        <option value="12">12 hrs</option>
                                        <option value="13">13 hrs</option>
                                        <option value="14">14 hrs</option>
                                        <option value="15">15 hrs</option>
                                        <option value="16">16 hrs</option>
                                        <option value="17">17 hrs</option>
                                        <option value="18">18 hrs</option>
                                        <option value="19">19 hrs</option>
                                        <option value="20">20 hrs</option>
                                    </select>
                                    <select name="fec_m" id="fec_m">
                                        <option value="">Min</option>
                                        <option value="00">00 min</option>
                                        <option value="15">15 min</option>
                                        <option value="30">30 min</option>
                                        <option value="45">45 min</option>
                                    </select>
                                </div>
                            </td>
                            <td width="39%">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2">Motivo de Consulta <textarea name="mot" id="mot"
                                                                         data-mini="true"></textarea></td>
                        </tr>
                    </table>
                    <?php if ($user['role'] == 1) { ?>
                        <input type="Submit" value="AGENDAR CONSULTA" name="boton_datos" data-icon="check"
                               data-iconpos="left" data-mini="true" class="show-page-loading-msg"
                               data-textonly="false" data-textvisible="true" data-msgtext="Agregando datos.."
                               data-theme="b" data-inline="true"/>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
        jQuery(document).ready(function($){
            $('#alerta').delay(3000).fadeOut('slow');

            $('#tab-lista').on('click', function() {
                $('#tab-agendar').addClass('ui-disabled');
            });

            $('.sin-cita').on('click', function() {
                $('#tab-agendar').removeClass('ui-disabled').click();
                $paciente = $(this).find('.paciente');
                $('#nombre-paciente').text($paciente.text());
                $('#fecha-beta').text($(this).find('.fecha').text());
                $('#pro').val($(this).find('.pro').data('pro'));
                $('#dni').val($paciente.data('dni'));
            });

            $('.con-cita, .con-ecografia').on('click', function(){
                window.location.href = 'eco_beta_positivo.php?med=<?php echo $medico ?>&id=' + $(this).data('idecobetapositivo');
            });

            $('#proestado, #proestado option').on('click', function(e){
                e.stopPropagation();
            })
        });
    </script>
</body>
</html>