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
    <script src="jquery.maskedinput.js" type="text/javascript"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="e_ante_pap" data-dialog="true">
        <?php
        if (!!$_POST && !!$_POST['dni'] && !!$_POST['fec']) {
            updateAnte_pap($_POST['idx'], $_POST['dni'], $_POST['fec'], $_POST['tip'], $_POST['obs']);
        }

        if (!!$_GET && !!$_GET['dni'] && isset($_GET['id'])) {
            $dni = $_GET['dni'];
            $id = $_GET['id'];

            $Rpop = $db->prepare("SELECT * FROM hc_antece_pap WHERE id = ?;");
            $Rpop->execute(array($id));
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
                    $(".mascara").mask("9999", {placeholder:"____"});
                    
                    $('.numeros').keyup(function() {
                        var $th = $(this);
                        $th.val( $th.val().replace(/[^0-9]/g, function(str) { 
                            return ''; } ) );
                    });
                    
                    $(".med_insert").change(function () {
                        var med = $(this).attr("title");
                        var str =$('#'+med).val();
                        var items = $(this).val();
                        
                        var n = str.indexOf(items);
                            if (n == -1) {
                                $('#'+med).val(items +", "+ str);
                                $('#'+med).textinput('refresh');
                            }    
                        
                        if (items=="Borrar") $('#'+med).val("");
                        
                        $(this).prop('selectedIndex', 0);
                        $(this).selectmenu( "refresh", true );
                    });
                });
            </script>

            <div data-role="header" data-position="fixed">
                <?php
				if (!!$_GET && isset($_GET["path"])) {
					print('<a href="'.$_GET["path"].'.php?dni=' . $dni . '" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
				} /* else {
					print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
				} */ ?>
                <h3>Historial PAP</h3>
            </div>

            <div class="ui-content" role="main">
                <form action="" method="post" data-ajax="false" name="form2">
                    <input type="hidden" name="idx" value="<?php echo $id;?>">
                    <input type="hidden" name="dni" value="<?php echo $dni;?>">

                    <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                        <tr>
                            <td width="268">Año:</td>
                            <td width="269">
                            <input name="fec" type="text" id="fec" data-mini="true" value="<?php if ($pop['fec']<>"") echo $pop['fec']; else echo date("Y"); ?>" class="mascara">
                            </td>
                            <td width="211">&nbsp;</td>
                            <td width="607">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <select name="select" class="med_insert" title="tip" data-mini="true">
                                    <option value="" selected>Seleccione TIPO..</option>
                                    <option value="Borrar">- Borrar Datos -</option>
                                    <option value="Normal">Normal</option>
                                    <option value="Inflamación Leve">Inflamación Leve</option>
                                    <option value="Inflamación Moderada">Inflamación Modereda</option>
                                    <option value="Inflamación Severa">Inflamación Severa</option>
                                    <option value="Cambios Coilocitoticos">Cambios Coilocitoticos</option>
                                    <option value="NIC1">NIC1</option>
                                    <option value="NIC2">NIC2</option>
                                    <option value="NIC3">NIC3</option>
                                    <option value="CA Insitu">CA Insitu</option>
                                    <option value="AGUS">AGUS</option>
                                    <option value="ASCUS">ASCUS</option>
                                </select>
                            </td>
                            <td colspan="3"><textarea name="tip" readonly id="tip" data-mini="true"><?php echo $pop['tip']; ?></textarea></td>
                        </tr>
                        <tr>
                            <td>Observaciones</td>
                            <td colspan="3"><textarea name="obs" id="obs" data-mini="true"><?php echo $pop['obs']; ?></textarea></td>
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
