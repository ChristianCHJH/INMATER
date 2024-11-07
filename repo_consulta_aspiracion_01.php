<?php
	//consulta datos
	require("_database/db_tools.php");
	/*print(substr("abcdef", -2, 2));
	exit();*/
	/*if (isset($_POST["data"]) && !empty($_POST["data"])) {
		print($_POST["data"]);
	}
	exit();*/
	$idmedico = $tipaspi = $anio = $mes = $between = "";
	$cadena1 = $cadena2 = $cadena3 = $cadena4 = $cadena5 = "";
	if (isset($_POST["idmedico"]) && isset($_POST["anio"]) && isset($_POST["tipaspi"]) && !empty($_POST["tipaspi"])) {
		$idmedico = $_POST["idmedico"];
		$anio = $_POST["anio"];
		$tipaspi = $_POST["tipaspi"];
	} else {
		exit();
	}
	//
	// $mes = "01";
	for ($i=0; $i < 12; $i++) {
		$mes = "0".($i+1);
		$mes = substr($mes, -2, 2);
		//continue;
		$asp1 = $asp2 = $asp3 = $asp4 = $asp5 = 0;
		if ($anio == "") {
			$fechaini = "2000-01-01";
			$fechafin = "2999-12-31";
		} else {
			$fechaini = date('Y-m-d', strtotime(date($anio."-".$mes."-01")));
			$fechafin = date('Y-m-t', strtotime(date($anio."-".$mes."-01")));
		}
		/*print($fechaini);
		print("<br>");
		print($fechafin);
		print("<br>");
		continue;*/
	    //consulta para fechas de aspiracion
	    $rPaci = $db->prepare("
	        SELECT
	        coalesce(lab_aspira.tip, 'D') tip, hc_reprod.des_don, hc_reprod.des_dia, don_todo, p_cri, p_fiv, p_icsi, pago_extras
	        , hc_reprod.p_dni_het, coalesce(lab_andro_cap.id, 0) idesperma
	        , hc_paciente.ape, hc_paciente.nom, hc_reprod.f_asp
	        from hc_reprod
	        inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
	        left join lab_aspira ON hc_reprod.id = lab_aspira.rep and lab_aspira.estado is true
	        left join lab_andro_cap on lab_andro_cap.pro = lab_aspira.pro and lab_andro_cap.eliminado is false
	        where hc_reprod.estado = true and cancela=0 and unaccent(hc_reprod.med) ilike ('%$idmedico%') and CAST(hc_reprod.f_asp as date) between '$fechaini' and '$fechafin'
	        group by coalesce(lab_aspira.tip, 'D'), hc_reprod.des_don, hc_reprod.des_dia, don_todo, p_cri, p_fiv, p_icsi, pago_extras
	        , hc_reprod.p_dni_het, hc_paciente.ape, hc_paciente.nom
	        order by hc_reprod.f_asp
	    ");
	    $rPaci->execute();
	    $fecant;
		while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
			switch ($tipaspi) {
				case '1':
					if (
						($paci['tip'] == 'P' and $paci['p_cri'] <> 1)
						and !($paci['des_don'] == null and $paci['des_dia'] >= 1)
						and !($paci['des_don'] == null and $paci['des_dia'] === 0)
						and !($paci['des_don'] <> null and $paci['des_dia'] === 0)
						and !($paci['des_don'] <> null and $paci['des_dia'] >= 1)
					) {
						$asp1++;
					}
				break;
				case '2':
					if (
						($paci['tip'] == 'D' and $paci['don_todo'] == 1 and $paci['p_cri'] <> 1)
						or ($paci['tip'] == 'D' and $paci['p_dni_het'] <> "")
						or ($paci['tip'] == 'D' and $paci['idesperma'] <> 0)
					)
						$asp2++;
					break;
				case '3': if ($paci['tip'] == 'R') $asp3++; break;
				case '4': if ($paci['tip'] == 'P' and $paci['p_cri'] == 1) $asp4++; break;
				case '5': if ($paci['tip'] == 'D' and $paci['p_cri'] == 1) $asp5++; break;
			}
		}
		// print($asp1);
		switch ($tipaspi) {
			case '1': $cadena1.="|".(string)$asp1; break;
			case '2': $cadena2.="|".(string)$asp2; break;
			case '3': $cadena3.="|".(string)$asp3; break;
			case '4': $cadena4.="|".(string)$asp4; break;
			case '5': $cadena5.="|".(string)$asp5; break;
		}
	}
	//
	switch ($tipaspi) {
		case '1': print($cadena1); break;
		case '2': print($cadena2); break;
		case '3': print($cadena3); break;
		case '4': print($cadena4); break;
		case '5': print($cadena5); break;
	}
?>