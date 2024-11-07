<?php
/* ini_set("display_errors","1");
error_reporting(E_ALL); */
error_reporting(error_reporting() & ~E_NOTICE);

    if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['tip']) && !empty($_GET['tip'])) {
        require("_database/database.php");
        include "_database/db_tools.php";
        // consulta de datos
        $consulta = $db->prepare("SELECT
        r.*, m.serie_cpe serie, m.correlativo_cpe correlativo, m.cadena_para_codigo_qr qr, sp.nombre nomproced
        from recibos r
        left join servicios_procedimiento sp on sp.id = r.procedimiento_id
        left join facturacion_recibo_mifact_response m on m.id = (
            select a.id from facturacion_recibo_mifact_response a where a.estado = 1 and r.id = a.id_recibo and r.tip = a.tip_recibo and a. estado_documento in ('101', '102', '103') limit 1 offset 0
        ) and m.estado = 1
        where r.id=? and r.tip=?");
        $consulta->execute(array($_GET['id'], $_GET['tip']));
        $pop = $consulta->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT me.* from man_empresas me where me.id = ?");
        $stmt->execute(array( $pop["id_empresa"] ));
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt5 = $db->prepare("SELECT direccion from sedes WHERE id=?;");
        $stmt5->execute([$pop["id_empresa_sede"]]);
        $sedeDirec = $stmt5->fetch(PDO::FETCH_ASSOC);

        include('_libraries/phpqrcode/qrlib.php');
        $tempDir = 'storage/facturacion_qr/';
        $fileName = $pop['serie'].'_'.$pop['correlativo'].'.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;

        ob_start();
        QRcode::png($pop["qr"]);
        $qrImage = ob_get_contents();
        ob_end_clean();
        $qrImageBase64 = base64_encode($qrImage);

    } else {
        print("ruta no existe");
    }

    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
    require_once __DIR__ . '/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular"]);

    $html = '
    <div class="ui-content" role="main" style="border: 1px;">';

    if ($pop['anu'] == 1) {
        $html .= '
        <div style="position: absolute; opacity: 0.4; float: left; text-align: center">
            <h1>ANULADO</h1>
            <img src="_images/blocker.svg" width="250" alt="">
            <h1>ANULADO</h1>
        </div>';
    }
    if($empresa['ruc']=='20609906121'){
        $cabeseraE = '
        <div style="font-size:10px; width:250px;">
        <div style="text-align: center;"><img src="_images/logo_fondo.png" width="170" heigth="50" alt="" class="img_logo"><br><br></div>
        <div style="text-align:center;">'.
            $empresa['nom_comer_emis'].'<br>
            RUC: '.$empresa['ruc'].'<br>
            Domicilio Fiscal: AV. JAVIER PRADO OESTE NRO. 856<br>
            URB. SAN FELIPE - LIMA LIMA <br>
            MAGDALENA DEL MAR<br>
            Sucursal: '.$sedeDirec['direccion'].'<br>
            Tlf.(01)4762727<br>
            Cel: (51)922762663<br><br>';
    }else{
        $cabeseraE = '
    <div style="font-size:10px; width:250px;">
        <div style="text-align: center;"><img src="_images/logo_fondo.png" width="170" heigth="50" alt="" class="img_logo"><br><br></div>
        <div style="text-align:center;">'.
            $empresa['nom_comer_emis'].'<br>
            RUC: '.$empresa['ruc'].'<br>
            Domicilio Fiscal: '.$sedeDirec['direccion'].'<br>              
            Tlf.(01)4762727<br>
            Cel: (51)922762663<br><br>';
    }
    
    

    $html .= $cabeseraE;
    
    $html .= '<b>' . ($pop['tip'] == 1 ? 'BOLETA DE VENTA ELECTRÓNICA' : 'FACTURA DE VENTA ELECTRÓNICA') . '<br>' . $pop['serie']. ' - ' . $pop['correlativo'] . '</b>
        </div><br>';
    $html .= '<div style="font-size: 10px;">';
    $html .= 'Cliente: ' . mb_strtoupper($pop['raz']) . '<br>';
    $html .= (!empty($pop['dni']) ? 'N° Documento Cliente: ' . $pop['ruc'] . '<br>' : '');
    if ($pop['dni']!=$pop['ruc'] && $pop['fec'] > '2024-05-26') {
        $html .= 'Paciente: ' . mb_strtoupper($pop['nom']) . '<br>';
        $html .= (!empty($pop['dni']) ? 'N° Documento Paciente: ' . $pop['dni'] . '<br>' : '');
    }
    $html .= (!empty($pop['med']) ? 'Médico: ' . $pop['med'] . '<br>' : '');
    $html .= 'F. Emisión: ' . date("d-m-Y H:i:s", strtotime($pop['fec'])) .'<br>';
    if ($pop["condicion_pago_id"] == "2") {
        $html .= 'F. Vencimiento: ' . date("d-m-Y", strtotime($pop['fecha_vencimiento'])) .'<br>';
    }
    if ($pop['man_fin'] > '2000-01-01' && $pop['man_fin'] > '2000-01-01') { $html .= 'Mantenimiento desde: ' . date("d-m-Y", strtotime($pop['man_ini'])) . '<br>'.'Mantenimiento hasta: ' . date("d-m-Y", strtotime($pop['man_fin'])) . '<br>';}
    if ($pop['tip'] == 2) {
        if ($pop['raz'] <> '') { $html .= ('Razón Social: ' . $pop['raz'] . '<br>'); }
        $html .= ('RUC: ' . $pop['ruc'] . '<br>');
        if (!empty($pop['direccionfiscal'])) { $html .= ('Dirección Fiscal: '.mb_strtoupper($pop['direccionfiscal']).'<br>'); }
    }

    if ($pop['t_ser'] == 1) {$titu = 'REPRODUCCION ASISTIDA';}
    if ($pop['t_ser'] == 2) {$titu = 'ANDROLOGIA';}
    if ($pop['t_ser'] == 3) {$titu = 'PROCEDIMIENTO SALA';}
    if ($pop['t_ser'] == 4) {$titu = 'ANALISIS SANGRE';}
    if ($pop['t_ser'] == 5) {$titu = 'PERFIL: ' . $pop['pak'];}
    if ($pop['t_ser'] == 6) {$titu = 'ECOGRAFIA';}
    if ($pop['t_ser'] == 7) {$titu = 'ADICIONALES';}
    $formaPago = forPago();
    $tarjetas=[];
    foreach ($formaPago as $fila) {
        $tarjetas[$fila['codigo_facturacion']] =$fila['tipotarjeta'];
    }
    $tipoTarjeta = tipTarjeta();
    $tipos=[];
    foreach ($tipoTarjeta as $fila) {
        $tipos[$fila['codigo_facturacion']] =$fila['formapago'];
    }
    $t1 = "";
    if ($pop['t1'] !== 0 && $pop['tipotarjeta1'] !== 0) {
        $t1=$tarjetas[$pop['t1']]."(".ucfirst(strtolower($tipos[$pop['tipotarjeta1']])).")";
    }

    // modificacion de moneda
    if ($pop['m1'] == 1) {$m1 = '$';} else {$m1 = 'S/.';}

    $html .= 'T. Servicio: ' . $titu;
    $nomProced= $pop['nomproced']  ?? '-';
    $tratamiento = '';
    if (!empty($pop['nomproced'] && in_array($pop['t_ser'], [1, 3, 7]))) {
        $tratamiento='<br> T. Tratamiento: ' . $nomProced . '</div>';
    }
    $html .= $tratamiento;

    $html .=  '
    <br>
    <table style="font-size:10px;border-collapse: collapse;">
        <tr><td colspan="3"><br><hr></td></tr>
        <tr>
            <th style="text-align: left;">CÓDIGO</th>
            <th style="text-align: left;">DESCRIPCIÓN</th>
            <th>TOTAL</th>
        </tr>' . $pop['ser'];

    // bolsa de plastico
    if ($pop['bolsa_plastico'] != 0) {
        $html .= '<tr>0<td></td><td>BOLSA DE BIODEGRADABLE ('.$pop['bolsa_plastico'].' und)</td><td align="right">'.number_format($pop['bolsa_plastico']*0.1, 2).'</td></tr>';
    }

    if ($pop['t_ser'] == 1 || $pop['t_ser'] == 2 || $pop['t_ser'] == 3) {
        if ($pop['mon'] == 1) {$mon = "$";}
        else {$mon = "S/.";}
    } else {
        if ($pop['mon'] == 1) {$mon = "S/.";}
        else {$mon = "$";}
    }
    
    $html .= '<tr><td colspan="3"><hr></td></tr>';

    if ($pop['tip'] == 2) {
        $html .= '<tr><td>SUBTOTAL</td><td></td><td>' . $mon . number_format(round($pop['tot'] / 1.18, 2), 2) . '</td></tr>';
        $html .= '<tr><td>I.G.V (18%)</td><td></td><td>' . $mon . number_format(round($pop['tot'] - ($pop['tot'] / 1.18), 2), 2) . '</td></tr>';
    }

    $html .= '
    <tr style="font-weight: bold;">
        <td colspan="2">TOTAL</td>
        <td align="right">' . $mon . ($pop['gratuito'] == 0 ? number_format($pop['tot'], 2) : '0.00') . '</td>
    </tr>
    <tr><td colspan="3"><hr></td></tr>
    <tr style="font-weight: bold;">
        <td colspan="2">Total Gravado</td>
        <td align="right">' . $mon . ($pop['gratuito'] == 0 ? number_format(($pop['tot'] - $pop['descuento']) / 1.18, 2) : '0.00') . '</td>
    </tr>';

    if ($pop['descuento'] != 0) {
        $html .= '<tr style="font-weight: bold;">
            <td colspan="2">Total Desc. ('.number_format($pop['descuento'] / $pop['tot'] * 100, 2).' %)</td>
            <td align="right">' . $mon . number_format($pop['descuento'], 2) . '</td>
        </tr>';
    }

    if ($pop['gratuito'] != 0) {
        $html .= '<tr style="font-weight: bold;">
            <td colspan="2">Total Op. Gratuitas</td>
            <td align="right">' . $mon . number_format($pop['tot'], 2) . '</td>
        </tr>';
    }

    if ($pop['bolsa_plastico'] != 0) {
        $html .= '
        <tr style="font-weight: bold;">
            <td colspan="2">ICBPER</td>
            <td align="right">' . $mon . (number_format($pop['bolsa_plastico']*0.1, 2)) . '</td>
        </tr>';
    }

    $html .= '
    <tr style="font-weight: bold;">
        <td colspan="2"> Total IGV (18%)</td>
        <td align="right">' . $mon . ($pop['gratuito'] == 0 ? number_format(($pop['tot'] - $pop['descuento']) * 0.18 / 1.18, 2) : '0.00') . '</td>
    </tr>
    <tr style="font-weight: bold;">
        <td colspan="2">Importe Total</td>
        <td align="right">' . $mon . ($pop['gratuito'] == 0 ? number_format($pop['tot'] - $pop['descuento'], 2) : '0.00') . '</td>
    </tr>';

    if ($pop["total_cancelar"] != 0) {
        if ($pop["condicion_pago_id"] != "2") {
					$html .= '
						<tr><td colspan="3"><hr></td></tr>
						<tr style="font-weight: bold;">
								<td colspan="2">Total cancelado</td>
								<td align="right">' . $mon . number_format($pop["total_cancelar"], 2) . '</td>
						</tr>';
          $html .= '<tr style="font-weight: bold;">
              <td colspan="2">Vuelto</td>
              <td align="right">' . $mon . ($pop['gratuito'] == 0 ? number_format($pop["total_cancelar"] - $pop['tot'] + $pop['descuento'], 2) : '0.00') . '</td>
          </tr>';
        }
    }

    if ($pop['gratuito'] == 0) {
        $html .= '<tr style="font-weight: bold;">
            <td colspan="3"><br>CONDICIÓN DE PAGO: '.($pop["condicion_pago_id"] == "2" ? diferenciaDias($pop["fecha_vencimiento"]) : "AL CONTADO").'</td>
        </tr>';
        if ($pop["condicion_pago_id"] != "2") {
            $html .= '<tr>
                <td colspan="2"><br><b>MEDIO DE PAGO</b><br> '.$t1."</td> <td style='position:relative;top:5px'><br>". $m1 . number_format($pop['p1'], 2).'</td>
            </tr>';
        }
        
    } else {
        $html .= '><tr><td colspan="3" align="center"><br>SERVICIO PRESTADO GRATUITAMENTE.</td></tr>';
    }

    function diferenciaDias($fechaVencimiento) {
        $hoy = time();
        $fVencimiento = strtotime((string)$fechaVencimiento);
        $dif = $fVencimiento - $hoy;
        return "AL CRÉDITO " . round($dif / (60 * 60 * 24)) . " DIAS";
    }
    

    if ($pop['t2'] !== 0 && $pop['tipotarjeta2'] !== 0) {
        $formaPago = forPago();
            $tarjetas2=[];
            foreach ($formaPago as $fila) {
                $tarjetas2[$fila['codigo_facturacion']] =$fila['tipotarjeta'];
            }
            $tipoTarjeta = tipTarjeta();
            $tipos2=[];
            foreach ($tipoTarjeta as $fila) {
                $tipos2[$fila['codigo_facturacion']] =$fila['formapago'];
            }
            $t2=$tarjetas2[$pop['t2']]."(".ucfirst(strtolower($tipos2[$pop['tipotarjeta2']])).")";       
        if ($pop['m2'] == 1){ $m2 = '$';} else {$m2 = 'S/.';}
        $html .= '<tr><td>' . $t2 . '</td><td></td><td>' . $m2 . number_format($pop['p2'], 2) . '</td></tr>';
    }

    if ($pop['t3'] !== 0 && $pop['tipotarjeta3'] !== 0) {
        $formaPago = forPago();
        $tarjetas3=[];
        foreach ($formaPago as $fila) {
            $tarjetas3[$fila['codigo_facturacion']] =$fila['tipotarjeta'];
        }
        $tipos3=[];
        foreach ($tipoTarjeta as $fila) {
            $tipos3[$fila['codigo_facturacion']] =$fila['formapago'];
        }
        $t3=$tarjetas3[$pop['t3']]."(".ucfirst(strtolower($tipos3[$pop['tipotarjeta3']])).")";  
               
        if ($pop['m3'] == 1) {$m3 = '$';} else {$m3 = 'S/.';}
        $html .= '<tr><td>' . $t3 . '</td><td></td><td>' . $m3 . number_format($pop['p3'], 2) . '</td></tr>';
    }

    $html .= '</table><br>';

    if ($pop['mon'] > 1) { $html .= "Tipo de Cambio: " . $pop['mon']; }

    if (!empty($pop['comentarios'])) {
        $html .= ('
        <table style="font-size:10px; border-collapse: collapse;">
            <tr><td>Ref.: '.$pop['comentarios'].'</td></tr>
        </table>');
    }

    if ($pop["condicion_pago_id"] == "2" && $pop['tot'] - $pop['descuento'] > 700) {
        $html .= ('
        <table style="font-size:10px; border-collapse: collapse;">
            <tr><td><hr></td></tr>
            <tr><td>SUJETO A DETRACCIÓN NÚMERO DE CUENTA DE DETRACCIONES DEL 12% BANCO DE LA NACIÓN '.$empresa['cuentadetraccion'].'</td></tr>
        </table>');
    }

    $html .= '
    <div style="text-align: center;padding:0;"><img src="data:image/png;base64,'.$qrImageBase64.'" alt="QR Code" style="width:200px"></div>
    <p style="font-size:10px; text-align: center;">
        Representación impresa de la' . ($pop['tip'] == 1 ? "Boleta" : "Factura") . 'Electrónica.<br>
        Autorización № 0023845136723 Serie: FFCF297034 - SUNAT<br>
        Cualquier consulta puede comunicarse al 4762727
    </p></div></div>';

    $html.='<style>@page {
     margin: 20px;
    }</style>';

	$mpdf->WriteHTML('<body>' . $html . '</body>');
    $mpdf->Output();
    // print($html);
	exit();
?>