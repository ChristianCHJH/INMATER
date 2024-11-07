$(document).ready(function() {
    const searchCache = new Map();
    function getUserId() {
        return $('#login').val() || 'unknown_user';
    }

    function getFormName() {
        return $('#form-name').val() || 'unknown_form';
    }

    // Función para actualizar el objeto de conteo en el localStorage y en el campo oculto
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
    //envio de formulario
    $('#formapi').on('submit', function(event) {
        event.preventDefault(); // Evita el envío del formulario de manera predeterminada

        const formData = new FormData(this); 
        $.ajax({
            url: 'ajax/paciente/registro_paciente.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {  
                if (response.status) {
                    mostrarToast('info', response.message);
                    resetQueryCounts(); // Resetear los contadores después de guardar el formulario
                }else{
                    mostrarToast('error', response.message);
                }
                
                $(".show-page-loading-msg").hide();
                return true;
            },
            error: function(xhr) { 
                $(".show-page-loading-msg").hide();
                alert('Error al guardar el formulario: ' + xhr.responseText);
            }
        });
        return false;
    });

    $('#validate-icon').on('click', function() {
        const nacionalidad = $('#nac');
        const depart = $('#depa');
        const prov = $('#prov');
        const dist = $('#dist');
        const tip = $('#tip');
        const dni = $('#dni');
        const nombre = $('#nom');
        const apellido = $('#ape');
        const fnac = $('#fnac');
        const telefono = $('#tcel');
        const direccion = $('#dir');
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

            // Incrementar el contador de consultas desde la caché
            updateQueryCounts(tipo, numero, 'cache', true);

            mostrarToast('info', 'Cargando datos desde la caché');
            clearForm();
            fillForm(data);
            return;
        }

        // Incrementar el contador de consultas AJAX
        updateQueryCounts(tipo, 'ajax');
        mostrarLoader('Espere por favor', 'Cargando datos');

        $.ajax({
            url: '/sunat/document-type/' + tipo + '/' + numero,
            type: 'GET',
            success: function(response) {
                ocultarLoader();

                if (response.status) {
                    const body = response.data.body;
                    if (body.errors) {
                        updateQueryCounts(tipo, 'ajax', false);
                        const errorPattern = /apikey:[a-zA-Z0-9]+\ }/;
                        if (errorPattern.test(body.errors[0].message)) {
                            mostrarToast('error', 'Ha ocurrido un error con los datos ingresados. Por favor, verifica e intenta nuevamente.');
                        } else {
                            mostrarToast('error', body.errors[0].message);
                        }
                    } else {
                        updateQueryCounts(tipo, 'ajax', true);
                        mostrarToast('info', 'Datos cargados correctamente');
                        localStorage.setItem(cacheKey, JSON.stringify(body));
                        clearForm();
                        fillForm(body);
                    }
                } else {
                    updateQueryCounts(tipo, 'ajax', false);
                    mostrarToast('error', 'Error en la validación. Inténtelo más tarde ');
                    //alert('Error en la validación: ' + response.message);
                }
            },
            error: function(xhr) {
                ocultarLoader();
                updateQueryCounts(tipo, 'ajax', false);
                mostrarToast('error', 'Error en la validación. Inténtelo más tarde ');
                //alert('Error en la validación: ' + xhr.responseText);
            }
        });
    });

    
 

    function clearForm() {
        $('#nac').val('');
        $('#depa').val('');
        $('#prov').val('');
        $('#dist').val('');
        $('#nom').val('');
        $('#ape').val('');
        $('#fnac').val('');
        $('#tcel').val('');
        $('#dir').val('');
    }

    function fillForm(data) {
        const nacionalidad = $('#nac');
        const depart = $('#depa');
        const prov = $('#prov');
        const dist = $('#dist');
        const tip = $('#tip');
        const dni = $('#dni');
        const nombre = $('#nom');
        const apellido = $('#ape');
        const fnac = $('#fnac');
        const telefono = $('#tcel');
        const direccion = $('#dir');
        const tipo = tip.val();

        switch (tipo) {
            case 'DNI':
                nacionalidad.val('PE');
                nacionalidad.click();
                nombre.val(data.preNombres);
                apellido.val(data.apePaterno + ' ' + data.apeMaterno);
                fnac.val(convertDateToISO(data.feNacimiento));
                direccion.val(data.desDireccion);
                prov.attr('op-text', data.ubigeo.provincia);
                dist.attr('op-text', data.ubigeo.distrito);
                loadDepart('PE', data.ubigeo.departamento);
                break;
            case 'PAS':
                data = (data.data && data.data[0] !== undefined) ? data.data[0] : data.data;
                nacionalidad.val('PE');
                nacionalidad.click();
                nombre.val(data.nombres);
                apellido.val(data.apellido_paterno + ' ' + data.apellido_materno);
                fnac.val(convertDateToISO(data.fecha_nacimiento));
                break;
            case 'CEX':
                data = (data && data[0] !== undefined) ? data[0] : data;
                nacionalidad.val('PE');
                nacionalidad.click();
                nombre.val(data.nombres);
                apellido.val(data.apellido_paterno + ' ' + data.apellido_materno);
                fnac.val(convertDateToISO(data.fecha_nacimiento));
                break;
        }
    }

    function loadDepart(country, dpto = null) {
        $.ajax({
            url: '../ubigeo/departments/' + country,
            method: 'GET',
            success: function(res) {
                $.each(res, function(index, department) {
                    var option = $('<option></option>')
                        .attr('value', department.id)
                        .text(department.name);
                    $("#depa").append(option);
                });
                if (dpto != null) {
                    selectOptionByText($("#depa"), dpto);
                }
                $("#depa").selectmenu("refresh");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
            }
        });
    }

    function loadProv(depa, prov = null) {
        $.ajax({
            url: '../ubigeo/provinces/' + depa,
            method: 'GET',
            success: function(res) {
                $.each(res, function(index, province) {
                    var option = $('<option></option>')
                        .attr('value', province.id)
                        .text(province.name);
                    $("#prov").append(option);
                });
                console.log(prov);
                selectOptionByText($("#prov"), prov);
                $("#prov").selectmenu("refresh");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
            }
        });
    }

    function loadDistrict(prov, dist = null) {
        $.ajax({
            url: '../ubigeo/districts/' + prov,
            method: 'GET',
            success: function(res) {
                $.each(res, function(index, district) {
                    var option = $('<option></option>')
                        .attr('value', district.id)
                        .text(district.name);
                    $("#dist").append(option);
                });
                selectOptionByText($("#dist"), dist);
                $("#dist").selectmenu("refresh");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
            }
        });
    }
});
