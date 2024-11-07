$(document).on("click", "#analisis_aceptar", function () {
    $("#analisis_confirmacion").val("1")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#analisis_confirmar').submit()
    }
});

$(document).on("click", "#analisis_descartar", function () {
    $("#analisis_confirmacion").val("0")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#analisis_confirmar').submit()
    }
});