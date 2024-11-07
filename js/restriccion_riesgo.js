$(document).on("click", "#riesgo_aceptar", function () {
    $("#riesgo_confirmacion").val("1")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#riesgo_confirmar').submit()
    }
});

$(document).on("click", "#riesgo_descartar", function () {
    $("#riesgo_confirmacion").val("0")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#riesgo_confirmar').submit()
    }
});