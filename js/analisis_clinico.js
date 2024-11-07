var mostrar; // Variable global para mantener la referencia al popup de Swal

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
function convertToYYYYMMDD(dateString) {
    // Dividir el string de fecha para obtener solo la parte de la fecha
    const datePart = dateString.split(' ')[0];
    
    // Crear un objeto Date a partir del string de la fecha
    const date = new Date(datePart);

    // Obtener los componentes de la fecha
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Los meses en JavaScript son 0-11
    const year = date.getFullYear();

    // Formatear la fecha en yyyy-MM-dd
    return `${year}-${month}-${day}`;
}
function loadClinicalExam() { 
    id = getParameterByName('id');
    if (id.trim() != '') {
        mostrarLoader(mensaje = 'Espere por favor', type = 'Cargando datos...');
        $.ajax({
            url: '../../ajax/examen_clinico/man_examen_ajax_load.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json', // Especifica que esperas una respuesta JSON
            success: function (res) {
                ocultarLoader(); // Ocultar el loader después de la operación
                fecha = convertToYYYYMMDD(res.data.fecha);
                console.log(fecha);
                $('#fecha').val(fecha);
                $('#tipo_examen_id').val(res.data.tipo_examen_id).change(); //21
                $('#resultado_id').val(res.data.resultado_id).change(); //1
                $('#observacion').val(res.data.observacion);
                //console.log(res);
            },
            error: function (xhr, status, error) {
                ocultarLoader(); // Ocultar el loader en caso de error 
                // Mostrar mensaje de error con Toast de SweetAlert2
                mostrarToast('error', 'Hubo un problema al intentar cargar los datos');
            }
        });
    } 
}
/**
 * Función para confirmar y eliminar un registro.
 * @param {Element} THIS - Elemento que desencadenó la acción.
 * @param {string} op - Opción adicional para identificar el tipo de operación.
 */
function deleteClinicalExam(THIS, op = null) {
    var id = $(THIS).attr("id-attr");

    // Mostrar confirmación con SweetAlert2
    Swal.fire({
        title: 'Confirmación',
        text: 'Esta seguro que desea eliminar?', 
        iconHtml: ` 
                <svg class="custom-warning-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
<circle cx="12" cy="17" r="1" fill="#000000"/>
<path d="M12 10L12 14" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M3.44722 18.1056L10.2111 4.57771C10.9482 3.10361 13.0518 3.10362 13.7889 4.57771L20.5528 18.1056C21.2177 19.4354 20.2507 21 18.7639 21H5.23607C3.7493 21 2.78231 19.4354 3.44722 18.1056Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
            `,
        showCancelButton: true,
        showCloseButton: true, // Mostrar el botón de cerrar (X)
        confirmButtonText: '<i class="fas fa-check"></i> Sí',
        cancelButtonText: '<i class="fas fa-times"></i> No',
        customClass: {
            htmlContainer: 'custom-swal2-html-container',
            actions: 'confirm_swal2-actions',
            title: 'confirm_swal2-title',
            popup: 'swal-popup-cap swal2-toast', // Personalización de la clase del popup si es necesario, 
            closeButton: 'swal2-close', // Clase personalizada para el botón de cerrar 
            confirmButton: 'confirm_swal2-confirm', // Clase personalizada para el botón de confirmar
            cancelButton: 'confirm_swal2-cancel' // Clase personalizada para el botón de cancelar
        },
        backdrop: true, // Permite clic en el fondo para cerrar el popup
        width: '23%', // Ancho personalizado del popup
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarRegistro('Eliminando...Espere por favor'); // Llamar a la función para eliminar el registro

            // Realizar la solicitud AJAX para eliminar el registro
            $.ajax({
                url: '../../ajax/examen_clinico/eliminar_examen.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json', // Especifica que esperas una respuesta JSON
                success: function (response) {
                    ocultarLoader(); // Ocultar el loader después de la operación

                    // Eliminar visualmente el elemento según la opción especificada
                    if (op === "invitro") {
                        $(THIS).parent().remove();
                    } else {
                        $(THIS).parent().parent().remove();
                    }

                    // Mostrar mensaje de éxito con Toast de SweetAlert2
                    mostrarToast('success', 'El registro se eliminó correctamente');
                },
                error: function (xhr, status, error) {
                    ocultarLoader(); // Ocultar el loader en caso de error

                    // Mostrar mensaje de error con Toast de SweetAlert2
                    mostrarToast('error', 'Hubo un problema al intentar eliminar el registro');
                }
            });
        }
    });
}

/**
 * Función para mostrar un loader con SweetAlert2.
 * @param {string} mensaje - Mensaje a mostrar dentro del popup.
 * @param {string} type - Título del popup.
 */
function mostrarLoader(mensaje = 'Espere por favor', type = 'Buscando...') {
    mostrar = Swal.fire({
        title: type,
        text: mensaje,
        allowEscapeKey: false,
        allowOutsideClick: false,
        showConfirmButton: false, 
        didOpen: () => {
            Swal.showLoading();
        }
    });
    Swal.showLoading();
    console.log("loading");
}

/**
 * Función para ocultar el loader de SweetAlert2.
 */
function ocultarLoader() {
    mostrar.close(); // Cerrar el popup de Swal manteniendo solo el ícono de carga
    Swal.hideLoading(); // Ocultar el ícono de carga
}

/**
 * Función para simular la eliminación de un registro.
 * Esta función solo simula un retraso antes de llamar a ocultarLoader().
 */
function eliminarRegistro(mensaje = '',type = '') {
    mostrarLoader(mensaje,type); 
    setTimeout(function () {
        ocultarLoader(); 
    }, 3000);
}

/**
 * Función para mostrar un Toast de SweetAlert2.
 * @param {string} icon - Icono a mostrar ('success', 'error', etc.).
 * @param {string} title - Título del mensaje del Toast.
 */
function mostrarToast(icon, title) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    Toast.fire({
        icon: icon,
        title: title
    });
}
