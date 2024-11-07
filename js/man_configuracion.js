$(document).ready(function () {
    var id = "";
    var modalConfirm = function (callback) {
        $("#guardar").on("click", function () {
            $("#modal_editar").modal('show')
        });

        $("#modal-btn-si").on("click", function () {
            var id = $("#id_man_configuracion").val()
            var descripcion = $("#descripcion").val()
            var valor = $("#valor").val()
            callback(true, id, descripcion, valor)
            $("#modal_editar").modal('hide')
        });

        $("#modal-btn-no").on("click", function () {
            callback(false, id, id, descripcion, valor)
            $("#modal_editar").modal('hide')
        });
    };

    modalConfirm(function (confirm, id, descripcion, valor) {
        if (confirm) {
            $.post("_operaciones/man_configuracion.php", { id: id, tipo_operacion: 3, descripcion: descripcion, valor: valor }, function (data) {
                location.reload()
            });
        }
    });
});