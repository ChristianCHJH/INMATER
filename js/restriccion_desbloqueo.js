$(document).ready(function () {
    $('#buscar_paciente').keyup(function (e) {
        if (e.which === 13) {
            $.ajax({
                type: 'POST',
                url: '_operaciones/restriccion_desbloqueo.php',
                async: false,
                data: {
                    tipo: "info_restricciones",
                    paciente: $("#buscar_paciente").val()
                },
                dataType: "JSON",
                success: function (result) {
                    $("#buscarpaciente_tabla").html(result.message)
                    console.log(result.message)

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

                    console.log(msg)
                },
            });
        }
    });
});

var id = "";
var tipo = "";

var modalConfirm = function (callback) {
    $(document).on('click', '.restricciones', function (e) {
        $("#modal_bloquear").modal('show')
        id = $(this).attr("data-id")
        tipo = $(this).attr("data-tipo")
    });

    $("#modal-btn-si").on("click", function () {
        callback(true, id, tipo);
        $("#modal_bloquear").modal('hide');
    });

    $("#modal-btn-no").on("click", function () {
        callback(false, id, tipo);
        $("#modal_bloquear").modal('hide');
    });
};

modalConfirm(function (confirm, id, tipo) {
    if (confirm) {
        $.post("_operaciones/restriccion_desbloqueo.php", { reprod_id: id, tipo: tipo }, function (data) {
            location.reload();
        });
    }
});