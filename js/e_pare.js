$(document).on("click", ".show-page-loading-msg", function () {
    if (document.getElementById("p_fnac").value == "") {
        alert("Debe llenar el campo de fecha de nacimiento.");
        return false;
    }

    if (document.getElementById("p_nom").value == "") {
        alert("Debe llenar el campo 'Nombre'");
        return false;
    }

    if (document.getElementById("p_ape").value == "") {
        alert("Debe llenar el campo 'Apellidos'");
        return false;
    }

    if ($('#p_m_inf').prop('checked')) {
        if (document.getElementById("p_m_inf1").value == "") {
            alert("Debe especificar la Infección");
            return false;
        }
    }

    if ($('#p_m_ale').prop('checked')) {

        if (document.getElementById("p_m_ale1").value == "") {
            alert("Debe especificar la alergia");
            return false;
        }
    }

    var $this = $(this),
        theme = $this.jqmData("theme") || $.mobile.loader.prototype.options.theme,
        msgText = $this.jqmData("msgtext") || $.mobile.loader.prototype.options.text,
        textVisible = $this.jqmData("textvisible") || $.mobile.loader.prototype.options.textVisible,
        textonly = !!$this.jqmData("textonly");
    html = $this.jqmData("html") || "";
    $.mobile.loading("show", {
        text: msgText,
        textVisible: textVisible,
        theme: theme,
        textonly: textonly,
        html: html
    });
}).on("click", ".hide-page-loading-msg", function () {
    $.mobile.loading("hide");
});

$(function () {
    $('#alerta').delay(3000).fadeOut('slow');
});

function anular(x, y, z) {
    document.form2.borrarX.value = x;
    document.form2.borrarY.value = y;
    document.form2.borrarTipo.value = z;
    document.form2.submit();
}

function borrar(x) {
    if (confirm("CONFIRMA BORRAR ESTA CONSULTA ?")) {
        document.form2.borrarUro.value = x;
        document.form2.submit();
        return true;
    } else return false;
}

function eliminar_consentimiento(id) {
    if (confirm("CONFIRMA BORRAR ESTA CONSULTA ?")) {
        document.form2.eliminar_consentimiento.value = id;
        document.form2.submit();
        return true;
    } else return false;
}