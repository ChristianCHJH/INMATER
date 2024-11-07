<?php
date_default_timezone_set('America/Lima');
require $_SERVER["DOCUMENT_ROOT"] . "/_database/database.php";
require $_SERVER["DOCUMENT_ROOT"] . "/config/environment.php";

if (isset($_POST['action']) && $_POST['action'] == 'guardarUsuario') {
    $ruta = $_POST['ruta'];
    $usercreate = $_POST['usercreate'];
    $tip_doc = $_POST['tip_doc'];
    $documento = $_POST['documento'];
    
    if (saveValidateUsers($ruta, $usercreate, $tip_doc, $documento)) {
        echo json_encode(array("status" => "success", "message" => "Usuario guardado exitosamente"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error al guardar el usuario ".$ruta." - ".$usercreate." - ".$tip_doc." - ".$documento." "));
    }
}elseif (isset($_POST['action']) && $_POST['action'] == 'sedeEmpresa') {
    http_response_code(200);
    echo sedeEmpresa($_POST['idEmpresa']);
}

function recibo($id, $fec, $dni, $nom, $med, $sede, $validacion, $tip, $ruc, $raz, $t_ser, $pak, $ser, $mon, $tot, $t1, $m1, $p1, $t2, $m2, $p2, $t3, $m3, $p3, $man_ini, $man_fin, $cadena, $login, $id_empresa,$id_sede, $comentarios="",$comprobante_referencia="", $direccionfiscal="", $correo_electronico = "", $datos = array())
{
    global $db;
    $anglo = '';
    $procedimiento_id = !empty($datos["procedimiento_id"]) ? intval($datos["procedimiento_id"]) : 0;

    $stmt = $db->prepare("SELECT * from man_empresas WHERE id=?;");
    $stmt->execute([$id_empresa]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt5 = $db->prepare("SELECT sucursal_anglo from sedes WHERE id=?;");
    $stmt5->execute([$id_sede]);
    $sedeAnglo = $stmt5->fetch(PDO::FETCH_ASSOC);

    if ($id == "") {
        $stmt = $db->prepare("SELECT sede_id from usuario WHERE userx=?;");
        $stmt->execute([$login]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $sede_pago_id = $id_sede;
        if ($id_empresa === '4') {
            $sede_pago_id = 3;
        }

        $Rpop = $db->prepare("SELECT id FROM recibos WHERE tip=? ORDER BY id DESC LIMIT 1;");
        $Rpop->execute(array($tip));

        if ($Rpop->rowCount() == 1) {
            $pop = $Rpop->fetch(PDO::FETCH_ASSOC);
            $id = $pop['id'] + 1;
        } else {
            $id = 1;
        }

        if ($cadena <> '' && ($t_ser == 4 || $t_ser == 5 || $t_ser == 7)) {
            $Rpaci = $db->prepare("SELECT tip, nom, ape, fnac FROM hc_paciente WHERE dni = ?;");
            $Rpaci->execute(array($dni));

            if ($Rpaci->rowCount() == 1) {
                $sex = 'F';
            } else {
                $sex = 'M';
                $Rpaci = $db->prepare("SELECT p_tip AS tip, p_nom AS nom, p_ape AS ape, p_fnac AS fnac FROM hc_pareja WHERE p_dni = ?;");
                $Rpaci->execute(array($dni));
            }

            $paci = $Rpaci->fetch(PDO::FETCH_ASSOC);

            // corregir formato de tipo de documento para anglolab
            if ($paci['tip'] == 'CE') {
                $paci['tip'] = 'CEX';
            }

            $ape = explode(' ', $paci['ape'], 2);
            $apepaterno = $apematerno = "";

            if (isset($ape[0]) && !empty($ape[0])) {
                $apepaterno = $ape[0];
            }

            if (isset($ape[1]) && !empty($ape[1])) {
                $apematerno = $ape[1];
            }

            // buscar cmp de medico
            $medico_cmp = '';
            $stmt_med = $db->prepare("SELECT cmp FROM usuario WHERE nom = ?;");
            $stmt_med->execute([$med]);

            if ($stmt_med->rowCount() != 0) {
                $data_med = $stmt_med->fetch(PDO::FETCH_ASSOC);
                $medico_cmp = (string) $data_med['cmp'];
                $medico_cmp = str_pad($medico_cmp, 6, "0", STR_PAD_LEFT);
            }

            $medico = explode(' ', $med, 2);
            $medpaterno = $medmaterno = "";

            if (isset($medico[0]) && !empty($medico[0])) {
                $medpaterno = $medico[0];
            }

            if (isset($medico[1]) && !empty($medico[1])) {
                $medmaterno = $medico[1];
            }
            
            // numero de cama
            $cadena_array = explode(",", $cadena);
            $cadena_ordenada = '';
            
            $numero_cama = '';
            $movilidad_codigo = '';
            
            foreach ($cadena_array as $key => $value) {
                if (in_array($value, $_ENV["anglolab_anillos"])) {
                    $numero_cama = '999';
                    $movilidad_codigo = $value;
                } else {
                    $cadena_ordenada .= $value . ',';
                }
            }
            
            if ($numero_cama == '999') {
                $cadena_ordenada .= $movilidad_codigo;
            } else {
                $cadena_ordenada = substr($cadena_ordenada, 0, strlen($cadena_ordenada) - 1);
            }

            $sucursal = $sedeAnglo["sucursal_anglo"];
            $clienteAnglo = $empresa["clienteanglo"];
            $codigoTarifaAnglo=$empresa["codigotarifaanglo"];
            $codigoRespuestaAnglo = $empresa["codigorespuestaanglo"];
            $registroAnglo = $empresa["metodoregisanglo"];


            if ($procedimiento_id == 121) {
                $codigoTarifaAnglo='PT3159';
            }


            $param = [
                'Dato' => "|" . $id . "-" . $tip . "|" . $id . "-" . $tip . "|" . date("d/m/Y", strtotime($fec)) . "|$sucursal|$clienteAnglo|$codigoTarifaAnglo||||AMBULATORIO|NORMAL|" . $numero_cama . "|R|" . $dni . "|". $paci['tip'] . "|" . $dni . "|" . $paci['nom'] . "|" . $apepaterno . "|" . $apematerno . "|" . $sex . "|" . date("d/m/Y", strtotime($paci['fnac'])) . "|" . $direccionfiscal ."|" . $datos["numero_contacto"] . "|||20544478096|INMATER|" . $medico_cmp . "|" . $medpaterno . "|" . $medmaterno . "||" . $cadena_ordenada . "|"
            ];
            

            $client = new nusoap_client($_ENV["anglolab_ws"], 'wsdl');

            
            $client->soap_defencoding = 'UTF-8';
            $client->decode_utf8 = FALSE;
            $error = $client->getError();

            if ($error) {
                $anglo = 'Error 4 anglolab: <pre>' . $error . '</pre>';
            }

            $result = $client->call($registroAnglo, $param);

            if ($client->fault) {
                $anglo = 'Error 3 anglolab: <pre>' . $result . '</pre>';
            } else {
                $error = $client->getError();

                if ($error) {
                    $anglo = 'Error 2 anglolab: <pre>' . $error . '</pre>';
                } else {
                    $anglo = 'Anglolab Respuesta: <pre>' . $result[$codigoRespuestaAnglo] . '</pre>';
                    echo $anglo;
                }
            }

            // registrar log anglolab
            $stmt_anglo = $db->prepare("INSERT INTO factu_anglolab_response (recibo_tip, recibo_id, request, response, idusercreate) VALUES (?, ?, ?, ?, ?)");
            $stmt_anglo->execute([$tip, $id, '<pre>' . $param['Dato'] . '</pre>', $anglo, $login]);
        }
        $t1 = !empty($t1) ? $t1 : 0;
        $t2 = !empty($t2) ? $t2 : 0;
        $t3 = !empty($t3) ? $t3 : 0;
        $p2 = !empty($p2) ? $p2 : 0;
        $p3 = !empty($p3) ? $p3 : 0;
        $m1 = !empty($m1) ? intval($m1) : 0;
        $m2 = !empty($m2) ? intval($m2) : 0;
        $m3 = !empty($m3) ? intval($m3) : 0;
        $banco1 = !empty($datos["banco1"]) ? intval($datos["banco1"]) : 0;
        $banco2 = !empty($datos["banco2"]) ? intval($datos["banco2"]) : 0;
        $banco3 = !empty($datos["banco3"]) ? intval($datos["banco3"]) : 0;
        $numerocuotas1 = !empty($datos["numerocuotas1"]) ? intval($datos["numerocuotas1"]) : 0;
        $numerocuotas2 = !empty($datos["numerocuotas2"]) ? intval($datos["numerocuotas2"]) : 0;
        $numerocuotas3 = !empty($datos["numerocuotas3"]) ? intval($datos["numerocuotas3"]) : 0;
        $tipotarjeta1 = !empty($datos["tipotarjeta1"]) ? intval($datos["tipotarjeta1"]) : 0;
        $tipotarjeta2 = !empty($datos["tipotarjeta2"]) ? intval($datos["tipotarjeta2"]) : 0;
        $tipotarjeta3 = !empty($datos["tipotarjeta3"]) ? intval($datos["tipotarjeta3"]) : 0;
        $pos1 = !empty($datos["pos1"]) ? intval($datos["pos1"]) : 1;
        $pos2 = !empty($datos["pos2"]) ? intval($datos["pos2"]) : 1;
        $pos3 = !empty($datos["pos3"]) ? intval($datos["pos3"]) : 1;
        $fecha_vencimiento = !empty($datos["fecha_vencimiento"]) ? $datos["fecha_vencimiento"] : '1899-12-30';
        $descuento = !empty($datos["descuento"]) ? doubleval($datos["descuento"]) : 0;
        $gratuito = !empty($datos["gratuito"]) ? intval($datos["gratuito"]) : 0;
        $validacion = !empty($validacion) ? intval($validacion) : 0;
        $stmt = $db->prepare(

            "INSERT INTO recibos
            (id, tip, sede_pago_id, cli_atencion_unica_id, fec, dni, nom, med, sede, id_tipo_documento_facturacion, ruc, raz, direccionfiscal,
             t_ser, programa_id, procedimiento_id, pak, ser, mon, tot, descuento, gratuito, bolsa_plastico, t1, m1, p1, banco1, tipotarjeta1, 
             numerocuotas1, t2, m2, p2, banco2, tipotarjeta2, numerocuotas2, t3, m3, p3, banco3, tipotarjeta3, numerocuotas3, man_ini, man_fin, 
             anglo, userx, comentarios, correo_electronico, numero_contacto, comprobante_referencia, total_cancelar, condicion_pago_id, fecha_vencimiento, 
             idusercreate, pos1_id, pos2_id, pos3_id,verificacion,id_empresa,id_empresa_sede) VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?)"
        );

        $stmt->execute([$id, $tip, $sede_pago_id, $datos["cli_atencion_unica_id"], $fec, $dni, $nom, $med, $sede, $datos["tipo_documento_facturacion"], $ruc, $raz, $direccionfiscal, $t_ser, $datos["programa_id"], $procedimiento_id, $pak, $ser, $mon, $tot, $descuento, $gratuito, $datos["bolsa_plastico"], $t1, $m1, $p1, $banco1, $tipotarjeta1, $numerocuotas1, $t2, $m2, $p2, $banco2, $tipotarjeta2, $numerocuotas2, $t3, $m3, $p3, $banco3, $tipotarjeta3, $numerocuotas3, $man_ini, $man_fin, $anglo, $login, $comentarios, $correo_electronico, $datos["numero_contacto"] ,$datos["comprobante_referencia"] ,$datos["total_cancelar"], $datos["condicion_pago_id"], $fecha_vencimiento, $login,$pos1,$pos2,$pos3,$validacion,$id_empresa,$id_sede]);
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
        $log_Recibos->execute(array($id, $tip));
        require("db_facturacion_electronica.php");
        $serie = "";
        $correlativo = 0;

        switch ($tip) {
            case '1':
                // serie
                $consulta = $db->prepare("SELECT valor from man_configuracion where codigo = 'serie_boleta' and estado = 1 and sede_id = ?");
                $consulta->execute([$sede_pago_id]);
                $info = $consulta->fetch(PDO::FETCH_ASSOC);
                $serie = $info["valor"];

                // correlativo
                $consulta = $db->prepare("SELECT valor from man_correlativos where codigo = 'boletas' and estado = 1 and sede_id = ?");
                $consulta->execute([$sede_pago_id]);
                $info = $consulta->fetch(PDO::FETCH_ASSOC);
                $correlativo = $info["valor"]+1;

                // actualizar correlativo
                $stmt = $db->prepare("UPDATE man_correlativos SET valor=? WHERE codigo = 'boletas' and estado=1 and sede_id=?");
                $stmt->execute(array($correlativo, $sede_pago_id));

                break;
            case '2':
                // serie
                $consulta = $db->prepare("SELECT valor from man_configuracion where codigo = 'serie_factura' and estado = 1 and sede_id = ?");
                $consulta->execute([$sede_pago_id]);
                $info = $consulta->fetch(PDO::FETCH_ASSOC);
                $serie = $info["valor"];

                // correlativo
                $consulta = $db->prepare("SELECT valor from man_correlativos where codigo = 'facturas' and estado = 1 and sede_id = ?");
                $consulta->execute([$sede_pago_id]);
                $info = $consulta->fetch(PDO::FETCH_ASSOC);
                $correlativo = $info["valor"]+1;

                // actualizar correlativo
                $stmt = $db->prepare("UPDATE man_correlativos SET valor=? WHERE codigo = 'facturas' and estado=1 and sede_id=?");
                $stmt->execute(array($correlativo, $sede_pago_id));

                break;
            default: break;
        }

        $moneda = "";

        if ($t_ser == 1 || $t_ser == 2 || $t_ser == 3) {
            if ($mon == 1) {$moneda="USD";} else {$moneda="PEN";}
        } else {
            if ($mon == 1) {$moneda="PEN";} else {$moneda="USD";}
        }

        // tipo documento facturacion
        $tipo_documento_facturacion = "";
        $consulta = $db->prepare("SELECT codigo_facturacion_electronica codigo from man_tipo_documento_facturacion where estado = 1 and id = ?");
        $consulta->execute(array($datos["tipo_documento_facturacion"]));
        $info = $consulta->fetch(PDO::FETCH_ASSOC);
        $tipo_documento_facturacion = $info["codigo"];

        $data = array(
            "id" => $id,
            // 
            "fec" => date('Y-m-d', strtotime($fec)), // fecha
            "dni" => $dni, // dni paciente
            "nom" => $nom, // nombre de paciente
            "med" => $med, // medico
            "sede" => $sede, // sede
            "tip" => $tip, // tipo de documento (boleta, factura)
            "correo_electronico" => $correo_electronico,
            "tipo_documento_facturacion" => $tipo_documento_facturacion,
            "ruc" => $ruc, // ruc
            "raz" => $raz, // razon social
            "t_ser" => $t_ser, // tipo de servicio
            "pak" => $pak, // paquete medico
            "ser" => $ser, // detalle de productos
            "mon" => $mon, // tipo de cambio
            "tot" => floatval($tot) - floatval($datos["descuento"]), // total
            "descuento" => $datos["descuento"], // descuento
            "bolsa_plastico" => $datos["bolsa_plastico"], // cantidad bolsa de plastico
            //medios de pago
            "t1" => $t1,
            "m1" => $m1,
            "p1" => $p1,
            "t2" => $t2,
            "m2" => $m2,
            "p2" => $p2,
            "t3" => $t3,
            "m3" => $m3,
            "p3" => $p3,
            // mantenimiento de embriones
            "man_ini" => $man_ini,
            "man_fin" => $man_fin,
            // 
            "cadena" => $cadena, // cadena para anglolab
            "login" => $login,
            "comentarios" => $comentarios,
            "direccionfiscal" => $direccionfiscal,
            "serie" => $serie,
            "correlativo" => sprintf('%08d', $correlativo),
            "moneda" => $moneda,
            "gratuito" => $datos["gratuito"],
            "condicion_pago_id" => $datos["condicion_pago_id"],
            "fecha_vencimiento" => $datos["fecha_vencimiento"],
            "empresa" => $empresa
        );

        $request = json_encode(cargar_facturacion_electronica($data), true);
        $response = enviar_facturacion_electronica($request, "/SendInvoice", $empresa);

        // grabar log
        $stmt = $db->prepare('INSERT INTO factu_mifact_response (recibo_tip, recibo_id, request, response, error, idusercreate) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$tip, $id, $request, $response["cadena_para_codigo_qr"], $response["errors"], $login]);
        // grabar respuesta
        $stmt = $db->prepare("INSERT INTO facturacion_recibo_mifact_response (
            id_recibo, tip_recibo
            , cadena_para_codigo_qr, cdr_sunat, codigo_hash, correlativo_cpe, errors, estado_documento, pdf_bytes, serie_cpe, sunat_description, sunat_note, sunat_responsecode, ticket_sunat, tipo_cpe, url, xml_enviado
            , idusercreate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($id, $tip, $response["cadena_para_codigo_qr"], $response["cdr_sunat"], $response["codigo_hash"], $response["correlativo_cpe"], $response["errors"], $response["estado_documento"], $response["pdf_bytes"], $response["serie_cpe"], $response["sunat_description"], $response["sunat_note"], $response["sunat_responsecode"], $response["ticket_sunat"], $response["tipo_cpe"], $response["url"], $response["xml_enviado"], $login));

				// actualizo el recibo con los datos de la serie y el correlativo
				$stmt = $db->prepare("UPDATE recibos set cpe_serie=?, cpe_correlativo=?,iduserupdate=?,updatex=? where tip=? and id=?;");
                $hora_actual = date("Y-m-d H:i:s");
				$stmt->execute([$response["serie_cpe"], $response["correlativo_cpe"],$login,$hora_actual, $tip, $id]);
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
                    WHERE id=? AND tip=?");
                $log_Recibos->execute(array($id,$tip));

                //GUARDAR ESTADO EN RECIBO
                $stmt = $db->prepare('UPDATE recibos SET status_mi_fac=? WHERE id=? and tip=?');
                $stmt->execute([$response["estado_documento"],$id, $tip]); 
                
    } else {
        $t1 = !empty($t1) ? $t1 : 0;
        $t2 = !empty($t2) ? $t2 : 0;
        $t3 = !empty($t3) ? $t3 : 0;
        $p2 = !empty($p2) ? $p2 : 0;
        $p3 = !empty($p3) ? $p3 : 0;
        $m1 = !empty($m1) ? intval($m1) : 0;
        $m2 = !empty($m2) ? intval($m2) : 0;
        $m3 = !empty($m3) ? intval($m3) : 0;
        $banco1 = !empty($datos["banco1"]) ? intval($datos["banco1"]) : 0;
        $banco2 = !empty($datos["banco2"]) ? intval($datos["banco2"]) : 0;
        $banco3 = !empty($datos["banco3"]) ? intval($datos["banco3"]) : 0;
        $numerocuotas1 = !empty($datos["numerocuotas1"]) ? intval($datos["numerocuotas1"]) : 1;
        $numerocuotas2 = !empty($datos["numerocuotas2"]) ? intval($datos["numerocuotas2"]) : 1;
        $numerocuotas3 = !empty($datos["numerocuotas3"]) ? intval($datos["numerocuotas3"]) : 1;
        if($p2 == 0){$numerocuotas2 = 0;}
        if($p3 == 0){$numerocuotas3 = 0;}
        $tipotarjeta1 = !empty($datos["tipotarjeta1"]) ? intval($datos["tipotarjeta1"]) : 0;
        $tipotarjeta2 = !empty($datos["tipotarjeta2"]) ? intval($datos["tipotarjeta2"]) : 0;
        $tipotarjeta3 = !empty($datos["tipotarjeta3"]) ? intval($datos["tipotarjeta3"]) : 0;
        $pos1 = !empty($datos["pos1"]) ? intval($datos["pos1"]) : 1;
        $pos2 = !empty($datos["pos2"]) ? intval($datos["pos2"]) : 1;
        $pos3 = !empty($datos["pos3"]) ? intval($datos["pos3"]) : 1;
        $stmt = $db->prepare("UPDATE recibos
        set programa_id=?, med=?, sede=?, ruc=?, raz=?, direccionfiscal=?,
        t1=?, m1=?, p1=?, banco1=?, tipotarjeta1=?, numerocuotas1=?,
        t2=?, m2=?, p2=?, banco2=?, tipotarjeta2=?, numerocuotas2=?,
        t3=?, m3=?, p3=?, banco3=?, tipotarjeta3=?, numerocuotas3=?,
        correo_electronico=?,comprobante_referencia=?,
        man_ini=?, man_fin=?, comentarios=?, iduserupdate=?,updatex=?, pos1_id=?, pos2_id=?, pos3_id=?
        where id=? and tip=?");
        $hora_actual = date("Y-m-d H:i:s");
        $stmt->execute(array($datos['programa_id'],$med, $sede, $ruc, $raz, $direccionfiscal,
        $t1, $m1, $p1, $banco1, $tipotarjeta1, $numerocuotas1,
        $t2, $m2, $p2, $banco2, $tipotarjeta2, $numerocuotas2,
        $t3, $m3, $p3, $banco3, $tipotarjeta3, $numerocuotas3,
        $correo_electronico,$comprobante_referencia,
        $man_ini, $man_fin, $comentarios, $login, $hora_actual, $pos1, $pos2, $pos3, $id, $tip));
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
        $log_Recibos->execute(array($id,$tip));
    }
    
    if ($tip == 2) {
        ValidarRazonSocialPagos($ruc, $raz, $direccionfiscal, $login);
    } ?>
    <script type="text/javascript">
        var x = "<?php echo $id; ?>";
        var y = "<?php echo $tip; ?>";
        window.open("pago_imp_pdf.php?id=" + x + "&tip=" + y, '_blank');
    </script>
    <?php
    
    $url = 'https://nd-be-pacientes-id6qsbxfsa-uc.a.run.app/api/facturacion/serviciosRecibos';
    $response = file_get_contents($url);

}

function validarMontoTotalItems($data) {
    $i = 1;
    $cadena = $data["ser"];
    $total = substr_count($data['ser'], "</tr>");
    $items = [];
    $valorventacod = "";
    $total_items = 0;

    while ($i <= $total) {
        $pos = strpos($cadena, "</tr>");
        $tam = strlen($cadena);
        $servicios = substr($cadena, 4, $pos-4);
        $cadena = substr($cadena, $pos+5, $tam-3);
        // $demo="";
        $valorventacod="";
        // $idservicio="";
        $idserviciopos = strpos($servicios, "</td>");
        $tamservicio = strlen($servicios); // add
        // $idservicio = substr($servicios, 4, $idserviciopos-4);
        $cadena1 = substr($servicios, $idserviciopos+5, $tamservicio-3); // add
        $demopos = strpos($cadena1, "</td>"); // add
        $tamdemo = strlen($cadena1); // add
        // $demo = substr($cadena1, 4, $demopos-4); // add
        $valorventa = substr($cadena1, $demopos+5, $tamdemo-3); // add
        $valorventacod = substr($valorventa, 4, strlen($valorventacod)-5);
        if ($data['tipo_comprobante'] == 2) {
            $total_items += number_format((float)$valorventacod*1.18, 2, '.', '');
        } else {
            $total_items += number_format((float)$valorventacod, 2, '.', '');
        }
        
        $i++;
    }

    if ($data["total"] == $total_items) {
        return true;
    } else {
        return false;
    }
    /* return [
        'total_global' => $data["total"],
        'total_items' => $total_items,
    ]; */
}

function ValidarRazonSocialPagos($ruc, $razonsocial, $direccionfiscal, $iduser)
{
    global $db;
    // validar si existe el ruc
    $consulta = $db->prepare("SELECT id FROM man_razonsocial_pagos WHERE ruc=? and estado=1");
    $consulta->execute(array($ruc));
    if ($consulta->rowCount() == 0) {
        $stmt = $db->prepare("INSERT INTO man_razonsocial_pagos (nombre, ruc, direccionfiscal, idusercreate) VALUES (?, ?, ?, ?)");
        $stmt->execute(array($razonsocial, $ruc, $direccionfiscal, $iduser));
    } else {
        $data = $consulta->fetch(PDO::FETCH_ASSOC);
        $stmt = $db->prepare("UPDATE man_razonsocial_pagos SET nombre=?, direccionfiscal=?, iduserupdate=? WHERE id=?");
        $stmt->execute(array($razonsocial, $direccionfiscal, $iduser, $data["id"]));
    }
}

function saveValidateUsers($ruta, $usercreate, $tip_doc, $documento) {
    global $db;

    try {
        $stmt = $db->prepare("INSERT INTO appinmater_log.factu_validacion_api (ruta, usercreate, tip_doc, documento) VALUES (?, ?, ?, ?)");
        $stmt->execute(array($ruta, $usercreate, $tip_doc, $documento));

        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function sedeEmpresa($id){
    global $db;
    $consulta = $db->prepare("SELECT id, nombre FROM sedes WHERE id_empresa=? and estado = 1");
    $consulta->execute(array($id));

    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $jsonResultados = json_encode($resultados);

    return $jsonResultados;
}