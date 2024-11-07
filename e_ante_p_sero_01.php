<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="_themes/tema_inmater.min.css" />
	<link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
	<div data-role="page" class="ui-responsive-panel" id="e_ante_sero" data-dialog="true">
	    <?php
		if ( isset($_POST['fec']) && !empty($_POST['fec']) && isset($_GET['tipopaciente']) && !empty($_GET['tipopaciente']) ) {
            updateAnte_p_sero_01($_GET['tipopaciente'], $_POST['dni'], $_POST['p_dni'], $_POST['fec'], $_POST['hbs'], $_POST['hcv'], $_POST['hiv'], $_POST['rpr'], $_POST['rub'], $_POST['tox'], $_POST['cla_g'], $_POST['cla_m'], $_FILES['pdf']);
		}
        // conseguir datos
		if ( isset($_GET['tipopaciente']) && !empty($_GET['tipopaciente']) && isset($_GET['dni']) && !empty($_GET['dni']) ) {
            $tipopaciente = $_GET['tipopaciente'];
            $dni = $_GET['dni'];
            $fec = "";
            if( isset($_GET['fec']) && !empty($_GET['fec']) ){
                $fec = $_GET['fec'];
            }
            // paciente
            $rPare = $db->prepare("SELECT nom, ape FROM hc_paciente WHERE dni=?");
            $rPare->execute( array($dni) );
            $pare = $rPare->fetch(PDO::FETCH_ASSOC);
            switch ($tipopaciente) {
                case 1:

                break;
                case 2:
                    // consulta parejas
                    $data = $db->prepare("
                        select a.p_dni dni, a.p_ape ape, a.p_nom nom
                        from hc_pareja a
                        inner join hc_pare_paci b on b.p_dni = a.p_dni
                        inner join hc_paciente c on c.dni = b.dni and c.dni = ?");
                    $data->execute( array($dni) );
                break;
                default: exit(); break;
            }
            $Rpop = $db->prepare("select * from hc_antece_p_sero where tipo_paciente=? and fec=? and p_dni=?");
			$Rpop->execute( array($tipopaciente, $fec, $dni) );
            $pop = $Rpop->fetch(PDO::FETCH_ASSOC);
        ?>
        <style>
            .ui-dialog-contain {
                max-width: 600px;
                margin: 2% auto 15px;
                padding: 0;
                position: relative;
                top: -15px;
            }
            .scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap;}
        </style>
        <div data-role="header" data-position="fixed">
            <?php if ($tipopaciente == "1") print("<h3>Serologías</h3>");  else print("<h3>Serologías para pareja</h3>"); ?>
        </div>
        <div class="ui-content" role="main">
            <form action="e_ante_p_sero.php" method="post" enctype="multipart/form-data" data-ajax="false" name="form2">
                <input type="hidden" name="idx" value="<?php echo $id;?>">
                <input type="hidden" name="dni" value="<?php echo $dni;?>">
                <input type="hidden" name="p_dni" value="<?php echo $p_dni;?>">
                <table width="100%" align="center" style="margin: 0 auto;text-align:center;">
                    <tr>
                        <td>Fecha</td>
                        <td><input name="fec" type="date" id="fec" value="<?php echo $pop['fec'];?>" data-mini="true" required></td>
                    </tr>
                    <tr>
                        <td>Paciente</td>
                        <td>
                            <?php print($pare['ape']." ".$pare['nom']); ?>
                        </td>
                    </tr>
                    <?php
                        if ($tipopaciente == "2") {
                            print('
                            <tr>
                                <td>Pareja</td>
                                <td>
                                    <select name="tipoinforme" class="form-control" required>
                                        <option value="">---</option>');
                                        /* $data = $db->prepare("select id, nombre from man_legal_tipodocumento");
                                        $data->execute(); */
                                        while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value='".$info['dni']."'");
                                        if ($pop["pare_dni"] == $info['dni'])
                                            print(" selected");
                                        print(">" . mb_strtoupper($info['ape'])." " . mb_strtoupper($info['nom']) ."</option>");
                                        }
                            print('</select></td></tr>');
                        }
                    ?>
                    <tr>
                        <td width="532">Tipo Examen</td>
                        <td width="721">Resultado</td>
                    </tr>
                    <tr>
                        <td>Hepatitis B - HBs Ag</td>
                        <td>
                            <select name="hbs" id="hbs" data-mini="true">
                                <option value="">---</option>
                                <option value=1 <?php if ($pop["hbs"]==1) echo "selected"; ?>>Positivo</option>
                                <option value=2 <?php if ($pop["hbs"]==2) echo "selected"; ?>>Negativo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Hepatitis C - HCV Ac</td>
                        <td>
                            <select name="hcv" id="hcv" data-mini="true">
                                <option value="">---</option>
                                <option value=1 <?php if ($pop["hcv"]==1) echo "selected"; ?>>Positivo</option>
                                <option value=2 <?php if ($pop["hcv"]==2) echo "selected"; ?>>Negativo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>HIV Ac/Ag</td>
                        <td>
                        <select name="hiv" id="hiv" data-mini="true">
                            <option value="">---</option>
                            <option value=1 <?php if ($pop["hiv"]==1) echo "selected"; ?>>Positivo</option>
                            <option value=2 <?php if ($pop["hiv"]==2) echo "selected"; ?>>Negativo</option>
                        </select>
                        </td>
                    </tr>
                    <tr>
                        <td>RPR</td>
                        <td>
                            <select name="rpr" id="rpr" data-mini="true">
                            <option value="">---</option>
                            <option value=1 <?php if ($pop["rpr"]==1) echo "selected"; ?>>Positivo</option>
                            <option value=2 <?php if ($pop["rpr"]==2) echo "selected"; ?>>Negativo</option>
                            </select>
                        </td>
                    </tr>
                    <?php if ($dni=="mujer") { ?>
                    <tr>
                        <td>Rubeola IgG</td>
                        <td>
                        <select name="rub" id="rub" data-mini="true">
                            <option value="">---</option>
                            <option value=1 <?php if ($pop["rub"]==1) echo "selected"; ?>>Positivo</option>
                            <option value=2 <?php if ($pop["rub"]==2) echo "selected"; ?>>Negativo</option>
                        </select>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>Toxoplasma IgG</td>
                        <td>
                            <select name="tox" id="tox" data-mini="true">
                                <option value="">---</option>
                                <option value=1 <?php if ($pop["tox"]==1) echo "selected"; ?>>Positivo</option>
                                <option value=2 <?php if ($pop["tox"]==2) echo "selected"; ?>>Negativo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Clamidia IgG</td>
                        <td>
                            <select name="cla_g" id="cla_g" data-mini="true">
                                <option value="">---</option>
                                <option value=1 <?php if ($pop["cla_g"]==1) echo "selected"; ?>>Positivo</option>
                                <option value=2 <?php if ($pop["cla_g"]==2) echo "selected"; ?>>Negativo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Clamidia IgM</td>
                        <td>
                            <select name="cla_m" id="cla_m" data-mini="true">
                                <option value="">---</option>
                                <option value=1 <?php if ($pop["cla_m"]==1) echo "selected"; ?>>Positivo</option>
                                <option value=2 <?php if ($pop["cla_m"]==2) echo "selected"; ?>>Negativo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Adjuntar Resultado (PDF)</td>
                        <td>
                            <input name="pdf" type="file" accept="application/pdf" id="pdf"/>
                            <?php
                                if ( file_exists("analisis/sero_" . $dni . "_" . $pop['fec'].".pdf") )
                                    print("<a href='archivos_hcpacientes.php?idArchivo=sero_" . $dni."_" . $pop['fec']."' target='_blank'>Ver Resultado</a>");
                            ?>
                        </td>
                    </tr>
                </table>
                <input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Guardando datos.." data-theme="b" data-inline="true"/>
            </form>
        </div>
		<?php } ?>
	</div>
</body>
</html>