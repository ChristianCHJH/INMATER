$(document).on("click", "#cariotipo_aceptar", function () {
    $("#cariotipo_confirmacion").val("1")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#cariotipo_confirmar').submit()
    }
});

$(document).on("click", "#cariotipo_descartar", function () {
    $("#cariotipo_confirmacion").val("0")
    if (confirm("¿Está seguro que desea continuar?")) {
        $('#cariotipo_confirmar').submit()
    }
});