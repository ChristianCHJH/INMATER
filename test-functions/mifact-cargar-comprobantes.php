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
	global $db_mifact;

	$stmt = $db->prepare("SELECT *
		from recibos
		where year(createdate) = 2021
		order by fec desc;");
	$stmt->execute();
	// print("<p>total de registros: " .  $stmt->rowCount() . "</p>");
  $data = $stmt->fetchAll();

  foreach ($data as $item) {
    $stmt_mifact = $db_mifact->prepare("SELECT tip, id
      from recibos
      where tip=? and id=?;");
	  $stmt_mifact->execute([$item["tip"], $item["id"]]);
    if ($stmt_mifact->rowCount() > 0) {
      // update
      /* print("<p>ini update: ".$item["tip"]."-".$item["id"]."</p>");
      print("<pre>"); print_r($item); print("</pre>");
      print("<p>fin update: ".$item["tip"]."-".$item["id"]."</p>"); */
      $stmt_mifact1 = $db_mifact->prepare("UPDATE recibos set
        sede_pago_id=?, cpe_serie=?, cpe_correlativo=?, fec=?, fecha_vencimiento=?
        , dni=?, nom=?, med=?, sede=?, correo_electronico=?, numero_contacto=?, id_tipo_documento_facturacion=?
        , ruc=?, raz=?, direccionfiscal=?, t_ser=?, pak=?, ser=?, mon=?, tot=?, descuento=?, gratuito=?
        , total_cancelar=?, bolsa_plastico=?
        , t1=?, m1=?, p1=?, banco1=?, tipotarjeta1=?, numerocuotas1=?
        , t2=?, m2=?, p2=?, banco2=?, tipotarjeta2=?, numerocuotas2=?
        , t3=?, m3=?, p3=?, banco3=?, tipotarjeta3=?, numerocuotas3=?
        , anu=?, veri=?, `userx`=?, comentarios=?, comprobante_referencia=?
        , estado=?, idusercreate=?, createdate=?,updatex=?
        where tip=? and id=?;");
      $hora_actual = date("Y-m-d H:i:s");
      $stmt_mifact1->execute([
        $item["sede_pago_id"], $item["cpe_serie"], $item["cpe_correlativo"], $item["fec"], $item["fecha_vencimiento"]
        , $item["dni"], $item["nom"], $item["med"], $item["sede"], $item["correo_electronico"], $item["numero_contacto"], $item["id_tipo_documento_facturacion"]
        , $item["ruc"], $item["raz"], $item["direccionfiscal"], $item["t_ser"], $item["pak"], $item["ser"], $item["mon"], $item["tot"], $item["descuento"], $item["gratuito"]
        , $item["total_cancelar"], $item["bolsa_plastico"]
        , $item["t1"], $item["m1"], $item["p1"], $item["banco1"], $item["tipotarjeta1"], $item["numerocuotas1"]
        , $item["t2"], $item["m2"], $item["p2"], $item["banco2"], $item["tipotarjeta2"], $item["numerocuotas2"]
        , $item["t3"], $item["m3"], $item["p3"], $item["banco3"], $item["tipotarjeta3"], $item["numerocuotas3"]
        , $item["anu"], $item["veri"], $item["userx"], $item["comentarios"], $item["comprobante_referencia"]
        , $item["estado"], $item["idusercreate"], $item["createdate"],$hora_actual
        , $item["tip"], $item["id"]
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
      $log_Recibos->execute(array($item["id"], $item["tip"]));
    } else {
      // create
      /* print("<p>ini create: ".$item["tip"]."-".$item["id"]."</p>");
      print("<pre>"); print_r($item); print("</pre>");
      print("<p>fin create: ".$item["tip"]."-".$item["id"]."</p>"); */
      $stmt_mifact1 = $db_mifact->prepare("INSERT into recibos (
        id, tip, sede_pago_id, cpe_serie, cpe_correlativo, fec, fecha_vencimiento
      , dni, nom, med, sede, correo_electronico, numero_contacto, id_tipo_documento_facturacion
      , ruc, raz, direccionfiscal, t_ser, pak, ser, mon, tot, descuento, gratuito
      , total_cancelar, bolsa_plastico
      , t1, m1, p1, banco1, tipotarjeta1, numerocuotas1
      , t2, m2, p2, banco2, tipotarjeta2, numerocuotas2
      , t3, m3, p3, banco3, tipotarjeta3, numerocuotas3
      , anu, veri, `userx`, comentarios, comprobante_referencia
      , estado, idusercreate, createdate) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
      $stmt_mifact1->execute([
        $item["id"], $item["tip"], $item["sede_pago_id"], $item["cpe_serie"], $item["cpe_correlativo"], $item["fec"], $item["fecha_vencimiento"]
        , $item["dni"], $item["nom"], $item["med"], $item["sede"], $item["correo_electronico"], $item["numero_contacto"], $item["id_tipo_documento_facturacion"]
        , $item["ruc"], $item["raz"], $item["direccionfiscal"], $item["t_ser"], $item["pak"], $item["ser"], $item["mon"], $item["tot"], $item["descuento"], $item["gratuito"]
        , $item["total_cancelar"], $item["bolsa_plastico"]
        , $item["t1"], $item["m1"], $item["p1"], $item["banco1"], $item["tipotarjeta1"], $item["numerocuotas1"]
        , $item["t2"], $item["m2"], $item["p2"], $item["banco2"], $item["tipotarjeta2"], $item["numerocuotas2"]
        , $item["t3"], $item["m3"], $item["p3"], $item["banco3"], $item["tipotarjeta3"], $item["numerocuotas3"]
        , $item["anu"], $item["veri"], $item["userx"], $item["comentarios"], $item["comprobante_referencia"]
        , $item["estado"], $item["idusercreate"], $item["createdate"]
      ]);
      $id_recibo = $db->lastInsertId();
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
                idusercreate, createdate, 
                'I',
                pos1_id, pos2_id, pos3_id
            FROM appinmater_modulo.recibos
            WHERE id=? and tip=?");
      $log_Recibos->execute(array($item["id"], $item["tip"]));
    }
	}
?>
</body>
</html>