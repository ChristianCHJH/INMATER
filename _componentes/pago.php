<script>
    $(".chosen-select").chosen();

    $("#total_cancelar").bind("keyup mouseup", function() {
        var total_cancelar = 0;
        if ($("#total_cancelar").val() != "") {
            total_cancelar = $("#total_cancelar").val();
        }

        var total_descuento = 0;
        if ($("#total_descuento").val() != "") {
            total_descuento = $("#total_descuento").val();
        }

        $("#vuelto").val((total_cancelar - total_descuento).toFixed(2));
    });

    $("#p1, #p2, #p3, #m1, #m2, #m3").bind("keyup mouseup", function() {
        var tipo_servicio = $("#t_ser").val();
        var total_medios = "";

        if (tipo_servicio == "1" || tipo_servicio == "2" || tipo_servicio == "3") {
            var pago_1 = $("#p1").val() ? $("#p1").val() : "0";
            var tipo_cambio_1 = $("#m1").val() ? ($("#m1").val() == "0" ? "1" : $("#mon").val()) : "0";
            var pago_2 = $("#p2").val() ? $("#p2").val() : "0";
            var tipo_cambio_2 = $("#m2").val() ? ($("#m2").val() == "0" ? "1" : $("#mon").val()) : "0";
            var pago_3 = $("#p3").val() ? $("#p3").val() : "0";
            var tipo_cambio_3 = $("#m3").val() ? ($("#m3").val() == "0" ? "1" : $("#mon").val()) : "0";
            total_medios =
                parseFloat(pago_1) * parseFloat(tipo_cambio_1) +
                parseFloat(pago_2) * parseFloat(tipo_cambio_2) +
                parseFloat(pago_3) * parseFloat(tipo_cambio_3);
        } else {
            var pago_1 = $("#p1").val() ? $("#p1").val() : "0";
            var tipo_cambio_1 = $("#m1").val() ? ($("#m1").val() == 1 ? 1 : 1 / $("#mon").val()) : 0;
            var pago_2 = $("#p2").val() ? $("#p2").val() : "0";
            var tipo_cambio_2 = $("#m2").val() ? ($("#m2").val() == 1 ? 1 : 1 / $("#mon").val()) : 0;
            var pago_3 = $("#p3").val() ? $("#p3").val() : "0";
            var tipo_cambio_3 = $("#m3").val() ? ($("#m3").val() == 1 ? 1 : 1 / $("#mon").val()) : 0;
            total_medios =
                parseFloat(pago_1) * parseFloat(tipo_cambio_1) +
                parseFloat(pago_2) * parseFloat(tipo_cambio_2) +
                parseFloat(pago_3) * parseFloat(tipo_cambio_3);
        }

        var total_descuento = $("#total_descuento").val() ? $("#total_descuento").val() : "0";
        $("#alerta-medios-pago").hide();

        if (parseFloat(total_descuento) != total_medios && !$("#check_serviciogratuito").prop("checked")) {
            $("#alerta-medios-pago").show();
        }
    });

    $("#two-digits").keyup(function() {
        if ($("#tot").val() == "") {
            this.value = "";
            return;
        }

        if ($(this).val().indexOf(".") != -1 && $(this).val().split(".")[1].length > 2) {
            if (isNaN(parseFloat(this.value))) return;
            this.value = parseFloat(this.value).toFixed(2);
        }

        $("#total_descuento").val(($("#tot").val() - this.value).toFixed(2));

        return this;
    });

    $("#porcentaje_descuento").keyup(function() {
        $("#total_cancelar, #vuelto").val("");
        $("#descuento").val(((this.value * $("#tot").val()) / 100).toFixed(2));
        $("#total_descuento").val(($("#tot").val() - $("#descuento").val()).toFixed(2));
        return this;
    });
    $("#descuento").keyup(function() {
        $("#total_cancelar, #vuelto").val("");
        $("#porcentaje_descuento").val(((this.value * 100 / $("#tot").val())).toFixed(2));
        $("#total_descuento").val(($("#tot").val() - $("#descuento").val()).toFixed(2));
        return this;
    });

    $("#check_serviciogratuito").change(function() {
        if ($(this).prop("checked")) {
            $("#alerta-medios-pago").hide();
            $("#porcentaje_descuento, #descuento, #total_cancelar, #vuelto").val("");
            $("#total_descuento").val(($("#tot").val() - $("#descuento").val()).toFixed(2));
            $(".div_descuento").hide();
            $("#check_descuento").prop("disabled", true).checkboxradio("refresh");
            $("#check_descuento").prop("checked", false).checkboxradio("refresh");
        } else {
            $("#alerta-medios-pago").show();
            $("#check_descuento").prop("disabled", false).checkboxradio("refresh");
        }
    });

    $(document).on("click", ".paci_insert", function(ev) {

        $('#ruc').val("");
        $('#razon').val("");
        $('#direccionfiscal').val("");
        $('#condicion_contribuyente').val("");
        $('#estado_contribuyente').val("");
        $("#verificar_texto").text("Verificar:");

        $("#razon").prop("readonly", false);
        $("#ruc").prop("readonly", false);
        $("#direccionfiscal").prop("readonly", false);
        $('#verificacion_reniec').prop('checked', false);
        $('#verificacion_reniec').hide();

        $("#tip").val("0").change();

        $(".carga_paci .ui-input-search input").attr("id", "paci_nom");
        $("#paci_nom").prop("required", true);
        $("#paci_nom").val($(this).text().trim());
        $("#nom").val($(this).text().trim());
        $("#razon").val($(this).text().trim());
        $("#dni").val($(this).attr("dni").trim());
        $("#programa_id").val($(this).attr("programa")).selectmenu("refresh");
        $("#ruc").val($(this).attr("dni").trim());
        $("#tipo_documento_facturacion").val($(this).attr("tip")).selectmenu("refresh");
        $("#correo_electronico").val($(this).attr("mai"));
        $("#paci_nom").textinput("refresh");
        $(".fil_paci li").addClass("ui-screen-hidden");
        $("#paci_nom").focus();
        $("#med").val($(this).attr("med")).selectmenu("refresh");
        $("#sede").val($(this).attr("sede").trim()).selectmenu("refresh");
        $("#textFechaCreacion").val($(this).attr("createdate").trim());
        tipoDniG = $(this).attr("tip");

    });

    $(document).on("click", ".carga_paci .ui-input-search .ui-input-clear", function(ev) {
        $("#nom").val("");
        $("#dni").val("");
    });

    $(document).on("input paste", ".carga_paci .ui-input-search", debounce(function(e) {
        var paciente = $(".carga_paci .ui-input-search :input")[0].value;
        if (paciente.length > 3) {
            $.post(
                "le_tanque.php", {
                    carga_paci_det: paciente,
                },
                function(data) {
                    $(".carga_paci ul").html("");
                    $(".carga_paci ul").append(data);
                    $(".ui-page").trigger("create"); //recarga los css del jqm
                }
            );
        }
    }, 800));

    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this,
                args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };
</script>