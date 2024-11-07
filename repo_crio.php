<?php
$rPaci = $db->prepare("
    select
    hc_reprod.id, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
    , hc_reprod.des_dia, hc_reprod.des_don
    , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom, floor(datediff(lab_aspira.fec, hc_paciente.fnac)/ 365) edad
    , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
    , upper(substring(hc_reprod.con1_med, 1, position('|' in hc_reprod.con1_med)-1)) 'medicamento 1'
    , upper(substring(hc_reprod.con2_med, 1, position('|' in hc_reprod.con2_med)-1)) 'medicamento 2'
    , upper(substring(hc_reprod.con3_med, 1, position('|' in hc_reprod.con3_med)-1)) 'medicamento 3'
    , upper(substring(hc_reprod.con4_med, 1, position('|' in hc_reprod.con4_med)-1)) 'medicamento 4'
    , upper(substring(hc_reprod.con5_med, 1, position('|' in hc_reprod.con5_med)-1)) 'medicamento 5'
    , hc_reprod.con1_med, hc_reprod.con2_med, hc_reprod.con3_med, hc_reprod.con4_med, hc_reprod.con5_med
    , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
    , lab_aspira.fec
    , lab_aspira.n_ovo
    , count( case when lab_aspira_dias.d0est = 'MII' and lab_aspira_dias.d0f_cic = 'C' then true end ) crio
    from hc_reprod
    inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
    inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'$between
    left join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
    left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
    where hc_reprod.estado = true and hc_reprod.cancela=0 and hc_reprod.p_cri = 1
    group by hc_reprod.id, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
    , hc_reprod.des_dia, hc_reprod.des_don
    , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom, floor(datediff(lab_aspira.fec, hc_paciente.fnac)/ 365)
    , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
    , upper(substring(hc_reprod.con1_med, 1, position('|' in hc_reprod.con1_med)-1))
    , upper(substring(hc_reprod.con2_med, 1, position('|' in hc_reprod.con2_med)-1))
    , upper(substring(hc_reprod.con3_med, 1, position('|' in hc_reprod.con3_med)-1))
    , upper(substring(hc_reprod.con4_med, 1, position('|' in hc_reprod.con4_med)-1))
    , upper(substring(hc_reprod.con5_med, 1, position('|' in hc_reprod.con5_med)-1))
    , hc_reprod.con1_med, hc_reprod.con2_med, hc_reprod.con3_med, hc_reprod.con4_med, hc_reprod.con5_med
    , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
    , lab_aspira.fec
    , lab_aspira.n_ovo
    -- order by lab_aspira.fec asc
    order by edad asc
");
?>