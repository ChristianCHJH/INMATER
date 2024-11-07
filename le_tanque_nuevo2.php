<!DOCTYPE HTML>
<html>

<head>
    <?php
    include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Clínica Inmater | Tratamientos fuera de Inmater</title>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php
    if ($_POST['paciente_dni'] <> "" and $_POST['fec'] <> "" and $_POST['med'] <> "" and $_POST['emb'] <> "" and $_POST['dia'] <> "" and $_POST['cri'] <> "") {

        $embriones = array();

        for ($p = 1; $p <= $_POST['cont']; $p++) {
            if (isset($_POST['adju' . $p])) {
                $tan = explode("|", $_POST['c' . $p]);
                $embriones[] = array('pro' => $tan[0], 'ovo' => $tan[1]);
            }
        }

        lab_retiroEmbrio($_POST['paciente_dni'], $_POST['fec'], $_POST['med'], $_POST['emb'], $_POST['embriologo'], $_POST['dia'], $_POST['cri'], $_POST['retiro_num'], $embriones, $login, $_FILES['documento']);
    }
    ?>
    <div data-role="page">


        <style>
            .ui-dialog-contain {
                max-width: 1500px;
                padding: 0;
                position: relative;
                top: -15px;

            }

            .scroll_h {
                overflow-x: scroll;
                overflow-y: hidden;
                white-space: nowrap;
            }

            .button-container {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .button-container input,
            .button-container a {
                margin: 0 10px;
            }
        </style>
        <div data-role="header" data-position="fixed">
            <h3>RETIRO DE OVULOS / EMBRIONES</h3>
        </div>

        <div class="ui-content" role="main" style="max-width: calc(100% - 20rem); margin-right: auto;margin: 0 auto;">

            <form action="../_database/db_tools.php" method="Post" data-ajax="false" id="form2" enctype="multipart/form-data">
                <input type="hidden" name="login" value="<?php echo $login; ?>">
                <table width="100%" align="center" style="margin: 0 auto;max-width:1000px;">
                    <tr>
                        <td width="211">Retiro de: </td>
                        <td width="607">
                            <select name="tip_retiro" id="tip_retiro" data-mini="true" required>
                                <option value="">SELECCIONAR</option>
                                <option value=1>OVULOS</option>
                                <option value=2>EMBRIONES</option>

                            </select>
                        </td>
                        <td width="211">Paciente</td>
                        <td width="607">
                            <select name="paciente_dni" id="paciente_dni" data-mini="true" required>
                                <optgroup label="Lista de Pacientes">
                                    <option value="">SELECCIONAR</option>
                                </optgroup>
                            </select>
                        </td>
                    </tr>

                    <script>
                        $('#tip_retiro').change(function(){

                            console.log('hola')
                            if(!$(this).val()){
                                return false;
                            }

                            if($(this).val() == 1){
                                $('#texto').text('Nº Ovulos retirados')
                                action = 'listarPacienteOvulo'
                            }else if(($(this).val() == 2)){
                                action = 'listarPacienteEmbriones'
                                $('#texto').text('Nº Embriones retirados')
                            }


                            $.ajax({
                                type: "POST",
                                url: "_database/db_tools.php",
                                dataType: "json",
                                data: {
                                    action: action,
                                },
                                success: function (data) {
                                    var select = $("#paciente_dni");
                                    select.empty();
                                    
                                    select.append('<option value="" selected>SELECCIONAR PACIENTE</option>');

                                    $.each(data, function (index, sede) {
                                        console.log(sede)
                                        select.append('<option value="' + sede.dni + '">'+ sede.nombre +'</option>');
                                    });

                                    $('#sede_contabilidad_id').trigger("chosen:updated");
                                },
                                error: function(jqXHR, exception) {
                                    console.log(jqXHR, exception);
                                    console.log('Error: '+exception);
                                },
                            });
                        });
                    </script>
                    <tr>
                        <td width="211">Nº Retiro</td>
                        <td width="607">
                            <input name="retiro_num" type="text" id="retiro_num" data-mini="true">
                        </td>
                        <td width="211">Fecha de retiro</td>
                        <td width="607">
                            <input name="fec" type="date" id="fec" data-mini="true">
                        </td>
                    </tr>
                    <tr>
                        <td width="211">Documento</td>
                        <td width="607">
                            <input name="documento" id="documento" type="file" accept="application/pdf" data-mini="true" />
                        </td>
                        <td id="texto">Nº Embriones retirados</td>
                        <td>
                            <input name="emb" type="number" min="0" id="emb" data-mini="true" value="" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td>Médico</td>
                        <td>
                            <select name="med" id="med" data-mini="true" required>
                                <optgroup label="Lista de Medicos">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    $medicos = listarMedicos();
                                    foreach ($medicos as $medico) {
                                        $selected = '';
                                        if ($medico['codigo'] == $login) {
                                            $selected = 'selected';
                                        }
                                        echo '<option value="' . $medico['codigo'] . '"' . $selected . '>' . strtoupper($medico['nombre']) . '</option>';
                                    }
                                    ?>
                                </optgroup>
                            </select>
                        </td>
                        <td>Embriologo que Ejecuta el retiro</td>
                        <td>
                            <select name="embriologo" id="embriologo" data-mini="true" required>
                                <optgroup label="Lista de Embriologos">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    $embriologos = listarEmbriologos();
                                    foreach ($embriologos as $embriologo) {
                                        $selected = '';
                                        if ($embriologo['id'] == $login) {
                                            $selected = 'selected';
                                        }
                                        echo '<option value="' . $embriologo['id'] . '"' . $selected . '>' . strtoupper($embriologo['nom']) . '</option>';
                                    }
                                    ?>
                                </optgroup>
                            </select>
                        </td>

                    </tr>
                </table>


                <div class="lista_des scroll_h">&nbsp;</div>

                <div class="button-container">
                    <input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Guardando datos.." data-theme="b" data-inline="true" />
                    <a href="lista_pro_x.php" rel="external" class="ui-btn ui-btn-inline ui-mini">CANCELAR</a>
                </div>

            </form>

        </div>
    </div>


    <script>

        
        function embrionesRetirados(){
            embrioCheck = $('.lista_des input[type="checkbox"]:checked').length;
            $('#emb').val(embrioCheck)
        }
        $(document).ready(function() {

            
            function validacionCampo(icon,titulo,text){
                const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                        });
    
                        Toast.fire({
                            icon: icon,
                            title: titulo,
                            text: text
                        });
            }
            
            $('#form2').submit(function(event) {
                embrioCheck = $('.lista_des input[type="checkbox"]:checked').length;
                totalCheckboxes = $('.lista_des input[type="checkbox"]').length;

                if (!$('#paciente_dni').val()) {
                    validacionCampo("warning","Hay un error","Seleccione el Paciente.")
                    return false;
                }

                if (!$('#retiro_num').val()) {
                    validacionCampo("warning","Hay un error","Ingrese el protocolo.")
                    return false;
                }

                if (!$('#fec').val()) {
                    validacionCampo("warning","Hay un error","Seleccione la fecha.")
                    return false;
                }

                if (!$('#documento').val()) {
                    validacionCampo("warning","Hay un error","Seleccione un archivo")
                    return false;
                }

                if (!$('#med').val()) {
                    validacionCampo("warning","Hay un error","Seleccione el Medico")
                    return false;
                }

                if (!$('#embriologo').val()) {
                    validacionCampo("warning","Hay un error","Seleccione el Embriologo")
                    return false;
                }

                if (embrioCheck === 0) {
                    validacionCampo("warning","Hay un error","Seleccione almenos un embrion a retirar")
                    return false;
                } 

                event.preventDefault();

                Swal.fire({
                    title: "Confirmación",
                    text: "Esta seguro de registrar el retiro",
                    iconHtml: `<style> div.swal2-icon { border-color: #ffffff00; } </style>
                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                </svg>`,
                    showCancelButton: true,
                    showCloseButton: true, 
                    confirmButtonText: '<i class="fas fa-check"></i> Sí',
                    cancelButtonText: '<i class="fas fa-times"></i> No',
                    customClass: {
                        htmlContainer: "custom-swal2-html-container",
                        actions: "confirm_swal2-actions",
                        title: "confirm_swal2-title",
                        popup: "swal-popup-cap swal2-toast", 
                        closeButton: "swal2-close",
                        confirmButton: "confirm_swal2-confirm",
                        cancelButton: "confirm_swal2-cancel",
                    },
                    backdrop: true,
                    width: "23%", 
                }).then((result) => {
                    if (result.isConfirmed) {

                Swal.fire({
                    title: 'Guardando datos...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData(this);
                formData.append('accion', 'retiroEmbrionnes');
                formData.append('embrioRestant', totalCheckboxes-embrioCheck);
                setTimeout(function() {
                    $.ajax({
                        type: 'POST',
                        url: $('#form2').attr('action'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function(response) {

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
                                    text: response.message
                                });
                                return false;
                            }
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

                            $('#form2')[0].reset();
                            $('.lista_des').html('');

                            Toast.fire({
                                icon: 'success',
                                title: 'Retiro guardado exitosamente'
                            });


                            setTimeout(function() {
                                window.location.href = 'lista_pro_x.php';
                            }, 3000);

                        },
                        error: function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al guardar los datos...',
                                text: 'Hubo un problema al procesar la solicitud'
                            });
                        }
                    });
                }, 1000);

                return false;
            }
                });


            });
        });

        //LISTADO DE EMBRIONES
        $("#paciente_dni").change(function() {
            embrioCheck = $('.lista_des input[type="checkbox"]:checked').length;
            var h = $('#tip_retiro').val();
            var dni = $("#paciente_dni").val();
            $('.lista_des').html('<h3>CARGANDO DATOS...</h3>');

            if ($('#tip_retiro').val() == 1) {
                width_div = '30'
            }else{
                width_div = '100'

            }

            $.post("le_tanque.php", {
                h: h,
                dni: dni,
                paci: dni,
                btn_guarda_retiro: 1
            }, function(data) {
                $('.lista_des').html('');
                $(".lista_des").append('<div class="parent-container" style="display: flex; justify-content: center;"><div class="lista_des2" style="display: flex; flex-direction: column; width: '+width_div+'%;">' + data + '</div></div>');
                $('.ui-page').trigger('create');
            });
        });
    </script>
</body>

</html>