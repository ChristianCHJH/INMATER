var mostrarfx = null; // Variable global para mantener la referencia al popup de Swal

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

function mostrarLoader(mensaje = 'Espere por favor', type = 'Buscando...') {
    mostrarfx = Swal.fire({
        title: type,
        text: mensaje,
        allowEscapeKey: false,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    console.log("loading");
}

function ocultarLoader() {
    if (mostrarfx) {
        mostrarfx.close(); // Cerrar el popup de Swal manteniendo solo el ícono de carga
    }
    Swal.hideLoading(); // Ocultar el ícono de carga
}



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

function convertDateToISO(dateStr) {
    const [day, month, year] = dateStr.split('/');
    return `${year}-${month}-${day}`;
}

function selectOptionByText(selectElement, text) {
    const option = Array.from(selectElement.options).find(opt => opt.text === text);
    if (option) {
        option.selected = true;
        selectElement.dispatchEvent(new Event('change'));
    }
}

function updateQueryCounts(tipo, numero, source, success = true) {
    const userId = getUserId(); // Obtener el ID del usuario
    const formName = getFormName(); // Obtener el nombre del formulario
    const key = `queryCount_${userId}_${formName}_${tipo}_${numero}_${source}_${success ? 'success' : 'error'}`;

    // Obtener y actualizar el conteo en localStorage
    let count = parseInt(localStorage.getItem(key) || '0', 10);
    count++;
    localStorage.setItem(key, count);

    // Recuperar los contadores actuales y actualizar el objeto JSON
    let queryCounts = JSON.parse($('#query-counts').val() || '{}');

    // Inicializar la estructura de datos si no existe
    queryCounts[userId] = queryCounts[userId] || {};
    queryCounts[userId][formName] = queryCounts[userId][formName] || {};
    queryCounts[userId][formName][tipo] = queryCounts[userId][formName][tipo] || {};
    queryCounts[userId][formName][tipo][numero] = queryCounts[userId][formName][tipo][numero] || { ajax: { success: 0, error: 0 }, cache: { success: 0, error: 0 }, not_found: 0 };
    queryCounts[userId][formName][tipo][numero][source] = queryCounts[userId][formName][tipo][numero][source] || { success: 0, error: 0 };

    // Actualizar el conteo
    if (success) {
        queryCounts[userId][formName][tipo][numero][source]['success'] = count;
    } else {
        queryCounts[userId][formName][tipo][numero][source]['error'] = count;
        queryCounts[userId][formName][tipo][numero]['not_found']++;
    }

    // Guardar los datos actualizados en localStorage
    $('#query-counts').val(JSON.stringify(queryCounts));

    console.log(`Cantidad de consultas ${success ? 'exitosas' : 'con error'} para ${tipo} ${numero} desde ${source} en el formulario ${formName} para usuario ${userId}: ${count}`);
}

function resetQueryCounts() {
    const userId = getUserId();
    const formName = getFormName();
    // Borrar los contadores del localStorage que coincidan con el usuario y el formulario
    Object.keys(localStorage).forEach((key) => {
        if (key.startsWith(`queryCount_${userId}_${formName}_`)) {
            localStorage.removeItem(key);
        }
    });

    // Borrar el objeto de conteo en el campo oculto para el usuario y el formulario
    let queryCounts = JSON.parse($('#query-counts').val() || '{}');
    if (queryCounts[userId]) {
        if (queryCounts[userId][formName]) {
            delete queryCounts[userId][formName];
            if (Object.keys(queryCounts[userId]).length === 0) {
                delete queryCounts[userId];
            }
        }
    }
    $('#query-counts').val(JSON.stringify(queryCounts));
    console.log(`Contadores resetados para usuario ${userId} en el formulario ${formName}`);
}

function getUserId() {
    return $('#login').val() || 'unknown_user';
}

function getFormName() {
    return $('#form-name').val() || 'unknown_form';
}


(function() {
    function createTooltip(element) {
        const parent = element.parentNode;
        parent.classList.add('relative-container');

        if (!element.nextElementSibling || !element.nextElementSibling.classList.contains('tooltip')) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip feedback';
            parent.appendChild(tooltip);
        }
    }

    function showTooltip(element, message, duration) {
        const tooltip = element.nextElementSibling;
        if (tooltip && tooltip.classList.contains('tooltip')) {
            tooltip.textContent = message;
            tooltip.style.display = 'block';
            element.parentNode.classList.add('no-overflow'); // Add no-overflow class to parent
            if (duration > 0) {
                setTimeout(() => hideTooltip(element), duration * 1000); // Hide the tooltip after `duration` seconds
            }
        }
    }

    function hideTooltip(element) {
        const tooltip = element.nextElementSibling;
        if (tooltip && tooltip.classList.contains('tooltip')) {
            tooltip.style.display = 'none';
            element.parentNode.classList.remove('no-overflow'); // Remove no-overflow class from parent
        }
    }

    window.showErrors = function(form, errors, showTooltips = true, tooltipDuration = 0) {
        // Clear previous error messages and classes
        form.querySelectorAll('.border-red-500, .border-green-500').forEach(element => {
            element.classList.remove('border-red-500', 'border-green-500');
        });
        form.querySelectorAll('.text-red-500, .text-green-500').forEach(element => {
            element.classList.remove('text-red-500', 'text-green-500');
        });

        form.querySelectorAll('input, select, textarea, file').forEach(element => {
            createTooltip(element);
            if (!showTooltips) {
                hideTooltip(element);
            }
        });

        for (const [key, error] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                input.classList.add(error.class);
                const feedbackElement = input.nextElementSibling;
                if (feedbackElement && feedbackElement.classList.contains('tooltip')) {
                    feedbackElement.classList.add(error.classmsj);
                    feedbackElement.textContent = error.msj;
                }
                if (error.status === false && showTooltips) {
                    showTooltip(input, error.msj, tooltipDuration);
                } else {
                    hideTooltip(input);
                }
            }
        }
    };
})();