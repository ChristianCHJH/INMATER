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
        if (document.getElementById("codigo").value == "") {
            alert("Debe ingresar un código.");
            return false;
        }

        if (document.getElementById("descripcion").value == "") {
            alert("Debe ingresar una descripción.");
            return false;
        }
        var codigo = $('#codigo').val();
        var descripcion = $('#descripcion').val();

        console.log("demo")
        $.post("_operaciones/man_gine_tipo_intervencion.php", {id: id, tipo_operacion: 2, codigo: codigo, descripcion: descripcion}, function (data) {
            location.reload();
        });
    });

    modalConfirm(function (confirm, id) {
        if (confirm) {
            $.post("_operaciones/man_gine_tipo_intervencion.php", {id: id, tipo_operacion: 1}, function (data) {
                location.reload();
            });
        }
    });
});