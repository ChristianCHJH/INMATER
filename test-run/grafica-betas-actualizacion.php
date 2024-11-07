<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

    global $db;
    $stmt = $db->prepare("SELECT la.pro, rep.id, rep.f_tra
    , sum(if (lad.d3f_cic = 'T', 1, 0)) dia3, la.fec3
    , sum(if (lad.d4f_cic = 'T', 1, 0)) dia4, la.fec4
    , sum(if (lad.d5f_cic = 'T', 1, 0)) dia5, la.fec5
    , sum(if (lad.d6f_cic = 'T', 1, 0)) dia6, la.fec6
    from hc_reprod rep
    inner join lab_aspira la on la.rep = rep.id and la.estado is true
    inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true
    inner join lab_aspira_t lat on lat.pro = la.pro and lat.estado is true
    where rep.estado = true and coalesce(rep.des_dia, 0) < 1 and rep.f_tra = '1899-12-30'
    group by lat.pro, lat.beta
    order by rep.id;");
    $stmt->execute();

    print("<p>total de registros: " .  $stmt->rowCount() . "</p>");

    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print("<pre>"); print_r($item); print("</pre>");
        // calculo de la fecha de transferencia
        $fecha_transferencia = "";
        if ($item["dia3"] != 0) {
            $fecha_transferencia = $item["fec3"];
        }
        if ($item["dia4"] != 0) {
            $fecha_transferencia = $item["fec4"];
        }
        if ($item["dia5"] != 0) {
            $fecha_transferencia = $item["fec5"];
        }
        if ($item["dia6"] != 0) {
            $fecha_transferencia = $item["fec6"];
        }
        // actualizo segun el valor del dia en que transfirio
        if ($fecha_transferencia != "") {
            $stmt_upd = $db->prepare("update hc_reprod set f_tra = ?, updatex=? where id = ?;");
            $hora_actual = date("Y-m-d H:i:s");
            $stmt_upd->execute([$fecha_transferencia, $hora_actual, $item["id"]]);

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
            $log_Reprod->execute([$item["id"]]);
        }
    }
    ?>
</body>

</html>