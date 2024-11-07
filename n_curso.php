<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Cursos</title>
	<link rel="stylesheet" href="_themes/tema_inmater.min.css" />
	<link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.3/jquery.mobile.structure-1.4.3.min.css" />
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
	<style>
		.ui-dialog-contain {
		max-width: 800px;
		margin: 2% auto 15px;
		padding: 0;
		position: relative;
		top: -15px;
		}
	</style>
</head>
<body>
	<div data-role="page" class="ui-responsive-panel" id="ncurso" data-dialog="true">
	<script language="JavaScript" type="text/javascript">
		function anular(x,y) {
			document.form2.conf.value=x;
			document.form2.acti.value=y;
			document.form2.submit();
		}

		$("#ncurso").bind("pagehide",function(){
			//alert("Dialog closed");
		});
	</script>
	<div data-role="header" data-position="fixed">
	<h1>Cursos</h1>
	<a href="lista.php" data-icon="back" rel="external">Regresar</a>
	</div><!-- /header -->
	<div class="ui-content" role="main">
	<?php

	$conf=$_POST['conf'];
	$acti=$_POST['acti'];

	if ($conf<>"" and $acti<>"")
	mysql_query("UPDATE curso SET Stac=$acti where idc=$conf");

	if ($_POST['Nom']<>"")
		insertCurso($_POST['Nom'],$_POST['Mai']."@upch.pe",1);

	$rCurso = mysql_query("SELECT * FROM curso ORDER by Stac DESC"); ?>

	<form action="n_curso.php" method="post" name="form2">
	<input type="hidden" name="conf">
	<input type="hidden" name="acti">
	<table style="margin: 0 auto;" width="100%">
		<tr>
			<td width="20%">Nombre</td>
			<td colspan="3"><input name="Nom" type="Text" id="Nom" data-mini="true"/></td>
		</tr>
		<tr>
			<td>Correo electrónico</td>
			<td width="27%"><input name="Mai" type="Text" id="Mai" data-mini="true" data-inline="true"/></td>
			<td width="19%">@upch.pe</td>
			<td width="34%"><input type="Submit" value="Agregar curso"  data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Guardando.." data-inline="true"/></td>
		</tr>
	</table>

	<?php
	if ($curso = mysql_fetch_array($rCurso)){ ?>
	Lista de Cursos
	<table data-role="tablex" class="ui-body-d ui-shadow table-stripe ui-responsive" style="margin: 0 auto;" width="90%">
		<thead class="ui-bar-b ui-corner-all">
			<tr>
				<th data-priority="0">Nº</th>
				<th data-priority="1">Nombre</th>
				<th data-priority="2">Correo electónico</th>
				<th data-priority="3">Estado</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$c=0;
			do {
			$c++; ?>
			<tr  bgcolor="#FFFFFF" onMouseOver="this.style.backgroundColor='#FC6'" onMouseOut="this.style.backgroundColor='#FFFFFF'"> 
				<td><? echo $c;?></td>
				<td><? echo $curso["Nomc"];?></td>
				<td><? echo $curso["Maic"];?></td>
				<td>
				<? if($curso["Stac"]==0) { ?>
				<a href='javascript:anular("<? echo $curso["idc"];?>",1);' data-role="button" data-mini="true" data-icon="minus" data-theme="a">Oculto</a> 
				<? }else{ ?>
				<a href='javascript:anular("<? echo $curso["idc"];?>",0);' data-role="button" data-mini="true" data-icon="eye" data-theme="b">Visible</a> 
				<? } ?>
				</td> 
			</tr> 
			<?php
			} while ($curso = mysql_fetch_array($rCurso));
			?>
		</tbody>
	</table> 
	<?php }else{ ?>
	<p><h3 class="text_buscar">¡ No hay cursos !</h3></p>
	<?php }
		mysql_free_result($rCurso);
		mysql_close($connection); ?>
	</form>
	</div>
	<script>
		$( document ).on( "click", ".show-page-loading-msg", function() {
			if (document.getElementById("Nom").value == "")  {
				alert ("Por favor llene el campo 'Nombre'");
				return false;
			}
			if (document.getElementById("Mai").value == "") {
				alert ("Por favor llene el campo 'Correo electrónico'");
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

			$(".show-page-loading-msg").hide();
		})
		.on( "click", ".hide-page-loading-msg", function() {
		$.mobile.loading( "hide" );
		});
	</script>
	</div>
</body>
</html>