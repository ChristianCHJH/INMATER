<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
$login = "";

if (!!$_SESSION) {
    $login = $_SESSION['login'];
} else {
    http_response_code(400);
    echo json_encode(array("message" => "no se ha iniciado sesión"));
    exit();
}

/* var_dump($_POST); var_dump($_FILES); exit(); */

if (isset($_POST["tipo"]) && !empty($_POST["tipo"])) {
    switch ($_POST["tipo"]) {
        case 'lock':
            http_response_code(201);
            echo json_encode(array("message" => restriccion_bloquear($_POST["reprod_id"])));
            break;
        case 'unlock':
            http_response_code(201);
            echo json_encode(array("message" => restriccion_desbloquear($_POST["reprod_id"])));
            break;
        case 'info_restricciones':
            http_response_code(201);
            echo json_encode(array("message" => info_restricciones($_POST["paciente"])));
            break;
            
        
        default: exit(); break;
    }
}

function info_restricciones($paciente)
{
    global $db;
    $detalle = '';

    $stmt = $db->prepare("SELECT
        r.id, r.dni, r.med, p.ape apellidos, p.nom nombres, r.fec fecha, COALESCE(d.id, 0) desbloqueo_id, d.motivo
        FROM hc_reprod r
        LEFT JOIN hc_reprod_desbloqueo d ON d.reprod_id = r.id AND d.estado = 1
        INNER JOIN hc_paciente p ON p.dni = r.dni AND (p.dni ILIKE ? OR unaccent(p.ape) ILIKE ? OR unaccent(p.nom) ILIKE ?)
        WHERE r.estado = true
        ORDER BY id DESC");
    $stmt->execute(["%".$paciente."%", "%".$paciente."%", "%".$paciente."%"]);
    $i=1;

    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $bloqueo = "lock";
        $desbloqueo = "unlock";
        $bloqueo_text = "";
        $bloqueo_class = "bloquear";

        if ($item["desbloqueo_id"] != 0) {
            $bloqueo = "unlock";
            $desbloqueo = "lock";
            $bloqueo_text = "Desbloqueado";
            $bloqueo_class = "desbloquear";
        }

        $detalle.='<tr>
            <td align="center">'.$i++.'</td>
            <td>'.$item["fecha"].'</td>
            <td>'.mb_strtoupper($item["apellidos"]).' '.mb_strtoupper($item["nombres"]).'</td>
            <td align="center"><span>'.$bloqueo_text.'</span></td>
            <td align="center">
                <img src="_images/'.$bloqueo.'.png" height="18" width="18" alt="icon name" class="restricciones" data-id="'.$item["id"].'" data-tipo="'.$desbloqueo.'">
            </td></tr>';
    }

    return $detalle;
}

function restriccion_bloquear($reprod_id)
{
    global $db;
    global $login;

    $stmt = $db->prepare("update hc_reprod_desbloqueo SET estado = 0, iduserupdate = ? WHERE reprod_id=? AND estado = 1");
    $stmt->execute([$login, $reprod_id]);

    $log_Reprod = $db->prepare(
        "INSERT INTO appinmater_log.hc_reprod (
                    reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                    p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                    p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                    tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                    con_oi, con_end,
                    con1_med, 
                    con2_med, 
                    con3_med, 
                    con4_med, 
                    con5_med, 
                    con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                    complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                    pago_extras, pago_notas, pago_obs, repro, 
                    idusercreate, createdate, action
            )
        SELECT 
            id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
            p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
            p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
            tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
            con_oi, con_end,
            con1_med, 
            con2_med, 
            con3_med, 
            con4_med, 
            con5_med,
            con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
            complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
            pago_extras, pago_notas, pago_obs, repro, 
            iduserupdate, updatex, 'U'
        FROM appinmater_modulo.hc_reprod
        WHERE id=?");
    $log_Reprod->execute([$reprod_id]);
}

function restriccion_desbloquear($reprod_id)
{
    global $db;
    global $login;

    $stmt = $db->prepare("update hc_reprod_desbloqueo SET estado = 0, iduserupdate = ? WHERE reprod_id=? AND estado = 1");
    $stmt->execute([$login, $reprod_id]);

    

    $stmt = $db->prepare("insert into hc_reprod_desbloqueo (reprod_id, motivo, estado, idusercreate) VALUES (?, ?, ?, ?)");
    $stmt->execute([$reprod_id, "", 1, $login]);

}
?>