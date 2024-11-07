<?php
session_start();
ini_set("display_errors","1");
error_reporting(E_ALL);
?>
<!DOCTYPE HTML>
<html>
<head>
    <?php
        $login = $_SESSION['login'];
        $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
        if ($_SESSION['role'] <> 2) {
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
        }
        require("_database/db_tools.php");
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
</head>
<body>
    <?php require ('_includes/menu_andrologia.php'); ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <a class="breadcrumb" href="lista_and.php">
                <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
            </a>

            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="lista_and.php">Andrología</a></li>
                <li class="breadcrumb-item active" aria-current="page">Capacitaciones Invitro</li>
            </ol>
        </nav>
            <?php
                $between = $ini = $fin = "";

                $ini = date('Y-m-01');
                $fin = date('Y-m-t');
                $between .= " and a.fec between '$ini' and '$fin'";

                $consulta = $db->prepare("SELECT id, nom from lab_user where sta=0");
                $consulta->execute();
                $consulta->setFetchMode(PDO::FETCH_ASSOC);
                $datos = $consulta->fetchAll();

                $item = 0;

                $consulta = $db->prepare("SELECT
                r.id rep, STRING_AGG(CAST(c.id AS TEXT), ',') ids, STRING_AGG(to_char(c.fec, 'YYYY-MM-DD'), ',') fecha_capacitacion, r.f_asp fecha_aspiracion
                , r.fec fecha, a.pro, r.tipo_documento, r.dni, r.p_dni, r.des_dia, r.des_don, r.p_od, r.p_fiv, r.p_icsi,
                r.apellidos, r.nombres, r.fnacimiento, r.med medico, r.p_dni_het
                , r.p_dtri, r.p_cic, r.p_cri, r.p_iiu, r.p_don, r.pago_extras
                from (
                    select a.id, a.fec, a.f_asp, di.nombre tipo_documento, a.dni, a.p_dni, a.des_dia, a.des_don, a.p_od, a.p_fiv, a.p_icsi, a.med
                    , p.ape apellidos, p.nom nombres, p.fnac fnacimiento, a.p_dni_het
                    , a.p_dtri, a.p_cic, a.p_cri, a.p_iiu, a.p_don, a.pago_extras
                    from hc_reprod a
                    inner join hc_paciente p on p.dni = a.dni
                    inner join man_tipo_documento_identidad di on di.codigo = p.tip
                    where a.estado = true and (1=1)$between) r
                left join lab_aspira a on a.rep = r.id and a.estado is true
                left join lab_andro_cap c on ((c.pro = a.pro) or (c.rep = r.id)) and c.eliminado is false
                group by r.id,r.f_asp,r.fec,a.pro,r.tipo_documento,r.dni,r.p_dni, r.des_dia, r.des_don, r.p_od, r.p_fiv, r.p_icsi,r.apellidos, r.nombres, r.fnacimiento, r.med, r.p_dni_het
                , r.p_dtri, r.p_cic, r.p_cri, r.p_iiu, r.p_don, r.pago_extras
                order by r.f_asp desc");
                $consulta->execute(); ?>
                <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                    <?php
                    print('
                        <small><b>Lista de Capacitaciones Invitro: </b>'.date("Y-m-d H:i:s").'
                        <b>, Total Registros: </b>'.$consulta->rowCount().'
                        </small>'); ?>
                </h5>
                <input type="text" class="form-control" id="datos_paciente" onkeyup="datos_paciente()" placeholder="Buscar Paciente.." title="escribe apellidos, nombres o número de documento de paciente">
                <?php
                print('
                    <table width="100%" class="table table-responsive table-bordered align-middle" style="margin-bottom: 0 !important;" id="lista_capacitaciones">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">Item</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">F. Aspiración</th>
                                <th class="text-center">Procedimiento</th>
                                <th class="text-center">Protocolo</th>
                                <th class="text-center">Tipo Documento</th>
                                <th class="text-center">N° Documento</th>
                                <th class="text-center">Apellidos y Nombres</th>
                                <th class="text-center">Médico</th>
                                <th class="text-center">Capacitaciones</th>
                                <th class="text-center">Operaciones</th>
                            </tr>
                        </thead>
                        <tbody>');
                $item = 1;
                
                $path_url = "";
                if (strpos($_SERVER["REQUEST_URI"], "?") !== false) {
                    $path_url = substr($_SERVER["REQUEST_URI"], strpos($_SERVER["REQUEST_URI"], "?"), strlen($_SERVER["REQUEST_URI"]));
                    $path_url = urlencode($path_url);
                }

                while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
                    $var = "";
                    if (!empty($data["ids"])) {
                        $var = "";
                        $pos = 0;

                        foreach (explode(",", $data["ids"]) as $key => $value) {
                            $var .= '<a href="le_andro_cap.php?path=andro_capacitaciones_invitro&path_url=' . $path_url . '&dni='.$data['dni'].'&ip='.$data['p_dni'].'&id='.$value.'" rel="external">'.explode(",", $data["fecha_capacitacion"])[$pos].'</a><br>';
                            $pos++;
                        }
                    }

                    print("
                    <tr>
                        <td class='text-center'>".$item++."</td>
                        <td class='text-center'>".$data['fecha']."</td>
                        <td class='text-center'>".substr($data['fecha_aspiracion'], 0, 10)."</td>
                        <td>");
                        if ($data['p_dtri'] >= 1) { echo "Dual Trigger<br>"; }
                        if ($data['p_cic'] >= 1) { echo "Ciclo Natural<br>"; }
                        if ($data['p_fiv'] >= 1) { echo "FIV<br>"; }
                        if ($data['p_icsi'] >= 1) { echo $_ENV["VAR_ICSI"] . "<br>"; }
                        if ($data['p_od'] <> '') { echo "OD Fresco<br>"; }
                        if ($data['p_cri'] >= 1) { echo "Crio Ovulos<br>"; }
                        if ($data['p_iiu'] >= 1) { echo "IIU<br>"; }
                        if ($data['p_don'] == 1) { echo "Donación Fresco<br>"; }
                        if ($data['des_don'] == null && $data['des_dia'] >= 1) { echo "TED<br>"; }
                        if ($data['des_don'] == null && $data['des_dia'] === 0) { echo "<small>Descongelación Ovulos Propios</small><br>"; }
                        if ($data['des_don'] <> null && $data['des_dia'] >= 1) { echo "EMBRIODONACIÓN<br>"; }
                        if ($data['des_don'] <> null && $data['des_dia'] === 0) { echo "<small>Descongelación Ovulos Donados</small><br>"; }
                        print('Extras: '.$data['pago_extras']);
                        print('
                        </td>
                        <td class="text-center">'.$data['pro'].'</td>
                        <td class="text-center">'.$data['tipo_documento'].'</td>
                        <td class="text-center">'.$data['dni'].'</td>
                        <td>'.mb_strtoupper($data['apellidos']).' '.mb_strtoupper($data['nombres']).' ('.date_diff(date_create($data['fnacimiento']), date_create('today'))->y.')</td>
                        <td class="text-center">'.$data['medico'].'</td>
                        <td class="text-center">'.$var.'</td>
                        <td class="text-center">
                            <a href="le_andro_cap.php?path=andro_capacitaciones_invitro&path_url=' . $path_url . '&dni='.$data['dni'] . '&ip=' . $data['p_dni'] . '&pro='.$data['pro'].'&rep='.$data['rep'].'&het='.$data['p_dni_het'].'&id=" rel="external" class="btn btn-danger">Agregar</a>
                        </td>');
                    print('</tr>');
                }

                print('
                </tbody>
                    </table>'); ?>
        </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $("#guardar").on("click", function () {
                $("#modal_editar").modal('show')
            });
        });

        $(document).keydown('#datos_paciente', function(e){
            if(e.which == 13) {
                var paciente = $('#datos_paciente')[0].value

                mostrarLoader("Espere por favor"); // Mostrar el loader antes de la solicitud AJAX
                    $.post("le_tanque.php", {tipo_reporte: "lista_paciente", dato: paciente}, function (data) {
                        $("#lista_capacitaciones tbody").html(""); 
                        if (data.trim().length === 0) { // Verificar si no hay datos
                            search = $('#datos_paciente').val();
                            mostrarToast('info','No se encontraron registros para "'+search+'"');
                        } else {
                            $("#lista_capacitaciones tbody").append(data);
                        }
                    })
                    .done(function() {
                        $("#datos_paciente").prop("disabled", false);
                        $("#datos_paciente").focus();
                        ocultarLoader(); // Ocultar el loader después de que la solicitud se complete con éxito
                    })
                    .fail(function() {
                        $("#datos_paciente").prop("disabled", false);
                        ocultarLoader(); // Ocultar el loader en caso de error
                        mostrarToast('error','Hubo un problema al intentar obtener la lista de pacientes.');
                    });
            }
        });
    </script>
    <script src="js/capacitacion.js"></script>
</body>
</html>