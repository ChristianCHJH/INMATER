<?php
$rPaci = $db->prepare("
    select
    hc_reprod.id, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
    , hc_reprod.des_dia, hc_reprod.des_don
    , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
    , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
    , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
    , lab_aspira.fec
    from hc_reprod
    inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
    inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'
    inner join lab_aspira_t on lab_aspira_t.pro=lab_aspira.pro and lab_aspira_t.beta=1 and lab_aspira_t.estado is true
    inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
    inner join lab_aspira la on la.pro = lab_aspira_dias.pro_c and la.estado is true$between
    left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
    where hc_reprod.estado = true and hc_reprod.des_don is not null and hc_reprod.des_dia >= 1 $busqueda
    group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
    , hc_reprod.des_dia, hc_reprod.des_don
    , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
    , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
    , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
    , lab_aspira.fec
    , lab_aspira_t.beta
    , la.pro, la.tip
    order by lab_aspira.fec asc
");
?>