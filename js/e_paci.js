$(".btn_consulta_resultado").on("click", function () {
    // traer serie, correlativo
    $.ajax({
        type: 'POST',
        url: '_operaciones/e_paci.php',
        data: { orden: $(this).attr("data-orden") },
        success: function (result) {
            var data = jQuery.parseJSON(result);
            $("#popup_resultados").html(data.resultado);
            var content_width = $.mobile.activePage.find("div[data-role='content']:visible:visible").outerWidth();
            console.log(content_width)
            $('#popup_resultados').css({ 'width': content_width * 0.8 });
        }
    });

    /* $("#ver_documento_credito").modal('show'); */
});

$("#enviar_acceso").on("click", function (e) {
    window.onbeforeunload = null;
    if (!isValidEmailAddress($("#correo_acceso").val())) {
        alert('email no es correo.')
        return
    }

    $("#send").html('<a class="btn-enviar"><i style="color: #000;" class="fa-1x fas fa-spinner fa-spin"></i></a>');
    $('#enviar_acceso').prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: '_operaciones/e_paci_1.php',
        async: false,
        data: {
            tipo: "enviar_correo_acceso_paciente",
            email: $("#correo_acceso").val(),
            dni: $('[name="dni"]').val(),
            login: $('[name="login"]').val()
        },
        success: function (result) {
            $("#send").html('<span style="color: green; font-size: 12px;">Enviado!</span>');
        }
    });
});

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}