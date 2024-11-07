<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"]."/_database/db_tools.php");
ini_set("display_errors","1");
error_reporting(E_ALL);
require dirname(__FILE__).'/utils.php';

if (!isset($_GET['start']) || !isset($_GET['end'])) {
    die("Please provide a date range.");
}

$range_start = $_GET['start'];
$range_start_ini = parseDateTime($_GET['start']);
$range_end = $_GET['end'];
$range_end_ini = parseDateTime($_GET['end']);
$timezone = null;

if (isset($_GET['timezone'])) {
    $timezone = new DateTimeZone($_GET['timezone']);
}

if ($_GET['med'] <> '') {
    $login = $_GET['med'];
    $rReprox = $db->prepare("SELECT
        b.nom, b.ape, b.dni, a.med, a.des_dia, a.des_don, a.f_iny, a.h_iny, a.f_asp
        , c.nombre turno, to_char((a.f_asp::timestamp  + (split_part(c.formato_hora_minuto, ':', 1)::int * interval '1 hour') + (split_part(c.formato_hora_minuto, ':', 2)::int * interval '1 minute')), 'HH24:MI') AS horafin
        , mmc.abreviatura programa
        from hc_reprod a
        inner join hc_paciente b on b.dni = a.dni
        LEFT JOIN man_medios_comunicacion mmc on mmc.id = a.programaid
        left join man_turno_reproduccion c on c.codigo = a.idturno and c.estado = 1
        where a.estado = true and coalesce(a.cancela, 0) = 0 and (a.p_od='' or a.p_od is null) and a.f_asp<>'' and a.des_dia is null and a.med = ? and a.f_asp between ? and ?
        union
        select
        b.nom, b.ape, b.dni, a.med, a.des_dia, a.des_don, a.f_iny, a.h_iny, a.f_asp
        , c.nombre turno, to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI') as horafin
        , mmc.abreviatura programa
        from hc_reprod a
        inner join hc_paciente b on b.dni = a.dni
        LEFT JOIN man_medios_comunicacion mmc on mmc.id = a.programaid
        left join man_turno_reproduccion c on c.codigo = a.idturno and c.estado = 1
        where a.estado = true and coalesce(a.cancela, 0) = 0 and (a.p_od='' or a.p_od is null) and a.f_asp<>'' and a.des_dia is not null and a.med = ? and a.f_iny between ? and ? and a.h_iny <> ''"
    );
    $rReprox->execute(array($login, $range_start, $range_end, $login, $range_start, $range_end));

    $rReproTra = $db->prepare("SELECT
        nom, ape, hc_paciente.dni, hc_reprod.med, f_tra, h_tra
        FROM hc_reprod, hc_paciente
        WHERE hc_reprod.estado = true and coalesce(hc_reprod.cancela, 0) = 0 and hc_paciente.dni=hc_reprod.dni and hc_reprod.med=? and hc_reprod.h_tra<>'' and hc_reprod.f_tra between ? and ?
        ORDER BY hc_reprod.id DESC"
    );
    $rReproTra->execute(array($login, $range_start, $range_end));

    $rInter_Gine = $db->prepare("SELECT
        b.nom, b.ape, b.dni, a.med, a.in_t, a.in_f2, a.in_h2, a.in_m2
        , c.nombre turno, ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval) as horafin, mmc.abreviatura programa
        from hc_gineco a
        inner join hc_paciente b on b.dni=a.dni
        LEFT JOIN man_medios_comunicacion mmc on mmc.id = a.programaid
        left join man_turno_reproduccion c on c.codigo = a.idturno_inter and c.estado = 1
        where a.cancela = 0 and a.in_h2<>'' and a.in_m2<>'' and a.in_c=1 and a.med=? and a.in_f2 between ? and ?
        order by a.id desc"
    );
    $rInter_Gine->execute(array($login, $range_start, $range_end));
} else {
    $login = $_SESSION['login'];
    $role = $_SESSION['role'];

    $rReprox = $db->prepare("SELECT
        b.nom, b.ape, b.dni, a.med, a.des_dia, a.des_don, a.f_iny, a.h_iny, a.f_asp,
        c.nombre AS turno,
        to_char((a.f_asp::timestamp + (split_part(c.formato_hora_minuto, ':', 1)::int * interval '1 hour') + (split_part(c.formato_hora_minuto, ':', 2)::int * interval '1 minute')), 'HH24:MI') AS horafin
        , mmc.abreviatura programa
        FROM hc_reprod a
        INNER JOIN hc_paciente b ON b.dni = a.dni
        LEFT JOIN man_medios_comunicacion mmc on mmc.id = a.programaid
        LEFT JOIN man_turno_reproduccion c ON c.codigo = a.idturno AND c.estado = 1
        WHERE a.estado = true and coalesce(a.cancela, 0) = 0 and  (a.p_od='' OR a.p_od IS NULL) AND a.f_asp<>'' AND a.des_dia IS NULL AND a.f_asp BETWEEN ? AND ?
        UNION
        SELECT
        b.nom, b.ape, b.dni, a.med, a.des_dia, a.des_don, a.f_iny, a.h_iny, a.f_asp,
        c.nombre AS turno,
        to_char((a.f_asp::timestamp + (split_part(c.formato_hora_minuto, ':', 1)::int * interval '1 hour') + (split_part(c.formato_hora_minuto, ':', 2)::int * interval '1 minute')), 'HH24:MI') AS horafin
        , mmc.abreviatura programa
        FROM hc_reprod a
        INNER JOIN hc_paciente b ON b.dni = a.dni
        LEFT JOIN man_medios_comunicacion mmc on mmc.id = a.programaid
        LEFT JOIN man_turno_reproduccion c ON c.codigo = a.idturno AND c.estado = 1
        WHERE a.estado = true and coalesce(a.cancela, 0) = 0 and (a.p_od='' OR a.p_od IS NULL) AND a.f_asp<>'' AND a.des_dia IS NOT NULL AND a.f_iny BETWEEN ? AND ?"
    );
    $rReprox->execute(array($range_start, $range_end, $range_start, $range_end));
    
    $rReproTra = $db->prepare("SELECT
        nom, ape, hc_paciente.dni, hc_reprod.med, f_tra, h_tra
        FROM hc_reprod, hc_paciente
        WHERE hc_reprod.estado = true and coalesce(hc_reprod.cancela, 0) = 0 and hc_paciente.dni=hc_reprod.dni and hc_reprod.h_tra<>'' and hc_reprod.f_tra between ? and ?
        ORDER BY hc_reprod.id DESC"
    );
    $rReproTra->execute(array($range_start, $range_end));

    $rInter_Gine = $db->prepare("SELECT
        b.nom, b.ape, b.dni, a.med, a.in_t, a.in_f2, a.in_h2, a.in_m2
        , c.nombre as turno, to_char((CONCAT(a.in_h2, ':', a.in_m2, ':00')::time + c.formato_hora_minuto::interval), 'HH24:MI') as horafin, mmc.abreviatura programa
        FROM hc_gineco a
        LEFT JOIN man_medios_comunicacion mmc on mmc.id = a.programaid
        INNER JOIN hc_paciente b ON b.dni=a.dni
        LEFT JOIN man_turno_reproduccion c ON c.codigo = a.idturno_inter and c.estado = 1
        WHERE a.cancela = 0 and a.in_h2 <> '' and a.in_m2 <> '' and a.in_c = 1 and a.in_f2 between ? and ?
        ORDER BY a.id desc"
    );
    $rInter_Gine->execute(array($range_start, $range_end));

    $rInter_Uro = $db->prepare("SELECT
        b.p_nom, b.p_ape, b.p_dni, a.med, a.in_t, a.in_f2, a.in_h2, a.in_m2
        , c.nombre as turno, to_char((a.in_h2::int || ':' || a.in_m2::int)::time + (c.formato_hora_minuto || ' minute')::interval, 'HH24:MI') as horafin
        from hc_urolo a
        inner join hc_pareja b on b.p_dni = a.p_dni
        left join man_turno_reproduccion c on c.codigo = a.idturno_inter and c.estado = 1
        where a.in_h2 <> '' and a.in_m2 <> '' and a.in_f2 between ? and ?
        order by a.id desc"
    );
    $rInter_Uro->execute(array($range_start, $range_end));

    $consulta_bloqueo = $db->prepare("SELECT
        b.fecha, h.nombre as horainicio, substring(h.nombre, 1, 2) as hora, substring(h.nombre, 4, 2) as minuto, to_char((substring(h.nombre, 1, 2)::int || ':' || substring(h.nombre, 4, 2)::int)::time + (t.formato_hora_minuto || ' minute')::interval, 'HH24:MI') as horafin
        from lab_agenda_bloqueo b
        inner join man_hora h on h.id = b.idhora and h.estado = 1
        inner join man_turno_reproduccion t on t.id = b.idturno and t.estado = 1
        where b.fecha between ? and ? and b.estado=1"
    );    
    $consulta_bloqueo->execute(array($range_start, $range_end));
}

$rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
$rUser->execute(array($login));
$user = $rUser->fetch(PDO::FETCH_ASSOC);
$role = $_SESSION['role'];

if ($role <> '2') {
    if ($user['role']==1 ||  $user['role']==8) {
        $rObstex = $db->prepare("SELECT
            nom, ape, hc_paciente.dni, in_t, in_f2, in_h2, in_m2, con_fec, con_fec_h, con_fec_m
            FROM hc_obste, hc_paciente
            WHERE hc_paciente.dni=hc_obste.dni AND hc_obste.med=? AND hc_obste.con_fec_h<>'' AND hc_obste.con_fec_h<>'||||||||||||'"
        );

        $rObstex->execute(array($login));

        $rGinex = $db->prepare("SELECT
            nom, ape, hp.dni, hg.fec, fec_h, fec_m, in_c, in_t, in_f2, in_h2, in_m2, cupon, hg.estadoconsulta_ginecologia_id, hg.tipoconsulta_ginecologia_id, hg.id, mgm.nombre as motivo_consulta
            FROM hc_gineco hg
            inner join hc_paciente hp on hp.dni = hg.dni
            inner join man_gine_motivoconsulta mgm on mgm.id = hg.man_motivoconsulta_id
            where hg.cancela = 0 and hg.med = ? AND hg.in_c <> 1 and hg.in_f2 IS NOT NULL and hg.in_h2 IS NOT NULL and hg.in_m2 IS NOT NULL and hg.in_f2 between ? and ?
            union
            SELECT
            nom, ape, hp.dni, hg.fec, fec_h, fec_m, in_c, in_t, in_f2, in_h2, in_m2, cupon, hg.estadoconsulta_ginecologia_id, hg.tipoconsulta_ginecologia_id, hg.id, mgm.nombre as motivo_consulta
            FROM hc_gineco hg
            inner join hc_paciente hp on hp.dni = hg.dni
            inner join man_gine_motivoconsulta mgm on mgm.id = hg.man_motivoconsulta_id
            where hg.cancela = 0 and hg.med = ? AND hg.in_c <> 1 and hg.fec IS NOT NULL and hg.fec_h IS NOT NULL and hg.fec_m IS NOT NULL and hg.fec between ? and ?");
        $rGinex->execute(array($login, $range_start, $range_end, $login, $range_start, $range_end));
    }

    $rDisponi = $db->prepare("SELECT * FROM hc_disponible WHERE med=? ORDER BY fec DESC");
    $rDisponi->execute(array($login));
}

$json = '[';

if ($_SESSION['role'] <> 2) {
    if ($user['role']==1 || $user['role']==8) {
        while ($cita = $rGinex->fetch(PDO::FETCH_ASSOC)) {
            $arrayTipoConsulta = array('1' => 'Presencial', '2' => 'Virtual');
            $arrayEstadoConsulta = array('1' => 'Programado', '2' => 'Confirmado', '3' => 'Anulado', '4' => 'Pagado');
            $arraySede = array('5' => 'San Borja', '6' => 'San Judas Tadeo', '10' => 'Clínica Monterrico', '11' => 'Virtual');
            $url_estado_programacion = '';
            $detalle = "<b># " . $cita['id'] . "</b><br><br>Apellidos y Nombres: " . ucwords(mb_strtolower($cita['ape'])) . " " . ucwords(mb_strtolower($cita['nom'])) . "<br>Tipo de consulta: " . (isset($arrayTipoConsulta[$cita['tipoconsulta_ginecologia_id']]) ? $arrayTipoConsulta[$cita['tipoconsulta_ginecologia_id']] : 'Tipo de consulta no definido') . '<br>Motivo de consulta: ' . $cita['motivo_consulta'] . '<br>Sede: ';
            $json = $json.'{"title":"(Consulta)\n' . ucwords(mb_strtolower($cita['ape'])) . ' ' . ucwords(mb_strtolower($cita['nom'])) . '\nTipo: ' . (isset($arrayTipoConsulta[$cita['tipoconsulta_ginecologia_id']]) ? $arrayTipoConsulta[$cita['tipoconsulta_ginecologia_id']] : 'Tipo de consulta no definido') . '\nEstado: ' . (isset($arrayEstadoConsulta[$cita['estadoconsulta_ginecologia_id']]) ? $arrayEstadoConsulta[$cita['estadoconsulta_ginecologia_id']] : 'Estado no definido') . '","start":"'.$cita['fec'].'T'.$cita['fec_h'].':'.$cita['fec_m'].'","color":"#687466","url":"detalle.html", "detalle": "' . $detalle . '", "id": "' . $cita['id'] . '", "className":"demo-class"},';

            if ($cita['in_h2'] <> '' && $cita['in_m2'] <> '') {
                $json = $json.'{"title":"(Intervención) \n'.ucwords(mb_strtolower($cita['ape'])).' '.ucwords(mb_strtolower($cita['nom'])).'\n'.$cita['in_t'].'","start":"'.$cita['in_f2'].'T'.$cita['in_h2'].':'.$cita['in_m2'].'","color":"orange","url":"n_gine.php?id='.$cita['dni'].'"},';
            }
        }

        while ($cita = $rObstex->fetch(PDO::FETCH_ASSOC)) {
            $con_fec = explode("|", $cita['con_fec']);
            $con_fec_h = explode("|", $cita['con_fec_h']);
            $con_fec_m = explode("|", $cita['con_fec_m']);

            for ($i = 0; $i < count($con_fec); $i++) {
                if (isset($con_fec[$i]) && !empty($con_fec[$i]) && isset($con_fec_h[$i]) && !empty($con_fec_h[$i]) && isset($con_fec_m[$i]) && !empty($con_fec_m[$i])) {
                    $json = $json.'{"title":"(Obstetricia)\n'.ucwords(mb_strtolower($cita['ape'])).' '.ucwords(mb_strtolower($cita['nom'])).'","start":"'.$con_fec[$i].'T'.$con_fec_h[$i].':'.$con_fec_m[$i].'","color":"green","url":"n_obst.php?id='.$cita['dni'].'"},';
                }
            }

            if ($cita['in_h2'] <> '') {
                $json = $json.'{"title":"(Intervención)\n'.ucwords(mb_strtolower($cita['ape'])).' '.ucwords(mb_strtolower($cita['nom'])).'\n'.$cita['in_t'].'","start":"'.$cita['in_f2'].'T'.$cita['in_h2'].':'.$cita['in_m2'].'","color":"orange","url":"n_obst.php?id='.$cita['dni'].'"},';
            }
        }
    }

    while ($dispo = $rDisponi->fetch(PDO::FETCH_ASSOC)) {
        $json = $json.'{"title":" - '.$dispo['fin'].'\n'.$dispo['obs'].'\n ('.$dispo['med'].')","start":"' .$dispo['fec'].'T'.$dispo['ini'].'","color":"deeppink"},';
    }
}

while ($cita = $rReprox->fetch(PDO::FETCH_ASSOC)) {
    $title="";
    $start="";
    $url="";
    if (is_null($cita['des_dia'])) {
        $start=$cita['f_asp'];
        $title=' - '.$cita['horafin'].'\nAspiracion\n'.mb_strtoupper($cita['ape']).' '.mb_strtoupper($cita['nom']).'\n'.$cita['med'].' ('.$cita['programa'].')';
        $url="n_repro.php?id=".$cita['dni'];
        $json.='{"title": "'.$title.'", "start": "'.$start.'", "url": "'.$url.'"},';
    } else {
        $tipo_procedimiento='';
        if ($cita['des_don'] == null && $cita['des_dia'] >= 1) { $tipo_procedimiento = 'TED'; }
        if ($cita['des_don'] == null && $cita['des_dia'] === 0) { $tipo_procedimiento = 'Des Ovulos Propios'; }
        if ($cita['des_don'] <> null && $cita['des_dia'] >= 1) { $tipo_procedimiento = 'Embriodonacion'; }
        if ($cita['des_don'] <> null && $cita['des_dia'] === 0) { $tipo_procedimiento = 'Des Ovulos Donados'; }
        if ($cita['h_iny'] == '') { $cita['h_iny'] = '06:00'; } // descongelaciones
        $start=$cita['f_iny'].'T'.$cita['h_iny'];
        $title=' - '.$cita['horafin'].'\n'.$tipo_procedimiento.'\n'.mb_strtoupper($cita['ape']).' '.mb_strtoupper($cita['nom']).'\n'.$cita['med'].' ('.$cita['programa'].')';
        $url="n_repro.php?id=".$cita['dni'];
        $json.='{"title": "'.$title.'", "start": "'.$start.'", "url": "'.$url.'"},';
    }
}

while ($cita = $rReproTra->fetch(PDO::FETCH_ASSOC)) {
    $json = $json.'{"title":"Transferencia Fresco\n'.$cita['ape'].' '.$cita['nom'].'\n ('.$cita['med'].')","start":"'.$cita['f_tra'].'T'.$cita['h_tra'].'","url":"n_repro.php?id='.$cita['dni'].'"},';
}

$title="";
$start="";
$url="";
while ($cita = $rInter_Gine->fetch(PDO::FETCH_ASSOC)) {
    $title=' - '.$cita['horafin'].'\n'.$cita['in_t'].' '.'\n'.mb_strtoupper($cita['ape']).' '.mb_strtoupper($cita['nom']).'\n'.$cita['med'].' ('.$cita['programa'].')';
    $start=$cita['in_f2'].'T'.$cita['in_h2'].':'.$cita['in_m2'];
    $url="n_gine.php?id=".$cita['dni'];
    $json.='{"title": "'.$title.'", "start": "'.$start.'", "url": "'.$url.'"},';
}

if ($_GET['med'] == '') {
    while ($cita = $rInter_Uro->fetch(PDO::FETCH_ASSOC)) {
        $json = $json.'{"title":"'.$cita['in_t'].' \nFin: '.$cita['horafin'].'\n'.$cita['p_ape'].' '.$cita['p_nom'].'\n ('.$cita['med'].')","start":"'.$cita['in_f2'].'T'.$cita['in_h2'].':'.$cita['in_m2'].'"},';
    }

    while ($cita = $consulta_bloqueo->fetch(PDO::FETCH_ASSOC)) {
        $json = $json.'{"title":"No disponible \nFin: '.$cita['horafin'].'\n", "start":"'.$cita['fecha'].'T'.$cita['hora'].':'.$cita['minuto'].'", "color":"deeppink"},';
    }
}

$json = $json.'{"title":"nulo","start":"2000-01-12T07:00"}]';
$json_eample = '[{"title":"Meeting","start":"2015-01-12T07:00"},
  {
    "title":"Lunches \n juana maribel cubana delamarrana",
    "start":"2015-01-13T12:00"
  },
  {
    "title": "Meeting",
    "start": "2015-01-18T15:00",
	"color": "green"
  },{}]';

$input_arrays = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json), true );
$output_arrays = array();

foreach ($input_arrays as $array) {
    $event = new Event($array, $timezone);
    if ($event->isWithinDayRange($range_start_ini, $range_end_ini)) {
        $output_arrays[] = $event->toArray();
    }
}

echo json_encode($output_arrays);