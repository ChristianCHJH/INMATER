$(document).ready(function () {
    var id = "";
    var modalConfirm = function (callback) {
        $(".eliminar").on("click", function () {
            $("#modal_eliminar").modal('show')
            id = $(this).attr("data-id")
        });

        $("#modal-btn-si").on("click", function () {
            callback(true, id);
            $("#modal_eliminar").modal('hide');
        });

        $("#modal-btn-no").on("click", function () {
            callback(false, id);
            $("#modal_eliminar").modal('hide');
        });
    };

    $("#agregar").on("click", function () {
        var fecha = $('#fecha').val();
        var hora = $('#hora').val();
        var turno = $('#turno').val();

        if (fecha == "") {
            alert("Debe ingresar la fecha.");
            return false;
        }

        if (hora == "") {
            alert("Debe ingresar la hora.");
            return false;
        }

        if (turno == "") {
            alert("Debe ingresar turno.");
            return false;
        }

        $.post("_operaciones/man_horario_bloqueo.php", { tipo_operacion: 2, fecha: fecha, hora: hora, turno: turno }, function (data) {
            location.reload();
        });
    });

    modalConfirm(function (confirm, id) {
        if (confirm) {
            $.post("_operaciones/man_horario_bloqueo.php", { id: id, tipo_operacion: 1 }, function (data) {
                location.reload();
            });
        }
    });
});