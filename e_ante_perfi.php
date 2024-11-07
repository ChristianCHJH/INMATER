<!DOCTYPE HTML>
<html lang="es">
<head>
    <?php
   include 'seguridad_login.php'
    ?>
	<meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Clínica Inmater | Análisis Clínico</title>
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="e_ante_perfi" data-dialog="true">
        <?php
        if (!!$_POST && !!$_POST['dni'] && !!$_POST['fec']) {
            updateAnte_perfi($_POST['idx'], $_POST['dni'], $_POST['fec'], $_POST['fsh'], $_POST['lh'], $_POST['est'], $_POST['prol'], $_POST['ins'], $_POST['t3'], $_POST['t4'], $_POST['tsh'], $_POST['amh'], $_POST['inh'], $_FILES['pdf']);
        }

        if (!!$_GET && !!$_GET['dni'] && isset($_GET['id'])) {
            $dni = $_GET['dni'];
            $id = $_GET['id'];

            $Rpop = $db->prepare("SELECT * FROM hc_antece_perfi WHERE fec = ? and dni = ?");
            $Rpop->execute(array($id, $dni));
            $pop = $Rpop->fetch(PDO::FETCH_ASSOC); ?>

            <style>
                .ui-dialog-contain {      
                    max-width: 800px;
                    margin: 2% auto 15px;
                    padding: 0;
                    position: relative;
                    top: -15px;
                }

                .scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap; } 
            </style>

            <div data-role="header" data-position="fixed">
            <?php
				if (!!$_GET && isset($_GET["path"])) {
					print('<a href="'.$_GET["path"].'.php?dni=' . $dni . '" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
				} /* else {
					print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
				} */ ?>

                <h3>Perfil Hormonal</h3>
            </div>

            <div class="ui-content" role="main">
                <form action="" method="post" enctype="multipart/form-data" data-ajax="false" name="form2">
                    <input type="hidden" name="idx" value="<?php echo $id;?>">
                    <input type="hidden" name="dni" value="<?php echo $dni;?>">

                    <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                        <tr>
                            <td width="268" bgcolor="#E8E8E8">Fecha</td>
                            <td width="269" bgcolor="#E8E8E8">
                                <input name="fec" type="date" id="fec" data-mini="true" value="<?php echo $pop['fec'];?>">
                            </td>
                            <td width="211">&nbsp;</td>
                            <td width="607">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Estradiol</td>
                            <td><input name="est" type="text" id="est" data-mini="true" value="<?php echo $pop['est'];?>"></td>
                            <td width="211">FSH</td>
                            <td width="607"><input name="fsh" type="text" id="fsh" data-mini="true" value="<?php echo $pop['fsh'];?>"></td>
                        </tr>
                        <tr>
                            <td>Prolactina</td>
                            <td>
                                <input name="prol" type="text" id="prol" data-mini="true" value="<?php echo $pop['prol'];?>">
                            </td>
                            <td>LH</td>
                            <td><input name="lh" type="text" id="lh" data-mini="true" value="<?php echo $pop['lh'];?>"></td>
                        </tr>
                        <tr>
                            <td>T3</td>
                            <td>
                                <input name="t3" type="text" id="t3" data-mini="true" value="<?php echo $pop['t3'];?>">
                                </td>
                            <td>AMH</td>
                            <td><input name="amh" type="text" id="amh" data-mini="true" value="<?php echo $pop['amh'];?>"></td>
                        </tr>
                        <tr>
                            <td>T4</td>
                            <td><input name="t4" type="text" id="t4" data-mini="true" value="<?php echo $pop['t4'];?>"></td>
                            <td>Insulina</td>
                            <td><input name="ins" type="text" id="ins" data-mini="true" value="<?php echo $pop['ins'];?>"></td>
                        </tr>
                        <tr>
                            <td>TSH</td>
                            <td><input name="tsh" type="text" id="tsh" data-mini="true" value="<?php echo $pop['tsh'];?>"></td>
                            <td>Inhibina B</td>
                            <td><input name="inh" type="text" id="inh" data-mini="true" value="<?php echo $pop['inh'];?>"></td>
                        </tr>
                        <tr>
                            <td colspan="2">Adjuntar resultado (PDF)</td>
                            <td colspan="2"><input name="pdf" type="file" accept="application/pdf" id="pdf"/><?php if (file_exists("analisis/perfil_" . $dni . "_" . $pop['fec'] . ".pdf")) echo "<a href='archivos_hcpacientes.php?idArchivo=perfil_" . $dni . "_" . $pop['fec']."' target='new'>Ver Resultado</a>"; ?></td>
                        </tr>
                    </table>
                    
                    <input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Guardando datos.." data-theme="b" data-inline="true"/>
                </form>
            </div>
        <?php } ?>
    </div>
    <script>
    $( document ).on( "click", ".show-page-loading-msg", function() {        
        if (document.getElementById("fec").value == "") {
            alert ("Debe especificar la Fecha");
            return false;
        }
                
        var $this = $( this ),
            theme = $this.jqmData( "theme" ) || $.mobile.loader.prototype.options.theme,
            msgText = $this.jqmData( "msgtext" ) || $.mobile.loader.prototype.options.text,
            textVisible = $this.jqmData( "textvisible" ) || $.mobile.loader.prototype.options.textVisible,
            textonly = !!$this.jqmData( "textonly" );
            html = $this.jqmData( "html" ) || "";

        $.mobile.loading( "show", {
            text: msgText,
            textVisible: textVisible,
            theme: theme,
            textonly: textonly,
            html: html
        });
    }).on( "click", ".hide-page-loading-msg", function() {
        $.mobile.loading( "hide" );
    });
    </script>
</body>
</html>