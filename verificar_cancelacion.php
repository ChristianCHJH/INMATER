<?php
	date_default_timezone_set('America/Lima');
	// verificar si son mas de las 3 pm del dia de hoy
	if (strtotime(date("H:i:s")) >= strtotime('15:00:00')) {
		// verificar si el procedimiento indicado tiene aspiracion el dia de mañana
		if (isset($_POST["idpro"]) && !empty($_POST["idpro"])) {
			$idpro = $_POST["idpro"];
			require("_database/db_tools.php");
			$consulta = $db->prepare("SELECT f_asp from hc_reprod WHERE estado = true and id=?");
			$consulta->execute(array($idpro));
			$data = $consulta->fetch(PDO::FETCH_ASSOC);
			//
			if (empty($data["f_asp"])) {
				print("eliminar");
			} else {
				$dasp = explode("T", $data["f_asp"]);
				$fasp = $dasp[0];
				$hasp = $dasp[1];
				// print(date("Y-m-d", strtotime(date("Y-m-d")." + 1 days"))."<br>");
				// print(strtotime(date("Y-m-d")." + 1 days")."<br>");
				// print(strtotime($fasp)."<br>");
				// print(strtotime(date("Y-m-d").' + 1 days'));
				if (strtotime($fasp) == strtotime(date("Y-m-d")." + 1 days")) {
					print("anular");
				} else {
					print("eliminar");
				}
			}
		}
	} else {
		print("eliminar");
	}
?>