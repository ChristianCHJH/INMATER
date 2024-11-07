<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php';
    require "_database/database.php"; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" id="e_analisis" data-dialog="true">
        <?php
        $id=0;
        if($_GET['id']!=""){
            $id=$_GET['id'];
        }else{
            $id=0;
        }
        
        if (isset($_POST['dni']) && !empty($_POST['dni'])) {
            global $db;
            $path = $_SERVER["DOCUMENT_ROOT"] . "/storage/examenes/";
            $archivo_id = 0;
            $informe = $_FILES['informe'];

            if (isset($informe)) {
                if (!empty($informe['name'])) {
                    $informe_name = $informe['name'];
                    $nombre_original = $informe_name;
                    $informe_name = preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                    $nombre_base = time() . "-" . $informe_name;
                    $ruta = $path . $nombre_base;
        
                    if (is_uploaded_file($informe['tmp_name'])) {
                        move_uploaded_file($informe['tmp_name'], $ruta);
        
                        $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?)");
                        $stmt->execute(array($nombre_base, $nombre_original, $login));
                        $archivo_id = $db->lastInsertId();
                    }
                }
            }

            if ($_POST['idx'] == "" || $_POST['idx'] ==0) {
                $stmt = $db->prepare("INSERT INTO man_examenes
                (paciente_id, tipo_examen_id, resultado_id, archivo_id, fecha, observacion, idusercreate) VALUES
                (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['dni'], $_POST['tipo_examen_id'], $_POST['resultado_id'], $archivo_id, $_POST['fecha'], $_POST['observacion'], $login]);
                $id = $db->lastInsertId();
            } else {
                if ($archivo_id != 0) {
                    $stmt = $db->prepare("UPDATE man_examenes SET paciente_id = ?, tipo_examen_id = ?, resultado_id = ?, archivo_id = ?, fecha = ?, observacion = ?, iduserupdate = ? WHERE id = ?");
                    $stmt->execute([$_POST['dni'], $_POST['tipo_examen_id'], $_POST['resultado_id'], $archivo_id, $_POST['fecha'], $_POST['observacion'], $login, $_POST['idx']]);
                } else {
                    $stmt = $db->prepare("UPDATE man_examenes SET paciente_id = ?, tipo_examen_id = ?, resultado_id = ?, fecha = ?, observacion = ?, iduserupdate = ? WHERE id = ?");
                    $stmt->execute([$_POST['dni'], $_POST['tipo_examen_id'], $_POST['resultado_id'], $_POST['fecha'], $_POST['observacion'], $login, $_POST['idx']]);
                }
            }
        }     
            ?>
        <style>
            .ui-dialog-contain {
                max-width: 1000px;
                margin: 2% auto 15px;
                padding: 0;
                position: relative;
                top: -15px;
            }
            .scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap; } 
            .paci_insert {
                text-transform: uppercase; font-size:small;
            }
            .enlinea .ui-checkbox {
                display : inline-block;
                float:right;
            }
        </style>

        <script>
        $(document).ready(function () {
            $('#form1').submit(function() {
                // Prevenir el envío por defecto del formulario
                event.preventDefault();

                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Guardando datos...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                 // Crear un FormData y agregar los datos del formulario
                var formData = new FormData(this);
                // Simular un retraso para demostración (eliminar en producción)
                setTimeout(function () {
                    // Enviar el formulario de forma asíncrona usando AJAX
                    $.ajax({
                        type: 'POST',
                        url: $('#form1').attr('action'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function (response) {
                            if (response.status == false) {
                                Swal.close();
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer);
                                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                                    }
                                });
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Error al guardar los datos',
                                    text: 'Hubo un problema al procesar la solicitud'
                                });
                                return false;
                            }
                            // Ocultar indicador de carga y mostrar Toast de éxito
                            Swal.close();
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            });

                            // Mostrar Toast de éxito
                            Toast.fire({
                                icon: 'success',
                                title: 'Datos guardados exitosamente'
                            });

                            if ($('#idx').val().trim() == '') {
                                // Limpiar el formulario si es necesario
                                $('#form1')[0].reset();
                            }
                        },
                        error: function (error) {
                            // Manejar errores si es necesario
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al guardar los datos',
                                text: 'Hubo un problema al procesar la solicitud'
                            });
                        }
                    });
                }, 1000); // Simular retraso de 1 segundo (eliminar en producción)

                return false;
            });
        });
        </script>

        <div data-role="header" data-theme="b" data-position="fixed">
            <?php
            if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
                print('<a href="'.$_GET["path"].'.php?dni=' . $_GET["dni"] . '" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } ?>
            <h1>Nuevo Exámen</h1>
        </div>
<?php

    $stmt = $db->prepare(
        "SELECT
        me.id, me.tipo_examen_id, me.resultado_id, coalesce(a.nombre_base, '-') archivo, me.fecha, me.observacion
        FROM man_examenes me
        LEFT JOIN man_archivo a ON a.id = me.archivo_id
        WHERE me.id = ?;"
    );
    $stmt->execute([$id]);
    $pop = $stmt->fetch(PDO::FETCH_ASSOC); 
    
?>
        <div class="ui-content" role="main">
            <form action="ajax/examen_clinico/man_examen_ajax_send.php" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
                <input type="hidden" name="idx" id="idx" value="<?php echo $id;?>">
                <?php print('<input type="hidden" name="dni" id="dni" value="' . $_GET['dni'] . '">'); ?>

                <table width="100%" align="center" style="margin: 0 auto;">
                    <tr>
                        <td>Fecha</td>
                        <td width="1053"><input name="fecha" type="date" required id="fecha" value="<?php echo date('Y-m-d', strtotime($pop['fecha']));?>" data-mini="true"></td>
                        <!-- <td width="4">&nbsp;</td> -->
                    </tr>
                    <tr>
                        <td>Tipo de Exámen</td>
                        <td colspan="2">
                            <select name="tipo_examen_id" id="tipo_examen_id" required data-mini="true">
                                <option value="">SELECCIONAR</option>
                                <?php
                                $stmt = $db->prepare("SELECT id, nombre FROM man_tipo_examen WHERE estado = 1 AND padre_id = ? ORDER BY nombre ASC");
                                $stmt->execute([$_GET['tipo']]);

                                while($med = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $med['id']; ?>" <?php if (isset($pop['archivo'])){if ($med['id'] == $pop['tipo_examen_id']) echo 'selected';}?>><?php print(mb_strtoupper($med['nombre'])); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Resultado</td>
                        <td colspan="2">
                            <select name="resultado_id" id="resultado_id" required data-mini="true">
                                <option value="">SELECCIONAR</option>
                                <option value="1" <?php if (isset($pop['archivo'])){if ($pop['resultado_id'] == '1') print('selected'); } ?>>POSITIVO</option>
                                <option value="2" <?php if (isset($pop['archivo'])){if ($pop['resultado_id'] == '2') print('selected'); }?>>NEGATIVO</option>
                                <option value="3" <?php if (isset($pop['archivo'])){if ($pop['resultado_id'] == '3') print('selected'); }?>>NO RESULTADO</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Informe</td>
                        <td colspan="2">
                            <input name="informe" type="file" id="informe" accept="application/pdf" data-mini="true"/>
                            <?php
                                if (isset($pop['archivo']) && file_exists('storage/examenes/' . $pop['archivo'])) {
                                    print('<a href="archivos_hcpacientes.php?idStorage=examenes/' . $pop['archivo'] . '" target="new">Ver Informe</a>');
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Observación</td>
                        <td colspan="2">
                            <textarea name="observacion" id="observacion" data-mini="true"><?php echo isset($pop['observacion']) ? $pop['observacion'] : ''; ?></textarea>
                        </td>
                    </tr>
                </table>
                <div class="enlinea">
                    <input name="guardar" type="Submit" id="guardar" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-inline="true" data-theme="b" data-mini="true"/>
                </div>
                <div data-role="popup" id="cargador" data-overlay-theme="b" data-dismissible="false"><p>GUARDANDO DATOS..</p></div>
            </form>
        </div>
    </div>
    <script src="js/analisis_clinico.js"></script>
    <script>
        loadClinicalExam();
    </script>
</body>
</html>