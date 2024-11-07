<?php
  ini_set("display_errors","1");
  error_reporting(E_ALL);
  session_start();

  $login = "";

  if (!!$_SESSION) {
    $login = $_SESSION['login'];
  } else {
    http_response_code(400);
    echo json_encode(array("message" => "no se ha iniciado sesión"));
    exit();
  }

  require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
  $tipo_operacion = "";

  if (isset($_POST["tipo_operacion"]) && !empty($_POST["tipo_operacion"])) {
    $tipo_operacion = $_POST["tipo_operacion"];
  } else {
    http_response_code(400);
    echo json_encode(array("message" => "no se ingresó el tipo de operación"));
    exit();
  }

  switch ($tipo_operacion) {
    case 'visualizar_analisisgenomics':
      visualizar_analisisgenomics($_POST);
      break;
    case 'visualizar_ngsgenomics':
      visualizar_ngsgenomics($_POST);
      break;
    case 'visualizar_analisisera':
      visualizar_analisisera($_POST);
      break;
    
    default: exit(); break;
  }

  function visualizar_analisisgenomics($data)
  {
    global $db;
    $stmt = $db->prepare("SELECT * FROM hc_analisis WHERE estado = 1 and lab = ? AND (a_nom ILIKE ? OR a_med ILIKE ?) AND a_exa <> ? ORDER BY a_mue DESC;");
    $stmt->execute(["genomics", ("%".$data["buscar"]."%"), ("%".$data["buscar"]."%"), "ERA"]);

    while ($anal = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $borrar_ngs = "";

      if ($anal['a_exa'] == 'NGS') {
        $borrar_ngs = " - <a href='javascript:borrarNGS(".$anal['id'].", ".$anal['a_dni'].");'>Eliminar</a>";
      }

      print('<tr>
        <th><a href="e_analisis.php?path=lista_genomics&id='.$anal['id'].'" rel="external">'.mb_strtoupper($anal['a_exa']).'</a></th>
        <td>'.mb_strtoupper($anal['a_nom']).'</td>
        <td>'.mb_strtoupper($anal['a_med']).'</td>
        <td>'.mb_strtoupper($anal['a_sta']).'</td>
        <th><a href="archivos_hcpacientes.php?idArchivo=' . $anal['id'] . '_' . $anal['a_dni'] . '" target="new">Ver/Descargar</a>'.$borrar_ngs.'</th>
        <td>'.date("d-m-Y", strtotime($anal['a_mue'])).'</td></tr>');
    }

    http_response_code(200);
  }

  function visualizar_ngsgenomics($data)
  {
    global $db;
    $stmt1 = $db->prepare("SELECT
        hc_paciente.dni, ape, nom, hc_reprod.med, lab_aspira.pro, lab_aspira.f_fin
        FROM hc_paciente, lab_aspira, hc_reprod
        WHERE hc_reprod.estado = true and lab_aspira.estado is true and hc_paciente.dni = lab_aspira.dni AND hc_reprod.id = lab_aspira.rep AND lab_aspira.f_fin <> '1899-12-30' AND lab_aspira.tip <> 'T' AND hc_reprod.pago_extras ILIKE '%NGS%' AND lab_aspira.dias >= 5
        AND (ape ILIKE ? OR nom ILIKE ? OR hc_reprod.med ILIKE ?)
        ORDER BY lab_aspira.f_fin DESC;");
    $stmt1->execute([("%".$data["buscar"]."%"), ("%".$data["buscar"]."%"), ("%".$data["buscar"]."%")]);

    while ($ngs = $stmt1->fetch(PDO::FETCH_ASSOC)) {
      $stmt = $db->prepare("SELECT ngs1
        FROM lab_aspira_dias
        WHERE pro=? and estado is true AND ((d5d_bio<>0 AND d5f_cic='C') OR (d6d_bio<>0 AND d6f_cic='C'))");
      $stmt->execute(array($ngs['pro']));

      if ($stmt->rowCount() > 0) {
        if (file_exists("../analisis/ngs_".$ngs['pro'].".pdf")) {
          $res = 'NEGATIVO';

          while ($ovo = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($ovo['ngs1'] == 1) {
              $res = 'POSITIVO';
              break;
            }
          }

          $pdf = '<a href="archivos_hcpacientes.php?idArchivo=ngs_'.$ngs['pro'].'" target="new">Ver/Descargar</a>';
        } else {
          $res = '-';
          $pdf = 'PENDIENTE';
        }

        print("<tr>
          <td>".$ngs['pro']."</td>
          <td><a href='e_ngs.php?path=lista_genomics&id=".$ngs['pro']."' rel='external'>".date("d-m-Y", strtotime($ngs['f_fin']))."</a></td>
          <td>".mb_strtoupper($ngs['ape'].' '.$ngs['nom'])."</td>
          <td>".mb_strtoupper($ngs['med'])."</td>
          <th>".$pdf."</th>
          <th>".$res."</th>
        </tr>");
      }
    }

    http_response_code(200);
  }

  function visualizar_analisisera($data)
  {
    global $db;
    $stmt = $db->prepare("SELECT * FROM hc_analisis WHERE estado = 1 and lab = ? AND (a_nom ILIKE ? OR a_med ILIKE ?) AND a_exa = ? ORDER BY a_mue DESC;");
    $stmt->execute(["genomics", ("%".$data["buscar"]."%"), ("%".$data["buscar"]."%"), "ERA"]);

    while ($anal = $stmt->fetch(PDO::FETCH_ASSOC)) {
      print('<tr>
        <th><a href="e_analisis.php?path=lista_genomics&id='.$anal['id'].'" rel="external">'.mb_strtoupper($anal['a_exa']).'</a></th>
        <td>'.mb_strtoupper($anal['a_nom']).'</td>
        <td>'.mb_strtoupper($anal['a_med']).'</td>
        <td>'.mb_strtoupper($anal['a_sta']).'</td>
        <th><a href="archivos_hcpacientes.php?idArchivo=' . $anal['id'] . '_' . $anal['a_dni'] . '" target="new">Ver/Descargar</a></th>
        <td>'.date("d-m-Y", strtotime($anal['a_mue'])).'</td></tr>');
    }

    http_response_code(200);
  }
?>