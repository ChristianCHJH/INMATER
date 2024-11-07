$(document).ready(function () {
    $("#agregar").on("click", function () {
        var nombre = $('#nombre').val();
        var codigo = $('#codigo').val();

        if (codigo == "") {
            alert("Debe ingresar un Código.");
            return false;
        }

        if (nombre == "") {
            alert("Debe ingresar un Nombre.");
            return false;
        }

        $.post("_operaciones/lab_celulas.php", { tipo_operacion: 2, codigo: codigo, nombre: nombre }, function (data) {
            location.reload();
        });
    });

    $('#predeterminado2, #predeterminado3, #predeterminado4, #predeterminado5, #predeterminado6').on('change', function () {
        if (confirm("¿Está seguro que quiere seleccionarlo como predeterminado?")) {
            var dia = $(this).attr('data-dia');
            var id = $(this).val();

            $.post("_operaciones/lab_celulas.php", { tipo_operacion: 3, dia: dia, id: id }, function (data) {
                location.reload();
            });
        } else {
            return false;
        }
    });

    $(document).on("click", ".celuladia", function () {
        if (confirm("¿Está seguro que quiere marcar este registro?")) {
            var id = $(this).attr('data-id');
            var dia = $(this).attr('data-dia');
            var valor = ($(this).is(":checked") ? 1 : 0);

            $.post("_operaciones/lab_celulas.php", { tipo_operacion: 1, id: id, dia: dia, valor: valor }, function (data) {
                location.reload();
            });
        } else {
            return false;
        }
    });
});