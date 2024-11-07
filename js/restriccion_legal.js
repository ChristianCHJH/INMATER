$(document).on("click", "#legal_aceptar", function () {
    $("#legal_confirmacion").val("1")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#legal_confirmar').submit()
    }
});

$(document).on("click", "#legal_descartar", function () {
    $("#legal_confirmacion").val("0")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#legal_confirmar').submit()
    }
});