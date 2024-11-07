<?php
date_default_timezone_set('America/Lima');
require("database.php");
require("database_farmacia.php");
require("database_log.php");
if(isset($_POST['accion'])){
    $accion= $_POST['accion'];

    if($accion=='cicloEstimulacion'){
        require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
        $valor = $_POST['valor'];
        $fecha = date('Y-m-d', strtotime($_POST['fecha']));
        $dia = intval($_POST['dia']);
        $idCiclo = $_POST['idCiclo'];
        $login = $_POST['login'];
        $campo = $_POST['campo'];
        $busqueda =obtenerDiaEstimulacion($idCiclo,$dia,$login,$campo);

        if($busqueda=='crear') {
            $result= nuevoDiaEstimulacion($idCiclo,$fecha,$dia,$login,$campo,$valor);
            header("HTTP/1.1 200 OK");
            echo $result;
        }else if($busqueda=='actualizar') {
            $result= actualizarDiaEstimulacion($idCiclo,$fecha,$dia,$login,$campo,$valor);
            header("HTTP/1.1 200 OK");
            echo $result;
        }else if($busqueda=='Lo siento, No puedes editar este dia') {
            header("HTTP/1.1 200 OK");
            echo "No puedes Actualizar este Dia";
        }
    }

    if($accion =='hcreprod') {
        require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
        $valor = $_POST['valor'];
        $idCiclo = $_POST['idCiclo'];
        $login = $_POST['login'];
        $campo = $_POST['campo']; 
        $result= camposHcReprod($campo,$valor,$login,$idCiclo);
        echo $result;
    }

    if($accion =='elimina'){
        require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
        $dia = $_POST['dia'];
        $idCiclo = $_POST['idCiclo'];
        $login = $_POST['login'];
        $result= eliminarDiaEstimulacion($dia,$login,$idCiclo);
        echo $result;
    }

    if($accion == 'retiroEmbrionnes') {

        $embriones = array();

        for ($p = 1; $p <= $_POST['cont']; $p++) {
            if (isset($_POST['adju'.$p])) {
                $tan = explode("|", $_POST['c'.$p]);
                $embriones[] = array('pro' => $tan[0], 'ovo' => $tan[1]);
            }
        }

        $result=  lab_retiroEmbrio($_POST['paciente_dni'],$_POST['fec'],$_POST['med'],$_POST['emb'],$_POST['embriologo'],$_POST['retiro_num'],$embriones,$_POST['login'],$_FILES['documento'],$_POST['embrioRestant'],$_POST['tip_retiro']);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit; // Salir del script después de enviar la respuesta JSON
    }
}

require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

if (isset($_POST['action']) && $_POST['action'] == 'listarPos') {
    http_response_code(200);
    echo listarPos($_POST['idTipoTarjeta'],$_POST['id_sede'],$_POST['accion']);
}

if (isset($_POST['action']) && $_POST['action'] == 'listarSedes') {
    http_response_code(200);
    echo listarSedes($_POST['id_empresa']);
}

if (isset($_POST['action']) && $_POST['action'] == 'listarPacienteEmbriones') {
    http_response_code(200);
    echo listarPacienteEmbriones();
}

if (isset($_POST['action']) && $_POST['action'] == 'listarPacienteOvulo') {
    http_response_code(200);
    echo listarPacienteOvulo();
}

if (isset($_POST['action']) && $_POST['action'] == 'listarTarifarios') {
    http_response_code(200);
    echo listarTarifarios($_POST['id_sede'], $_POST['id_tip']);
}

if (isset($_POST['action']) && $_POST['action'] == 'listarProcedimientos') {
    http_response_code(200);
    echo listarProcedimientos($_POST['id_sede'],$_POST['id_tarifario'],$_POST['id_tip']);
}

