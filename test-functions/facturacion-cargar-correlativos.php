<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
<?php
	require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
	require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

	global $db;
	$stmt = $db->prepare("SELECT
		id, tip_recibo, id_recibo, serie_cpe, correlativo_cpe
		from facturacion_recibo_mifact_response
		where year(createdate) = 2021
		order by id desc;");
	$stmt->execute();
	print("<p>total de registros: " .  $stmt->rowCount() . "</p>");
	
	while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$stmt_2 = $db->prepare("UPDATE recibos set cpe_correlativo=?, cpe_serie=?,updatex=? where tip=? and id=?;");
			$hora_actual = date("Y-m-d H:i:s");
			$stmt_2->execute([
				$item["correlativo_cpe"],
				$item["serie_cpe"],
				$hora_actual,
				$item["tip_recibo"],
				$item["id_recibo"],
			]);
			$log_Recibos = $db->prepare(
							"INSERT INTO appinmater_log.recibos (
										recibo_id, recibo_tip, 
										sede_pago_id, 
										cli_atencion_unica_id, 
										fec, 
										dni, nom, med, sede, correo_electronico, numero_contacto, 
										id_tipo_documento_facturacion, ruc, raz, direccionfiscal, 
										t_ser,
										pak, ser, mon, tot, descuento, gratuito, total_cancelar, 
										bolsa_plastico, t1, m1, p1, banco1, tipotarjeta1, numerocuotas1,
										t2, m2, p2, banco2, tipotarjeta2, numerocuotas2, 
										t3, m3, p3, banco3, tipotarjeta3, numerocuotas3, 
										anu, veri, man_ini, man_fin, anglo, userx, comentarios,
										comprobante_referencia, 
										estado, 
										idusercreate, createdate, 
										action, 
										pos1_id, pos2_id, pos3_id
								)
							SELECT 
								id, tip, 
								sede_pago_id, 
								cli_atencion_unica_id, 
								fec, 
								dni, nom, med, sede, correo_electronico, numero_contacto, 
								id_tipo_documento_facturacion, ruc, raz, direccionfiscal,
								t_ser, 
								pak, ser, mon, tot, descuento, gratuito, total_cancelar, 
								bolsa_plastico, t1, m1, p1, banco1, tipotarjeta1, numerocuotas1,
								t2, m2, p2, banco2, tipotarjeta2, numerocuotas2, 
								t3, m3, p3, banco3, tipotarjeta3, numerocuotas3, 
								anu, veri, man_ini, man_fin, anglo, userx, comentarios, 
								comprobante_referencia, 
								estado, 
								iduserupdate, updatex, 
								'U',
								pos1_id, pos2_id, pos3_id
							FROM appinmater_modulo.recibos
							WHERE id=? and tip=?");
			$log_Recibos->execute(array($item["id_recibo"],$item["tip_recibo"]));
	}
?>
</body>
</html>