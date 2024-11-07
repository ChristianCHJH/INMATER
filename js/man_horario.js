$(document).ready(function () {
    $(document).on("click", ".horarios", function () {
        if (confirm("¿Está seguro que quiere marcar este registro?")) {
            var id = $(this).attr('data-id');
            var procedimiento = $(this).attr('data-procedimiento');
            var valor = ($(this).is(":checked") ? 1 : 0);

            $.post("_operaciones/man_horario.php", {id: id, procedimiento: procedimiento, valor: valor}, function (data) {
            });
        } else {
            return false;
        }
    });
});