 <script>
    

    function validarDocumento(valDni,nom,apeP,apeM,fnac,dni,tipDoc,selectTipDoc,campDni,msgVal,valueDni,sistema,usuario){

        rutaApi = 'https://nd-be-eva-id6qsbxfsa-uc.a.run.app/api/persona/validarpersona'

        if (dni == '') {
            mostrarToast('error', 'Ingrese el numero de documento.');
            return false;
        }

        switch (tipDoc) {
                    case 1:
                        if (dni.length != 8) {
                            mostrarToast('error', 'El campo DNI debe tener 8 digitos.');
                            return false;
                        }
                        break;
                    case 4:
                        if (dni.length > 12) {
                            mostrarToast('error', 'El número de pasaporte debe tener máximo 12 digitos.');
                            return false;
                        }
                        break;
                    case 2:
                        if (dni.length > 12) {
                            console.log(dni.length)
                            mostrarToast('error', 'El número de carnet de extranjería debe tener máximo 12 digitos.');
                            return false;
                        }
                        break;
                    default:
                        break;
                }
        
        mostrarLoader('VALIDANDO','Buscando DNI')
        $.ajax({
            url: rutaApi,
            type: 'POST',
            data: {
                documento: dni,
                tipo_documento: tipDoc,
                sistema: 'TM('+sistema+')',
                usuario: usuario,
                },
            contentType: 'application/x-www-form-urlencoded', 
            headers: {
                    'Authorization': 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZFVzZXIiOjEsInJvbFVzZXIiOjEsIm5hbWVVc2VyIjoiRU5ZRVJCRSBCQVJSSU9TIiwiZW1wcmVzYSI6MSwibmFtZUVtcHJlc2EiOiJGRVJUSUxJREFEIEVWQSBTLkEuQy4iLCJpYXQiOjE3MjM1NjY4MjN9.ICxsXAdf_gQeEWj3Sy0kIrA4me_qy-pIpvb1kD6qlzA'  
                },
            success: function(response) {
                if (response.success) {
                    
                    ocultarLoader()
                    console.log('Respuesta del servidor:', response);
                    mostrarToast('success', 'El documento, se valido correctamente.');
    
                    nombre = response.nombres
                    apellidosP = response.apellidoPaterno
                    apellidosM = response.apellidoMaterno
                    let valFechNac = response.fecha_nacimiento
                    llenarCampos(valDni,nom,apeP,apeM,fnac,nombre,apellidosP,apellidosM,valFechNac,selectTipDoc,campDni,msgVal,valueDni)

                }else{
                    noEncontrado(nom,apeP,apeM,msgVal,campDni,valueDni);
                }
            },
            error: function(xhr, status, error) {

                noEncontrado(nom,apeP,apeM,msgVal,campDni,valueDni);
                ocultarLoader(); 

            }
        });
        
    }

    function noEncontrado(nombre,apellidoP,apellidoM,msgVal,campDni){
        const newSvgContent2 = `
                    <td style="text-align: right; width: 50%;color:#c63737" id="`+ msgVal +`" colspan="2">**<strong>NO</strong> VALIDADO CON <strong>RENIEC</strong></td>
                    `;
                    
        const newSvgContent = `
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" id="`+ campDni +`" title="Quitar validacion" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;">
                <path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z" fill="red"></path>
            </svg>
        `;
        $('#'+campDni).html(newSvgContent)
        $('#'+msgVal).html(newSvgContent2)
        $('#'+valDni).prop('readonly', true)
        $('#'+nombre).prop('readonly', false)
        $('#'+apellidoP).prop('readonly', false)
        $('#'+apellidoM).prop('readonly', false)
        $('#'+valueDni).val('3')

        mostrarToast('error', 'No se encontraron resultados');
    }
    
    function llenarCampos(valDni,nombre,apellidoP,apellidoM,fNac,valNom,valApeP,valApeM,valFechNac,selectTipDoc,campDni,msgVal,valueDni){
        $('#'+nombre).val(valNom)
        $('#'+apellidoP).val(valApeP)
        $('#'+apellidoM).val(valApeM)
        $('#'+fNac).val(valFechNac)
        $('#'+valDni).prop('readonly', true)
        $('#'+nombre).prop('readonly', true)
        $('#'+apellidoP).prop('readonly', true)
        $('#'+apellidoM).prop('readonly', true)
        $('#'+fNac).prop('readonly', true)
        $('#'+selectTipDoc).css('pointer-events', 'none')

        const newSvgContent = `
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" id="`+ campDni +`" title="Quitar validacion" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;">
                <path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z" fill="red"></path>
            </svg>
        `;

        $('#'+campDni).html(newSvgContent)

        const newSvgContent2 = `
                <td style="text-align: right; width: 50%;color:#256029" id="`+ msgVal +`" colspan="2">**VALIDADOS CON <strong>RENIEC</strong></td>
            `;

        $('#'+msgVal).html(newSvgContent2)

        $('#'+valueDni).val('2')


    }

    function habilitarCampos(valDni,nombre,apellidoP,apellidoM,fNac,selectTipDoc,campDni,msgVal,valueDni){
        $('#'+valDni).prop('readonly', false)
        $('#'+nombre).prop('readonly', true)
        $('#'+nombre).val("")
        $('#'+apellidoP).prop('readonly', true)
        $('#'+apellidoP).val("")
        $('#'+apellidoM).prop('readonly', true)
        $('#'+apellidoM).val("")
        $('#'+fNac).prop('readonly', false)
        $('#'+fNac).val("")
        $('#'+selectTipDoc).css('pointer-events', 'auto')

        const newSvgContent = `
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" id="`+ campDni +`" viewBox="0 0 24 24" style="margin-right: 5px; cursor: pointer;fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z" fill="#44d51f"></path><path d="M11.412 8.586c.379.38.588.882.588 1.414h2a3.977 3.977 0 0 0-1.174-2.828c-1.514-1.512-4.139-1.512-5.652 0l1.412 1.416c.76-.758 2.07-.756 2.826-.002z" fill="#44d51f"></path></svg>
        `;

        $('#'+campDni).html(newSvgContent)

        const newSvgContent2 = `
                <td style="text-align: right; width: 50%;color:#c63737" id="`+ msgVal +`" colspan="2">**<strong>NO</strong> VALIDADO CON <strong>RENIEC</strong></td>
            `;

        $('#'+msgVal).html(newSvgContent2)

        $('#'+valueDni).val('1')

        mostrarToast('error', 'Se quito la validacion por RENIEC');

    }

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
    }

    function ocultarLoader() {
        mostrar.close();
        Swal.hideLoading();
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
</script>
