<?php
    ini_set("display_errors","1");
    error_reporting(E_ALL);
    /* error_reporting(error_reporting() & ~E_NOTICE); */
    date_default_timezone_set('America/Lima');
    require($_SERVER["DOCUMENT_ROOT"]."/_database/database.php");
    require($_SERVER["DOCUMENT_ROOT"]."/config/environment.php");

    function cargar_facturacion_electronica($data) {
        $mnt_tot = 0;
        $mnt_tot_trib_igv = 0;
        $mnt_tot_gravado = 0;
        $mnt_tot_gratuito = 0;
        $mnt_tot_exonerado = 0;
        $cod_tip_ope_sunat = "0101";
        if ($data['gratuito'] == 1) {
            $mnt_tot_gratuito = number_format((float)$data["tot"]/ 1.18, 2, '.', '');
        } else {
            $mnt_tot = number_format((float)$data["tot"] + $data['bolsa_plastico'] * 0.10, 2, '.', '');
            $mnt_tot_trib_igv = number_format((float)$data["tot"] * 0.18 / 1.18, 2, '.', '');
            $mnt_tot_gravado = number_format((float)$data["tot"] / 1.18, 2, '.', '');
        }
        // documentos no domiciliados
        if ($data["tipo_documento_facturacion"] == "0") {
            $mnt_tot_exonerado = number_format((float)$data["tot"], 2, '.', '');
            $mnt_tot_trib_igv = 0;
            $mnt_tot_gravado = 0;
            $cod_tip_ope_sunat = "0401";
        }
        
        $mifact = [
            "TOKEN" => $data["empresa"]["token"], // TOKEN UNICO POR EMPRESA (este token o clave sera rotativo cada cierto tiempo por ejemplo cada 6 meses se cambiara, previa comunicación)
            // datos del emisor
            "COD_TIP_NIF_EMIS" => $data["empresa"]["cod_tip_nif_emis"],
            "NUM_NIF_EMIS" => $data["empresa"]["num_nif_emis"], // RUC emisor 
            "NOM_RZN_SOC_EMIS" => $data["empresa"]["nom_rzn_soc_emis"], // Razon social emisor (tal cual esta registrado en SUNAT)
            "NOM_COMER_EMIS" => $data["empresa"]["nom_comer_emis"], // nombre comercial emisor (tal cual esta registrado en SUNAT si no tiene nombre comercial no enviar este tag)
            "COD_UBI_EMIS" => $data["empresa"]["cod_ubi_emis"],  // Ubigeo de direccion emisor, ver codigo ubigeo de inei
            "TXT_DMCL_FISC_EMIS" => $data["empresa"]["txt_dmcl_fisc_emis"], // Direccion fiscal emisor
            // datos del receptor
            "COD_TIP_NIF_RECP" => $data["tipo_documento_facturacion"], // Tipo RUC receptor 6 es RUC, 1 es DNI, 0 ES DOC.TRIB.NO.DOM.SIN.RUC (para el caso de boletas si no tiene DNI colocar el codigo 0)
            "NUM_NIF_RECP" => $data["ruc"], // Numero documento receptor (para el caso de boletas si no tiene DNI colocar el numero 99999999)
            "NOM_RZN_SOC_RECP" => $data["raz"], // Nombre o Razon social Receptor (para el caso de boleta si no tiene DNI colocar CLIENTE SIN NOMBRE)
            "TXT_DMCL_FISC_RECEP" => $data["direccionfiscal"], // Direccion receptor (para el caso de boleta si no tiene DNI colocar SIN DIRECCION)
            // datos del documento
            "FEC_EMIS" => $data["fec"], // fecha emision (respetar el formato de la fecha)
            "FEC_VENCIMIENTO" => $data["condicion_pago_id"] == "2" ? $data["fecha_vencimiento"]: $data["fec"], // Fecha de vencimiento del documento (respetar el formato de la fecha - opcional)
            "COD_TIP_CPE" => $data["tip"] == "2" ? "01" : "03", // tipo documento (01 FACTURA, 03 BOLETA, 07 NOTA DE CRÉDITO, 08 NOTA DE CRÉDITO, 09 GUIA REMISION REMITENTE)
            "NUM_SERIE_CPE" => $data["serie"], // Serie del documento alfanumerico (siempre La letra F al inicio para facturas, letra B para boletas, para nota de credito y debito factura la letra F, para nota de credito y debito boleta la letra B al inicio)
            "NUM_CORRE_CPE" => $data["correlativo"], // Correlativo del documento (su sistema es el que genera el correlativo, el WS no genera el correlativo)
            "COD_MND" => $data["moneda"], // tipo moneda de venta USD es dolares, PEN es soles (PEN: soles, USD: dólar americano, EUR: euro)
            "MailEnvio" => $data["correo_electronico"], // email del cliente (si no tiene este dato, no enviar esta linea)
            "COD_IMPRE_DEST" => "", // impresora destino, solo para ticketera, solo en la version offline (solo para Ws instalados de forma local en su servidor)
            "COD_PRCD_CARGA" => "001", // procedencia de carga para web service y apis es 001 siempre (procedencia de carga para web service y apis es 001 siempre)
            "MNT_TOT_GRAVADO" => $mnt_tot_gravado, // monto neto de venta sin IGV - base imponible (la suma de todos los item que estan afecto a IGV - operación gravada o base imponible, no incluir los exonerado, inafectos o gratuitos)
            "MNT_TOT_INAFECTO" => "", // monto total de venta inafecto (la suma de todos los item que estan inafecto del IGV)
            "MNT_TOT_EXONERADO" => $mnt_tot_exonerado, // monto total de venta exonerada (la suma de todos los item que estan exonerado del IGV)
            "MNT_TOT_GRATUITO" => $mnt_tot_gratuito, // monto total de venta gratuita (la suma de todos los item que estan exonerado del IGV)
            "MNT_TOT_DESCUENTO" => "", // Monto total descuento sin Igv (suma total del descuento - suma de descuentos de los items)
            "MNT_DSCTO_GLOB" => number_format((float)$data["descuento"] / 1.18, 2, '.', ''), // Monto total Descuento Global sin IGV (suma total del descuento - descuento global, descuento total de la factura no incluir los descuentos de los items)
            "MNT_TOT_OTR_CGO" => "", // monto total de recargo o cargo global (recargo al documento, este recargo no esta afecto a IGV y tampoco forma parte de la base imponible y es parte del total de la venta, se puede utilizar para recargo a restaurantes, propinas etc, consultar con su contador)
            "MNT_TOT_TRIB_IGV" => $mnt_tot_trib_igv, // monto total IGV (suma de IGV total de cada  producto(item) o servicios que estan afecto a IGV)
            "MNT_TOT_TRIB_ISC" => "",
            "MNT_TOT_TRIB_OTR" => "",
            "MNT_TOT" => $mnt_tot, // monto total documento a pagar (gravado+inafecto+gratuito+exonerado-DSCTO+Cargo-Anticipo) (suma de todos los item operación gravada, exonerado , inafecto , IGV, descuento global - total a cobrar)
            "COD_PTO_VENTA" => $data['login'],
            "TIP_CAMBIO" => "",
            "COD_FORM_IMPR" => "003", // formato de impresión (004: ticket termico, 001: A4, 003: ticket matricial)
            "COD_TIP_OPE_SUNAT" => $cod_tip_ope_sunat, // tipo de operación de la venta según el tipo de documento (codigo de operación de la operación o transaccion, este codigo debera ser según el catalogo 51, ejemplo 0101 es venta interna)
            "COD_PTO_VENTA" => $data["login"], // usuario de su sistema (si no tiene este dato, no enviar esta linea)
            "ENVIAR_A_SUNAT" => $data["empresa"]["enviar_a_sunat"], // indicador si la factura se va a enviar a SUNAT inmediatamente (true es enviar inmediatamente y false es no enviar y esperar que el sistema mifact lo envie en la hora programada)
            "RETORNA_XML_ENVIO" => "false",
            "RETORNA_XML_CDR" => "true",
            "RETORNA_PDF" => "false",
            "TXT_VERS_UBL" => "2.1",
            "TXT_VERS_ESTRUCT_UBL" => "2.0",
            "COD_ANEXO_EMIS" => "0000",
            "MNT_TOT_ANTCP" => "", // monto total de todos los anticipos
            "COD_TIP_DSCTO" => "02",
            "MNT_IMPUESTO_BOLSAS" => $data['bolsa_plastico'] * 0.10,
            "datos_adicionales" => [
                [
                    "COD_TIP_ADIC_SUNAT" => "01",
                    "TXT_DESC_ADIC_SUNAT" => $data["condicion_pago_id"] == "2" ? diferenciaDias($data["fecha_vencimiento"]): "AL CONTADO",
                ],[
                    "COD_TIP_ADIC_SUNAT" => "05",
                    "TXT_DESC_ADIC_SUNAT" => $data["condicion_pago_id"] == "2" ? "NÚMERO DE CUENTA DE DETRACCIONES DEL 12% BANCO DE LA NACIÓN 00-076-084742" : $data["comentarios"]
                ]
            ],
            "items" => cargar_facturacion_electronica_detalle($data)
        ];
        if ($data["condicion_pago_id"] == "2") {
            $mifact["MNT_PENDIENTE"] = number_format((float)$data["tot"] * 0.88, 2, '.', '');
        }
        return $mifact;
    }

    function diferenciaDias($fechaVencimiento) {
        $hoy = time();
        $fVencimiento = strtotime((string)$fechaVencimiento);
        $dif = $fVencimiento - $hoy;
        return "AL CRÉDITO " . round($dif / (60 * 60 * 24)) . " DIAS";
    }

    function agregarDatosAdicionales() {
        $items = [];
        $item = [
            "COD_TIP_ADIC_SUNAT" => "01",
            "TXT_DESC_ADIC_SUNAT" => "EFECTIVO"
        ];
        array_push($items, $item);
        return $items;
    }

    function cargar_facturacion_electronica_detalle($data) {
        $i = 1;
        $cadena = $data["ser"];
        $total = substr_count($data['ser'], "</tr>");
        $items = [];
        $valorventacod = "";

        while ($i <= $total) {
            $pos = strpos($cadena, "</tr>");
            $tam = strlen($cadena);
            $servicios = substr($cadena, 4, $pos-4);
            $cadena = substr($cadena, $pos+5, $tam-3);
            $demo="";
            $valorventacod="";
            $idservicio="";
            $idserviciopos = strpos($servicios, "</td>");
            $tamservicio = strlen($servicios); // add
            $idservicio = substr($servicios, 4, $idserviciopos-4);
            $cadena1 = substr($servicios, $idserviciopos+5, $tamservicio-3); // add
            $demopos = strpos($cadena1, "</td>"); // add
            $tamdemo = strlen($cadena1); // add
            $demo = substr($cadena1, 4, $demopos-4); // add
            $valorventa = substr($cadena1, $demopos+5, $tamdemo-3); // add
            $valorventacod = substr($valorventa, 4, strlen($valorventacod)-5);

            /* if ($data['tip'] == 2) {
                $valorventacod = number_format((float)$valorventacod * 1.18, 3, '.', '');
            } */

            $val_unit_item = number_format((float)$valorventacod / 1.18, 3, '.', '');
            $val_vta_item = number_format((float)$valorventacod / 1.18, 2, '.', '');
            $cod_tip_afect_igv_item = "10";
            $cod_trib_igv_item = "1000";
            $por_igv_item = "18";
            $mnt_igv_item = number_format((float)$valorventacod * 0.18 / 1.18, 2, '.', '');
            $MNT_BRUTO = number_format((float)$valorventacod / 1.18, 2, '.', '');
            // documento gratuito
            if ($data['gratuito'] == 1) {
                $cod_tip_afect_igv_item = "13";
                $cod_trib_igv_item = "9996";
                $MNT_BRUTO = 0;
            }
            // documentos no domiciliados
            if ($data["tipo_documento_facturacion"] == "0") {
                $cod_trib_igv_item = "9997";
                $cod_tip_afect_igv_item = "20";
                $val_unit_item = number_format((float)$valorventacod, 2, '.', '');
                $val_vta_item = number_format((float)$valorventacod, 2, '.', '');
                $por_igv_item = "0";
                $mnt_igv_item = 0;
                $MNT_BRUTO = 0;
            }

            $item = array (
                // "NUM_LIN_ITEM" => 1,
                "COD_ITEM" => $idservicio, // codigo del producto o servicio (codigo interno)
                "COD_UNID_ITEM" => "NIU", // unidad medida, deacuerdo a la tabla del catalogo 03 (si no encuentran el codigo deseado, contarse para ver si se actualizaron los codigos)
                "CANT_UNID_ITEM" => "1", // cantidad del item
                "VAL_UNIT_ITEM" => $val_unit_item, // valor del item sin igv (Se consignará el importe correspondiente al valor o monto unitario del bien vendido o cedido o servicio prestado. Este importe no incluye los tributos (IGV, ISC y otros Tributos) ni los cargos globales)
                "PRC_VTA_UNIT_ITEM" => number_format((float)$valorventacod, 2, '.', ''), // precio del item incluido igv
                "VAL_VTA_ITEM" => $val_vta_item, // valor total del item sin IGV (Este elemento es el producto de la cantidad por el valor unitario ( Q x Valor Unitario) y la deducción de los descuentos aplicados a dicho ítem (de existir). Este importe no incluye los tributos (IGV, ISC y otros Tributos), los descuentos globales o cargos)
                "MNT_BRUTO" => $data['gratuito'] == 0 ?  : 0, // monto bruto del item que casi siempre es igual al valor total del item sin IGV (este campo podria variar cuando hace un descuento y quiere mostrar en la impresión cual era el valor original y cual es valor con descuento)
                "MNT_PV_ITEM" => number_format((float)$valorventacod, 2, '.', ''), // Venta Total del ITEM incluido IGV, descuentos, cargos adicionales - total a cobrar ()
                "COD_TIP_PRC_VTA" => $data['gratuito'] == 0 ? '01' : '02', // codigo SUNAT tipo de venta del item (01 es para la mayoria de casos y 02 es para venta por transferencia gratuita)
                "COD_TIP_AFECT_IGV_ITEM" => $cod_tip_afect_igv_item, // valor 10 en caso sea afecto a igv (valor 10 en caso sea afecto a igv, caso contrario ver el catalogo 07)
                "COD_TRIB_IGV_ITEM" => $cod_trib_igv_item, // codigo de tributo IGV Catalogo 05 SUNAT (1000: IGV Impuesto General a las Ventas, 2000: ISC Impuesto Selectivo al Consumo)
                "POR_IGV_ITEM" => $por_igv_item, // Tasa Igv del documento actualmente es 18, en caso cambie el impuesto IGV deberan de cambiarlo en su sistema (Tasa Igv del documento actualmente es 18, en caso cambie el impuesto IGV deberan de cambiarlo en su sistema)
                "MNT_IGV_ITEM" => $mnt_igv_item, // igv total del item
                "MNT_OTR_ITEM" => "0.00", //  ()
                "POR_OTR_ITEM" => "0.00", //  ()
                "TXT_DESC_ITEM" => $demo, // descripcion principal del item
                "DET_VAL_ADIC01" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                "DET_VAL_ADIC02" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                "DET_VAL_ADIC03" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                "DET_VAL_ADIC04" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                "DET_VAL_ADIC05" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                "DET_VAL_ADIC06" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                "DET_VAL_ADIC07" => "", // era descripcion adicional del item
                "DET_VAL_ADIC08" => "", // era descripcion adicional del item
                "DET_VAL_ADIC09" => "", // era descripcion adicional del item
                "DET_VAL_ADIC10" => "", // era descripcion adicional del item
                // "MNT_DSCTO_ITEM" => "0.00", //  ()
                // "MNT_RECGO_ITEM" => "0.00" //  ()
            );

            array_push($items, $item);
            $i++;
        }

        // agregar bolsa plastico
        if ($data['bolsa_plastico'] != 0) {
            array_push($items, array (
                "COD_ITEM" => "0", // codigo del producto o servicio (codigo interno)
                "COD_UNID_ITEM" => "NIU", // unidad medida, deacuerdo a la tabla del catalogo 03 (si no encuentran el codigo deseado, contarse para ver si se actualizaron los codigos)
                "CANT_UNID_ITEM" => $data['bolsa_plastico'], // cantidad del item
                "VAL_UNIT_ITEM" => '0.10',
                "PRC_VTA_UNIT_ITEM" => '0.10',
                "VAL_VTA_ITEM" => number_format($data['bolsa_plastico'] * 0.10, 2, '.', ''),
                "MNT_PV_ITEM" => number_format($data['bolsa_plastico'] * 0.10, 2, '.', ''),
                "COD_TIP_PRC_VTA" => '01',
                "COD_TIP_AFECT_IGV_ITEM" =>'20',
                "COD_TRIB_IGV_ITEM" => '9997',
                "POR_IGV_ITEM" => '0.00',
                "MNT_IGV_ITEM" => "0.00", // number_format((float)$data['bolsa_plastico'] * 0.10 * 0.18, 2, '.', '')
                "MNT_OTR_ITEM" => "0.00",
                "POR_OTR_ITEM" => "0.00",
                "TXT_DESC_ITEM" => 'BOLSA DE BIODEGRADABLE',
                "IMPUESTO_BOLSAS_UNIT" => '0.10'
            ));
        }

        return $items;
    }

    function impresion_facturacion_electronica($data)
    {
        global $db;
        $consulta = $db->prepare("SELECT m.id, m.tipo_cpe, m.serie_cpe, m.correlativo_cpe, date(m.createdate) fecha_cpe
            from facturacion_recibo_mifact_response m
            where m.estado = 1 and m.id = (
                select a.id
                from facturacion_recibo_mifact_response a
                where a.estado = 1 and a.tip_recibo = ? and a.id_recibo = ? and a.estado_documento in ('101', '102', '103')
                order by id desc
                limit 1 offset 0)");

        $consulta->execute(array($data["tip"], $data["id"]));
        $info = $consulta->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT me.* from man_empresas me 
                                    where me.id = (select r.id_empresa 
                                    from recibos r
                                    inner join factu_notacredito fnt on fnt.recibo_id = r.id
                                    inner join facturacion_recibo_mifact_response frmr on frmr.id_recibo  = fnt.id
                                    where frmr.id = ? and fnt.recibo_tip = r.tip)");
        $stmt->execute(array($info["id"]));
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
        return enviar_facturacion_electronica(
            json_encode(
                array(
                    'TOKEN' => $empresa["token"],
                    'NUM_NIF_EMIS' => $empresa["num_nif_emis"],
                    'COD_TIP_CPE' => $info["tipo_cpe"],
                    'FEC_EMIS' => $info["fecha_cpe"],
                    "NUM_SERIE_CPE" => $info["serie_cpe"], // Serie del documento alfanumerico (siempre La letra F al inicio para facturas, letra B para boletas, para nota de credito y debito factura la letra F, para nota de credito y debito boleta la letra B al inicio)
                    "NUM_CORRE_CPE" => $info["correlativo_cpe"], // Correlativo del documento (su sistema es el que genera el correlativo, el WS no genera el correlativo)
                    'RETORNA_XML_ENVIO' => false,
                    'RETORNA_XML_CDR' => false,
                    'RETORNA_PDF' => true,
                ), true), "/GetInvoice",$empresa);
    }

    function anular_facturacion_electronica($data)
    {
        global $db;

        $consulta = $db->prepare("SELECT r.id, r.tip, m.serie_cpe serie, m.correlativo_cpe correlativo, r.fec
        from recibos r
        inner join facturacion_recibo_mifact_response m on m.id_recibo = r.id and m.tip_recibo = r.tip and m.estado_documento IN (?, ?)
        where r.id = ? and r.tip = ?");
        $consulta->execute(array(101, 102, $data["id"], $data["tip"]));

        $stmt = $db->prepare("SELECT me.* from man_empresas me where me.id = (select r.id_empresa from recibos r where r.id =? and r.tip =?)");
        $stmt->execute(array($data["id"], $data["tip"]));
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($consulta->rowCount() == 0) {
          return [];
        } else {
          $info = $consulta->fetch(PDO::FETCH_ASSOC);
          $request = json_encode(
            array(
                'TOKEN' => $empresa["token"],
                "COD_TIP_NIF_EMIS" => $empresa["cod_tip_nif_emis"], // tipo de RUC del emisor, siempre sera 6
                'NUM_NIF_EMIS' => $empresa["num_nif_emis"],
                "FEC_EMIS" => date('Y-m-d', strtotime($info["fec"])),
                'COD_TIP_CPE' => $info["tip"] == "2" ? "01" : "03",
                "NUM_SERIE_CPE" => $info["serie"], // Serie del documento alfanumerico (siempre La letra F al inicio para facturas, letra B para boletas, para nota de credito y debito factura la letra F, para nota de credito y debito boleta la letra B al inicio)
                "NUM_CORRE_CPE" => $info["correlativo"], // Correlativo del documento (su sistema es el que genera el correlativo, el WS no genera el correlativo)
                "TXT_DESC_MTVO" => "ANULACION POR ERROR",
                // "COD_PTO_VENTA" => "jmifact", // usuario de su sistema (si no tiene este dato, no enviar esta linea)
            ), true);

            $response = enviar_facturacion_electronica($request
            , "/LowInvoice", $empresa);

            $stmt = $db->prepare('INSERT INTO factu_mifact_response (recibo_tip, recibo_id, request, response, error, idusercreate) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$data["tip"], $data["id"], $request, json_encode($response,true), $response["errors"], $data["login"]]);


          return $response;
        }
    }

    function consultar_facturacion_electronica($data)
    {
        global $db;

        $consulta = $db->prepare("SELECT r.id, r.tip, m.serie_cpe serie, m.correlativo_cpe correlativo, r.fec
        from recibos r
        inner join facturacion_recibo_mifact_response m on m.id = (
            select a.id from facturacion_recibo_mifact_response a where a.estado = 1 and a.id_recibo = r.id and a.tip_recibo = r.tip limit 1 offset 0
        ) and m.estado = 1
        where r.id = ? and r.tip = ?");
        $consulta->execute(array($data["id"], $data["tip"]));
        $info = $consulta->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT me.* from man_empresas me where me.id = (select r.id_empresa from recibos r where r.id =? and r.tip =?)");
        $stmt->execute(array($data["id"], $data["tip"]));
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);


        return enviar_facturacion_electronica(
            json_encode(
                array(
                    'TOKEN' => $empresa["token"],
                    "COD_TIP_NIF_EMIS" => $empresa["cod_tip_nif_emis"], // tipo de RUC del emisor, siempre sera 6
                    'NUM_NIF_EMIS' => $empresa["num_nif_emis"],

                    'COD_TIP_CPE' => $info["tip"] == "2" ? "01" : "03",
                    "NUM_SERIE_CPE" => $info["serie"],
                    "NUM_CORRE_CPE" => $info["correlativo"],
                ), true), "/GetEstatusInvoice",$empresa);
    }

    function enviar_facturacion_electronica($data, $method, $empresa)
    {
        global $db;

        $requestHeaders = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: no-cache',
            'Postman-Token: b4938777-800c-1fb1-b127-aefda436e223',
            sprintf('Content-Length: %d', strlen($data))
        );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'content' => $data,
                'header'  => implode("\r\n", $requestHeaders)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($empresa["service_mifact"].$method, false, $context);
        return json_decode($result, true);
    }

    function cargar_facturacion_electronica_credito($data) {
        return array(
            "TOKEN" => $data["empresa"]["token"], // TOKEN UNICO POR EMPRESA (este token o clave sera rotativo cada cierto tiempo por ejemplo cada 6 meses se cambiara, previa comunicación)
            // datos del emisor
            "COD_TIP_NIF_EMIS" => $data["empresa"]["cod_tip_nif_emis"], // tipo de RUC del emisor, siempre sera 6
            "NUM_NIF_EMIS" => $data["empresa"]["num_nif_emis"], // RUC emisor
            "NOM_RZN_SOC_EMIS" => $data["empresa"]["nom_rzn_soc_emis"], // Razon social emisor (tal cual esta registrado en SUNAT)
            "NOM_COMER_EMIS" => $data["empresa"]["nom_comer_emis"], // nombre comercial emisor (tal cual esta registrado en SUNAT si no tiene nombre comercial no enviar este tag)
            "COD_UBI_EMIS" => $data["empresa"]["cod_ubi_emis"], // Ubigeo de direccion emisor, ver codigo ubigeo de inei
            "TXT_DMCL_FISC_EMIS" => $data["empresa"]["txt_dmcl_fisc_emis"], // Direccion fiscal emisor
            // datos del receptor
            "COD_TIP_NIF_RECP" => $data["documentotipo_codigo"], // Tipo RUC receptor 6 es RUC, 1 es DNI, 0 ES DOC.TRIB.NO.DOM.SIN.RUC (para el caso de boletas si no tiene DNI colocar el codigo 0)
            "NUM_NIF_RECP" => $data["numero"], // Numero documento receptor (para el caso de boletas si no tiene DNI colocar el numero 99999999)
            "NOM_RZN_SOC_RECP" => $data["nombre"], // Nombre o Razon social Receptor (para el caso de boleta si no tiene DNI colocar CLIENTE SIN NOMBRE)
            "TXT_DMCL_FISC_RECEP" => $data["direccion"], // Direccion receptor (para el caso de boleta si no tiene DNI colocar SIN DIRECCION)
            "MailEnvio" => $data["correo"], // email del cliente (si no tiene este dato, no enviar esta linea)
            // datos del documento
            "FEC_EMIS" => date('Y-m-d'), // fecha emision (respetar el formato de la fecha)
            "FEC_VENCIMIENTO" => date('Y-m-d'), // Fecha de vencimiento del documento (respetar el formato de la fecha - opcional)
            "COD_TIP_CPE" => $data["comprobantetipo_codigo"], // tipo documento (01 FACTURA, 03 BOLETA, 07 NOTA DE CRÉDITO, 08 NOTA DE CRÉDITO, 09 GUIA REMISION REMITENTE)
            "NUM_SERIE_CPE" => $data["serie"], // Serie del documento alfanumerico (siempre La letra F al inicio para facturas, letra B para boletas, para nota de credito y debito factura la letra F, para nota de credito y debito boleta la letra B al inicio)
            "NUM_CORRE_CPE" => $data["correlativo"], // Correlativo del documento (su sistema es el que genera el correlativo, el WS no genera el correlativo)
            "COD_MND" => $data["moneda_codigo"], // tipo moneda de venta USD es dolares, PEN es soles (PEN: soles, USD: dólar americano, EUR: euro)
            // configuracion
            "COD_IMPRE_DEST" => "", // impresora destino, solo para ticketera, solo en la version offline (solo para Ws instalados de forma local en su servidor)
            "COD_PRCD_CARGA" => "001", // procedencia de carga para web service y apis es 001 siempre (procedencia de carga para web service y apis es 001 siempre)
            // totales
            "MNT_TOT_GRAVADO" => number_format((float)$data["total"] / 1.18, 2, '.', ''), // monto neto de venta sin IGV - base imponible (la suma de todos los item que estan afecto a IGV - operación gravada o base imponible, no incluir los exonerado, inafectos o gratuitos)
            "MNT_TOT_INAFECTO" => "", // monto total de venta inafecto (la suma de todos los item que estan inafecto del IGV)
            "MNT_TOT_EXONERADO" => "", // monto total de venta exonerada (la suma de todos los item que estan exonerado del IGV)
            "MNT_TOT_GRATUITO" => "", // monto total de venta gratuita (la suma de todos los item que estan exonerado del IGV)
            "MNT_TOT_DESCUENTO" => "", // Monto total descuento sin Igv (suma total del decuento - suma de descuentos de los items)
            "MNT_DSCTO_GLOB" => "", // Monto total Descuento Global sin IGV (suma total del descuento - descuento global, descuento total de la factura no incluir los descuentos de los items)
            "MNT_TOT_OTR_CGO" => "", // monto total de recargo o cargo global (recargo al documento, este recargo no esta afecto a IGV y tampoco forma parte de la base imponible y es parte del total de la venta, se puede utilizar para recargo a restaurantes, propinas etc, consultar con su contador)
            "MNT_TOT_TRIB_IGV" => number_format((float)$data["total"] * 0.18 / 1.18, 2, '.', ''), // monto total IGV (suma de IGV total de cada  producto(item) o servicios que estan afecto a IGV)
            "MNT_TOT_TRIB_ISC" => "",
            "MNT_TOT_TRIB_OTR" => "",
            "MNT_TOT" => number_format((float)$data["total"], 2, '.', ''), // monto total documento a pagar (gravado+inafecto+gratuito+exonerado-DSCTO+Cargo-Anticipo) (suma de todos los item operación gravada, exonerado , inafecto , IGV, descuento global - total a cobrar)
            "TIP_CAMBIO" => "", // tipo de cambio
            // nota de credito, debito
            $data["comprobantetipo_nombre"] => $data["motivotipo_codigo"],
            // "COD_TIP_NC" => "", // Tipo de nota de credito (enviar una de las lineas 15  nota de debito o 16 credito, según sea el caso, si no es Nota de credito o debito no enviar estas lineas)
            // "COD_TIP_ND" => "", // Tipo de nota de debito (enviar una de las lineas 15  nota de debito o 16 credito, según sea el caso, si no es Nota de credito o debito no enviar estas lineas)
            "TXT_DESC_MTVO" => $data["observacion"], // Descripcion del motivo de nota de credito o debito (enviar esta linea siempre en cuando se trate de una nota de credito o debito)
            //
            "COD_FORM_IMPR" => "003", // formato de impresión (004: ticket termico, 001: A4, 003: ticket matricial)
            "COD_TIP_OPE_SUNAT" => "0101", // tipo de operación de la venta según el tipo de documento (codigo de operación de la operación o transaccion, este codigo debera ser según el catalogo 51, ejemplo 0101 es venta interna)
            // "COD_PTO_VENTA" => "jmifact", // usuario de su sistema (si no tiene este dato, no enviar esta linea)
            "ENVIAR_A_SUNAT" => $data["empresa"]["enviar_a_sunat"], // indicador si la factura se va a enviar a SUNAT inmediatamente (true es enviar inmediatamente y false es no enviar y esperar que el sistema mifact lo envie en la hora programada)
            "RETORNA_XML_ENVIO" => "false",
            "RETORNA_XML_CDR" => "true",
            "RETORNA_PDF" => "false",
            "TXT_VERS_UBL" => "2.1",
            "TXT_VERS_ESTRUCT_UBL" => "2.0",
            "MNT_TOT_ANTCP" => "", // monto total de todos los anticipos
            "items" => cargar_facturacion_electronica_detalle_credito($data),
            "docs_referenciado" => cargar_facturacion_electronica_referencia_credito($data)
        );
    }

    function cargar_facturacion_electronica_detalle_credito($data) {
        $cadena = $data["data_detalle"];
        $items = [];

        foreach ($data["data_detalle"] as $key => $value) {
            if ($value['cantidad'] != 0) {
                $item = array (
                    "NUM_LIN_ITEM" => $key,
                    "COD_ITEM" => $value["servicio_id"], // codigo del producto o servicio (codigo interno)
                    "COD_UNID_ITEM" => "NIU", // unidad medida, deacuerdo a la tabla del catalogo 03 (si no encuentran el codigo deseado, contarse para ver si se actualizaron los codigos)
                    "CANT_UNID_ITEM" => $value['cantidad'], // cantidad del item

                    "VAL_UNIT_ITEM" => number_format((float)$value['precio'] / 1.18, 2, '.', ''), // valor del item sin igv (Se consignará el importe correspondiente al valor o monto unitario del bien vendido o cedido o servicio prestado. Este importe no incluye los tributos (IGV, ISC y otros Tributos) ni los cargos globales)
                    "PRC_VTA_UNIT_ITEM" => number_format((float)$value['precio'], 2, '.', ''), // precio del item incluido igv
                    "VAL_VTA_ITEM" => number_format((float)$value['precio'] / 1.18, 2, '.', ''), // valor total del item sin IGV (Este elemento es el producto de la cantidad por el valor unitario ( Q x Valor Unitario) y la deducción de los descuentos aplicados a dicho ítem (de existir). Este importe no incluye los tributos (IGV, ISC y otros Tributos), los descuentos globales o cargos)
                    "MNT_BRUTO" => number_format((float)$value['precio'] / 1.18, 2, '.', ''), // monto bruto del item que casi siempre es igual al valor total del item sin IGV (este campo podria variar cuando hace un descuento y quiere mostrar en la impresión cual era el valor original y cual es valor con descuento)
                    "MNT_PV_ITEM" => number_format((float)$value['precio'], 2, '.', ''), // Venta Total del ITEM incluido IGV, descuentos, cargos adicionales - total a cobrar ()
                    "COD_TIP_PRC_VTA" => "01", // codigo SUNAT tipo de venta del item (01 es para la mayoria de casos y 02 es para venta por transferencia gratuita)
                    "COD_TIP_AFECT_IGV_ITEM" => "10", // valor 10 en caso sea afecto a igv (valor 10 en caso sea afecto a igv, caso contrario ver el catalogo 07)
                    "COD_TRIB_IGV_ITEM" => "1000", // codigo de tributo IGV Catalogo 05 SUNAT (1000: IGV Impuesto General a las Ventas, 2000: ISC Impuesto Selectivo al Consumo)
                    "POR_IGV_ITEM" => "18", // Tasa Igv del documento actualmente es 18, en caso cambie el impuesto IGV deberan de cambiarlo en su sistema (Tasa Igv del documento actualmente es 18, en caso cambie el impuesto IGV deberan de cambiarlo en su sistema)
                    "MNT_IGV_ITEM" => number_format((float)$value['precio'] * 0.18 / 1.18, 2, '.', ''), // igv total del item

                    "MNT_OTR_ITEM" => "0.00", //  ()
                    "POR_OTR_ITEM" => "0.00", //  ()
                    "TXT_DESC_ITEM" =>  $value['nombre'], // descripcion principal del item
                    "DET_VAL_ADIC01" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                    "DET_VAL_ADIC02" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                    "DET_VAL_ADIC03" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                    "DET_VAL_ADIC04" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                    "DET_VAL_ADIC05" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                    "DET_VAL_ADIC06" => "", // era descripcion adicional del item (se realizara un salto de linea en la representacion impresa PDF) (una descripcion adicional que quiere que se muetre en el item, la suma de caracteres entre la descripcion principal y las descripciones adicionales debe ser maximo 250 caracteres, si no tiene o no desea poner una descripcion adicional no enviar este tag)
                    "DET_VAL_ADIC07" => "", // era descripcion adicional del item
                    "DET_VAL_ADIC08" => "", // era descripcion adicional del item
                    "DET_VAL_ADIC09" => "", // era descripcion adicional del item
                    "DET_VAL_ADIC10" => "", // era descripcion adicional del item
                    // "MNT_DSCTO_ITEM" => "0.00", //  ()
                    // "MNT_RECGO_ITEM" => "0.00" //  ()
                );

                array_push($items, $item);
            }
        }

        return $items;
    }

    function cargar_facturacion_electronica_referencia_credito($data)
    {
        $items_mifact = [];
        $items_mifact[] = [
			"COD_TIP_DOC_REF" => $data["tipo_cpe_referencia"],
			"NUM_SERIE_CPE_REF" => $data["serie_cpe_referencia"],
			"NUM_CORRE_CPE_REF" => $data["correlativo_cpe_referencia"],
			"FEC_DOC_REF" => date('Y-m-d', strtotime($data["fecha_referencia"]))
        ];
        return $items_mifact;
    }
?>