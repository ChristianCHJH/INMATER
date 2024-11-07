function calcular_total_ft() {
    var suma = 0;
    var igv = $("#hdnValorIgv").val();
    var sumaParcial = 0;

    $("#ver_documento_credito table tbody tr").each(function (key, val) {
        var $tds = $(this).find("td");
        sumaParcial = $tds.eq(3).find(".item_cantidad").val() * $tds.eq(4).find(".item_precio").val();
        $tds.eq(5).find(".item_valorventa").val(sumaParcial);
        suma += parseFloat(sumaParcial);
    });

    $("#subtotal").val(suma.toFixed(2));
    $("#igv").val((suma * 0.18).toFixed(2));
    $("#total").val((suma * 1.18).toFixed(2));
}

function calcular_total_bo() {
    var suma = 0;
    var sumaParcial = 0;

    $("#ver_documento_credito table tbody tr").each(function (key, val) {
        var $tds = $(this).find("td");
        sumaParcial = $tds.eq(3).find(".item_cantidad").val() * $tds.eq(4).find(".item_precio").val();
        $tds.eq(5).find(".item_valorventa").val(sumaParcial.toFixed(2));
        suma += parseFloat(sumaParcial.toFixed(2));
    });

    $("#total").val(suma.toFixed(2));
}

function myFunction() {
    var input, filter, table, tr, td, i;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        var encontro = false;
        for (var j = 0; j < 10; j++) {
            td = tr[i].getElementsByTagName("td")[j];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    encontro = true;
                    break;
                }
            }
        }

        if (encontro) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}

var origen = "";
var tip_recibo = "";
var num_recibo = "";
var monto_dif = "";

var modalConfirm = function (callback) {
    $(".btn_factura_electronica").on("click", function () {
        origen = $(this).attr("data-origen");
        tip_recibo = $(this).attr("data-tip-recibo");
        num_recibo = $(this).attr("data-num-recibo");

        $.post(
            "_operaciones/lista_facturacion.php",
            { origen: origen, tip_recibo: tip_recibo, num_recibo: num_recibo },
            function (data) {
                $("#ver_estado_documento_detalle").html("");
                $("#ver_estado_documento_detalle").append(data);
            }
        );

        $("#ver_estado_documento").modal("show");
    });

    $(".btn_reproduccion_asistida").on("click", function () {
        origen = $(this).attr("data-origen");
        tip_recibo = $(this).attr("data-tip-recibo");
        num_recibo = $(this).attr("data-num-recibo");
        $("#reproduccionasistida_serviciospagados>tbody").html("");
        $("#reproduccionasistida_detalle>tbody").html("");

        $.ajax({
            type: "POST",
            url: "_operaciones/lista_facturacion.php",
            data: { origen: origen, tip_recibo: tip_recibo, num_recibo: num_recibo },
            success: function (result) {
                var data = jQuery.parseJSON(result);
                $("#reproduccionasistida_serviciospagados>tbody").prepend(data.servicios_pagados);
                $("#reproduccionasistida_detalle>tbody").prepend(data.reproducciones_realizadas);
                $("#reproduccionasistida_numerorecibo").val(data.numerorecibo);
                $("#reproduccionasistida_fecha").val(data.fecha);
                $("#reproduccionasistida_paciente").val(data.paciente);
                $("#reproduccionasistida_medico").val(data.medico);
            },
        });

        $("#ver_reproduccion_asistida").modal("show");
    });

    $(".btn_documento_credito").on("click", function () {
        $("#ver_documento_credito").find("input:text").val("");
        $("#documentotipo_id").prop("selectedIndex", 0);
        origen = $(this).attr("data-origen");
        tip_recibo = $(this).attr("data-tip-recibo");
        num_recibo = $(this).attr("data-num-recibo");
        monto_dif = $(this).attr("data-monto-dif");

        $('#ver_documento_credito [name="recibo_tip"]').val(tip_recibo);
        $('#ver_documento_credito [name="recibo_id"]').val(num_recibo);

        // traer serie, correlativo
        $.ajax({
            type: "POST",
            url: "_operaciones/lista_facturacion.php",
            data: { origen: origen, tip_recibo: tip_recibo, num_recibo: num_recibo },
            success: function (result) {
                var data = jQuery.parseJSON(result);

                if ($("#detalle_servicios table tbody").length) {
                    $("#detalle_servicios table tbody").remove();
                }

                if ($("#detalle_servicios table tfoot").length) {
                    $("#detalle_servicios table tfoot").remove();
                }

                $("#detalle_servicios table thead").after(data.lista_detalle);
                $('#ver_documento_credito [name="documentotipo_id"]').val(data.documentotipo_id);
                $('#ver_documento_credito [name="numero"]').val(data.numero);
                $('#ver_documento_credito [name="sede_id"]').val(data.sede_id);
                $('#ver_documento_credito [name="nombre"]').val(data.nombre);
                $('#ver_documento_credito [name="direccion"]').val(data.direccion);
                $('#ver_documento_credito [name="correo"]').val(data.correo);
            },
        });

        $("#ver_documento_credito").modal("show");
    });

    $(".modal-btn-si").on("click", function () {
        if (confirm("¿Realmente desea continuar?")) {
            callback(true, origen, tip_recibo, num_recibo, monto_dif);
        } else {
            return false;
        }
    });

    $(".modal-btn-no").on("click", function () {
        callback(false, "", "", "");
        $("#ver_reproduccion_asistida").modal("hide");
        $("#ver_estado_documento").modal("hide");
        $("#ver_documento_credito").modal("hide");
    });
};

