<?php
date_default_timezone_set('America/Lima');
require("database.php");

function ValidarAgendaReproTurno($id, $fechaini, $idturno)
{
    global $db;
    $coincidencias=0;
    $timeini = explode("T", $fechaini);
    $horafin = TraerFechaFin($timeini[1], $idturno);
    if (empty($horafin)) {
        $horafin = '00:00';
    }
    if (empty($timeini[0])) {
        $timeini[0] = '1899-12-30';
    }
    if (empty($timeini[1])) {
        $timeini[1] = '00:00';
    }
    // validar aspiraciones
    $stmt = $db->prepare("SELECT a.id
        FROM hc_reprod a
        INNER JOIN hc_paciente b ON b.dni = a.dni
        INNER JOIN man_turno_reproduccion c ON c.codigo = a.idturno AND c.estado = 1
        WHERE a.estado = true and a.estado = true and a.id <> ? AND a.f_asp <> ''
        AND TO_CHAR(a.f_asp::timestamp, 'YYYY-MM-DD') = ?
        AND (
            (?::time < to_char(a.f_asp::timestamp, 'HH24:MI')::time AND ?::time > to_char(a.f_asp::timestamp, 'HH24:MI')::time) OR
            (?::time < to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time AND ?::time > to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time) OR
            (?::time < to_char(a.f_asp::timestamp, 'HH24:MI')::time AND ?::time > to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time) OR
            (?::time > to_char(a.f_asp::timestamp, 'HH24:MI')::time AND ?::time < to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time) OR
            (?::time >= to_char(a.f_asp::timestamp, 'HH24:MI')::time AND ?::time <= to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time)
        ) AND a.cancela <> 1 AND (a.des_dia IS NULL OR a.des_dia <> 0) AND (a.p_od = '' OR a.p_od IS NULL)");
    $stmt->execute(array($id, $timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar transferencias
    $stmt = $db->prepare("SELECT a.id
        FROM hc_reprod a
        INNER JOIN hc_paciente b ON b.dni = a.dni
        INNER JOIN man_turno_reproduccion c ON c.codigo = a.idturno_tra AND c.estado = 1
        WHERE a.estado = true and a.h_tra <> '00:00:00' AND a.f_tra = ?
        AND (
            (?::time < a.h_tra::time AND ?::time > a.h_tra::time) OR
            (?::time < (a.h_tra::time + c.formato_hora_minuto::interval) AND ?::time > (a.h_tra::time + c.formato_hora_minuto::interval)) OR
            (?::time < a.h_tra::time AND ?::time > (a.h_tra::time + c.formato_hora_minuto::interval)) OR
            (?::time > a.h_tra::time AND ?::time < (a.h_tra::time + c.formato_hora_minuto::interval)) OR
            (?::time >= a.h_tra::time AND ?::time <= (a.h_tra::time + c.formato_hora_minuto::interval))
        )
        AND a.cancela <> 1");
    $stmt->execute(array($timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar ginecologia
    $stmt = $db->prepare("SELECT a.id
        FROM hc_gineco a
        INNER JOIN hc_paciente b ON b.dni = a.dni
        INNER JOIN man_turno_reproduccion c ON c.codigo = a.idturno_inter AND c.estado = 1
        WHERE a.in_c = 1 AND a.in_h2 <> '00:00:00' AND a.in_m2 <> '' AND in_f2 = ?
        AND (
            (?::time < (a.in_h2 || ':' || a.in_m2)::time AND ?::time > (a.in_h2 || ':' || a.in_m2)::time) OR
            (?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval) AND ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) OR
            (?::time < (a.in_h2 || ':' || a.in_m2)::time AND ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) OR
            (?::time > (a.in_h2 || ':' || a.in_m2)::time AND ?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) OR
            (?::time >= (a.in_h2 || ':' || a.in_m2)::time AND ?::time <= ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval))
    )");
    $stmt->execute(array($timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar urologia
    $stmt = $db->prepare("SELECT a.id
        FROM hc_urolo a
        LEFT JOIN hc_pareja b ON b.p_dni = a.p_dni
        INNER JOIN man_turno_reproduccion c ON c.codigo = a.idturno_inter AND c.estado = 1
        WHERE a.in_f2 <> '1899-12-30' AND a.in_h2 <> '' AND a.in_m2 <> '' AND in_f2 = ?
        AND (
            (?::time < (a.in_h2 || ':' || a.in_m2)::time AND ?::time > (a.in_h2 || ':' || a.in_m2)::time) OR
            (?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval) AND ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) OR
            (?::time < (a.in_h2 || ':' || a.in_m2)::time AND ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) OR
            (?::time > (a.in_h2 || ':' || a.in_m2)::time AND ?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) OR
            (?::time >= (a.in_h2 || ':' || a.in_m2)::time AND ?::time <= ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval))
    )");
    $stmt->execute(array($timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar bloqueos
    $stmt = $db->prepare("SELECT b.id
        FROM lab_agenda_bloqueo b
        INNER JOIN man_hora h ON h.id = b.idhora AND h.estado = 1
        INNER JOIN man_turno_reproduccion c ON c.id = b.idturno AND c.estado = 1
        WHERE b.fecha = ? AND b.estado = 1
        AND (
            (?::time < h.nombre::time AND ?::time > h.nombre::time) OR
            (?::time < (h.nombre::time + c.formato_hora_minuto::interval) AND ?::time > (h.nombre::time + c.formato_hora_minuto::interval)) OR
            (?::time < h.nombre::time AND ?::time > (h.nombre::time + c.formato_hora_minuto::interval)) OR
            (?::time > h.nombre::time AND ?::time < (h.nombre::time + c.formato_hora_minuto::interval)) OR
            (?::time >= h.nombre::time AND ?::time <= (h.nombre::time + c.formato_hora_minuto::interval))
    )");
    $stmt->execute(array($timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    return $coincidencias;
}

function validarAgendaTransTurno($id, $fechaini, $idturno)
{
    global $db;
    $coincidencias=0;
    $timeini = explode("T", $fechaini);
    $horafin = TraerFechaFin($timeini[1], $idturno);
    if (empty($horafin)) {
        $horafin = '00:00';
    }
    if (empty($timeini[0])) {
        $timeini[0] = '1899-12-30';
    }
    if (empty($timeini[1])) {
        $timeini[1] = '00:00';
    }

    // validar aspiraciones
    $stmt = $db->prepare("SELECT a.id
        FROM hc_reprod a
        INNER JOIN hc_paciente b ON b.dni = a.dni
        INNER JOIN man_turno_reproduccion c ON c.codigo = a.idturno AND c.estado = 1
        WHERE a.estado = true and a.id <> ? AND a.f_asp <> ''
        AND TO_CHAR(a.f_asp::timestamp, 'YYYY-MM-DD') = ?
        AND (
            (?::time < to_char(a.f_asp::timestamp, 'HH24:MI')::time AND ?::time > to_char(a.f_asp::timestamp, 'HH24:MI')::time) OR
            (?::time < to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time AND ?::time > to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time) OR
            (?::time < to_char(a.f_asp::timestamp, 'HH24:MI')::time AND ?::time > to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time) OR
            (?::time > to_char(a.f_asp::timestamp, 'HH24:MI')::time AND ?::time < to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time) OR
            (?::time >= to_char(a.f_asp::timestamp, 'HH24:MI')::time AND ?::time <= to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time)
        ) AND a.cancela <> 1 AND (a.des_dia IS NULL OR a.des_dia <> 0) AND (a.p_od = '' OR a.p_od IS NULL)");
    $stmt->execute(array($id, $timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar transferencias
    $stmt = $db->prepare("SELECT a.id
        FROM hc_reprod a
        INNER JOIN hc_paciente b ON b.dni = a.dni
        INNER JOIN man_turno_reproduccion c ON c.codigo = a.idturno_tra AND c.estado = 1
        WHERE a.estado = true and a.id <> ? AND a.f_tra <> '1899-12-30' AND a.h_tra <> '00:00:00' AND a.f_tra = ?
        AND (
            (?::time < a.h_tra::time AND ? > a.h_tra::time) OR
            (?::time < (a.h_tra::time + c.formato_hora_minuto::interval) AND ? > (a.h_tra::time + c.formato_hora_minuto::interval)) OR
            (?::time < a.h_tra::time AND ? > (a.h_tra::time + c.formato_hora_minuto::interval)) OR
            (?::time > a.h_tra::time AND ? < (a.h_tra::time + c.formato_hora_minuto::interval)) OR
            (?::time >= a.h_tra::time AND ? <= (a.h_tra::time + c.formato_hora_minuto::interval))
        )
        AND a.cancela <> 1");
    $stmt->execute(array($id, $timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar ginecologia
    $stmt = $db->prepare("SELECT a.id
        from hc_gineco a
        inner join hc_paciente b on b.dni = a.dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno_inter and c.estado = 1
        where a.in_c=1 and a.in_f2<>'1899-12-30' and a.in_h2<>'00:00:00' and a.in_m2<>'' and in_f2 = ?
        and (
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ?::time > (a.in_h2 || ':' || a.in_m2)::time) or
            (?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval) and ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time > (a.in_h2 || ':' || a.in_m2)::time and ?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time >= (a.in_h2 || ':' || a.in_m2)::time and ?::time <= ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval))
        )");
    $stmt->execute(array($timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar urologia
    $stmt = $db->prepare("SELECT a.id
        from hc_urolo a
        left join hc_pareja b on b.p_dni = a.p_dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno_inter and c.estado = 1
        where a.in_f2<>'1899-12-30' and a.in_h2<>'00:00:00' and a.in_m2<>'' and in_f2 = ?
        and (
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ? > (a.in_h2 || ':' || a.in_m2)) or
            (?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval) and ? > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ? > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time > (a.in_h2 || ':' || a.in_m2)::time and ? < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time >= (a.in_h2 || ':' || a.in_m2)::time and ? <= ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval))
        )");
    $stmt->execute(array($timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar bloqueos
    $stmt = $db->prepare("SELECT b.id
        from lab_agenda_bloqueo b
        inner join man_hora h on h.id = b.idhora and h.estado = 1
        inner join man_turno_reproduccion c on c.id = b.idturno and c.estado = 1
        where b.fecha=? and b.estado=1
        and (
            (?::time < h.nombre::time and ? > h.nombre::time) or
            (?::time < (h.nombre::time + c.formato_hora_minuto::interval) and ? > (h.nombre::time + c.formato_hora_minuto::interval)) or
            (?::time < h.nombre::time and ? > (h.nombre::time + c.formato_hora_minuto::interval)) or
            (?::time > h.nombre::time and ? < (h.nombre::time + c.formato_hora_minuto::interval)) or
            (?::time >= h.nombre::time and ? <= (h.nombre::time + c.formato_hora_minuto::interval))
        )");
    $stmt->execute(array($timeini[0], $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin, $timeini[1], $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    return $coincidencias;
}

function validarAgendaGineTurno($id, $fechaini, $idturno)
{
    global $db;
    $coincidencias=0;
    $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $fechaini);
    $date='1899-12-30';
    $time='00:00';
    if($fechaini !='T')$date = $dateTime->format('Y-m-d');
    if($fechaini !='T')$time = $dateTime->format('H:i');
    $horafin = TraerFechaFin($time, $idturno);
    if (empty($horafin)) {
        $horafin = '00:30';
    }

    // validar aspiraciones
    $stmt = $db->prepare("SELECT a.id
        from hc_reprod a
        inner join hc_paciente b on b.dni = a.dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno and c.estado = 1
        where a.estado = true and a.id<>? and a.f_asp<>''
        and to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI'), 'YYYY-MM-DD') = ?
        and (
            (? < to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI'), 'HH24:MI') and ? > to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI'), 'HH24:MI')) or
            (? < to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI') + (c.formato_hora_minuto || ' minutes')::interval, 'HH24:MI') and ? > to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI') + (c.formato_hora_minuto || ' minutes')::interval, 'HH24:MI')) or
            (? < to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI'), 'HH24:MI') and ? > to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI') + (c.formato_hora_minuto || ' minutes')::interval, 'HH24:MI')) or
            (? > to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI'), 'HH24:MI') and ? < to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI') + (c.formato_hora_minuto || ' minutes')::interval, 'HH24:MI')) or
            (? >= to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI'), 'HH24:MI') and ? <= to_char(to_timestamp(a.f_asp, 'YYYY-MM-DD\"T\"HH24:MI') + (c.formato_hora_minuto || ' minutes')::interval, 'HH24:MI'))
        ) and a.cancela <> 1 and (a.des_dia is null or a.des_dia <> 0) and (a.p_od='' or a.p_od is null)");
    $stmt->execute(array($id, $date, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin));


    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar transferencias
    $stmt = $db->prepare("SELECT a.id
        from hc_reprod a
        inner join hc_paciente b on b.dni = a.dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno_tra and c.estado = 1
        where a.estado = true and a.f_tra <> '1899-12-30' and a.h_tra <> '' and a.f_tra = ?
        and (
            (?::time < a.h_tra::time and ?::time > a.h_tra::time) or
            (?::time < (a.h_tra::time + c.formato_hora_minuto::interval) and ?::time > (a.h_tra::time + c.formato_hora_minuto::interval)) or
            (?::time < a.h_tra::time and ?::time > (a.h_tra::time + c.formato_hora_minuto::interval)) or
            (?::time > a.h_tra::time and ?::time < (a.h_tra::time + c.formato_hora_minuto::interval)) or
            (?::time >= a.h_tra::time and ?::time <= (a.h_tra::time + c.formato_hora_minuto::interval))
        )
        and a.cancela <> 1");
    $stmt->execute(array($date, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin));

    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar ginecologia
    $stmt = $db->prepare("SELECT a.id
        from hc_gineco a
        inner join hc_paciente b on b.dni = a.dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno_inter and c.estado = 1
        where a.id <> ? and a.in_c = 1 and a.in_f2 IS NOT NULL and a.in_h2 <> '' and a.in_m2 <> '' and in_f2 = ?
        and (
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ?::time > (a.in_h2 || ':' || a.in_m2)::time) or
            (?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval) and ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time > (a.in_h2 || ':' || a.in_m2)::time and ?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time >= (a.in_h2 || ':' || a.in_m2)::time and ?::time <= ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval))
        )");
    $stmt->execute(array($id, $date, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin));

    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar urologia
    $stmt = $db->prepare("SELECT a.id
        from hc_urolo a
        left join hc_pareja b on b.p_dni = a.p_dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno_inter and c.estado = 1
        where a.in_f2 IS NOT NULL and a.in_h2 <> '' and a.in_m2 <> '' and in_f2 = ?
        and (
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ?::time > (a.in_h2 || ':' || a.in_m2)::time) or
            (?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval) and ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ?::time > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time > (a.in_h2 || ':' || a.in_m2)::time and ?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time >= (a.in_h2 || ':' || a.in_m2)::time and ?::time <= ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval))
        )");
    $stmt->execute(array($date, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin));

    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar bloqueos
    $stmt = $db->prepare("SELECT b.id
        from lab_agenda_bloqueo b
        inner join man_hora h on h.id = b.idhora and h.estado = 1
        inner join man_turno_reproduccion c on c.id = b.idturno and c.estado = 1
        where b.fecha=? and b.estado=1
        and (
            (?::time < h.nombre::time without time zone and ? > h.nombre::time without time zone) or
            (?::time < (h.nombre::time without time zone + c.formato_hora_minuto::interval) and ? > (h.nombre::time without time zone + c.formato_hora_minuto::interval)) or
            (?::time < h.nombre::time without time zone and ? > (h.nombre::time without time zone + c.formato_hora_minuto::interval)) or
            (?::time > h.nombre::time without time zone and ? < (h.nombre::time without time zone + c.formato_hora_minuto::interval)) or
            (?::time >= h.nombre::time without time zone and ? <= (h.nombre::time without time zone + c.formato_hora_minuto::interval))
        )");
    $stmt->execute(array($date, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin));

    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    return $coincidencias;
}

function validarAgendaUroTurno($id, $fechaini, $idturno)
{
    global $db;
    $coincidencias=0;
    $timeini = explode("T", $fechaini);
    $horafin = TraerFechaFin($timeini[1], $idturno);
    
    // validar aspiraciones
    $stmt = $db->prepare("
        select a.id
        from hc_reprod a
        inner join hc_paciente b on b.dni = a.dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno and c.estado = 1
        where a.estado = true and a.f_asp<>'' and to_char(a.f_asp::timestamp, 'YYYY-MM-DD') = ?
        and (
            (?::time < to_char(a.f_asp::timestamp, 'HH24:MI')::time and ? > to_char(a.f_asp::timestamp, 'HH24:MI')::time) or
            (?::time < to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time and ? > to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time) or
            (?::time < to_char(a.f_asp::timestamp, 'HH24:MI')::time and ? > to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time) or
            (?::time > to_char(a.f_asp::timestamp, 'HH24:MI')::time and ? < to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time) or
            (?::time >= to_char(a.f_asp::timestamp, 'HH24:MI')::time and ? <= to_char((a.f_asp::timestamp + c.formato_hora_minuto::interval), 'HH24:MI')::time)
        ) and a.cancela <> 1 and (a.des_dia is null or a.des_dia <> 0) and (a.p_od='' or a.p_od is null)");
        $defaultTime = '00:00';

        $stmt->execute(array(
            $timeini[0], 
            !empty($timeini[1]) ? $timeini[1] : $defaultTime, 
            !empty($horafin) ? $horafin : $defaultTime, 
            !empty($timeini[1]) ? $timeini[1] : $defaultTime, 
            !empty($horafin) ? $horafin : $defaultTime, 
            !empty($timeini[1]) ? $timeini[1] : $defaultTime, 
            !empty($horafin) ? $horafin : $defaultTime, 
            !empty($timeini[1]) ? $timeini[1] : $defaultTime, 
            !empty($horafin) ? $horafin : $defaultTime, 
            !empty($timeini[1]) ? $timeini[1] : $defaultTime, 
            !empty($horafin) ? $horafin : $defaultTime
        ));        

    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar transferencias
    $stmt = $db->prepare("
        select a.id
        from hc_reprod a
        inner join hc_paciente b on b.dni = a.dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno_tra and c.estado = 1
        where a.estado = true and a.f_tra<>'1899-12-30' and a.h_tra<>'' and a.f_tra=?
        and (
            (?::time < a.h_tra::time and ?::time > a.h_tra::time) or
            (?::time < (a.h_tra::time + c.formato_hora_minuto::interval) and ?::time > (a.h_tra::time + c.formato_hora_minuto::interval)) or
            (?::time < a.h_tra::time and ?::time > (a.h_tra::time + c.formato_hora_minuto::interval)) or
            (?::time > a.h_tra::time and ?::time < (a.h_tra::time + c.formato_hora_minuto::interval)) or
            (?::time >= a.h_tra::time and ?::time <= (a.h_tra::time + c.formato_hora_minuto::interval))
        )
        and a.cancela <> 1");
        $date = $timeini[0] != '' ? $timeini[0] : '1899-12-30';
        $time = $timeini[1] != '' ? $timeini[1] : '00:00:00';
        $stmt->execute(array($date, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin));
        

    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar ginecologia
    $stmt = $db->prepare("
        select a.id
        from hc_gineco a
        inner join hc_paciente b on b.dni = a.dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno_inter and c.estado = 1
        where a.in_c=1 and a.in_f2<>'1899-12-30' and a.in_h2<>'' and a.in_m2<>'' and in_f2 = ?
        and (
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ? > (a.in_h2 || ':' || a.in_m2)::time) or
            (?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval) and ? > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ? > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time > (a.in_h2 || ':' || a.in_m2)::time and ? < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time >= (a.in_h2 || ':' || a.in_m2)::time and ? <= ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval))
        )");
        $date = $timeini[0] != '' ? $timeini[0] : '1899-12-30';
        $time = $timeini[1] != '' ? $timeini[1] : '00:00:00';
        $stmt->execute(array($date, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }
  
    // validar urologia
    $stmt = $db->prepare("
        select a.id
        from hc_urolo a
        left join hc_pareja b on b.p_dni = a.p_dni
        inner join man_turno_reproduccion c on c.codigo = a.idturno_inter and c.estado = 1
        where a.id<>? and a.in_f2<>'1899-12-30' and a.in_h2<>'' and a.in_m2<>'' and in_f2 = ?
        and (
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ? > (a.in_h2 || ':' || a.in_m2)::time) or
            (?::time < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval) and ? > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time < (a.in_h2 || ':' || a.in_m2)::time and ? > ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time > (a.in_h2 || ':' || a.in_m2)::time and ? < ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval)) or
            (?::time >= (a.in_h2 || ':' || a.in_m2)::time and ? <= ((a.in_h2 || ':' || a.in_m2)::time + c.formato_hora_minuto::interval))
        )");
    $stmt->execute(array($id, $date, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    // validar bloqueos
    $stmt = $db->prepare("
        select b.id
        from lab_agenda_bloqueo b
        inner join man_hora h on h.id = b.idhora and h.estado = 1
        inner join man_turno_reproduccion c on c.id = b.idturno and c.estado = 1
        where b.fecha=? and b.estado=1
        and (
            (?::time < h.nombre::time and ? > h.nombre::time) or
            (?::time < (h.nombre::time + c.formato_hora_minuto::interval) and ? > (h.nombre::time + c.formato_hora_minuto::interval)) or
            (?::time < h.nombre::time and ? > (h.nombre::time + c.formato_hora_minuto::interval)) or
            (?::time > h.nombre::time and ? < (h.nombre::time + c.formato_hora_minuto::interval)) or
            (?::time >= h.nombre::time and ? <= (h.nombre::time + c.formato_hora_minuto::interval))
        )");
    $stmt->execute(array($date, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin, $time, $horafin));
    if ($stmt->rowCount() > 0)
    {
        $coincidencias+=$stmt->rowCount();
    }

    return $coincidencias;
}

function TraerFechaFin($horainicio, $idturno)
{
    global $db;
    $horafin = "";
    if ($horainicio == '' || $horainicio == 0) {
        $horainicio = '00:00';
    }
    $consulta = $db->prepare("SELECT to_char((?::time + formato_hora_minuto::interval), 'HH24:MI') AS horafin FROM man_turno_reproduccion WHERE codigo = ? AND estado = 1");
    $consulta->execute(array($horainicio, $idturno));
    if ($consulta->rowCount() > 0)
    {
        $data = $consulta->fetch(PDO::FETCH_ASSOC);
        $horafin = $data["horafin"];
    }

    return $horafin;
}

?>