function listarPos($idTipoTarjeta,$id_sede,$busqueda){

    if ($busqueda == 0) {
        $consulta = "";
        $datos = array($idTipoTarjeta,$id_sede);
    }else{
        $consulta = "and tp.id = ?";
        $datos = array($idTipoTarjeta,$id_sede,$busqueda);
    }

    global $farma;
    $consulta = $farma->prepare("SELECT tp.id, tp.codigo, tp.nombrepos, tp.moneda
                                from tipo_tarjeta_pos ttp
                                inner join tblpos tp on tp.id = ttp.pos_id
                                inner join tbltipotarjeta tpj on tpj.id = ttp.tipo_tarjeta_id
                                where ttp.estado is true and tp.estado = 1 and tpj.codigo_facturacion = ? and tp.id_sede = ? ".$consulta);

    $consulta->execute($datos);

    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $jsonResultados = json_encode($resultados);

    if ($busqueda == 0) {
        return $jsonResultados;
    }else{
        return $resultados;
    }
}

function listarSedes($id_tip){

    global $db;
    $consulta = $db->prepare("SELECT DISTINCT sc.* 
                                FROM tarifario t 
                                INNER JOIN recibo_serv r ON r.tarifario_id = t.id
                                INNER JOIN servicios_procedimiento spr ON r.procedimiento_id = spr.id
                                INNER JOIN conta_sub_centro_costo cscc ON r.conta_sub_centro_costo_id = cscc.id
                                INNER JOIN conta_centro_costo ccc ON ccc.id = cscc.conta_centro_costo_id 
                                INNER JOIN sedes_contabilidad sc ON ccc.sede_id = sc.id 
                                WHERE r.tip = ? and t.eliminado = 0 and r.estado = 1 and sc.eliminado = 0;");

    $consulta->execute([$id_tip]);

    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $jsonResultados = json_encode($resultados);

    return $jsonResultados;
}

function listarTarifarios($id_sede, $id_tip){

    global $db;
    $consulta = $db->prepare("SELECT DISTINCT t.* 
                                FROM tarifario t 
                                INNER JOIN recibo_serv r ON r.tarifario_id = t.id
                                INNER JOIN servicios_procedimiento spr ON r.procedimiento_id = spr.id
                                INNER JOIN conta_sub_centro_costo cscc ON r.conta_sub_centro_costo_id = cscc.id
                                INNER JOIN conta_centro_costo ccc ON ccc.id = cscc.conta_centro_costo_id 
                                INNER JOIN sedes_contabilidad sc ON ccc.sede_id = sc.id 
                                WHERE sc.id = ? and r.tip = ? and t.eliminado = 0 and r.estado = 1;");

    $consulta->execute([$id_sede, $id_tip]);

    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $jsonResultados = json_encode($resultados);

    return $jsonResultados;
}

function listarProcedimientos($id_sede, $id_tarifario, $id_tip){

    global $db;
    $consulta = $db->prepare("SELECT DISTINCT sp.* 
                                from servicios_procedimiento sp 
                                INNER JOIN recibo_serv r ON r.procedimiento_id  = sp.id
                                inner join tarifario t on t.id = r.tarifario_id
                                INNER JOIN conta_sub_centro_costo cscc ON r.conta_sub_centro_costo_id = cscc.id
                                INNER JOIN conta_centro_costo ccc ON ccc.id = cscc.conta_centro_costo_id 
                                INNER JOIN sedes_contabilidad sc ON ccc.sede_id = sc.id 
                                WHERE sc.id = ? and r.tarifario_id = ? and r.tip = ? and r.estado = 1;");

    $consulta->execute([$id_sede, $id_tarifario, $id_tip]);

    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
    $jsonResultados = json_encode($resultados);

    return $jsonResultados;
}

function eliminarDiaEstimulacion($dia,$login,$idCiclo){
    global $db;
    $stmt2 = $db->prepare("SELECT * from ciclo_estimulacion_detalle where hcreprod_id = ? AND eliminado=0 AND medico=? AND dia_ciclo=?;");
    $stmt2->execute([$idCiclo, $login, $dia]);
    if($stmt2->rowCount()!= 0){
    $stmt = $db->prepare("UPDATE ciclo_estimulacion_detalle set eliminado=1, fecha_actualizacion=? where hcreprod_id=? AND medico=? AND dia_ciclo=?");
        $stmt->execute(array(date('Y-m-d H:i:s'),$idCiclo, $login, $dia));
        return "Dia eliminado exitosamente";
    }else{
        return false;
    }
}
function camposHcReprod($campo,$valor,$login,$idCiclo){
    global $db;
    $stmt = $db->prepare("update hc_reprod set $campo=?, iduserupdate=?, updatex=? where id=?");
    $hora_actual = date("Y-m-d H:i:s");
        $stmt->execute(array($valor, $login, $hora_actual, $idCiclo));

        $log_Reprod = $db->prepare(
            "INSERT INTO appinmater_log.hc_reprod (
                        reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                        p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                        p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                        tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                        con_oi, con_end,
                        con1_med, 
                        con2_med, 
                        con3_med, 
                        con4_med, 
                        con5_med,
                        con6_med, 
                        con7_med, 
                        con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                        complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                        pago_extras, pago_notas, pago_obs, repro, 
                        idusercreate, createdate, action
                )
            SELECT 
                id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
                con_oi, con_end,
                con1_med, 
                con2_med, 
                con3_med, 
                con4_med, 
                con5_med,
                con6_med,
                con7_med,
                con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                pago_extras, pago_notas, pago_obs, repro, 
                iduserupdate, updatex, 'U'
            FROM appinmater_modulo.hc_reprod
            WHERE id=?");
        $log_Reprod->execute(array($idCiclo));

        return "Actualizacion Exitosa";
}
function obtenerDiaEstimulacion($idCiclo,$dia,$login,$campo) {
    global $db;
    $stmt = $db->prepare("SELECT * from ciclo_estimulacion_detalle where hcreprod_id = ? AND dia_ciclo=? AND eliminado=0;");
    $stmt->execute([$idCiclo,$dia]);

    if ($stmt->rowCount()== 0 ) {
        $stmt1 = $db->prepare("SELECT * from ciclo_estimulacion_detalle where hcreprod_id = ? AND dia_ciclo=? AND eliminado=0;");
        $stmt1->execute([$idCiclo,$dia]);
        if($stmt1->rowCount()>0){
            return "Lo siento, No puedes editar este dia";
        }else{
            return "crear";
        }
    } else if ($stmt->rowCount()!= 0 || $campo == 'indicaciones' || $campo == 'proximo_control') {
        return "actualizar";
    }

}
function actualizarDiaEstimulacion($idCiclo,$fecha,$dia,$login,$campo,$valor)
{
    global $db;
    $stmt1 = $db->prepare("SELECT * from ciclo_estimulacion_detalle where hcreprod_id = ? AND dia_ciclo=? AND eliminado=0;");
    $stmt1->execute([$idCiclo,$dia]);
    $userCreate = '';
    $usuarioCreador = $stmt1->fetch(PDO::FETCH_ASSOC)["usuario_creacion_id"];
    if ($usuarioCreador == null && $campo != 'indicaciones' && $campo != 'proximo_control') {
        $userCreate=' , usuario_creacion_id=\'' . $login . '\', medico=\'' . $login . '\' ';
    }
    if ($stmt1->rowCount() != 0 ) {
        $stmt2 = $db->prepare("UPDATE ciclo_estimulacion_detalle set $campo=?, fecha_actualizacion=? $userCreate where hcreprod_id=? AND dia_ciclo=? AND eliminado=0;");
        $stmt2->execute(array( $valor,date('Y-m-d H:i:s'), $idCiclo, $dia));
        return "Actualizacion Exitosa";
    }else{
        return "Lo siento solo el usuario: ".$usuarioCreador." puede actualizar este dia";
    }
}
function nuevoDiaEstimulacion($idCiclo,$fecha,$dia,$login,$campo,$valor)
{
    if ($campo == 'indicaciones' || $campo == 'proximo_control') {
        $login=null;
    }
    global $db;
    if($campo!='fecha_estimulacion'){
        $fech = date('Y-m-d', strtotime($fecha . ' -1 day'));
        $stmt = $db->prepare("INSERT INTO ciclo_estimulacion_detalle (usuario_creacion_id, hcreprod_id,medico, fecha_estimulacion,$campo, dia_ciclo, fecha_creacion) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute(array($login,$idCiclo,$login,$fech,$valor,$dia,date('Y-m-d H:i:s')));
        return "Nuevo dia creado exitosamente";
    }else{
        $diap=1;
        $stmt = $db->prepare("INSERT INTO ciclo_estimulacion_detalle (usuario_creacion_id, hcreprod_id,medico, fecha_estimulacion, dia_ciclo, fecha_creacion) VALUES (?,?,?,?,?,?)");
        $stmt->execute(array($login,$idCiclo,$login,$fecha,$diap,date('Y-m-d H:i:s')));
        $stmt2 = $db->prepare("update hc_reprod set  inc_fech = ? where id = ?");
        $stmt2->execute(array($fecha,$idCiclo));
        return "Nuevo dia creado exitosamente"; 
    }
    
}
function obtenerCicloEstimulacionDetalle($id) {
    global $db;
    $stmt = $db->prepare("SELECT * from ciclo_estimulacion_detalle where hcreprod_id = ? AND eliminado=0;");
    $stmt->execute([$id]);
    return $stmt->fetchAll();
}

function inicioCicloEstimulacionDetalle($id) {
    global $db;
    $stmt = $db->prepare("SELECT inc_fech as dia from hc_reprod where estado = true and id = ?;");
    $stmt->execute([$id]);
    return $stmt->fetchAll();
}

//migracion de hc_reprod a ciclo_estimulacion_detalle
function migrarCicloEstimulacionDetalle() {
    global $db;
    $sql2 = "SELECT * FROM ciclo_estimulacion_detalle limit 1";
    $stmt2 = $db->prepare($sql2);
    $stmt2->execute();
    if($stmt2->rowCount()==0){
    $sql = "SELECT id, med, con_fec, con_od, con_oi, con_end, medicamento1_id,con1_med, medicamento2_id,con2_med, medicamento3_id,con3_med, medicamento4_id,con4_med, medicamento5_id,con5_med, con_obs, createdate FROM hc_reprod where estado = true order by id";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    foreach ($result as $row) {
        
        $cicloEstimulacionId = $row["id"];
        $usuarioCreacionId = $row["med"];//usuario creador
        $conFec = explode("|", $row["con_fec"]);
        $conOd = explode("|", $row["con_od"]);
        $conOi = explode("|", $row["con_oi"]);
        $conEnd = explode("|", $row["con_end"]);
        $medico = $row["med"];
        $medicamento1 = $row["medicamento1_id"];
        $concentracion1Id = explode("|", $row["con1_med"]);
        $medicamento2 = $row["medicamento2_id"];
        $concentracion2Id = explode("|", $row["con2_med"]);
        $medicamento3 = $row["medicamento3_id"];
        $concentracion3Id = explode("|", $row["con3_med"]);
        $medicamento4 = $row["medicamento4_id"];
        $concentracion4Id = explode("|", $row["con4_med"]);
        $medicamento5 = $row["medicamento5_id"];
        $concentracion5Id = explode("|", $row["con5_med"]);
        $conObs = explode("|", $row["con_obs"]);
        $createdate = $row["createdate"];
        $diaCiclo = 1;
        for ($i = 0; $i < count($conFec); $i++) {
            
            $sql = "INSERT INTO ciclo_estimulacion_detalle (hcreprod_id, usuario_creacion_id, medico, fecha_estimulacion, ovulo_derecho, ovulo_izquierdo, endometrio, concentracion1, concentracion2, concentracion3, concentracion4, concentracion5, observaciones, dia_ciclo, fecha_creacion)
                    VALUES (:cicloEstimulacionId, :usuarioCreacionId, :medico, :fechaEstimulacion, :ovuloDerecho, :ovuloIzquierdo, :endometrio, :concentracion1, :concentracion2, :concentracion3, :concentracion4, :concentracion5, :observaciones, :diaCiclo, :createdate)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":cicloEstimulacionId", $cicloEstimulacionId, PDO::PARAM_INT);
            $stmt->bindParam(":usuarioCreacionId", $usuarioCreacionId, PDO::PARAM_STR);
            $stmt->bindParam(":medico", $medico, PDO::PARAM_STR);
            $fechaEstimulacion = date('Y-m-d', strtotime($conFec[$i])) ? : 'null';
            $stmt->bindParam(":fechaEstimulacion", $fechaEstimulacion);
            $stmt->bindParam(":ovuloDerecho", $conOd[$i]);
            $stmt->bindParam(":ovuloIzquierdo", $conOi[$i]);
            $stmt->bindParam(":endometrio", $conEnd[$i]);
            $stmt->bindParam(":concentracion1", $concentracion1Id[$i+1]);
            $stmt->bindParam(":concentracion2", $concentracion2Id[$i+1]);
            $stmt->bindParam(":concentracion3", $concentracion3Id[$i+1]);
            $stmt->bindParam(":concentracion4", $concentracion4Id[$i+1]);
            $stmt->bindParam(":concentracion5", $concentracion5Id[$i+1]);
            $stmt->bindParam(":observaciones", $conObs[$i]);
            $stmt->bindParam(":diaCiclo", $diaCiclo, PDO::PARAM_INT);
            $createdat = date('Y-m-d', strtotime($createdate)) ? : date('Y-m-d H:i:s');
            $stmt->bindParam(":createdate", $createdat);
            $stmt->execute();
            
            $diaCiclo++;
        }
    }
}
}
function pedidosInmaterApp() {
    global $farma;
    $rProd = $farma->prepare("SELECT
        producto.id , producto.producto, laboratorio.laboratorio, unidad.unidad
        FROM tblproducto producto
        join tbllaboratorio laboratorio on laboratorio.id = producto.id
        join tblunidad unidad on unidad.id = producto.idunidadcompra
        where producto.estado=1 order by producto.producto asc ;
        ");
    $rProd->execute();
    $rProd->rowCount();
}

function consultaProcedimientoById($id) {
    global $db;
    $stmt = $db->prepare("SELECT * from servicios_procedimiento where id = ?;");
    $stmt->execute([$id]);
    return $stmt->fetchAll();
}

function listarMedicosByCodigo($codigo) {
    global $db;
    $listMedicos = $db->prepare("SELECT upper(trim(nombre)) medico FROM man_medico WHERE codigo = ?;");
    $listMedicos->execute([$codigo]);
    $nombre_medico = "";
    if ($listMedicos->rowCount() != 0) {
        $nombre_medico = $listMedicos->fetch(PDO::FETCH_ASSOC)["medico"];
    }
    return $nombre_medico;
}

function listarMedicos(){
    global $db;
    $listMedicos = $db->prepare("SELECT codigo, nombre FROM man_medico WHERE estado='1' ORDER BY nombre;");
    $listMedicos->execute();
    $medicos = $listMedicos->fetchAll();
    return $medicos;
}

function listarEmbriologos(){
    global $db;
    $listEmbriologos = $db->prepare("SELECT id,nom FROM lab_user ORDER BY nom;");
    $listEmbriologos->execute();
    $embriologos = $listEmbriologos->fetchAll();
    return $embriologos;
}

function listarPacienteEmbriones(){
    global $db;
    $listPacienteEmbrio = $db->prepare("   SELECT hp.dni, upper(hp.ape || ' ' || hp.nom) nombre
                                    from hc_paciente hp
                                    inner join man_medios_comunicacion mmc on mmc.id = hp.medios_comunicacion_id
                                    inner join hc_reprod hr on hr.dni = hp.dni
                                    inner join lab_aspira la on la.rep = hr.id and la.estado is true
                                    inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true and lad.des <> 1 and (lad.adju is null or lad.adju = '' or lad.adju = hr.dni)
                                    and (lad.d6f_cic = 'C' or lad.d5f_cic='C' or lad.d4f_cic='C' or lad.d3f_cic='C' or lad.d2f_cic='C')
                                    where hr.estado = true and hp.estado=1 and hp.don='P'
                                    group by hp.dni, hp.ape, hp.nom, hp.med, mmc.nombre
                                    order by ape asc;");
    $listPacienteEmbrio->execute();
    $paciente = $listPacienteEmbrio->fetchAll();
    $jsonResultados = json_encode($paciente);

    return $jsonResultados;
}

function listarPacienteOvulo(){
    global $db;
    $listPacienteEmbrio = $db->prepare("SELECT hp.dni, upper(hp.ape || ' ' || hp.nom) nombre from hc_paciente hp
                                        inner join man_medios_comunicacion mmc on mmc.id = hp.medios_comunicacion_id
                                        inner join hc_reprod hr on hr.dni = hp.dni
                                        inner join lab_aspira la on la.rep = hr.id and la.estado is true
                                        inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true and lad.des <> 1 and (lad.adju is null or lad.adju = '' or lad.adju = hr.dni)
                                            and d0f_cic='C'
                                        where hr.estado = true and hp.estado=1 and hp.don='D'
                                        group by hp.dni, hp.ape, hp.nom, hp.med, mmc.nombre
                                        order by hp.ape asc;");
    $listPacienteEmbrio->execute();
    $paciente = $listPacienteEmbrio->fetchAll();
    $jsonResultados = json_encode($paciente);

    return $jsonResultados;
}

function forPago(){
    global $farma;
    $fPago = $farma->prepare("SELECT * FROM tbltipotarjeta WHERE estado='1' order by orden asc");
    $fPago->execute();
    $formaPago = $fPago->fetchAll();
    return $formaPago;
}

function posList(){
    global $farma;
    $fPago = $farma->prepare("SELECT * FROM tblpos WHERE estado='1'");
    $fPago->execute();
    $formaPago = $fPago->fetchAll();
    return $formaPago;
}

function posListFact(){
    global $farma;
    $fPago = $farma->prepare("SELECT * FROM tblpos WHERE estado='1' and area = 'facturacion' or area = 'pic' ORDER BY nombrepos asc , id asc  ");
    $fPago->execute();
    $formaPago = $fPago->fetchAll();
    return $formaPago;
}

function posCodComercio($id, $nombrepos){
    global $farma;
    $fPago = $farma->prepare("SELECT * FROM tblpos WHERE id = $id and nombrepos = '$nombrepos'");
    $fPago->execute();
    $formaPago = $fPago->fetchAll();
    return $formaPago;
}

function tipTarjeta(){
    global $farma;
    $tTarjeta = $farma->prepare("SELECT * FROM tblformapago WHERE estado='1'");
$tTarjeta->execute();
$tipoTarjeta = $tTarjeta->fetchAll();
return $tipoTarjeta;
}
function pedido_insertar($user, $fechainicio, $fechaprocedimiento,$estado,$dnipaciente)
{
    global $dbfarmacia;
    $stmt = $dbfarmacia->prepare("INSERT INTO tblpedido (idusuario, fechainicio, fechaprocedimiento, estado, dnipaciente) VALUES (?,?,?,?,?)");
    $stmt->execute(array($user, $fechainicio, $fechaprocedimiento,$estado,$dnipaciente));
    $id = $dbfarmacia->lastInsertId();
    return $id;
}

function pedido_detalle_insertar($idpedido, $idproducto, $idkit,$cantidad,$estado)
{
    global $dbfarmacia;
    $stmt = $dbfarmacia->prepare("INSERT INTO tblpedidodetalle (idpedido, idproducto, idkit, cantidad, estado) VALUES (?,?,?,?,?)");
    $stmt->execute(array($idpedido, $idproducto, $idkit,$cantidad,$estado));
    $id = $dbfarmacia->lastInsertId();
    return $id;
}

function update_control($id, $f_uso)
{
    global $db;
    $stmt = $db->prepare("UPDATE lab_control set f_uso=? where id=?");
    $stmt->execute(array($f_uso, $id));
    print("<script type='text/javascript'>window.parent.location.href = 'n_control.php';</script>");
}
function man_notas_eliminar($id, $login)
{
    global $db;
    $stmt = $db->prepare("update man_notas set estado = 0, iduserupdate = ?, updatex = ? where id = ?");
    $stmt->execute(array($login, date("Y-m-d H:i:s"), $id));
}
function man_notas_insertar($nombre, $login)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO man_notas (nombre, idusercreate, createdate) VALUES (?, ?, ?)");
    $stmt->execute(array($nombre, $login, date("Y-m-d H:i:s")));
    echo "<div id='alerta'> Servicio guardado! </div>";
}
function man_extras_eliminar($id, $login)
{
    global $db;
    $stmt = $db->prepare("update man_extras set estado = 0, iduserupdate = ?, updatex = ? where id = ?");
    $stmt->execute(array($login, date("Y-m-d H:i:s"), $id));
}
function man_extras_insertar($nombre, $login)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO man_extras (nombre, idusercreate, createdate) VALUES (?, ?, ?)");
    $stmt->execute(array($nombre, $login, date("Y-m-d H:i:s")));
    echo "<div id='alerta'> Servicio guardado! </div>";
}
function updatePerfil($login, $pass, $nom, $mail, $cmp, $sede)
{
    global $db;
    if ($pass == "") {
        $stmt = $db->prepare("UPDATE usuario SET nom=?, mail=?, cmp=?, sede_id=? WHERE userx=?");
        $stmt->execute(array($nom, $mail, $cmp, $sede, $login));
    } else {
        $stmt = $db->prepare("UPDATE usuario SET pass=?, nom=?, mail=?, cmp=?, sede_id=? WHERE userx=?");
        $stmt->execute(array($pass, $nom, $mail, $cmp, $sede, $login));
    }
}

function enviar_api($data, $url)
{
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => $data,
            'header'=>  "Content-Type: application/json\r\n" . "Accept: application/json\r\n"
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result);
}

function insertPaci($dni, $valid_reniec_api,$medios_comunicacion_id, $med, $tip, $nom, $apeP, $apeM, $fnac, $tcel, $tcas, $tofi, $mai, $dir, $nac, $depa, $prov, $dist, $prof, $san, $don, $raz,$talla,$peso, $rem, $nota, $foto,$sede,$medTratante,$asesora, $user_id='') {
	global $db;
	$base64_foto = "";
    $pass=$dni;
	if (empty($user_id)) {
		$user_id = $med;
	}
    if (empty($rem)) {
        $rem = NULL; 
    }
    // verificar el medico tratante
    $stmt = $db->prepare("SELECT id, codigo from man_medico WHERE id=?;");
	$stmt->execute([$medTratante]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $med = $data["codigo"];

    // ingresar la foto del paciente
	if (isset($foto) and isset($foto['name']) and !empty($foto['name'])) {
		$nom_destination = 'paci/' . $dni . '/foto.jpg';
		if (is_uploaded_file($foto['tmp_name'])) {
			move_uploaded_file($foto['tmp_name'], $nom_destination);
			$base64_foto = base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/paci/" . $dni . "/foto.jpg"));
		}
	}
	$rPaci = $db->prepare("SELECT dni, nom, ape, med FROM hc_paciente WHERE dni=?;");
	$rPaci->execute([$dni]);
	// verificar si existe
	if ($rPaci->rowCount() < 1) {
        $dni = !empty($dni) ? $dni : '';
        $pass = !empty($pass) ? $pass : '';
        $medios_comunicacion_id = !empty($medios_comunicacion_id) ? $medios_comunicacion_id : 0;
        $sta = !empty($sta) ? $sta : '';
        $med = !empty($med) ? $med : '';
        $tip = !empty($tip) ? $tip : '';
        $nom = !empty($nom) ? $nom : '';
        $nombre = !empty($nom) ? $nom : '';
        $ape = !empty($apeP) ? $apeP." ".$apeM : '';
        $apeP = !empty($apeP) ? $apeP : '';
        $apeM = !empty($apeM) ? $apeM : '';
        $fnac = !empty($fnac) ? $fnac : '1900-01-01';
        $tcel = !empty($tcel) ? $tcel : '';
        $tcas = !empty($tcas) ? $tcas : '';
        $tofi = !empty($tofi) ? $tofi : '';
        $mai = !empty($mai) ? $mai : '';
        $dir = !empty($dir) ? $dir : '';
        $nac = !empty($nac) ? $nac : '';
        $depa = !empty($depa) ? $depa : '';
        $prov = !empty($prov) ? $prov : '';
        $dist = !empty($dist) ? $dist : '';
        $prof = !empty($prof) ? $prof : '';
        $san = !empty($san) ? $san : '';
        $don = !empty($don) ? $don : '';
        $raz = !empty($raz) ? $raz : '';
        $talla = !empty($talla) ? $talla : '';
        $peso = !empty($peso) ? $peso : '';
        $rem = !empty($rem) ? $rem : '';
        $base64_foto = !empty($base64_foto) ? $base64_foto : '';
        $sede = !empty($sede) ? intval($sede) : 0;
        $user_id = !empty($user_id) ? $user_id : '';
        $medTratante = !empty($medTratante) ? intval($medTratante) : 0;
        $asesora = !empty($asesora) ? intval($asesora) : 0;
        $medio_referencia_id = !empty($medio_referencia_id) ? intval($medio_referencia_id) : 0;
        $nota = !empty($nota) ? intval($nota) : 0;
        $valid_reniec_api = ($valid_reniec_api == 1 || $valid_reniec_api == 3) ? 'false' : (($valid_reniec_api == 2) ? 'true' : null);
			$stmt = $db->prepare("INSERT INTO hc_paciente
			(dni,valid_reniec_api, pass, medios_comunicacion_id,sta,med, tip, nom, ape, fnac, tcel, tcas, tofi, mai, dir, nac, depa, prov, dist, prof, san, don, raz, talla, peso, rem,foto_principal, idsedes, idusercreate,medico_tratante_id, asesor_medico_id, medio_referencia_id,nombres,apellido_paterno,apellido_materno) VALUES
			(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?,?,?)");
			$stmt->execute(array($dni, $valid_reniec_api, $pass,$medios_comunicacion_id, $sta, $med, $tip, $nom, $ape, $fnac, $tcel, $tcas, $tofi, $mai, $dir, $nac, $depa, $prov, $dist, $prof, $san, $don, $raz,$talla,$peso, $rem, $base64_foto,$sede,$user_id,$medTratante,$asesora,$nota,$nombre,$apeP,$apeM));
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
                    idusercreate,createdate, 'I'
                FROM appinmater_modulo.hc_paciente
                WHERE dni=?");
            $log_Paciente->execute(array($dni));
            $stmt = $db->prepare("INSERT INTO hc_antece (dni) VALUES (?)");
			$stmt->execute(array($dni));
            if (!file_exists('paci/' . $dni)) {
                mkdir('paci/' . $dni, 0755);
            }
	} else {
			?>
    <script type="text/javascript">
    mostrarToastt('error', 'EL PACIENTE YA SE ENCUENTRA REGISTRADO');

    </script>
<?php
	}
}


function updatePaci($dni, $valid_reniec_api, $tip,$medTratante,$asesora, $nom, $ape, $sede, $fnac, $tcel, $tcas, $tofi, $mai, $dir, $nac, $depa, $prov, $dist, $prof, $san, $don, $raz, $peso, $talla, $foto, $rem, $sta, $data, $login="", $tipo_paciente=1) {
	global $db;
    $dni = !empty($dni) ? $dni : '';
        $pass = !empty($pass) ? $pass : '';
        $medios_comunicacion_id = !empty($medios_comunicacion_id) ? $medios_comunicacion_id : 0;
        $sta = !empty($sta) ? $sta : '';
        $med = !empty($med) ? $med : '';
        $tip = !empty($tip) ? $tip : '';
        $nom = !empty($nom) ? $nom : '';
        $ape = !empty($ape) ? $ape : '';
        $fnac = !empty($fnac) ? $fnac : '1900-01-01';
        $tcel = !empty($tcel) ? $tcel : '';
        $tcas = !empty($tcas) ? $tcas : '';
        $tofi = !empty($tofi) ? $tofi : '';
        $mai = !empty($mai) ? $mai : '';
        $dir = !empty($dir) ? $dir : '';
        $nac = !empty($nac) ? $nac : '';
        $depa = !empty($depa) ? $depa : '';
        $prov = !empty($prov) ? $prov : '';
        $dist = !empty($dist) ? $dist : '';
        $prof = !empty($prof) ? $prof : '';
        $san = !empty($san) ? $san : '';
        $don = !empty($don) ? $don : '';
        $raz = !empty($raz) ? $raz : '';
        $talla = !empty($talla) ? $talla : '';
        $peso = !empty($peso) ? $peso : '';
        $rem = !empty($rem) ? $rem : '';
        $base64_foto = !empty($base64_foto) ? $base64_foto : '';
        $sede = !empty($sede) ? intval($sede) : 0;
        $user_id = !empty($user_id) ? $user_id : '';
        $medTratante = !empty($medTratante) ? $medTratante : '';
        $asesora = !empty($asesora) ? intval($asesora) : 0;
        $medio_referencia_id = !empty($medio_referencia_id) ? intval($medio_referencia_id) : 0;
        $nota = !empty($nota) ? intval($nota) : 0;
        $tipo_paciente = !empty($tipo_paciente) ? intval($tipo_paciente) : 0;
        $icq = !empty($data["icq"]) ? intval($data["icq"]) : 0;
        $nivel_instruccion = !empty($data["nivel_instruccion"]) ? intval($data["nivel_instruccion"]) : 0;
        $color_cabello = !empty($data["color_cabello"]) ? intval($data["color_cabello"]) : 0;
        $color_ojos = !empty($data["color_ojos"]) ? intval($data["color_ojos"]) : 0;
        $valid_reniec_api = ($valid_reniec_api == 1) ? 'false' : (($valid_reniec_api == 2) ? 'true' : null);

        if ($tipo_paciente == 2) {
		$stmt1 = $db->prepare("UPDATE hc_paciente
				SET valid_reniec_api=?,iduserupdate=?,updatex=?, medios_comunicacion_id=?
				WHERE dni=?;");
        $hora_actual = date("Y-m-d H:i:s");
		$stmt1->execute([$valid_reniec_api,$login,$hora_actual, $tipo_paciente, $dni]);
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
        $log_Paciente->execute(array($dni));
	    }
        
        $stmt2 = $db->prepare("UPDATE hc_paciente
                SET tip=?,valid_reniec_api=?,idsedes=?,medios_comunicacion_id=?,fnac=?,tcel=?,tcas=?,tofi=?,mai=?,dir=?,nac=?,depa=?,prov=?,dist=?,prof=?,san=?,don=?,raz=?,peso=?,talla=?,rem=?,sta=?
                , icq=?, nivel_instruccion_id=?, color_cabello_id=?, color_ojos_id=? 
                , iduserupdate=?,updatex=?,med=?,asesor_medico_id=?,medio_referencia_id=?
                WHERE dni=?");
        $hora_actual = date("Y-m-d H:i:s");
        $stmt2->execute([$tip, $valid_reniec_api, $sede, $tipo_paciente, $fnac, $tcel, $tcas, $tofi, $mai, $dir, $nac, $depa, $prov, $dist, $prof, $san, $don, $raz, $peso, $talla, $rem, $sta
                        , $icq, $nivel_instruccion, $color_cabello, $color_ojos
                        , $login,$hora_actual,$medTratante,$asesora,$rem, $dni]);
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
        $log_Paciente->execute(array($dni));

    if (!file_exists('paci/' . $dni)) {
        mkdir('paci/' . $dni, 0755);
    }

    // ingresar la foto del paciente
	if (isset($foto) and isset($foto['name']) and !empty($foto['name'])) {
		$nom_destination = 'paci/' . $dni . '/foto.jpg';
		if (is_uploaded_file($foto['tmp_name'])) {
			move_uploaded_file($foto['tmp_name'], $nom_destination);
			$base64_foto = base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/paci/" . $dni . "/foto.jpg"));
		}
	}

	if ($foto['name'] <> "") {
		if (is_uploaded_file($foto['tmp_name'])) {
			move_uploaded_file($foto['tmp_name'], 'paci/' . $dni . '/foto.jpg');
		}
	}
	// agregar fotos de donante
	if (isset($data["donante_foto1"]["name"]) and !empty($data["donante_foto1"]["name"])) {
		if (is_uploaded_file($data["donante_foto1"]['tmp_name'])) {
			move_uploaded_file($data["donante_foto1"]['tmp_name'], 'paci/' . $dni . '/donante_foto1.jpg');
		}
	}
	if (isset($data["donante_foto2"]["name"]) and !empty($data["donante_foto2"]["name"])) {
		if (is_uploaded_file($data["donante_foto2"]['tmp_name'])) {
			move_uploaded_file($data["donante_foto2"]['tmp_name'], 'paci/' . $dni . '/donante_foto2.jpg');
		}
	}
	if (isset($data["donante_evaluacion_psicologica"]["name"]) and !empty($data["donante_evaluacion_psicologica"]["name"])) {
		if (is_uploaded_file($data["donante_evaluacion_psicologica"]['tmp_name'])) {
			move_uploaded_file($data["donante_evaluacion_psicologica"]['tmp_name'], 'paci/' . $dni . '/donante_evaluacion_psicologica.jpg');
		}
	}
	if (isset($data["donante_cariotipo"]["name"]) and !empty($data["donante_cariotipo"]["name"])) {
		if (is_uploaded_file($data["donante_cariotipo"]['tmp_name'])) {
			move_uploaded_file($data["donante_cariotipo"]['tmp_name'], 'paci/' . $dni . '/donante_cariotipo.jpg');
		}
	}
}

function updatePaciAnte($dni, $f_dia, $f_hip, $f_gem, $f_hta, $f_tbc, $f_can, $f_otr, $m_dia, $m_hip, $m_inf, $m_ale, $m_ale1, $m_tbc, $m_ets, $m_can, $m_otr, $h_str, $h_dep, $h_dro, $h_tab, $h_alc, $h_otr, $g_men, $g_per, $g_dur, $g_vol, $g_fur, $g_ant, $g_pap, $g_pap1, $g_pap2, $g_dis, $g_ges, $g_abo, $g_abo1, $g_abo_ges, $g_abo_com, $g_pt, $g_pp, $g_vag, $g_ces, $g_nv, $g_nm, $g_neo, $g_viv, $g_fup, $g_rn_men, $g_rn_mul, $g_rn_may, $g_agh, $g_his, $g_obs, $fe_exa)
{
    global $db;
    foreach (array('f_dia', 'f_hip', 'f_gem', 'f_hta', 'f_tbc', 'f_can', 'f_otr', 'm_dia', 'm_hip', 'm_inf', 'm_ale', 'm_ale1', 'm_tbc', 'm_ets', 'm_can', 'm_otr', 'h_str', 'h_dep', 'h_dro', 'h_tab', 'h_alc', 'h_otr', 'g_men', 'g_per', 'g_dur', 'g_vol', 'g_fur', 'g_ant', 'g_pap', 'g_pap1', 'g_pap2', 'g_dis', 'g_ges', 'g_abo', 'g_abo1', 'g_abo_ges', 'g_abo_com', 'g_pt', 'g_pp', 'g_vag', 'g_ces', 'g_nv', 'g_nm', 'g_neo', 'g_viv', 'g_fup', 'g_rn_men', 'g_rn_mul', 'g_rn_may', 'g_agh', 'g_his', 'g_obs', 'fe_exa') as $var_name) {
        $$var_name = empty($$var_name) ? NULL : $$var_name;
    }

    $stmt = $db->prepare("UPDATE hc_antece
        SET f_dia=?,f_hip=?,f_gem=?,f_hta=?,f_tbc=?,f_can=?,f_otr=?,m_dia=?,m_hip=?,m_inf=?,m_ale=?,m_ale1=?,m_tbc=?,m_ets=?,m_can=?,m_otr=?,h_str=?,h_dep=?,h_dro=?,h_tab=?,h_alc=?,h_otr=?,g_men=?,g_per=?,g_dur=?,g_vol=?,g_fur=?,g_ant=?,g_pap=?,g_pap1=?,g_pap2=?,g_dis=?,g_ges=?,g_abo=?,g_abo1=?,g_abo_ges=?,g_abo_com=?,g_pt=?,g_pp=?,g_vag=?,g_ces=?,g_nv=?,g_nm=?,g_neo=?,g_viv=?,g_fup=?,g_rn_men=?,g_rn_mul=?,g_rn_may=?,g_agh=?,g_his=?,g_obs=?,fe_exa=?
        WHERE dni=?");
    $stmt->execute(array($f_dia, $f_hip, $f_gem, $f_hta, $f_tbc, $f_can, $f_otr, $m_dia, $m_hip, $m_inf, $m_ale, $m_ale1, $m_tbc, $m_ets, $m_can, $m_otr, $h_str, $h_dep, $h_dro, $h_tab, $h_alc, $h_otr, $g_men, $g_per, $g_dur, $g_vol, $g_fur, $g_ant, $g_pap, $g_pap1, $g_pap2, $g_dis, $g_ges, $g_abo, $g_abo1, $g_abo_ges, $g_abo_com, $g_pt, $g_pp, $g_vag, $g_ces, $g_nv, $g_nm, $g_neo, $g_viv, $g_fup, $g_rn_men, $g_rn_mul, $g_rn_may, $g_agh, $g_his, $g_obs, $fe_exa, $dni));
    echo "<div id='alerta'> Datos actualizados! </div>";
}
function updateAnte_quiru($id, $dni, $fec, $pro, $med, $dia, $lug, $pdf)
{
    global $db;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO hc_antece_quiru (dni,fec,pro,med,dia,lug) VALUES (?,?,?,?,?,?)");
        $stmt->execute(array($dni, $fec, $pro, $med, $dia, $lug));
        $id = $db->lastInsertId();
    } else {
        $stmt = $db->prepare("UPDATE hc_antece_quiru SET fec=?,pro=?,med=?,dia=?,lug=? WHERE id=?");
        $stmt->execute(array($fec, $pro, $med, $dia, $lug, $id));
    }
    if (is_uploaded_file($pdf['tmp_name'])) {
        $ruta = 'analisis/quiru_' . $id . '.pdf';
        move_uploaded_file($pdf['tmp_name'], $ruta);
    } ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
window.parent.location.href = "e_paci.php?id=" + x + "&pop=Quiru";
</script>
<?php
}
function updateAnte_pap($id, $dni, $fec, $tip, $obs)
{
    global $db;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO hc_antece_pap (dni,fec,tip,obs) VALUES (?,?,?,?)");
        $stmt->execute(array($dni, $fec, $tip, $obs));
    } else {
        $stmt = $db->prepare("UPDATE hc_antece_pap SET fec=?,tip=?,obs=? WHERE id=?");
        $stmt->execute(array($fec, $tip, $obs, $id));
    }
}
function updateAnte_hsghes($id, $dni, $fec, $tip, $con, $pdf)
{
    global $db;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO hc_antece_hsghes (dni, fec, tip, con, lab) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(array($dni, $fec, $tip, $con, ''));
    } else {
        $stmt = $db->prepare("UPDATE hc_antece_hsghes SET fec=?,tip=?,con=? WHERE dni=? AND fec=?");
        $stmt->execute(array($fec, $tip, $con, $dni, $id));
    }
    if (is_uploaded_file($pdf['tmp_name'])) {
        $ruta = 'analisis/hsghes_' . $dni . '_' . $fec . '.pdf';
        move_uploaded_file($pdf['tmp_name'], $ruta);
    }
}
function updateAnte_perfi($id, $dni, $fec, $fsh, $lh, $est, $prol, $ins, $t3, $t4, $tsh, $amh, $inh, $pdf)
{
    global $db;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO hc_antece_perfi (dni,fec,fsh,lh,est,prol,ins,t3,t4,tsh,amh,inh) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($dni, $fec, $fsh, $lh, $est, $prol, $ins, $t3, $t4, $tsh, $amh, $inh));
    } else {
        $stmt = $db->prepare("UPDATE hc_antece_perfi SET fec=?,fsh=?,lh=?,est=?,prol=?,ins=?,t3=?,t4=?,tsh=?,amh=?,inh=? WHERE dni=? AND fec=?");
        $stmt->execute(array($fec, $fsh, $lh, $est, $prol, $ins, $t3, $t4, $tsh, $amh, $inh, $dni, $id));
    }
    if (is_uploaded_file($pdf['tmp_name'])) {
        $ruta = 'analisis/perfil_' . $dni . '_' . $fec . '.pdf';
        move_uploaded_file($pdf['tmp_name'], $ruta);
    }
}
function updateAnte_cirug($id, $dni, $fec, $pro, $med, $dia, $lug, $pdf)
{
    global $db;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO hc_antece_cirug (dni,fec,pro,med,dia,lug) VALUES (?,?,?,?,?,?)");
        $stmt->execute(array($dni, $fec, $pro, $med, $dia, $lug));
        $id = $db->lastInsertId();
    } else {
        $stmt = $db->prepare("UPDATE hc_antece_cirug SET fec=?,pro=?,med=?,dia=?,lug=? WHERE id=?");
        $stmt->execute(array($fec, $pro, $med, $dia, $lug, $id));
    }
    if (is_uploaded_file($pdf['tmp_name'])) {
        $ruta = 'analisis/cirug_' . $id . '.pdf';
        move_uploaded_file($pdf['tmp_name'], $ruta);
    } ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
window.parent.location.href = "e_paci.php?id=" + x + "&pop=Cirug";
</script>
<?php
}

function updateAnte_trata($id, $dni, $fec, $pro, $med, $medica, $fol, $ovo, $emb, $dia, $cri, $res, $tras) {
    global $db;
		$tras_sms=0;
        $pro = !empty($pro) ? $pro : '';
        $medica = !empty($medica) ? $medica : '';
        $fol = !empty($fol) ? $fol : '';
        $ovo = !empty($ovo) ? intval($ovo) : 0;
        $emb = !empty($emb) ? intval($emb) : 0;
        $dia = !empty($dia) ? $dia : null;
        $cri = !empty($cri) ? intval($cri) : 0;
        $res = !empty($res) ? $res : '';
        $tras = !empty($tras) ? $tras : '';
        
    if ($id == "") {
        if ($tras == 'x') {
            $tras = 1;
            $tras_sms = 1;
        }
        $stmt = $db->prepare("INSERT INTO hc_antece_trata (dni,fec,pro,med,medica,fol,ovo,emb,dia,cri,res,tras) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($dni, $fec, $pro, $med, $medica, $fol, $ovo, $emb, $dia, $cri, $res, $tras));
    } else {
        $stmt = $db->prepare("UPDATE hc_antece_trata SET fec=?,pro=?,med=?,medica=?,fol=?,ovo=?,emb=?,dia=?,cri=?,res=?,tras=? WHERE id=?");
        $stmt->execute(array($fec, $pro, $med, $medica, $fol, $ovo, $emb, $dia, $cri, $res, $tras, $id));
    }
}

function insertPareja($dni, $p_dni,$valid_reniec_api,  $p_tip, $p_nom, $p_apeP, $p_apeM, $p_fnac, $p_tcel, $p_tcas, $p_tofi, $p_mai, $p_dir, $p_prof, $p_san, $p_raz, $p_med, $p_med_mai, $p_med_cel, $tipo_clienteid, $programaid, $sedeid)
{
    global $db;
    $rPare = $db->prepare("SELECT p_dni,p_nom,p_ape FROM hc_pareja WHERE estado = 1 and p_dni=?");
    $rPare->execute(array($p_dni));
    $p_ape = !empty($p_apeP) ? $p_apeP." ".$p_apeM : '';

    $valid_reniec_api = ($valid_reniec_api == 1) ? 'false' : (($valid_reniec_api == 2) ? 'true' : null);
    if ($rPare->rowCount() < 1) {
        $stmt = $db->prepare("INSERT INTO hc_pareja (p_dni,valid_reniec_api,p_tip,p_nom,p_ape,p_fnac,p_tcel,p_tcas,p_tofi,p_mai,p_dir,p_prof,p_san,p_raz,p_med,p_med_mai,p_med_cel,idusercreate,tipo_clienteid,programaid,sedeid,nombres,apellido_paterno,apellido_materno) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($p_dni, $valid_reniec_api, $p_tip, $p_nom, $p_ape, $p_fnac, $p_tcel, $p_tcas, $p_tofi, $p_mai, $p_dir, $p_prof, $p_san, $p_raz, $p_med, $p_med_mai, $p_med_cel, $_SESSION['login'],$tipo_clienteid,$programaid,$sedeid,$p_nom,$p_apeP,$p_apeM));
        $stmt2 = $db->prepare("INSERT INTO hc_pare_paci (dni,p_dni) VALUES (?,?)");
        $stmt2->execute(array($dni, $p_dni));
    } else {
        $pare = $rPare->fetch(PDO::FETCH_ASSOC);
        ?>
<script type="text/javascript">
    mostrarToastt('success', "<?php echo $pare['p_nom'] . " " . $pare['p_ape']; ?> \n YA EXISTE EN EL SISTEMA!");
</script>
<?php if ($dni <> "") {
            $stmt2 = $db->prepare("INSERT INTO hc_pare_paci (dni,p_dni) VALUES (?,?)");
            $stmt2->execute(array($dni, $p_dni));
            echo "<div id='alerta'> Datos guardados en el historial de parejas! </div>";
        }
    }
}
function updatePareja($dni, $p_dni, $p_tip, $p_nom, $p_ape, $p_fnac, $p_tcel, $p_tcas, $p_tofi, $p_mai, $p_dir, $p_prof, $p_san, $p_raz, $p_f_dia, $p_f_hip, $p_f_gem, $p_f_hta, $p_f_tbc, $p_f_can, $p_f_otr, $p_m_dia, $p_m_hip, $p_m_inf1, $p_m_ale1, $p_m_tbc, $p_m_can, $p_m_otr, $p_m_ets, $p_h_str, $p_h_dep, $p_h_dro, $p_h_tab, $p_h_alc, $p_h_otr, $p_obs, $p_pes, $p_tal, $p_ojo, $p_cab, $p_ins, $p_icq, $foto, $foto1, $foto2, $doc1, $doc2, $doc3, $doc4, $p_het, $p_med, $p_med_mai, $p_med_cel, $role)
{
    global $db;
    $stmt = $db->prepare("UPDATE hc_pareja SET p_tip=?,p_nom=?,p_ape=?,p_fnac=?,p_tcel=?,p_tcas=?,p_tofi=?,p_mai=?,p_dir=?,p_prof=?,p_san=?,p_raz=?,p_f_dia=?,p_f_hip=?,p_f_gem=?,p_f_hta=?,p_f_tbc=?,p_f_can=?,p_f_otr=?,p_m_dia=?,p_m_hip=?,p_m_inf=?,p_m_ale=?,p_m_tbc=?,p_m_can=?,p_m_otr=?,p_m_ets=?,p_h_str=?,p_h_dep=?,p_h_dro=?,p_h_tab=?,p_h_alc=?,p_h_otr=?,p_obs=?,p_pes=?,p_tal=?,p_ojo=?,p_cab=?,p_ins=?,p_icq=?,p_med=?,p_med_mai=?,p_med_cel=? WHERE p_dni=?");
    $stmt->execute(array($p_tip, $p_nom, $p_ape, $p_fnac, $p_tcel, $p_tcas, $p_tofi, $p_mai, $p_dir, $p_prof, $p_san, $p_raz, $p_f_dia, $p_f_hip, $p_f_gem, $p_f_hta, $p_f_tbc, $p_f_can, $p_f_otr, $p_m_dia, $p_m_hip, $p_m_inf1, $p_m_ale1, $p_m_tbc, $p_m_can, $p_m_otr, $p_m_ets, $p_h_str, $p_h_dep, $p_h_dro, $p_h_tab, $p_h_alc, $p_h_otr, $p_obs, $p_pes, $p_tal, $p_ojo, $p_cab, $p_ins, $p_icq, $p_med, $p_med_mai, $p_med_cel, $p_dni));
    if ($dni == "") {
        $stmt2 = $db->prepare("UPDATE hc_pare_paci SET p_het=? WHERE dni=? AND p_dni=?");
        $stmt2->execute(array($p_het, '', $p_dni));
    }
    $dir = 'pare/' . $p_dni;
    if (!file_exists($dir)) mkdir($dir, 0755, true);
    if (is_uploaded_file($foto['tmp_name'])) {
        $ruta = $dir . '/foto.jpg';
        move_uploaded_file($foto['tmp_name'], $ruta);
    }
    if (is_uploaded_file($foto1['tmp_name'])) {
        $ruta = $dir . '/foto1.jpg';
        move_uploaded_file($foto1['tmp_name'], $ruta);
    }
    if (is_uploaded_file($foto2['tmp_name'])) {
        $ruta = $dir . '/foto2.jpg';
        move_uploaded_file($foto2['tmp_name'], $ruta);
    }
    if (is_uploaded_file($doc1['tmp_name'])) {
        $ruta = $dir . '/eval_sico.pdf';
        move_uploaded_file($doc1['tmp_name'], $ruta);
    }
    if (is_uploaded_file($doc2['tmp_name'])) {
        $ruta = $dir . '/careotipo.pdf';
        move_uploaded_file($doc2['tmp_name'], $ruta);
    }
    if (is_uploaded_file($doc3['tmp_name'])) {
        $ruta = $dir . '/frag_adn.pdf';
        move_uploaded_file($doc3['tmp_name'], $ruta);
    }
    if (is_uploaded_file($doc4['tmp_name'])) {
        $ruta = $dir . '/fish_spz.pdf';
        move_uploaded_file($doc4['tmp_name'], $ruta);
    }
    if ($role == 7) echo "<div id='alerta'> Datos guardados! </div>"; // cuando es urologo
    else {
        ?>
<script type="text/javascript">
var x =
    "<?php if ($_SESSION['role'] == 2) echo "lista_and.php"; else if ($role == 3) echo "lista_facturacion.php"; else echo "n_pare.php?id=" . $dni; ?>";
window.parent.location.href = x;
</script>
<?php }
}
function updatePareja_01($dni, $valid_reniec_api, $p_dni, $p_tip, $p_nom, $p_ape, $p_fnac, $p_tcel, $p_tcas, $p_tofi, $p_mai, $p_dir, $p_prof, $p_san, $p_raz, $p_f_dia, $p_f_hip, $p_f_gem, $p_f_hta, $p_f_tbc, $p_f_can, $p_f_otr, $p_m_dia, $p_m_hip, $p_m_inf1, $p_m_ale1, $p_m_tbc, $p_m_can, $p_m_otr, $p_m_ets, $p_h_str, $p_h_dep, $p_h_dro, $p_h_tab, $p_h_alc, $p_h_otr, $p_obs, $p_pes, $p_tal, $p_ojo, $p_cab, $p_ins, $p_icq, $foto, $foto1, $foto2, $doc1, $doc2, $doc3, $doc4, $p_het, $p_med, $role, $informe, $login,$tipo_clienteid,$programaid,$sedeid)
{
    global $db;
    $dir = 'pare/' . $p_dni;
    $idconsentimiento=0;
    // informe de consentimiento informado de donante
    if (isset($informe) && !empty($informe['name'])) {
        // $path = "cariotipo/";
        $informe_name = $informe['name'];
        $informe_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
        $informe_name=time() . "-". $informe_name;
        // $ruta_dni = $path.$dni;
        if ( !file_exists($dir) ) {
            mkdir($dir, 0755);
        }
        $ruta = $dir . '/' . $informe_name;
        if (is_uploaded_file($informe['tmp_name'])){
            move_uploaded_file($informe['tmp_name'], $ruta);
        }
        // ingreso del registro
        $stmt = $db->prepare("
        insert into hc_legal_01
        (tipodocumento, numerodocumento, finforme, nombre, idlegaltipodocumento, obs, idusercreate, createdate, iduserupdate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array("1", $p_dni, date("Y-m-d"), $informe_name, 1, "", $login, date("Y-m-d H:i:s"), $login));
        $idconsentimiento = $db->lastInsertId();
    }

    $valid_reniec_api = ($valid_reniec_api == 1) ? 'false' : (($valid_reniec_api == 2) ? 'true' : null);

    $all_variables = array($p_tip, $valid_reniec_api, $p_fnac, $p_tcel, $p_tcas, $p_tofi, $p_mai, $p_dir, $p_prof, $p_san, $p_raz, $p_f_dia, $p_f_hip, $p_f_gem, $p_f_hta, $p_f_tbc, $p_f_can, $p_f_otr, $p_m_dia, $p_m_hip, $p_m_inf1, $p_m_ale1, $p_m_tbc, $p_m_can, $p_m_otr, $p_m_ets, $p_h_str, $p_h_dep, $p_h_dro, $p_h_tab, $p_h_alc, $p_h_otr, $p_obs, $p_pes, $p_tal, $p_ojo, $p_cab, $p_ins, $p_icq, $p_med, $idconsentimiento,$tipo_clienteid,$programaid,$sedeid, $p_dni);

    for ($i = 0; $i < count($all_variables); $i++) {
        if ($all_variables[$i] === "") {
            $all_variables[$i] = NULL;
        }
    }

    
    $stmt = $db->prepare("
        UPDATE hc_pareja
        SET p_tip=?, valid_reniec_api=?, p_fnac=?, p_tcel=?, p_tcas=?, p_tofi=?, p_mai=?, p_dir=?, p_prof=?, p_san=?, p_raz=?, p_f_dia=?, p_f_hip=?, p_f_gem=?, p_f_hta=?, p_f_tbc=?, p_f_can=?, p_f_otr=?, p_m_dia=?, p_m_hip=?, p_m_inf=?, p_m_ale=?, p_m_tbc=?, p_m_can=?, p_m_otr=?, p_m_ets=?, p_h_str=?, p_h_dep=?, p_h_dro=?, p_h_tab=?, p_h_alc=?, p_h_otr=?, p_obs=?, p_pes=?, p_tal=?, p_ojo=?, p_cab=?, p_ins=?, p_icq=?, p_med=?
        , id_hc_legal=?, tipo_clienteid=?,  programaid=?, sedeid=?
        WHERE p_dni=?");
        $stmt->execute($all_variables);
    if ($dni == "") {
        $stmt2 = $db->prepare("UPDATE hc_pare_paci SET p_het=? WHERE dni=? AND p_dni=?");
        $stmt2->execute(array($p_het, '', $p_dni));
    }
    if (!file_exists($dir)) mkdir($dir, 0755, true);
    if (is_uploaded_file($foto['tmp_name'])) {
        $ruta = $dir . '/foto.jpg';
        move_uploaded_file($foto['tmp_name'], $ruta);
    }
    if (is_uploaded_file($foto1['tmp_name'])) {
        $ruta = $dir . '/foto1.jpg';
        move_uploaded_file($foto1['tmp_name'], $ruta);
    }
    if (is_uploaded_file($foto2['tmp_name'])) {
        $ruta = $dir . '/foto2.jpg';
        move_uploaded_file($foto2['tmp_name'], $ruta);
    }
    if (is_uploaded_file($doc1['tmp_name'])) {
        $ruta = $dir . '/eval_sico.pdf';
        move_uploaded_file($doc1['tmp_name'], $ruta);
    }
    if (is_uploaded_file($doc2['tmp_name'])) {
        $ruta = $dir . '/careotipo.pdf';
        move_uploaded_file($doc2['tmp_name'], $ruta);
    }
    if (is_uploaded_file($doc3['tmp_name'])) {
        $ruta = $dir . '/frag_adn.pdf';
        move_uploaded_file($doc3['tmp_name'], $ruta);
    }
    if (is_uploaded_file($doc4['tmp_name'])) {
        $ruta = $dir . '/fish_spz.pdf';
        move_uploaded_file($doc4['tmp_name'], $ruta);
    }
    // 
    if ($role == 7) echo "<div id='alerta'> Datos guardados! </div>"; // cuando es urologo
    else {
        ?>
<script type="text/javascript">
var x =
    "<?php if ($_SESSION['role'] == 2) echo "lista_and.php"; else if ($role == 3) echo "lista_facturacion.php"; else echo "n_pare.php?id=" . $dni; ?>";
window.parent.location.href = x;
</script>
<?php }
}
function updateAnte_p_examp($id, $dni, $p_dni, $fec, $tip, $con, $pdf)
{
    global $db;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO hc_antece_p_examp (p_dni,fec,tip,con) VALUES (?,?,?,?)");
        $stmt->execute(array($p_dni, $fec, $tip, $con));
        $id = $db->lastInsertId();
    } else {
        $stmt = $db->prepare("UPDATE hc_antece_p_examp SET fec=?,tip=?,con=? WHERE id=?");
        $stmt->execute(array($fec, $tip, $con, $id));
    }
    if (is_uploaded_file($pdf['tmp_name'])) {
        $ruta = 'analisis/p_examp_' . $id . '.pdf';
        move_uploaded_file($pdf['tmp_name'], $ruta);
    } ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
var y = "<?php echo $p_dni; ?>";
window.parent.location.href = "e_pare.php?id=" + x + "&ip=" + y + "&pop=p_Examp";
</script>
<?php
}
function updateAnte_p_quiru($id, $dni, $p_dni, $fec, $pro, $med, $dia, $lug, $pdf)
{
    global $db;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO hc_antece_p_quiru (p_dni,fec,pro,med,dia,lug) VALUES (?,?,?,?,?,?)");
        $stmt->execute(array($p_dni, $fec, $pro, $med, $dia, $lug));
        $id = $db->lastInsertId();
    } else {
        $stmt = $db->prepare("UPDATE hc_antece_p_quiru SET fec=?,pro=?,med=?,dia=?,lug=? WHERE id=?");
        $stmt->execute(array($fec, $pro, $med, $dia, $lug, $id));
    }
    if (is_uploaded_file($pdf['tmp_name'])) {
        $ruta = 'analisis/p_quiru_' . $id . '.pdf';
        move_uploaded_file($pdf['tmp_name'], $ruta);
    } ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
var y = "<?php echo $p_dni; ?>";
window.parent.location.href = "e_pare.php?id=" + x + "&ip=" + y + "&pop=p_Quiru";
</script>
<?php
}
function update_serologia($id, $tipopaciente, $dni, $fec, $hbs, $hcv, $hiv, $rpr, $rub, $tox, $clag, $clam, $lab, $informe)
{
    global $db;
    $path = "analisis/";
    $informe_name = "";
    // 
    if ($id == 0) {
        // datos de informe
        if ( isset($informe) ) {
            if (!empty($informe['name'])) {
                /*$informe_name = $informe['name'];
                $informe_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                $informe_name=time() . "-". $informe_name;
                $ruta_dni = $path.$dni;
                if ( !file_exists($ruta_dni) ) {
                    mkdir($path.$dni, 0755);
                }
                $ruta = $path . $dni . '/' . $informe_name;*/
                if ( is_uploaded_file($informe['tmp_name']) )
                {
                    $ruta = 'analisis/sero_' . $dni . '_' . $fec . '.pdf';
                    move_uploaded_file($informe['tmp_name'], $ruta);
                }
            }
        }
        // ingreso del registro
        $stmt = $db->prepare("INSERT INTO hc_antece_p_sero
        (tipo_paciente, p_dni, fec, hbs, hcv, hiv, rpr, rub, tox, cla_g, cla_m, lab, idusercreate, iduserupdate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($tipopaciente, $dni, $fec, $hbs, $hcv, $hiv, $rpr, $rub, $tox, $clag, $clam, $lab, $_SESSION['login'], $_SESSION['login']));
        // header("Location: n_enfermeria.php?dni=" . $dni);
    }    
}
function updateAnte_p_sero($login,$id, $dni, $p_dni, $fec, $hbs, $hcv, $hiv, $rpr, $rub, $tox, $cla_g, $cla_m, $pdf)
{   
    $fec = !empty($fec) ? date("Y-m-d", strtotime($fec)) : date("Y-m-d");
    $hbs = !empty($hbs) ? intval($hbs) : 0;
    $hcv = !empty($hcv) ? intval($hcv) : 0;
    $hiv = !empty($hiv) ? intval($hiv) : 0;
    $rpr = !empty($rpr) ? intval($rpr) : 0;
    $rub = !empty($rub) ? intval($rub) : 0;
    $tox = !empty($tox) ? intval($tox) : 0;
    $cla_g = !empty($cla_g) ? intval($cla_g) : 0;
    $cla_m = !empty($cla_m) ? intval($cla_m) : 0;
    
    global $db;
    if ($id=='1899-12-30') {
        $stmt = $db->prepare("INSERT INTO hc_antece_p_sero
        (p_dni, fec, hbs, hcv, hiv, rpr, rub, tox, cla_g, cla_m, idusercreate, iduserupdate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($p_dni, $fec, $hbs, $hcv, $hiv, $rpr, $rub, $tox, $cla_g, $cla_m, $login, $login));
        $error = $stmt->errorInfo();
        if($error[0] != "00000"){
            echo "Error SQL: ".$error[2];
        }
    } else {
        $stmt = $db->prepare("UPDATE hc_antece_p_sero SET fec=?,hbs=?,hcv=?,hiv=?,rpr=?,rub=?,tox=?,cla_g=?,cla_m=? WHERE p_dni=? AND fec=?");
        $stmt->execute(array($fec, $hbs, $hcv, $hiv, $rpr, $rub, $tox, $cla_g, $cla_m, $p_dni, $id));
    }
    if (is_uploaded_file($pdf['tmp_name'])) {
        $ruta = 'analisis/sero_' . $p_dni . '_' . $fec . '.pdf';
        move_uploaded_file($pdf['tmp_name'], $ruta);
    } ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
var y = "<?php echo $p_dni; ?>";
<?php if ($dni == "mujer") { ?>
window.parent.location.href = "e_paci.php?id=" + y + "&pop=Perfi";
<?php } else { ?>
window.parent.location.href = "e_pare.php?id=" + x + "&ip=" + y + "&pop=p_Sero";
<?php } ?>
</script>
<?php
}
function updateAndro_esp($id, $dni, $p_dni, $fec, $vol_f, $con_f, $via, $agl, $sha, $c_red, $ph, $lic, $deb, $con, $pl_f, $pnl_f, $ins_f, $inm_f, $m_n, $m_a, $m_mic, $m_mac, $m_cab, $m_col, $m_inm, $m_bic, $m_bic2, $nota, $emb)
{
    global $db;
    try {
        if ($id == "") {
            $stmt = $db->prepare("INSERT INTO lab_andro_esp (p_dni,fec) VALUES (?,?)");
            $stmt->execute(array($p_dni, $fec));
        } else {
            $stmt = $db->prepare("UPDATE lab_andro_esp SET fec=?,vol_f=?,con_f=?,via=?,agl=?,sha=?,c_red=?,ph=?,lic=?,deb=?,con=?,pl_f=?,pnl_f=?,ins_f=?,inm_f=?,m_n=?,m_a=?,m_mic=?,m_mac=?,m_cab=?,m_col=?,m_inm=?,m_bic=?,m_bic2=?,nota=?,emb=? WHERE p_dni=? AND fec=?");
            $stmt->execute(array($fec, $vol_f, $con_f, $via, $agl, $sha, $c_red, $ph, $lic, $deb, $con, $pl_f, $pnl_f, $ins_f, $inm_f, $m_n, $m_a, $m_mic, $m_mac, $m_cab, $m_col, $m_inm, $m_bic, $m_bic2, $nota, $emb, $p_dni, $id));
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { ?>
<script type="text/javascript">
alert("No puede ingresar datos en la misma Fecha!");
</script>
<?php } else {
            echo $e->getMessage();
        }
    }
    ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
var y = "<?php echo $p_dni; ?>";
window.parent.location.href = "e_pare.php?id=" + x + "&ip=" + y + "&pop=p_Esp";
</script>
<?php
}
function updateAndro_esp_01($p_dni, $fec, $dni
            /*,$vol_f,$con_f,$via,$agl,$sha,$c_red,$ph,$lic,$deb,$con,$pl_f,$pnl_f,$ins_f,$inm_f,$m_n,$m_a,$m_mic,$m_mac,$m_cab,$m_col,$m_inm,$m_bic,$m_bic2,$nota,$emb*/
            ,$info_fmuestra,$info_lobtencion,$info_hentrega,$info_mobtencion,$info_dobtencion,$info_medicacion
            ,$macro_apariencia,$macro_viscosidad,$macro_liquefaccion,$macro_aglutinacion,$macro_ph,$macro_volumen
            ,$movi_mprogresivo,$movi_mnoprogresivo,$movi_tvitalidad
            ,$concen_exml
            ,$morfo_normal,$morfo_anormal_cabeza,$morfo_anormal_pieza,$morfo_anormal_cola,$morfo_micro,$morfo_macro,$morfo_inmaduro,$morfo_bicefalo,$morfo_bicaudo
            ,$resul_cripto,$resul_azo
        )
{
    global $db;
    try {
        $stmt = $db->prepare("
        UPDATE lab_andro_esp
        set
        info_fmuestra=?,info_lobtencion=?,info_hentrega=?,info_mobtencion=?,info_dobtencion=?,info_medicacion=?
        ,macro_apariencia=?,macro_viscosidad=?,macro_liquefaccion=?,macro_aglutinacion=?,macro_ph=?,macro_volumen=?
        ,movi_mprogresivo=?,movi_mnoprogresivo=?,movi_tvitalidad=?
        ,concen_exml=?
        ,morfo_normal=?,morfo_anormal_cabeza=?,morfo_anormal_pieza=?,morfo_anormal_cola=?,morfo_micro=?,morfo_macro=?,morfo_inmaduro=?,morfo_bicefalo=?,morfo_bicaudo=?
        ,resul_cripto=?,resul_azo=?
        /*,vol_f=?,con_f=?,via=?,agl=?,sha=?,c_red=?,ph=?,lic=?,deb=?,con=?,pl_f=?,pnl_f=?,ins_f=?,inm_f=?,m_n=?,m_a=?,m_mic=?,m_mac=?,m_cab=?,m_col=?,m_inm=?,m_bic=?,m_bic2=?,nota=?,emb=?*/
        where p_dni=? and fec=?");
        $stmt->execute(array(
            $info_fmuestra,$info_lobtencion,$info_hentrega,$info_mobtencion,$info_dobtencion,$info_medicacion
            ,$macro_apariencia,$macro_viscosidad,$macro_liquefaccion,$macro_aglutinacion,$macro_ph,$macro_volumen
            ,$movi_mprogresivo,$movi_mnoprogresivo,$movi_tvitalidad
            ,$concen_exml
            ,$morfo_normal,$morfo_anormal_cabeza,$morfo_anormal_pieza,$morfo_anormal_cola,$morfo_micro,$morfo_macro,$morfo_inmaduro,$morfo_bicefalo,$morfo_bicaudo
            ,$resul_cripto,$resul_azo
            /*$vol_f,$con_f,$via,$agl,$sha,$c_red,$ph,$lic,$deb,$con,$pl_f,$pnl_f,$ins_f,$inm_f,$m_n,$m_a,$m_mic,$m_mac,$m_cab,$m_col,$m_inm,$m_bic,$m_bic2,$nota,$emb */
            , $p_dni, $fec));
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { ?>
<script type="text/javascript">
alert("No puede ingresar datos en la misma Fecha!");
</script>
<?php } else {
            echo $e->getMessage();
        }
    }
}
function updateAndro_esp_02($img_movi,$img_concen,$img_mtotal,$img_mclasi,$archivo_id,$p_dni, $fec, $dni
            ,$info_fmuestra,$info_lobtencion,$info_hentrega,$info_mobtencion,$info_dobtencion,$info_medicacion
            ,$macro_apariencia,$macro_viscosidad,$macro_liquefaccion,$macro_aglutinacion,$macro_aglutinacion_porc,$macro_ph,$macro_volumen
            ,$movi_mprogresivo,$movi_mnoprogresivo,$movi_tvitalidad
            ,$concen_exml,$concen_credon
            ,$morfo_normal,$morfo_anormal_cabeza,$morfo_anormal_pieza,$morfo_anormal_cola,$morfo_micro,$morfo_macro,$morfo_inmaduro,$morfo_bicefalo,$morfo_bicaudo
            ,$resul_cripto,$resul_azo,$emb, $observaciones, $fecha_resultado
            ,$movi_mprogresivo_lineal_cantidad, $movi_mprogresivo_no_lineal_cantidad, $movi_mnoprogresivo_cantidad, $movi_nmoviles_cantidad,$data = array()
        )
{
    global $db;

    try {
        $upload_dir = "_upload/andro/";
        $img_data_arr = ['img_movi' => $img_movi, 'img_concen' => $img_concen, 'img_mtotal' => $img_mtotal, 'img_mclasi' => $img_mclasi];
        $saved_img_names = [];

        foreach ($img_data_arr as $img_name => $img_data) {
            if ($img_data) {
                $img_data = str_replace('data:image/png;base64,', '', $img_data);
                $img_data = str_replace(' ', '+', $img_data);
                $data2 = base64_decode($img_data);
                $file_name = time()."_{$img_name}.png";
                $file_path = $upload_dir.$file_name;
                $success = file_put_contents($file_path, $data2);
                if (!$success) {
                    throw new Exception("Failed to write image data to {$file_path}.");
                }
                $saved_img_names[$img_name] = $file_name;
            }
        }
        $img_movi = isset($saved_img_names['img_movi']) ? $saved_img_names['img_movi'] : null;
        $img_concen = isset($saved_img_names['img_concen']) ? $saved_img_names['img_concen'] : null;
        $img_mtotal = isset($saved_img_names['img_mtotal']) ? $saved_img_names['img_mtotal'] : null;
        $img_mclasi = isset($saved_img_names['img_mclasi']) ? $saved_img_names['img_mclasi'] : null;

        $stmt = $db->prepare("UPDATE lab_andro_esp
        SET fec=?,archivo_id=?,info_fmuestra=?,info_lobtencion=?,info_hentrega=?,info_mobtencion=?,info_dobtencion=?,info_medicacion=?
        ,macro_apariencia=?,macro_viscosidad=?,macro_liquefaccion=?,macro_aglutinacion=?,macro_aglutinacion_porc=?,macro_ph=?,macro_volumen=?
        ,movi_mprogresivo=?,movi_mnoprogresivo=?,movi_tvitalidad=?
        ,concen_exml=?,concen_credon=?
        ,morfo_normal=?,morfo_anormal_cabeza=?,morfo_anormal_pieza=?,morfo_anormal_cola=?,morfo_micro=?,morfo_macro=?,morfo_inmaduro=?,morfo_bicefalo=?,morfo_bicaudo=?
        ,resul_cripto=?,resul_azo=?, img_movi=?, img_concen=?, img_mtotal=?, img_mclasi=?,emb=?,nota=?
        ,movi_mprogresivo_lineal_cantidad=?, movi_mprogresivo_no_lineal_cantidad=?, movi_mnoprogresivo_cantidad=?, movi_nmoviles_cantidad=?
        , cine_vap=?, cine_lin=?, cine_alh=?, cine_vsl=?, cine_str=?, cine_bcf=?, cine_vcl=?, cine_wob=?
        , normal_largocabeza_promedio=?, normal_largocabeza_porcentaje=?, normal_ancho_promedio=?, normal_ancho_porcentaje=?, normal_perimetro_promedio=?, normal_perimetro_porcentaje=?, normal_area_promedio=?, normal_area_porcentaje=?, normal_largocola_promedio=?, normal_largocola_porcentaje=?
        , abstinencia=?, antibioticos=?, antidepresivos=?, antiinflamatorios=?, protectores=?, otros_texto=?
        WHERE p_dni=? AND fec=?");
        $stmt->execute(array(
            $fecha_resultado,$archivo_id, $info_fmuestra,$info_lobtencion,$info_hentrega,$info_mobtencion,$info_dobtencion,$info_medicacion
            ,$macro_apariencia,$macro_viscosidad,$macro_liquefaccion,$macro_aglutinacion,$macro_aglutinacion_porc,$macro_ph,$macro_volumen
            ,$movi_mprogresivo,$movi_mnoprogresivo,$movi_tvitalidad
            ,$concen_exml,$concen_credon
            ,$morfo_normal,$morfo_anormal_cabeza,$morfo_anormal_pieza,$morfo_anormal_cola,$morfo_micro,$morfo_macro,$morfo_inmaduro,$morfo_bicefalo,$morfo_bicaudo
            ,$resul_cripto,$resul_azo,$img_movi, $img_concen, $img_mtotal, $img_mclasi,$emb, $observaciones
            ,$movi_mprogresivo_lineal_cantidad, $movi_mprogresivo_no_lineal_cantidad, $movi_mnoprogresivo_cantidad, $movi_nmoviles_cantidad
            , $data["cine_vap"], $data["cine_lin"], $data["cine_alh"], $data["cine_vsl"], $data["cine_str"], $data["cine_bcf"], $data["cine_vcl"], $data["cine_wob"]
            , $data["normal_largocabeza_promedio"], $data["normal_largocabeza_porcentaje"], $data["normal_ancho_promedio"], $data["normal_ancho_porcentaje"], $data["normal_perimetro_promedio"], $data["normal_perimetro_porcentaje"], $data["normal_area_promedio"], $data["normal_area_porcentaje"], $data["normal_largocola_promedio"], $data["normal_largocola_porcentaje"]
            , $data["abstinencia"], $data["antibioticos"], $data["antidepresivos"], $data["antiinflamatorios"], $data["protectores"], $data["otros_texto"]
            , $p_dni, $fec));
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { ?>
<script type="text/javascript">
alert("No puede ingresar datos en la misma Fecha!");
</script>
<?php } else {
            echo $e->getMessage();
        }
    }
}

function updateAndro_cap($id, $dni, $p_dni, $fec, $vol_f, $con_f, $esp, $con_c, $pl_f, $pl_c, $pnl_f, $pnl_c, $ins_f, $ins_c, $inm_f, $inm_c, $cap, $sel, $mue, $des_tip, $des_fec, $cont, $des, $pro, $p_dni_het, $emb, $rep=0, $idusercreate="") {
    global $db;
    if ($cont > 0 && $mue >= 3 && $des == "") {
        for ($p = 1; $p <= $cont; $p++) {
            if (isset($_POST['c' . $p])) {
                $tan = explode("-", $_POST['c' . $p]);
                $stmt2 = $db->prepare("UPDATE lab_tanque_res SET sta=?,tip=?,tip_id=? WHERE t=? AND c=? AND v=? AND p=?");
                $stmt2->execute(array("", 0, "", $tan[0], $tan[1], $tan[2], $tan[3]));
                $des .= $tan[0] . '-' . $tan[1] . '-' . $tan[2] . '-' . $tan[3] . '|';
            }
        }
    }
    if ($mue == 4) {$des_dni = $p_dni_het;} else {$des_dni = $p_dni;}
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO lab_andro_cap (p_dni,fec,vol_f,con_f,esp,con_c,pl_f,pl_c,pnl_f,pnl_c,ins_f,ins_c,inm_f,inm_c,cap,sel,mue,des,des_tip,des_fec,des_dni,pro,emb,rep,idusercreate) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($p_dni, $fec, $vol_f, $con_f, $esp, $con_c, $pl_f, $pl_c, $pnl_f, $pnl_c, $ins_f, $ins_c, $inm_f, $inm_c, $cap, $sel, $mue, $des, $des_tip, $des_fec, $des_dni, $pro, $emb, $rep,$idusercreate));
    } else {
        $stmt = $db->prepare("UPDATE lab_andro_cap SET fec=?,vol_f=?,con_f=?,esp=?,con_c=?,pl_f=?,pl_c=?,pnl_f=?,pnl_c=?,ins_f=?,ins_c=?,inm_f=?,inm_c=?,cap=?,sel=?,mue=?,des=?,des_tip=?,des_fec=?,des_dni=?,emb=?,rep=? WHERE id=? and eliminado is false");
        $stmt->execute(array($fec, $vol_f, $con_f, $esp, $con_c, $pl_f, $pl_c, $pnl_f, $pnl_c, $ins_f, $ins_c, $inm_f, $inm_c, $cap, $sel, $mue, $des, $des_tip, $des_fec, $des_dni, $emb, $rep, $id));
    }
}
function updateAndro_tes_cap($id, $dni, $p_dni, $fec, $vol_f, $con_f, $con_c, $pl_f, $pl_c, $pnl_f, $pnl_c, $ins_f, $ins_c, $inm_f, $inm_c, $cap, $sel, $emb)
{
    global $db;
    try {
        if ($id == "") {
            $stmt = $db->prepare("INSERT INTO lab_andro_tes_cap (p_dni,fec) VALUES (?,?)");
            $stmt->execute(array($p_dni, $fec));
        } else {
            $stmt = $db->prepare("UPDATE lab_andro_tes_cap SET fec=?,vol_f=?,con_f=?,con_c=?,pl_f=?,pl_c=?,pnl_f=?,pnl_c=?,ins_f=?,ins_c=?,inm_f=?,inm_c=?,cap=?,sel=?,emb=? WHERE p_dni=? AND fec=?");
            $stmt->execute(array($fec, $vol_f, $con_f, $con_c, $pl_f, $pl_c, $pnl_f, $pnl_c, $ins_f, $ins_c, $inm_f, $inm_c, $cap, $sel, $emb, $p_dni, $id));
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            ?>
<script type="text/javascript">
alert("No puede ingresar datos en la misma Fecha!");
</script> <?php
        } else {
            echo $e->getMessage();
        }
    }
    ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
var y = "<?php echo $p_dni; ?>";
window.parent.location.href = "e_pare.php?id=" + x + "&ip=" + y + "&pop=p_Tes_cap";
</script>
<?php
}
function updateAndro_tes_sob($id, $dni, $p_dni, $fec, $vol_f, $con_f, $con_c, $pl_f, $pl_c, $pnl_f, $pnl_c, $ins_f, $ins_c, $inm_f, $inm_c, $cap, $sel, $emb)
{
    global $db;
    try {
        if ($id == "") {
            $stmt = $db->prepare("INSERT INTO lab_andro_tes_sob (p_dni,fec) VALUES (?,?)");
            $stmt->execute(array($p_dni, $fec));
        } else {
            $stmt = $db->prepare("UPDATE lab_andro_tes_sob SET fec=?,vol_f=?,con_f=?,con_c=?,pl_f=?,pl_c=?,pnl_f=?,pnl_c=?,ins_f=?,ins_c=?,inm_f=?,inm_c=?,cap=?,sel=?,emb=? WHERE p_dni=? AND fec=?");
            $stmt->execute(array($fec, $vol_f, $con_f, $con_c, $pl_f, $pl_c, $pnl_f, $pnl_c, $ins_f, $ins_c, $inm_f, $inm_c, $cap, $sel, $emb, $p_dni, $id));
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            ?>
<script type="text/javascript">
alert("No puede ingresar datos en la misma Fecha!");
</script> <?php
        } else {
            echo $e->getMessage();
        }
    }
    ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
var y = "<?php echo $p_dni; ?>";
window.parent.location.href = "e_pare.php?id=" + x + "&ip=" + y + "&pop=p_Tes_sob";
</script>
<?php
}
function updateAndro_bio_tes($id, $dni, $p_dni, $fec, $tip, $con_f, $esp, $nota, $pl_f, $pnl_f, $ins_f, $inm_f, $crio, $tra, $doc, $vol, $via, $met, $emb, $c_tan, $c_can, $v_p)
{
    global $db;
    try {
        if ($id == "") {
            $stmt = $db->prepare("INSERT INTO lab_andro_bio_tes (p_dni,fec) VALUES (?,?)");
            $stmt->execute(array($p_dni, $fec));
        } else {
            $stmt = $db->prepare("UPDATE lab_andro_bio_tes SET fec=?,tip=?,con_f=?,esp=?,nota=?,pl_f=?,pnl_f=?,ins_f=?,inm_f=?,crio=?,tra=?,vol=?,via=?,met=?,emb=? WHERE p_dni=? AND fec=?");
            $stmt->execute(array($fec, $tip, $con_f, $esp, $nota, $pl_f, $pnl_f, $ins_f, $inm_f, $crio, $tra, $vol, $via, $met, $emb, $p_dni, $id));
        }
        if ($c_tan > 0 and $c_can > 0 and $crio == 1) {
            if ($doc['name'] <> "") {
                $dir = 'pare/' . $p_dni;
                if (!file_exists($dir)) mkdir($dir, 0755, true);
                if (is_uploaded_file($doc['tmp_name'])) {
                    $ruta = $dir . '/biopsia_traslado_' . $fec . '.pdf';
                    move_uploaded_file($doc['tmp_name'], $ruta);
                }
            }
            $vp = explode("|", $v_p);
            for ($v = 1; $v <= $vp[0]; $v++) {
                for ($p = 1; $p <= $vp[1]; $p++) {
                    //echo "vial: ".$_POST[$v.'_'.$p]."---".$c_tan."_".$c_can."_".$v."_".$p."<br>";
                    if (isset($_POST[$v . '_' . $p])) {
                        $stmt2 = $db->prepare("UPDATE lab_tanque_res SET sta=?,tip=?,tip_id=? WHERE t=? AND c=? AND v=? AND p=?");
                        if ($_POST[$v . '_' . $p] == 1) // chekeado
                            $stmt2->execute(array($p_dni, 1, $fec, $c_tan, $c_can, $v, $p)); // 1=bio_tes 2=crio_sem 3=embrio 4=ovo
                        else
                            $stmt2->execute(array("", 0, "", $c_tan, $c_can, $v, $p));
                    }
                }
            }
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            ?>
<script type="text/javascript">
alert("No puede ingresar datos en la misma Fecha!");
</script> <?php
        } else {
            echo $e->getMessage();
        }
    }
    ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
var y = "<?php echo $p_dni; ?>";
window.parent.location.href = "e_pare.php?id=" + x + "&ip=" + y + "&pop=p_Bio_tes";
</script>
<?php
}
function updateAndro_crio_sem($id, $dni, $p_dni, $fec, $vol_f, $vol_c, $con_f, $con_c, $pl_f, $pl_c, $pnl_f, $pnl_c, $ins_f, $ins_c, $inm_f, $inm_c, $obs, $cap, $tra, $doc, $vol, $via, $met, $emb, $c_tan, $c_can, $v_p, $data=[])
{
    global $db;
    try {
        if ($id == "") {
            $stmt = $db->prepare("INSERT INTO lab_andro_crio_sem (p_dni,fec) VALUES (?,?)");
            $stmt->execute(array($p_dni, $fec));
        } else {
            $stmt = $db->prepare("UPDATE lab_andro_crio_sem SET fec=?,vol_f=?,vol_c=?,con_f=?,con_c=?,pl_f=?,pl_c=?,pnl_f=?,pnl_c=?,ins_f=?,ins_c=?,inm_f=?,inm_c=?,obs=?,cap=?,tra=?,vol=?,via=?,met=?,emb=?,cuaderno=?,pagina=? WHERE p_dni=? AND fec=?");
            $stmt->execute(array($fec, $vol_f, $vol_c, $con_f, $con_c, $pl_f, $pl_c, $pnl_f, $pnl_c, $ins_f, $ins_c, $inm_f, $inm_c, $obs, $cap, $tra, $vol, $via, $met, $emb,$data['cuaderno'],$data['pagina'], $p_dni, $id));
        }
        if ($c_tan > 0 and $c_can > 0) {
            if ($doc['name'] <> "") {
                $dir = 'pare/' . $p_dni;
                if (!file_exists($dir)) mkdir($dir, 0755, true);
                if (is_uploaded_file($doc['tmp_name'])) {
                    $ruta = $dir . '/crio_traslado_' . $fec . '.pdf';
                    move_uploaded_file($doc['tmp_name'], $ruta);
                }
            }
            $vp = explode("|", $v_p);
            for ($v = 1; $v <= $vp[0]; $v++) {
                for ($p = 1; $p <= $vp[1]; $p++) {
                    //echo "vial: ".$_POST[$v.'_'.$p]."---".$c_tan."_".$c_can."_".$v."_".$p."<br>";
                    if (isset($_POST[$v . '_' . $p])) {
                        $stmt2 = $db->prepare("UPDATE lab_tanque_res SET sta=?,tip=?,tip_id=? WHERE t=? AND c=? AND v=? AND p=?");
                        if ($_POST[$v . '_' . $p] == 1) // chekeado
                            $stmt2->execute(array($p_dni, 2, $fec, $c_tan, $c_can, $v, $p)); // 1=bio_tes 2=crio_sem 3=embrio 4=ovo
                        else
                            $stmt2->execute(array("", 0, "", $c_tan, $c_can, $v, $p));
                    }
                }
            }
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            ?>
<script type="text/javascript">
alert("No puede ingresar datos en la misma Fecha!");
</script> <?php
        } else {
            echo $e->getMessage();
        }
    }
    ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
var y = "<?php echo $p_dni; ?>";
window.parent.location.href = "e_pare.php?id=" + x + "&ip=" + y + "&pop=p_Crio_sem";
</script>
<?php
}
function updateUrolo($id, $fec, $fec_h, $fec_m, $mot, $dig, $medi, $aux, $e_sol, $in_t, $in_f2, $in_h2, $in_m2, $idturno="3") {
    function validar_fecha($fecha) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return $fecha;
        } else {
            return '1899-12-30'; // Fecha por defecto en caso de que la entrada esté vacía
        }
    }    
    global $db;
    $in_f2_validado = validar_fecha($in_f2);
    $stmt = $db->prepare("UPDATE hc_urolo SET fec=?,fec_h=?,fec_m=?,mot=?,dig=?,medi=?,aux=?,e_sol=?,in_t=?,in_f2=?,in_h2=?,in_m2=?, idturno_inter=? WHERE id=?");
    $stmt->execute(array($fec, $fec_h, $fec_m, $mot, $dig, $medi, $aux, $e_sol, $in_t, $in_f2_validado, $in_h2, $in_m2, $idturno, $id));
    echo "<div id='alerta'> Datos guardados! </div>";
}
function insertBetaCitaEco($dni, $pro, $fec, $med, $fec_h, $fec_m, $mot, $confirmacion = false)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO hc_eco_beta_positivo (dni,pro,fec,med,fec_h,fec_m,mot, confirmacion) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute(array($dni, $pro, $fec, $med, $fec_h, $fec_m, $mot, $confirmacion));
    $idp = $db->lastInsertId();
    // mkdir('paci/' . $dni . '/' . $idp, 0755); // crea carpeta para subir imagenes de los planes de trabajo -------
    echo "<div id='alerta'> Datos guardados en el historial de consultas! </div>";

    return $idp;
}
function updateEcoBetaPositivo($id, $fec, $fec_h, $fec_m, $mot, $dig, $aux, $efec, $betacuantitativa, $sacogestacional, $condicion, $ubicacionsaco, $latidos1, $latidos2, $latidos3, $umc, $semanas_umc, $decision_siguiente_ecografia, $siguiente_ecografia, $lcc, $progesterona, $observaciones)
{

    global $db;
    $stmt = $db->prepare("UPDATE hc_eco_beta_positivo SET fec=?,fec_h=?,fec_m=?,mot=?,dig=?,aux=?,efec=?,betacuantitativa=?, sacogestacional=?, condicion=?, ubicacionsaco=?, latidos1=?, latidos2=?, latidos3=?, umc=?, semanas_umc=?, decision_siguiente_ecografia=?, siguiente_ecografia=?, lcc=?, progesterona=?, observaciones=? WHERE id=?");
    $stmt->execute(array($fec, $fec_h, $fec_m, $mot, $dig, $aux, $efec, $betacuantitativa, $sacogestacional, $condicion, $ubicacionsaco, $latidos1, $latidos2, $latidos3,  $umc, $semanas_umc, $decision_siguiente_ecografia, $siguiente_ecografia, $lcc, $progesterona, $observaciones, $id));
    echo "<div id='alerta'> Datos guardados! </div>";
}
function insertGine($dni, $fec, $asesor_medico_id, $fec_h, $fec_m, $mot, $cupon, $cod_med_trat, &$atencion_id, $man_motivoconsulta_id = 1, $usercreate_id='sistemas', $tipoconsulta_id = 1)
{
    global $db;

    $programa_id = $db->prepare("SELECT medios_comunicacion_id,idsedes from hc_paciente where dni = ?");
    $programa_id->execute([$dni]);
    $programa_id = $programa_id->fetch(PDO::FETCH_ASSOC);
    $stmt = $db->prepare(
        "INSERT INTO hc_gineco (dni, fec, med, fec_h, fec_m, mot, cupon,asesor_medico_id, tipoconsulta_ginecologia_id, man_motivoconsulta_id, idusercreate,programaid,sedeid) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)"
    );

    $stmt2 = $db->prepare("UPDATE hc_paciente
    SET med=?, asesor_medico_id =?, iduserupdate=?, updatex=?
    WHERE dni=?");
    //var_dump($cupon);
    //exit;

    $stmt->execute(array($dni, $fec, $cod_med_trat, $fec_h, $fec_m, $mot, $cupon, $asesor_medico_id, $tipoconsulta_id, $man_motivoconsulta_id, $usercreate_id, $programa_id['medios_comunicacion_id'], $programa_id['idsedes']));
    $idp = $db->lastInsertId();
    $log_Gineco = $db->prepare(
        "INSERT INTO appinmater_log.hc_gineco (
            gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
            man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
            fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
            vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
            in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
            cupon, repro, legal, cancela, cancela_motivo,
            isuser_log, date_log,
            asesor_medico_id, 
            action
            )
        SELECT
            id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
            man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
            fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
            vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
            in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
            cupon, repro, legal, cancela, cancela_motivo,
            idusercreate, createdate,
            asesor_medico_id,
            'I'
        FROM appinmater_modulo.hc_gineco
        WHERE id =?");
    
    $log_Gineco->execute(array($idp));

    $hora_actual = date("Y-m-d H:i:s");
    $stmt2->execute([ $cod_med_trat,$asesor_medico_id,$usercreate_id,$hora_actual, $dni ]);

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
    $log_Paciente->execute(array($dni));
    $atencion_id = $db->lastInsertId();

    echo "<div id='alerta'> Datos guardados en el historial de consultas! </div>";
}
function updateGine($id, $fec, $fec_h, $fec_m, $mot,$med, $asesor_medico_id, $cupon, $dig, $aux, $efec, $cic, $vag, $vul, $cer, $cer1, $mam, $mam1, $t_vag, $eco, $e_sol, $i_med, $i_fec, $i_obs, $in_t, $in_f1, $in_h1, $in_m1, $in_f2, $in_h2, $in_m2, $in_c, $repro, $idturno, $interconsulta_id, $cancela_motivo,$login)
{
    global $db;
    $efec = empty($efec) ? NULL : $efec;
    $i_fec = empty($i_fec) ? NULL : $i_fec;
    $in_f1 = empty($in_f1) ? NULL : $in_f1;
    $in_f2 = empty($in_f2) ? NULL : $in_f2;
    $cic = ($cic === '') ? NULL : $cic;
    $in_c = !empty($in_c) ? intval($in_c) : 0;
    $stmt = $db->prepare(
        "UPDATE hc_gineco
        SET fec=?,fec_h=?,fec_m=?,mot=?,med=?, asesor_medico_id=?, cupon=?,dig=?,aux=?,efec=?,cic=?,vag=?,vul=?,cer=?,cer1=?,mam=?,mam1=?,t_vag=?,eco=?,e_sol=?,i_med=?,i_fec=?,i_obs=?,in_t=?,in_f1=?,in_h1=?,in_m1=?,in_f2=?,in_h2=?,in_m2=?,in_c=?,repro=?,idturno_inter=?,interconsulta_id=?, cancela_motivo=?, iduserupdate=?, updatex=?
        WHERE id=?"
    );

    $stmt2 = $db->prepare("UPDATE hc_paciente
    SET med=?, asesor_medico_id =?, iduserupdate=?, updatex=?
    WHERE dni=(select dni from appinmater_modulo.hc_gineco where id = ?)");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute(array($fec, $fec_h, $fec_m, $mot,$med,$asesor_medico_id,$cupon, $dig, $aux, $efec, $cic, $vag, $vul, $cer, $cer1, $mam, $mam1, $t_vag, $eco, $e_sol, $i_med, $i_fec, $i_obs, $in_t, $in_f1, $in_h1, $in_m1, $in_f2, $in_h2, $in_m2, $in_c, $repro, $idturno, $interconsulta_id, $cancela_motivo, $login, $hora_actual, $id));
    
    $log_Gineco = $db->prepare(
            "INSERT INTO appinmater_log.hc_gineco (
                gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
                fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
                vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
                cupon, repro, legal, cancela, cancela_motivo,
                isuser_log, date_log,
                asesor_medico_id, 
                action
                )
            SELECT
                id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
                fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
                vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
                cupon, repro, legal, cancela, cancela_motivo,
                iduserupdate, updatex, 
                asesor_medico_id,
                'U'
            FROM appinmater_modulo.hc_gineco
            WHERE id =?");
    $log_Gineco->execute(array($id));

    $stmt2->execute([ $med,$asesor_medico_id,$login,$hora_actual, $id ]);
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
        WHERE dni=(select dni from appinmater_modulo.hc_gineco where id = ?)");
    $log_Paciente->execute(array($id));
    echo "<div id='alerta'> Datos guardados! </div>"; 
}
function updateMedi($idx, $dni, $medi_name, $medi_dosis, $medi_frecuencia, $medi_cant_dias, $medi_init_fec, $medi_init_h, $medi_init_m, $medi_obs, $id)
{
    global $db;
    $medi=explode('|',$medi_name);
    if ($id == 0) {
        $stmt = $db->prepare("INSERT INTO hc_agenda (id,dni,medi_id,medi_name,medi_dosis,medi_frecuencia,medi_cant_dias,medi_init_fec,medi_init_h,medi_init_m,medi_obs) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($idx, $dni, $medi[0], $medi[1], $medi_dosis, $medi_frecuencia, $medi_cant_dias, $medi_init_fec, $medi_init_h, $medi_init_m, $medi_obs));
    } else {
        $stmt = $db->prepare("UPDATE hc_agenda SET medi_id=?,medi_name=?,medi_dosis=?,medi_frecuencia=?,medi_cant_dias=?,medi_init_fec=?,medi_init_h=?,medi_init_m=?,medi_obs=? WHERE id_agenda=?");
        $stmt->execute(array($medi[0], $medi[1], $medi_dosis, $medi_frecuencia, $medi_cant_dias, $medi_init_fec, $medi_init_h, $medi_init_m, $medi_obs, $id)); ?>
<script type="text/javascript">
var x = "<?php echo $idx; ?>";
window.parent.location.href = "e_gine.php?id=" + x;
</script>
<?php }
}
function updateGine_plan($id, $idp, $fec, $plan, $foto, $dni, $login = 'sistemas')
{
    global $db;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO hc_gineco_plan (idp,fec,plan) VALUES (?,?,?)");
        $stmt->execute(array($idp, $fec, $plan));
        $id = $db->lastInsertId();
    } else {
        $stmt = $db->prepare("UPDATE hc_gineco_plan SET fec=?,plan=? WHERE id=?");
        $stmt->execute(array($fec, $plan, $id));
    }

    if (isset($foto) && !empty($foto['name'])) {
        $path = $_SERVER["DOCUMENT_ROOT"] . "/storage/ginecologia_plan_trabajo/";
        $informe_name = $foto['name'];
        $nombre_original = $informe_name;
        $informe_name = preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
        $nombre_base = time() . "-" . $informe_name;
        $ruta = $path . $nombre_base;
        print($ruta);

        if (is_uploaded_file($foto['tmp_name'])) {
            move_uploaded_file($foto['tmp_name'], $ruta);

            // registrar archivo
            $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?);");
            $stmt->execute(array($nombre_base, $nombre_original, $login));
            $archivo_id = $db->lastInsertId();

            // actualizar informe
            $stmt = $db->prepare("UPDATE hc_gineco_plan set archivo_id = ? where id = ?;");
            $stmt->execute([$archivo_id, $id]);

        }
    } ?>
<script type="text/javascript">
        var x = "<?php echo $idp; ?>";
        window.parent.location.href = "e_gine.php?id=" + x + "&pop=Plan";
    </script>
<?php
}
function insertObst($dni, $fec, $med, $g_3par, $g_rn_men, $g_gem, $g_ges, $g_abo, $g_pt, $g_pp, $g_vag, $g_ces, $g_nv, $g_nm, $g_viv, $g_m1, $g_m2, $g_fup, $g_rn_may, $pes, $tal, $fur, $fpp, $dud, $fuma, $vdrl, $vdrl_f, $hb, $hb_f)
{
    $hb_f = !empty($hb_f) ? date('Y-m-d', strtotime($hb_f)) : '1899-12-30';
    $vdrl_f = !empty($vdrl_f) ? date('Y-m-d', strtotime($vdrl_f)) : '1899-12-30';
    $vdrl = !empty($vdrl) ? intval($vdrl) : 0;
    $fuma = !empty($fuma) ? intval($fuma) : 0;
    $tal = !empty($tal) ? intval($tal) : 0;
    $g_rn_may = !empty($g_rn_may) ? intval($g_rn_may) : 0;
    $pes = !empty($pes) ? intval($pes) : 0;
    $g_m1 = !empty($g_m1) ? intval($g_m1) : 0;
    $g_m2 = !empty($g_m2) ? intval($g_m2) : 0;
    $g_gem = !empty($g_gem) ? intval($g_gem) : 0;
    $g_ges = !empty($g_ges) ? intval($g_ges) : 0;
    $g_abo = !empty($g_abo) ? intval($g_abo) : 0;
    $g_pt = !empty($g_pt) ? intval($g_pt) : 0;
    $g_pp = !empty($g_pp) ? intval($g_pp) : 0;
    $g_vag = !empty($g_vag) ? intval($g_vag) : 0;
    $g_ces = !empty($g_ces) ? intval($g_ces) : 0;
    $g_nv = !empty($g_nv) ? intval($g_nv) : 0;
    $g_nm = !empty($g_nm) ? intval($g_nm) : 0;
    $g_viv = !empty($g_viv) ? intval($g_viv) : 0;
    $g_m1 = !empty($g_m1) ? intval($g_m1) : 0;
    $g_m2 = !empty($g_m2) ? intval($g_m2) : 0;
    $g_m1 = !empty($g_m1) ? intval($g_m1) : 0;
    $g_fup = !empty($g_fup) ? $g_fup : '';
    global $db;
    $stmt = $db->prepare("INSERT INTO hc_obste (dni,fec,med,g_3par,g_rn_men,g_gem,g_ges,g_abo,g_pt,g_pp,g_vag,g_ces,g_nv,g_nm,g_viv,g_m1,g_m2,g_fup,g_rn_may,pes,tal,fur,fpp,dud,fuma,vdrl,vdrl_f,hb,hb_f) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute(array($dni, $fec, $med, $g_3par, $g_rn_men, $g_gem, $g_ges, $g_abo, $g_pt, $g_pp, $g_vag, $g_ces, $g_nv, $g_nm, $g_viv, $g_m1, $g_m2, $g_fup, $g_rn_may, $pes, $tal, $fur, $fpp, $dud, $fuma, $vdrl, $vdrl_f, $hb, $hb_f));
    echo "<div id='alerta'> Datos guardados en el historial de Embarazos! </div>";
}
function updateObst($id, $dni, $g_3par, $g_rn_men, $g_gem, $g_ges, $g_abo, $g_pt, $g_pp, $g_vag, $g_ces, $g_nv, $g_nm, $g_viv, $g_m1, $g_m2, $g_fup, $g_rn_may, $pes, $tal, $fur, $fpp, $dud, $fuma, $vdrl, $vdrl_f, $hb, $hb_f, $con_fec, $con_fec_h, $con_fec_m, $con_sem, $con_eg, $con_pes, $con_pa, $con_mov, $con_ede, $con_la, $con_pla, $con_pre, $con_fcf, $con_pc, $con_lcn, $con_vv, $con_eco, $con_hb, $con_gi, $con_ori, $con_obs, $parto_sex, $parto_pes, $parto_tal, $parto_nom, $parto_nac, $parto_obs, $in_t, $in_f1, $in_h1, $in_m1, $in_f2, $in_h2, $in_m2, $in_c)
{
    $in_f2 = !empty($in_f2) ? date('Y-m-d', strtotime($in_f2)) : '1899-12-30';
    $parto_nac = !empty($parto_nac) ? date('Y-m-d', strtotime($parto_nac)) : '1899-12-30';
    $hb_f = !empty($hb_f) ? date('Y-m-d', strtotime($hb_f)) : '1899-12-30';
    $vdrl_f = !empty($vdrl_f) ? date('Y-m-d', strtotime($vdrl_f)) : '1899-12-30';
    $in_f1 = !empty($in_f1) ? date('Y-m-d', strtotime($in_f1)) : '1899-12-30';
    $vdrl = !empty($vdrl) ? intval($vdrl) : 0;
    $fuma = !empty($fuma) ? intval($fuma) : 0;
    $tal = !empty($tal) ? intval($tal) : 0;
    $g_rn_may = !empty($g_rn_may) ? intval($g_rn_may) : 0;
    $pes = !empty($pes) ? intval($pes) : 0;
    $g_m1 = !empty($g_m1) ? intval($g_m1) : 0;
    $g_m2 = !empty($g_m2) ? intval($g_m2) : 0;
    $parto_sex = !empty($parto_sex) ? intval($parto_sex) : 0;
    $parto_pes = !empty($parto_pes) ? doubleval($parto_pes) : 0.0;
    $parto_tal = !empty($parto_tal) ? doubleval($parto_tal) : 0.0;

    global $db;
    $stmt = $db->prepare("UPDATE hc_obste SET g_3par=?,g_rn_men=?,g_gem=?,g_ges=?,g_abo=?,g_pt=?,g_pp=?,g_vag=?,g_ces=?,g_nv=?,g_nm=?,g_viv=?,g_m1=?,g_m2=?,g_fup=?,g_rn_may=?,pes=?,tal=?,fur=?,fpp=?,dud=?,fuma=?,vdrl=?,vdrl_f=?,hb=?,hb_f=?,con_fec=?,con_fec_h=?,con_fec_m=?,con_sem=?,con_eg=?,con_pes=?,con_pa=?,con_mov=?,con_ede=?,con_la=?,con_pla=?,con_pre=?,con_fcf=?,con_pc=?,con_lcn=?,con_vv=?,con_eco=?,con_hb=?,con_gi=?,con_ori=?,con_obs=?,parto_sex=?,parto_pes=?,parto_tal=?,parto_nom=?,parto_nac=?,parto_obs=?,in_t=?,in_f1=?,in_h1=?,in_m1=?,in_f2=?,in_h2=?,in_m2=?,in_c=? WHERE id=?");
    $stmt->execute(array($g_3par, $g_rn_men, $g_gem, $g_ges, $g_abo, $g_pt, $g_pp, $g_vag, $g_ces, $g_nv, $g_nm, $g_viv, $g_m1, $g_m2, $g_fup, $g_rn_may, $pes, $tal, $fur, $fpp, $dud, $fuma, $vdrl, $vdrl_f, $hb, $hb_f, $con_fec, $con_fec_h, $con_fec_m, $con_sem, $con_eg, $con_pes, $con_pa, $con_mov, $con_ede, $con_la, $con_pla, $con_pre, $con_fcf, $con_pc, $con_lcn, $con_vv, $con_eco, $con_hb, $con_gi, $con_ori, $con_obs, $parto_sex, $parto_pes, $parto_tal, $parto_nom, $parto_nac, $parto_obs, $in_t, $in_f1, $in_h1, $in_m1, $in_f2, $in_h2, $in_m2, $in_c, $id));
    ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
window.parent.location.href = "n_obst.php?id=" + x;
</script>
<?php
}

function insertRepro($data, &$id) {
    global $db;

    $programa_id = $db->prepare("SELECT medios_comunicacion_id,idsedes from hc_paciente where dni = ?");
    $programa_id->execute([$data["dni"]]);
    $programa_id = $programa_id->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("insert into hc_reprod
    (dni, p_dni, fec, med, eda, poseidon, p_dtri, p_cic, p_fiv, p_icsi
    , des_dia, des_don, p_od, p_don, p_cri, p_iiu, t_mue, obs, idusercreate,programaid,sedeid) VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt2 = $db->prepare("UPDATE hc_paciente
    SET med=?, iduserupdate=?, updatex=?
    WHERE dni=?");
    
    // Comprueba y asigna valores predeterminados
    $p_dni = !empty($data["p_dni"]) ? $data["p_dni"] : '';
    $t_mue = !empty($data["t_mue"]) ? $data["t_mue"] : 0;
    
    $stmt->execute([
        $data["dni"], $p_dni, $data["fec"], $data["med"], $data["eda"], $data["poseidon"], $data["p_dtri"]?:0, $data["p_cic"], $data["p_fiv"], $data["p_icsi"]
        , $data["des_dia"], $data["des_don"], $data["p_od"], $data["p_don"]?:0, $data["p_cri"]?:0, $data["p_iiu"], $t_mue, $data["obs"], $data["idusercreate"],$programa_id['medios_comunicacion_id'],$programa_id['idsedes']]);
    $id_reprod = $db->lastInsertId();

    $log_Reprod = $db->prepare(
        "INSERT INTO appinmater_log.hc_reprod (
                    reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                    p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                    p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                    tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                    con_oi, con_end,
                    con1_med, 
                    con2_med, 
                    con3_med, 
                    con4_med, 
                    con5_med, 
                    con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                    complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                    pago_extras, pago_notas, pago_obs, repro, 
                    idusercreate, createdate, action
            )
        SELECT 
            id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
            p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
            p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
            tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
            con_oi, con_end,
            con1_med, 
            con2_med, 
            con3_med, 
            con4_med, 
            con5_med,
            con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
            complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
            pago_extras, pago_notas, pago_obs, repro, 
            idusercreate, createdate, 'I'
        FROM appinmater_modulo.hc_reprod
        WHERE id=?");
    $log_Reprod->execute([$id_reprod]);

    $hora_actual = date("Y-m-d H:i:s");
    $stmt2->execute([ $data["med"],$data["idusercreate"],$hora_actual,$data["dni"] ]);

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
    $log_Paciente->execute(array($data["dni"]));

    $id = $db->lastInsertId();
    print("<div id='alerta'> Datos guardados en el historial de Reproduccion Asistida! ".$data["dni"]."</div>");
    return $id_reprod;
}

function validarRepro($f_asp, $h_iny)
{
    global $db;
    $var = false;
    $stmt = $db->prepare("select * from hc_reprod where estado = true and f_asp=? and h_iny=?");
    $stmt->execute(array($f_asp, $h_iny));
    if ($stmt->rowCount() > 0) {
        $var = true;
    }
    return $var;
}
function validarAgendaRepro($id, $fecha)
{
    global $db;
    $var = false;
    $time = explode("T", $fecha);
    // validar aspiraciones
    $stmt = $db->prepare("
        select *
        from hc_reprod
        where estado = true and id<>? and f_asp=? and f_asp<>'' and cancela <> 1 and (des_dia is null or des_dia <> 0)");
    $stmt->execute(array($id, $fecha));
    if ($stmt->rowCount() > 0) $var = true;
    // validar transferencias
    $stmt = $db->prepare("
        select *
        from hc_reprod
        where estado = true and f_tra=? and h_tra=? and h_tra<>'' and cancela <> 1");
    $stmt->execute(array($time[0], $time[1]));
    if ($stmt->rowCount() > 0) $var = true;
    // validar ginecologia
    $timemin = explode(":", $time[1]);
    $stmt = $db->prepare("
        select *
        from hc_gineco
        where in_f2=? and in_h2=? and in_m2=? and in_h2<>'' and in_m2<>'' and in_c=1");
    $stmt->execute(array($time[0], $timemin[0], $timemin[1]));
    if ($stmt->rowCount() > 0) $var = true;
    // validar bloqueos
    $stmt = $db->prepare("
    select *
    from lab_agenda_bloqueo
    where fecha=? and hora=? and estado=1");
    $stmt->execute(array($time[0], $time[1]));
    if ($stmt->rowCount() > 0) $var = true;
    return $var;
}
function validarAgendaGine($id, $fecha, $hora)
{
    global $db;
    $var = false;
    $time = explode(":", $hora);
    //validar aspiraciones
    $stmt = $db->prepare("
        select *
        from hc_reprod
        where estado = true and f_asp=? and f_asp<>'' and cancela <> 1 and (des_dia is null or des_dia <> 0)");
    $stmt->execute(array($fecha."T".$hora));
    if ($stmt->rowCount() > 0) $var = true;
    //validar transferencias
    $stmt = $db->prepare("
        select *
        from hc_reprod
        where estado = true and f_tra=? and h_tra=? and h_tra<>'' and cancela <> 1");
    $stmt->execute(array($fecha, $hora));
    if ($stmt->rowCount() > 0) $var = true;
    //validar ginecologia
    $timemin = explode(":", $time[1]);
    $stmt = $db->prepare("
        select *
        from hc_gineco
        where id<>? and  in_f2=? and in_h2=? and in_m2=? and in_h2<>'' and in_m2<>'' and in_c=1");
    $stmt->execute(array($id, $fecha, $time[0], $time[1]));
    if ($stmt->rowCount() > 0) $var = true;
    // validar bloqueos
    $stmt = $db->prepare("
    select *
    from lab_agenda_bloqueo
    where fecha=? and hora=? and estado=1");
    $stmt->execute(array($fecha, $hora));
    if ($stmt->rowCount() > 0) $var = true;
    return $var;
}
function validarAgendaTrans($id, $fecha, $hora)
{
    global $db;
    $var = false;
    $time = explode(":", $hora);
    //validar aspiraciones
    $stmt = $db->prepare("
        select *
        from hc_reprod
        where estado = true and f_asp=? and f_asp<>'' and cancela <> 1 and (des_dia is null or des_dia <> 0)");
    $stmt->execute(array($fecha."T".$hora));
    if ($stmt->rowCount() > 0) $var = true;
    //validar transferencias
    $stmt = $db->prepare("
        select *
        from hc_reprod
        where estado = true and id<>? and f_tra=? and h_tra=? and h_tra<>'' and cancela <> 1");
    $stmt->execute(array($id, $fecha, $hora));
    if ($stmt->rowCount() > 0) $var = true;
    //validar ginecologia
    $timemin = explode(":", $time[1]);
    $stmt = $db->prepare("
        select *
        from hc_gineco
        where in_f2=? and in_h2=? and in_m2=? and in_h2<>'' and in_m2<>'' and in_c=1");
    $stmt->execute(array($fecha, $time[0], $time[1]));
    if ($stmt->rowCount() > 0) $var = true;
    // validar bloqueos
    $stmt = $db->prepare("
    select *
    from lab_agenda_bloqueo
    where fecha=? and hora=? and estado=1");
    $stmt->execute(array($fecha, $hora));
    if ($stmt->rowCount() > 0) $var = true;
    return $var;
}
function updateRepro($p_dni, $t_mue, $id, $eda, $p_dni_het, $poseidon, $p_dtri, $p_cic, $p_fiv, $p_icsi, $p_od, $p_don, $p_cri, $p_iiu, $p_extras, $n_fol, $fur,$fecha_lorelina, $f_aco, $fsh, $lh, $est, $prol, $ins, $amh, $inh, $t3, $t4, $tsh, $m_agh, $m_vdrl, $m_clam, $m_his, $m_hsg, $f_fem, $f_mas, $con_iny, $obs, $motivo_cancelacion, $f_iny, $h_iny, $f_asp, $cancela, $repro, $complicacionesparto_id, $complicacionesparto_motivo, $anestesia, $login, $idturno="3")
{
    global $db;
    if (empty($fur)) $fur = NULL;
    if (empty($f_aco)) $f_aco = NULL;
    if (empty($f_iny)) $f_iny = NULL;
    if (empty($h_iny)) $h_iny = NULL;
    if (empty($f_asp)) $f_asp = NULL;
    if (empty($fecha_lorelina)) $fecha_lorelina = NULL;
    $cant_iny = !empty($cant_iny) ? intval($cant_iny) : 0;
    $n_fol = !empty($n_fol) ? intval($n_fol) : 0;
    $t_mue = !empty($t_mue) ? intval($t_mue) : 0;
    $stmt = $db->prepare("UPDATE hc_reprod
        SET p_dni=?, t_mue=?, eda=?, p_dni_het=?, poseidon=?, p_dtri=?, p_cic=?, p_fiv=?, p_icsi=?, p_od=?, p_don=?, p_cri=?, p_iiu=?, p_extras=?, n_fol=?, fur=?,fecha_lorelina=?, f_aco=?, fsh=?, lh=?, est=?, prol=?, ins=?, amh=?, inh=?, t3=?, t4=?, tsh=?, m_agh=?, m_vdrl=?, m_clam=?, m_his=?, m_hsg=?, f_fem=?, f_mas=?, con_iny=?, obs=?, motivo_cancelacion=?, f_iny=?, h_iny=?, f_asp=?, cancela=?, repro=?, idturno=?, complicacionesparto_id=?, complicacionesparto_motivo=?, anestesia=?, iduserupdate=?,updatex=?
        WHERE id=?;");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute([
        $p_dni, $t_mue, $eda, $p_dni_het, $poseidon, $p_dtri, $p_cic, $p_fiv, $p_icsi, $p_od, $p_don, $p_cri, $p_iiu, $p_extras, $n_fol, $fur,$fecha_lorelina, $f_aco, $fsh, $lh, $est, $prol, $ins, $amh, $inh, $t3, $t4, $tsh, $m_agh, $m_vdrl, $m_clam, $m_his, $m_hsg, $f_fem, $f_mas, $con_iny, $obs, $motivo_cancelacion, $f_iny, $h_iny, $f_asp, $cancela, $repro, $idturno, $complicacionesparto_id, $complicacionesparto_motivo, $anestesia, $login, $hora_actual, $id]);

    if ($cancela == 1) {
        $stmt = $db->prepare("UPDATE appinmater_modulo.lab_aspira_dias set adju = '', rep_c = null WHERE rep_c = ?;");
        $stmt->execute([$id]);
    }
    
    $log_Reprod = $db->prepare(
        "INSERT INTO appinmater_log.hc_reprod (
                    reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                    p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                    p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                    tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                    con_oi, con_end,
                    con1_med, 
                    con2_med, 
                    con3_med, 
                    con4_med, 
                    con5_med, 
                    con_iny, con_obs, obs,motivo_cancelacion, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                    complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                    pago_extras, pago_notas, pago_obs, repro, 
                    idusercreate, createdate, action
            )
        SELECT 
            id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
            p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
            p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
            tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
            con_oi, con_end,
            con1_med, 
            con2_med, 
            con3_med, 
            con4_med, 
            con5_med,
            con_iny, con_obs, obs, motivo_cancelacion, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
            complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
            pago_extras, pago_notas, pago_obs, repro, 
            iduserupdate, updatex, 'U'
        FROM appinmater_modulo.hc_reprod
        WHERE id=?");
    $log_Reprod->execute([$id]);
}
function updateReproAndro($id, $p_dni_het)
{
    global $db;
    $stmt = $db->prepare("update hc_reprod SET p_dni_het=?,updatex=? WHERE id=?;");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute([$p_dni_het, $hora_actual, $id]);
    
}
function updateRepro_info($pro_info, $dni, $pro, $rep, $nom_pro, $n_ovo, $nof, $ins, $pn2, $pn3, $inm, $atr, $ct, $ids, $d2, $d3, $d4, $d5, $d6, $d7, $c_T, $c_C, $bio, $fin, $obs, $f_tra, $h_tra, $login, $idturno="3")
{
    global $db;
    if ($pro_info == "") {
        $stmt = $db->prepare("insert into hc_reprod_info (pro,nom_pro,n_ovo,nof,ins,pn2,pn3,inm,atr,ct,ids,d2,d3,d4,d5,d6,d7,c_T,c_C,bio,fin,obs) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($pro, $nom_pro, $n_ovo, $nof, $ins, $pn2, $pn3, $inm, $atr, $ct, $ids, $d2, $d3, $d4, $d5, $d6, $d7, $c_T, $c_C, $bio, $fin, $obs));
    } else {
        $stmt = $db->prepare("UPDATE hc_reprod_info SET nom_pro=?,n_ovo=?,nof=?,ins=?,pn2=?,pn3=?,inm=?,atr=?,ct=?,ids=?,d2=?,d3=?,d4=?,d5=?,d6=?,d7=?,c_T=?,c_C=?,bio=?,fin=?,obs=?,iduserupdate=? WHERE pro=?");
        $stmt->execute(array($nom_pro, $n_ovo, $nof, $ins, $pn2, $pn3, $inm, $atr, $ct, $ids, $d2, $d3, $d4, $d5, $d6, $d7, $c_T, $c_C, $bio, $fin, $obs, $login, $pro));


    }
    $stmt2 = $db->prepare("update hc_reprod SET f_tra=?, h_tra=?, idturno_tra=?, iduserupdate=?, updatex=? WHERE id=?;");
    $hora_actual = date("Y-m-d H:i:s");
    $f_tra = isset($f_tra) ? null : $f_tra;
    $stmt2->execute([$f_tra, $h_tra, $idturno, $login, $hora_actual, $rep]);

    $log_Reprod = $db->prepare(
        "INSERT INTO appinmater_log.hc_reprod (
                    reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                    p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                    p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                    tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                    con_oi, con_end,
                    con1_med, 
                    con2_med, 
                    con3_med, 
                    con4_med, 
                    con5_med, 
                    con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                    complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                    pago_extras, pago_notas, pago_obs, repro, 
                    idusercreate, createdate, action
            )
        SELECT 
            id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
            p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
            p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
            tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
            con_oi, con_end,
            con1_med, 
            con2_med, 
            con3_med, 
            con4_med, 
            con5_med,
            con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
            complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
            pago_extras, pago_notas, pago_obs, repro, 
            iduserupdate, updatex, 'U'
        FROM appinmater_modulo.hc_reprod
        WHERE id=?");
    $log_Reprod->execute([$rep]);
    ?>
<script type="text/javascript">
var x = "<?php echo $dni; ?>";
window.parent.location.href = "n_repro.php?id=" + x;
</script>
<?php
}
function update_eco_consultorio($id, $dni, $fconsulta, $informe, $ecos, $obs, $login)
{
    global $db;
    $path = "eco_consultorio/";
    $informe_name = "";
    // 
    if ($id == 0) {
        // datos de informe
        if ( isset($informe) ) {
            if ( !empty($informe['name']) ) {
                $informe_name = $informe['name'];
                $informe_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                $informe_name=time() . "-". $informe_name;
                $ruta_dni = $path.$dni;
                if ( !file_exists($ruta_dni) ) {
                    mkdir($path.$dni, 0755);
                }
                $ruta = $path . $dni . '/' . $informe_name;
                if ( is_uploaded_file($informe['tmp_name']) )
                    move_uploaded_file($informe['tmp_name'], $ruta);
            }
        }
        // ingreso del registro
        $stmt = $db->prepare("
        insert into hc_eco_consultorio
        (tipodocumento, documento, fconsulta, informe, obs, idusercreate, createdate) VALUES
        (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array(1, $dni, $fconsulta, $informe_name, $obs, $login, date("Y-m-d H:i:s")));
        $id = $db->lastInsertId();
        // datos de ecografias
        if ( isset($ecos) ) {
            $total = count($ecos['name']);
            $ruta_dni = $path . $dni;
            if ( !file_exists($ruta_dni) ) {
                mkdir($path.$dni, 0755);
            }
            for( $i=0 ; $i < $total ; $i++ ) {
                $eco_name = $ecos['name'][$i];
                $eco_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $eco_name);
                $eco_name=time() . "-". $eco_name;
                $eco_tmp = $ecos['tmp_name'][$i];
                if ( !empty($eco_tmp) ){
                    $ruta = $path . $dni . "/" . $eco_name;
                    move_uploaded_file($eco_tmp, $ruta);
                }
                // ingreso del registro
                $stmt = $db->prepare("
                insert into hc_eco_consultorio_img
                (id_eco_consultorio, nombre, idusercreate, createdate) VALUES
                (?, ?, ?, ?)");
                $stmt->execute(array($id, $eco_name, $login, date("Y-m-d H:i:s")));
            }
        }
        header("Location: n_eco.php?dni=" . $dni);
    }
}
function update_hematologia($id, $tipopaciente, $dni, $fresultado, $informe, $obs, $login)
{
    global $db;
    $path = "hematologia/";
    $informe_name = "";
    // 
    if ($id == 0) {
        // datos de informe
        if ( isset($informe) ) {
            if ( !empty($informe['name']) ) {
                $informe_name = $informe['name'];
                $informe_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                $informe_name = time() . "-". $informe_name;
                $ruta_dni = $path.$dni;
                if ( !file_exists($ruta_dni) ) {
                    mkdir($path.$dni, 0755);
                }
                $ruta = $path . $dni . '/' . $informe_name;
                if ( is_uploaded_file($informe['tmp_name']) ) move_uploaded_file($informe['tmp_name'], $ruta);
            }
        }
        // ingreso del registro
        $stmt = $db->prepare("
        insert into hc_hematologia
        (tipopaciente, tipodocumento, numerodocumento, fresultado, documento, obs, idusercreate, createdate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute( array($tipopaciente, "1", $dni, $fresultado, $informe_name, $obs, $login, date("Y-m-d H:i:s")) );
    }
}
function update_legal_01($id, $dni, $finforme, $informe, $tipoinforme, $obs, $login)
{
    global $db;
    $path = "legal_01/";
    $informe_name = "";
    // 
    if ($id == 0) {
        // datos de informe
        if ( isset($informe) ) {
            if ( !empty($informe['name']) ) {
                $informe_name = $informe['name'];
                $informe_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                $informe_name=time() . "-". $informe_name;
                $ruta_dni = $path.$dni;
                if ( !file_exists($ruta_dni) ) {
                    mkdir($path.$dni, 0755);
                }
                $ruta = $path . $dni . '/' . $informe_name;
                if ( is_uploaded_file($informe['tmp_name']) )
                    move_uploaded_file($informe['tmp_name'], $ruta);
            }
        }
        // ingreso del registro
        $stmt = $db->prepare("
        insert into hc_legal_01
        (tipodocumento, numerodocumento, finforme, nombre, idlegaltipodocumento, obs, idusercreate, createdate, iduserupdate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array("1", $dni, $finforme, $informe_name, $tipoinforme, $obs, $login, date("Y-m-d H:i:s"), $login));
    }
}
function update_riesgoquirurgico_01($id, $dni, $fvigencia, $informe, $obs, $nivel, $login)
{
    global $db;
    $path = "riesgo_quirurgico/";
    $informe_name = "";
    // 
    if ($id == 0) {
        // datos de informe
        if ( isset($informe) ) {
            if ( !empty($informe['name']) ) {
                $informe_name = $informe['name'];
                $informe_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                $informe_name=time() . "-". $informe_name;
                $ruta_dni = $path.$dni;
                if ( !file_exists($ruta_dni) ) {
                    mkdir($path.$dni, 0755);
                }
                $ruta = $path . $dni . '/' . $informe_name;
                if ( is_uploaded_file($informe['tmp_name']) )
                    move_uploaded_file($informe['tmp_name'], $ruta);
            }
        }
        // ingreso del registro
        $stmt = $db->prepare("
        insert into hc_riesgo_quirurgico
        (tipodocumento, numerodocumento, fvigencia, nombre, obs, nivel, idusercreate, createdate, iduserupdate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array("1", $dni, $fvigencia, $informe_name, $obs, $nivel, $login, date("Y-m-d H:i:s"), $login));
    }
}
function update_cariotipo($id, $dni, $fvigencia, $informe, $obs, $login)
{
    global $db;
    $path = "cariotipo/";
    $informe_name = "";

    if ($id == 0) {
        // datos de informe
        if ( isset($informe) ) {
            if ( !empty($informe['name']) ) {
                $informe_name = $informe['name'];
                $informe_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                $informe_name=time() . "-". $informe_name;
                $ruta_dni = $path.$dni;
                if ( !file_exists($ruta_dni) ) {
                    mkdir($path.$dni, 0755);
                }
                $ruta = $path . $dni . '/' . $informe_name;
                if ( is_uploaded_file($informe['tmp_name']) )
                    move_uploaded_file($informe['tmp_name'], $ruta);
            }
        }

        // actualizar estados anteriores
        $stmt = $db->prepare("UPDATE hc_cariotipo SET estado = 0 WHERE numerodocumento=?");
        $stmt->execute(array($dni));

        // ingreso del registro
        $stmt = $db->prepare("INSERT INTO hc_cariotipo
        (tipodocumento, numerodocumento, fvigencia, nombre, obs, idusercreate, createdate, iduserupdate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array("1", $dni, $fvigencia, $informe_name, $obs, $login, date("Y-m-d H:i:s"), $login));
    }
}
function update_enfermeria($id, $dni, $finforme, $informe, $medico, $reproasistida, $procesala, $obs, $login)
{
    global $db;
    $path = "enfermeria/";
    $informe_name = "";
    // 
    if ($id == 0) {
        // datos de informe
        if ( isset($informe) ) {
            if ( !empty($informe['name']) ) {
                $informe_name = $informe['name'];
                $informe_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                $informe_name=time() . "-". $informe_name;
                $ruta_dni = $path.$dni;
                if ( !file_exists($ruta_dni) ) {
                    mkdir($path.$dni, 0755);
                }
                $ruta = $path . $dni . '/' . $informe_name;
                if ( is_uploaded_file($informe['tmp_name']) )
                    move_uploaded_file($informe['tmp_name'], $ruta);
            }
        }
        // ingreso del registro
        $stmt = $db->prepare("
        insert into hc_enfermeria
        (tipodocumento, numerodocumento, finforme, documento, idmedico, idreproasistida, idprocesala, obs, idusercreate, createdate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute( array("1", $dni, $finforme, $informe_name, $medico, $reproasistida, $procesala, $obs, $login, date("Y-m-d H:i:s")) );
        header("Location: n_enfermeria.php?dni=" . $dni);
    }
}

function update_psicologia($id, $dni, $finforme, $informe)
{
    global $db;
    $path = "psicologia/";
    $informe_name = "";
    // 
    if ($id == 0) {
        // datos de informe
        if ( isset($informe) ) {
            if ( !empty($informe['name']) ) {
                $informe_name = $informe['name'];
                $informe_name =  preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                $informe_name=time() . "-". $informe_name;
                $ruta_dni = $path.$dni;
                if ( !file_exists($ruta_dni) ) {
                    mkdir($path.$dni, 0755);
                }
                $ruta = $path . $dni . '/' . $informe_name;
                if ( is_uploaded_file($informe['tmp_name']) )
                    move_uploaded_file($informe['tmp_name'], $ruta);
            }
        }
        // ingreso del registro
        $stmt = $db->prepare("
        insert into hc_psicologia_doc
        (tipodocumento, numerodocumento, finforme, documento, createdate) VALUES
        (?, ?, ?, ?, ?)");
        $stmt->execute( array("1", $dni, $finforme, $informe_name, date("Y-m-d H:i:s")) );
        header("Location: n_psicologia.php?dni=" . $dni);
    }
}

function updateLegal($id, $a_dni, $a_mue, $a_nom, $a_med, $a_exa, $a_sta, $a_obs, $gin, $lab, $foto)
{
    global $db;
    $gin = (empty($gin)) ? 0 : $gin;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO hc_legal (a_dni,a_mue,a_nom,a_med,a_exa,a_sta,a_obs,gin,lab) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($a_dni, $a_mue, $a_nom, $a_med, $a_exa, $a_sta, $a_obs, $gin, $lab));
        $id = $db->lastInsertId();
        if ($gin <> '') { // para q no inserte cuando es Andrologia
            $stmt = $db->prepare("UPDATE hc_gineco SET legal=?, iduserupdate=?, updatex=? WHERE id=?");
            $hora_actual = date("Y-m-d H:i:s");
            $stmt->execute(array($id, $lab, $hora_actual, $gin));

            $log_Gineco = $db->prepare(
                "INSERT INTO appinmater_log.hc_gineco (
                    gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                    man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
                    fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
                    vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                    in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
                    cupon, repro, legal, cancela, cancela_motivo,
                    isuser_log, date_log,
                    asesor_medico_id, 
                    action
                    )
                SELECT
                    id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                    man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
                    fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
                    vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                    in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
                    cupon, repro, legal, cancela, cancela_motivo,
                    iduserupdate, updatex, 
                    asesor_medico_id,
                    'U'
                FROM appinmater_modulo.hc_gineco
                WHERE id =?");
            $log_Gineco->execute(array($gin));

        }
    } else {
        $stmt = $db->prepare("UPDATE hc_legal SET a_dni=?,a_mue=?,a_nom=?,a_med=?,a_exa=?,a_sta=?,a_obs=?,gin=?,lab=? WHERE id=?");
        $stmt->execute(array($a_dni, $a_mue, $a_nom, $a_med, $a_exa, $a_sta, $a_obs, $gin, $lab, $id));
    }
    if ($foto['name'] <> "") {
        if (is_uploaded_file($foto['tmp_name'])) {
            $ruta = 'legal/' . $id . '_' . $a_dni . '.pdf';
            move_uploaded_file($foto['tmp_name'], $ruta);
            $stmt = $db->prepare("UPDATE hc_legal SET fec_doc=CURDATE() WHERE id=?");
            $stmt->execute(array($id));
        }
    } ?>
<script type="text/javascript">
window.parent.location.href = "lista.php";
</script>
<?php
}
function updateAnalisis($id, $a_dni, $a_mue, $a_nom, $a_med, $a_exa, $a_sta, $a_obs, $cor, $lab, $informe, $video, $idf=0, $path='')
{
    global $db;
    $video_path = $_SERVER["DOCUMENT_ROOT"] . "/storage/analisis_archivo/";
    $archivo_path = '';
    $cor = !empty($cor) ? intval($cor) : 0;
    $idf = empty($idf) ? NULL : $idf;
    if ($id == 0) {
        $stmt = $db->prepare("INSERT INTO hc_analisis
        (a_dni, a_mue, a_nom, a_med, a_exa, a_sta, a_obs, cor, lab, idf, idusercreate, iduserupdate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($a_dni, $a_mue, $a_nom, $a_med, $a_exa, $a_sta, $a_obs, $cor, $lab, $idf, $lab, $lab));
        $id = $db->lastInsertId();
    } else {
        $stmt = $db->prepare("UPDATE hc_analisis SET a_dni=?,a_mue=?,a_nom=?,a_med=?,a_exa=?,a_sta=?,a_obs=?,cor=?,lab=?,idf=? WHERE id=?");
        $stmt->execute(array($a_dni, $a_mue, $a_nom, $a_med, $a_exa, $a_sta, $a_obs, $cor, $lab, $idf, $id));
    }

    if ($informe['name'] <> "") {
        if (is_uploaded_file($informe['tmp_name'])) {
            move_uploaded_file($informe['tmp_name'], 'analisis/' . $id . '_' . $a_dni . '.pdf');
        }
    }

    if (isset($video) && !empty($video['name'])) {
        $informe_name = $video['name'];
        $nombre_original = $informe_name;
        $informe_name = preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
        $nombre_base = time() . "-" . $informe_name;
        $archivo_path = $video_path . $nombre_base;

        if (is_uploaded_file($video['tmp_name'])) {
            move_uploaded_file($video['tmp_name'], $archivo_path);

            // registrar archivo
            $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?);");
            $stmt->execute(array($nombre_base, $nombre_original, $lab));
            $archivo_id = $db->lastInsertId();

            // actualizar informe
            $stmt = $db->prepare("UPDATE hc_analisis set archivo_id = ?, iduserupdate = ? where id = ?;");
            $stmt->execute([$archivo_id, $lab, $id]);
        }
    }

    return [
        'procedimiento' => $id,
        'archivo_path' => $archivo_path
    ];
}

function insertAnalisisTip($nom, $lab)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO hc_analisis_tip (nom,lab) VALUES (?,?)");
    $stmt->execute(array($nom, $lab));
    echo "<div id='alerta'> Examen Agregado! </div>";
}
function insertDisponi($med, $fec, $ini, $fin, $obs)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO hc_disponible (med, fec, ini, fin, obs) VALUES (?,?,?,?,?)");
    $stmt->execute(array($med, $fec, $ini, $fin, $obs));
    echo "<div id='alerta'>Evento Agendado! </div>";
}
// --------------------------------------- LABORATORIO -------------------------------------------------------------------------------------
function Descongela_Ovo_Emb($des_dia, $pro, $dni, $tip, $rep, $cont, $obs_med)
{
    global $db;
    if ($cont > 0 && isset($des_dia) && $des_dia >= 0) {
        $VeriPro = $db->prepare("SELECT pro FROM lab_aspira WHERE pro=? and estado is true");
        $VeriPro->execute(array($pro));
        if ($VeriPro->rowCount() > 0) { ?>
<script type="text/javascript">
alert("Este protocolo YA EXISTE! Ingrese uno diferente");
var x = "<?php echo $rep; ?>";
window.parent.location.href = "le_aspi9.php?rep=" + x;
</script>
<?php } else {
            $c = 0;
            for ($p = 1; $p <= $cont; $p++) {
                if (isset($_POST['c' . $p])) {
                    $tan = explode("|", $_POST['c' . $p]);
                    $stmt2 = $db->prepare("UPDATE lab_aspira_dias SET des=? WHERE pro=? AND ovo=? and estado is true");
                    $stmt2->execute(array(1, $tan[0], $tan[1])); // Descongela ovo/emb
                    $c++;
                    $rOvo = $db->prepare("SELECT * FROM lab_aspira_dias WHERE pro=? and estado is true AND ovo=?");
                    $rOvo->execute(array($tan[0], $tan[1]));
                    $ovo = $rOvo->fetch(PDO::FETCH_ASSOC);
                    // todos los cogelados antiguos van como vacios , el d6f_cic no se inserta porque es el ultimo y debe ser vacio tb
                    if ($ovo['d0f_cic'] == 'C') $ovo['d0f_cic'] = '';
                    if ($ovo['d1f_cic'] == 'C') $ovo['d1f_cic'] = '';
                    if ($ovo['d2f_cic'] == 'C') $ovo['d2f_cic'] = '';
                    if ($ovo['d3f_cic'] == 'C') $ovo['d3f_cic'] = '';
                    if ($ovo['d4f_cic'] == 'C') $ovo['d4f_cic'] = '';
                    if ($ovo['d5f_cic'] == 'C') $ovo['d5f_cic'] = '';
                    $stmt = $db->prepare("INSERT INTO lab_aspira_dias (pro,ovo,anu,obs,pro_c,ovo_c,d0est,d0mor,d0z_pel,d0rot,d0inte,d1c_pol,d1pron,d1t_pro,d1d_nuc,d1hal,d0f_cic,d2cel,d2fra,d2sim,d1f_cic,d3cel,d3fra,d3sim,d3c_bio,d2f_cic,d4cel,d4fra,d4sim,d4mci,d4tro,d3f_cic,d5cel,d5mci,d5tro,d5fra,d5vac,d5col,d5d_bio,d4f_cic,d6cel,d6mci,d6tro,d6fra,d6vac,d6col,d6d_bio,d5f_cic,ngs1,ngs2,ngs3) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $stmt->execute(array($pro, $c, $ovo['anu'], $ovo['obs'], $ovo['pro'], $ovo['ovo'], $ovo['d0est'], $ovo['d0mor'], $ovo['d0z_pel'], $ovo['d0rot'], $ovo['d0inte'], $ovo['d1c_pol'], $ovo['d1pron'], $ovo['d1t_pro'], $ovo['d1d_nuc'], $ovo['d1hal'], $ovo['d0f_cic'], $ovo['d2cel'], $ovo['d2fra'], $ovo['d2sim'], $ovo['d1f_cic'], $ovo['d3cel'], $ovo['d3fra'], $ovo['d3sim'], $ovo['d3c_bio'], $ovo['d2f_cic'], $ovo['d4cel'], $ovo['d4fra'], $ovo['d4sim'], $ovo['d4mci'], $ovo['d4tro'], $ovo['d3f_cic'], $ovo['d5cel'], $ovo['d5mci'], $ovo['d5tro'], $ovo['d5fra'], $ovo['d5vac'], $ovo['d5col'], $ovo['d5d_bio'], $ovo['d4f_cic'], $ovo['d6cel'], $ovo['d6mci'], $ovo['d6tro'], $ovo['d6fra'], $ovo['d6vac'], $ovo['d6col'], $ovo['d6d_bio'], $ovo['d5f_cic'], $ovo['ngs1'], $ovo['ngs2'], $ovo['ngs3']));
                    if (file_exists("emb_pic/p" . $ovo['pro'] . "d0_" . $ovo['ovo'] . ".jpg")) {
                        $img = "emb_pic/p" . $ovo['pro'] . "d0_" . $ovo['ovo'] . ".jpg";
                        $newimg = "emb_pic/p" . $pro . "d0_" . $c . ".jpg";
                        if (!copy($img, $newimg)) echo "fallo al copiar imagen";
                    }
                    if (file_exists("emb_pic/p" . $ovo['pro'] . "d1_" . $ovo['ovo'] . ".jpg")) {
                        $img = "emb_pic/p" . $ovo['pro'] . "d1_" . $ovo['ovo'] . ".jpg";
                        $newimg = "emb_pic/p" . $pro . "d1_" . $c . ".jpg";
                        if (!copy($img, $newimg)) echo "fallo al copiar imagen";
                    }
                    if (file_exists("emb_pic/p" . $ovo['pro'] . "d2_" . $ovo['ovo'] . ".jpg")) {
                        $img = "emb_pic/p" . $ovo['pro'] . "d2_" . $ovo['ovo'] . ".jpg";
                        $newimg = "emb_pic/p" . $pro . "d2_" . $c . ".jpg";
                        if (!copy($img, $newimg)) echo "fallo al copiar imagen";
                    }
                    if (file_exists("emb_pic/p" . $ovo['pro'] . "d3_" . $ovo['ovo'] . ".jpg")) {
                        $img = "emb_pic/p" . $ovo['pro'] . "d3_" . $ovo['ovo'] . ".jpg";
                        $newimg = "emb_pic/p" . $pro . "d3_" . $c . ".jpg";
                        if (!copy($img, $newimg)) echo "fallo al copiar imagen";
                    }
                    if (file_exists("emb_pic/p" . $ovo['pro'] . "d4_" . $ovo['ovo'] . ".jpg")) {
                        $img = "emb_pic/p" . $ovo['pro'] . "d4_" . $ovo['ovo'] . ".jpg";
                        $newimg = "emb_pic/p" . $pro . "d4_" . $c . ".jpg";
                        if (!copy($img, $newimg)) echo "fallo al copiar imagen";
                    }
                    if (file_exists("emb_pic/p" . $ovo['pro'] . "d5_" . $ovo['ovo'] . ".jpg")) {
                        $img = "emb_pic/p" . $ovo['pro'] . "d5_" . $ovo['ovo'] . ".jpg";
                        $newimg = "emb_pic/p" . $pro . "d5_" . $c . ".jpg";
                        if (!copy($img, $newimg)) echo "fallo al copiar imagen";
                    }
                    if (file_exists("emb_pic/p" . $ovo['pro'] . "d6_" . $ovo['ovo'] . ".jpg")) {
                        $img = "emb_pic/p" . $ovo['pro'] . "d6_" . $ovo['ovo'] . ".jpg";
                        $newimg = "emb_pic/p" . $pro . "d6_" . $c . ".jpg";
                        if (!copy($img, $newimg)) echo "fallo al copiar imagen";
                    }
                }
            }
            $sta = "Dia " . $des_dia;
            if ($des_dia == 0) {
                $fec0 = date("Y-m-d");
                $fec1 = endCycle($fec0, 1);
                $fec2 = endCycle($fec0, 2);
                $fec3 = endCycle($fec0, 3);
                $fec4 = endCycle($fec0, 4);
                $fec5 = endCycle($fec0, 5);
                $fec6 = endCycle($fec0, 6);
            }
            if ($des_dia == 1) {
                $fec0 = "";
                $fec1 = date("Y-m-d");
                $fec2 = endCycle($fec0, 1);
                $fec3 = endCycle($fec0, 2);
                $fec4 = endCycle($fec0, 3);
                $fec5 = endCycle($fec0, 4);
                $fec6 = endCycle($fec0, 5);
            }
            if ($des_dia == 2) {
                $fec0 = $fec1 = "";
                $fec2 = date("Y-m-d");
                $fec3 = endCycle($fec0, 1);
                $fec4 = endCycle($fec0, 2);
                $fec5 = endCycle($fec0, 3);
                $fec6 = endCycle($fec0, 4);
            }
            if ($des_dia == 3) {
                $fec0 = $fec1 = $fec2 = "";
                $fec3 = date("Y-m-d");
                $fec4 = endCycle($fec0, 1);
                $fec5 = endCycle($fec0, 2);
                $fec6 = endCycle($fec0, 3);
            }
            if ($des_dia == 4) {
                $fec0 = $fec1 = $fec2 = $fec3 = "";
                $fec4 = date("Y-m-d");
                $fec5 = endCycle($fec0, 1);
                $fec6 = endCycle($fec0, 2);
            }
            if ($des_dia == 5) {
                $fec0 = $fec1 = $fec2 = $fec3 = $fec4 = "";
                $fec5 = date("Y-m-d");
                $fec6 = endCycle($fec0, 1);
            }
            if ($des_dia == 6) {
                $fec0 = $fec1 = $fec2 = $fec3 = $fec4 = $fec5 = "";
                $fec6 = date("Y-m-d");
            }
            $fec0 = !empty($fec0) ? date("Y-m-d", strtotime($fec0)) : '1899-12-30';
            $fec1 = !empty($fec1) ? date("Y-m-d", strtotime($fec1)) : '1899-12-30';
            $fec2 = !empty($fec2) ? date("Y-m-d", strtotime($fec2)) : '1899-12-30';
            $fec3 = !empty($fec3) ? date("Y-m-d", strtotime($fec3)) : '1899-12-30';
            $fec4 = !empty($fec4) ? date("Y-m-d", strtotime($fec4)) : '1899-12-30';
            $fec5 = !empty($fec5) ? date("Y-m-d", strtotime($fec5)) : '1899-12-30';
            $fec6 = !empty($fec6) ? date("Y-m-d", strtotime($fec6)) : '1899-12-30';
            $obs_med = !empty($obs_med) ? $obs_med : '';
            $stmt = $db->prepare("INSERT INTO lab_aspira (pro,rep,tip,dni,n_ovo,sta,dias,fec0,fec1,fec2,fec3,fec4,fec5,fec6,obs_med) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute(array($pro, $rep, $tip, $dni, $c, $sta, $des_dia, $fec0, $fec1, $fec2, $fec3, $fec4, $fec5, $fec6, $obs_med));
            $rVeces = $db->prepare("SELECT pro FROM lab_aspira WHERE dni=? and estado is true");
            $rVeces->execute(array($dni));
            $vec = $rVeces->rowCount();
            $stmt2 = $db->prepare("UPDATE lab_aspira SET vec=? WHERE pro=? and estado is true");
            $stmt2->execute(array($vec, $pro));
            ?>
<script type="text/javascript">
window.parent.location.href = "lista_pro.php";
</script>
<?php }
    }
}

function lab_insertAspi($pro, $rep, $tip, $dni, $f_pun, $o_ovo, $pen, $end, $n_ovo, $obs, $obs_med, $s_pun, $s_cum, $sta, $dias, $n_ins, $hra, $emb, $hra_a, $emb_a, $f_fin, $p_cic, $p_fiv, $p_icsi, $p_cri, $pago_extras, $inc, $login)
{
    global $db;
    $ve=0;
    $dias = !empty($dias) ? intval($dias) : 0;
    $n_ins = !empty($n_ins) ? intval($n_ins) : 0;
    $emb = !empty($emb) ? intval($emb) : 0;
    $emb_a = !empty($emb_a) ? intval($emb_a) : 0;
    try {
        $stmt = $db->prepare("INSERT INTO lab_aspira (pro,rep,tip,dni,f_pun,o_ovo,pen,endx,n_ovo,obs,obs_med,s_pun,s_cum,sta,dias,n_ins,hra0,emb0,hra_a,emb_a,f_fin,inc) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($pro, $rep, $tip, $dni, $f_pun, $o_ovo, $pen, $end, $n_ovo, $obs, $obs_med, $s_pun, $s_cum, $sta, $dias, $n_ins, $hra, $emb, $hra_a, $emb_a, $f_fin, $inc));
        $rVeces = $db->prepare("SELECT pro FROM lab_aspira WHERE dni=? and estado is true");
        $rVeces->execute(array($dni));
        $vec = $rVeces->rowCount();
        $stmt2 = $db->prepare("UPDATE lab_aspira SET vec=? WHERE pro=? and estado is true");
        $stmt2->execute(array($vec, $pro));
        $stmt3 = $db->prepare("update hc_reprod SET p_cic=?, p_fiv=?, p_icsi=?, p_cri=?, pago_extras=?, iduserupdate=?, updatex=? WHERE id=?");
        $hora_actual = date("Y-m-d H:i:s");
        $stmt3->execute([$p_cic, $p_fiv, $p_icsi, $p_cri, $pago_extras, $login, $hora_actual, $rep]); 
        
        $log_Reprod = $db->prepare(
            "INSERT INTO appinmater_log.hc_reprod (
                        reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                        p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                        p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                        tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                        con_oi, con_end,
                        con1_med, 
                        con2_med, 
                        con3_med, 
                        con4_med, 
                        con5_med, 
                        con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                        complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                        pago_extras, pago_notas, pago_obs, repro, 
                        idusercreate, createdate, action
                )
            SELECT 
                id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
                con_oi, con_end,
                con1_med, 
                con2_med, 
                con3_med, 
                con4_med, 
                con5_med,
                con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                pago_extras, pago_notas, pago_obs, repro, 
                iduserupdate, updatex, 'U'
            FROM appinmater_modulo.hc_reprod
            WHERE id=?");
        $log_Reprod->execute([$rep]);
        ?>
        <script type="text/javascript">
            var x = "<?php echo $pro; ?>";
            window.parent.location.href = "le_aspi0.php?id=" + x;
        </script>
    <?php } catch (PDOException $e) {
        if ($e->getCode() == 23000) { ?>
            <script type="text/javascript">
                alert("Este protocolo YA EXISTE! Ingrese uno diferente");
                window.parent.location.href = "lista_pro.php";
            </script>
        <?php } else {
            echo $e->getMessage();
        }
    }
}

function lab_retiroEmbrio($dni, $fec, $med, $emb, $embriologo, $n_prot,$embriones,$login,$img,$cri,$tip){

    global $db;
    $dia = 0;
    $rTras = $db->prepare("SELECT pro FROM lab_aspira WHERE pro=? and estado is true");
    $rTras->execute(array($n_prot));
    $fechaActual = date('Y-m-d H:i:s');
    $sta = "Dia " . $dia;

    try {
        if ($rTras->rowCount() == 0) {

            $programa_id = $db->prepare("SELECT medios_comunicacion_id,idsedes from hc_paciente where dni = ?");
            $programa_id->execute([$dni]);
            $programa_id = $programa_id->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $db->prepare("INSERT INTO hc_reprod (dni,fec,med,cancela,programaid,sedeid,idusercreate) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute(array($dni, $fec, $med, 5,$programa_id['medios_comunicacion_id'],$programa_id['idsedes'], $login)); // cancela = 5 significa q es una reproduccion RETIRO
            $rep = $db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO hc_antece_trata (dni,fec,pro,med,medica,fol,ovo,emb,dia,cri,res,tras,embriologo,id_reprod,tip_ret) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$dni, $fec, 'TRANSFERENCIA', $med, '', '', $emb, $emb, $dia, $cri,'', 5,$embriologo,$rep,$tip]);

            $stmt1 = $db->prepare("INSERT INTO lab_aspira (pro,rep,tip,dni,n_ovo,dias,sta,idusercreate,createdate) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt1->execute(array($n_prot, $rep, 'X', $dni, 0, $dia, $sta,$login, $fechaActual));


            foreach ($embriones as $embrion) {

                $stmt = $db->prepare("INSERT INTO appinmater_modulo.lab_aspira_dias 
                                        (pro, ovo, pro_c, ovo_c, anu, d0est, d0mor, d0z_pel, d0rot, d0inte, d0f_cic, d1est, d1c_pol, d1pron, d1t_pro, d1d_nuc, d1hal, d1f_cic, d2cel, d2fra, d2sim, d2f_cic, d3cel, d3fra, d3sim, d3c_bio, d3f_cic, d4cel, d4fra, d4sim, d4mci, d4tro, d4f_cic, d5cel, d5mci, d5tro, d5fra, d5vac, d5col, d5d_bio, d5kid, d5kid_tipo, d5kid_decimal, d5f_cic, d6cel, d6mci, d6tro, d6fra, d6vac, d6col, d6d_bio, d6kid, d6kid_tipo, d6kid_decimal, d6f_cic, obs, t, c, g, p, col, des, don, analizar, ngs1, ngs2, ngs3, valores_mitoscore, prioridad_transferencia, adju, idusercreate, createdate, rep_c,id_estado)
                                    SELECT 
                                        ? as pro, ovo, ? as pro_c, ovo_c, anu, d0est, d0mor, d0z_pel, d0rot, d0inte, d0f_cic, d1est, d1c_pol, d1pron, d1t_pro, d1d_nuc, d1hal, d1f_cic, d2cel, d2fra, d2sim, d2f_cic, d3cel, d3fra, d3sim, d3c_bio, d3f_cic, d4cel, d4fra, d4sim, d4mci, d4tro, d4f_cic, d5cel, d5mci, d5tro, d5fra, d5vac, d5col, d5d_bio, d5kid, d5kid_tipo, d5kid_decimal, d5f_cic, d6cel, d6mci, d6tro, d6fra, d6vac, d6col, d6d_bio, d6kid, d6kid_tipo, d6kid_decimal, d6f_cic, obs, t, c, g, p, col, des, don, analizar, ngs1, ngs2, ngs3, valores_mitoscore, prioridad_transferencia, adju, ? as idusercreate, ? as createdate, ? as rep_c, ? as id_estado
                                    FROM appinmater_modulo.lab_aspira_dias 
                                    WHERE pro = ? and ovo = ? and estado is true;");

                $stmt->execute(array($n_prot, $embrion['pro'], $login, $fechaActual,$rep,3,$embrion['pro'],$embrion['ovo'])); 

                $stmt = $db->prepare("UPDATE lab_aspira_dias SET id_estado = 3, rep_c = ? where pro = ? and ovo = ? and estado is true;");
                $stmt->execute(array($rep, $embrion['pro'],$embrion['ovo']));
        
            }

            if ($img['name'] <> "") {
                if (is_uploaded_file($img['tmp_name'])) {
                    $ruta = '../retiro_embrio/retiro_' . $n_prot . '.pdf';
                    move_uploaded_file($img['tmp_name'], $ruta);
                }
            } 

            $response = array(
                'message' => "Retiro guardados exitosamente.",
                'status' => true
            );

        } else { 
            
            $response = array(
                'message' => "Este protocolo YA EXISTE! Ingrese uno diferente.",
                'status' => false
            );
        }

    } catch (\Throwable $th) {
        $response = array(
            'message' => "Error: " . $th,
            'status' => false
        );
    }

    return $response;
}

function lab_inserAspiTraslado($id_tra, $dni, $ovos, $des_dia, $pro, $fec_tra, $med, $img) {
    global $db;
    $rTras = $db->prepare("SELECT pro FROM lab_aspira WHERE pro=? and estado is true");
    $rTras->execute(array($pro));
    if ($rTras->rowCount() == 0) {
        $sta = "Dia " . $des_dia;
        if ($des_dia == 0) {
            $fec0 = $fec_tra;
            $fec1 = endCycle($fec0, 1);
            $fec2 = endCycle($fec0, 2);
            $fec3 = endCycle($fec0, 3);
            $fec4 = endCycle($fec0, 4);
            $fec5 = endCycle($fec0, 5);
            $fec6 = endCycle($fec0, 6);
        }
        if ($des_dia == 1) {
            $fec0 = "1899-12-30";
            $fec1 = $fec_tra;
            $fec2 = endCycle($fec0, 1);
            $fec3 = endCycle($fec0, 2);
            $fec4 = endCycle($fec0, 3);
            $fec5 = endCycle($fec0, 4);
            $fec6 = endCycle($fec0, 5);
        }
        if ($des_dia == 2) {
            $fec0 = $fec1 = "1899-12-30";
            $fec2 = $fec_tra;
            $fec3 = endCycle($fec0, 1);
            $fec4 = endCycle($fec0, 2);
            $fec5 = endCycle($fec0, 3);
            $fec6 = endCycle($fec0, 4);
        }
        if ($des_dia == 3) {
            $fec0 = $fec1 = $fec2 = "1899-12-30";
            $fec3 = $fec_tra;
            $fec4 = endCycle($fec0, 1);
            $fec5 = endCycle($fec0, 2);
            $fec6 = endCycle($fec0, 3);
        }
        if ($des_dia == 4) {
            $fec0 = $fec1 = $fec2 = $fec3 = "1899-12-30";
            $fec4 = $fec_tra;
            $fec5 = endCycle($fec0, 1);
            $fec6 = endCycle($fec0, 2);
        }
        if ($des_dia == 5) {
            $fec0 = $fec1 = $fec2 = $fec3 = $fec4 = "1899-12-30";
            $fec5 = $fec_tra;
            $fec6 = endCycle($fec0, 1);
        }
        if ($des_dia == 6) {
            $fec0 = $fec1 = $fec2 = $fec3 = $fec4 = $fec5 = "1899-12-30";
            $fec6 = $fec_tra;
        }

        $programa_id = $db->prepare("SELECT medios_comunicacion_id,idsedes from hc_paciente where dni = ?");
        $programa_id->execute([$dni]);
        $programa_id = $programa_id->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("insert into hc_reprod (dni,fec,med,cancela,programaid,sedeid) VALUES (?,?,?,?,?,?)");
        $stmt->execute(array($dni, $fec_tra, $med, 2,$programa_id['medios_comunicacion_id'],$programa_id['idsedes'])); // cancela = 2 significa q es una reproduccion traslado
        $rep = $db->lastInsertId();

        $log_Reprod = $db->prepare(
            "INSERT INTO appinmater_log.hc_reprod (
                        reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                        p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                        p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                        tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                        con_oi, con_end,
                        con1_med, 
                        con2_med, 
                        con3_med, 
                        con4_med, 
                        con5_med, 
                        con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                        complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                        pago_extras, pago_notas, pago_obs, repro, 
                        idusercreate, createdate, action
                )
            SELECT 
                id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
                con_oi, con_end,
                con1_med, 
                con2_med, 
                con3_med, 
                con4_med, 
                con5_med,
                con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                pago_extras, pago_notas, pago_obs, repro, 
                idusercreate, createdate, 'I'
            FROM appinmater_modulo.hc_reprod
            WHERE id=?");
        $log_Reprod->execute([$rep]);

        $stmt1 = $db->prepare("INSERT INTO lab_aspira (pro,rep,tip,dni,n_ovo,dias,sta,fec0,fec1,fec2,fec3,fec4,fec5,fec6) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt1->execute(array($pro, $rep, 'T', $dni, $ovos, $des_dia, $sta, $fec0, $fec1, $fec2, $fec3, $fec4, $fec5, $fec6));

        for ($i = 1; $i <= $ovos; $i++) {
            lab_insertAspi_ovos($pro, $i);
        }
        $stmt2 = $db->prepare("UPDATE hc_antece_trata SET tras=? WHERE id=?");
        $stmt2->execute(array(2, $id_tra));

        if ($img['name'] <> "") {
            if (is_uploaded_file($img['tmp_name'])) {
                $ruta = 'emb_pic/traslado_' . $pro . '.pdf';
                move_uploaded_file($img['tmp_name'], $ruta);
            }
        } ?>
        <script type="text/javascript">
            window.parent.location.href = "lista_pro_t.php";
        </script>
    <?php } else { ?>
        <script type="text/javascript">
            alert("Este protocolo YA EXISTE! Ingrese uno diferente");
            window.parent.location.href = "lista_pro.php";
        </script>
    <?php }
}
function lab_updateAspi($pro, $sta, $dias, $f_fin)
{
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira SET sta=?,dias=?,f_fin=? WHERE pro=? and estado is true");
    $stmt->execute(array($sta, $dias, $f_fin, $pro));
    echo "<div id='alerta'> Datos Guardados!</div>";
}
function lab_updateAspi_sta($pro, $sta, $dias, $hra, $emb, $hra_c, $emb_c)
{
    global $db;
    $emb_c = ($emb_c !== '') ? intval($emb_c) : 0;

    $stmt = $db->prepare("UPDATE lab_aspira SET sta=?,dias=?,hra" . ($dias - 1) . "=?,emb" . ($dias - 1) . "=?,hra" . ($dias - 1) . "c=?,emb" . ($dias - 1) . "c=? WHERE pro=? and estado is true");
    $stmt->execute(array($sta, $dias, $hra, $emb, $hra_c, $emb_c, $pro));
}

function lab_updateAspi_sta_T($id, $pro, $dia, $t_cat, $s_gui, $s_cat, $endo, $inte, $eco, $med, $emb, $obs, $login = '') {
    global $db;

    $stmt = $db->prepare("SELECT pro FROM lab_aspira_t where pro = ? and estado is true;");
    $stmt->execute([$pro]);
    if ($stmt->rowCount() == 0) {
        $stmt = $db->prepare("INSERT INTO lab_aspira_t (pro,dia,t_cat,s_gui,s_cat,endo,inte,eco,med,emb,obs,idusercreate) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($pro, $dia, $t_cat, $s_gui, $s_cat, $endo, $inte, $eco, $med, $emb, $obs, $login));
    } else {
        $stmt = $db->prepare("UPDATE lab_aspira_t SET t_cat=?,s_gui=?,s_cat=?,endo=?,inte=?,eco=?,med=?,emb=?,obs=?,iduserupdate=? WHERE pro=? and lab_aspira_t.estado is true");
        $stmt->execute(array($t_cat, $s_gui, $s_cat, $endo, $inte, $eco, $med, $emb, $obs, $login, $pro));
    }
}

function lab_updateAspi_fin($pro)
{
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira SET f_fin=? WHERE pro=? and estado is true");
    $stmt->execute(array(date("Y-m-d"), $pro));
}
function lab_updateAspi_fec_dia($pro, $fec0, $fec1, $fec2, $fec3, $fec4, $fec5, $fec6)
{
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira SET fec0=?,fec1=?,fec2=?,fec3=?,fec4=?,fec5=?,fec6=? WHERE pro=? and estado is true");
    $stmt->execute(array($fec0, $fec1, $fec2, $fec3, $fec4, $fec5, $fec6, $pro));
}

function lab_don_todo($id)
{
    global $db;
    $stmt = $db->prepare("update hc_reprod SET don_todo=?, iduserupdate=?, updatex=? WHERE id=?;");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute([1, $login, $hora_actual, $id]); 
    
    $log_Reprod = $db->prepare(
        "INSERT INTO appinmater_log.hc_reprod (
                    reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                    p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                    p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                    tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                    con_oi, con_end,
                    con1_med, 
                    con2_med, 
                    con3_med, 
                    con4_med, 
                    con5_med, 
                    con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                    complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                    pago_extras, pago_notas, pago_obs, repro, 
                    idusercreate, createdate, action
            )
        SELECT 
            id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
            p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
            p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
            tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
            con_oi, con_end,
            con1_med, 
            con2_med, 
            con3_med, 
            con4_med, 
            con5_med,
            con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
            complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
            pago_extras, pago_notas, pago_obs, repro, 
            iduserupdate, updatex, 'U'
        FROM appinmater_modulo.hc_reprod
        WHERE id=?");
    $log_Reprod->execute([$id]);
    ?>
<script type="text/javascript">
window.parent.location.href = "lista_pro.php";
</script>
<?php }

function lab_updateRepro0($data)
{
    $id = $data["rep"];
    $p_cic = $data["p_cic"];
    $dia = $data["dia"];
    $hra_a = $data["hra_a"];
    $obs = $data["obs"];
    $obs_med = $data["obs_med"];
    $fin = $data["fin"];
    $hra_c = $data["hra0c"];
    $inc = !empty($data["inc"]) ? intval($data["inc"]) : 0;
    $hoja = !empty($data["hoja"]) ? intval($data["hoja"]) : 0;
    $book = !empty($data["book"]) ? intval($data["book"]) : 0;
    $emb_a = !empty($data["emb_a"]) ? intval($data["emb_a"]) : 0;
    $emb = !empty($data["emb0"]) ? $data["emb0"] : 0;
    $hra = !empty($data["hra0"]) ? $data["hra0"] : '';
    $n_ins = !empty($data["n_ins"]) ? intval($data["n_ins"]) : 0;
    $s_cum = !empty($data["s_cum"]) ? intval($data["s_cum"]) : 0;
    $s_pun = !empty($data["s_pun"]) ? intval($data["s_pun"]) : 0;
    $end = !empty($data["endx"]) ? intval($data["endx"]) : 0;
    $pen = !empty($data["pen"]) ? intval($data["pen"]) : 0;
    $pago_extras = !empty($data["p_extras"]) ? $data["p_extras"] : '';
    $p_cri = !empty($data["p_cri"]) ? intval($data["p_cri"]) : 0;
    $p_icsi = !empty($data["p_icsi"]) ? intval($data["p_icsi"]) : 0;
    $p_fiv = !empty($data["p_fiv"]) ? intval($data["p_fiv"]) : 0;
    $o_ovo = !empty($data["o_ovo"]) ? $data["o_ovo"] : '';
    $emb_c = !empty($data['emb0c']) ? intval($data['emb0c']) : 0;
    $fec0 = !empty($fec0) ? date('Y-m-d', strtotime($fec0)) : '1899-12-30';
    

    global $db;
    $stmt = $db->prepare("update hc_reprod SET p_cic=?, p_fiv=?, p_icsi=?, p_cri=?, pago_extras=?, iduserupdate=?, updatex=? WHERE id=?;");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute([$p_cic, $p_fiv, $p_icsi, $p_cri, $pago_extras, $data["iduserupdate"], $hora_actual, $id]);

    $log_Reprod = $db->prepare(
        "INSERT INTO appinmater_log.hc_reprod (
                    reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                    p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                    p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                    tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                    con_oi, con_end,
                    con1_med, 
                    con2_med, 
                    con3_med, 
                    con4_med, 
                    con5_med, 
                    con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                    complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                    pago_extras, pago_notas, pago_obs, repro, 
                    idusercreate, createdate, action
            )
        SELECT 
            id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
            p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
            p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
            tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
            con_oi, con_end,
            con1_med, 
            con2_med, 
            con3_med, 
            con4_med, 
            con5_med,
            con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
            complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
            pago_extras, pago_notas, pago_obs, repro, 
            iduserupdate, updatex, 'U'
        FROM appinmater_modulo.hc_reprod
        WHERE id=?");
    $log_Reprod->execute([$id]);

    $stmt2 = $db->prepare("UPDATE lab_aspira SET o_ovo=?,pen=?,endx=?,s_pun=?,s_cum=?,n_ins=?,hra0=?,emb0=?,hra0c=?,emb0c=?,hra_a=?,emb_a=?,obs=?,obs_med=?,book=?,hoja=?,inc=?,fec0=? WHERE rep=? and estado is true");
    $stmt2->execute(array($o_ovo, $pen, $end, $s_pun, $s_cum, $n_ins, $hra, $emb, $hra_c, $emb_c, $hra_a, $emb_a, $obs, $obs_med, $book, $hoja, $inc, $fec0, $id));

    $stmt = $db->prepare("SELECT dni, p_dni FROM hc_reprod WHERE estado = true and id = ?");
    $stmt->execute(array($id));
    $consulta1 = $stmt->fetch(PDO::FETCH_ASSOC);
    $dni = $consulta1['dni'];
    $p_dni = $consulta1['p_dni'];

    $stmt3 = $db->prepare("SELECT pro FROM lab_aspira WHERE rep = ? and estado is true");
    $stmt3->execute(array($id));
    $consulta = $stmt3->fetch(PDO::FETCH_ASSOC);
    $pro = $consulta['pro'];

    if ($dia == 1) {
        if ($fin == 1) { ?>
<script type="text/javascript">
if (confirm('¿Desea imprimir el informe?')) {
    window.open("info_r.php?a=<?php print($pro); ?>&b=<?php print($dni); ?>&c=<?php print($p_dni); ?>", "_blank");
    window.parent.location.href = "lista_pro.php";
} else {
    window.parent.location.href = "lista_pro.php";
}
</script>
<?php } else { ?>
<script type="text/javascript">
window.parent.location.href = "lista_pro.php";
</script>
<?php }
    }
}

function lab_updateRepro($id, $p_cic, $p_fiv, $p_icsi, $p_cri, $pago_extras, $dia, $o_ovo, $pen, $end, $s_pun, $s_cum, $n_ins, $hra, $emb, $hra_c, $emb_c, $hra_a, $emb_a, $obs, $obs_med, $book, $hoja, $inc)
{
    $inc = !empty($data["inc"]) ? intval($data["inc"]) : 0;
    $hoja = !empty($data["hoja"]) ? intval($data["hoja"]) : 0;
    $book = !empty($data["book"]) ? intval($data["book"]) : 0;
    $emb_a = !empty($data["emb_a"]) ? intval($data["emb_a"]) : 0;
    $emb = !empty($data["emb0"]) ? $data["emb0"] : 0;
    $hra = !empty($data["hra0"]) ? $data["hra0"] : '';
    $n_ins = !empty($data["n_ins"]) ? intval($data["n_ins"]) : 0;
    $s_cum = !empty($data["s_cum"]) ? intval($data["s_cum"]) : 0;
    $s_pun = !empty($data["s_pun"]) ? intval($data["s_pun"]) : 0;
    $end = !empty($data["endx"]) ? intval($data["endx"]) : 0;
    $pen = !empty($data["pen"]) ? intval($data["pen"]) : 0;
    $pago_extras = !empty($data["p_extras"]) ? $data["p_extras"]: '';
    $p_cri = !empty($data["p_cri"]) ? intval($data["p_cri"]) : 0;
    $p_icsi = !empty($data["p_icsi"]) ? intval($data["p_icsi"]) : 0;
    $p_fiv = !empty($data["p_fiv"]) ? intval($data["p_fiv"]) : 0;
    $o_ovo = !empty($data["o_ovo"]) ? $data["o_ovo"] : '';
    $emb_c = !empty($emb_c) ? intval($emb_c) : 0;
    $fec0 = !empty($fec0) ? date('Y-m-d', strtotime($fec0)) : '1899-12-30';
    global $db;
    $stmt = $db->prepare("update hc_reprod SET p_cic=?,p_fiv=?,p_icsi=?,p_cri=?,pago_extras=?, updatex=? WHERE id=?");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute(array($p_cic, $p_fiv, $p_icsi, $p_cri, $pago_extras, $hora_actual, $id));

    $log_Reprod = $db->prepare(
        "INSERT INTO appinmater_log.hc_reprod (
                    reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                    p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                    p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                    tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                    con_oi, con_end,
                    con1_med, 
                    con2_med, 
                    con3_med, 
                    con4_med, 
                    con5_med, 
                    con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                    complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                    pago_extras, pago_notas, pago_obs, repro, 
                    idusercreate, createdate, action
            )
        SELECT 
            id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
            p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
            p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
            tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
            con_oi, con_end,
            con1_med, 
            con2_med, 
            con3_med, 
            con4_med, 
            con5_med,
            con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
            complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
            pago_extras, pago_notas, pago_obs, repro, 
            iduserupdate, updatex, 'U'
        FROM appinmater_modulo.hc_reprod
        WHERE id=?");
    $log_Reprod->execute([$id]);

    $stmt2 = $db->prepare("UPDATE lab_aspira SET o_ovo=?,pen=?,endx=?,s_pun=?,s_cum=?,n_ins=?,hra0=?,emb0=?,hra0c=?,emb0c=?,hra_a=?,emb_a=?,obs=?,obs_med=?,book=?,hoja=?,inc=? WHERE rep=? and estado is true");
    $stmt2->execute(array($o_ovo, $pen, $end, $s_pun, $s_cum, $n_ins, $hra, $emb, $hra_c, $emb_c, $hra_a, $emb_a, $obs, $obs_med, $book, $hoja, $inc, $id));
    if ($dia == 1) { ?>
<script type="text/javascript">
window.parent.location.href = "lista_pro.php";
</script><?php }
}

function lab_updateRepro2($data)
{
    $id = $data["rep"];
    $pago_notas = $data["p_notas"];
    $obs = $data["obs"];
    $obs_med = $data["obs_med"];
    $fin = $data["fin"];
    $path = $data["path"];
    $hoja = !empty($data["hoja"]) ? intval($data["hoja"]) : 0;
    $book = !empty($data["book"]) ? intval($data["book"]) : 0;
    $pago_extras = !empty($data["p_extras"]) ? $data["p_extras"] : '';
    $emb_c = !empty($data['emb0c']) ? intval($data['emb0c']) : 0;
    $fec0 = !empty($fec0) ? date('Y-m-d', strtotime($fec0)) : '1899-12-30';
    global $db;
    $stmt = $db->prepare("update hc_reprod SET pago_extras=?, pago_notas=?, iduserupdate=?, updatex=? WHERE id=?;");
    $hora_actual = date("Y-m-d H:i:s");
    $stmt->execute(array($pago_extras, $pago_notas, $data["iduserupdate"], $hora_actual, $id));

    $log_Reprod = $db->prepare(
        "INSERT INTO appinmater_log.hc_reprod (
                    reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                    p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                    p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                    tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                    con_oi, con_end,
                    con1_med, 
                    con2_med, 
                    con3_med, 
                    con4_med, 
                    con5_med, 
                    con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                    complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                    pago_extras, pago_notas, pago_obs, repro, 
                    idusercreate, createdate, action
            )
        SELECT 
            id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
            p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
            p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
            tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
            con_oi, con_end,
            con1_med, 
            con2_med, 
            con3_med, 
            con4_med, 
            con5_med,
            con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
            complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
            pago_extras, pago_notas, pago_obs, repro, 
            iduserupdate, updatex, 'U'
        FROM appinmater_modulo.hc_reprod
        WHERE id=?");
    $log_Reprod->execute([$id]);

    $stmt2 = $db->prepare("UPDATE lab_aspira SET obs=?, obs_med=?, book=?, hoja=? WHERE rep=? and estado is true");
    $stmt2->execute(array($obs, $obs_med, $book, $hoja, $id));

    $stmt3 = $db->prepare("SELECT pro FROM lab_aspira WHERE rep = ? and estado is true");
    $stmt3->execute(array($id));
    $consulta = $stmt3->fetch(PDO::FETCH_ASSOC);
    $pro = $consulta['pro'];

    $stmt = $db->prepare("SELECT dni, p_dni FROM hc_reprod WHERE estado = true and id = ?");
    $stmt->execute(array($id));
    $consulta1 = $stmt->fetch(PDO::FETCH_ASSOC);
    $dni = $consulta1['dni'];
    $p_dni = $consulta1['p_dni'];

    // buscar embriones que terminaron en crio y biopsia es laser o mecanica
    $stmt4 = $db->prepare("SELECT ovo FROM lab_aspira_dias WHERE ((d5f_cic = 'C' AND d5d_bio <> 0) OR (d6f_cic = 'C' AND d6d_bio <> 0)) AND pro = ?");
    $stmt4->execute(array($pro));

    // verificar datos
    if (strpos($pago_extras, 'NGS') !== false && $stmt4->rowCount() <> 0) { ?>
<script type="text/javascript">
window.parent.location.href = "info_igenomix_new.php?path=<?php print($path); ?>&pro=<?php print($pro); ?>";
</script>
<?php
    } else {
        if ($fin == 1) { ?>
<script type="text/javascript">
if (confirm('¿Desea imprimir el informe?')) {
    window.parent.location.href = "lista_pro.php";
    window.open("info_r.php?a=<?php print($pro); ?>&b=<?php print($dni); ?>&c=<?php print($p_dni); ?>", "_blank");
} else {
    window.parent.location.href = "lista_pro.php";
}
</script>
<?php
        } else { ?>
<script type="text/javascript">
window.parent.location.href = "lista_pro.php";
</script>
<?php
        }
    }
}
function lab_incubadora1($id, $inc1)
{
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira SET inc1=? WHERE rep=? and estado is true");
    $stmt->execute(array($inc1, $id));
}
function lab_insertAspi_ovos($pro, $ovo)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO lab_aspira_dias (pro,ovo) VALUES (?,?)");
    $stmt->execute(array($pro, $ovo));
}
function lab_updateAspi_d0($pro, $ovo, $anu, $est, $mor, $z_pel, $rot, $inte, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $img)
{
    if ($f_cic <> 'O') $otros_f_cic = ",d1f_cic='',d2f_cic='',d3f_cic='',d4f_cic='',d5f_cic='',d6f_cic=''"; else $otros_f_cic = "";
    $anu = ($anu !== '') ? $anu : 0;
    $f_cic = ($f_cic !== '') ? $f_cic : 0;
    $obs = ($obs !== '') ? $obs : 0;
    $T = ($T !== '') ? $T : 0;
    $C = ($C !== '') ? $C : 0;
    $G = ($G !== '') ? $G : 0;
    $P = ($P !== '') ? $P : 0;
    $col = ($col !== '') ? $col : 0;
    $don = ($don !== '') ? $don : 0;
    $inte = ($inte !== '') ? $inte : 0;
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira_dias SET anu=?,d0est=?,d0mor=?,d0z_pel=?,d0rot=?,d0inte=?,d0f_cic=?,obs=?,t=?,c=?,g=?,p=?,col=?,don=?" . $otros_f_cic . " WHERE pro=? AND ovo=? and estado is true");
    $stmt->execute(array($anu, $est, $mor, $z_pel, $rot, $inte, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $pro, $ovo));
    if ($img['name'] <> "") {
        if (is_uploaded_file($img['tmp_name'])) {
            $ruta = 'emb_pic/p' . $pro . 'd0_' . $ovo . '.jpg';
            move_uploaded_file($img['tmp_name'], $ruta);
        }
    }
}
function lab_updateAspi_d1($pro, $ovo, $anu, $est1, $c_pol, $pron, $t_pro, $d_nuc, $hal, $f_cic, $obs, $don, $img)
{
    if ($f_cic <> 'O') $otros_f_cic = ",d2f_cic='',d3f_cic='',d4f_cic='',d5f_cic='',d6f_cic=''"; else $otros_f_cic = "";
    $f_cic = ($f_cic !== '') ? $f_cic : 0;
    $obs = ($obs !== '') ? $obs : 0;
    $don = ($don !== '') ? $don : 0;
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira_dias SET anu=?,d1est=?,d1c_pol=?,d1pron=?,d1t_pro=?,d1d_nuc=?,d1hal=?,d1f_cic=?,obs=?,don=?" . $otros_f_cic . " WHERE pro=? AND ovo=? and estado is true");
    $stmt->execute(array($anu, $est1, $c_pol, $pron, $t_pro, $d_nuc, $hal, $f_cic, $obs, $don, $pro, $ovo));
    if ($img['name'] <> "") {
        if (is_uploaded_file($img['tmp_name'])) {
            $ruta = 'emb_pic/p' . $pro . 'd1_' . $ovo . '.jpg';
            move_uploaded_file($img['tmp_name'], $ruta);
        }
    }
}
function lab_updateAspi_d2($pro, $ovo, $anu, $cel, $fra, $sim, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $img)
{
    //if ($T=='') $T = null; if ($C=='') $C = null; if ($G=='') $G = null; if ($P=='') $P = null;
    if ($f_cic <> 'O') $otros_f_cic = ",d3f_cic='',d4f_cic='',d5f_cic='',d6f_cic=''"; else $otros_f_cic = "";
    $anu = ($anu !== '') ? $anu : 0;
    $cel = ($cel !== '') ? $cel : 0;
    $f_cic = ($f_cic !== '') ? $f_cic : 0;
    $obs = ($obs !== '') ? $obs : 0;
    $T = ($T !== '') ? $T : 0;
    $C = ($C !== '') ? $C : 0;
    $G = ($G !== '') ? $G : 0;
    $P = ($P !== '') ? $P : 0;
    $col = ($col !== '') ? $col : 0;
    $don = ($don !== '') ? $don : 0;
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira_dias SET anu=?,d2cel=?,d2fra=?,d2sim=?,d2f_cic=?,obs=?,t=?,c=?,g=?,p=?,col=?,don=?" . $otros_f_cic . " WHERE pro=? AND ovo=? and estado is true");
    $stmt->execute(array($anu, $cel, $fra, $sim, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $pro, $ovo));
    if ($img['name'] <> "") {
        if (is_uploaded_file($img['tmp_name'])) {
            $ruta = 'emb_pic/p' . $pro . 'd2_' . $ovo . '.jpg';
            move_uploaded_file($img['tmp_name'], $ruta);
        }
    }
}
function lab_updateAspi_d3($pro, $ovo, $anu, $cel, $fra, $sim, $c_bio, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $img)
{
    //if ($T=='') $T = null; if ($C=='') $C = null; if ($G=='') $G = null; if ($P=='') $P = null;
    if ($f_cic <> 'O') $otros_f_cic = ",d4f_cic='',d5f_cic='',d6f_cic=''"; else $otros_f_cic = "";
    $anu = ($anu !== '') ? $anu : 0;
    $cel = ($cel !== '') ? $cel : 0;
    $f_cic = ($f_cic !== '') ? $f_cic : 0;
    $obs = ($obs !== '') ? $obs : 0;
    $T = ($T !== '') ? $T : 0;
    $C = ($C !== '') ? $C : 0;
    $G = ($G !== '') ? $G : 0;
    $P = ($P !== '') ? $P : 0;
    $col = ($col !== '') ? $col : 0;
    $don = ($don !== '') ? $don : 0;
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira_dias SET anu=?,d3cel=?,d3fra=?,d3sim=?,d3c_bio=?,d3f_cic=?,obs=?,t=?,c=?,g=?,p=?,col=?,don=?" . $otros_f_cic . " WHERE pro=? AND ovo=? and estado is true");
    $stmt->execute(array($anu, $cel, $fra, $sim, $c_bio, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $pro, $ovo));
    if ($img['name'] <> "") {
        if (is_uploaded_file($img['tmp_name'])) {
            $ruta = 'emb_pic/p' . $pro . 'd3_' . $ovo . '.jpg';
            move_uploaded_file($img['tmp_name'], $ruta);
        }
    }
}
function lab_updateAspi_d4($pro, $ovo, $anu, $cel, $fra, $sim, $mci, $tro, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $img)
{
    //if ($T=='') $T = null; if ($C=='') $C = null; if ($G=='') $G = null; if ($P=='') $P = null;
    if ($f_cic <> 'O') $otros_f_cic = ",d5f_cic='',d6f_cic=''"; else $otros_f_cic = "";
    $anu = ($anu !== '') ? $anu : 0;
    $cel = ($cel !== '') ? $cel : 0;
    $mci = ($mci !== '') ? $mci : 0;
    $tro = ($tro !== '') ? $tro : 0;
    $fra = ($fra !== '') ? $fra : 0;
    $f_cic = ($f_cic !== '') ? $f_cic : 0;
    $obs = ($obs !== '') ? $obs : 0;
    $T = ($T !== '') ? $T : 0;
    $C = ($C !== '') ? $C : 0;
    $G = ($G !== '') ? $G : 0;
    $P = ($P !== '') ? $P : 0;
    $col = ($col !== '') ? $col : 0;
    $don = ($don !== '') ? $don : 0;
    $sim = !empty($sim) ? intval($sim) : 0;
    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira_dias SET anu=?,d4cel=?,d4fra=?,d4sim=?,d4mci=?,d4tro=?,d4f_cic=?,obs=?,t=?,c=?,g=?,p=?,col=?,don=?" . $otros_f_cic . " WHERE pro=? AND ovo=? and estado is true");
    $stmt->execute(array($anu, $cel, $fra, $sim, $mci, $tro, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $pro, $ovo));
    if ($img['name'] <> "") {
        if (is_uploaded_file($img['tmp_name'])) {
            $ruta = 'emb_pic/p' . $pro . 'd4_' . $ovo . '.jpg';
            move_uploaded_file($img['tmp_name'], $ruta);
        }
    }
}
function lab_updateAspi_d5($pro, $ovo, $anu, $cel, $mci, $tro, $fra, $vac, $colap, $d_bio, $kid, $kid_tipo, $kid_decimal, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $img)
{
    if ($f_cic <> 'O') {
        $otros_f_cic = ",d6f_cic=''";
    } else {
        $otros_f_cic = "";
    }
    $anu = ($anu !== '') ? $anu : 0;
    $cel = ($cel !== '') ? $cel : 0;
    $mci = ($mci !== '') ? $mci : 0;
    $tro = ($tro !== '') ? $tro : 0;
    $fra = ($fra !== '') ? $fra : 0;
    $vac = ($vac !== '') ? $vac : 0;
    $colap = ($colap !== '') ? $colap : 0;
    $d_bio = ($d_bio !== '') ? $d_bio : 0;
    $kid = ($kid !== '') ? $kid : 0;
    $kid_tipo = ($kid_tipo !== '') ? $kid_tipo : 0;
    $kid_decimal = ($kid_decimal !== '') ? $kid_decimal : 0;
    $f_cic = ($f_cic !== '') ? $f_cic : 0;
    $obs = ($obs !== '') ? $obs : 0;
    $T = ($T !== '') ? $T : 0;
    $C = ($C !== '') ? $C : 0;
    $G = ($G !== '') ? $G : 0;
    $P = ($P !== '') ? $P : 0;
    $col = ($col !== '') ? $col : 0;
    $don = ($don !== '') ? $don : 0;

    global $db;
    $stmt = $db->prepare("UPDATE lab_aspira_dias
    SET anu=?, d5cel=?, d5mci=?, d5tro=?, d5fra=?, d5vac=?, d5col=?, d5d_bio=?, d5kid=?, d5kid_tipo=?, d5kid_decimal=?, d5f_cic=?, obs=?, t=?, c=?, g=?, p=?, col=?, don=?" . $otros_f_cic . "
    WHERE pro=? AND ovo=? and estado is true");
    $stmt->execute(array($anu, $cel, $mci, $tro, $fra, $vac, $colap, $d_bio, $kid, $kid_tipo, $kid_decimal, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $pro, $ovo));

    if ($img['name'] <> "") {
        if (is_uploaded_file($img['tmp_name'])) {
            $ruta = 'emb_pic/p' . $pro . 'd5_' . $ovo . '.jpg';
            move_uploaded_file($img['tmp_name'], $ruta);
        }
    }
}
function lab_updateAspi_d6($pro, $ovo, $anu, $cel, $mci, $tro, $fra, $vac, $colap, $d_bio, $kid, $kid_tipo, $kid_decimal, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $img)
{
    global $db;
    $anu = ($anu !== '') ? $anu : 0;
    $cel = ($cel !== '') ? $cel : 0;
    $mci = ($mci !== '') ? $mci : 0;
    $tro = ($tro !== '') ? $tro : 0;
    $fra = ($fra !== '') ? $fra : 0;
    $vac = ($vac !== '') ? $vac : 0;
    $colap = ($colap !== '') ? $colap : 0;
    $d_bio = ($d_bio !== '') ? $d_bio : 0;
    $kid = ($kid !== '') ? $kid : 0;
    $kid_tipo = ($kid_tipo !== '') ? $kid_tipo : 0;
    $kid_decimal = ($kid_decimal !== '') ? $kid_decimal : 0;
    $f_cic = ($f_cic !== '') ? $f_cic : 0;
    $obs = ($obs !== '') ? $obs : 0;
    $T = ($T !== '') ? $T : 0;
    $C = ($C !== '') ? $C : 0;
    $G = ($G !== '') ? $G : 0;
    $P = ($P !== '') ? $P : 0;
    $col = ($col !== '') ? $col : 0;
    $don = ($don !== '') ? $don : 0;
    $stmt = $db->prepare("UPDATE lab_aspira_dias
    SET anu=?, d6cel=?, d6mci=?, d6tro=?, d6fra=?, d6vac=?, d6col=?, d6d_bio=?, d6kid=?, d6kid_tipo=?, d6kid_decimal=?, d6f_cic=?, obs=?, t=?, c=?, g=?, p=?, col=?, don=?
    WHERE pro=? AND ovo=? and estado is true");
    $stmt->execute(array($anu, $cel, $mci, $tro, $fra, $vac, $colap, $d_bio, $kid, $kid_tipo, $kid_decimal, $f_cic, $obs, $T, $C, $G, $P, $col, $don, $pro, $ovo));

    if ($img['name'] <> "") {
        if (is_uploaded_file($img['tmp_name'])) {
            $ruta = 'emb_pic/p' . $pro . 'd6_' . $ovo . '.jpg';
            move_uploaded_file($img['tmp_name'], $ruta);
        }
    }
}
function lab_insertTanque($n_tan, $n_c, $n_v, $n_p, $tip, $sta)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO lab_tanque (n_tan,n_c,n_v,n_p,tip,sta) VALUES (?,?,?,?,?,?)");
    $stmt->execute(array($n_tan, $n_c, $n_v, $n_p, $tip, $sta));
    $t = $db->lastInsertId();
    for ($c = 1; $c <= $n_c; $c++) {
        for ($v = 1; $v <= $n_v; $v++) {
            for ($p = 1; $p <= $n_p; $p++) {
                $stmt2 = $db->prepare("INSERT INTO lab_tanque_res (t,c,v,p) VALUES (?,?,?,?)");
                $stmt2->execute(array($t, $c, $v, $p));
            }
        }
    }
}
function updateEmbrio($id, $nom, $mai, $cbp, $cel, $img)
{
    global $db;
    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO lab_user (nom,mai,cbp,cel) VALUES (?,?,?,?)");
        $stmt->execute(array($nom, $mai, $cbp, $cel));
        $id = $db->lastInsertId();
    } else {
        $stmt = $db->prepare("UPDATE lab_user SET nom=?,mai=?,cbp=?,cel=? WHERE id=?");
        $stmt->execute(array($nom, $mai, $cbp, $cel, $id));
    }
    if ($img['name'] <> "") {
        if (is_uploaded_file($img['tmp_name'])) {
            $ruta = 'emb_pic/emb_' . $id . '.jpg';
            move_uploaded_file($img['tmp_name'], $ruta);
        }
    }
    ?>
<script type="text/javascript">
window.parent.location.href = "lista_emb.php";
</script>
<?php
}

function lab_insertEmbry($pro,$vid,$pdf) {
    if ($vid['name'] <> "") {
        if (is_uploaded_file($vid['tmp_name'])) {
            $ruta = 'emb_pic/embryoscope_' . $pro . '.mp4';
            move_uploaded_file($vid['tmp_name'], $ruta);
        }
    }
    if ($pdf['name'] <> "") {
        if (is_uploaded_file($pdf['tmp_name'])) {
            $ruta = 'emb_pic/embryoscope_' . $pro . '.pdf';
            move_uploaded_file($pdf['tmp_name'], $ruta);
        }
    }
}

function embryoscope_video($vid, $repro_id = 0, $procedimiento = '', $login = 'sistemas') {
    /*try {
        require_once "google-api-php-client/src/Google/autoload.php";
        require_once "google-api-php-client/src/Google/Client.php";
        require $_SERVER["DOCUMENT_ROOT"] . '/config/environment.php';

        $accountname = $_ENV["googlecalendar_accountname"];
        $keyfilelocation = $_ENV["googlecalendar_keyfilelocation"];
        $applicationname = $_ENV["googlecalendar_applicationname"];

        if (!strlen($accountname) || !strlen($keyfilelocation)) {
            echo missingServiceAccountDetailsWarning();
        }
        $config = new Google_Config();
        $config->setClassConfig('Google_Cache_File', array('directory' => '/home/appinmater/public_html/google-api-php-client/src/Google/Cache'));
        $client = new Google_Client($config);
        $client->setApplicationName($applicationname);
        if (isset($_SESSION['service_token'])) {
            $client->setAccessToken($_SESSION['service_token']);
        }
        $cred = new Google_Auth_AssertionCredentials($accountname, array('https://www.googleapis.com/auth/drive'), file_get_contents($keyfilelocation));
        $client->setAssertionCredentials($cred);
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }
        $_SESSION['service_token'] = $client->getAccessToken();
    
        $file_path = '';
        $video_original = $vid['name'];
        $video_base = date("YmdHis") . "-" . preg_replace("/[^a-zA-Z0-9.]/", "", $vid['name']);

        $service = new Google_Service_Drive($client);
        $file = new Google_Service_Drive_DriveFile();
        $file->setName($video_base);
        $file->setParents([$_ENV["google_drive_embryoscope_path"]]);
        $file->setDescription($_ENV["google_drive_embryoscope_description"]);
        $file->setMimeType("video/mp4");

        $result = $service->files->create(
            $file, [
              'data' => file_get_contents('emb_pic/embryoscope_' . $procedimiento . '.mp4'),
              'mimeType' => "video/mp4",
              'uploadType' => 'multipart'
            ]
        );

        // registro de reponse
        global $db;
        $stmt = $db->prepare("INSERT into google_drive_response (tipo_procedimiento_id, procedimiento_id, drive_id, nombre_base, nombre_original, idusercreate)
            values (?, ?, ?, ?, ?, ?)");
        $stmt->execute([1, $repro_id, $result->id, $video_base, $video_original, $login]);
        /* print('<pre>'); print_r($vid); print('</pre>');
        print('<pre>'); print_r($result); print('</pre>'); exit(); */
    //} catch (Exception $e) {
        /* $m = json_decode($gs->getMessage()); */
        /* print($e->getMessage()); exit(); */
       /* global $db;
        $stmt = $db->prepare("INSERT into google_drive_response (tipo_procedimiento_id, procedimiento_id, drive_id, nombre_base, nombre_original, error, idusercreate)
            values (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([1, $repro_id, 0, date("YmdHis") . "-". preg_replace("/[^a-zA-Z0-9.]/", "", $vid['name']), $vid['name'], $e->getMessage(), $login]);
    }*/
}

function upload_video(
    $archivo,
    $tipo_procedimiento = 0,
    $procedimiento = 0,
    $archivo_path = '',
    $accountname = '',
    $keyfilelocation = '',
    $applicationname = '',
    $google_drive_path = '',
    $google_drive_description = '',
    $login = 'sistemas'
    ) {/*
    try {
        require_once "google-api-php-client/src/Google/autoload.php";
        require_once "google-api-php-client/src/Google/Client.php";

        if (!strlen($accountname) || !strlen($keyfilelocation)) {
            echo missingServiceAccountDetailsWarning();
        }
        $config = new Google_Config();
        $config->setClassConfig('Google_Cache_File', array('directory' => '/home/appinmater/public_html/google-api-php-client/src/Google/Cache'));
        $client = new Google_Client($config);
        $client->setApplicationName($applicationname);
        if (isset($_SESSION['service_token'])) {
            $client->setAccessToken($_SESSION['service_token']);
        }
        $cred = new Google_Auth_AssertionCredentials($accountname, array('https://www.googleapis.com/auth/drive'), file_get_contents($keyfilelocation));
        $client->setAssertionCredentials($cred);
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }
        $_SESSION['service_token'] = $client->getAccessToken();
    
        $archivo_original = $archivo['name'];
        $archivo_base = date("YmdHis") . "-" . preg_replace("/[^a-zA-Z0-9.]/", "", $archivo['name']);

        $service = new Google_Service_Drive($client);
        $file = new Google_Service_Drive_DriveFile();
        $file->setName($archivo_base);
        $file->setParents([$google_drive_path]);
        $file->setDescription($google_drive_description);
        $file->setMimeType("video/mp4");

        $resultado = $service->files->create(
            $file, [
              'data' => file_get_contents($archivo_path),
              'mimeType' => "video/mp4",
              'uploadType' => 'multipart'
            ]
        );

        // registro de reponse
        global $db;
        $stmt = $db->prepare("INSERT into google_drive_response (tipo_procedimiento_id, procedimiento_id, drive_id, nombre_base, nombre_original, idusercreate)
            values (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tipo_procedimiento, $procedimiento, $resultado->id, $archivo_base, $archivo_original, $login]);
    } catch (Exception $e) {
        global $db;
        $stmt = $db->prepare("INSERT into google_drive_response (tipo_procedimiento_id, procedimiento_id, drive_id, nombre_base, nombre_original, error, idusercreate)
            values (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tipo_procedimiento, $procedimiento, 0, $archivo['name'], $archivo['name'], $e->getMessage(), $login]);
    }
    */
}

function Recibo_serv($nom, $pak, $costo, $cc, $tip, $cod)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO recibo_serv (nom,pak,costo,cc,tip,cod) VALUES (?,?,?,?,?,?)");
    $stmt->execute(array($nom, $pak, $costo, $cc, $tip, $cod));
    echo "<div id='alerta'> Servicio guardado! </div>";
}

function recibo_serv_01($conta_sub_centro_costo_id, $nombreservicio, $codigo, $moneda, $costo, $paquete, $centrocosto, $cuentacontable, $tiposervicio)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO recibo_serv (conta_sub_centro_costo_id, nom, cod, idmoneda, costo, pak, cc, cuenta_contable, tip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(array($conta_sub_centro_costo_id, $nombreservicio, $codigo, $moneda, $costo, $paquete, $centrocosto, $cuentacontable, $tiposervicio));
    print("<div id='alerta'>Servicio guardado!</div>");
    print("<script type='text/javascript'>window.parent.location.href = 'man_ser.php?tiposervicio=$tiposervicio';</script>");
}
function update_servicio($id, $conta_sub_centro_costo_id, $nombreservicio, $codigo, $moneda, $costo, $paquete, $centrocosto, $cuentacontable, $tiposervicio)
{
    global $db;
    $stmt = $db->prepare("UPDATE recibo_serv set conta_sub_centro_costo_id=?, cod=?, nom=?, pak=?, idmoneda=?, costo=?, cc=?, cuenta_contable=?, tip=? where id=?");
    $stmt->execute(array($conta_sub_centro_costo_id, $codigo, $nombreservicio, $paquete, $moneda, $costo, $centrocosto, $cuentacontable, $tiposervicio, $id));
    print("<script type='text/javascript'>window.parent.location.href = 'man_ser_edit.php?id='".$id.";</script>");
}
function Control($nom, $cat, $pres, $lote, $f_ven, $f_ing, $f_uso, $sob1, $sob2, $sob3, $color)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO lab_control (nom,cat,pres,lote,f_ven,f_ing,f_uso,sob1,sob2,sob3,color) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute(array($nom, $cat, $pres, $lote, $f_ven, $f_ing, $f_uso, $sob1, $sob2, $sob3, $color));
    echo "<div id='alerta'> Insumo Guardado! </div>";
}

function google_cal(
    $title,
    $description,
    $date_start,
    $date_end,
    $id = 'inmater.pe_kubl15o1cur6od1up7ngg2v260@group.calendar.google.com',
    $accountname = 'inmater-app@inmater-app-258321.iam.gserviceaccount.com',
    $keyfilelocation = 'inmater-app-258321-e8fd4780161b.p12',
    $applicationname = 'inmater-app',
    $adicionales = [],
    $codigo_color = 0) {
        return (object)["id"=> 0, "htmlLink"=>""];
        /*
    session_start();
    require_once "google-api-php-client/src/Google/autoload.php";
    require_once "google-api-php-client/src/Google/Client.php";
    require_once "google-api-php-client/src/Google/Service/Calendar.php";
    if (!strlen($accountname) || !strlen($keyfilelocation)) {
      echo missingServiceAccountDetailsWarning();
    }
    $client = new Google_Client();
    $client->setApplicationName($applicationname);
    if (isset($_SESSION['service_token'])) {
      $client->setAccessToken($_SESSION['service_token']);
    }
    $cred = new Google_Auth_AssertionCredentials($accountname, array('https://www.googleapis.com/auth/calendar'), file_get_contents($keyfilelocation));
    $client->setAssertionCredentials($cred);
    if ($client->getAuth()->isAccessTokenExpired()) {
      $client->getAuth()->refreshTokenWithAssertion($cred);
    }
    $_SESSION['service_token'] = $client->getAccessToken();
    $service = new Google_Service_Calendar($client);
    $event = new Google_Service_Calendar_Event();
    $event->setSummary($title);
    $event->setDescription($description);
    $start = new Google_Service_Calendar_EventDateTime();
    $start->setDateTime($date_start);
    $event->setStart($start);
    $end = new Google_Service_Calendar_EventDateTime();
    $end->setDateTime($date_end);
    $event->setEnd($end);
    // agregar copia de procedimientos
    foreach ($adicionales as $item) {
        $attendee = new Google_Service_Calendar_EventAttendee();
        $attendee->setEmail($item);
        $attendees[] = $attendee;
    }
    $event->attendees = $attendees;
    if ($codigo_color != 0) {
        $event->setColorId($codigo_color);
    }
    $createdEvent = $service->events->insert($id, $event);
    return $createdEvent; */
}

function googlecalendar_actualizar($data) {
    /*
  session_start();
  require_once "google-api-php-client/src/Google/autoload.php";
  require_once "google-api-php-client/src/Google/Client.php";
  require_once "google-api-php-client/src/Google/Service/Calendar.php";
  if (!strlen($data["accountname"]) || !strlen($data["keyfilelocation"])) {
    echo missingServiceAccountDetailsWarning();
  }
  $client = new Google_Client();
  $client->setApplicationName($data["applicationname"]);
  if (isset($_SESSION['service_token'])) {
    $client->setAccessToken($_SESSION['service_token']);
  }
  $cred = new Google_Auth_AssertionCredentials(
    $data["accountname"],
    array('https://www.googleapis.com/auth/calendar'),
    file_get_contents($data["keyfilelocation"])
  );
  $client->setAssertionCredentials($cred);
  if ($client->getAuth()->isAccessTokenExpired()) {
    $client->getAuth()->refreshTokenWithAssertion($cred);
  }
  $_SESSION['service_token'] = $client->getAccessToken();
  $service = new Google_Service_Calendar($client);
  $event = $service->events->get($data["id"], $data["googlecalendar_codigo"]);
  $date_start = new Google_Service_Calendar_EventDateTime();
  $date_start->setDateTime($data["googlecalendar_date_start"]);
  $event->setStart($date_start);
  $date_end = new Google_Service_Calendar_EventDateTime();
  $date_end->setDateTime($data["googlecalendar_date_end"]);
  $event->setEnd($date_end);
  $event->setDescription($data["description"]);
  $event->setColorId(5);
  $updatedEvent = $service->events->update($data["id"], $event->getId(), $event); */
}

function googlecalendar_eliminar($data)
{ /*
  require_once "google-api-php-client/src/Google/autoload.php";
  require_once "google-api-php-client/src/Google/Client.php";
  require_once "google-api-php-client/src/Google/Service/Calendar.php";

  if (!strlen($data["accountname"]) || !strlen($data["keyfilelocation"])) {
    echo missingServiceAccountDetailsWarning();
  }

  $client = new Google_Client();
  $client->setApplicationName($data["applicationname"]);

  if (isset($_SESSION['service_token'])) {
    $client->setAccessToken($_SESSION['service_token']);
  }

  $cred = new Google_Auth_AssertionCredentials(
    $data["accountname"],
    array('https://www.googleapis.com/auth/calendar'),
    file_get_contents($data["keyfilelocation"])
  );

  $client->setAssertionCredentials($cred);

  if ($client->getAuth()->isAccessTokenExpired()) {
    $client->getAuth()->refreshTokenWithAssertion($cred);
  }

  $_SESSION['service_token'] = $client->getAccessToken();
  $service = new Google_Service_Calendar($client);

  $service->events->delete($data["id"], $data["googlecalendar_codigo"]);
  */
}

function endCycle($d1, $days)
{
    return date('Y-m-d', strtotime($d1 . ' + ' . $days . ' days'));
}
function rm_folder_recursively($dir)
{
    foreach (scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) rm_folder_recursively("$dir/$file");
        else unlink("$dir/$file");
    }
    rmdir($dir);
    return true;
}
function delete_hc_paciente($tel, $confe)
{
    if ($confe <> "") {
        $dir = $tel . '/' . $confe;
        rm_folder_recursively($dir);
        mysql_query("DELETE FROM hc_paciente WHERE userx='$tel' and Confer= '$confe'");
    }
}
function getExtension($chaine)
{
    $taille = strlen($chaine) - 1;
    for ($i = $taille; $i >= 0; $i--)
        if ($chaine['$i'] == '.') break;
    return substr($chaine, $i + 1, strlen($chaine) - ($i + 1));
}
function redimensionar_jpeg($nom_fichier, $destino_temporal, $destino_temporal_anchura, $destino_temporal_altura, $destino_temporal_calidad)
{
#
// crear una imagen desde el original
#
    $img = imagecreatefromjpeg($nom_fichier);
    if (imagesx($img) > 100 or imagesy($img) > 100) {
#
// crear una imagen nueva
#
        $thumb = imagecreatetruecolor($destino_temporal_anchura, $destino_temporal_altura);
#
// redimensiona la imagen original copiandola en la imagen
#
        imagecopyresized($thumb, $img, 0, 0, 0, 0, $destino_temporal_anchura, $destino_temporal_altura, imagesx($img), imagesy($img));
#
// guardar la nueva imagen redimensionada donde indicia $destino_temporal
#
        imagejpeg($thumb, $destino_temporal, $destino_temporal_calidad);
    } else {
        imagejpeg($img, $destino_temporal, $destino_temporal_calidad);
    }
    imagedestroy($img);
}

function getSero($dni, $fec, $tipopaciente = 1)
{
    global $db;

    $query = "SELECT 
            CASE
            WHEN s1.hbs > 0 THEN s1.hbs
            WHEN s2.hbs > 0 THEN s2.hbs
            WHEN s3.hbs > 0 THEN s3.hbs
            WHEN s4.hbs > 0 THEN s4.hbs
            WHEN s5.hbs > 0 THEN s5.hbs
            WHEN s6.hbs > 0 THEN s6.hbs
            WHEN s7.hbs > 0 THEN s7.hbs
            WHEN s8.hbs > 0 THEN s8.hbs
            WHEN s9.hbs > 0 THEN s9.hbs
            ELSE 0
        END as hbs,
        (
            SELECT CASE
                WHEN s1.hbs > 0 THEN s1.fec
                WHEN s2.hbs > 0 THEN s2.fec
                WHEN s3.hbs > 0 THEN s3.fec
                WHEN s4.hbs > 0 THEN s4.fec
                WHEN s5.hbs > 0 THEN s5.fec
                WHEN s6.hbs > 0 THEN s6.fec
                WHEN s7.hbs > 0 THEN s7.fec
                WHEN s8.hbs > 0 THEN s8.fec
                WHEN s9.hbs > 0 THEN s9.fec
            END
        ) as hbsfec,
        CASE
            WHEN (
                SELECT CASE
                    WHEN s1.hbs > 0 THEN s1.fec
                    WHEN s2.hbs > 0 THEN s2.fec
                    WHEN s3.hbs > 0 THEN s3.fec
                    WHEN s4.hbs > 0 THEN s4.fec
                    WHEN s5.hbs > 0 THEN s5.fec
                    WHEN s6.hbs > 0 THEN s6.fec
                    WHEN s7.hbs > 0 THEN s7.fec
                    WHEN s8.hbs > 0 THEN s8.fec
                    WHEN s9.hbs > 0 THEN s9.fec
                END
            ) IS NULL OR
            (
                SELECT DATE(
                    CASE
                        WHEN s1.hbs > 0 THEN s1.fec
                        WHEN s2.hbs > 0 THEN s2.fec
                        WHEN s3.hbs > 0 THEN s3.fec
                        WHEN s4.hbs > 0 THEN s4.fec
                        WHEN s5.hbs > 0 THEN s5.fec
                        WHEN s6.hbs > 0 THEN s6.fec
                        WHEN s7.hbs > 0 THEN s7.fec
                        WHEN s8.hbs > 0 THEN s8.fec
                        WHEN s9.hbs > 0 THEN s9.fec
                    END
                ) >= DATE('$fec') - INTERVAL '102 DAYS'
            ) THEN false
            ELSE true
        END as hbsvencido,
        CASE
            WHEN s1.hcv > 0 THEN s1.hcv
            WHEN s2.hcv > 0 THEN s2.hcv
            WHEN s3.hcv > 0 THEN s3.hcv
            WHEN s4.hcv > 0 THEN s4.hcv
            WHEN s5.hcv > 0 THEN s5.hcv
            WHEN s6.hcv > 0 THEN s6.hcv
            WHEN s7.hcv > 0 THEN s7.hcv
            WHEN s8.hcv > 0 THEN s8.hcv
            WHEN s9.hcv > 0 THEN s9.hcv
            ELSE 0
        END as hcv,
        (
            SELECT CASE
                WHEN s1.hcv > 0 THEN s1.fec
                WHEN s2.hcv > 0 THEN s2.fec
                WHEN s3.hcv > 0 THEN s3.fec
                WHEN s4.hcv > 0 THEN s4.fec
                WHEN s5.hcv > 0 THEN s5.fec
                WHEN s6.hcv > 0 THEN s6.fec
                WHEN s7.hcv > 0 THEN s7.fec
                WHEN s8.hcv > 0 THEN s8.fec
                WHEN s9.hcv > 0 THEN s9.fec
            END
        ) as hcvfec,
        CASE
            WHEN (
                SELECT CASE
                    WHEN s1.hcv > 0 THEN s1.fec
                    WHEN s2.hcv > 0 THEN s2.fec
                    WHEN s3.hcv > 0 THEN s3.fec
                    WHEN s4.hcv > 0 THEN s4.fec
                    WHEN s5.hcv > 0 THEN s5.fec
                    WHEN s6.hcv > 0 THEN s6.fec
                    WHEN s7.hcv > 0 THEN s7.fec
                    WHEN s8.hcv > 0 THEN s8.fec
                    WHEN s9.hcv > 0 THEN s9.fec
                END
            ) IS NULL OR
            (
                SELECT DATE(
                    CASE
                        WHEN s1.hcv > 0 THEN s1.fec
                        WHEN s2.hcv > 0 THEN s2.fec
                        WHEN s3.hcv > 0 THEN s3.fec
                        WHEN s4.hcv > 0 THEN s4.fec
                        WHEN s5.hcv > 0 THEN s5.fec
                        WHEN s6.hcv > 0 THEN s6.fec
                        WHEN s7.hcv > 0 THEN s7.fec
                        WHEN s8.hcv > 0 THEN s8.fec
                        WHEN s9.hcv > 0 THEN s9.fec
                    END
                ) >= DATE('$fec') - INTERVAL '102 DAYS'
            ) THEN false
            ELSE true
        END as hcvvencido,
        CASE
            WHEN s1.hiv > 0 THEN s1.hiv
            WHEN s2.hiv > 0 THEN s2.hiv
            WHEN s3.hiv > 0 THEN s3.hiv
            WHEN s4.hiv > 0 THEN s4.hiv
            WHEN s5.hiv > 0 THEN s5.hiv
            WHEN s6.hiv > 0 THEN s6.hiv
            WHEN s7.hiv > 0 THEN s7.hiv
            WHEN s8.hiv > 0 THEN s8.hiv
            WHEN s9.hiv > 0 THEN s9.hiv
            ELSE 0
        END as hiv,
        (
            SELECT CASE
                WHEN s1.hiv > 0 THEN s1.fec
                WHEN s2.hiv > 0 THEN s2.fec
                WHEN s3.hiv > 0 THEN s3.fec
                WHEN s4.hiv > 0 THEN s4.fec
                WHEN s5.hiv > 0 THEN s5.fec
                WHEN s6.hiv > 0 THEN s6.fec
                WHEN s7.hiv > 0 THEN s7.fec
                WHEN s8.hiv > 0 THEN s8.fec
                WHEN s9.hiv > 0 THEN s9.fec
            END
        ) as hivfec,
        CASE
            WHEN (
                SELECT CASE
                    WHEN s1.hiv > 0 THEN s1.fec
                    WHEN s2.hiv > 0 THEN s2.fec
                    WHEN s3.hiv > 0 THEN s3.fec
                    WHEN s4.hiv > 0 THEN s4.fec
                    WHEN s5.hiv > 0 THEN s5.fec
                    WHEN s6.hiv > 0 THEN s6.fec
                    WHEN s7.hiv > 0 THEN s7.fec
                    WHEN s8.hiv > 0 THEN s8.fec
                    WHEN s9.hiv > 0 THEN s9.fec
                END
            ) IS NULL OR
            (
                SELECT DATE(
                    CASE
                        WHEN s1.hiv > 0 THEN s1.fec
                        WHEN s2.hiv > 0 THEN s2.fec
                        WHEN s3.hiv > 0 THEN s3.fec
                        WHEN s4.hiv > 0 THEN s4.fec
                        WHEN s5.hiv > 0 THEN s5.fec
                        WHEN s6.hiv > 0 THEN s6.fec
                        WHEN s7.hiv > 0 THEN s7.fec
                        WHEN s8.hiv > 0 THEN s8.fec
                        WHEN s9.hiv > 0 THEN s9.fec
                    END
                ) >= DATE('$fec') - INTERVAL '102 DAYS'
            ) THEN false
            ELSE true
        END as hivvencido,
        CASE
            WHEN s1.rpr > 0 THEN s1.rpr
            WHEN s2.rpr > 0 THEN s2.rpr
            WHEN s3.rpr > 0 THEN s3.rpr
            WHEN s4.rpr > 0 THEN s4.rpr
            WHEN s5.rpr > 0 THEN s5.rpr
            WHEN s6.rpr > 0 THEN s6.rpr
            WHEN s7.rpr > 0 THEN s7.rpr
            WHEN s8.rpr > 0 THEN s8.rpr
            WHEN s9.rpr > 0 THEN s9.rpr
            ELSE 0
        END as rpr,
        (
            SELECT CASE
                WHEN s1.rpr > 0 THEN s1.fec
                WHEN s2.rpr > 0 THEN s2.fec
                WHEN s3.rpr > 0 THEN s3.fec
                WHEN s4.rpr > 0 THEN s4.fec
                WHEN s5.rpr > 0 THEN s5.fec
                WHEN s6.rpr > 0 THEN s6.fec
                WHEN s7.rpr > 0 THEN s7.fec
                WHEN s8.rpr > 0 THEN s8.fec
                WHEN s9.rpr > 0 THEN s9.fec
            END
        ) as rprfec,
        CASE
            WHEN (
                SELECT CASE
                    WHEN s1.rpr > 0 THEN s1.fec
                    WHEN s2.rpr > 0 THEN s2.fec
                    WHEN s3.rpr > 0 THEN s3.fec
                    WHEN s4.rpr > 0 THEN s4.fec
                    WHEN s5.rpr > 0 THEN s5.fec
                    WHEN s6.rpr > 0 THEN s6.fec
                    WHEN s7.rpr > 0 THEN s7.fec
                    WHEN s8.rpr > 0 THEN s8.fec
                    WHEN s9.rpr > 0 THEN s9.fec
                END
            ) IS NULL OR
            (
                SELECT DATE(
                    CASE
                        WHEN s1.rpr > 0 THEN s1.fec
                        WHEN s2.rpr > 0 THEN s2.fec
                        WHEN s3.rpr > 0 THEN s3.fec
                        WHEN s4.rpr > 0 THEN s4.fec
                        WHEN s5.rpr > 0 THEN s5.fec
                        WHEN s6.rpr > 0 THEN s6.fec
                        WHEN s7.rpr > 0 THEN s7.fec
                        WHEN s8.rpr > 0 THEN s8.fec
                        WHEN s9.rpr > 0 THEN s9.fec
                    END
                ) >= DATE('$fec') - INTERVAL '102 DAYS'
            ) THEN false
            ELSE true
        END as rprvencido,
        CASE
            WHEN s1.rub > 0 THEN s1.rub
            WHEN s2.rub > 0 THEN s2.rub
            WHEN s3.rub > 0 THEN s3.rub
            WHEN s4.rub > 0 THEN s4.rub
            WHEN s5.rub > 0 THEN s5.rub
            WHEN s6.rub > 0 THEN s6.rub
            WHEN s7.rub > 0 THEN s7.rub
            WHEN s8.rub > 0 THEN s8.rub
            WHEN s9.rub > 0 THEN s9.rub
            ELSE 0
        END as rub,
        (
            SELECT CASE
                WHEN s1.rub > 0 THEN s1.fec
                WHEN s2.rub > 0 THEN s2.fec
                WHEN s3.rub > 0 THEN s3.fec
                WHEN s4.rub > 0 THEN s4.fec
                WHEN s5.rub > 0 THEN s5.fec
                WHEN s6.rub > 0 THEN s6.fec
                WHEN s7.rub > 0 THEN s7.fec
                WHEN s8.rub > 0 THEN s8.fec
                WHEN s9.rub > 0 THEN s9.fec
            END
        ) as rubfec,
        CASE
            WHEN (
                SELECT CASE
                    WHEN s1.rub > 0 THEN s1.fec
                    WHEN s2.rub > 0 THEN s2.fec
                    WHEN s3.rub > 0 THEN s3.fec
                    WHEN s4.rub > 0 THEN s4.fec
                    WHEN s5.rub > 0 THEN s5.fec
                    WHEN s6.rub > 0 THEN s6.fec
                    WHEN s7.rub > 0 THEN s7.fec
                    WHEN s8.rub > 0 THEN s8.fec
                    WHEN s9.rub > 0 THEN s9.fec
                END
            ) IS NULL OR
            (
                SELECT DATE(
                    CASE
                        WHEN s1.rub > 0 THEN s1.fec
                        WHEN s2.rub > 0 THEN s2.fec
                        WHEN s3.rub > 0 THEN s3.fec
                        WHEN s4.rub > 0 THEN s4.fec
                        WHEN s5.rub > 0 THEN s5.fec
                        WHEN s6.rub > 0 THEN s6.fec
                        WHEN s7.rub > 0 THEN s7.fec
                        WHEN s8.rub > 0 THEN s8.fec
                        WHEN s9.rub > 0 THEN s9.fec
                    END
                ) >= DATE('$fec') - INTERVAL '102 DAYS'
            ) THEN false
            ELSE true
        END as rubvencido,
        CASE
            WHEN s1.tox > 0 THEN s1.tox
            WHEN s2.tox > 0 THEN s2.tox
            WHEN s3.tox > 0 THEN s3.tox
            WHEN s4.tox > 0 THEN s4.tox
            WHEN s5.tox > 0 THEN s5.tox
            WHEN s6.tox > 0 THEN s6.tox
            WHEN s7.tox > 0 THEN s7.tox
            WHEN s8.tox > 0 THEN s8.tox
            WHEN s9.tox > 0 THEN s9.tox
            ELSE 0
        END as tox,
        (
            SELECT CASE
                WHEN s1.tox > 0 THEN s1.fec
                WHEN s2.tox > 0 THEN s2.fec
                WHEN s3.tox > 0 THEN s3.fec
                WHEN s4.tox > 0 THEN s4.fec
                WHEN s5.tox > 0 THEN s5.fec
                WHEN s6.tox > 0 THEN s6.fec
                WHEN s7.tox > 0 THEN s7.fec
                WHEN s8.tox > 0 THEN s8.fec
                WHEN s9.tox > 0 THEN s9.fec
            END
        ) as toxfec,
        CASE
            WHEN (
                SELECT CASE
                    WHEN s1.tox > 0 THEN s1.fec
                    WHEN s2.tox > 0 THEN s2.fec
                    WHEN s3.tox > 0 THEN s3.fec
                    WHEN s4.tox > 0 THEN s4.fec
                    WHEN s5.tox > 0 THEN s5.fec
                    WHEN s6.tox > 0 THEN s6.fec
                    WHEN s7.tox > 0 THEN s7.fec
                    WHEN s8.tox > 0 THEN s8.fec
                    WHEN s9.tox > 0 THEN s9.fec
                END
            ) IS NULL OR
            (
                SELECT DATE(
                    CASE
                        WHEN s1.tox > 0 THEN s1.fec
                        WHEN s2.tox > 0 THEN s2.fec
                        WHEN s3.tox > 0 THEN s3.fec
                        WHEN s4.tox > 0 THEN s4.fec
                        WHEN s5.tox > 0 THEN s5.fec
                        WHEN s6.tox > 0 THEN s6.fec
                        WHEN s7.tox > 0 THEN s7.fec
                        WHEN s8.tox > 0 THEN s8.fec
                        WHEN s9.tox > 0 THEN s9.fec
                    END
                ) >= DATE('$fec') - INTERVAL '102 DAYS'
            ) THEN false
            ELSE true
        END as toxvencido,
        CASE
            WHEN s1.cla_g > 0 THEN s1.cla_g
            WHEN s2.cla_g > 0 THEN s2.cla_g
            WHEN s3.cla_g > 0 THEN s3.cla_g
            WHEN s4.cla_g > 0 THEN s4.cla_g
            WHEN s5.cla_g > 0 THEN s5.cla_g
            WHEN s6.cla_g > 0 THEN s6.cla_g
            WHEN s7.cla_g > 0 THEN s7.cla_g
            WHEN s8.cla_g > 0 THEN s8.cla_g
            WHEN s9.cla_g > 0 THEN s9.cla_g
            ELSE 0
        END as cla_g,
        (
            SELECT CASE
                WHEN s1.cla_g > 0 THEN s1.fec
                WHEN s2.cla_g > 0 THEN s2.fec
                WHEN s3.cla_g > 0 THEN s3.fec
                WHEN s4.cla_g > 0 THEN s4.fec
                WHEN s5.cla_g > 0 THEN s5.fec
                WHEN s6.cla_g > 0 THEN s6.fec
                WHEN s7.cla_g > 0 THEN s7.fec
                WHEN s8.cla_g > 0 THEN s8.fec
                WHEN s9.cla_g > 0 THEN s9.fec
            END
        ) as cla_gfec,
        CASE
            WHEN (
                SELECT CASE
                    WHEN s1.cla_g > 0 THEN s1.fec
                    WHEN s2.cla_g > 0 THEN s2.fec
                    WHEN s3.cla_g > 0 THEN s3.fec
                    WHEN s4.cla_g > 0 THEN s4.fec
                    WHEN s5.cla_g > 0 THEN s5.fec
                    WHEN s6.cla_g > 0 THEN s6.fec
                    WHEN s7.cla_g > 0 THEN s7.fec
                    WHEN s8.cla_g > 0 THEN s8.fec
                    WHEN s9.cla_g > 0 THEN s9.fec
                END
            ) IS NULL OR
            (
                SELECT DATE(
                    CASE
                        WHEN s1.cla_g > 0 THEN s1.fec
                        WHEN s2.cla_g > 0 THEN s2.fec
                        WHEN s3.cla_g > 0 THEN s3.fec
                        WHEN s4.cla_g > 0 THEN s4.fec
                        WHEN s5.cla_g > 0 THEN s5.fec
                        WHEN s6.cla_g > 0 THEN s6.fec
                        WHEN s7.cla_g > 0 THEN s7.fec
                        WHEN s8.cla_g > 0 THEN s8.fec
                        WHEN s9.cla_g > 0 THEN s9.fec
                    END
                ) >= DATE('$fec') - INTERVAL '102 DAYS'
            ) THEN false
            ELSE true
        END as cla_gvencido,
        CASE
            WHEN s1.cla_m > 0 THEN s1.cla_m
            WHEN s2.cla_m > 0 THEN s2.cla_m
            WHEN s3.cla_m > 0 THEN s3.cla_m
            WHEN s4.cla_m > 0 THEN s4.cla_m
            WHEN s5.cla_m > 0 THEN s5.cla_m
            WHEN s6.cla_m > 0 THEN s6.cla_m
            WHEN s7.cla_m > 0 THEN s7.cla_m
            WHEN s8.cla_m > 0 THEN s8.cla_m
            WHEN s9.cla_m > 0 THEN s9.cla_m
            ELSE 0
        END as cla_m,
        (
            SELECT CASE
                WHEN s1.cla_m > 0 THEN s1.fec
                WHEN s2.cla_m > 0 THEN s2.fec
                WHEN s3.cla_m > 0 THEN s3.fec
                WHEN s4.cla_m > 0 THEN s4.fec
                WHEN s5.cla_m > 0 THEN s5.fec
                WHEN s6.cla_m > 0 THEN s6.fec
                WHEN s7.cla_m > 0 THEN s7.fec
                WHEN s8.cla_m > 0 THEN s8.fec
                WHEN s9.cla_m > 0 THEN s9.fec
            END
        ) as cla_mfec,
        CASE
            WHEN (
                SELECT CASE
                    WHEN s1.cla_m > 0 THEN s1.fec
                    WHEN s2.cla_m > 0 THEN s2.fec
                    WHEN s3.cla_m > 0 THEN s3.fec
                    WHEN s4.cla_m > 0 THEN s4.fec
                    WHEN s5.cla_m > 0 THEN s5.fec
                    WHEN s6.cla_m > 0 THEN s6.fec
                    WHEN s7.cla_m > 0 THEN s7.fec
                    WHEN s8.cla_m > 0 THEN s8.fec
                    WHEN s9.cla_m > 0 THEN s9.fec
                END
            ) IS NULL OR
            (
                SELECT DATE(
                    CASE
                        WHEN s1.cla_m > 0 THEN s1.fec
                        WHEN s2.cla_m > 0 THEN s2.fec
                        WHEN s3.cla_m > 0 THEN s3.fec
                        WHEN s4.cla_m > 0 THEN s4.fec
                        WHEN s5.cla_m > 0 THEN s5.fec
                        WHEN s6.cla_m > 0 THEN s6.fec
                        WHEN s7.cla_m > 0 THEN s7.fec
                        WHEN s8.cla_m > 0 THEN s8.fec
                        WHEN s9.cla_m > 0 THEN s9.fec
                    END
                ) >= DATE('$fec') - INTERVAL '102 DAYS'
            ) THEN false
            ELSE true
        END as cla_mvencido,
        CASE
            WHEN p.nom IS NOT NULL THEN 1
            ELSE 0
        END as es_paciente,
        s1.id,
        s1.iduserupdate
        FROM hc_antece_p_sero s1
        LEFT JOIN hc_paciente p ON p.dni = s1.iduserupdate
        LEFT JOIN LATERAL (
            SELECT *
            FROM hc_antece_p_sero
            WHERE estado = 1 AND p_dni = '$dni'
            ORDER BY fec DESC
            LIMIT 2
        ) s2 ON s1.fec > s2.fec
        LEFT JOIN LATERAL (
            SELECT *
            FROM hc_antece_p_sero
            WHERE estado = 1 AND p_dni = '$dni'
            ORDER BY fec DESC
            LIMIT 3
        ) s3 ON s2.fec > s3.fec
        LEFT JOIN LATERAL (
            SELECT *
            FROM hc_antece_p_sero
            WHERE estado = 1 AND p_dni = '$dni'
            ORDER BY fec DESC
            LIMIT 4
        ) s4 ON s3.fec > s4.fec
        LEFT JOIN LATERAL (
            SELECT *
            FROM hc_antece_p_sero
            WHERE estado = 1 AND p_dni = '$dni'
            ORDER BY fec DESC
            LIMIT 5
        ) s5 ON s4.fec > s5.fec
        LEFT JOIN LATERAL (
            SELECT *
            FROM hc_antece_p_sero
            WHERE estado = 1 AND p_dni = '$dni'
            ORDER BY fec DESC
            LIMIT 6
        ) s6 ON s5.fec > s6.fec
        LEFT JOIN LATERAL (
            SELECT *
            FROM hc_antece_p_sero
            WHERE estado = 1 AND p_dni = '$dni'
            ORDER BY fec DESC
            LIMIT 7
        ) s7 ON s6.fec > s7.fec
        LEFT JOIN LATERAL (
            SELECT *
            FROM hc_antece_p_sero
            WHERE estado = 1 AND p_dni = '$dni'
            ORDER BY fec DESC
            LIMIT 8
        ) s8 ON s7.fec > s8.fec
        LEFT JOIN LATERAL (
            SELECT *
            FROM hc_antece_p_sero
            WHERE estado = 1 AND p_dni = '$dni'
            ORDER BY fec DESC
            LIMIT 9
        ) s9 ON s8.fec > s9.fec
        WHERE s1.p_dni = '$dni' AND s1.estado = 1 AND s1.tipo_paciente = $tipopaciente
        ORDER BY s1.id DESC
        LIMIT 1";

    $Sero = $db->prepare($query);

    $Sero->execute();

    return $Sero->fetch(PDO::FETCH_ASSOC);
}

function insertGineMensaje($fecha, $paciente_id, $idusercreate, $mensaje ,$estado,$id_gineco_tip_atencion)
{
    global $db;
    $stmt = $db->prepare(
        "INSERT INTO hc_gineco_mensajes (fecha, paciente_id, idusercreate, mensaje,estado,id_gineco_tip_atencion) VALUES
        (?, ?, ?, ?,?,?)"
    );

    $stmt->execute(array($fecha, $paciente_id, $idusercreate, $mensaje,$estado,$id_gineco_tip_atencion));

    echo "<div id='alerta'> Datos guardados en el historial de consultas de laboratorio! </div>";
}

function updatehisteroscopia($dni, $nom,$fnac, $a_fecha, $a_analisis_tipo, $a_parrafo1, $imagen1parr1, $imagen2parr1, $imagen3parr1, $a_parrafo2, $imagen1parr2, $imagen2parr2, $imagen3parr2, $comentario, $login, $idx,$id)
{

    global $db;
    $hist_path = "storage/analisis_archivo/histeroscopia/";
    $archivo_path = '';

    if ($id == "") {
        $stmt = $db->prepare("INSERT INTO analisis_histeroscopia
        (dni, nombre, fnac, fecha, tipo_analisis, a_parrafo1,  a_parrafo2,idx,comentario,idusercreate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
        $stmt->execute(array($dni, $nom,$fnac, $a_fecha, $a_analisis_tipo, $a_parrafo1, $a_parrafo2,$idx, $comentario, $login));
        $id = $db->lastInsertId();

    } else {
        $stmt = $db->prepare("UPDATE analisis_histeroscopia SET dni=?,nombre=?,fnac=?,fecha=?,tipo_analisis=?,a_parrafo1=?,a_parrafo2=?,idx=?,comentario=?,idusercreate=? WHERE id=?");
        $stmt->execute(array($dni, $nom,$fnac, $a_fecha, $a_analisis_tipo, $a_parrafo1,  $a_parrafo2,$idx, $comentario, $login,$id));

    }

    $randomimg1p1=rand(100, 99999999);
    $randomimg1p12=rand(100, 99999999);
    if (isset($imagen1parr1) && !empty($imagen1parr1['name'])) {
        if ($imagen1parr1['name'] <> "") {
            if (is_uploaded_file($imagen1parr1['tmp_name'])) {
                $nombre_original = $imagen1parr1['name'];

                $imagen1parr1_name = $dni .'-'.$randomimg1p1.'-'.$randomimg1p12.'.jpg';
                $nombre_base = time() . "-" . $imagen1parr1_name;
                $archivo_path = $hist_path . $nombre_base;
                move_uploaded_file($imagen1parr1['tmp_name'], $archivo_path);

                $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?);");
                $stmt->execute(array($nombre_base, $nombre_original, $login));
                $archivo_id = $db->lastInsertId();

                // actualizar informe
                $stmt = $db->prepare("UPDATE analisis_histeroscopia set imagen1parr1 = ?, iduserupdate = ? where id = ?;");
                $stmt->execute([$archivo_id, $login, $id]);
            }
        }
    }

    $randomimg2p1=rand(100, 99999999);
    $randomimg2p12=rand(100, 99999999);
    if (isset($imagen2parr1) && !empty($imagen2parr1['name'])) {
        if ($imagen2parr1['name'] <> "") {
            if (is_uploaded_file($imagen2parr1['tmp_name'])) {
                $nombre_original = $imagen2parr1['name'];

                $imagen2parr1_name = $dni .'-'.$randomimg2p1.'-'.$randomimg2p12.'.jpg';
                $nombre_base = time() . "-" . $imagen2parr1_name;
                $archivo_path = $hist_path . $nombre_base;
                move_uploaded_file($imagen2parr1['tmp_name'], $archivo_path);

                $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?);");
                $stmt->execute(array($nombre_base, $nombre_original, $login));
                $archivo_id = $db->lastInsertId();

                // actualizar informe
                $stmt = $db->prepare("UPDATE analisis_histeroscopia set imagen2parr1 = ?, iduserupdate = ? where id = ?;");
                $stmt->execute([$archivo_id, $login, $id]);
            }
        }
    }

    $randomimg3p1=rand(100, 99999999);
    $randomimg3p12=rand(100, 99999999);
    if (isset($imagen3parr1) && !empty($imagen3parr1['name'])) {
        if ($imagen3parr1['name'] <> "") {
            if (is_uploaded_file($imagen3parr1['tmp_name'])) {
                $nombre_original = $imagen3parr1['name'];

                $imagen3parr1_name = $dni .'-'.$randomimg3p1.'-'.$randomimg3p12.'.jpg';
                $nombre_base = time() . "-" . $imagen3parr1_name;
                $archivo_path = $hist_path . $nombre_base;
                move_uploaded_file($imagen3parr1['tmp_name'], $archivo_path);

                $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?);");
                $stmt->execute(array($nombre_base, $nombre_original, $login));
                $archivo_id = $db->lastInsertId();

                // actualizar informe
                $stmt = $db->prepare("UPDATE analisis_histeroscopia set imagen3parr1 = ?, iduserupdate = ? where id = ?;");
                $stmt->execute([$archivo_id, $login, $id]);
            }
        }
    }

    $randomimg1p2=rand(100, 99999999);
    $randomimg1p22=rand(100, 99999999);
    if (isset($imagen1parr2) && !empty($imagen1parr2['name'])) {
        if ($imagen1parr2['name'] <> "") {
            if (is_uploaded_file($imagen1parr2['tmp_name'])) {
                $nombre_original = $imagen1parr2['name'];

                $imagen1parr2_name = $dni .'-'.$randomimg1p2.'-'.$randomimg1p22.'.jpg';
                $nombre_base = time() . "-" . $imagen1parr2_name;
                $archivo_path = $hist_path . $nombre_base;
                move_uploaded_file($imagen1parr2['tmp_name'], $archivo_path);

                $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?);");
                $stmt->execute(array($nombre_base, $nombre_original, $login));
                $archivo_id = $db->lastInsertId();

                // actualizar informe
                $stmt = $db->prepare("UPDATE analisis_histeroscopia set imagen1parr2 = ?, iduserupdate = ? where id = ?;");
                $stmt->execute([$archivo_id, $login, $id]);
            }
        }
    }

    $randomimg2p2=rand(100, 99999999);
    $randomimg2p22=rand(100, 99999999);
    if (isset($imagen2parr2) && !empty($imagen2parr2['name'])) {
        if ($imagen2parr2['name'] <> "") {
            if (is_uploaded_file($imagen2parr2['tmp_name'])) {
                $nombre_original = $imagen2parr2['name'];

                $imagen2parr2_name = $dni .'-'.$randomimg2p2.'-'.$randomimg2p22.'.jpg';
                $nombre_base = time() . "-" . $imagen2parr2_name;
                $archivo_path = $hist_path . $nombre_base;
                move_uploaded_file($imagen2parr2['tmp_name'], $archivo_path);

                $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?);");
                $stmt->execute(array($nombre_base, $nombre_original, $login));
                $archivo_id = $db->lastInsertId();

                // actualizar informe
                $stmt = $db->prepare("UPDATE analisis_histeroscopia set imagen2parr2 = ?, iduserupdate = ? where id = ?;");
                $stmt->execute([$archivo_id, $login, $id]);
            }
        }
    }

    $randomimg3p2=rand(100, 99999999);
    $randomimg3p22=rand(100, 99999999);
    if (isset($imagen3parr2) && !empty($imagen3parr2['name'])) {
        if ($imagen3parr2['name'] <> "") {
            if (is_uploaded_file($imagen3parr2['tmp_name'])) {
                $nombre_original = $imagen3parr2['name'];

                $imagen3parr2_name = $dni .'-'.$randomimg3p2.'-'.$randomimg3p22.'.jpg';
                $nombre_base = time() . "-" . $imagen3parr2_name;
                $archivo_path = $hist_path . $nombre_base;
                move_uploaded_file($imagen3parr2['tmp_name'], $archivo_path);

                $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?);");
                $stmt->execute(array($nombre_base, $nombre_original, $login));
                $archivo_id = $db->lastInsertId();

                // actualizar informe
                $stmt = $db->prepare("UPDATE analisis_histeroscopia set imagen3parr2 = ?, iduserupdate = ? where id = ?;");
                $stmt->execute([$archivo_id, $login, $id]);
            }
        }
    }

    return [
        'procedimiento' => $id,
        'archivo_path' => $archivo_path
    ];
}