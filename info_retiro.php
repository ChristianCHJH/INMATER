<?php
		session_start();
    require $_SERVER["DOCUMENT_ROOT"] . "/config/environment.php";
    require $_SERVER["DOCUMENT_ROOT"] . "/_database/database.php";
		require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_log.php");
		global $dblog;
		$login = $_SESSION['login'];
		$nombre_modulo="informe_laboratorio";
		$ruta="perfil_medico/busqueda_paciente/paciente/informe_laboratorio.php";
		$tipo_operacion="consulta";
		$createdate=date("Y-m-d H:i:s");
		$sql = "INSERT INTO log_inmater
							(nombre_modulo, ruta, tipo_operacion, idusercreate,createdate)
							VALUES
							(?, ?, ?, ?,?)";
		$statement = $dblog->prepare($sql);
		$statement->execute(array($nombre_modulo,$ruta,$tipo_operacion,$login, $createdate));
    $pro = $_GET['a'];
    $dni = $_GET['b'];
    $p_dni = $_GET['c'];

    $rPaci = $db->prepare("SELECT nom, ape FROM hc_paciente WHERE dni=?");
    $rPaci->execute(array($dni));
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

    // datos de procedimiento
    $stmt = $db->prepare("SELECT
        la.fec
        from hc_reprod hr
        inner join lab_aspira la on la.rep = hr.id and la.estado is true
        where la.pro = ?");
    $stmt->execute([$pro]);
    $data_procedimiento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($p_dni <> "") {
        $rPare = $db->prepare("SELECT p_nom,p_ape,p_fnac FROM hc_pareja WHERE p_dni=?");
        $rPare->execute([$p_dni]);
        $pare = $rPare->fetch(PDO::FETCH_ASSOC);

        if ($pare['p_fnac'] == '1899-12-30') {
            $p_edad = ' (Edad: -)';
        } else {
            $p_edad = ' (Edad: '.date_diff(date_create($pare['p_fnac']), date_create($data_procedimiento['fec']))->y.')';
        }
        $pareja = $pare['p_ape'].' '.$pare['p_nom'].$p_edad;
    } else {
        $pareja = 'Soltera';
    }

    $Rpop = $db->prepare("SELECT
        lab_aspira.*,
        hc_reprod.id, hc_reprod.eda, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_antece_trata.embriologo as embriologo, hc_reprod.p_cri, hc_reprod.pago_extras, hc_reprod.f_mas, hc_reprod.f_fem, hc_reprod.p_dni, hc_reprod.p_dni_het, hc_reprod.med, hc_reprod.des_dia, hc_reprod.des_don, hc_reprod.f_iny, hc_reprod.p_iiu
        , case
            when emb6 != 0 then emb6
            when emb5 != 0 then emb5
            when emb4 != 0 then emb4
            when emb3 != 0 then emb3
            when emb2 != 0 then emb2
            when emb1 != 0 then emb1
            when emb0 != 0 then emb0
            else 0 end emb_firma
        , case
            when emb6c != 0 then emb6c
            when emb5c != 0 then emb5c
            when emb4c != 0 then emb4c
            when emb3c != 0 then emb3c
            when emb2c != 0 then emb2c
            when emb1c != 0 then emb1c
            when emb0c != 0 then emb0c
            else 0 end emb_firma_c
        FROM lab_aspira
        LEFT JOIN hc_reprod ON hc_reprod.id=lab_aspira.rep
        LEFT JOIN hc_antece_trata ON hc_reprod.id = hc_antece_trata.id_reprod
        WHERE lab_aspira.pro=? and lab_aspira.estado is true");
    $Rpop->execute(array($pro));
    $pop = $Rpop->fetch(PDO::FETCH_ASSOC);

    $rMed = $db->prepare("SELECT nom,cmp FROM usuario WHERE userx=?");
    $rMed->execute(array($pop['med']));
    $med = $rMed->fetch(PDO::FETCH_ASSOC);

    $rEmbrio = $db->prepare("SELECT id,nom,cbp FROM lab_user WHERE id=?");
    $rEmbrio->execute(array($pop['embriologo']));
    $embrio = $rEmbrio->fetch(PDO::FETCH_ASSOC);


    $rRes = $db->prepare("SELECT lad.*, teo.id as idestadoebrio,teo.nombre
    FROM lab_aspira_dias lad
    inner join tblestado_embrio_ovo teo on teo.id = lad.id_estado
    WHERE lad.pro in (SELECT pro_c FROM lab_aspira_dias where pro = ? and estado is true) 
	AND lad.des<>1 and lad.estado is true AND (lad.adju is null OR lad.adju='' OR lad.adju= ?) 
	AND (lad.d6f_cic='C' OR lad.d5f_cic='C' OR lad.d4f_cic='C' OR lad.d3f_cic='C' 
		 OR lad.d2f_cic='C')
	AND lad.id_estado = 3
    ORDER BY lad.ovo;");

    $rRes->execute(array($pro, $_dni));


    $html = '<h1>LABORATORIO DE REPRODUCCIÓN ASISTIDA</h1><h4>DATOS DEL PROCEDIMIENTO</h4>';
    $html .= '<blockquote><table border="0" align="left">
    <tr>
    <th width="200" align="left">Paciente</th><td>'.$paci['ape'].' '.$paci['nom'].'</td>
    </tr>
    <tr>
    <th align="left">Médico</th><td>'.$med['nom'].' (CMP '.$med['cmp'].')</td>
    </tr>
    <tr>
    <th align="left">Embriologo</th><td>'.$embrio['nom'].' (CBP '.$embrio['cbp'].')</td>
    </tr>
    <tr>';

    // traslado
    $html .= '<th align="left"></th><td></td>';
    

    $html.='</tr>
    <tr>
    <th align="left">Tipo de procedimiento realizado</th><td>RETIRO</td>
    </tr>
    <tr>';


    $html .= '<th align="left"></th><td></td>';

    

    $html.='</tr>
    </table></blockquote>';

    $html .= '<h4>EVALUACIÓN DEL DESARROLLO</h4>';

    
    $html .= '<blockquote class="tabla">
        <table class="tabla">
            <thead>
                <tr>
                    <th></th>
                    <th colspan="5">Día 5</th>
                    <th colspan="5">Día 6</th>
                    <th colspan="4"></th>
                </tr>
                <tr>
                    <th>ID Embriones</th>
                    <th>Células</th>
                    <th>MCI</th>
                    <th>TROF.</th>
                    <th>KID/IDA Score</th>
                    <th>CONTRACCIÓN</th>
                    <th>Células</th>
                    <th>MCI</th>
                    <th>TROF.</th>
                    <th>KID/IDA Score</th>
                    <th>CONTRACCIÓN</th>
                    <th>NGS</th>
                    <th>Mitoscore</th>
                    <th>Prioridad<br>transferencia</th>
                    <th>PAJUELA</th>
                </tr>
            </thead>
            <tbody>';
        while ($res = $rRes->fetch(PDO::FETCH_ASSOC)) {

            if ($res['d5f_cic'] <> '') {
                if ($res['d5col']==1) {
                    $res['d5col']="Si";
                } else {
                    $res['d5col']="No";
                }
                if ($res['d6col']==1) {
                    $res['d6col']="Si";
                } else {
                    $res['d6col']="No";
                }
                $kidscore5 = 0;
                if($res['d5kid_tipo'] != 0){
                    $kidscore5 = $res['d5kid_decimal'];
                }elseif($res['d5kid_tipo'] == 0){
                    $kidscore5 = $res['d5kid'];}
                if ($res['d5f_cic'] == 'C') $des_dia = 5;

            } else {
                    $res['d5cel'] = '-';
                    $res['d5mci'] = '-';
                    $res['d5tro'] = '-';
                    $kidscore5 = '-';
                    $res['d5col'] = '-';
            }

            if ($res['d6f_cic'] <> '') {
                $kidscore6 = 0;
                if($res['d6kid_tipo'] != 0){
                    $kidscore6 = $res['d6kid_decimal'];
                }elseif($res['d6kid_tipo'] == 0){
                    $kidscore6 = $res['d6kid'];
                }
                if ($res['d6f_cic'] == 'C') $des_dia = 6;
            } else {
                $res['d6cel'] = '-';
                $res['d6mci'] = '-';
                $res['d6tro'] = '-';
                $kidscore6 = '-';
                $res['d6col'] = '-';
            }

            $kidscore5 = 0;
            if($res['d5kid_tipo'] != 0){$kidscore5 = $res['d5kid_decimal'];}elseif($res['d5kid_tipo'] == 0){$kidscore5 = $res['d5kid'];}

            //ngs
            $ngs = '-';
            $ngs3 = '';
            if ($res['ngs1'] == 1) $ngs = 'Normal';
            if ($res['ngs1'] == 2) $ngs = '<font color="red">Anormal</font>';
            if ($res['ngs1'] == 3) $ngs = 'NR';
            if ($res['ngs1'] == 4) $ngs = '<font color="red">Mosaico</font>';
            if ($res['ngs3'] == 1) $ngs3 = ' (H)';
            if ($res['ngs3'] == 2) $ngs3 = ' (M)';
            if ($res['ngs3'] == 3) $ngs3 = ' -';

            $res['valores_mitoscore'] = !!$res['valores_mitoscore'] ? $res['valores_mitoscore'] : '-';
            $res['prioridad_transferencia'] = !!$res['prioridad_transferencia'] ? $res['prioridad_transferencia'] : '-';


            $ubica = $res['t'] . '-' . $res['c'] . '-' . $res['g'] . '-' . $res['p'];

            $html.='
                    <tr>
                    <td>' . $res['ovo'] . '</td>
                    <td>' . $res['d5cel'] . '</td>
                    <td>' . $res['d5mci'] . '</td>
                    <td>' . $res['d5tro'] . '</td>
                    <td>' . $kidscore5 . '</td>
                    <td>' . $res['d5col'] . '</td>
                    <td>'.$res['d6cel'].'</td>
                    <td>'.$res['d6mci'].'</td>
                    <td>' . $res['d6tro'] . '</td>
                    <td>' . $kidscore6 . '</td>
                    <td>' . $res['d6col'] . '</td>
                    <td>' . $ngs . $ngs3 . '</td>
                    <td>'.$res['valores_mitoscore'].'</td>
                    <td>'.$res['prioridad_transferencia'].'</td>
                    <td>' . $ubica . '</td>
                    </tr>';
        }
        
    $html.='
            </tbody>
        </table>
    </blockquote>';
 

    $estilo = '<style>@page {
        margin-header: 0mm;
        margin-footer: 0mm;
        margin-left: 0cm;
        margin-right: 0cm;
        margin-top: 3cm;
        header: html_myHTMLHeader;
        footer: html_myHTMLFooter;
        margin-bottom: 4cm;
    } .xxx {margin-left: 2.3cm;margin-right: 1.7cm;} .tabla table {border-collapse: collapse;} .tabla table, .tabla th, .tabla td {border: 1px solid #72a2aa;} .resaltar{background-color: #ffefcf;}
     
    .tabla {
            width: 100%;
            border-collapse: collapse;
            font-size: 56px; /* Tamaño de letra más grande */
            text-align: center;
            margin-left: 0; /* Asegura que la tabla esté alineada a la izquierda */
            font-family: Arial, sans-serif; /* Fuente legible */
        }
        .tabla th, .tabla td {
            border: 2px solid #72a2aa;
            padding: 10px; /* Más espaciado para mejor legibilidad */
        }
        .tabla th[colspan] {
            border: 1px solid #72a2aa;
        }
        .tabla th {
            background-color: #f2f2f2;
            font-size: 56px; /* Tamaño de letra más grande para encabezados */
        }
        .tabla a {
            color: red;
        }
        .ui-state-disabled input {
            pointer-events: none;
        }
    </style>';

    $stmt = $db->prepare("SELECT id, nom, cbp, nombre, apellido from lab_user where id=?;");
    if ($pop["p_cri"] == 1) {
        $stmt->execute([$pop['emb_firma_c']]);
    } else {
        $stmt->execute([$pop['emb_firma']]);
    }
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $cbp= '<br><i>CBP: ' . $data['cbp'] . '</i>';
	if($data['cbp']=='0'){ $cbp= '';}
    $html .= '</p><div width="200" style="float:right;"><img src="emb_pic/emb_' . $data['id'] . '.jpg" width="200px" height="100px"><br><br><i>Blgo. ' . $data['nombre'].' '. $data['apellido'] . '</i>'.$cbp.'</div>';

    $head_foot = '<!--mpdf
    <htmlpageheader name="myHTMLHeader"><img src="_images/info_head.jpg" width="100%"></htmlpageheader>
    <htmlpagefooter name="myHTMLFooter"><img src="_images/info_foot.jpg" width="100%"></htmlpagefooter>
    mpdf-->';

    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
    require_once __DIR__ . '/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular_notfont"]);

    $mpdf->WriteHTML($estilo.'<body><div class="xxx">'.$head_foot.$html.'</div></body>');
    $mpdf->Output();
    //echo $head_foot.$html;
    exit;
?>