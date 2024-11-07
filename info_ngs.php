<?php
    require("_database/db_info_ngs.php");

    //
    $html = "<br><hr><br>";
    $html .= '
    <table border="0" width="100%">
        <tr>
            <td width="25%">Protocolo</td>
            <td width="50%" colspan="2" class="border_bottom" align="center">'.$datadesarrollo['repro'].'</td>
            <td width="25%"></td>
        </tr>
        <tr>
            <td width="25%">Extras</td>
            <td width="50%" colspan="2" class="border_bottom" align="center">'.mb_strtoupper($datarepro['extras']).'</td>
            <td width="25%"></td>
        </tr>
        <tr><td colspan="4">Información de la paciente</td></tr>
        <tr>
            <td align="left" width="25%">Nombres y Apellidos</td>
            <td align="left" width="50%" colspan="2" class="border_bottom">'.mb_strtoupper($datapaciente['nombres']).' '.mb_strtoupper($datapaciente['apellidos']).'</td>
            <td align="left" width="25%" class="border_bottom">Cel.: '.$datapaciente['celular'].'</td>
        </tr>
        <tr>
            <td align="left" width="25%">Fecha de Nacimiento</td>
            <td align="left" width="50%" colspan="2" class="border_bottom">'.$datapaciente['fechanacimiento'].' ('.$datapaciente['edad'].' años)</td>
            <td align="left" width="25%" class="border_bottom">DNI: '.$datapaciente['numerodocumentoidentidad'].'</td>
        </tr>
        <tr><td colspan="4">Informacion de la pareja:</td></tr>
        <tr>
            <td align="left" width="20%">Nombres y Apellidos</td>
            <td align="left" width="50%" colspan="2" class="border_bottom">'.mb_strtoupper($datapareja['nombres']).' '.mb_strtoupper($datapareja['apellidos']).'</td>
            <td align="left" width="25%" class="border_bottom">Cel.: '.$datapareja['celular'].'</td>
        </tr>
        <tr>
            <td align="left" width="25%">Fecha de Nacimiento</td>
            <td align="left" width="50%" colspan="2" class="border_bottom">'.$datapareja['fechanacimiento'].' ('.$datapareja['edad'].' años)</td>
            <td align="left" width="25%" class="border_bottom">DNI: '.$datapareja['numerodocumentoidentidad'].'</td>
        </tr>
        <tr><td colspan="4"><br><hr></td></tr>
        <tr><td colspan="4">Información de la clínica de FIV:</td></tr>
        <tr>
            <td align="left" width="25%">Nombre de la clínica</td>
            <td colspan="3" width="75%" class="border_bottom">CENTRO MÉDICO INMATER</td>
        </tr>
        <tr>
            <td align="left" width="25%">Médico tratante</td>
            <td colspan="3" width="75%" class="border_bottom">'.mb_strtoupper($datamedico['nombrescompletos']).'</td>
        </tr>
        <tr>
            <td align="left">Jefe de Laboratorio</td>
            <td colspan="3" class="border_bottom">FERNANDO PEÑA</td>
        </tr>
        <tr><td colspan="4"><hr></td></tr>
    </table>
    <table border="0" width="100%">
        <tr><td colspan="4"><br>Información de la Biopsia:</td></tr>
        <tr>
            <td align="left" width="35%">Responsable de la biopsia (Día 5)</td>
            <td width="25%" align="left" class="border_bottom">'.mb_strtoupper($dataembriologodia5['nombrescompletos']).'</td>
            <td align="left" width="15%">Testigo (Día 5)</td>
            <td width="25%" align="left" class="border_bottom">'.mb_strtoupper(testigoBiopsiaDesarrolloListar($repro, '5')['nombre']).'</td>
        </tr>
        <tr>
            <td align="left" width="35%">Responsable de la biopsia (Día 6)</td>
            <td width="25%" align="left" class="border_bottom">'.mb_strtoupper($dataembriologodia6['nombrescompletos']).'</td>
            <td align="left" width="15%">Testigo (Día 6)</td>
            <td width="25%" align="left" class="border_bottom">'.mb_strtoupper(testigoBiopsiaDesarrolloListar($repro, '6')['nombre']).'</td>
        </tr>
        <tr>
            <td align="left" width="35%">Método de Fecundación</td>
            <td width="25%" class="border_bottom">'.metodoFecundacion($datarepro).'</td>
            <td align="left" width="15%">Fecha Biopsia</td>
            <td width="25%" class="border_bottom">'.$datadesarrollo['fec5'].'</td>
        </tr>
    </table>
    <table border="0" width="100%">
        <tr><td colspan="4"><hr></td></tr>
        <tr><td colspan="5">Indicaciones</td></tr>
        <tr>
            <td colspan="2" width="40%">Prueba a realizar</td>
            <td align="center" width="30%" class="border_bottom">'.mb_strtoupper(pruebaBiopsiaDesarrolloListar($repro)['nombre']).'</td>
            <td width="30%"></td>
        </tr>
        <tr>
            <td colspan="2" width="40%">Donación de Óvulos</td>
            <td align="center" width="30%" class="border_bottom">'.donacionOvulos($datadesarrollo['tipo'], $datapaciente['tipo']).'</td>
            <td width="30%"></td>
        </tr>
        <tr>
            <td colspan="2" width="40%">Donación de Espermatozoides</td>
            <td align="center" width="30%" class="border_bottom">'.donacionEspermatozoides($datadesarrollo['repro']).'</td>
            <td width="30%"></td>
        </tr>
        <tr><td colspan="4"><hr></td></tr>
    </table>
    <table border="1" width="100%">
        <tr>
            <td align="center" width="25%">Indicación del Embrion<br>(Iniciales + Número):</td>
            <td align="center" width="10%">Clasificación morfológica</td>
            <td align="center" width="10%">Día de biopsia</td>
            <td align="center" width="55%">Observaciones</td>
        </tr>
        <tr>'.detalleBiopsia($datadesarrollo['repro'], inicialesNombres($datapaciente['nombres'])).'</tr>
    </table>
    <table border="0" width="100%">
        <tr><td colspan="4"><br></td></tr>
        <tr>
            <td width="25%">OBSERVACIONES:</td>
            <td width="50%" colspan="2" align="left">'.mb_strtoupper(pruebaBiopsiaDesarrolloListar($repro)['observacion']).'</td>
            <td width="25%"></td>
        </tr>
    </table>';

    $cabecera = '
    <table width="100%">
        <tr>
            <td align="center" width="25%"><img src="_images/genomics_peru_logo.jpg" width="200"></td>
            <td align="center" width="75%" style="font-size:14px">
                '.mb_strtoupper(pruebaBiopsiaDesarrolloListar($repro)['correlativo']).'<br><br>
                <b>FORMULARIO DE BIOPSIA PARA SCREENING DE ANEUPLOIDIAS (PGS) POR MEDIO DE NGS Y MITOTEST</b>
            </td>
        </tr>
    </table>';

    $estilo = '
    <style>
        @page {
            margin-header: 0mm;
            margin-footer: 0mm;
            margin-left: 0cm;
            margin-right: 0cm;
            header: html_myHTMLHeader;
            footer: html_myHTMLFooter;
        }
        .xxx {margin-left: 1cm; margin-right: 1cm;}
        .tabla table {border-collapse: collapse;}
        .tabla table, .tabla th, .tabla td {border: 1px solid #72a2aa;}
        .border_bottom {border-bottom: 1pt solid #72a2aa;}
    </style>';
    $head_foot = '
    <!--mpdf
    <htmlpageheader name="myHTMLHeader"><img src="_images/info_head.jpg" width="100%"></htmlpageheader>
    <htmlpagefooter name="myHTMLFooter"><img src="_images/info_foot.jpg" width="100%"></htmlpagefooter>
    mpdf-->';

    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
    require_once __DIR__ . '/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular"]);
    $mpdf->WriteHTML($estilo.'<body><div class="xxx">'.$cabecera.$head_foot.$html.'</div></body>');
    $mpdf->Output();
    // print($cabecera.$head_foot.$html);
    exit;
?>