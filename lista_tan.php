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
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <title>Inmater Clínica de Fertilidad | Mantenimiento de tanques</title>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script language="JavaScript" type="text/javascript">
    function anular(x, y) {
        document.form1.conf.value = x;
        document.form1.acti.value = y;
        document.form1.n_c.value = ""; //para que no inserte registros
        document.form1.submit();
    }
    </script>
    <style>
    .ui-slider input {
        visibility: hidden
    }

    .ui-slider-track {
        margin: 0 15px 0 15px
    }
    </style>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="lista">
        <?php
			if ($_SESSION['role'] == 2) {
				if (isset($_POST['conf']) && !empty($_POST['conf']) && isset($_POST['acti']) && !empty($_POST['acti'])) {
					$conf=$_POST['conf'];
					$acti=$_POST['acti'];
					// if ($conf<>"" and $acti<>"") {
					$stmt = $db->prepare("UPDATE lab_tanque SET sta=? where tan=?");
					$stmt->execute(array($acti, $conf));
					// }
				}

				if (isset($_POST['n_tan']) && !empty($_POST['n_tan']) && isset($_POST['n_c']) && !empty($_POST['n_c']) && isset($_POST['n_v']) && !empty($_POST['n_v']) && isset($_POST['n_p']) && !empty($_POST['n_p']) && isset($_POST['tip']) && !empty($_POST['tip'])) {
					lab_insertTanque($_POST['n_tan'], $_POST['n_c'], $_POST['n_v'], $_POST['n_p'], $_POST['tip'], 1);
				}

				$rTan = $db->prepare("SELECT * FROM lab_tanque where sta = 1;");
				$rTan->execute();
			}
		?>
        <div data-role="header" data-position="fixed">
            <h1>Tanques</h1>
            <?php
				if ($_SESSION['role'] == 2) {
			?>
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
                <a href='lista_and.php' class="ui-btn ui-btn-c ui-icon-home ui-btn-icon-left" rel="external">Andrologia</a>
            </div>
            <?php
				}
			?>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div>
        <div class="ui-content" role="main">
            <form action="" method="post" name="form1" data-ajax="false">
                <input type="hidden" name="conf">
                <input type="hidden" name="acti">
                <table style="margin: 0 auto;" width="80%">
                    <tr>
                        <td colspan="6" align="center" class="ui-bar-a">Agregue aqui un nuevo tanque:</td>
                    </tr>
                    <tr>
                        <td align="center">N° Tanque</td>
                        <td align="center">Canisters</td>
                        <td align="center">Varillas o Gobelets</td>
                        <td align="center">Viales o Pajuelas</td>
                        <td align="center">Tipo</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="20%">
                            <input name="n_tan" type="number" id="n_tan" data-mini="true" required>
                        </td>
                        <td width="20%">
                            <input name="n_c" type="range" id="n_c" min="5" max="10" data-show-value="true" data-popup-enabled="true" data-highlight="true" required>
                        </td>
                        <td width="20%">
                            <input name="n_v" type="range" id="n_v" min="20" max="50" data-show-value="true" data-popup-enabled="true" data-highlight="true" required>
                        </td>
                        <td width="20%">
                            <select name="n_p" required id="n_p" data-mini="true">
                                <option value="" selected>-</option>
                                <option value=4>4</option>
                                <option value=5>5</option>
                                <option value=6>6</option>
                            </select>
                        </td>
                        <td width="20%">
                            <select name="tip" required id="tip" data-mini="true">
                                <option value="" selected>-</option>
                                <option value=1>SEMEN</option>
                            </select>
                        </td>
                        <td width="20%">
                            <input type="Submit" value="Agregar" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Agregando.." data-theme="b" />
                        </td>
                    </tr>
                </table>
                <ul data-role="listview" data-inset="true">
                    <?php
				while ($tan = $rTan->fetch(PDO::FETCH_ASSOC)) { ?>
                    <li <?php if ($tan['sta']==0) print 'data-icon="false" data-theme="b"'; else print 'data-icon="false"';?>>
                        <h5>
                            <?php
				    		print "TANQUE: ".$tan['n_tan'];
			    		?>
                            <?php
							if ($tan['tip']==1) {
								$pos="VIALES"; print " (SEMEN)";
							}
							if ($tan['tip']==2) {
								$pos="PAJUELAS";
							print " (ÓVULOS Y EMBRIONES)";}
						?>
                        </h5>
                        <p><?php print "CANISTERS: ".$tan['n_c']."<br>VARILLAS: ".$tan['n_v']."<br>".$pos.": ".$tan['n_p'];?>
                        </p>
                        <!-- <span class="ui-li-count">
						<?php // if($tan['sta']==0) { ?>
				        <a href='javascript:anular("<?php // print $tan['tan'];?>",1);' data-theme="a">[Desactivado]</a> 
				        <?php // }else{ ?>
				        <a href='javascript:anular("<?php // print $tan['tan'];?>",0);' data-theme="b">[Activado]</a> 
				        <?php // } ?>
				    </span> -->
                    </li>
                    <?php }
				?>
                </ul>
                <div data-role="tabs">
                    <div data-role="navbar">
                        <ul>
                            <li><a href="#one" data-ajax="false" class="ui-btn-active ui-btn-icon-left ui-icon-carat-r">ÓVULOS Y EMBRIONES</a></li>
                            <li><a href="#two" data-ajax="false" class="ui-btn-icon-left ui-icon-carat-r">SEMEN</a></li>
                        </ul>
                    </div>
                    <div id="one" class="ui-body-d ui-content">Lista de los ovulos y embriones</div>
                    <div id="two"></div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>