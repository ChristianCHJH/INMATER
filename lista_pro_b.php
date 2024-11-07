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
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" data-dialog="true">
    <?php
        if (isset($_POST['val_beta']) && !empty($_POST['val_beta']) && isset($_POST['pro_beta']) && !empty($_POST['pro_beta'])) {
        	$stmt = $db->prepare("UPDATE lab_aspira_t SET beta=?,iduserupdate=? where pro=? and estado is true");
        	$stmt->execute(array($_POST['val_beta'], $login, $_POST['pro_beta']));
        	print("<div id='alerta'> BETA Guardado! </div>");
    	}	
        $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
        $rUser->execute(array($login));
        $user = $rUser->fetch(PDO::FETCH_ASSOC);

        if ($user['role']==1 or $user['role']==2)
            if (isset($_GET['med']) && !empty($_GET['med'])) {
                if ($_GET['med']==1) {
                    $cerrar="lista.php";
                    $porMed=" and lab_aspira_t.med='".$login."'";
                } else {
                    $cerrar="lista_pro.php";
                    $porMed="";
                }
            } else {
                $cerrar="lista_pro.php";
                $porMed="";
            }
        // 
    $between=$t_med=$t_emb=$tipa=$edesde=$ehasta=$ngs="";
    if (isset($_POST) && !empty($_POST)) {
        if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
            $between.="
            and case lab_aspira_t.dia
                when 6 then date_add(lab_aspira.fec6, interval 11 day)
                when 5 then date_add(lab_aspira.fec5, interval 12 day)
                when 4 then date_add(lab_aspira.fec4, interval 13 day)
                when 3 then date_add(lab_aspira.fec3, interval 14 day)
                when 2 then date_add(lab_aspira.fec2, interval 15 day)
                else null end between '".$_POST["ini"]."' and '".$_POST["fin"]."'";
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
    }

    $rPaci = $db->prepare("SELECT
        hc_paciente.dni, ape, nom, hc_paciente.med,
        lab_aspira.pro, lab_aspira.tip, lab_aspira.dias,
        lab_aspira.fec2, lab_aspira.fec3, lab_aspira.fec4, lab_aspira.fec5, lab_aspira.fec6,
        lab_aspira_t.beta, lab_aspira_t.dia
        FROM hc_paciente,lab_aspira,hc_reprod,lab_aspira_t
        WHERE hc_reprod.estado = true and lab_aspira.estado is true and hc_paciente.dni=lab_aspira.dni and hc_reprod.id=lab_aspira.rep and lab_aspira_t.pro=lab_aspira.pro".$porMed.$between."
        ORDER by lab_aspira_t.beta ASC");
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
        <a href="<?php echo $cerrar; ?>" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
        <h2>Transferencia - Betas</h2>
    </div>

    <div class="ui-content" role="main">
        <form action="" method="post" data-ajax="false" name="form2">
            <input type="hidden" name="val_beta">
            <input type="hidden" name="pro_beta">
            <div class="enlinea">
                Mostrar Desde<input name="ini" type="date" id="ini" value="<?php echo $_POST['ini']; ?>" data-mini="true">
                Hasta<input name="fin" type="date" id="fin" value="<?php echo $_POST['fin']; ?>" data-mini="true"><br>

                <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                    Edad cumplida:
                    <div>
                        <input class="form-control" name="edesde" type="number" value="<?php echo $_POST['edesde']; ?>" data-mini="true">
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                    Y menor a:
                    <div>
                        <input class="form-control" name="ehasta" type="number" value="<?php echo $_POST['ehasta']; ?>" data-mini="true">
                    </div>
                </div><br>

                <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                    Tipo Paciente
                    <select name='tipa' data-mini="true">
                        <option value='' >todos</option>
                        <option value='P' <?php if($tipa == "P") print("selected"); ?>>Paciente</option>
                        <option value='R' <?php if($tipa == "R") print("selected"); ?>>Receptor</option>
                        <option value='D' <?php if($tipa == "D") print("selected"); ?>>Donante</option>
                    </select>
                </div><br>

                <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                    Realizó NGS
                    <select name='ngs' data-mini="true">
                        <option value='' >Seleccionar</option>
                        <option value='s' <?php if($ngs == "s") print("selected"); ?>>Si</option>
                        <option value='n' <?php if($ngs == "n") print("selected"); ?>>No</option>
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
                <input type="Submit" name="Mostrar" value="Mostrar" data-mini="true" data-theme="b" data-inline="true"/>
            </div>
            <div class="demo" style="position: relative; padding-top: 270px">
                <div style="width:800px;display:inline-block;">
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
                            $beta = date('d-m-Y', strtotime($beta.' + '.$dt.' days')); ?>
                            <?php
                            if ($paci['beta']==0) {
                            ?>
                            <li <?php echo $color; ?>>
                                <?php echo '('.$paci['tip'].'-'.$paci['pro'].')';?> <small><?php echo $paci['ape'].' '.$paci['nom'];?></small>
                                <span class="ui-li-count">
                                <?php echo $paci['med'];?>
                                <select data-role="none" name="beta<?php echo $paci['pro']; ?>" data-mini="true" onChange="Beta(this,'<?php echo $paci['pro']; ?>')">
                                    <option value=0 <?php if($paci['beta']==0) { echo 'selected'; $t_0++; } ?>>Pendiente</option>
                                    <option value=1 <?php if($paci['beta']==1) { echo 'selected'; $t_1++; } ?>>Positivo</option>
                                    <option value=2 <?php if($paci['beta']==2) { echo 'selected'; $t_2++; } ?>>Negativo</option>
                                    <option value=3 <?php if($paci['beta']==3) { echo 'selected'; $t_3++; } ?>>Bioquimico</option>
                                    <option value=4 <?php if($paci['beta']==4) { echo 'selected'; $t_4++; } ?>>Aborto</option>
                                    <option value=5 <?php if($paci['beta']==5) { echo 'selected'; $t_5++; } ?>>Anembrionado</option>
                                    <option value=6 <?php if($paci['beta']==6) { echo 'selected'; $t_6++; } ?>>Ectópico</option>
                                </select>
                                <?php echo $beta;?>
                                </span>
                            </li>
                            <?php
                            } else {
                                $datos[$i] = array($beta, $color, '('.$paci['tip'].'-'.$paci['pro'].')', $paci['ape'].' '.$paci['nom'], $paci['med'], $paci['pro'],$paci['beta']);
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
                            $estado_bloqueado = $boy[6] == 1 || $boy[6] == 2;
                            ?>
                            <li <?php echo $boy[1]; ?>>
                            <?php echo $boy[2];?> <small><?php echo $boy[3];?></small>
                                <span class="ui-li-count">
                                <?php echo $boy[4];?>
                                <select data-role="none" name="beta<?php echo $boy[5]; ?>" data-mini="true" onChange="Beta(this,'<?php echo $boy[5]; ?>')" <?php if( $estado_bloqueado ) echo 'disabled="disabled"' ?>>
                                    <option value=0 <?php if($boy[6]==0) { echo 'selected'; $t_0++; } ?>>Pendiente</option>
                                    <option value=1 <?php if($boy[6]==1) { echo 'selected'; $t_1++; } ?>>Positivo</option>
                                    <option value=2 <?php if($boy[6]==2) { echo 'selected'; $t_2++; } ?>>Negativo</option>
                                    <option value=3 <?php if($boy[6]==3) { echo 'selected'; $t_3++; } ?>>Bioquimico</option>
                                    <option value=4 <?php if($boy[6]==4) { echo 'selected'; $t_4++; } ?>>Aborto</option>
                                    <option value=5 <?php if($boy[6]==5) { echo 'selected'; $t_5++; } ?>>Anembrionado</option>
                                    <option value=6 <?php if($boy[6]==6) { echo 'selected'; $t_6++; } ?>>Ectópico</option>
                                </select>
                                <?php echo $boy[0];?>
                                </span>
                            </li>
                            <?php
                        }
                        if ($rPaci->rowCount()<1)  echo '<p><h3>¡ No hay registros !</h3></p>';
                    ?>
                    </ul>
                </div>
                <div style="width:150px; position: absolute; top: 0;">
                    <h3>RESUMEN</h3>
                    <?php
                    print"
                    <span class='col0'>Pendientes: ".$t_0."</span><br>
                    Negativos: ".$t_2."(".number_format(($t_2*100/($t_1+$t_2+$t_3+$t_4)), 2)."%)<br>
                    <span class='col1'>Positivos: ".$t_1."(".number_format(($t_1*100/($t_1+$t_2+$t_3+$t_4)), 2)."%)</span><br>
                    Bioquimicos: ".$t_3."(".number_format(($t_3*100/($t_1+$t_2+$t_3+$t_4)), 2)."%)<br>
                    Abortos: ".$t_4."(".number_format(($t_4*100/($t_1+$t_2+$t_3+$t_4)), 2)."%)<br><br>
                    TOTAL: ".($t_0+$t_1+$t_2+$t_3+$t_4); ?>
                </div>
            </div>
        </form>     
    </div>
    </div>
    <script>
        $(function(){
        	$('#alerta').delay(3000).fadeOut('slow');
        });//]]> 
    </script>
</body>
</html>