<?php
	//consulta datos
	require("_database/db_tools.php");
	$anio = $mes = $between = $cadena = "";
	$total = $totalfivd5 = $totalfivd6 = $totalicsid5 = $totalicsid6 = $totalfiv = $totalicsi = 0;
	//
	if (isset($_POST["anio"]) && isset($_POST["tiprepo"]) && isset($_POST["between"])) {
		$anio = $_POST["anio"];
		$tiprepo = $_POST["tiprepo"];
		$between = $_POST["between"];
	} else {
		exit();
	}
	//
	for ($i=0; $i < 12; $i++) {
		$mes = "0".($i+1);
		$mes = substr($mes, -2, 2);
		//
		if ($anio == "") {
			$fechaini = "2000-01-01";
			$fechafin = "2999-12-31";
		} else {
			$fechaini = date('Y-m-d', strtotime(date($anio."-".$mes."-01")));
			$fechafin = date('Y-m-t', strtotime(date($anio."-".$mes."-01")));
		}
	    //consulta para fechas de aspiracion
	    $consulta = $db->prepare("
			select
			hc_reprod.id, hc_reprod.p_fiv, hc_reprod.p_icsi, lab_aspira.fec
			, count( case when lab_aspira_dias.d4f_cic = 'O' then true end ) totd5
			, count( case when lab_aspira_dias.d5f_cic = 'O' then true end ) totd6
			, (
				count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BT' then true end ) -- blasterd5
				+ count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BC' then true end ) -- blascavid5
				+ count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BE' then true end ) -- blasexpd5
				+ count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BHI' then true end ) -- blasinid5
				+ count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BH' then true end ) -- blashatd5
				+ count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BT' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) -- blasterd6				
				+ count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BC' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) -- blascavid6
				+ count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BE' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) -- blasexpd6
				+ count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BHI' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) -- blasinid6
				+ count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d6cel = 'BH' and lab_aspira_dias.d5cel not in ('BT', 'BE', 'BHI', 'BH') then true end ) -- blashatd6
			) total
			from hc_reprod
			inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'
			inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
			inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true$between and CAST(lab_aspira.fec as date) between '$fechaini' and '$fechafin'
			where hc_reprod.estado = true and hc_reprod.p_fiv >= 1 or hc_reprod.p_icsi >= 1
			group by hc_reprod.id, lab_aspira.fec
			order by lab_aspira.fec desc");
	    $consulta->execute();
	    $total=$totalfiv=$totalicsi=$totalfivd5=$totalfivd6=$totalicsid5=$totalicsid6=0;
		while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
			$total+=$data["total"];
			/*$totalfivd5+=$data["totd5"];
			$totalfivd6+=$data["totd6"];*/
			if ($data['p_fiv'] == 1 and $data['p_icsi'] == 1) {
				$totalfiv+=$data["total"];
				$totalfivd5+=$data["totd5"];
				$totalfivd6+=$data["totd6"];
			} else if ($data['p_fiv'] == 1) {
				$totalfiv+=$data["total"];
				$totalfivd5+=$data["totd5"];
				$totalfivd6+=$data["totd6"];
			} else if ($data['p_icsi'] == 1) {
				$totalicsi+=$data["total"];
				$totalicsid5+=$data["totd5"];
				$totalicsid6+=$data["totd6"];
			}
		}
		switch ($tiprepo) {
			// case 0: $cadena.="|".(string)(100); break;
			case 1:
				if ($totalfivd5 != 0) {
					$cadena.="|".(string)number_format(($totalfiv*100/$totalfivd5), 2);
				} else {
					$cadena.="|0.00";
				}
				break;
			case 2:
				if ($totalicsid5 != 0) {
					$cadena.="|".(string)number_format(($totalicsi*100/$totalicsid5), 2);
				} else {
					$cadena.="|0.00";
				}
				break;
			default: break;
		}		
	}
	//
	print($cadena);
?>