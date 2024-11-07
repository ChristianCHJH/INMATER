<?php
	//consulta datos
	require("_database/db_tools.php");
	$tipaspi = $between = "";
	$cadena1 = "";
	if (isset($_POST["between"]) && isset($_POST["tipaspi"]) && !empty($_POST["tipaspi"])) {
		$between = $_POST["between"];
		$tipaspi = $_POST["tipaspi"];
	} else {
		exit();
	}
	//
    $rPaci = $db->prepare("
	    select
		count(*) cantidad, 'userx'
		from usuario_log
		where 1 = 1$between
		group by userx");
    $rPaci->execute();
    $fecant;
	while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
		switch ($tipaspi) {
			case '1':
				$cadena1.="|".$paci["userx"];
			break;
			case '2':
				$cadena1.="|".$paci["cantidad"];
			break;
		}
	}
	//
	print($cadena1);
?>