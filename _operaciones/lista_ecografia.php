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
    case 'visualizar': visualizar($_POST); break;
    
    default: exit(); break;
  }

  function visualizar($data) {
    // var_dump($data); exit();
    global $db;

    if (isset($data["buscar"]["fecha_ini"]) and isset($data["buscar"]["fecha_fin"])) {
      $stmt = $db->prepare("SELECT a.*, coalesce(ma.nombre_base, '-') nombre_base, coalesce(ma.nombre_original, '-') nombre_original
        FROM hc_analisis a
        left join man_archivo ma on ma.id = a.archivo_id
        where a.estado = 1 and a.lab = ? and (a.a_mue between ? and ?)
        order by  a.a_mue desc;"
      );

      $stmt->execute(["eco", $data["buscar"]["fecha_ini"], $data["buscar"]["fecha_fin"]]);
    } else {
      $stmt = $db->prepare("SELECT a.*, coalesce(ma.nombre_base, '-') nombre_base, coalesce(ma.nombre_original, '-') nombre_original
        FROM hc_analisis a
        left join man_archivo ma on ma.id = a.archivo_id
        where a.estado = 1 and a.lab = ? and (a.a_nom ilike ? or a.a_med ilike ?)
        order by  a.a_mue desc;"
      );

      $stmt->execute(["eco", ("%" . $data["buscar"] . "%"), ("%" . $data["buscar"] . "%")]);
    }

    while ($anal = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $analisis = '';
      $video = '';
      $link_video = '';
      $stmt1 = $db->prepare("SELECT * from google_drive_response where drive_id <> '0' and estado = 1 and tipo_procedimiento_id = 2 and procedimiento_id = ? order by id desc limit 1 offset 0;");
      $stmt1->execute([$anal['id']]);
      if ($stmt1->rowCount() > 0) {
          $data1 = $stmt1->fetch(PDO::FETCH_ASSOC);
          $link_video = "<a href='https://drive.google.com/open?id=" . $data1['drive_id'] . "' target='new'>Vídeo</a> - ";
      }

      if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/analisis/' . $anal['id'] . '_' . $anal['a_dni'] . '.pdf')) {
        $analisis = '<a href="archivos_hcpacientes.php?idArchivo=' . $anal['id'] . '_' . $anal['a_dni'] . '" target="new">Informe</a> - ';
      }

      /* if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/storage/analisis_archivo/' . $anal['nombre_base'])) {
          $video = '<a href="storage/analisis_archivo/' . $anal['nombre_base'] . '" target="new">Vídeo</a> - ';
      } */

      print('<tr>
        <th><a href="e_analisis.php?path=lista_ecografia&id=' . $anal['id'] . '" rel="external">' . mb_strtoupper($anal['a_exa']) . '</a></th>
        <td><a href="e_paci_mail.php?path=lista_ecografia&id=' . $anal['a_dni'] . '" rel="external">' . mb_strtoupper($anal['a_nom']) . '</a></td>
        <td>' . mb_strtoupper($anal['a_med']) . '</td>
        <th>' . $link_video . $video . $analisis . '<a href="javascript:eliminarAnalisis(' . $anal['id'] . ', ' . $anal['a_dni'] . ');">Eliminar</a></th>
        <td>' . date("d-m-Y", strtotime($anal['a_mue'])) . '</td>
      </tr>');
    }

    http_response_code(200);
  }
?>