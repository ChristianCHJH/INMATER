$(document).ready(function () {
    $(document).on("click", ".incubadoradia", function () {
        if (confirm("¿Está seguro que quiere marcar este registro?")) {
            var id = $(this).attr('data-id');
            var dia = $(this).attr('data-dia');
            var valor = ($(this).is(":checked") ? 1 : 0);

            $.post("_operaciones/lab_incubadora.php", {id: id, dia: dia, valor: valor}, function (data) {
            });
        } else {
            return false;
        }
    });
});