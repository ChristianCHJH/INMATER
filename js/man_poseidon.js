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
        if (document.getElementById("nombre").value == "") {
            alert("Debe ingresar un nombre.");
            return false;
        }

        var nombre = $('#nombre').val();

        $.post("_operaciones/man_poseidon.php", { id: id, tipo_operacion: 2, nombre: nombre }, function (data) {
            location.reload();
        });
    });

    modalConfirm(function (confirm, id) {
        if (confirm) {
            $.post("_operaciones/man_poseidon.php", { id: id, tipo_operacion: 1 }, function (data) {
                location.reload();
            });
        }
    });

    var modalConfirm1 = function (callback) {
        $("#guardar").on("click", function () {
            $("#modal_editar").modal('show')
        });

        $("#modal-btn-si").on("click", function () {
            var id = $("#principal").val()
            var nombre = $("#nombre").val()
            callback(true, id, nombre)
            $("#modal_editar").modal('hide')
        });

        $("#modal-btn-no").on("click", function () {
            callback(false, id, nombre)
            $("#modal_editar").modal('hide')
        });
    };

    modalConfirm1(function (confirm, id, nombre) {
        if (confirm) {
            $.post("_operaciones/man_poseidon.php", { id: id, tipo_operacion: 3, nombre: nombre }, function (data) {
                location.reload();
            });
        }
    });
});