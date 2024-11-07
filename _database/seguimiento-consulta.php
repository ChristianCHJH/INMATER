<?php
date_default_timezone_set('America/Lima');
require $_SERVER["DOCUMENT_ROOT"] . "/_database/database.php";

function actualizarConfirmacion($login, $id)
{
    global $db;
    $stmt = $db->prepare("UPDATE hc_gineco SET fecha_confirmacion = ?, estadoconsulta_ginecologia_id = 2, iduserupdate = ?, updatex=? WHERE id = ?");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute([date("Y-m-d H:i:s"), $login, $hora_actual, $id]);

    $log_Gineco = $db->prepare(
        "INSERT INTO appinmater_log.hc_gineco (
            gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
            man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
            fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
            vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
            in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
            cupon, repro, legal, cancela, cancela_motivo,
            isuser_log, date_log,
            asesor_medico_id, 
            action
            )
        SELECT
            id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
            man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
            fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
            vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
            in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
            cupon, repro, legal, cancela, cancela_motivo,
            iduserupdate, updatex, 
            asesor_medico_id,
            'U'
        FROM appinmater_modulo.hc_gineco
        WHERE id =?");
    $log_Gineco->execute(array($id));
}

function actualizarAnulacion($login, $id)
{
    global $db;
    $stmt = $db->prepare("UPDATE hc_gineco SET fecha_confirmacion = ?, estadoconsulta_ginecologia_id = 3, iduserupdate = ?, updatex=? WHERE id = ?");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute([date("Y-m-d H:i:s"), $login, $hora_actual, $id]);

    $log_Gineco = $db->prepare(
        "INSERT INTO appinmater_log.hc_gineco (
            gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
            man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
            fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
            vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
            in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
            cupon, repro, legal, cancela, cancela_motivo,
            isuser_log, date_log,
            asesor_medico_id, 
            action
            )
        SELECT
            id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
            man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
            fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
            vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
            in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
            cupon, repro, legal, cancela, cancela_motivo,
            iduserupdate, updatex, 
            asesor_medico_id,
            'U'
        FROM appinmater_modulo.hc_gineco
        WHERE id =?");
    $log_Gineco->execute(array($id));
}

function subirVoucher($login, $id, $informe)
{
    $path = $_SERVER["DOCUMENT_ROOT"] . "/archivo/";
    global $db;

    if (isset($informe)) {
        if (!empty($informe['name'])) {
            $informe_name = $informe['name'];
            $nombre_original = $informe_name;
            $informe_name = preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
            $nombre_base = time()."-".$informe_name;
            $ruta = $path.$nombre_base;

            if (is_uploaded_file($informe['tmp_name'])) {
                move_uploaded_file($informe['tmp_name'], $ruta);

                // registrar video
                $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?)");
                $stmt->execute(array($nombre_base, $nombre_original, $login));
                $voucher_id = $db->lastInsertId();

                // actualizar informe
                $stmt = $db->prepare("UPDATE hc_gineco set estadoconsulta_ginecologia_id=4, voucher_id=?, fecha_voucher=?, iduserupdate=?, updatex=? where id=?");
                $hora_actual = date("Y-m-d H:i:s");
                $stmt->execute([$voucher_id, date("Y-m-d H:i:s"), $login, $hora_actual, $id]);

                $log_Gineco = $db->prepare(
                    "INSERT INTO appinmater_log.hc_gineco (
                        gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                        man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
                        fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
                        vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                        in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
                        cupon, repro, legal, cancela, cancela_motivo,
                        isuser_log, date_log,
                        asesor_medico_id, 
                        action
                        )
                    SELECT
                        id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                        man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
                        fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
                        vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                        in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
                        cupon, repro, legal, cancela, cancela_motivo,
                        iduserupdate, updatex, 
                        asesor_medico_id,
                        'U'
                    FROM appinmater_modulo.hc_gineco
                    WHERE id =?");
                $log_Gineco->execute(array($id));

                return ["nombre_base" => $nombre_base, "nombre_original" => $nombre_original];
            }
        }
    }

    return "error";
}
?>