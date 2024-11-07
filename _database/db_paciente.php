<?php
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

function traer_paciente($numero_documento) {
	global $db;
	$stmt = $db->prepare("SELECT * FROM hc_paciente WHERE dni=?;");
	$stmt->execute([$numero_documento]);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}
function traer_paciente_andro($numero_documento) {
	global $db;
	$stmt = $db->prepare("SELECT p_ape ape, p_nom nom FROM hc_pareja WHERE p_dni=?;");
	$stmt->execute([$numero_documento]);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

function paciente_autenticar($user, $password)
{
    if (isset($user) && !empty($user) and isset($password) && !empty($password))
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM hc_paciente_accesos WHERE dni=? AND acceso=? AND estado=1");
        $stmt->execute(array($user, $password));

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function actualizar_paciente($dni_id, $dni, $ape, $nom, $sede, $medios_comunicacion_id, $don, $login=""){
	global $db;
	// verificar si existe
    $dni_id = !empty($dni_id) ? $dni_id : '';
    $dni = !empty($dni) ? $dni : '';
    $nom = !empty($nom) ? $nom : '';
    $ape = !empty($ape) ? $ape : '';
    $sede = !empty($sede) ? intval($sede) : 0;
    $don = !empty($don) ? $don : '';
    $medios_comunicacion_id = !empty($medios_comunicacion_id) ? $medios_comunicacion_id : 0;


    $stmt = $db->prepare("UPDATE hc_paciente
    SET dni=?, ape=?, nom=?, idsedes=?, medios_comunicacion_id=?, don=? ,iduserupdate=?,updatex=?
    WHERE dni=?
    ");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute(array($dni,$ape,$nom,$sede,$medios_comunicacion_id,$don,$login, $hora_actual,$dni_id));
    $log_Paciente = $db->prepare(
        "INSERT INTO appinmater_log.hc_paciente (
                    dni, pass, sta, med, tip, nom, ape, fnac, tcel,
                    tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                    san, don, raz, talla, peso, rem, nota, fec, idsedes,
                    idusercreate, createdate, 
                    action
            )
        SELECT 
            dni, pass, sta, med, tip, nom, ape, fnac, tcel, 
            tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
            san, don, raz, talla, peso, rem, nota, fec, idsedes,
            iduserupdate,updatex, 'U'
        FROM appinmater_modulo.hc_paciente
        WHERE dni=?");
    $log_Paciente->execute(array($dni_id));

    header("Location: man_paciente_edit.php?id=".$dni_id."&historia=1");
    exit();
}

function paciente_ingresar_log($data)
{
    global $db;
    $stmt = $db->prepare("insert into usuario_log (user, idusercreate, createdate) values (?, ?, ?)");
    $stmt->execute(array($data['userx'], $data['idusercreate'], $data['createdate'], ));
}

function getSero($dni, $fec, $tipopaciente = 1)
{
    global $db;

    $query = "SELECT 
        CASE WHEN s1.hbs > 0 THEN s1.hbs WHEN s2.hbs > 0 THEN s2.hbs WHEN s3.hbs > 0 THEN s3.hbs WHEN s4.hbs > 0 THEN s4.hbs WHEN s5.hbs > 0 THEN s5.hbs WHEN s6.hbs > 0 THEN s6.hbs WHEN s7.hbs > 0 THEN s7.hbs WHEN s8.hbs > 0 THEN s8.hbs WHEN s9.hbs > 0 THEN s9.hbs ELSE 0
        END as hbs,
        CASE WHEN s1.hbs > 0 THEN s1.idusercreate WHEN s2.hbs > 0 THEN s2.idusercreate WHEN s3.hbs > 0 THEN s3.idusercreate WHEN s4.hbs > 0 THEN s4.idusercreate WHEN s5.hbs > 0 THEN s5.idusercreate WHEN s6.hbs > 0 THEN s6.idusercreate WHEN s7.hbs > 0 THEN s7.idusercreate WHEN s8.hbs > 0 THEN s8.idusercreate WHEN s9.hbs > 0 THEN s9.idusercreate ELSE ''
        END as hbsidusercreate,
        CASE WHEN s1.hbs > 0 THEN s1.fec WHEN s2.hbs > 0 THEN s2.fec WHEN s3.hbs > 0 THEN s3.fec WHEN s4.hbs > 0 THEN s4.fec WHEN s5.hbs > 0 THEN s5.fec WHEN s6.hbs > 0 THEN s6.fec WHEN s7.hbs > 0 THEN s7.fec WHEN s8.hbs > 0 THEN s8.fec WHEN s9.hbs > 0 THEN s9.fec
        END as hbsfec,
        CASE WHEN 
            (CASE WHEN s1.hbs > 0 THEN s1.fec WHEN s2.hbs > 0 THEN s2.fec WHEN s3.hbs > 0 THEN s3.fec WHEN s4.hbs > 0 THEN s4.fec WHEN s5.hbs > 0 THEN s5.fec WHEN s6.hbs > 0 THEN s6.fec WHEN s7.hbs > 0 THEN s7.fec WHEN s8.hbs > 0 THEN s8.fec WHEN s9.hbs > 0 THEN s9.fec END) IS NULL 
            OR CAST((CASE WHEN s1.hbs > 0 THEN s1.fec WHEN s2.hbs > 0 THEN s2.fec WHEN s3.hbs > 0 THEN s3.fec WHEN s4.hbs > 0 THEN s4.fec WHEN s5.hbs > 0 THEN s5.fec WHEN s6.hbs > 0 THEN s6.fec WHEN s7.hbs > 0 THEN s7.fec WHEN s8.hbs > 0 THEN s8.fec WHEN s9.hbs > 0 THEN s9.fec END) AS DATE) >= (CAST('$fec' as date) - INTERVAL '102 days')
            THEN false
        ELSE true
        END as hbsvencido,
        CASE WHEN s1.hcv > 0 THEN s1.hcv WHEN s2.hcv > 0 THEN s2.hcv WHEN s3.hcv > 0 THEN s3.hcv WHEN s4.hcv > 0 THEN s4.hcv WHEN s5.hcv > 0 THEN s5.hcv WHEN s6.hcv > 0 THEN s6.hcv WHEN s7.hcv > 0 THEN s7.hcv WHEN s8.hcv > 0 THEN s8.hcv WHEN s9.hcv > 0 THEN s9.hcv ELSE 0
        END as hcv,
        CASE WHEN s1.hcv > 0 THEN s1.idusercreate WHEN s2.hcv > 0 THEN s2.idusercreate WHEN s3.hcv > 0 THEN s3.idusercreate WHEN s4.hcv > 0 THEN s4.idusercreate WHEN s5.hcv > 0 THEN s5.idusercreate WHEN s6.hcv > 0 THEN s6.idusercreate WHEN s7.hcv > 0 THEN s7.idusercreate WHEN s8.hcv > 0 THEN s8.idusercreate WHEN s9.hcv > 0 THEN s9.idusercreate ELSE ''
        END as hcvidusercreate,
        CASE WHEN s1.hcv > 0 THEN s1.fec WHEN s2.hcv > 0 THEN s2.fec WHEN s3.hcv > 0 THEN s3.fec WHEN s4.hcv > 0 THEN s4.fec WHEN s5.hcv > 0 THEN s5.fec WHEN s6.hcv > 0 THEN s6.fec WHEN s7.hcv > 0 THEN s7.fec WHEN s8.hcv > 0 THEN s8.fec WHEN s9.hcv > 0 THEN s9.fec
        END as hcvfec,
        CASE WHEN 
            (CASE WHEN s1.hcv > 0 THEN s1.fec WHEN s2.hcv > 0 THEN s2.fec WHEN s3.hcv > 0 THEN s3.fec WHEN s4.hcv > 0 THEN s4.fec WHEN s5.hcv > 0 THEN s5.fec WHEN s6.hcv > 0 THEN s6.fec WHEN s7.hcv > 0 THEN s7.fec WHEN s8.hcv > 0 THEN s8.fec WHEN s9.hcv > 0 THEN s9.fec END) IS NULL 
            OR CAST((CASE WHEN s1.hcv > 0 THEN s1.fec WHEN s2.hcv > 0 THEN s2.fec WHEN s3.hcv > 0 THEN s3.fec WHEN s4.hcv > 0 THEN s4.fec WHEN s5.hcv > 0 THEN s5.fec WHEN s6.hcv > 0 THEN s6.fec WHEN s7.hcv > 0 THEN s7.fec WHEN s8.hcv > 0 THEN s8.fec WHEN s9.hcv > 0 THEN s9.fec END) AS DATE) >= (CAST('$fec' as date) - INTERVAL '102 days')
            THEN false
        ELSE true
        END as hcvvencido,
        CASE WHEN s1.hiv > 0 THEN s1.hiv WHEN s2.hiv > 0 THEN s2.hiv WHEN s3.hiv > 0 THEN s3.hiv WHEN s4.hiv > 0 THEN s4.hiv WHEN s5.hiv > 0 THEN s5.hiv WHEN s6.hiv > 0 THEN s6.hiv WHEN s7.hiv > 0 THEN s7.hiv WHEN s8.hiv > 0 THEN s8.hiv WHEN s9.hiv > 0 THEN s9.hiv ELSE 0
        END as hiv,
        CASE WHEN s1.hiv > 0 THEN s1.idusercreate WHEN s2.hiv > 0 THEN s2.idusercreate WHEN s3.hiv > 0 THEN s3.idusercreate WHEN s4.hiv > 0 THEN s4.idusercreate WHEN s5.hiv > 0 THEN s5.idusercreate WHEN s6.hiv > 0 THEN s6.idusercreate WHEN s7.hiv > 0 THEN s7.idusercreate WHEN s8.hiv > 0 THEN s8.idusercreate WHEN s9.hiv > 0 THEN s9.idusercreate ELSE ''
        END as hividusercreate,
        CASE WHEN s1.hiv > 0 THEN s1.fec WHEN s2.hiv > 0 THEN s2.fec WHEN s3.hiv > 0 THEN s3.fec WHEN s4.hiv > 0 THEN s4.fec WHEN s5.hiv > 0 THEN s5.fec WHEN s6.hiv > 0 THEN s6.fec WHEN s7.hiv > 0 THEN s7.fec WHEN s8.hiv > 0 THEN s8.fec WHEN s9.hiv > 0 THEN s9.fec
        END as hivfec,
        CASE WHEN 
            (CASE WHEN s1.hiv > 0 THEN s1.fec WHEN s2.hiv > 0 THEN s2.fec WHEN s3.hiv > 0 THEN s3.fec WHEN s4.hiv > 0 THEN s4.fec WHEN s5.hiv > 0 THEN s5.fec WHEN s6.hiv > 0 THEN s6.fec WHEN s7.hiv > 0 THEN s7.fec WHEN s8.hiv > 0 THEN s8.fec WHEN s9.hiv > 0 THEN s9.fec END) IS NULL 
            OR CAST((CASE WHEN s1.hiv > 0 THEN s1.fec WHEN s2.hiv > 0 THEN s2.fec WHEN s3.hiv > 0 THEN s3.fec WHEN s4.hiv > 0 THEN s4.fec WHEN s5.hiv > 0 THEN s5.fec WHEN s6.hiv > 0 THEN s6.fec WHEN s7.hiv > 0 THEN s7.fec WHEN s8.hiv > 0 THEN s8.fec WHEN s9.hiv > 0 THEN s9.fec END) AS DATE) >= (CAST('$fec' as date) - INTERVAL '102 days')
            THEN false
        ELSE true
        END as hivvencido,
        CASE WHEN s1.rpr > 0 THEN s1.rpr WHEN s2.rpr > 0 THEN s2.rpr WHEN s3.rpr > 0 THEN s3.rpr WHEN s4.rpr > 0 THEN s4.rpr WHEN s5.rpr > 0 THEN s5.rpr WHEN s6.rpr > 0 THEN s6.rpr WHEN s7.rpr > 0 THEN s7.rpr WHEN s8.rpr > 0 THEN s8.rpr WHEN s9.rpr > 0 THEN s9.rpr ELSE 0
        END as rpr,
        CASE WHEN s1.rpr > 0 THEN s1.idusercreate WHEN s2.rpr > 0 THEN s2.idusercreate WHEN s3.rpr > 0 THEN s3.idusercreate WHEN s4.rpr > 0 THEN s4.idusercreate WHEN s5.rpr > 0 THEN s5.idusercreate WHEN s6.rpr > 0 THEN s6.idusercreate WHEN s7.rpr > 0 THEN s7.idusercreate WHEN s8.rpr > 0 THEN s8.idusercreate WHEN s9.rpr > 0 THEN s9.idusercreate ELSE ''
        END as rpridusercreate,
        CASE WHEN s1.rpr > 0 THEN s1.fec WHEN s2.rpr > 0 THEN s2.fec WHEN s3.rpr > 0 THEN s3.fec WHEN s4.rpr > 0 THEN s4.fec WHEN s5.rpr > 0 THEN s5.fec WHEN s6.rpr > 0 THEN s6.fec WHEN s7.rpr > 0 THEN s7.fec WHEN s8.rpr > 0 THEN s8.fec WHEN s9.rpr > 0 THEN s9.fec
        END as rprfec,
        CASE WHEN 
            (CASE WHEN s1.rpr > 0 THEN s1.fec WHEN s2.rpr > 0 THEN s2.fec WHEN s3.rpr > 0 THEN s3.fec WHEN s4.rpr > 0 THEN s4.fec WHEN s5.rpr > 0 THEN s5.fec WHEN s6.rpr > 0 THEN s6.fec WHEN s7.rpr > 0 THEN s7.fec WHEN s8.rpr > 0 THEN s8.fec WHEN s9.rpr > 0 THEN s9.fec END) IS NULL 
            OR CAST((CASE WHEN s1.rpr > 0 THEN s1.fec WHEN s2.rpr > 0 THEN s2.fec WHEN s3.rpr > 0 THEN s3.fec WHEN s4.rpr > 0 THEN s4.fec WHEN s5.rpr > 0 THEN s5.fec WHEN s6.rpr > 0 THEN s6.fec WHEN s7.rpr > 0 THEN s7.fec WHEN s8.rpr > 0 THEN s8.fec WHEN s9.rpr > 0 THEN s9.fec END) AS DATE) >= (CAST('$fec' as date) - INTERVAL '102 days')
            THEN false
        ELSE true
        END as rprvencido,
        CASE WHEN s1.rub > 0 THEN s1.rub WHEN s2.rub > 0 THEN s2.rub WHEN s3.rub > 0 THEN s3.rub WHEN s4.rub > 0 THEN s4.rub WHEN s5.rub > 0 THEN s5.rub WHEN s6.rub > 0 THEN s6.rub WHEN s7.rub > 0 THEN s7.rub WHEN s8.rub > 0 THEN s8.rub WHEN s9.rub > 0 THEN s9.rub ELSE 0
        END as rub,
        CASE WHEN s1.rub > 0 THEN s1.idusercreate WHEN s2.rub > 0 THEN s2.idusercreate WHEN s3.rub > 0 THEN s3.idusercreate WHEN s4.rub > 0 THEN s4.idusercreate WHEN s5.rub > 0 THEN s5.idusercreate WHEN s6.rub > 0 THEN s6.idusercreate WHEN s7.rub > 0 THEN s7.idusercreate WHEN s8.rub > 0 THEN s8.idusercreate WHEN s9.rub > 0 THEN s9.idusercreate ELSE ''
        END as rubidusercreate,
        CASE WHEN s1.rub > 0 THEN s1.fec WHEN s2.rub > 0 THEN s2.fec WHEN s3.rub > 0 THEN s3.fec WHEN s4.rub > 0 THEN s4.fec WHEN s5.rub > 0 THEN s5.fec WHEN s6.rub > 0 THEN s6.fec WHEN s7.rub > 0 THEN s7.fec WHEN s8.rub > 0 THEN s8.fec WHEN s9.rub > 0 THEN s9.fec
        END as rubfec,
        CASE WHEN 
            (CASE WHEN s1.rub > 0 THEN s1.fec WHEN s2.rub > 0 THEN s2.fec WHEN s3.rub > 0 THEN s3.fec WHEN s4.rub > 0 THEN s4.fec WHEN s5.rub > 0 THEN s5.fec WHEN s6.rub > 0 THEN s6.fec WHEN s7.rub > 0 THEN s7.fec WHEN s8.rub > 0 THEN s8.fec WHEN s9.rub > 0 THEN s9.fec END) IS NULL 
            OR CAST((CASE WHEN s1.rub > 0 THEN s1.fec WHEN s2.rub > 0 THEN s2.fec WHEN s3.rub > 0 THEN s3.fec WHEN s4.rub > 0 THEN s4.fec WHEN s5.rub > 0 THEN s5.fec WHEN s6.rub > 0 THEN s6.fec WHEN s7.rub > 0 THEN s7.fec WHEN s8.rub > 0 THEN s8.fec WHEN s9.rub > 0 THEN s9.fec END) AS DATE) >= (CAST('$fec' as date) - INTERVAL '102 days')
            THEN false
        ELSE true
        END as rubvencido,
        CASE WHEN s1.tox > 0 THEN s1.tox WHEN s2.tox > 0 THEN s2.tox WHEN s3.tox > 0 THEN s3.tox WHEN s4.tox > 0 THEN s4.tox WHEN s5.tox > 0 THEN s5.tox WHEN s6.tox > 0 THEN s6.tox WHEN s7.tox > 0 THEN s7.tox WHEN s8.tox > 0 THEN s8.tox WHEN s9.tox > 0 THEN s9.tox ELSE 0
        END as tox,
        CASE WHEN s1.tox > 0 THEN s1.idusercreate WHEN s2.tox > 0 THEN s2.idusercreate WHEN s3.tox > 0 THEN s3.idusercreate WHEN s4.tox > 0 THEN s4.idusercreate WHEN s5.tox > 0 THEN s5.idusercreate WHEN s6.tox > 0 THEN s6.idusercreate WHEN s7.tox > 0 THEN s7.idusercreate WHEN s8.tox > 0 THEN s8.idusercreate WHEN s9.tox > 0 THEN s9.idusercreate ELSE ''
        END as toxidusercreate,
        CASE WHEN s1.tox > 0 THEN s1.fec WHEN s2.tox > 0 THEN s2.fec WHEN s3.tox > 0 THEN s3.fec WHEN s4.tox > 0 THEN s4.fec WHEN s5.tox > 0 THEN s5.fec WHEN s6.tox > 0 THEN s6.fec WHEN s7.tox > 0 THEN s7.fec WHEN s8.tox > 0 THEN s8.fec WHEN s9.tox > 0 THEN s9.fec
        END as toxfec,
        CASE WHEN 
            (CASE WHEN s1.tox > 0 THEN s1.fec WHEN s2.tox > 0 THEN s2.fec WHEN s3.tox > 0 THEN s3.fec WHEN s4.tox > 0 THEN s4.fec WHEN s5.tox > 0 THEN s5.fec WHEN s6.tox > 0 THEN s6.fec WHEN s7.tox > 0 THEN s7.fec WHEN s8.tox > 0 THEN s8.fec WHEN s9.tox > 0 THEN s9.fec END) IS NULL 
            OR CAST((CASE WHEN s1.tox > 0 THEN s1.fec WHEN s2.tox > 0 THEN s2.fec WHEN s3.tox > 0 THEN s3.fec WHEN s4.tox > 0 THEN s4.fec WHEN s5.tox > 0 THEN s5.fec WHEN s6.tox > 0 THEN s6.fec WHEN s7.tox > 0 THEN s7.fec WHEN s8.tox > 0 THEN s8.fec WHEN s9.tox > 0 THEN s9.fec END) AS DATE) >= (CAST('$fec' as date) - INTERVAL '102 days')
            THEN false
        ELSE true
        END as toxvencido,
        CASE WHEN s1.cla_g > 0 THEN s1.cla_g WHEN s2.cla_g > 0 THEN s2.cla_g WHEN s3.cla_g > 0 THEN s3.cla_g WHEN s4.cla_g > 0 THEN s4.cla_g WHEN s5.cla_g > 0 THEN s5.cla_g WHEN s6.cla_g > 0 THEN s6.cla_g WHEN s7.cla_g > 0 THEN s7.cla_g WHEN s8.cla_g > 0 THEN s8.cla_g WHEN s9.cla_g > 0 THEN s9.cla_g ELSE 0
        END as cla_g,
        CASE WHEN s1.cla_g > 0 THEN s1.idusercreate WHEN s2.cla_g > 0 THEN s2.idusercreate WHEN s3.cla_g > 0 THEN s3.idusercreate WHEN s4.cla_g > 0 THEN s4.idusercreate WHEN s5.cla_g > 0 THEN s5.idusercreate WHEN s6.cla_g > 0 THEN s6.idusercreate WHEN s7.cla_g > 0 THEN s7.idusercreate WHEN s8.cla_g > 0 THEN s8.idusercreate WHEN s9.cla_g > 0 THEN s9.idusercreate ELSE ''
        END as cla_gidusercreate,
        CASE WHEN s1.cla_g > 0 THEN s1.fec WHEN s2.cla_g > 0 THEN s2.fec WHEN s3.cla_g > 0 THEN s3.fec WHEN s4.cla_g > 0 THEN s4.fec WHEN s5.cla_g > 0 THEN s5.fec WHEN s6.cla_g > 0 THEN s6.fec WHEN s7.cla_g > 0 THEN s7.fec WHEN s8.cla_g > 0 THEN s8.fec WHEN s9.cla_g > 0 THEN s9.fec
        END as cla_gfec,
        CASE WHEN 
            (CASE WHEN s1.cla_g > 0 THEN s1.fec WHEN s2.cla_g > 0 THEN s2.fec WHEN s3.cla_g > 0 THEN s3.fec WHEN s4.cla_g > 0 THEN s4.fec WHEN s5.cla_g > 0 THEN s5.fec WHEN s6.cla_g > 0 THEN s6.fec WHEN s7.cla_g > 0 THEN s7.fec WHEN s8.cla_g > 0 THEN s8.fec WHEN s9.cla_g > 0 THEN s9.fec END) IS NULL 
            OR CAST((CASE WHEN s1.cla_g > 0 THEN s1.fec WHEN s2.cla_g > 0 THEN s2.fec WHEN s3.cla_g > 0 THEN s3.fec WHEN s4.cla_g > 0 THEN s4.fec WHEN s5.cla_g > 0 THEN s5.fec WHEN s6.cla_g > 0 THEN s6.fec WHEN s7.cla_g > 0 THEN s7.fec WHEN s8.cla_g > 0 THEN s8.fec WHEN s9.cla_g > 0 THEN s9.fec END) AS DATE) >= (CAST('$fec' as date) - INTERVAL '102 days')
            THEN false
        ELSE true
        END as cla_gvencido,
        CASE WHEN s1.cla_m > 0 THEN s1.cla_m WHEN s2.cla_m > 0 THEN s2.cla_m WHEN s3.cla_m > 0 THEN s3.cla_m WHEN s4.cla_m > 0 THEN s4.cla_m WHEN s5.cla_m > 0 THEN s5.cla_m WHEN s6.cla_m > 0 THEN s6.cla_m WHEN s7.cla_m > 0 THEN s7.cla_m WHEN s8.cla_m > 0 THEN s8.cla_m WHEN s9.cla_m > 0 THEN s9.cla_m ELSE 0
        END as cla_m,
        CASE WHEN s1.cla_m > 0 THEN s1.idusercreate WHEN s2.cla_m > 0 THEN s2.idusercreate WHEN s3.cla_m > 0 THEN s3.idusercreate WHEN s4.cla_m > 0 THEN s4.idusercreate WHEN s5.cla_m > 0 THEN s5.idusercreate WHEN s6.cla_m > 0 THEN s6.idusercreate WHEN s7.cla_m > 0 THEN s7.idusercreate WHEN s8.cla_m > 0 THEN s8.idusercreate WHEN s9.cla_m > 0 THEN s9.idusercreate ELSE ''
        END as cla_midusercreate,
        CASE WHEN s1.cla_m > 0 THEN s1.fec WHEN s2.cla_m > 0 THEN s2.fec WHEN s3.cla_m > 0 THEN s3.fec WHEN s4.cla_m > 0 THEN s4.fec WHEN s5.cla_m > 0 THEN s5.fec WHEN s6.cla_m > 0 THEN s6.fec WHEN s7.cla_m > 0 THEN s7.fec WHEN s8.cla_m > 0 THEN s8.fec WHEN s9.cla_m > 0 THEN s9.fec
        END as cla_mfec,
        CASE WHEN 
            (CASE WHEN s1.cla_m > 0 THEN s1.fec WHEN s2.cla_m > 0 THEN s2.fec WHEN s3.cla_m > 0 THEN s3.fec WHEN s4.cla_m > 0 THEN s4.fec WHEN s5.cla_m > 0 THEN s5.fec WHEN s6.cla_m > 0 THEN s6.fec WHEN s7.cla_m > 0 THEN s7.fec WHEN s8.cla_m > 0 THEN s8.fec WHEN s9.cla_m > 0 THEN s9.fec END) IS NULL 
            OR CAST((CASE WHEN s1.cla_m > 0 THEN s1.fec WHEN s2.cla_m > 0 THEN s2.fec WHEN s3.cla_m > 0 THEN s3.fec WHEN s4.cla_m > 0 THEN s4.fec WHEN s5.cla_m > 0 THEN s5.fec WHEN s6.cla_m > 0 THEN s6.fec WHEN s7.cla_m > 0 THEN s7.fec WHEN s8.cla_m > 0 THEN s8.fec WHEN s9.cla_m > 0 THEN s9.fec END) AS DATE) >= (CAST('$fec' as date) - INTERVAL '102 days')
            THEN false
        ELSE true
        END as cla_mvencido, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente, s1.id, s1.iduserupdate
        FROM hc_antece_p_sero s1
        LEFT JOIN hc_paciente p on p.dni = s1.iduserupdate
        LEFT JOIN (SELECT * FROM hc_antece_p_sero WHERE estado = 1 AND p_dni = '$dni' ORDER BY fec DESC LIMIT 2)
            s2 ON s1.fec > s2.fec
        LEFT JOIN (SELECT * FROM hc_antece_p_sero WHERE estado = 1 AND p_dni = '$dni' ORDER BY fec DESC LIMIT 3)
            s3 ON s2.fec > s3.fec
    
        LEFT JOIN (SELECT * FROM hc_antece_p_sero WHERE estado = 1 AND p_dni = '$dni' ORDER BY fec DESC LIMIT 4)
            s4 ON s3.fec > s4.fec
    
        LEFT JOIN (SELECT * FROM hc_antece_p_sero WHERE estado = 1 AND p_dni = '$dni' ORDER BY fec DESC LIMIT 5)
            s5 ON s4.fec > s5.fec
    
        LEFT JOIN (SELECT * FROM hc_antece_p_sero WHERE estado = 1 AND p_dni = '$dni' ORDER BY fec DESC LIMIT 6)
            s6 ON s5.fec > s6.fec
    
        LEFT JOIN (SELECT * FROM hc_antece_p_sero WHERE estado = 1 AND p_dni = '$dni' ORDER BY fec DESC LIMIT 7)
            s7 ON s6.fec > s7.fec
    
        LEFT JOIN (SELECT * FROM hc_antece_p_sero WHERE estado = 1 AND p_dni = '$dni' ORDER BY fec DESC LIMIT 8)
            s8 ON s7.fec > s8.fec
    
        LEFT JOIN (SELECT * FROM hc_antece_p_sero WHERE estado = 1 AND p_dni = '$dni' ORDER BY fec DESC LIMIT 9)
            s9 ON s8.fec > s9.fec
    
        WHERE s1.p_dni = '$dni' AND s1.estado = 1 AND s1.tipo_paciente = $tipopaciente
        ORDER BY s1.id DESC
        LIMIT 1";

    $Sero = $db->prepare($query);

    $Sero->execute();

    return $Sero->fetch(PDO::FETCH_ASSOC);
}

function print_sero($tipo, $nombre) {
    global $repro;
    global $sero;    

    $link_sero = 'archivos_hcpacientes.php?idArchivo=sero_' . $repro['dni'] . "_" . $sero[$tipo.'fec'];

    return '<tr>
        <td>
            '.$nombre.'
        </td>
        <td>
            '.($sero[$tipo] == 1 ? 'Positivo' : ($sero[$tipo] == 2 ? 'Negativo' : '-')).'
        </td>
        <td>
            '.($sero[$tipo] == 0 || !file_exists('analisis/sero_' . $repro['dni'] . "_" . $sero[$tipo.'fec'] . '.pdf') ? '-' : '<a href="'.$link_sero.'" target="_blank">Ver/Descargar</a>') .'
        </td>
        <td>
            '.$sero[$tipo.'fec'].'
        </td>
        <td>
            '.($sero[$tipo] == 0 ? '-' : (!$sero[$tipo.'vencido'] ? 'Vigente' : '<b>Vencido</b>')).'
        </td>
    </tr>';
}

function print_sero_1($tipo, $nombre) {
    global $repro;
    global $sero;

    $link_sero = 'archivos_hcpacientes.php?idArchivo=sero_' . $repro['dni'] . "_" . $sero[$tipo.'fec'];

    return '<tr>
        <td>
            '.$nombre.'
        </td>
        <td>
            '.($sero[$tipo] == 1 ? 'Positivo' : ($sero[$tipo] == 2 ? 'Negativo' : '-')).'
        </td>
        <td>
            '.($sero[$tipo] == 0 || !file_exists('analisis/sero_' . $repro['dni'] . "_" . $sero[$tipo.'fec'] . '.pdf') ? '-' : '<a href="'.$link_sero.'" target="_blank">Ver/Descargar</a>') .'
        </td>
        <td>
            '.$sero[$tipo.'fec'].'
        </td>
        <td>
            '.($sero[$tipo] == 0 ? '-' : (!$sero[$tipo.'vencido'] ? 'Vigente' : '<b>Vencido</b>')).'
        </td>
        <td>
            '.@$sero['iduserupdate'].'
        </td>
    </tr>';
}
?>