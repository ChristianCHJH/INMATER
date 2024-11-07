var descon = 0;
var conta = 0;

$(document).ready(function () {
    $("#p_fiv").change(function () {
        if ($(this).prop('checked')) {
            $('#p_icsi').val("");
            $('#p_icsi').checkboxradio("disable");
        } else {
            $('#p_icsi').checkboxradio("enable");
        }
    });
    $("#p_icsi").change(function () {
        if ($(this).prop('checked')) {
            $('#p_fiv').val("");
            $('#p_fiv').checkboxradio("disable");
        } else {
            $('#p_fiv').checkboxradio("enable");
        }
    });

    $("#p_iiu").change(function () {
        if ($(this).prop('checked')) {
            $('#p_fiv,#p_icsi,#p_cri,#p_don,#p_od,#p_des').val("");
            $('#p_fiv,#p_icsi,#p_cri,#p_don').checkboxradio("disable");
            $('#p_od,#p_des').selectmenu("disable");
        } else {
            $('#p_fiv,#p_icsi,#p_cri,#p_don').checkboxradio("enable");
            $('#p_od,#p_des').selectmenu("enable");
        }
    });

    $('#donante-button').hide();

    $("#p_des").change(function () {
        $('#donante-button').hide();
        $('#p_fiv, #p_icsi, #p_cri, #p_don, #p_iiu').checkboxradio("enable");
        $('#p_od').selectmenu("enable");
        $(".lista_des2").remove();

        if ($(this).val() == 1 || $(this).val() == 3) { // embriones
            $('#p_fiv, #p_icsi, #p_cri, #p_don, #p_iiu, #p_od').val("");
            $('#p_fiv, #p_icsi, #p_cri, #p_don, #p_iiu').checkboxradio("disable");
            $('#p_od').selectmenu("disable");
        }
        if ($(this).val() == 2 || $(this).val() == 4) { // ovulos
            $('#p_cri, #p_don, #p_iiu, #p_od').val("");
            $('#p_cri, #p_don, #p_iiu').checkboxradio("disable");
            $('#p_od').selectmenu("disable");
        }
        if ($(this).val() == 1 || $(this).val() == 2) { // ovulos/ embriones donados
            $('#donante-button').show();
            $('#donante').val('');
            $('#donante').prop('selectedIndex', 0);
            $('#donante').selectmenu("refresh", true);
        }
        if ($(this).val() == 3 || $(this).val() == 4) { // ovulos/ embriones propios
            var h = $(this).val();
            var dni = $("#dni").val();
            $('.lista_des').html('<h3>CARGANDO DATOS...</h3>');
            $.post("le_tanque.php", {h: h, dni: dni, paci: dni, btn_guarda: 1}, function (data) {
                $('.lista_des').html('');
                $(".lista_des").append('<div class="lista_des2">' + data + '</div>');
                $('.ui-page').trigger('create'); // recarga los css del jqm
            });
        }
    });

    $("#donante").change(function () {
        if ($(this).val() != '') {
            $(".lista_des2").remove();
            var h = $("#p_des").val();
            var dni = $(this).val();
            var paci = $("#dni").val();
            $('.lista_des').html('<h3>CARGANDO DATOS...</h3>');
            $.post("le_tanque.php", {h: h, dni: dni, paci: paci, btn_guarda: 1}, function (data) {
                $('.lista_des').html('');
                $(".lista_des").append('<div class="lista_des2">' + data + '</div>');
                $('.ui-page').trigger('create'); // recarga los css del jqm
            });
        }
    });
});


$(document).on('change', '.deschk', function (ev) {
    console.log($(this).attr("id"));
    $("#des_dia").val($(this).attr("id")); // Esto define el dia de descongelacion segun el ultimo check q se presiono
    /* SE QUITA ESTE SCRIPT PARA QUE PERMITA SELECCIONAR CUALQUIER DIA DE DESCONGELACION
    if (descon == $(this).attr("id") || descon==0) {
        conta++;
        descon = $(this).attr("id");

        if (conta==1) {
            // var arr = descon.split('|');
            // $("#des_tip").val(arr[0]);
            $("#des_dia").val(descon);
        }

    } else {
        $('.deschk').attr('checked', false);
        descon = $(this).attr("id");
        conta = 0;
        $("#des_dia").val('');
    }
    */
});

function anular(id) {
    if (confirm("Esta apunto de eliminar esta Reproducción asistida, esta seguro?")) {
        document.form2.borrar.value = id;
        document.form2.submit();
        /*$.post("verificar_cancelacion.php", {idpro: id}, function (data) {
            console.log(data);
            document.getElementById("borrar").value = data;
            // $("#prov").html(data);
            // $("#prov").selectmenu("refresh");
        });*/
    }
    else return false;
}

function Beta(beta, pro) {
    document.form2.val_beta.value = beta.value;
    document.form2.pro_beta.value = pro;
    document.form2.submit();
}

$(document).on("click", ".show-page-loading-msg", function () {
    /*if (document.getElementById("p_dni").value == "") {
        alert("Debe ingresar la Pareja");
        return false;
    }
    if (document.getElementById("t_mue").value == "") {
        alert("Debe ingresar el tipo de Muestra");
        return false;
    }*/
    if (document.getElementById("p_des").value != "") {
        if (!document.getElementById("des_dia")) {
            alert("Debe marcar el Dia de Descongelación");
            return false;
        } else if (document.getElementById("des_dia").value == "") {
            alert("Debe marcar el Dia de Descongelación");
            return false;
        }
        /*if (confirm("Esta a punto de solicitar una Descongelación, esta seguro?"))
         return true;
         else
         return false;*/
    }

    var $this = $(this),
        theme = $this.jqmData("theme") || $.mobile.loader.prototype.options.theme,
        msgText = $this.jqmData("msgtext") || $.mobile.loader.prototype.options.text,
        textVisible = $this.jqmData("textvisible") || $.mobile.loader.prototype.options.textVisible,
        textonly = !!$this.jqmData("textonly");
    html = $this.jqmData("html") || "";
    $.mobile.loading("show", {
        text: msgText,
        textVisible: textVisible,
        theme: theme,
        textonly: textonly,
        html: html
    });
}).on("click", ".hide-page-loading-msg", function () {
    $.mobile.loading("hide");
});

$(function () {
    $('#alerta').delay(3000).fadeOut('slow');
});//]]>