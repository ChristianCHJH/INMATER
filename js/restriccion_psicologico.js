$(document).on("click", "#psicologico_aceptar", function () {
    $("#psicologico_confirmacion").val("1")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#psicologico_confirmar').submit()
    }
});

$(document).on("click", "#psicologico_descartar", function () {
    $("#psicologico_confirmacion").val("0")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#psicologico_confirmar').submit()
    }
});