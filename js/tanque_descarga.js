$(document).ready(function () {
    $("#guardar").on("click", function () {
        $("#modal_editar").modal('show')
    });

    $.ajax({
        type: 'POST',
        url: '_operaciones/tanque_descarga.php',
        async: false,
        data: {
            tipo: "info_detalle_tanque"
        },
        dataType: "JSON",
        success: function (result) {
            $("#detalle_tanque tbody").after(result.message)
        },
        error: function (jqXHR, exception) {
            var msg = '';
            console.log(jqXHR)
            console.log(exception)

            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            /* $('#post').html(msg); */
        },
    });

    $('.caracteristica_tanque').keyup(function (e) {
        if (e.keyCode == 13) {
            var data = $('#form_tanque').serializeArray();
            $(".nuevo_posicion").remove()

            $.ajax({
                type: 'POST',
                url: '_operaciones/tanque_descarga.php',
                async: false,
                data: {
                    tipo: "info_caracteristica_tanque",
                    data: data,
                    seleccionados: JSON.stringify(seleccionados)
                },
                dataType: "JSON",
                success: function (result) {
                    $("#buscar_tanque").after(result.message)
                },
                error: function (jqXHR, exception) {
                    var msg = '';
                    console.log(jqXHR)
                    console.log(exception)

                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                },
            });
        }
    });
});

var seleccionados = [];

$(document).on('click', '.seleccionar_posicion', function (e) {
    var tanque = $(this).attr("data-tanque")
    var canister = $(this).attr("data-canister")
    var varilla = $(this).attr("data-varilla")
    var vial = $(this).attr("data-vial")
    var dni = $(this).attr("data-dni")

    var seleccionado_id = tanque + '-' + canister + '-' + varilla + '-' + vial

    var posicion = {};
    posicion['tanque'] = tanque
    posicion['canister'] = canister
    posicion['varilla'] = varilla
    posicion['vial'] = vial
    posicion['dni'] = dni

    if ($(this).is(":checked")) {
        seleccionados.push(posicion)
        var clone = $(this).clone()
        $('#items_seleccionados > tbody:last-child').append('<tr id="' + seleccionado_id + '"><td>' + seleccionado_id + '</td><td>' + dni + '</td><td>' + $(this).attr("data-apellidos-nombres")  + '</td><td></td></tr>')
        $('#items_seleccionados > tbody > tr:last-child > td:last-child').hide()
        $('#items_seleccionados > tbody > tr:last-child > td:last-child').append(clone.get(0))
    } else {
        $('#' + seleccionado_id).remove()
        var data = $.grep(seleccionados, function (e) {
            return e.tanque != posicion['tanque'] || e.canister != posicion['canister'] || e.varilla != posicion['varilla'] || e.vial != posicion['vial'];
        });

        seleccionados = data.slice();
    }

    if (seleccionados.length == 0) {
        $("#descargar_tanque").hide()
    } else {
        $("#descargar_tanque").show()
    }
});