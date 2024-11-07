<?php
$rPaci = $db->prepare("
    select
    hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
    , hc_reprod.des_dia, hc_reprod.des_don
    , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
    , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
    , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
    , lab_aspira.fec
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' then true end ) blascavid5
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='a' and lab_aspira_dias.d5tro='a' then true end ) blascalaa
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='a' and lab_aspira_dias.d5tro='b' then true end ) blascalab
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='a' and lab_aspira_dias.d5tro='c' then true end ) blascalac
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='a' and lab_aspira_dias.d5tro='d' then true end ) blascalad
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='b' and lab_aspira_dias.d5tro='a' then true end ) blascalba
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='b' and lab_aspira_dias.d5tro='b' then true end ) blascalbb
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='b' and lab_aspira_dias.d5tro='c' then true end ) blascalbc
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='b' and lab_aspira_dias.d5tro='d' then true end ) blascalbd
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='c' and lab_aspira_dias.d5tro='a' then true end ) blascalca
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='c' and lab_aspira_dias.d5tro='b' then true end ) blascalcb
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='c' and lab_aspira_dias.d5tro='c' then true end ) blascalcc
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='c' and lab_aspira_dias.d5tro='d' then true end ) blascalcd
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='d' and lab_aspira_dias.d5tro='a' then true end ) blascalda
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='d' and lab_aspira_dias.d5tro='b' then true end ) blascaldb
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='d' and lab_aspira_dias.d5tro='c' then true end ) blascaldc
    , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' and lab_aspira_dias.d5mci='d' and lab_aspira_dias.d5tro='d' then true end ) blascaldd
    from hc_reprod
    inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'$between
    inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
    left join hc_paciente on hc_paciente.dni = hc_reprod.dni
    left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
    where hc_reprod.estado = true and hc_reprod.p_fiv >= 1
    group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
    , hc_reprod.des_dia, hc_reprod.des_don
    , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
    , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
    , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
    , lab_aspira.fec
    having count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' then true end ) > 0
    order by lab_aspira.fec asc
");
?>