modalConfirm(function (confirm, origen, tip_recibo, num_recibo, monto_dif) {
    if (!confirm) return;

    switch (origen) {
        case "modal_factura_electronica":
            $.post(
                "_operaciones/lista_facturacion.php",
                { origen: "btn_si_" + origen, tip_recibo: tip_recibo, num_recibo: num_recibo },
                function (data) {
                    $("#ver_estado_documento_detalle>tbody").prepend(data);
                }
            );

            break;
        case "modal_documento_credito":
            var total = parseFloat($("#total").val());
            if (total > parseFloat(monto_dif)) {
                alert("el monto de la nota de crédito o débito no puede ser mayor a la de la factura o boleta");
                return false;
            }
            if ($('#ver_documento_credito [name="observacion"]').val() == "") {
                alert("Debe rellenar el campo de observaciones.");
                return false;
            }
            if ($('#ver_documento_credito [name="comprobantetipo_id"]').val() == "") {
                alert("Debe marcar el tipo de comprobante.");
                return false;
            }

            if ($('#ver_documento_credito [name="serie"]').val() == "") {
                alert("Error al asignar Serie");
                return false;
            }

            if ($('#ver_documento_credito [name="correlativo"]').val() == "") {
                alert("Error al asignar Correlativo.");
                return false;
            }

            if ($('#ver_documento_credito [name="motivotipo_id"]').val() == "") {
                alert("Debe marcar el motivo.");
                return false;
            }

            if ($('#ver_documento_credito [name="documentotipo_id"]').val() == "") {
                alert("Debe marcar el tipo de documento.");
                return false;
            }

            if ($('#ver_documento_credito [name="numero"]').val() == "") {
                alert("Debe marcar numero de documento.");
                return false;
            }

            if ($('#ver_documento_credito [name="nombre"]').val() == "") {
                alert("Debe marcar el nombre.");
                return false;
            }

            var data_cabecera = $(
                "#ver_documento_credito input, #ver_documento_credito textarea, #ver_documento_credito select, #ver_documento_credito table tfoot select"
            ).serialize();
            var data_detalle = new Array();
            $("#detalle_servicios_tabla > tbody  > tr").each(function (index, element) {
                var $tds = $(element).find("td");
                data_detalle.push({
                    servicio_id: $tds.eq(0).html(),
                    nombre: $tds.eq(2).html(),
                    cantidad: $tds.eq(3).find(".item_cantidad").val(),
                    precio: $tds.eq(4).find(".item_precio").val(),
                });
            });

            var data_referencia = {
                tip_recibo: tip_recibo,
                num_recibo: num_recibo,
            };

            $.ajax({
                type: "POST",
                url: "_operaciones/lista_facturacion.php",
                data: {
                    origen: "btn_si_" + origen,
                    data_detalle: data_detalle,
                    data_cabecera: data_cabecera,
                    data_referencia: data_referencia,
                },
                dataType: "JSON",
                success: function (result) {
                    console.log(result);
                    $("#ver_documento_credito").modal("hide");
                },
            });
            break;
        default:
            break;
    }
});

$(window).load(function (e) {
    $.ajax({
        type: "POST",
        url: "_operaciones/lista_facturacion.php",
        dataType: "json",
        data: {
            origen: "verificar_tipo_cambio",
        },
        success: function (response) {
            if (response.status) {
                $("#modal-confirm-tipo-cambio").modal("show");
            }
        },
        error: function (jqXHR, exception) {
            console.log(jqXHR, exception);
        },
        complete: function (e) {
            console.log("complete load window");
        },
    });
});

$("#form-tipo-cambio").submit(function (e) {
    if (
        document.getElementById("form-tipo-cambio").elements["tipo_cambio_compra"].value == "0" ||
        document.getElementById("form-tipo-cambio").elements["tipo_cambio_venta"].value == "0"
    ) {
        alert("El tipo de cambio no puede ser cero.");
        return false;
    }

    document.getElementById("form-tipo-cambio").elements["tipo_cambio_compra"].disabled = true;
    document.getElementById("form-tipo-cambio").elements["tipo_cambio_venta"].disabled = true;
    document.getElementById("form-tipo-cambio").elements["tipo-cambio-add"].disabled = true;
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "_operaciones/lista_facturacion.php",
        dataType: "json",
        data: {
            origen: "ingresar_tipo_cambio",
            tipo_cambio_compra: document.getElementById("form-tipo-cambio").elements["tipo_cambio_compra"].value,
            tipo_cambio_venta: document.getElementById("form-tipo-cambio").elements["tipo_cambio_venta"].value,
        },
        success: function (response) {
            console.log(response);
            if (response.status) {
                $("#modal-confirm-tipo-cambio").modal("hide");
            }
        },
        error: function (jqXHR, exception) {
            console.log(jqXHR, exception);
        },
        complete: function (e) {
            console.log("complete");
            /* $("#modal-confirm-add").modal('hide'); */
            /* jQuery(".loader").hide(); */
        },
    });
});

$('#ver_documento_credito [name="comprobantetipo_id"]').change(function () {
    // traer serie y correlativo
    $.ajax({
        url: "_operaciones/lista_facturacion.php",
        data: {
            origen: "consulta_configuracion",
            comprobantetipo_id: $(this).val(),
            recibo_tip: $('#ver_documento_credito [name="recibo_tip"]').val(),
            recibo_id: $('#ver_documento_credito [name="recibo_id"]').val(),
        },
        type: "POST",
        dataType: "JSON",
        success: function (data) {
            $('#ver_documento_credito [name="serie"]').val(data.serie);
            $('#ver_documento_credito [name="correlativo"]').val(data.correlativo);
            $('#ver_documento_credito [name="motivotipo_id"]').html(data.lista_motivo);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert.log("error");
        },
    });
});
