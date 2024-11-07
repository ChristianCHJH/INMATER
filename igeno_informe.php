<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php';
   require("_database/database_log.php");
   require("_database/database.php");
    ?>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="_images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="css/chosen.min.css">
  <link rel="stylesheet" href="css/jquery.timepicker.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
  <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
</head>
<body>
  <div class="loader">
    <img src="_images/load.gif" alt="">
  </div>
  <?php require ('_includes/menu_laboratorio_1.php'); ?>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Laboratorio</li>
        <li class="breadcrumb-item"><a href="lista_pro_f.php">Procedimientos</a></li>
        <?php print('<li class="breadcrumb-item"><a href="'.$_GET["path"].'.php?id='.$_GET["pro"].'" data-icon="back" class="ui-icon-alt" data-theme="a" rel="external">Protocolo '.$_GET["pro"].'</a></li>'); ?>
        <li class="breadcrumb-item active" aria-current="page">Informe IGenomix</li>
      </ol>
    </nav>
    <?php
      require ('_database/igeno_informe.php');

      $protocolo = $_GET["pro"];
      $paciente = traer_paciente($protocolo);
      $pareja = traer_pareja($paciente["dni"]);
      $paciente_iniciales = mb_strtoupper(substr($paciente["nom"], 0, 1).substr($paciente["ape"], 0, 1));
      
      if (!!$_POST) {
        $data = array(
          'protocolo' => $protocolo,
          'analisis' => isset($_POST['analisis']) ? $_POST['analisis'] : [],
          'mitoscore_id' => !!$_POST['mitoscore_id'] ? $_POST['mitoscore_id'] : 0,
          'tarifa' => isset($_POST['tarifa']) ? $_POST['tarifa'] : [],
          // peticionario del estudio
          'peticionario_clinica' => $_POST["peticionario_clinica"],
          'peticionario_medicoremitente' => $_POST["peticionario_medicoremitente"],
          'peticionario_labmanager' => $_POST["peticionario_labmanager"],
          'peticionario_personacontacto' => $_POST["peticionario_personacontacto"],
          'peticionario_mailcontacto' => $_POST["peticionario_mailcontacto"],
          'peticionario_mailresultados' => $_POST["peticionario_mailresultados"],
          'peticionario_direccion' => $_POST["peticionario_direccion"],
          'peticionario_ciudad' => $_POST["peticionario_ciudad"],
          'peticionario_provincia' => $_POST["peticionario_provincia"],
          'peticionario_cp' => $_POST["peticionario_cp"],
          // datos paciente
          'paciente_id' => $paciente["dni"],
          'cariotipo_paciente' => $_POST["cariotipo_paciente"],
          'pareja_id' => !!$pareja["p_dni"] ? $pareja["p_dni"] : 0,
          'cariotipo_pareja' => $_POST["cariotipo_pareja"],
          'idioma_id' => !!$_POST['idioma_id'] ? $_POST['idioma_id'] : 0,
          // informacion del ciclo
          'origenovocito' => isset($_POST['origenovocito']) ? $_POST['origenovocito'] : [],
          'fecha_extraccionovulos' => $_POST["fecha_extraccionovulos"],
          'ovulos_fecundados' => $_POST["ovulos_fecundados"],
          'embriones_biopsiados' => $_POST["embriones_biopsiados"],
          'metodo_fecundacion_id' => !!$_POST['metodo_fecundacion_id'] ? $_POST['metodo_fecundacion_id'] : 0,
          'fecha_transferencia' => $_POST["fecha_transferencia"],
          'hora_transferencia' => $_POST["hora_transferencia"],
          'tipotransferencia' => isset($_POST['tipotransferencia']) ? $_POST['tipotransferencia'] : [],
          'tipobiopsia' => isset($_POST['tipobiopsia']) ? $_POST['tipobiopsia'] : [],
          'fecha_previstabiopsia' => $_POST["fecha_previstabiopsia"],
          // autorizacion del medico
          'fecha_autorizacion' => $_POST["fecha_autorizacion"],
          // indicaciones
          'edad_materna' => isset($_POST["edad_materna"]) ? $_POST["edad_materna"] : 0,
          'gestacion' => isset($_POST["gestacion"]) ? $_POST["gestacion"] : 0,
          'fish' => isset($_POST["fish"]) ? $_POST["fish"] : 0,
          'fallo_implantacion' => isset($_POST["fallo_implantacion"]) ? $_POST["fallo_implantacion"] : 0,
          'factor_masculino' => isset($_POST["factor_masculino"]) ? $_POST["factor_masculino"] : 0,
          'aborto' => isset($_POST["aborto"]) ? $_POST["aborto"] : 0,
          'enfermedad_sexo' => isset($_POST["enfermedad_sexo"]) ? $_POST["enfermedad_sexo"] : 0,
          'translocacion' => isset($_POST["translocacion"]) ? $_POST["translocacion"] : 0,
          'inversion' => isset($_POST["inversion"]) ? $_POST["inversion"] : 0,
          'anomalia_numerica' => isset($_POST["anomalia_numerica"]) ? $_POST["anomalia_numerica"] : 0,
          'formula' => $_POST["formula"],
          'enfermedades' => $_POST["enfermedades"],
          'otras_indicaciones' => $_POST["otras_indicaciones"],
          // informacion de la biopsia
          'biologo_biopsia_id' => $_POST["biologo_biopsia_id"],
          'biologo_tubing_id' => $_POST["biologo_tubing_id"],
          'fecha_biopsia' => $_POST["fecha_biopsia"],
          'lote_medio' => $_POST["lote_medio"],
          // informacion de la biopsia d6
          'biologo_biopsia_d6_id' => isset($_POST["biologo_biopsia_d6_id"]) ? $_POST["biologo_biopsia_d6_id"] : 0,
          'biologo_tubing_d6_id' => isset($_POST["biologo_tubing_d6_id"]) ? $_POST["biologo_tubing_d6_id"] : 0,
          'fecha_biopsia_d6' => isset($_POST["fecha_biopsia_d6"]) ? $_POST["fecha_biopsia_d6"] : '',
          'lote_medio_d6' => isset($_POST["lote_medio_d6"]) ? $_POST["lote_medio_d6"] : '',
          // datos muestra
          'muestras' => [
            'ovos' => isset($_POST['ovos']) ? $_POST['ovos'] : [],
            'ovo_fres' => isset($_POST['ovo_fres']) ? $_POST['ovo_fres'] : [],
            'ovo_vitri' => isset($_POST['ovo_vitri']) ? $_POST['ovo_vitri'] : [],
            'vitri_d2' => isset($_POST['vitri_d2']) ? $_POST['vitri_d2'] : [],
            'vitri_d3' => isset($_POST['vitri_d3']) ? $_POST['vitri_d3'] : [],
            'blasto_vitri' => isset($_POST['blasto_vitri']) ? $_POST['blasto_vitri'] : [],
            'd3' => isset($_POST['d3']) ? $_POST['d3'] : [],
            'd5' => isset($_POST['d5']) ? $_POST['d5'] : [],
            'd6' => isset($_POST['d6']) ? $_POST['d6'] : [],
            'rebiopsia' => isset($_POST['rebiopsia']) ? $_POST['rebiopsia'] : [],
            'nucleo_visible' => isset($_POST['nucleo_visible']) ? $_POST['nucleo_visible'] : [],
            'tubing' => isset($_POST['tubing']) ? $_POST['tubing'] : [],
            'observaciones' => isset($_POST['observaciones']) ? $_POST['observaciones'] : [],
          ],
          // espacio para igenomix
          'correlativo' => $_POST["correlativo"],
          'recepcionado_por' => $_POST["recepcionado_por"],
          'fecha' => $_POST["fecha"],
          'hora' => $_POST["hora"],
          'motivo_rechazo' => $_POST["motivo_rechazo"],
          'login' => $login,
        );

        guardar_informe($data);
      }

      // 
      $informe = traer_informe($protocolo); ?>
      <form action="" method="post">
        <!-- analisis solicitado -->
        <div class="card mb-3">
          <h5 class="card-header">Análisis solicitado</h5>
          <div class="card-body">
            <div class="row">
              <div class="col pb-2">
                <?php
                  $stmt = $db->prepare("SELECT id, descripcion from igeno_analisis where estado=1");
                  $stmt->execute();

                  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $checked = '';
                    if (in_array($data["id"], $informe["analisis"], true)) $checked = 'checked';

                    print('
                    <div class="form-check col-12 col-sm-12 col-md-12 col-lg-12">
                      <input class="form-check-input" type="checkbox" name="analisis[]" value="'.$data["id"].'" id="analisis_'.$data["id"].'" '.$checked.'>
                      <label class="form-check-label" for="analisis_'.$data["id"].'">'.$data['descripcion'].'</label>
                    </div>');
                  } ?>
              </div>
            </div>
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">¿Solicita MitoScore?</span>
                  <select class="form-control form-control-sm" name="mitoscore_id" id="mitoscore_id" required>
                    <option value="">SELECCIONAR</option>
                    <?php
                      $stmt = $db->prepare("SELECT id, nombre from si_no where estado=1");
                      $stmt->execute();

                      while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = "";
                        if ($informe["sino_mitoscore_id"] == $data["id"]) $selected = "selected";

                        print("<option value='".$data['id']."' ".$selected.">".mb_strtoupper($data['nombre'])."</option>");
                      } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- tarifa solicitada -->
        <div class="card mb-3">
          <h5 class="card-header">Tarifa Solicitada <small>(sólo para PGT-A y PGT-SR translocaciones Robertsonianas)</small></h5>
          <div class="card-body">
            <div class="row pb-2">
              <div class="col">
                <?php
                  $stmt = $db->prepare("SELECT id, descripcion from igeno_tarifa where estado=1");
                  $stmt->execute();

                  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $checked = '';
                    if (in_array($data["id"], $informe["tarifa"], true)) $checked = 'checked';

                    print('
                    <div class="form-check col-12 col-sm-12 col-md-12 col-lg-12">
                      <input class="form-check-input" type="checkbox" name="tarifa[]" value="'.$data["id"].'" id="tarifa_'.$data["id"].'" '.$checked.'>
                      <label class="form-check-label" for="tarifa_'.$data["id"].'">'.$data['descripcion'].'</label>
                    </div>');
                  } ?>
              </div>
            </div>
          </div>
        </div>
        <!-- datos peticionario del estudio -->
        <div class="card mb-3">
          <h5 class="card-header">Datos peticionario del estudio</h5>
          <div class="card-body">
            <div class="row pb-2">
              <!-- clinica -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Clínica*</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_clinica" id="peticionario_clinica" value="<?php print(mb_strtoupper($informe["peticionario_clinica"])); ?>" required>
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- medico -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Médico remitente*</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_medicoremitente" id="peticionario_medicoremitente" value="<?php print(mb_strtoupper($informe["peticionario_medicoremitente"])); ?>" required>
                </div>
              </div>
              <!-- lab manager -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">IVF Lab Manager*</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_labmanager" id="peticionario_labmanager" value="<?php print(mb_strtoupper($informe["peticionario_labmanager"])); ?>">
                </div>
              </div>
            </div>
            <hr>
            <div class="row pb-2">
              <!-- persona contacto -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Persona de contacto</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_personacontacto" id="peticionario_personacontacto" value="<?php print(mb_strtoupper($informe["peticionario_personacontacto"])); ?>">
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- email contacto -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">E-mail o teléfono de contacto</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_mailcontacto" id="peticionario_mailcontacto" value="<?php print(mb_strtolower($informe["peticionario_mailcontacto"])); ?>">
                </div>
              </div>
              <!-- email resultados -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">E-mail para entrega de resultados</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_mailresultados" id="peticionario_mailresultados" value="<?php print(mb_strtolower($informe["peticionario_mailresultados"])); ?>">
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- direccion -->
              <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Dirección</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_direccion" id="peticionario_direccion" value="<?php print(mb_strtoupper($informe["peticionario_direccion"])); ?>">
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- direccion -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Ciudad</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_ciudad" id="peticionario_ciudad" value="<?php print(mb_strtoupper($informe["peticionario_ciudad"])); ?>">
                </div>
              </div>
              <!-- direccion -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Provincia</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_provincia" id="peticionario_provincia" value="<?php print(mb_strtoupper($informe["peticionario_provincia"])); ?>">
                </div>
              </div>
              <!-- direccion -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">C.P.</span>
                  <input type="text" class="form-control form-control-sm" name="peticionario_cp" id="peticionario_cp" value="<?php print(mb_strtoupper($informe["peticionario_cp"])); ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- datos paciente -->
        <div class="card mb-3">
          <h5 class="card-header">Datos paciente</h5>
          <div class="card-body">
            <div class="row pb-2">
              <!-- medico -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">N° de Historia</span>
                  <input type="text" class="form-control form-control-sm" name="paciente_historia" id="paciente_historia" value="<?php print($paciente["dni"]); ?>" required>
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- nombres -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Nombres paciente</span>
                  <input type="text" class="form-control form-control-sm" name="paciente_nombres" id="paciente_nombres" value="<?php print(mb_strtoupper($paciente["nom"])); ?>" required>
                </div>
              </div>
              <!-- apellidos -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Apellidos paciente</span>
                  <input type="text" class="form-control form-control-sm" name="paciente_apellidos" id="paciente_apellidos" value="<?php print(mb_strtoupper($paciente["ape"])); ?>" required>
                </div>
              </div>
              <!-- fecha nacimiento -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">F. Nacimiento</span>
                  <input type="date" class="form-control form-control-sm" name="paciente_fechanacimiento" id="paciente_fechanacimiento" value="<?php print($paciente["fnac"]); ?>" required>
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- mail paciente -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">E-mail paciente</span>
                  <input type="text" class="form-control form-control-sm" name="paciente_mail" id="paciente_mail" value="<?php print(mb_strtoupper($paciente["mai"])); ?>" required>
                </div>
              </div>
              <!-- celular paciente -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">N° celular o teléfono</span>
                  <input type="text" class="form-control form-control-sm" name="paciente_celular" id="paciente_celular" value="<?php print(mb_strtoupper($paciente["tcel"])); ?>" required>
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- nombres -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Nombres pareja</span>
                  <input type="text" class="form-control form-control-sm" name="pareja_nombres" id="pareja_nombres" value="<?php print(mb_strtoupper($pareja["p_nom"])); ?>">
                </div>
              </div>
              <!-- apellidos -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Apellidos pareja</span>
                  <input type="text" class="form-control form-control-sm" name="pareja_apellidos" id="pareja_apellidos" value="<?php print(mb_strtoupper($pareja["p_ape"])); ?>">
                </div>
              </div>
              <!-- fecha nacimiento -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">F. Nacimiento</span>
                  <input type="date" class="form-control form-control-sm" name="pareja_fechanacimiento" id="pareja_fechanacimiento" value="<?php print(mb_strtoupper($pareja["p_fnac"])); ?>">
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- mail pareja -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">E-mail pareja</span>
                  <input type="text" class="form-control form-control-sm" name="pareja_mail" id="pareja_mail" value="<?php print(mb_strtoupper($pareja["p_mai"])); ?>">
                </div>
              </div>
              <!-- celular pareja -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">N° celular o teléfono</span>
                  <input type="text" class="form-control form-control-sm" name="pareja_celular" id="pareja_celular" value="<?php print(mb_strtoupper($pareja["p_tcel"])); ?>">
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- nombres -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Cariotipo paciente</span>
                  <input type="text" class="form-control form-control-sm" name="cariotipo_paciente" id="cariotipo_paciente" value="<?php print(mb_strtoupper($informe["cariotipo_paciente"])); ?>">
                </div>
              </div>
              <!-- apellidos -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Cariotipo pareja</span>
                  <input type="text" class="form-control form-control-sm" name="cariotipo_pareja" id="cariotipo_pareja" value="<?php print(mb_strtoupper($informe["cariotipo_pareja"])); ?>">
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Idioma del informe</span>
                  <select class="form-control form-control-sm" name="idioma_id" id="idioma_id" required>
                    <option value="">SELECCIONAR</option>
                    <?php
                      $stmt = $db->prepare("SELECT id, descripcion from igeno_idioma where estado=1");
                      $stmt->execute();

                      while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = "";
                        if ($informe["igeno_idioma_id"] == $data["id"]) $selected = "selected";

                        print("<option value='".$data['id']."' ".$selected.">".mb_strtoupper($data['descripcion'])."</option>");
                      } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- informacion del ciclo -->
        <div class="card mb-3">
          <h5 class="card-header">Información del ciclo</h5>
          <div class="card-body">
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Origen de ovocito</span>
                  <div class="col">
                    <?php
                      $stmt = $db->prepare("SELECT id, descripcion from igeno_origenovocito where estado=1");
                      $stmt->execute();

                      while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $checked = '';
                        if (in_array($data["id"], $informe["origenovocito"], true)) $checked = 'checked';

                        print('
                        <div class="form-check col-12 col-sm-12 col-md-12 col-lg-12">
                          <input class="form-check-input" type="checkbox" name="origenovocito[]" value="'.$data["id"].'" id="origenovocito_'.$data["id"].'" '.$checked.'>
                          <label class="form-check-label" for="origenovocito_'.$data["id"].'">'.$data['descripcion'].'</label>
                        </div>');
                      } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Fecha extracción de óvulos</span>
                  <input type="date" class="form-control form-control-sm" name="fecha_extraccionovulos" id="fecha_extraccionovulos" value="<?php print($informe["fecha_extraccionovulos"]); ?>" required>
                </div>
              </div>
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Óvulos fecundados</span>
                  <input type="text" class="form-control form-control-sm" name="ovulos_fecundados" id="ovulos_fecundados" value="<?php print($informe["ovulos_fecundados"]); ?>" required>
                </div>
              </div>
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Embriones biopsiados</span>
                  <input type="text" class="form-control form-control-sm" name="embriones_biopsiados" id="embriones_biopsiados" value="<?php print($informe["embriones_biopsiados"]); ?>" required>
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Método de fecundación</span>
                  <select class="form-control form-control-sm" name="metodo_fecundacion_id" id="metodo_fecundacion_id" required>
                    <option value="">SELECCIONAR</option>
                    <?php
                      $stmt = $db->prepare("SELECT id, descripcion from igeno_metodofecundacion where estado=1");
                      $stmt->execute();

                      while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = "";
                        if ($informe["igeno_metodofecundacion_id"] == $data["id"]) $selected = "selected";
                        print("<option value='".$data['id']."' ".$selected.">".mb_strtoupper($data['descripcion'])."</option>");
                      } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- fecha -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Fecha T. embrionaria</span>
                  <input type="date" class="form-control form-control-sm" name="fecha_transferencia" id="fecha_transferencia" value="<?php print($informe["fecha_transferencia"]); ?>">
                </div>
              </div>
              <!-- hora -->
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Hora T. embrionaria</span>
                  <input type="text" class="form-control form-control-sm" name="hora_transferencia" id="hora_transferencia" value="<?php print($informe["hora_transferencia"]); ?>">
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Transferencia embrionaria</span>
                  <div class="col">
                    <?php
                      $stmt = $db->prepare("SELECT id, descripcion from igeno_tipotransferencia where estado=1");
                      $stmt->execute();

                      while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $checked = '';
                        if (in_array($data["id"], $informe["tipotransferencia"], true)) $checked = 'checked';

                        print('
                        <div class="form-check col-12 col-sm-12 col-md-12 col-lg-12">
                          <input class="form-check-input" type="checkbox" name="tipotransferencia[]" value="'.$data["id"].'" id="tipotransferencia_'.$data["id"].'" '.$checked.'>
                          <label class="form-check-label" for="tipotransferencia_'.$data["id"].'">'.$data['descripcion'].'</label>
                        </div>');
                      } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Tipo de biopsia</span>
                  <div class="col">
                    <?php
                      $stmt = $db->prepare("SELECT id, descripcion from igeno_tipobiopsia where estado=1");
                      $stmt->execute();

                      while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $checked = '';
                        if (in_array($data["id"], $informe["tipobiopsia"], true)) $checked = 'checked';

                        print('
                        <div class="form-check col-12 col-sm-12 col-md-12 col-lg-12">
                          <input class="form-check-input" type="checkbox" name="tipobiopsia[]" value="'.$data["id"].'" id="tipobiopsia_'.$data["id"].'" '.$checked.'>
                          <label class="form-check-label" for="tipobiopsia_'.$data["id"].'">'.$data['descripcion'].'</label>
                        </div>');
                      } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Fecha prevista de biopsia</span>
                  <input type="date" class="form-control form-control-sm" name="fecha_previstabiopsia" id="fecha_previstabiopsia" value="<?php print($informe["fecha_previstabiopsia"]); ?>" required>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- autorizacion del medico -->
        <div class="card mb-3">
          <h5 class="card-header">Autorización del médico</h5>
          <div class="card-body">
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Fecha</span>
                  <input type="date" class="form-control form-control-sm" name="fecha_autorizacion" id="fecha_autorizacion" value="<?php print($informe["fecha_autorizacion"]); ?>" required>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- hc -->
        <div class="card mb-3">
          <h5 class="card-header">Historia Clínica</h5>
          <div class="card-body">
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">NHC</span>
                  <input type="text" class="form-control form-control-sm" id="historia_clinica" value="<?php print(mb_strtoupper($paciente["dni"])); ?>">
                </div>
              </div>
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Nombres y apellidos</span>
                  <input type="text" class="form-control form-control-sm" id="nombres_apellidos" value="<?php print(mb_strtoupper($paciente["nom"]." ".$paciente["ape"])); ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- indicaciones -->
        <div class="card mb-3">
          <h5 class="card-header">Indicaciones</h5>
          <div class="card-body">
            <fieldset class="border p-2">
              <legend  class="w-auto"><h5>PGT-A</h5></legend>
              <div class="row pb-2">
                <div class="col">
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="edad_materna" value="1" id="edad_materna" <?php $informe["pgta_edadmaterna"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="edad_materna">Edad materna avanzada</label>
                    </div>
                  </div>
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="gestacion" value="1" id="gestacion" <?php $informe["pgta_gestacion"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="gestacion">Gestación aneuploide previa</label>
                    </div>
                  </div>
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="fish" value="1" id="fish" <?php $informe["pgta_fish"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="fish">FISH anormal de espermatozoids</label>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="fallo_implantacion" value="1" id="fallo_implantacion" <?php $informe["pgta_falloimplantacion"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="fallo_implantacion">Fallo de implantación (n° fallos)</label>
                    </div>
                  </div>
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="factor_masculino" value="1" id="factor_masculino" <?php $informe["pgta_factormasculino"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="factor_masculino">Factor masculino</label>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="aborto" value="1" id="aborto" <?php $informe["pgta_aborto"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="aborto">Aborto recurrente (n° abortos)</label>
                    </div>
                  </div>
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="enfermedad_sexo" value="1" id="enfermedad_sexo" <?php $informe["pgta_enfermedadsexo"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="enfermedad_sexo">Enfermedad ligada al sexo</label>
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset class="border p-2">
              <legend  class="w-auto"><h5>PGT-SR</h5></legend>
              <div class="row pb-2">
                <div class="col">
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="translocacion" value="1" id="translocacion" <?php $informe["pgtsr_translocacion"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="translocacion">Translocación</label>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="inversion" value="1" id="inversion" <?php $informe["pgtsr_inversion"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="inversion">Inversión</label>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <input class="form-check-input" type="checkbox" name="anomalia_numerica" value="1" id="anomalia_numerica" <?php $informe["pgtsr_anomalianumerica"] == 1 ? print("checked") : print(""); ?>>
                      <label class="form-check-label" for="anomalia_numerica">Anomalía numérica</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row pb-2">
                <div class="col">
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Fórmula</span>
                      <input type="text" class="form-control form-control-sm" name="formula" id="formula" value="<?php print(mb_strtoupper($informe["pgtsr_formula"])); ?>">
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset class="border p-2">
              <legend  class="w-auto"><h5>PGT-M</h5></legend>
              <div class="row pb-2">
                <div class="col">
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Enfermedades monogénicas</span>
                      <input type="text" class="form-control form-control-sm" name="enfermedades" id="enfermedades" value="<?php print(mb_strtoupper($informe["pgtm_enfermedades"])); ?>">
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset class="border p-2">
              <legend  class="w-auto"><h5>PGT-A, PGT-SR, PGT-M</h5></legend>
              <div class="row pb-2">
                <div class="col">
                  <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Otras indicaciones</span>
                      <input type="text" class="form-control form-control-sm" name="otras_indicaciones" id="otras_indicaciones" value="<?php print(mb_strtoupper($informe["pgt_otrasindicaciones"])); ?>">
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
          </div>
        </div>
        <!-- datos biopsia -->
        <div class="card mb-3">
          <h5 class="card-header">Información de la biopsia</h5>
          <div class="card-body">
            <fieldset class="border p-2">
              <legend  class="w-auto"><h5>Biopsia Día 5</h5></legend>
              <!-- informacion biopsia dia 5 -->
              <div class="row pb-2">
                <!-- biopsia -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Biopsia realizada por*</span>
                    <select class="form-control form-control-sm" name="biologo_biopsia_id" id="realizado_por" required>
                      <option value="">SELECCIONAR</option>
                      <?php
                        $stmt = $db->prepare("SELECT id, nom nombre from lab_user where sta=0");
                        $stmt->execute();

                        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $selected = "";
                          if ($informe["biologo_biopsia_id"] == $data["id"]) $selected = "selected";

                          print("<option value='".$data['id']."' ".$selected.">".mb_strtoupper($data['nombre'])."</option>");
                        } ?>
                    </select>
                  </div>
                </div>
                <!-- fecha biopsia -->
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Fecha biopsia*</span>
                    <input type="date" class="form-control form-control-sm" name="fecha_biopsia" id="fecha_biopsia" value="<?php print($informe["fecha_biopsia"]); ?>" required>
                  </div>
                </div>
              </div>
              <div class="row pb-2">
                <!-- tubing -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Tubing realizado por*</span>
                    <select class="form-control form-control-sm" name="biologo_tubing_id" id="biologo_tubing_id" required>
                      <option value="">SELECCIONAR</option>
                      <?php
                        $stmt = $db->prepare("SELECT id, nom nombre from lab_user where sta=0");
                        $stmt->execute();

                        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $selected = "";
                          if ($informe["biologo_tubing_id"] == $data["id"]) $selected = "selected";

                          print("<option value='".$data['id']."' ".$selected.">".mb_strtoupper($data['nombre'])."</option>");
                        } ?>
                  </select>
                  </div>
                </div>
                <!-- lote -->
                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Lote medio washing/ loading</span>
                    <input type="text" class="form-control form-control-sm" name="lote_medio" id="lote_medio" value="<?php print(mb_strtoupper($informe["lote_medio"])); ?>">
                  </div>
                </div>
              </div>
            </fieldset>
            <?php
            if(verificar_dia6($protocolo)) { ?>
              <fieldset class="border p-2">
                <legend  class="w-auto"><h5>Biopsia Día 6</h5></legend>
                <!-- informacion biopsia dia 6 -->
                <div class="row pb-2">
                  <!-- biopsia -->
                  <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Biopsia realizada por*</span>
                      <select class="form-control form-control-sm" name="biologo_biopsia_d6_id" id="biologo_biopsia_d6_id" required>
                        <option value="">SELECCIONAR</option>
                        <?php
                          $stmt = $db->prepare("SELECT id, nom nombre from lab_user where sta=0");
                          $stmt->execute();

                          while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = "";
                            if ($informe["biologo_biopsia_d6_id"] == $data["id"]) $selected = "selected";

                            print("<option value='".$data['id']."' ".$selected.">".mb_strtoupper($data['nombre'])."</option>");
                          } ?>
                      </select>
                    </div>
                  </div>
                  <!-- fecha biopsia -->
                  <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Fecha biopsia*</span>
                      <input type="date" class="form-control form-control-sm" name="fecha_biopsia_d6" id="fecha_biopsia_d6" value="<?php print($informe["fecha_biopsia_d6"]); ?>" required>
                    </div>
                  </div>
                </div>
                <div class="row pb-2">
                  <!-- tubing -->
                  <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Tubing realizado por*</span>
                      <select class="form-control form-control-sm" name="biologo_tubing_d6_id" id="biologo_tubing_d6_id" required>
                        <option value="">SELECCIONAR</option>
                        <?php
                          $stmt = $db->prepare("SELECT id, nom nombre from lab_user where sta=0");
                          $stmt->execute();

                          while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = "";
                            if ($informe["biologo_tubing_d6_id"] == $data["id"]) $selected = "selected";

                            print("<option value='".$data['id']."' ".$selected.">".mb_strtoupper($data['nombre'])."</option>");
                          } ?>
                    </select>
                    </div>
                  </div>
                  <!-- lote -->
                  <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Lote medio washing/ loading</span>
                      <input type="text" class="form-control form-control-sm" name="lote_medio_d6" id="lote_medio_d6" value="<?php print(mb_strtoupper($informe["lote_medio_d6"])); ?>">
                    </div>
                  </div>
                </div>
              </fieldset>
            <?php }
            ?>
          </div>
        </div>
        <!-- datos muestra -->
        <div class="card mb-3">
          <h5 class="card-header">Datos muestras</h5>
          <div class="card-body">
            <table class="table table-responsive table-bordered align-middle">
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
              </thead>
              <tbody>
              <?php
                $stmt = $db->prepare("SELECT
                  a.ovo, a.d5cel celula, a.d5mci mci, a.d5tro tro, 5 dia, coalesce(c.nombre, '') observacion
                  from lab_aspira_dias a
                  inner join lab_aspira b on b.pro = a.pro and b.estado is true
                  left join lab_aspira_dias_observacion_biopsia c on c.idrepro = b.rep and c.ovo = a.ovo and c.estado = 1
                  where a.analizar = 1 and a.pro=? and a.d5cel <> '' and a.d5cel <> 'Bloq' and a.d5f_cic = 'C' and (a.d5d_bio<>0) and a.estado is true
                  union
                  select
                  a.ovo, a.d6cel celula, a.d6mci mci, a.d6tro tro, 6 dia, coalesce(c.nombre, '') observacion
                  from lab_aspira_dias a
                  inner join lab_aspira b on b.pro = a.pro and b.estado is true
                  left join lab_aspira_dias_observacion_biopsia c on c.idrepro = b.rep and c.ovo = a.ovo and c.estado = 1
                  where a.analizar = 1 and a.pro=? and a.d6cel <> '' and a.d6cel <> 'Bloq' and a.d6f_cic = 'C' and (a.d6d_bio<>0) and a.estado is true");
                $stmt->execute([$protocolo, $protocolo]);

                if ($stmt->rowCount() > 0) {
                  $observaciones='';
                  $html = "";
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

                    if (in_array($data['ovo'], $informe["muestras"]["ovo_fres"], true)) $ovo_fres = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["ovo_vitri"], true)) $ovo_vitri = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["vitri_d2"], true)) $vitri_d2 = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["vitri_d3"], true)) $vitri_d3 = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["blasto_vitri"], true)) $blasto_vitri = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["d3"], true)) $d3 = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["d5"], true)) $d5 = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["d6"], true)) $d6 = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["rebiopsia"], true)) $rebiopsia = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["nucleo_visible"], true)) $nucleo_visible = 'checked';
                    if (in_array($data['ovo'], $informe["muestras"]["tubing"], true)) $tubing = 'checked';

                    $nombres = "";
                    $observaciones=mb_strtoupper($data['observacion']);
                    $observaciones='<input type="text" name="observaciones'.$data['ovo'].'" value="'.mb_strtoupper($data['observacion']).'">';
    
                    $html.='
                    <tr>
                      <td class="vertical-center text-center">'.$paciente_iniciales.'</td>
                      <td class="vertical-center text-center">'.$nombres.$data['ovo'].'</td>
                      <td class="vertical-center text-center">'.mb_strtoupper($data['celula'])." ".mb_strtoupper($data['mci']).mb_strtoupper($data['tro']).'</td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="ovo_fres[]" '.$ovo_fres.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="ovo_vitri[]" '.$ovo_vitri.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="vitri_d2[]" '.$vitri_d2.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="vitri_d3[]" '.$vitri_d3.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="blasto_vitri[]" '.$blasto_vitri.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="d3[]" '.$d3.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="d5[]" '.$d5.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="d6[]" '.$d6.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="rebiopsia[]" '.$rebiopsia.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="nucleo_visible[]" '.$nucleo_visible.'></td>
                      <td class="vertical-center"><input type="checkbox" class="form-control" value="'.$data['ovo'].'" name="tubing[]" '.$tubing.'></td>
                      <td class="vertical-center"><input type="text" class="form-control" value="'.(count($informe["muestras"]["observaciones"]) != 0 ? $informe["muestras"]["observaciones"][$index] : '').'" name="observaciones[]"></td>
                      <input type="hidden" value="'.$data['ovo'].'" name="ovos[]">
                    </tr>';

                    $index++;
                  }

                  print($html);
                }
              ?>
              </tbody>
            </table>
          </div>
        </div>
        <!-- espacio igenomix -->
        <div class="card mb-3">
          <h5 class="card-header">Espacio reservado para IGENOMIX</h5>
          <div class="card-body">
            <div class="row pb-2">
              <!-- correlativo -->
              <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Correlativo</span>
                  <input type="text" class="form-control form-control-sm" name="correlativo" id="correlativo" value="<?php print($informe["correlativo"]); ?>">
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- medico -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Recepcionado por</span>
                  <input type="text" class="form-control form-control-sm" name="recepcionado_por" id="recepcionado_por" value="<?php print($informe["recepcionado_por"]); ?>">
                </div>
              </div>
              <!-- fecha -->
              <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Fecha</span>
                  <input type="date" class="form-control form-control-sm" name="fecha" id="fecha" value="<?php print($informe["fecha"]); ?>">
                </div>
              </div>
              <!-- hora -->
              <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Hora</span>
                  <input type="text" class="form-control form-control-sm" name="hora" id="hora" value="<?php print($informe["hora"]); ?>">
                </div>
              </div>
            </div>
            <div class="row pb-2">
              <!-- muestra -->
              <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Muestra aceptada/ rechazada (indicar motivo en caso de rechazo)</span>
                  <textarea name="motivo_rechazo" id="motivo_rechazo" class="md-textarea form-control" rows="10"><?php print($informe["motivo_rechazo"]); ?></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="text-right">
          <?php print('<a href="info_igenomix.php?pro='.$protocolo.'" target="_blank"><img src="_images/pdf.png" height="20" width="20" alt="icon name"></a>&nbsp&nbsp'); ?>
          <button type="submit" name="guardar" class="btn btn-danger">Guardar Informe</button>
        </div>
      </form>
  </div>
  <footer class="footer">
    <div class="container">
      <span class="text-muted"></span>
    </div>
  </footer>
  <script src="js/jquery-1.11.1.min.js"></script>
  <script src="js/igeno_informe.js?v=191230"></script>
  <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script src="js/chosen.jquery.min.js"></script>
  <script src="js/jquery.timepicker.min.js"></script>
</body>
</html>