    function previewImage(input) {
        var preview = document.getElementById('preview');
       if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.setAttribute('src', 'placeholder.png');
        }
    } 
   
    function getUserId() {
        return $('#login').val() || 'unknown_user';
    }

    function getFormName() {
        return $('#form-name').val() || 'unknown_form';
    }
    function clearForm() { 
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

    $(document).ready(function() {
        //envio de formulario
        $('#formapi').on('submit', function(event) {
            mostrarLoader('Espere por favor', 'Guardando datos');
            //event.preventDefault(); // Evita el envío del formulario de manera predeterminada
            var form = document.getElementById('formapi');
            const formData = new FormData(this);  

            $.ajax({
                url: '../paciente/guardar',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {  
                    if (response.status) {
                        mostrarToast('info', response.message);
                        resetQueryCounts(); // Resetear los contadores después de guardar el formulario
                        form[0].reset();
                    }else{ 
                        showErrors(form, response.html,true,4);
                        mostrarToast('error', response.message);
                    }

                    ocultarLoader();
                    return true;
                },
                error: function(xhr) { 
                    mostrarToast('error', 'Error al guardar el formulario, por favor inténtelo más tarde'); 
                    //alert('Error al guardar el formulario: ' + xhr.responseText);
                    ocultarLoader();
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

         

        $('.numeros').keyup(function() {
            var $th = $(this);

            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        });

        $('.alfanumerico').keyup(function() {
            var $th = $(this);

            $th.val($th.val().replace(/[^a-zA-Z0-9]/g, function(str) {
                return '';
            }));
        });
 
        $("#nac").change(function() { 
            console.log('load country');
            $("#nac option:selected").each(function() {
                var country = $(this).val(); 
                if (country) {
                    $("#depa").empty(); 
                    $("#depa").append('<option value="">Seleccionar</option>'); 
                    loadDepart(country);
                } 
            });
        });
        $("#depa").change(function() {
            $("#depa option:selected").each(function() {
                var depa = $(this).val(); 
                if (depa) {
                    $("#prov").empty();
                    $("#prov").append('<option value="">Seleccionar</option>');
                    var op = $("#prov").attr("op-text") !== undefined ? $("#prov").attr("op-text") : null;
                    console.log('Data op-text:', $("#prov").attr("op-text")); // Depuración
                    loadProv(depa,op);
                }
            });
        });

        $("#prov").change(function() {
            $("#prov option:selected").each(function() {
                var prov = $(this).val();
                if (prov) {
                    $("#dist").empty(); 
                    $("#dist").append('<option value="">Seleccionar</option>');
                    op = $("#dist").attr("op-text") !== undefined ? $("#dist").attr("op-text") : null ;
                    loadDistrict(prov,op);
                } 
            });
        });

        $("#don").on('change', function() {
            $("#don option:selected").each(function() {
                let elegido = $(this).val();
                if (elegido) {
                    $("#medios_comunicacion_id").empty(); 
                    $("#medios_comunicacion_id").append('<option value="">Seleccionar</option>');
                    mostrarLoader()
                    $.ajax({
                        url: '/medios-comunicacion/'+elegido,
                        method: 'GET',
                        success: function(res) {
                            $.each(res, function(index, media) {
                                var option = $('<option></option>')
                                    .attr('value', media.id)  
                                    .text(media.name)
                                    .prop('disabled',media.disabled);
                                    $("#medios_comunicacion_id").append(option);
                            });
                            $("#medios_comunicacion_id").selectmenu("refresh");
                            ocultarLoader()
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('Error:', textStatus, errorThrown);
                            ocultarLoader();
                        }
                    });   
                } 
            });
        });
    });

    function loadDepart(country,dpto = null){
        if (country.trim() == '') {
            mostrarToast('error','Selecciona el pais');
            return false;
        }
        mostrarLoader('Cargando departamentos');
        $.ajax({
            url: '../ubigeo/departments/'+country,
            method: 'GET',
            success: function(res) {
                $.each(res, function(index, department) {
                    var option = $('<option></option>')
                        .attr('value', department.id)  
                        .text(department.name);  
                        $("#depa").append(option); 
                });
                if (dpto != null) {
                    selectOptionByText($("#depa"),dpto); 
                    //loadProv($("#depa").val(), $("#prov").val());
                }
                $("#depa").selectmenu("refresh"); 
                ocultarLoader();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
                ocultarLoader();
            }
        }); 
    }
    function loadProv(depa, prov = null){
        if (depa.trim() == '') {
            mostrarToast('error','Selecciona el departamento');
            return false;
        }
        mostrarLoader('Cargando provincias');
        $.ajax({
            url: '../ubigeo/provinces/'+depa,
            method: 'GET',
            success: function(res) {
                $.each(res, function(index, province) {
                    var option = $('<option></option>')
                        .attr('value', province.id)  
                        .text(province.name);  
                        $("#prov").append(option); 
                });
                console.log(prov);
                selectOptionByText($("#prov"),prov); 
                $("#prov").selectmenu("refresh"); 
                ocultarLoader();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
                ocultarLoader();
            }
        });  
    }
    function loadDistrict(prov, dist = null) {
        if (prov.trim() == '') {
            mostrarToast('error','Selecciona la provincia');
            return false;
        }
        mostrarLoader('Cargando distritos');
        $.ajax({
            url: '../ubigeo/districts/'+prov,
            method: 'GET',
            success: function(res) {
                $.each(res, function(index, district) {
                    var option = $('<option></option>')
                        .attr('value', district.id)  
                        .text(district.name);  
                        $("#dist").append(option);
                });
                selectOptionByText($("#dist"),dist); 
                $("#dist").selectmenu("refresh");
                ocultarLoader();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
                ocultarLoader();
            }
        }); 
    }
    function convertDateToISO(dateStr) {
        const [day, month, year] = dateStr.split('/');
        return `${year}-${month}-${day}`;
    }

    function selectOptionByText(selectElement, text) {
        selectElement.find('option').filter(function() {
            return $(this).text() === text;
        }).prop('selected', true).change();
    }