$(document).ready(function () {
    $("#donante").change(function () {
        if ($(this).val() != '' && $("#p_des").val() != '') {
            $(".lista_des2").remove();
            var h = $("#p_des").val();
            var dni = $(this).val();
            var paci = $("#dni").val();
            $('.lista_des').html('<h3>CARGANDO DATOS...</h3>');

            $.post("le_tanque.php", { h: h, dni: dni, paci: paci, btn_guarda: 2 }, function (data) {
                $('.lista_des').html('');
                $(".lista_des").append('<div class="lista_des2">' + data + '</div>');
                $('.ui-page').trigger('create'); // recarga los css del jqm
            });
        }
    });

    $("#p_des").change(function () {
        $("#donante").val("");
        $("#donante").prop('selectedIndex', 0);
        $("#donante").selectmenu("refresh", true);
    });
});