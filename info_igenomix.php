<?php
  require($_SERVER["DOCUMENT_ROOT"]."/_database/database.php");
  require($_SERVER["DOCUMENT_ROOT"]."/_database/igeno_informe.php");

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
    .contenido {margin-left: 0.5em; margin-right: 0.5em;}
    .table_principal {
      width: 100%;
      margin: 20px 0;
      border-top: solid 1px;
    }
    .border {border: solid 1px;}
    .marca {width: 5%; text-align: center; border: 1px solid;}
    .descripcion {width: 20%}
    .ancho25 {width: 25%}
  </style>';
  $cabecera = '';
  $head_foot = '';

  $protocolo = $_GET["pro"];
  $paciente = traer_paciente($protocolo);
  $pareja = traer_pareja($paciente["dni"]);
  $paciente_iniciales = mb_strtoupper(substr($paciente["nom"], 0, 1).substr($paciente["ape"], 0, 1));
  $informe = traer_informe($protocolo);
  $biologo_biopsia = traer_biologo($informe["biologo_biopsia_id"]);
  $biologo_tubing = traer_biologo($informe["biologo_tubing_id"]);
  $biologo_d6 = '';

  if (verificar_dia6($protocolo)) {
    $biologo_biopsia_d6 = traer_biologo($informe["biologo_biopsia_d6_id"]);
    $biologo_tubing_d6 = traer_biologo($informe["biologo_tubing_d6_id"]);
    $biologo_d6 = '<tr><td>Biopsia realizada por: <u>'.mb_strtoupper($biologo_biopsia_d6["nombres"]).'</u></td><td>Fecha biopsia: <u>'.mb_strtoupper($informe["fecha_biopsia_d6"]).'</u></td></tr>
    <tr><td>Tubing realizado por: <u>'.mb_strtoupper($biologo_tubing_d6["nombres"]).'</u></td><td>Lote medio washing/ loading: <u>'.mb_strtoupper($informe["lote_medio_d6"]).'</td></tr>';
  }

  $html = '<img width="166" src="_images/igenomix.png" alt=""><br>
  <label><b>FORMULARIO PARA SOLICITUD DE PRUEBAS PGT-A, PGT-SR, PGT-M & MITOSCORE</b></label><br><br>
  <labe>Correlativo: '.$informe["correlativo"].'</label>
  <table class="table_principal" border="0">
    <colgroup>
      <col/>
    </colgroup>
    <tbody>
      <tr><td><b>ANÁLISIS SOLICITADO</b></td></tr>
      <tr><td>'.traer_analisispdf($informe["analisis"]).'</td></tr>
      <tr><td>'.traer_mitoscorepdf($informe["sino_mitoscore_id"]).'</td></tr>
      <tr><td><small>(*)Únicamente disponible en caso de elegir PGT-A y/ o PGT-SR. En caso de no indicar ninguna opción se informará del valor de MitoScore</small></td></tr>
    </tbody>
  </table>
  <table class="table_principal" border="0">
    <tbody>
      <tr><td><b>TARIFA SOLICITADA <small>(sólo para PGT-A y PGT-SR transiocaciones Robertsonianas)</small></b></td></tr>
      <tr><td>'.traer_tarifapdf($informe["tarifa"]).'</td></tr>
    </tbody>
  </table>
  <table class="table_principal" border="0">
    <colgroup>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
    </colgroup>
    <tbody>
      <tr><td colspan="6"><b>DATOS PETICIONARIO DEL ESTUDIO</b></td></tr>
      <tr><td colspan="6">Clínica: <u>'.mb_strtoupper($informe["peticionario_clinica"]).'</u></td></tr>
      <tr><td colspan="6">Médico remitente: <u>'.mb_strtoupper($informe["peticionario_medicoremitente"]).'</u></td></tr>
      <tr><td colspan="3">IVF Lab Manager: <u>'.mb_strtoupper($informe["peticionario_labmanager"]).'</u></td><td colspan="3">Persona de contacto: <u>'.mb_strtoupper($informe["peticionario_personacontacto"]).'</u></td></tr>
      <tr><td colspan="3">E-mail o teléfono de contacto: <u>'.mb_strtolower($informe["peticionario_mailcontacto"]).'</u></td><td colspan="3">E-mail para entrega de resultados: <u>'.mb_strtolower($informe["peticionario_mailresultados"]).'</u></td></tr>
      <tr><td colspan="6">Dirección: <u>'.mb_strtoupper($informe["peticionario_labmanager"]).'</u></td></tr>
      <tr><td colspan="2">Ciudad: <u>'.mb_strtoupper($informe["peticionario_ciudad"]).'</u></td><td colspan="2">Provincia: <u>'.mb_strtoupper($informe["peticionario_provincia"]).'</u></td><td colspan="2">C.P.: <u>'.mb_strtoupper($informe["peticionario_cp"]).'</u></td></tr>
    </tbody>
  </table>
  <table class="table_principal" border="0">
    <colgroup>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
    </colgroup>
    <tbody>
      <tr><td colspan="6"><b>DATOS DEL PACIENTE</b></td></tr>
      <tr><td colspan="6">N° de Historia (NHC): <u>'.mb_strtoupper($paciente["dni"]).'</u></td></tr>
      <tr><td colspan="2">Nombre paciente: <u>'.mb_strtoupper($paciente["nom"]).'</u></td><td colspan="2">Apellidos: <u>'.mb_strtoupper($paciente["ape"]).'</u></td><td colspan="2">Fecha de nacimiento: <u>'.$paciente["fnac"].'</u></td></tr>
      <tr><td colspan="2">E-mail paciente: <u>'.mb_strtolower($paciente["mai"]).'</u></td><td colspan="2">N° celular o teléfono: <u>'.mb_strtoupper($paciente["tcel"]).'</u></td></tr>
      <tr><td colspan="2">Nombre pareja: <u>'.mb_strtoupper($pareja["p_nom"]).'</u></td><td colspan="2">Apellidos: <u>'.mb_strtoupper($pareja["p_ape"]).'</u></td><td colspan="2">Fecha de nacimiento: <u>'.$pareja["p_fnac"].'</u></td></tr>
      <tr><td colspan="2">E-mail pareja: <u>'.mb_strtolower($pareja["p_mai"]).'</u></td><td colspan="2">N° celular o teléfono: <u>'.mb_strtoupper($pareja["p_tcel"]).'</u></td></tr>
      <tr><td colspan="3">Cariotipo/s Paciente(*): <u>'.$informe["cariotipo_paciente"].'</u></td><td colspan="3">Cariotipo/s Pareja: <u>'.$informe["cariotipo_pareja"].'</u></td></tr>
      <tr><td colspan="6">Idioma del informe</td></tr>
      <tr><td colspan="6"><small>(*)Únicamente será obligatoria el cariotipo del portador en el caso de seleccionar PGT-SR</small></td></tr>
    </tbody>
  </table>
  <table class="table_principal" border="0">
    <colgroup>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
    </colgroup>
    <tbody>
      <tr><td colspan="3"><b>INFORMACIÓN DEL CICLO</b></td></tr>
      <tr><td colspan="3">'.traer_origenovocitopdf($informe["origenovocito"]).'</td></tr>
      <tr><td>Fecha extracción óvulos: <u>'.$informe["fecha_extraccionovulos"].'</u></td><td>Óvulos fecundados: <u>'.$informe["ovulos_fecundados"].'</u></td><td>Embriones biopsiados: <u>'.$informe["embriones_biopsiados"].'</u></td></tr>
      <tr><td colspan="3">'.traer_metodofecundacionpdf($informe["igeno_metodofecundacion_id"]).'</td></tr>
      <tr><td colspan="3">Fecha/hora prevista para la transferencia embrionaria(*): <u>'.$informe["fecha_transferencia"].' '.$informe["hora_transferencia"].'</u></td></tr>
      <tr><td colspan="3"><small>(*)Sólo obligatorio en caso de transferencias en el mismo ciclo</small></td></tr>
      <tr><td colspan="3">Transferencia embrionaria</td></tr>
      <tr><td colspan="3">'.traer_tipotransferenciapdf($informe["tipotransferencia"]).'</td></tr>
      <tr><td colspan="3">Tipo de biopsia</td></tr>
      <tr><td colspan="3">'.traer_tipobiopsiapdf($informe["tipobiopsia"]).'</td></tr>
      <tr><td colspan="3">Fecha prevista de biopsia: <u>'.$informe["fecha_previstabiopsia"].'</u></td></tr>
    </tbody>
  </table>
  <table style="border:1px solid; width:100%;">
    <colgroup>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
    </colgroup>
    <tbody>
      <tr><td colspan="2" style="border:1px solid; color: #fff; background-color: #000;"><b>AUTORIZACIÓN DEL MÉDICO</b></td></tr>
      <tr><td colspan="2">Certifico que la información del paciente y del médico prescriptor en esta solicitud es correcta según mi conocimiento y que he solicitado el test arriba indicado con base en mi criterio profesional de indicación clínica. He explicado las limitaciones de este test y he respondido cualquier pregunta con criterio médico. Entiendo que Igenomix pueda necesitar información adicional y acepto proporcionar esta información si es necesario.</td></tr>
      <tr><td></td><td></td></tr>
      <tr><td></td><td></td></tr>
      <tr><td style="text-align:center;">Firma del médico</td><td style="text-align:center;">Fecha: <u>'.$informe["fecha_autorizacion"].'</u></td></tr>
    </tbody>
  </table>
  <table class="table_principal" border="0">
    <colgroup>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
    </colgroup>
    <tbody>
      <tr><td colspan="2"><b>HISTORIA CLÍNICA</b></td></tr>
      <tr><td>NHC(*): <u>'.mb_strtoupper($paciente["dni"]).'</u></td><td>Nombres y Apellidos: <u>'.mb_strtoupper($paciente["nom"]).' '.mb_strtoupper($paciente["ape"]).'</u></td></tr>
      <tr><td colspan="2"><small>(*)En el caso de no existir indicar NO APLICA</small></td></tr>
    </tbody>
  </table>
  <table style="border:1px solid; width:100%;">
    <colgroup>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
    </colgroup>
    <tbody>
      <tr><td colspan="4"><b>INDICACIONES</b></td></tr>
      <tr><td colspan="4"><b>PGT-A</b></td></tr>
      <tr>
        <td style="text-align: center;" class="marca">'.($informe["pgta_edadmaterna"] == 1 ? "X" : "").'</td><td>Edad materna avanzada</td>
        <td style="text-align: center;" class="marca">'.($informe["pgta_gestacion"] == 1 ? "X" : "").'</td><td>Gestacion aneuploide prevista</td>
      </tr>
      <tr>
        <td style="text-align: center;" class="marca">'.($informe["pgta_falloimplantacion"] == 1 ? "X" : "").'</td><td>Fallo de implantación</td>
        <td style="text-align: center;" class="marca">'.($informe["pgta_factormasculino"] == 1 ? "X" : "").'</td><td>Factor masculino</td>
      </tr>
      <tr>
        <td style="text-align: center;" class="marca">'.($informe["pgta_aborto"] == 1 ? "X" : "").'</td><td>Aborto recurrente</td>
        <td style="text-align: center;" class="marca">'.($informe["pgta_enfermedadsexo"] == 1 ? "X" : "").'</td><td>Enfermedad ligada al sexo</td>
      </tr>
      <tr><td style="text-align: center;" class="marca">'.($informe["pgta_fish"] == 1 ? "X" : "").'</td><td colspan="3">FISH anormal de espermatozoides</td></tr>

      <tr><td colspan="4"><b>PGT-SR</b></td></tr>
      <tr><td colspan="4">Cariotipo alterado</td></tr>
      <tr><td style="text-align: center;" class="marca">'.($informe["pgtsr_translocacion"] == 1 ? "X" : "").'</td><td colspan="3">Translocación</td></tr>
      <tr><td style="text-align: center;" class="marca">'.($informe["pgtsr_inversion"] == 1 ? "X" : "").'</td><td colspan="3">Inversión</td></tr>
      <tr><td style="text-align: center;" class="marca">'.($informe["pgtsr_anomalianumerica"] == 1 ? "X" : "").'</td><td colspan="3">Anomalía numérica</td></tr>
      <tr><td colspan="4">Fórmula: <u>'.mb_strtoupper($informe["pgtsr_formula"]).'</u></td></tr>

      <tr><td colspan="4"><b>PGT-M</b></td></tr>
      <tr><td colspan="4">Enfermedades monogénicas (indicar): <u>'.mb_strtoupper($informe["pgtm_enfermedades"]).'</u></td></tr>
      <tr><td colspan="4"><b>PGT-A, PGT-SR, PGT-M</b></td></tr>
      <tr><td colspan="4">Otras indicaciones: <u>'.mb_strtoupper($informe["pgt_otrasindicaciones"]).'</u></td></tr>
    </tbody>
  </table>
  <table class="table_principal" border="0">
    <colgroup>
      <col style="width: 100px;"/>
      <col style="width: 100px;"/>
    </colgroup>
    <tbody>
      <tr><td colspan="2"><b>INFORMACIÓN DE LA BIOPSIA</b></td></tr>
      <tr><td>Biopsia realizada por: <u>'.mb_strtoupper($biologo_biopsia["nombres"]).'</u></td><td>Fecha biopsia: <u>'.mb_strtoupper($informe["fecha_biopsia"]).'</u></td></tr>
      <tr><td>Tubing realizado por: <u>'.mb_strtoupper($biologo_tubing["nombres"]).'</u></td><td>Lote medio washing/ loading: <u>'.mb_strtoupper($informe["lote_medio"]).'</td></tr>
      '.$biologo_d6.'
      <tr></tr>
    </tbody>
  </table>
  <table border="0">
    <tbody>
      <tr><td><b>DATOS MUESTRAS</b></td></tr>
      <tr><td>'.traer_muestraspdf($protocolo, $informe, $paciente_iniciales).'</td></tr>
    </tbody>
  </table>
  <table class="table_principal">
    <colgroup>
      <col style="width: 250px;"/>
      <col style="width: 250px;"/>
    </colgroup>
    <tbody>
      <tr><td colspan="2"><b>ESPACIO RESERVADO PARA IGENOMIX</b></td></tr>
      <tr><td colspan="1" style="border:1px solid;">Recepcionado por: <u>'.mb_strtoupper($informe["recepcionado_por"]).'</u></td><td colspan="1" style="border:1px solid;">Fecha/ Hora: <u>'.$informe["fecha"].' '.$informe["hora"].'</u></td></tr>
      <tr><td colspan="2" style="border:1px solid;">Muestra aceptada/ rechazada (indicar motivo en caso de rechazo): <u>'.mb_strtoupper($informe["motivo_rechazo"]).'</u></td></tr>
    </tbody>
  </table>';

  require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
  require_once __DIR__ . '/vendor/autoload.php';
  $mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular"]);
  $mpdf->WriteHTML($estilo.'<body><div class="contenido">'.$cabecera.$head_foot.$html.'</div></body>');
  $mpdf->Output();
  /* print($estilo.'<body><div class="contenido">'.$cabecera.$head_foot.$html.'</div></body>'); */