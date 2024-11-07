<?php
date_default_timezone_set('America/Lima');
require("database.php");

// consulta datos de paciente
function consultaDatosReproduccion($id)
{
    global $db;
    $data = null;

    $stmt = $db->prepare("
    select
    id, dni numerodocumentopaciente, p_dni numerodocumentopareja, med codigomedico, p_fiv fiv, p_icsi icsi,
    pago_extras extras
    from hc_reprod
    where estado = true and id=?");
    $stmt->execute(array($id));
    if ($stmt->rowCount() > 0)
    {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $data;
}

// desarrollo de laboratorio
function consultaDesarrolloEmbrionario($id)
{
    global $db;
    $data = null;

    $stmt = $db->prepare("
    select
    rep id, pro repro, emb5 embriologodia5, emb6 embriologodia6, fec5, tip tipo
    from lab_aspira
    where rep=? and estado is true");
    $stmt->execute(array($id));
    if ($stmt->rowCount() > 0)
    {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $data;
}

// detalle desarrollo de laboratorio
function consultaDesarrolloEmbrionarioDetalle($repro)
{
    global $db;
    $data = null;

    $stmt = $db->prepare("
    select
    pro, ovo
    from lab_aspira_dias and estado is true
    where pro=?");
    $stmt->execute(array($id));
    if ($stmt->rowCount() > 0)
    {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $data;
}

// consulta datos de paciente
function consultaDatosPaciente($tipodocumento, $numerodocumento)
{
    global $db;
    $data = null;

    $stmt = $db->prepare("
    SELECT
    tip tipodocumentoidentidad, dni numerodocumentoidentidad, ape apellidos, nom nombres, fnac fechanacimiento
    , round((now()::date - fnac::date)/ 365) edad
    , tcel celular, don tipo
    from hc_paciente
    where (tip=? or tip=? or tip=? or tip=?) and dni=?");
    $stmt->execute(array($tipodocumento, "PAS", "CE", "CEX", $numerodocumento));
    if ($stmt->rowCount() > 0)
    {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $data;
}

// consulta datos de la pareja
function consultaDatosPareja($tipodocumento, $numerodocumento)
{
    global $db;
    $data = null;

    $stmt = $db->prepare("
    select
    p_tip tipodocumentoidentidad, p_dni numerodocumentoidentidad, p_ape apellidos, p_nom nombres
    , p_fnac fechanacimiento, round((now()::date - p_fnac::date)/ 365) edad
    , p_tcel celular
    from hc_pareja
    where p_dni=?");
    $stmt->execute(array($numerodocumento));
    if ($stmt->rowCount() > 0)
    {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $data;
}

// consulta datos de medico
function consultaDatosMedico($codigo)
{
    global $db;
    $data = null;

    $stmt = $db->prepare("select nombre nombrescompletos from man_medico where codigo=?");
    $stmt->execute(array($codigo));
    if ($stmt->rowCount() > 0)
    {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $data;
}

// consulta datos de embriologo
function consultaDatosEmbriologo($id)
{
    global $db;
    $data = null;

    $stmt = $db->prepare("select nom nombrescompletos from lab_user where id=?");
    $stmt->execute(array($id));
    if ($stmt->rowCount() > 0)
    {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $data;
}
?>