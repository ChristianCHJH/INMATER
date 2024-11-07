<?php
  date_default_timezone_set('America/Lima');
  require($_SERVER["DOCUMENT_ROOT"]."/_database/database.php");
  require($_SERVER["DOCUMENT_ROOT"]."/_database/database_log.php");
  require($_SERVER["DOCUMENT_ROOT"]."/config/environment.php");

  function guardar_informe($data = [])
  {
    $protocolo = $data['protocolo'];
    $informe_id = existe_informe($protocolo);

    if ($informe_id == 0) {
      $data['informe_id'] = insertar_informe($data);
    } else {
      $data['informe_id'] = $informe_id;
      actualizar_informe($data);
    }

    insertar_informe_analisis($data);
    insertar_informe_tarifa($data);
    insertar_informe_origenovocito($data);
    insertar_informe_tipotransferencia($data);
    insertar_informe_tipobiopsia($data);
    insertar_informe_muestras($data);
  }

  function guardar_informe_ignomix($protocolo, $url){
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira set url_igenomix = ? WHERE pro = ? and estado is true;");
    $stmt->execute([$url, $protocolo]);

    return $stmt->rowCount();
  }

  function existe_informe_ignomix($protocolo){
    global $db;
    $stmt = $db->prepare("SELECT url_igenomix FROM lab_aspira WHERE pro = ? and lab_aspira.estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetch(PDO::FETCH_ASSOC)["url_igenomix"];
    } else {
      return 0;
    }
  }

  function existe_informe($protocolo)
  {
    global $db;
    $data = [];

    // buscar procedimiento_id
    $stmt = $db->prepare("SELECT id from igeno_informe where estado = 1 and protocolo = ?;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      return $data['id'];
    } else {
      return 0;
    }
  }

  function insertar_informe($data = [])
  {
    global $db;
    $stmt = $db->prepare("INSERT INTO igeno_informe (
      protocolo,
      sino_mitoscore_id,
      peticionario_clinica,
      peticionario_medicoremitente,
      peticionario_labmanager,
      peticionario_personacontacto,
      peticionario_mailcontacto,
      peticionario_mailresultados,
      peticionario_direccion,
      peticionario_ciudad,
      peticionario_provincia,
      peticionario_cp,
      paciente_id,
      cariotipo_paciente,
      pareja_id,
      cariotipo_pareja,
      igeno_idioma_id,
      fecha_extraccionovulos,
      ovulos_fecundados,
      embriones_biopsiados,
      igeno_metodofecundacion_id,
      fecha_transferencia,
      hora_transferencia,
      fecha_previstabiopsia,
      fecha_autorizacion,
      pgta_edadmaterna,
      pgta_gestacion,
      pgta_fish,
      pgta_falloimplantacion,
      pgta_factormasculino,
      pgta_aborto,
      pgta_enfermedadsexo,
      pgtsr_translocacion,
      pgtsr_inversion,
      pgtsr_anomalianumerica,
      pgtsr_formula,
      pgtm_enfermedades,
      pgt_otrasindicaciones,
      biologo_biopsia_id,
      biologo_tubing_id,
      fecha_biopsia,
      lote_medio,
      biologo_biopsia_d6_id,
      biologo_tubing_d6_id,
      fecha_biopsia_d6,
      lote_medio_d6,
      correlativo,
      recepcionado_por,
      fecha,
      hora,
      motivo_rechazo,
      idusercreate)
    values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $stmt->execute([
      $data["protocolo"],
      $data["mitoscore_id"],
      $data["peticionario_clinica"],
      $data["peticionario_medicoremitente"],
      $data["peticionario_labmanager"],
      $data["peticionario_personacontacto"],
      $data["peticionario_mailcontacto"],
      $data["peticionario_mailresultados"],
      $data["peticionario_direccion"],
      $data["peticionario_ciudad"],
      $data["peticionario_provincia"],
      $data["peticionario_cp"],
      $data["paciente_id"],
      $data["cariotipo_paciente"],
      $data["pareja_id"],
      $data["cariotipo_pareja"],
      $data["idioma_id"],
      $data["fecha_extraccionovulos"],
      $data["ovulos_fecundados"],
      $data["embriones_biopsiados"],
      $data["metodo_fecundacion_id"],
      $data["fecha_transferencia"],
      $data["hora_transferencia"],
      $data["fecha_previstabiopsia"],
      $data["fecha_autorizacion"],
      $data["edad_materna"],
      $data["gestacion"],
      $data["fish"],
      $data["fallo_implantacion"],
      $data["factor_masculino"],
      $data["aborto"],
      $data["enfermedad_sexo"],
      $data["translocacion"],
      $data["inversion"],
      $data["anomalia_numerica"],
      $data["formula"],
      $data["enfermedades"],
      $data["otras_indicaciones"],
      $data["biologo_biopsia_id"],
      $data["biologo_tubing_id"],
      $data["fecha_biopsia"],
      $data["lote_medio"],
      $data["biologo_biopsia_d6_id"],
      $data["biologo_tubing_d6_id"],
      $data["fecha_biopsia_d6"],
      $data["lote_medio_d6"],
      $data["correlativo"],
      $data["recepcionado_por"],
      $data["fecha"],
      $data["hora"],
      $data["motivo_rechazo"],
      $data["login"]
    ]);

    return $db->lastInsertId();
  }

  function insertar_informe_analisis($data)
  {
    global $db;

    // buscar procedimiento_id
    $stmt = $db->prepare("DELETE FROM igeno_informe_analisis WHERE igeno_informe_id = ?;");
    $stmt->execute([$data["informe_id"]]);

    foreach ($data["analisis"] as $key => $value) {
      $stmt = $db->prepare("INSERT INTO igeno_informe_analisis (igeno_informe_id, igeno_analisis_id) VALUES (?, ?);");
      $stmt->execute([$data["informe_id"], $value]);
    }
  }

  function insertar_informe_tarifa($data)
  {
    global $db;

    // buscar procedimiento_id
    $stmt = $db->prepare("DELETE FROM igeno_informe_tarifa WHERE igeno_informe_id = ?;");
    $stmt->execute([$data["informe_id"]]);

    foreach ($data["tarifa"] as $key => $value) {
      $stmt = $db->prepare("INSERT INTO igeno_informe_tarifa (igeno_informe_id, igeno_tarifa_id) VALUES (?, ?);");
      $stmt->execute([$data["informe_id"], $value]);
    }
  }

  function insertar_informe_origenovocito($data)
  {
    global $db;

    // buscar procedimiento_id
    $stmt = $db->prepare("DELETE FROM igeno_informe_origenovocito WHERE igeno_informe_id = ?;");
    $stmt->execute([$data["informe_id"]]);

    foreach ($data["origenovocito"] as $key => $value) {
      $stmt = $db->prepare("INSERT INTO igeno_informe_origenovocito (igeno_informe_id, igeno_origenovocito_id) VALUES (?, ?);");
      $stmt->execute([$data["informe_id"], $value]);
    }
  }

  function insertar_informe_tipotransferencia($data)
  {
    global $db;

    // buscar procedimiento_id
    $stmt = $db->prepare("DELETE FROM igeno_informe_tipotransferencia WHERE igeno_informe_id = ?;");
    $stmt->execute([$data["informe_id"]]);

    foreach ($data["tipotransferencia"] as $key => $value) {
      $stmt = $db->prepare("INSERT INTO igeno_informe_tipotransferencia (igeno_informe_id, igeno_tipotransferencia_id) VALUES (?, ?);");
      $stmt->execute([$data["informe_id"], $value]);
    }
  }

  function insertar_informe_tipobiopsia($data)
  {
    global $db;

    // buscar procedimiento_id
    $stmt = $db->prepare("DELETE FROM igeno_informe_tipobiopsia WHERE igeno_informe_id = ?;");
    $stmt->execute([$data["informe_id"]]);

    foreach ($data["tipobiopsia"] as $key => $value) {
      $stmt = $db->prepare("INSERT INTO igeno_informe_tipobiopsia (igeno_informe_id, igeno_tipobiopsia_id) VALUES (?, ?);");
      $stmt->execute([$data["informe_id"], $value]);
    }
  }

  function insertar_informe_muestras($data)
  {
    global $db;

    $stmt = $db->prepare("DELETE FROM igeno_informe_muestras WHERE igeno_informe_id = ?;");
    $stmt->execute([$data["informe_id"]]);

    foreach ($data["muestras"]["ovos"] as $index => $value) {
      $ovo_fres = 0;
      $ovo_vitri = 0;
      $vitri_d2 = 0;
      $vitri_d3 = 0;
      $blasto_vitri = 0;
      $d3 = 0;
      $d5 = 0;
      $d6 = 0;
      $rebiopsia = 0;
      $nucleo_visible = 0;
      $tubing = 0;

      if (in_array($value, $data["muestras"]["ovo_fres"], true)) $ovo_fres = 1;
      if (in_array($value, $data["muestras"]["ovo_vitri"], true)) $ovo_vitri = 1;
      if (in_array($value, $data["muestras"]["vitri_d2"], true)) $vitri_d2 = 1;
      if (in_array($value, $data["muestras"]["vitri_d3"], true)) $vitri_d3 = 1;
      if (in_array($value, $data["muestras"]["blasto_vitri"], true)) $blasto_vitri = 1;
      if (in_array($value, $data["muestras"]["d3"], true)) $d3 = 1;
      if (in_array($value, $data["muestras"]["d5"], true)) $d5 = 1;
      if (in_array($value, $data["muestras"]["d6"], true)) $d6 = 1;
      if (in_array($value, $data["muestras"]["rebiopsia"], true)) $rebiopsia = 1;
      if (in_array($value, $data["muestras"]["nucleo_visible"], true)) $nucleo_visible = 1;
      if (in_array($value, $data["muestras"]["tubing"], true)) $tubing = 1;

      $stmt = $db->prepare("INSERT INTO igeno_informe_muestras (
        igeno_informe_id,
        numero_embrion,
        ovo_fres,
        ovo_vitri,
        vitri_d2,
        vitri_d3,
        blasto_vitri,
        d3,
        d5,
        d6,
        rebiopsia,
        nucleo_visible,
        tubing,
        observaciones)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
      $stmt->execute([
        $data["informe_id"],
        $value,
        $ovo_fres,
        $ovo_vitri,
        $vitri_d2,
        $vitri_d3,
        $blasto_vitri,
        $d3,
        $d5,
        $d6,
        $rebiopsia,
        $nucleo_visible,
        $tubing,
        $data["muestras"]["observaciones"][$index]
      ]);
    }
  }

  function actualizar_informe($data = [])
  {
    global $db;

    $stmt = $db->prepare("UPDATE igeno_informe set
      protocolo=?,
      sino_mitoscore_id=?,
      peticionario_clinica=?,
      peticionario_medicoremitente=?,
      peticionario_labmanager=?,
      peticionario_personacontacto=?,
      peticionario_mailcontacto=?,
      peticionario_mailresultados=?,
      peticionario_direccion=?,
      peticionario_ciudad=?,
      peticionario_provincia=?,
      peticionario_cp=?,
      paciente_id=?,
      cariotipo_paciente=?,
      pareja_id=?,
      cariotipo_pareja=?,
      igeno_idioma_id=?,
      fecha_extraccionovulos=?,
      ovulos_fecundados=?,
      embriones_biopsiados=?,
      igeno_metodofecundacion_id=?,
      fecha_transferencia=?,
      hora_transferencia=?,
      fecha_previstabiopsia=?,
      fecha_autorizacion=?,
      pgta_edadmaterna=?,
      pgta_gestacion=?,
      pgta_fish=?,
      pgta_falloimplantacion=?,
      pgta_factormasculino=?,
      pgta_aborto=?,
      pgta_enfermedadsexo=?,
      pgtsr_translocacion=?,
      pgtsr_inversion=?,
      pgtsr_anomalianumerica=?,
      pgtsr_formula=?,
      pgtm_enfermedades=?,
      pgt_otrasindicaciones=?,
      biologo_biopsia_id=?,
      biologo_tubing_id=?,
      fecha_biopsia=?,
      lote_medio=?,
      biologo_biopsia_d6_id=?,
      biologo_tubing_d6_id=?,
      fecha_biopsia_d6=?,
      lote_medio_d6=?,
      correlativo=?,
      recepcionado_por=?,
      fecha=?,
      hora=?,
      motivo_rechazo=?,
      iduserupdate=?
      WHERE id=?;");
    $stmt->execute([
      $data["protocolo"],
      $data["mitoscore_id"],
      $data["peticionario_clinica"],
      $data["peticionario_medicoremitente"],
      $data["peticionario_labmanager"],
      $data["peticionario_personacontacto"],
      $data["peticionario_mailcontacto"],
      $data["peticionario_mailresultados"],
      $data["peticionario_direccion"],
      $data["peticionario_ciudad"],
      $data["peticionario_provincia"],
      $data["peticionario_cp"],
      $data["paciente_id"],
      $data["cariotipo_paciente"],
      $data["pareja_id"],
      $data["cariotipo_pareja"],
      $data["idioma_id"],
      $data["fecha_extraccionovulos"],
      $data["ovulos_fecundados"],
      $data["embriones_biopsiados"],
      $data["metodo_fecundacion_id"],
      $data["fecha_transferencia"],
      $data["hora_transferencia"],
      $data["fecha_previstabiopsia"],
      $data["fecha_autorizacion"],
      $data["edad_materna"],
      $data["gestacion"],
      $data["fish"],
      $data["fallo_implantacion"],
      $data["factor_masculino"],
      $data["aborto"],
      $data["enfermedad_sexo"],
      $data["translocacion"],
      $data["inversion"],
      $data["anomalia_numerica"],
      $data["formula"],
      $data["enfermedades"],
      $data["otras_indicaciones"],
      $data["biologo_biopsia_id"],
      $data["biologo_tubing_id"],
      $data["fecha_biopsia"],
      $data["lote_medio"],
      $data["biologo_biopsia_d6_id"],
      $data["biologo_tubing_d6_id"],
      $data["fecha_biopsia_d6"],
      $data["lote_medio_d6"],
      $data["correlativo"],
      $data["recepcionado_por"],
      $data["fecha"],
      $data["hora"],
      $data["motivo_rechazo"],
      $data["login"],
      $data['informe_id']
    ]);
  }

  function traer_informe($protocolo)
  {
    global $db;
    $informe = [];
    $informe_id = existe_informe($protocolo);

    if ($informe_id == 0) {
      $medico = traer_medico($protocolo);
      $procedimiento = traer_procedimiento($protocolo);

      return array(
        'protocolo' => $protocolo,
        'analisis' => [3],
        'sino_mitoscore_id' => 1,
        'tarifa' => [],
        // peticionario del estudio
        'peticionario_clinica' => 'INMATER',
        'peticionario_medicoremitente' => $medico["nombres"],
        'peticionario_labmanager' => 'FERNANDO PEÑA',
        'peticionario_personacontacto' => $medico["nombres"],
        'peticionario_mailcontacto' => $medico["mail"],
        'peticionario_mailresultados' => $medico["mail"],
        'peticionario_direccion' => 'AV. GUARDIA CIVIL 655',
        'peticionario_ciudad' => 'LIMA',
        'peticionario_provincia' => 'LIMA',
        'peticionario_cp' => 'SAN BORJA',
        // datos paciente
        /* 'paciente_id' => $paciente["dni"], */
        'cariotipo_paciente' => '',
        'cariotipo_pareja' => '',
        'igeno_idioma_id' => 1,
        // informacion del ciclo
        'origenovocito' => traer_origenovocito($protocolo),
        'fecha_extraccionovulos' => traer_fechaextraccionovulos($protocolo),
        'ovulos_fecundados' => traer_ovulosfecundados($protocolo),
        'embriones_biopsiados' => traer_embrionesbiopsiados($protocolo),
        'igeno_metodofecundacion_id' => empty($procedimiento["p_fiv"]) ? (empty($procedimiento["p_icsi"]) ? "0" : "1") : "1",
        'igeno_metodofecundacion_fiv' => empty($procedimiento["p_fiv"]) ? false : true,
        'igeno_metodofecundacion_icsi' => empty($procedimiento["p_icsi"]) ? false : true,
        'fecha_transferencia' => '',
        'hora_transferencia' => '',
        'tipotransferencia' => [2],
        'tipobiopsia' => traer_tipobiopsia($protocolo),
        'fecha_previstabiopsia' => traer_fechaprevistabiopsia($protocolo),
        // autorizacion del medico
        'fecha_autorizacion' => date('Y-m-d'),
        // indicaciones
        'pgta_edadmaterna' => 0,
        'pgta_gestacion' => 0,
        'pgta_fish' => 0,
        'pgta_falloimplantacion' => 0,
        'pgta_factormasculino' => 0,
        'pgta_aborto' => 0,
        'pgta_enfermedadsexo' => 0,
        'pgtsr_translocacion' => 0,
        'pgtsr_inversion' => 0,
        'pgtsr_anomalianumerica' => 0,
        'pgtsr_formula' => '',
        'pgtm_enfermedades' => '',
        'pgt_otrasindicaciones' => '',
        // informacion de la biopsia d5
        'biologo_biopsia_id' => traer_biologobiopsia($protocolo),
        'biologo_tubing_id' => traer_biologobiopsia($protocolo),
        'fecha_biopsia' => date('Y-m-d'),
        'lote_medio' => '',
        // informacion de la biopsia d6
        'biologo_biopsia_d6_id' => traer_biologobiopsia_d6($protocolo),
        'biologo_tubing_d6_id' => traer_biologobiopsia_d6($protocolo),
        'fecha_biopsia_d6' => date('Y-m-d'),
        'lote_medio_d6' => '',
        // datos muestra
        'muestras' => [
          'ovo_fres' => traer_ovofres($protocolo),
          'ovo_vitri' => traer_ovovitri($protocolo),
          'vitri_d2' => [],
          'vitri_d3' => [],
          'blasto_vitri' => traer_ovovitri($protocolo),
          'd3' => traer_dias($protocolo, 3),
          'd5' => traer_dias_5($protocolo, 5),
          'd6' => traer_dias_6($protocolo, 6),
          'rebiopsia' => [],
          'nucleo_visible' => traer_nucleovisible($protocolo),
          'tubing' => traer_tubing($protocolo),
          'observaciones' => [],
        ],
        // espacio para igenomix
        'correlativo' => '',
        'recepcionado_por' => '',
        'fecha' => '',
        'hora' => '',
        'motivo_rechazo' => '',
      );
    } else {
      $stmt = $db->prepare("SELECT * from igeno_informe where id = ?;");
      $stmt->execute([$informe_id]);

      $data = [];
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      $data["analisis"] = traer_informe_analisis($data["id"]);
      $data["tarifa"] = traer_informe_tarifa($data["id"]);
      $data["origenovocito"] = traer_informe_origenovocito($data["id"]);
      $data["tipotransferencia"] = traer_informe_tipotransferencia($data["id"]);
      $data["tipobiopsia"] = traer_informe_tipobiopsia($data["id"]);
      $data["muestras"] = [
        'ovo_fres' => traer_informemuestrasovofres($data["id"]),
        'ovo_vitri' => traer_informemuestrasovovitri($data["id"]),
        'vitri_d2' => traer_informemuestrasvitrid2($data["id"]),
        'vitri_d3' => traer_informemuestrasvitrid3($data["id"]),
        'blasto_vitri' => traer_informemuestrasblastovitri($data["id"]),
        'd3' => traer_informemuestrasd3($data["id"]),
        'd5' => traer_informemuestrasd5($data["id"]),
        'd6' => traer_informemuestrasd6($data["id"]),
        'rebiopsia' => traer_informemuestrasrebiopsia($data["id"]),
        'nucleo_visible' => traer_informemuestrasnucleovisible($data["id"]),
        'tubing' => traer_informemuestrastubing($data["id"]),
        'observaciones' => traer_informemuestrasobservaciones($data["id"]),
      ];
      return $data;
    }

    return $informe;
  }

  function traer_dias($protocolo, $dia)
  {
    global $db;

    // buscar origen ovulo
    $stmt = $db->prepare("SELECT dias FROM lab_aspira WHERE pro = ? and lab_aspira.estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      if ($stmt->fetch(PDO::FETCH_ASSOC)["dias"] == $dia + 1) {
        $stmt = $db->prepare("SELECT
          a.ovo
          FROM lab_aspira_dias a
          INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
          LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
          WHERE a.pro=? AND a.d5cel <> '' AND a.d5cel <> 'Bloq' AND a.d5f_cic = 'C' and (a.d5d_bio<>0) and a.estado is true
          UNION
          SELECT
          a.ovo
          FROM lab_aspira_dias a
          INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
          LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
          WHERE a.pro=? AND a.d6cel <> '' AND a.d6cel <> 'Bloq' AND a.d6f_cic = 'C' and (a.d6d_bio<>0) and a.estado is true");
        $stmt->execute([$protocolo, $protocolo]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
      } else {
        return [];
      }
    } else {
      return [];
    }
  }

  function traer_dias_5($protocolo)
  {
    global $db;

    // buscar origen ovulo
    $stmt = $db->prepare("SELECT dias FROM lab_aspira WHERE pro = ? and estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      $stmt = $db->prepare("SELECT
        a.ovo
        FROM lab_aspira_dias a
        INNER JOIN lab_aspira b ON b.pro = a.pro
        LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
        WHERE a.pro=? AND a.d5cel <> '' AND a.d5cel <> 'Bloq' AND a.d5f_cic = 'C' and (a.d5d_bio<>0) and a.estado is true");
      $stmt->execute([$protocolo]);

      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_dias_6($protocolo)
  {
    global $db;

    // buscar origen ovulo
    $stmt = $db->prepare("SELECT dias FROM lab_aspira WHERE pro = ? and estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      $stmt = $db->prepare("SELECT
        a.ovo
        FROM lab_aspira_dias a
        INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
        LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
        WHERE a.pro = ? AND a.d6cel <> '' AND a.d6cel <> 'Bloq' AND a.d6f_cic = 'C' and (a.d6d_bio<>0) and a.estado is true");
      $stmt->execute([$protocolo]);

      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_ovofres($protocolo)
  {
    global $db;

    // buscar origen ovulo
    $stmt = $db->prepare("SELECT o_ovo from lab_aspira where o_ovo <> '' and pro = ? and estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      if ($stmt->fetch(PDO::FETCH_ASSOC)["o_ovo"] == "Fresco") {
        $stmt = $db->prepare("SELECT
          a.ovo
          FROM lab_aspira_dias a
          INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
          LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
          WHERE a.pro=? AND a.d5cel <> '' AND a.d5cel <> 'Bloq' AND a.d5f_cic = 'C' and (a.d5d_bio<>0) and a.estado is true
          UNION
          SELECT
          a.ovo
          FROM lab_aspira_dias a
          INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
          LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
          WHERE a.pro=? AND a.d6cel <> '' AND a.d6cel <> 'Bloq' AND a.d6f_cic = 'C' and (a.d6d_bio<>0) and a.estado is true");
        $stmt->execute([$protocolo, $protocolo]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
      } else {
        return [];
      }
    } else {
      return [];
    }
  }

  function traer_ovovitri($protocolo)
  {
    global $db;

    // buscar origen ovulo
    $stmt = $db->prepare("SELECT o_ovo from lab_aspira where o_ovo <> '' and pro = ? and estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      if ($stmt->fetch(PDO::FETCH_ASSOC)["o_ovo"] == "Vitrificado") {
        $stmt = $db->prepare("SELECT
          a.ovo
          FROM lab_aspira_dias a
          INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
          LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
          WHERE a.pro=? AND a.d5cel <> '' AND a.d5cel <> 'Bloq' AND a.d5f_cic = 'C' and (a.d5d_bio<>0) and a.estado is true
          UNION
          SELECT
          a.ovo
          FROM lab_aspira_dias a
          INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
          LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
          WHERE a.pro=? AND a.d6cel <> '' AND a.d6cel <> 'Bloq' AND a.d6f_cic = 'C' and (a.d6d_bio<>0) and a.estado is true");
        $stmt->execute([$protocolo, $protocolo]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
      } else {
        return [];
      }

    } else {
      return [];
    }
  }

  function traer_nucleovisible($protocolo)
  {
    global $db;

    $stmt = $db->prepare("SELECT
      a.ovo
      FROM lab_aspira_dias a
      INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
      LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
      WHERE a.pro=? AND a.d5cel <> '' AND a.d5cel <> 'Bloq' AND a.d5f_cic = 'C' and a.estado is true
      UNION
      SELECT
      a.ovo
      FROM lab_aspira_dias a
      INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
      LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
      WHERE a.pro=? AND a.d6cel <> '' AND a.d6cel <> 'Bloq' AND a.d6f_cic = 'C' and a.estado is true");
    $stmt->execute([$protocolo, $protocolo]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);;
  }

  function traer_tubing($protocolo)
  {
    global $db;

    $stmt = $db->prepare("SELECT
      a.ovo
      FROM lab_aspira_dias a
      INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
      LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
      WHERE a.pro=? AND a.d5cel <> '' AND a.d5cel <> 'Bloq' AND a.d5f_cic = 'C' and (a.d5d_bio<>0) and a.estado is true
      UNION
      SELECT
      a.ovo
      FROM lab_aspira_dias a
      INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
      LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
      WHERE a.pro=? AND a.d6cel <> '' AND a.d6cel <> 'Bloq' AND a.d6f_cic = 'C' and (a.d6d_bio<>0) and a.estado is true");
    $stmt->execute([$protocolo, $protocolo]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);;
  }

  function traer_origenovocito($protocolo)
  {
    global $db;

    $stmt = $db->prepare("SELECT tip, rep FROM lab_aspira WHERE pro = ? and estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      $origenovocito = [];

      // buscar origen de ovulo
      if ($data["tip"] == "P" || $data["tip"] == "D") {
        array_push($origenovocito, 1);
      }

      if ($data["tip"] == "R") {
        array_push($origenovocito, 2);
      }

      // buscar origen de semen
      $stmt = $db->prepare("SELECT mue FROM lab_andro_cap WHERE pro = ? OR rep = ? and eliminado is false;");
      $stmt->execute([$protocolo, $data["rep"]]);
      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data["mue"] == 2 || $data["mue"] == 4) {
          array_push($origenovocito, 4);
        }

        if ($data["mue"] == 1 || $data["mue"] == 3) {
          array_push($origenovocito, 3);
        }
        
      }

      return $origenovocito;
    } else {
      return [];
    }
  }

  function traer_fechaextraccionovulos($protocolo)
  {
    global $db;

    $stmt = $db->prepare("SELECT
      CASE hr.des_dia WHEN 0 THEN la.fec0 ELSE la.f_pun END fecha_extraccionovulos
      FROM hc_reprod hr
      INNER JOIN lab_aspira la on la.rep = hr.id and la.estado is true AND la.pro = ? where hr.estado = true;");
    $stmt->execute([$protocolo]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $data["fecha_extraccionovulos"];
  }

  function traer_ovulosfecundados($protocolo)
  {
    global $db;

    $stmt = $db->prepare("SELECT pro FROM lab_aspira_dias WHERE pro = ? and estado is true AND d1est = 'MII' AND d1f_cic = 'O' AND d1c_pol = '2' AND d1pron = '2';");
    $stmt->execute([$protocolo]);

    return $stmt->rowCount();
  }

  function traer_embrionesbiopsiados($protocolo)
  {
    global $db;

    $stmt = $db->prepare("SELECT
      a.ovo, a.d5cel celula, a.d5mci mci, a.d5tro tro, 5 dia, coalesce(c.nombre, '') observacion
      FROM lab_aspira_dias a
      INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
      LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
      WHERE a.pro=? and a.estado is true AND a.d5cel <> '' AND a.d5cel <> 'Bloq' AND a.d5f_cic = 'C' and (a.d5d_bio<>0)
      UNION
      SELECT
      a.ovo, a.d6cel celula, a.d6mci mci, a.d6tro tro, 6 dia, coalesce(c.nombre, '') observacion
      FROM lab_aspira_dias a
      INNER JOIN lab_aspira b ON b.pro = a.pro and b.estado is true
      LEFT JOIN lab_aspira_dias_observacion_biopsia c ON c.idrepro = b.rep AND c.ovo = a.ovo AND c.estado = 1
      WHERE a.pro=? and a.estado is true AND a.d6cel <> '' AND a.d6cel <> 'Bloq' AND a.d6f_cic = 'C' and (a.d6d_bio<>0)");
    $stmt->execute([$protocolo, $protocolo]);

    return $stmt->rowCount();
  }

  function traer_tipobiopsia($protocolo)
  {
    global $db;

    // buscar dias
    $stmt = $db->prepare("SELECT dias from lab_aspira where pro = ? and estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      $data = $stmt->fetch(PDO::FETCH_ASSOC);
      return $data["dias"] == 6 || $data["dias"] == 7 ? [2] : [];
    } else {
      return [];
    }
  }

  function traer_fechaprevistabiopsia($protocolo)
  {
    global $db;

    $stmt = $db->prepare("SELECT
    CASE dias WHEN 7 THEN fec6 WHEN 6 THEN fec5 WHEN 5 THEN fec4 WHEN 4 THEN fec3 WHEN 3 THEN fec2 WHEN 2 THEN fec1 WHEN 1 THEN fec0 ELSE '1899-12-30' END fecha_previstabiopsia
    FROM lab_aspira WHERE pro = ? and estado is true;");
    $stmt->execute([$protocolo]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $data["fecha_previstabiopsia"];
  }

  function traer_biologobiopsia($protocolo)
  {
    global $db;

    $stmt = $db->prepare("SELECT emb5 embriologo FROM lab_aspira WHERE pro = ? and estado is true;");
    $stmt->execute([$protocolo]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $data["embriologo"];
  }

  function traer_biologobiopsia_d6($protocolo)
  {
    global $db;

    $stmt = $db->prepare("SELECT emb6 embriologo FROM lab_aspira WHERE pro = ? and estado is true;");
    $stmt->execute([$protocolo]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $data["embriologo"];
  }

  function traer_procedimiento($protocolo)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT rep from lab_aspira where pro = ? and estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      // buscar datos de paciente
      $stmt = $db->prepare("SELECT * from hc_reprod where estado = true and id = ?;");
      $stmt->execute([$data["rep"]]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return [];
    }
  }

  function traer_paciente($protocolo)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT dni from lab_aspira where pro = ? and estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      // buscar datos de paciente
      $stmt = $db->prepare("SELECT * from hc_paciente where dni = ?;");
      $stmt->execute([$data["dni"]]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return [];
    }
  }

  function traer_pareja($dni)
  {
    global $db;

    $stmt = $db->prepare("SELECT
      hp.*
      FROM hc_pareja hp
      INNER JOIN hc_pare_paci hpp ON hpp.p_dni = hp.p_dni AND hpp.estado = 1 AND hpp.dni = ?
      WHERE hp.estado = 1");
    $stmt->execute([$dni]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  function traer_medico($protocolo)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT rep reproduccion_id from lab_aspira where pro = ? and estado is true;");
    $stmt->execute([$protocolo]);

    if ($stmt->rowCount() > 0) {
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      // buscar datos de medico 
      $stmt = $db->prepare("SELECT med medico_id from hc_reprod where estado = true and  id = ?;");
      $stmt->execute([$data["reproduccion_id"]]);
      $medico = $stmt->fetch(PDO::FETCH_ASSOC);

      $stmt = $db->prepare("SELECT nom nombres, mail from usuario where userx = ?;");
      $stmt->execute([$medico["medico_id"]]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return [];
    }
  }

  function traer_informe_analisis($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT igeno_analisis_id FROM igeno_informe_analisis WHERE igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informe_tarifa($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT igeno_tarifa_id FROM igeno_informe_tarifa WHERE igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informe_origenovocito($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT igeno_origenovocito_id FROM igeno_informe_origenovocito WHERE igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informe_tipotransferencia($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT igeno_tipotransferencia_id FROM igeno_informe_tipotransferencia WHERE igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informe_tipobiopsia($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT igeno_tipobiopsia_id FROM igeno_informe_tipobiopsia WHERE igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasovofres($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE ovo_fres = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasovovitri($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE ovo_vitri = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasvitrid2($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE vitri_d2 = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasvitrid3($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE vitri_d3 = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasblastovitri($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE blasto_vitri = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasd3($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE d3 = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasd5($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE d5 = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasd6($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE d6 = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasrebiopsia($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE rebiopsia = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasnucleovisible($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE nucleo_visible = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrastubing($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT numero_embrion FROM igeno_informe_muestras WHERE tubing = 1 AND igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_informemuestrasobservaciones($informe_id)
  {
    global $db;

    // buscar dni
    $stmt = $db->prepare("SELECT observaciones FROM igeno_informe_muestras WHERE igeno_informe_id = ?;");
    $stmt->execute([$informe_id]);

    if ($stmt->rowCount() > 0) {
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    } else {
      return [];
    }
  }

  function traer_analisispdf($analisis)
  {
    global $db;
    $stmt = $db->prepare("SELECT id, descripcion FROM igeno_analisis WHERE estado=1");
    $stmt->execute();
    $html = '<table style="width: 100%;">';
    $total = $stmt->rowCount();

    for ($i=0; $i < $total * 2; $i++) { 
      $html .= '<col style="width: 100px;"/>';
    }

    $html .= '<tbody><tr>';

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $checked = '';
      if (in_array($data["id"], $analisis, true)) $checked = 'X';
  
      $html .= '<td class="marca">'.$checked.'</td><td class="descripcion">'.$data['descripcion'].'</td>';
    }

    $html .= '</tr></tbody></table>';
    /* $html = '<table style="width: 100%;"><tbody><tr><td>'.$stmt->rowCount().'</td></tbody></table>'; */

    return $html;
  }

  function traer_mitoscorepdf($mitoscore)
  {
    global $db;
    $stmt = $db->prepare("SELECT id, nombre descripcion FROM si_no WHERE estado=1");
    $stmt->execute();
    $html = '<table style="width: 100%;">';
    $total = $stmt->rowCount();

    for ($i=0; $i < ($total *2) + 1; $i++) { 
      $html .= '<col style="width: 100px;"/>';
    }

    $html .= '<tbody><tr><td>¿Solicita MitoScore?</td>';

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $checked = '';
      
      if ($data["id"] == $mitoscore) $checked = 'X';
  
      $html .= '<td class="marca">'.$checked.'</td><td class="descripcion">'.$data['descripcion'].'</td>';
    }

    $html .= '<td style="width: 5%;"></td><td class="descripcion"></td>';
    $html .= '</tr></tbody></table>';
    /* $html = '<table style="width: 100%;"><tbody><tr><td>'.$stmt->rowCount().'</td></tbody></table>'; */

    return $html;
  }

  function traer_tarifapdf($tarifa)
  {
    global $db;
    $stmt = $db->prepare("SELECT id, descripcion from igeno_tarifa where estado=1");
    $stmt->execute();
    $html = '<table style="width: 100%;">';
    $total = $stmt->rowCount();

    for ($i=0; $i < $total *2; $i++) { 
      $html .= '<col style="width: 100px;"/>';
    }

    $html .= '<tbody><tr>';

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $checked = '';
      if (in_array($data["id"], $tarifa, true)) $checked = 'X';
  
      $html .= '<td class="marca">'.$checked.'</td><td class="descripcion">'.$data['descripcion'].'</td>';
    }

    $html .= '</tr></tbody></table>';
    /* $html = '<table style="width: 100%;"><tbody><tr><td>'.$stmt->rowCount().'</td></tbody></table>'; */

    return $html;
  }

  function traer_origenovocitopdf($origenovocito)
  {
    global $db;
    $stmt = $db->prepare("SELECT id, descripcion from igeno_origenovocito where estado=1");
    $stmt->execute();
    $html = '<table style="width: 100%;">';
    $total = $stmt->rowCount();

    for ($i=0; $i < $total * 2; $i++) { 
      $html .= '<col style="width: 100px;"/>';
    }

    $html .= '<tbody><tr>';

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $checked = '';
      if (in_array($data["id"], $origenovocito, true)) $checked = 'X';
  
      $html .= '<td class="marca">'.$checked.'</td><td class="descripcion">'.$data['descripcion'].'</td>';
    }

    $html .= '</tr></tbody></table>';
    /* $html = '<table style="width: 100%;"><tbody><tr><td>'.$stmt->rowCount().'</td></tbody></table>'; */

    return $html;
  }

  function traer_metodofecundacionpdf($metodo_fecundacion)
  {
    global $db;
    $stmt = $db->prepare("SELECT id, descripcion FROM igeno_metodofecundacion WHERE estado=1;");
    $stmt->execute();
    $html = '<table style="width: 100%;">';
    $total = $stmt->rowCount();

    for ($i=0; $i < ($total *2) + 1; $i++) { 
      $html .= '<col style="width: 100px;"/>';
    }

    $html .= '<tbody><tr><td>Método de Fecundación</td>';

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $checked = '';
      
      if ($data["id"] == $metodo_fecundacion) $checked = 'X';
  
      $html .= '<td class="marca">'.$checked.'</td><td class="descripcion">'.$data['descripcion'].'</td>';
    }

    $html .= '<td class="descripcion"></td>';
    $html .= '</tr></tbody></table>';
    /* $html = '<table style="width: 100%;"><tbody><tr><td>'.$stmt->rowCount().'</td></tbody></table>'; */

    return $html;
  }

  function traer_tipotransferenciapdf($tipotransferencia)
  {
    global $db;
    $stmt = $db->prepare("SELECT id, descripcion from igeno_tipotransferencia where estado=1");
    $stmt->execute();
    $html = '<table style="width: 100%;">';
    $total = $stmt->rowCount();
    $html .= '<tbody><tr>';

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $checked = '';
      if (in_array($data["id"], $tipotransferencia, true)) $checked = 'X';
  
      $html .= '<td class="marca">'.$checked.'</td><td style="width: 45%;">'.$data['descripcion'].'</td>';
    }

    $html .= '</tr></tbody></table>';

    return $html;
  }

  function traer_tipobiopsiapdf($tipobiopsia)
  {
    global $db;
    $stmt = $db->prepare("SELECT id, descripcion from igeno_tipobiopsia where estado=1");
    $stmt->execute();
    $html = '<table style="width: 100%;">';
    $total = $stmt->rowCount();

    for ($i=0; $i < $total * 2; $i++) { 
      $html .= '<col style="width: 100px;"/>';
    }

    $html .= '<tbody><tr>';

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $checked = '';
      if (in_array($data["id"], $tipobiopsia, true)) $checked = 'X';
  
      $html .= '<td class="marca">'.$checked.'</td><td style="width: 45%;">'.$data['descripcion'].'</td>';
    }

    $html .= '</tr></tbody></table>';

    return $html;
  }

  function traer_biologo($biologobiopsia_id)
  {
    global $db;
    $stmt = $db->prepare("SELECT id, nom nombres FROM lab_user WHERE sta=0 AND id = ?;");
    $stmt->execute([$biologobiopsia_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  function traer_muestraspdf($protocolo, $informe, $paciente_iniciales)
  {
    global $db;

    $stmt = $db->prepare("SELECT
      a.ovo, a.d5cel celula, a.d5mci mci, a.d5tro tro, 5 dia, coalesce(c.nombre, '') observacion
      from lab_aspira_dias a
      inner join lab_aspira b on b.pro = a.pro and b.estado is true
      left join lab_aspira_dias_observacion_biopsia c on c.idrepro = b.rep and c.ovo = a.ovo and c.estado = 1
      where a.analizar = 1 and a.estado is true and a.pro=? and a.d5cel <> '' and a.d5cel <> 'Bloq' and a.d5f_cic = 'C' and (a.d5d_bio<>0)
      union
      select
      a.ovo, a.d6cel celula, a.d6mci mci, a.d6tro tro, 6 dia, coalesce(c.nombre, '') observacion
      from lab_aspira_dias a
      inner join lab_aspira b on b.pro = a.pro and b.estado is true
      left join lab_aspira_dias_observacion_biopsia c on c.idrepro = b.rep and c.ovo = a.ovo and c.estado = 1
      where a.analizar = 1 and a.estado is true and a.pro=? and a.d6cel <> '' and a.d6cel <> 'Bloq' and a.d6f_cic = 'C' and a.d6d_bio<>0");
    $stmt->execute([$protocolo, $protocolo]);

    $html = '
    <table style="width:100%;" border="1">
      <thead class="thead-dark">
        <tr>
          <th colspan="2" class="text-center vertical-center">Identificación del embrión</th>
          <th rowspan="2">Clasificación<br>morfológica<br>del embrión</th>
          <th colspan="5" class="text-center vertical-center">Origen</th>
          <th colspan="4" class="text-center vertical-center">Día de la biopsia</th>
          <th class="text-center">Núcleo visible</th>
          <th class="text-center vertical-center">Tubing</th>
          <th rowspan="2" class="text-center vertical-center">Observaciones</th>
        </tr>
        <tr>
          <th class="text-center">Iniciales<br>paciente</th>
          <th class="text-center">N°<br>Embrión</th>
          <th class="text-center">Ovo<br>fres</th>
          <th class="text-center">Ovo<br>vitri</th>
          <th class="text-center">Vitri<br>D2</th>
          <th class="text-center">Vitri<br>D3</th>
          <th class="text-center">Blasto<br>vitri</th>
          <th class="text-center">D3</th>
          <th class="text-center">D5</th>
          <th class="text-center">D6</th>
          <th class="text-center">Rebiopsia</th>
          <th class="text-center">Si</th>
          <th class="text-center">Si</th>
        </tr>
      </thead><tbody>';

    if ($stmt->rowCount() > 0) {
      $observaciones='';
      $index = 0;

      while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ovo_fres = '';
        $ovo_vitri = '';
        $vitri_d2 = '';
        $vitri_d3 = '';
        $blasto_vitri = '';
        $d3 = '';
        $d5 = '';
        $d6 = '';
        $rebiopsia = '';
        $nucleo_visible = '';
        $tubing = '';

        if (in_array($data['ovo'], $informe["muestras"]["ovo_fres"], true)) $ovo_fres = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["ovo_vitri"], true)) $ovo_vitri = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["vitri_d2"], true)) $vitri_d2 = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["vitri_d3"], true)) $vitri_d3 = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["blasto_vitri"], true)) $blasto_vitri = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["d3"], true)) $d3 = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["d5"], true)) $d5 = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["d6"], true)) $d6 = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["rebiopsia"], true)) $rebiopsia = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["nucleo_visible"], true)) $nucleo_visible = 'X';
        if (in_array($data['ovo'], $informe["muestras"]["tubing"], true)) $tubing = 'X';

        $nombres = "";
        $observaciones=mb_strtoupper($data['observacion']);
        $observaciones='<input type="text" name="observaciones'.$data['ovo'].'" value="'.mb_strtoupper($data['observacion']).'">';

        $html.='
        <tr>
          <td style="text-align: center;">'.$paciente_iniciales.'</td>
          <td style="text-align: center;">'.$nombres.$data['ovo'].'</td>
          <td style="text-align: center;">'.mb_strtoupper($data['celula'])." ".mb_strtoupper($data['mci']).mb_strtoupper($data['tro']).'</td>
          <td style="text-align: center;">'.$ovo_fres.'</td>
          <td style="text-align: center;">'.$ovo_vitri.'</td>
          <td style="text-align: center;">'.$vitri_d2.'</td>
          <td style="text-align: center;">'.$vitri_d3.'</td>
          <td style="text-align: center;">'.$blasto_vitri.'</td>
          <td style="text-align: center;">'.$d3.'</td>
          <td style="text-align: center;">'.$d5.'</td>
          <td style="text-align: center;">'.$d6.'</td>
          <td style="text-align: center;">'.$rebiopsia.'</td>
          <td style="text-align: center;">'.$nucleo_visible.'</td>
          <td style="text-align: center;">'.$tubing.'</td>
          <td style="text-align: center;">'.(count($informe["muestras"]["observaciones"]) != 0 ? $informe["muestras"]["observaciones"][$index] : '').'</td>
        </tr>';

        $index++;
      }
    }

    $html .= '</tbody></table>';

    return $html;
  }
  function verificar_dia5($protocolo)
  {
    global $db;
    $stmt = $db->prepare("SELECT * FROM lab_aspira_dias WHERE pro = ?  and estado is true and (d5d_bio is not null and d5d_bio <> 0)");
    $stmt->execute([$protocolo]);

    return ($stmt->rowCount() >= 1);
  }

  function verificar_dia6($protocolo)
  {
    global $db;
    $stmt = $db->prepare("SELECT * FROM lab_aspira_dias WHERE pro = ? and estado is true and (d6d_bio is not null and d6d_bio <> 0)");
    $stmt->execute([$protocolo]);

    return ($stmt->rowCount() >= 1);
  }