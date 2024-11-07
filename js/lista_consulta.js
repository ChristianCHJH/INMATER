$(document).ready(function () {
    $("#cupon").selectmenu('disable');

    $('.ui-input-search').appendTo($('.enlinea'));

    $('#med_agenda').on('change', function () {
        $(".marco_agenda").remove();

        if (this.value) {
            $(".td_agenda").append('<div class="marco_agenda"><iframe src="agenda.php?med=' + this.value + '" width="100%" height="800" seamless></iframe></div>');
        }
    });

    $(".ui-input-search input").attr("id", "paci_nom");

    $(document).on('click', '.paci_insert', function (e) {
        console.log($(this).attr("dni"));
        $('#paci_nom').val($(this).attr("nom"));
        $('#dni').val($(this).attr("dni"));
        $('#paci_nom').textinput('refresh');
        $('.fil_paci li').addClass('ui-screen-hidden');
        $('#paci_nom').focus();
        $('#med').val('');
    });

    $(document).on('input paste', '.carga_paci .ui-input-search', function (e) {
        var paciente = $('.carga_paci .ui-input-search :input')[0].value;

        if (paciente.length > 3) {
            $.post("le_tanque.php", { carga_paci_det: paciente }, function (data) {
                $(".carga_paci ul").html("");
                $(".carga_paci ul").append(data);
                $('.ui-page').trigger('create');
            });
        }
    });

    $(document).on('change', '#man_motivoconsulta_id', function (e) {
        if (this.value == '4') {
            $("#mot").prop('required', true);
        } else {
            $("#mot").prop('required', false);
        }
    });

    $("input[name='tipoconsulta_id']").bind( "change", function(event, ui) {
        // 1: presencial, 2: virtual
        if (this.value == 1) {
            $("#cupon").selectmenu('enable');
        } else {
            $("#cupon").prop('selectedIndex', 0);
            $("#cupon").selectmenu("refresh", true);
            $("#cupon").selectmenu('disable');
        }
    });
});