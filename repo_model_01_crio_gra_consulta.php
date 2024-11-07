<?php
	//consulta datos
	require("_database/db_tools.php");
	$tipaspi = $between = "";
	$cadena1 = $cadena2 = $cadena3 = $cadena4 = $cadena5 = "";
	$edad=-1;
	$total=$criototal=$crioprom=0;
	if (isset($_POST["between"]) && isset($_POST["tipaspi"]) && !empty($_POST["tipaspi"])) {
		$between = $_POST["between"];
		$tipaspi = $_POST["tipaspi"];
	} else {
		exit();
	}
	//
	for ($i=0; $i < 1; $i++) {
	    $rPaci = $db->prepare("
    select
        coalesce(floor(extract(year from age(lab_aspira.fec, hc_paciente.fnac))), 0) edad
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
        , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom, floor(extract(year from age(lab_aspira.fec, hc_paciente.fnac)))
        , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
        , upper(substring(hc_reprod.con1_med, 1, nullif(strpos(hc_reprod.con1_med, '|'), 0) - 1))
        , upper(substring(hc_reprod.con2_med, 1, nullif(strpos(hc_reprod.con2_med, '|'), 0) - 1))
        , upper(substring(hc_reprod.con3_med, 1, nullif(strpos(hc_reprod.con3_med, '|'), 0) - 1))
        , upper(substring(hc_reprod.con4_med, 1, nullif(strpos(hc_reprod.con4_med, '|'), 0) - 1))
        , upper(substring(hc_reprod.con5_med, 1, nullif(strpos(hc_reprod.con5_med, '|'), 0) - 1))
        , hc_reprod.con1_med, hc_reprod.con2_med, hc_reprod.con3_med, hc_reprod.con4_med, hc_reprod.con5_med
        , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi,
		hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
        , lab_aspira.fec
        , lab_aspira.n_ovo
    -- order by lab_aspira.fec asc
    order by edad asc");
$rPaci->execute();


	    $fecant;
		while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
			switch ($tipaspi) {
				case '1':
					if ($edad !=  $paci['edad']) {
						$edad = $paci['edad'];
						$cadena1.="|".(string)$edad;
					}
				break;
				case '2':
					if ($edad !=  $paci['edad']) {
						// $total=$criototal=$crioprom=0;
						if ($edad != -1) {
							$edad=$paci['edad'];
							$cadena2.="|".(string)(number_format($criototal/($total), 2));
							$total=1;
							$criototal=$paci['crio'];
						} else {
							$edad=$paci['edad'];
							$total++;
							$criototal+=$paci['crio'];
						}
					} else {
						$total++;
						$criototal+=$paci['crio'];
					}
				break;
			}
		}
	}
	//
	switch ($tipaspi) {
		case '1': print($cadena1); break;
		case '2': print($cadena2.="|".(string)(number_format($criototal/($total), 2))); break;
		case '3': print($cadena3); break;
		case '4': print($cadena4); break;
		case '5': print($cadena5); break;
	}
?>