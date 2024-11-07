<?php
 include 'seguridad_login.php'?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inmater Clínica de Fertilidad | Programación de agenda</title>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
</head>
<body>
    <?php
        // tipo de consulta
        $tipoconsulta_array = array(
            '1' => 'Presencial',
            '2'  => 'Virtual',
        );


        if (!!$_GET and isset($_GET['id']) and !empty($_GET['id'])) {
            // traer datos de la consulta
            $stmt = $db->prepare(
                "SELECT
                lower(mm.nombre) medico, hg.tipoconsulta_ginecologia_id tipo_consulta, lower(mgm.nombre) motivo_consulta, lower(hg.mot) motivoconsulta_otros, lower(s.nombre) sede
                , hg.fec fecha_programada, hg.fec_h hora_programada, hg.fec_m minuto_programado
                , hp.ape apellidos, hp.nom nombres
                , hg.estadoconsulta_ginecologia_id estadoconsulta_id
                , hg.fecha_confirmacion, hg.fecha_voucher, hg.voucher_id
                from hc_gineco hg
                inner join hc_paciente hp on hp.dni = hg.dni
                inner join man_medico mm on mm.codigo = hg.med
                inner join man_gine_motivoconsulta mgm on mgm.id = hg.man_motivoconsulta_id
                inner join sedes s on s.codigo_facturacion = hg.cupon
                where hg.id = ?;"
            );

            $stmt->execute([$_GET['id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        } /* else {
            # code...
        } */
        

    ?>

    <div class="loader">
        <img src="_images/load.gif" alt="">			
    </div>

    <?php require '_includes/menu_consulta.php'; ?>
    <div class="container">
        <?php print('<input type="hidden" name="consulta_id" id="consulta_id" value="' . $_GET['id'] . '">'); ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Inicio</li>
                <li class="breadcrumb-item"><a href="lista_consulta.php">Agenda</a></li>
                <?php print('<li class="breadcrumb-item">' . ucwords(mb_strtolower($data['apellidos'])) . ' ' . ucwords(mb_strtolower($data['nombres'])) .'</li>'); ?>
                <li class="breadcrumb-item active" aria-current="page">Seguimiento consulta</li>
            </ol>
        </nav>

        <div class="card mb-3">
            <h5 class="card-header">Paso 1: Información de esta consulta</h5>
            <div class="card-body">
                <div class="row pb-2">
                    <ul class="list-group list-group-flush">
                        <?php
                            print(
                                '<li class="list-group-item">Consulta programada para el día <span class="cn">' . strftime("%d %B", strtotime($data['fecha_programada'])) . '</span> a las <span class="cn">' . $data['hora_programada'] . ' horas con ' . $data['minuto_programado'] . ' minutos</span>.</li>
                                <li class="list-group-item">Tipo de consulta: <span class="cn">' . ucwords(mb_strtolower($tipoconsulta_array[$data['tipo_consulta']])) . '</span></li>
                                <li class="list-group-item">Médico: <span class="cn">' . ucwords(mb_strtolower($data['medico'])) . '</span></li>
                                <li class="list-group-item">Sede: <span class="cn">' . ucwords(mb_strtolower($data['sede'])) . '</span></li>
                                <li class="list-group-item">Motivo de la consulta: <span class="cn">' . mb_strtoupper($data['motivo_consulta']) . '</span></li>
                                <li class="list-group-item">Observaciones: <span class="cn">' . mb_strtoupper($data['motivoconsulta_otros']) . '</span></li>'
                            );
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <h5 class="card-header">Paso 2: Información de confirmación</h5>
            <div class="card-body">
                <div class="row pb-2">
                    <ul class="list-group list-group-flush">
                        <?php
                        if ($data['estadoconsulta_id'] == 2 or $data['estadoconsulta_id'] == 4) {
                            print('<li class="list-group-item">La cita se <span class="cn">confirmó</span> el <span class="cn">' . strftime("%d %B", strtotime($data['fecha_confirmacion'])) . '</span> a las <span class="cn">' . date("H:i:s", strtotime($data['fecha_confirmacion'])) . ' horas</span>.</li>');
                        } ?>

                        <?php
                        if ($data['estadoconsulta_id'] == 3) {
                            print('<li class="list-group-item">La cita se <span class="cn">anuló</span> el <span class="cn">' . strftime("%d %B", strtotime($data['fecha_confirmacion'])) . '</span> a las <span class="cn">' . date("H:i:s", strtotime($data['fecha_confirmacion'])) . ' horas</span>.</li>');
                        } ?>

                        <?php
                        if ($data['estadoconsulta_id'] == 1) { ?>
                            <li class="list-group-item">
                                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal_confirmacion">Confirmar esta cita</button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal_anulacion">Anular esta cita</button>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <?php
        if ($data['estadoconsulta_id'] == 2 or $data['estadoconsulta_id'] == 4) { ?>
            <div class="card mb-3">
                <h5 class="card-header">Paso 3: Información de pago</h5>
                <div class="card-body">
                    <div class="row pb-2">
                        <ul class="list-group list-group-flush">
                            <?php
                            if ($data['estadoconsulta_id'] == 4) {
                                print('<li class="list-group-item">Se adjunto el <span class="cn">voucher de pago</span> el <span class="cn">' . strftime("%d %B", strtotime($data['fecha_voucher'])) . '</span> a las <span class="cn">' . date("H:i:s", strtotime($data['fecha_voucher'])) . ' horas</span>.</li>');
                            } ?>
                            <li class="list-group-item">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileLang" accept="image/x-png,image/gif,image/jpeg" lang="es">
                                    <label class="custom-file-label" for="customFileLang">Adjuntar el voucher de pago</label>

                                    <span id="cargando_video" style="display: none;">Cargando...<br></span>
                                    <?php
                                        if ($data['voucher_id'] != 0) {
                                            $stmt = $db->prepare("SELECT * from man_archivo where id=?");
                                            $stmt->execute([$data['voucher_id']]);
                                            $archivo = $stmt->fetch(PDO::FETCH_ASSOC);

                                            print('<a href="archivo/'.$archivo['nombre_base'].'" target="_blank" id="enlace_video">'.$archivo['nombre_original'].'</a>');
                                        } else {
                                            print('<a href="javascript:void(0)" target="_blank" id="enlace_video">-</a>');
                                        }
                                    ?>
                                    <div class="form-group">
                                        <input type="button" class="btn btn-sm btn-dark" id="btn_video" value="Subir voucher" >
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>

    <!-- modals -->
    <!-- modal confirmacion -->
    <div class="modal fade" id="modal_confirmacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Confirmación de cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    La confirmación de cita va informar al médico que se va realizar la cita, se habilitará el adjunto del voucher de pago.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btn-sm" id="confirmar_cita">Confirmar esta cita</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal anulacion -->
    <div class="modal fade" id="modal_anulacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Anulación de cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    La anulación de cita va informar al médico que esta cita ya no se realizará.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger btn-sm" id="anular_cita">Anular de todas maneras</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        jQuery(window).load(function (event) {
            jQuery('.loader').fadeOut(1000);
        });

        $(document).ready(function () {
            $("#confirmar_cita").on("click", function () {
                console.log("confirmar_cita")
                var id = $('#consulta_id').val()
                $.post("_operaciones/seguimiento-consulta.php", {id: id, tipo_operacion: 1}, function (data) {
                    location.reload();
                });
            });

            $("#anular_cita").on("click", function () {
                console.log("anular_cita")
                var id = $('#consulta_id').val()
                $.post("_operaciones/seguimiento-consulta.php", {id: id, tipo_operacion: 2}, function (data) {
                    location.reload();
                });
            });

            jQuery('#btn_video').on('click',function(e){
                var data = new FormData();
                jQuery("#cargando_video").show();
                jQuery("#btn_video").attr("disabled", "disabled");

                var file_data = $('#customFileLang').prop('files')[0];
                // var form_data = new FormData();
                data.append('file', file_data);

                data.append('id', jQuery("#consulta_id").val());
                data.append('tipo_operacion', "3");

                jQuery.ajax({
                    method: "POST",
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    url: "_operaciones/seguimiento-consulta.php",
                    data: data,
                    success: function(data) {
                        console.log(data)
                        jQuery("#cargando_video").hide();
                        jQuery("#btn_video").attr("disabled", false);
                        var result = jQuery.parseJSON(data);
                        jQuery("#btn_video").before('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Éxito!</strong> Se registró el voucher.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        jQuery("#enlace_video").attr("href", "archivo/" + result.message.nombre_base);
                        jQuery("#enlace_video").text(result.message.nombre_original);
                    },
                    error: function(e) {
                        console.log(e)
                        jQuery("#btn_video").before('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Comuníquese con el administrador de su sistema.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }
                });
            });
        });
    </script>
</body>
</html>