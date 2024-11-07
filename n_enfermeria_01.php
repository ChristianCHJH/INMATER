<!DOCTYPE html>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/global.css">
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
</head>
<body>
	<?php require ('_includes/menu_salaprocedimientos.php'); ?>
	<div class="container">
		<br>
		<!-- <a class="navbar-brand float-right" href="javascript:window.close();">
			<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
		</a><br> -->
		<div data-role="collapsible">
			<?php print("<h4 class='color-primary'>Documentos Enfermeria: <small>".mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom'])."</small></h4>");?>
			<div class="card mb-3">
				<h5 class="card-header">Detalle</h5>
				<div class="card-body collapse show">
                    <?php
					// $rLegal = $db->prepare("select * from hc_legal_01 where numerodocumento=? order by createdate asc");
					$rLegal = $db->prepare("
                    select
                    a.id, b.pro, a.fec, a.p_dni, a.p_dni_het, a.des_dia, a.des_don, a.p_cic, a.p_fiv, a.p_icsi, a.p_od, a.p_don, a.p_cri, a.p_iiu, a.don_todo, a.f_iny, a.cancela, a.f_asp
                    from hc_reprod a
                    left join lab_aspira b on b.rep = a.id and b.estado is true
                    where a.estado = true and a.dni=?
                    order by fec desc");
					$rLegal->execute( array($dni) );
                    if (true) { ?>
						<table class="table table-responsive table-bordered align-middle">
							<thead class="thead-dark">
	                            <tr>
	                            	<th width="10%" class="text-center">Fecha</th>
                                    <th width="10%" class="text-center">Procedimiento</th>
	                                <th width="15%">Tipo Procedimiento</th>
	                                <th width="50%">Informes Enfermería</th>
	                                <th width="15%">Agregar Informe</th>
	                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $data1 = $rLegal->fetchAll();
                                    // while ($item = $rLegal->fetch(PDO::FETCH_ASSOC)) {
                                    foreach ($data1 as $item) { ?>
	                                <tr>
	                                	<td class="text-center"><?php echo date("d-m-Y", strtotime($item['fec'])); ?></td>
                                        <td class="text-center"><?php print($item['pro']); ?></td>
                                        <td class="text-center">
                                        <?php
                                            if ($item['p_cic'] >= 1) echo "Ciclo Natural<br>";
                                            if ($item['p_fiv'] >= 1) echo "FIV<br>";
                                            if ($item['p_icsi'] >= 1) echo $_ENV["VAR_ICSI"] . "<br>";
                                            if ($item['p_od'] <> '') echo "OD Fresco<br>";
                                            if ($item['p_cri'] >= 1) echo "Crio Ovulos<br>";
                                            if ($item['p_iiu'] >= 1) echo "IIU<br>";
                                            if ($item['p_don'] == 1) echo "Donación Fresco<br>";
                                            if ($item['des_don'] == null and $item['des_dia'] >= 1) echo "TED<br>";
                                            if ($item['des_don'] == null and $item['des_dia'] === 0) echo "<small>Descongelación Ovulos Propios</small><br>";
                                            if ($item['des_don'] <> null and $item['des_dia'] >= 1) echo "EMBRIODONACIÓN<br>";
                                            if ($item['des_don'] <> null and $item['des_dia'] === 0 and $item['id']<>2192) echo "<small>Descongelación Ovulos Donados</small><br>";
                                        ?></td>
                                        <td>
                                            <?php
                                                // subconsulta informes de enfermeria
                                                $subconsulta = $db->prepare("
                                                select
                                                finforme, nombre
                                                from hc_enfermeria
                                                where estado = 1 and numerodocumento = ? and idprocedimiento = ?");
                                                $subconsulta->execute( array($dni, $item['id']) );
                                                $data1 = $subconsulta->fetchAll();
                                                foreach ($data1 as $subitem) {
                                                    print("
                                                        " . $subitem['finforme'] . "
                                                        <a href='enfermeria/" . $dni . "/" . $subitem['nombre'] . "' target='new'>" . $subitem['nombre'] . "</a><br>");
                                                }
                                            ?>
                                        </td>
	                                    <td class="text-center">
                                            <?php
                                            print('
                                            <a href="n_enfermeria_add.php?idprocedimiento='.$item["id"].'&dni='.$dni.'" rel="external" class="btn btn-danger">
                                                <img src="_libraries/open-iconic/svg/plus.svg" height="18" width="18" alt="icon name">
					                        </a>'); ?>
	                                    </td>
	                                </tr>
	                            <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($rLegal->rowCount() < 1) echo '<h5>¡Aún no hay documentos cargados!</h5>'; ?>
                        <!-- <h3>Legal <span id="ultimo"></span></h3>
                        <script>
                            $(function () {
                                $("#ultimo").html("<?php // echo ($a_sta); ?>");
                            });
                        </script> -->
                    <?php } else echo "<h5>No hay Documentos</h5>"; ?>
				</div>
			</div>
		</div>
	</div>
	<script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="js/popper.min.js" crossorigin="anonymous"></script>
	<script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>