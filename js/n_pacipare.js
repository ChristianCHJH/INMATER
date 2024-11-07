$(document).on("click", ".show-page-loading-msg", function() {
    if (document.getElementById("paciente").value == "" ) {
        alert("El paciente es un campo obligatorio");
        return false;
    }
    if (document.getElementById("medico_id").value == "") {
        alert("El medico es un campo obligatorio");
        return false;
    }

    // validaciones para la paciente mujer 
    if (document.getElementById("paciente").value == 2 ) {
        dato=document.getElementById("medios_comunicacion_id_").value;
        if (document.getElementById("don2").value == "") {
            alert("El tipo de cliente es obligatorio.");
            return false;
        }
        if (document.getElementById("medios_comunicacion_id_").value == "") {
            alert("El programa por el que se entero de nosotros es un campo obligatorio");
            return false;
        }
        if (document.getElementById("sede_idP").value == "" ) {
            alert("La sede es un campo obligatorio");
            return false;
        }
        if (document.getElementById("p_nom").value == "") {
            alert("El nombre es un campo obligatorio.");
            return false;
        }
        if (document.getElementById("p_ape").value == "" ) {
            alert("Los apellidos son un campo obligatorio.");
            return false;
        }
        var tipo_documento = document.getElementById("p_tip").value;
        var numero_documento = document.getElementById("p_dni").value;
        if (numero_documento == "") {
            alert("El número de documento es obligatorio.");
            return false;
        } else {
            switch (tipo_documento) {
                case "DNI":
                    if (numero_documento.length != 8) {
                        alert("El campo DNI debe tener 8 digitos.");
                        return false;
                    }
                    break;
                case "PAS":
                    if (numero_documento.length > 12) {
                        alert("El número de pasaporte debe tener máximo 12 digitos.");
                        return false;
                    }
                    break;
                case "CEX":
                    if (numero_documento.length > 12) {
                        alert("El número de carnet de extranjería debe tener máximo 12 digitos.");
                        return false;
                    }
                    break;
                default:
                    break;
            }
        }
        if (document.getElementById("p_fnac").value == "") {
            alert("La Fecha de Nacimiento es un campo obligatorio.");
            return false;
        } else {
            var diff = new Date(new Date() - new Date(jQuery("#fnac").val()));
            anios = diff / 1000 / 60 / 60 / 24 / 365.25;
            if (anios > 115 || anios < 12) {
                alert("Verificar la edad del paciente, no puede ser menor a 12 años ni mayor a 115 años.");
                return false;
            }
        }
}
});