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
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="e_ante_hsghes" data-dialog="true">
        <?php
        if (!!$_POST && !!$_POST['dni'] && !!$_POST['fec']) {
            updateAnte_hsghes($_POST['idx'], $_POST['dni'], $_POST['fec'], $_POST['tip'], $_POST['con'], $_FILES['pdf']);
        }

        if ($_GET['dni'] <> "" ) {
            $dni = $_GET['dni'];
            $id = $_GET['id'];
            
            try {
                $date = new DateTime($id);
                $formatted_date = $date->format('Y-m-d');
            } catch (Exception $e) {
                die('La fecha proporcionada no es válida.');
            }
        
            $Rpop = $db->prepare("SELECT * FROM hc_antece_hsghes WHERE fec = ? and dni = ? and estado = true;");
            $Rpop->execute(array($formatted_date, $dni));
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

            <script>
                $(document).ready(function () {
                    $('.numeros').keyup(function() {
                        var $th = $(this);
                        $th.val($th.val().replace(/[^0-9]/g, function(str) { 
                            return '';
                        }));
                    });
                });
            </script>

            <div data-role="header" data-position="fixed">
            	<?php
				if (!!$_GET && isset($_GET["path"])) {
					print('<a href="' . $_GET["path"] . '.php?dni=' . $dni . '" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
				} ?>
                <h3>HSG - HES</h3>
            </div>

            <div class="ui-content" role="main">
                <form action="" method="post" enctype="multipart/form-data" data-ajax="false" name="form2">
                    <input type="hidden" name="idx" value="<?php echo $id;?>">
                    <input type="hidden" name="dni" value="<?php echo $dni;?>">

                    <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                        <tr>
                            <td width="268">Fecha</td>
                            <td width="269">
                            <input name="fec" type="date" id="fec" data-mini="true" value="<?php echo $pop['fec'];?>">
                            </td>
                            <td width="211">
                                <span class="ui-corner-all" style="padding:5px">
                                    <select name="tip" id="tip" data-mini="true">
                                        <option value="" selected="selected">Seleccione HSG o HES:</option>
                                        <option value="HSG" <?php if(isset($pop['tip'])){if ($pop['tip']=="HSG") echo "selected"; }?>>HSG</option>
                                        <option value="HES" <?php if(isset($pop['tip'])){if ($pop['tip']=="HES") echo "selected"; }?>>HES</option>
                                    </select>
                                </span>
                            </td>
                            <td width="607">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Condición</td>
                            <td colspan="3"><textarea name="con" id="con" data-mini="true"><?php if(isset($pop['con'])){ echo $pop['con'];}?></textarea></td>
                        </tr>
                        <tr>
                            <td>ADJUNTAR RESULTADO (PDF)</td>
                            <td colspan="3">
                                <input name="pdf" type="file" accept="application/pdf" id="pdf"/>
                                <?php
                                    if (isset($pop['fec']) && file_exists("analisis/hsghes_" . $dni . "_" . $pop['fec'] . ".pdf")) {
                                        echo "<a href='archivos_hcpacientes.php?idArchivo=hsghes_" . $dni . "_" . $pop['fec'] . "' target='new'>Ver Resultado</a>";
                                    }
                                ?>
                            </td>
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
            
            if (document.getElementById("tip").value == "") {
                alert ("Debe especificar el Tipo");
                return false;
            }
            
            if (document.getElementById("con").value == "") {
                alert ("Debe especificar la Condición");
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
