<?php
	$upload_dir = "_upload/andro/";
	$img_movi = $_POST['img_movi'];
	$img_concen = $_POST['img_concen'];
	$img_mtotal = $_POST['img_mtotal'];
	$img_mclasi = $_POST['img_mclasi'];
	$p_dni = $_POST['p_dni'];
	$fec = $_POST['fec'];
	// 
	$img_movi = str_replace('data:image/png;base64,', '', $img_movi);
	$img_movi = str_replace(' ', '+', $img_movi);
	$data = base64_decode($img_movi);
	$img_movi_name = mktime()."_movi.png";
	$file = $upload_dir.$img_movi_name;
	$success = file_put_contents($file, $data);
	//
	$img_mtotal = str_replace('data:image/png;base64,', '', $img_mtotal);
	$img_mtotal = str_replace(' ', '+', $img_mtotal);
	$data = base64_decode($img_mtotal);
	$img_mtotal_name = mktime()."_mtotal.png";
	$file = $upload_dir.$img_mtotal_name;
	$success = file_put_contents($file, $data);
	// 
	$img_mclasi = str_replace('data:image/png;base64,', '', $img_mclasi);
	$img_mclasi = str_replace(' ', '+', $img_mclasi);
	$data = base64_decode($img_mclasi);
	$img_mclasi_name = mktime()."_mclasi.png";
	$file = $upload_dir.$img_mclasi_name;
	$success = file_put_contents($file, $data);
	// 
	$img_concen = str_replace('data:image/png;base64,', '', $img_concen);
	$img_concen = str_replace(' ', '+', $img_concen);
	$data = base64_decode($img_concen);
	$img_concen_name = mktime()."_concen.png";
	$file = $upload_dir.$img_concen_name;
	$success = file_put_contents($file, $data);
	//
	require("_database/database.php");
	global $db;
    $stmt = $db->prepare("
    	update lab_andro_esp
    	set img_movi=?, img_concen=?, img_mtotal=?, img_mclasi=?
    	where p_dni=? and fec=?");
    $stmt->execute(array($img_movi_name, $img_concen_name, $img_mtotal_name, $img_mclasi_name, $p_dni, $fec));
?>