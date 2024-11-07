$(document).ready(function() {
    const searchCache = new Map();

    $('#validate-icon').on('click', function() { 
        const tip = $('#p_tip');
        const dni = $('#p_dni');
        const nombre = $('#p_nom');
        const apellido = $('#p_ape');
        const fnac = $('#p_fnac');
        const telefono = $('#p_tcel');
        const direccion = $('#p_dir');
        tipo = tip.val();
        numero = dni.val();

        if (numero.trim() == '') {
            mostrarToast('error', 'Escribe el número de documento');
            return false;
        }

        // Validación de DNI
        if (tipo === 'DNI' && !/^\d{8}$/.test(numero)) {
            mostrarToast('error', 'El DNI debe tener 8 dígitos');
            return false;
        }

        // Validación de Pasaporte (Ejemplo: longitud entre 6 y 9 caracteres alfanuméricos)
        if (tipo === 'PAS' && !/^[a-zA-Z0-9]{6,9}$/.test(numero)) {
            mostrarToast('error', 'El pasaporte debe tener entre 6 y 9 caracteres alfanuméricos');
            return false;
        }

        // Validación de Carnet de Extranjería (Ejemplo: longitud exacta de 12 dígitos)
        if (tipo === 'CEX' && !/^\d{12}$/.test(numero)) {
            mostrarToast('error', 'El carnet de extranjería debe tener 12 dígitos');
            return false;
        }

        const cacheKey = tipo + '_' + numero;
        const cachedData = localStorage.getItem(cacheKey);

        if (cachedData) {
            const data = JSON.parse(cachedData);
            mostrarToast('info', 'Cargando datos desde la cache');
            clearForm();
            fillForm(data);
            return;
        }

        mostrarLoader('Espere por favor', 'Cargando datos');

        $.ajax({
            url: '/sunat/document-type/' + tipo + '/' + numero,
            type: 'GET',
            success: function(response) {
                ocultarLoader();

                if (response.status) {
                    const body = response.data.body;
                    if (body.errors) {
                        const errorPattern = /apikey:[a-zA-Z0-9]+\ }/;
                        if (errorPattern.test(body.errors[0].message)) {
                            mostrarToast('error', 'Ha ocurrido un error con los datos ingresados. Por favor, verifica e intenta nuevamente.');
                        } else {
                            mostrarToast('error', body.errors[0].message);
                        }
                    } else {
                        mostrarToast('info', 'Datos cargados correctamente');
                        localStorage.setItem(cacheKey, JSON.stringify(body));
                        clearForm();
                        fillForm(body);
                    }
                } else {
                    alert('Error en la validación: ' + response.message);
                }
            },
            error: function(xhr) {
                ocultarLoader();
                alert('Error en la validación: ' + xhr.responseText);
            }
        });
    });

    function clearForm() { 
        $('#p_nom').val('');
        $('#p_ape').val('');
        $('#p_fnac').val('');
        $('#p_tcel').val('');
        $('#p_dir').val('');
    }

    function fillForm(data) { 
        const tip = $('#p_tip');
        const dni = $('#p_dni');
        const nombre = $('#p_nom');
        const apellido = $('#p_ape');
        const fnac = $('#p_fnac');
        const telefono = $('#p_tcel');
        const direccion = $('#p_dir');
        const tipo = tip.val();

        switch (tipo) {
            case 'DNI': 
                nombre.val(data.preNombres);
                apellido.val(data.apePaterno + ' ' + data.apeMaterno);
                fnac.val(convertDateToISO(data.feNacimiento));
                direccion.val(data.desDireccion); 
                break;
            case 'PAS':
                data = (data.data && data.data[0] !== undefined) ? data.data[0] : data.data; 
                nombre.val(data.nombres);
                apellido.val(data.apellido_paterno + ' ' + data.apellido_materno);
                fnac.val(convertDateToISO(data.fecha_nacimiento));
                break;
            case 'CEX':
                data = (data && data[0] !== undefined) ? data[0] : data; 
                nombre.val(data.nombres);
                apellido.val(data.apellido_paterno + ' ' + data.apellido_materno);
                fnac.val(convertDateToISO(data.fecha_nacimiento));
                break;
        }
    }
 
});
