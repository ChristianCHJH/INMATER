<script>
    $(document).ready(function() {
        var total = 0;
        // modificar el tipo de comprobante (1: booleta, 2: factura)
        $("#tip").change(function() {
            $("#tipo_documento_facturacion,#verificar").prop("disabled", false);
            $("#ser").val("");
            $("#servicios").empty();
            $("#tot, #p1").val("");
            $("#porcentaje_descuento, #descuento, #total_descuento, #total_cancelar, #vuelto").val("");
            $("#subtot, #igv").html("-");
            $("#man_ini, #man_fin, #cadena").val("");
            $("#man_ini, #man_fin").prop("required", false);
            $(".mantenimiento").hide();
            total = 0;
            if ($(this).val() != "") {
                $("#tipo_documento_facturacion, .servicio, .factura").show();
                if ($(this).val() == "1") {
                    $("#tipo_documento_facturacion option").prop("disabled", false);
                    $("#tipo_documento_facturacion").val(tipoDniG).change();
                    if (tipoDniG == 2) {
                        $('#ocultar').show();
                    } else {
                        $('#ocultar').hide();
                    }
                } else {
                    $("#tipo_documento_facturacion option").prop("disabled", true);
                    $("#tipo_documento_facturacion option[value='4']").attr("disabled", false);
                    $("#tipo_documento_facturacion option[value='1']").attr("disabled", false);
                    $("#tipo_documento_facturacion").val("4").change();
                    $('#ocultar').show();
                }
            } else {
                $(".servicio, .factura").hide();
            }
        });

        $(".med_insert").change(function() {
            var t_ser = $("#t_ser").val();
            // paquetes (3: procedimientos sala, 5: perfiles)
            if (t_ser == 3 || t_ser == 5) {
                if ($(this).val() != "") {
                    var pak = $(this).val();
                    var mon = $("#mon").val();
                    var tip = $("#tip").val();
                    var sede_id = $("#sede_contabilidad_id").val();
                    $("#servicios").html("<h3>CARGANDO DATOS...</h3>");
                    $.post(
                        "le_tanque.php", {
                            pak: pak,
                            t_ser: t_ser,
                            mon: mon,
                            tip: tip,
                            sede_id: sede_id,
                        },
                        function(data) {
                            var data = data.split("|");
                            $("#servicios").empty();
                            $("#ser").val(data[0]);
                            $("#servicios").append(data[0]);
                            $("#tot, #p1").val(data[1]);
                            $("#igv").html(data[2]);
                            $("#subtot").html(data[1] - data[2]);
                            $("#cadena").val(data[3]);
                            $("#porcentaje_descuento, #descuento, #total_cancelar, #vuelto").val("0");
                            $("#total_descuento").val($("#tot").val());
                        }
                    );
                }
            } else {
                // individuales (1: reproduccion asistida, 2: andrologia, 6: ecografia, 7: adicionales)
                var str = $("#ser").val();
                var items = $(this).val();
                var idcc = $("option:selected", this).attr("id");
                var mante = items.indexOf("MANTENIMIENTO");

                if (t_ser == 1 || t_ser == 2 || t_ser == 3) {
                    var costo = $("option:selected", this).attr("costo") * $("#mon").val();
                } else {
                    var costo = $("option:selected", this).attr("costo") / $("#mon").val();
                }

                $("#ser").val(
                    str + "<tr><td>" + idcc + "</td><td>" + items + "</td><td>" + costo.toFixed(2) + "</td></tr>"
                );
                $("#servicios").append("<tr><td>" + idcc + "</td><td>" + items + "</td><td>" + costo.toFixed(2) + "</td><td><input type='checkbox' class='eliminar-servicio' checked></td></tr>");

                if (t_ser == 1 || t_ser == 2 || t_ser == 3) {
                    total = total + parseFloat($("option:selected", this).attr("costo") * $("#mon").val());
                } else {
                    total = total + parseFloat($("option:selected", this).attr("costo") / $("#mon").val());
                }

                $("#tot, #p1").val(total.toFixed(2));
                $("#igv").html((total - total / 1.18).toFixed(2));
                $("#subtot").html((total / 1.18).toFixed(2));
                $(this).prop("selectedIndex", 0);
                $(this).selectmenu("refresh", true);

                if (mante >= 0) {
                    $(".mantenimiento").show();
                    $("#man_ini,#man_fin").prop("required", true);
                }

                $("#porcentaje_descuento, #descuento, #total_cancelar, #vuelto").val("0");
                $("#total_descuento").val($("#tot").val());

                $(".med_insert").val("");
                $(".med_insert").trigger("chosen:updated");
            }
        });

        $("#procedimiento_id").change(function() {
            if ($(this).val() == "") {
                $("#ser").val("");
                $("#servicios").empty();
                $("#tot, #p1").val("");
                $("#porcentaje_descuento, #descuento, #total_descuento, #total_cancelar, #vuelto").val("");
                $("#subtot, #igv").html("-");
                $("#man_ini, #man_fin, #cadena").val("");
                $("#man_ini, #man_fin").prop("required", false);
                $(".mantenimiento").hide();
                $("#procedimiento_id").val("").trigger("chosen:updated");
                total = 0;
                return false;
            }

            total = 0;
            var tipo_servicio = $("#t_ser").val();
            $.post(
                "le_tanque.php", {
                    sede_id: $("#sede_contabilidad_id").val(),
                    tarifario_id: $("#tarifario_id").val(),
                    procedimiento_id: $(this).val(),
                    tipo_servicio: tipo_servicio,
                    tipo_comprobante: $("#tip").val(),
                    tipo_cambio: $("#mon").val(),
                },
                function(data) {
                    var data = data.split("|");
                    total = total + parseFloat(data[1]);
                    // data0: tabla visual que ve el usuario
                    // data1: monto total
                    // data2: igv
                    // data3: codigo de servicio
                    $("#servicios").html(data[2]);
                    $("#ser").val(data[0]);
                    $("#cadena").val(data[3]);
                    $("#porcentaje_descuento, #descuento, #total_cancelar, #vuelto").val("0");
                    $("#subtot").html((total / 1.18).toFixed(2));
                    $("#igv").html((total - total / 1.18).toFixed(2));
                    $("#tot, #p1").val(total.toFixed(2));
                    $("#total_descuento").val($("#tot").val());
                    fecha_mantenimineto($("#ser").val())
                }
            );
        });

        function fecha_mantenimineto(servicios){
            var items = servicios;
                    var mante = items.indexOf("MANTENIMIENTO");

                    if (mante >= 0) {
                        $(".mantenimiento").show();
                        $("#man_ini,#man_fin").prop("required", true);
                    } else {
                        $(".mantenimiento").hide(); // Oculta los elementos si el valor no contiene "MANTENIMIENTO"
                        $("#man_ini,#man_fin").prop("required", false); // Quita el atributo 'required'
                    }
        }

        $(document).on("click", ".eliminar-servicio", function(e) {
            $("#marcar_todo").removeAttr("checked");
            eliminarServicio();
        });

        function eliminarServicio() {
            var table_temporal = jQuery('#servicios tbody').clone();
            let total_seleccionado = 0;
            var codigoanglo='';
            let contador = 0
            $(table_temporal.children()).each(function(e) {
                contador++
                if ($(this).find("td").eq(3).children().get(0).checked && contador != 1) {
                    total_seleccionado += parseFloat($(this).find("td").eq(2).html());
                    if(codigoanglo==''){
                        codigoanglo = $(this).find("td").eq(0).html();
                    }else{
                        codigoanglo = codigoanglo + "," +$(this).find("td").eq(0).html();
                    }
                    $(this).children("td:eq(3),th:eq(3)").remove();
                } else {
                    $(this).remove();
                }
            });

             $.post(
                "le_tanque.php", {
                    codAnglo: codigoanglo,
                },
                function(data) {
                    $("#cadena").val(data);
                }
            ); 

            $("#ser").val(table_temporal.html());
            $("#porcentaje_descuento, #descuento, #total_cancelar, #vuelto").val("0");
            $("#subtot").html((total_seleccionado / 1.18).toFixed(2));
            $("#igv").html((total_seleccionado - total_seleccionado / 1.18).toFixed(2));
            $("#tot, #p1").val(total_seleccionado.toFixed(2));
            $("#total_descuento").val($("#tot").val());
            total = total_seleccionado;
            fecha_mantenimineto($("#ser").val());
        }

        $(document).on("change", "#marcar_todo", function(e) {
            var checkboxes = document.querySelectorAll('#servicios input[type="checkbox"]');

            if ($(this).prop("checked")) {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = true;
                });
                eliminarServicio();
            } else {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = false;
                });
                eliminarServicio();
            }
        });

        // analisis de sangre
        $(".extra_insert").click(function(e) {
            var str = $("#ser").val();
            var items = $(this).attr("data");
            var idcc = $(this).attr("id");

            if ($("#cadena").val() == "") {
                $("#cadena").val($(this).attr("cod"));
            } else {
                $("#cadena").val($("#cadena").val() + "," + $(this).attr("cod"));
            }

            var costo = $(this).attr("costo") / $("#mon").val(); // aqui siempre divide porque este servicio es solo para t_ser analisis de sangres

            $("#ser").val(str + "<tr><td>" + idcc + "</td><td>" + items + "</td><td>" + costo.toFixed(2) + "</td></tr>");
            $("#servicios").html(
                str + "<tr><td>" + idcc + "</td><td>" + items + "</td><td>" + costo.toFixed(2) + "</td></tr>"
            );
            $(".fil_extra li").addClass("ui-screen-hidden");
            total = total + parseFloat($(this).attr("costo") / $("#mon").val());
            $("#tot, #p1").val(total.toFixed(2));
            $("#descuento, #total_cancelar, #vuelto").val("0");
            $("#total_descuento").val(total.toFixed(2));
            $("#igv").html((total - total / 1.18).toFixed(2));
            $("#subtot").html((total / 1.18).toFixed(2));
        });

        $("#sede_contabilidad_id").change(function(e) {
            borrar();
            $("#tarifario_id").val("").trigger("chosen:updated");
            $("#procedimiento_id").val("").trigger("chosen:updated");
        });

        $("#tarifario_id").change(function(e) {
            borrar();
            $("#procedimiento_id").val("").trigger("chosen:updated");
        });

        $("#borrar").click(function(e) {
            borrar();
            total = 0;
        });

        $("#cambio").change(function() {
            if ($(this).attr("data") == 1 || $(this).attr("data") == 2 || $(this).attr("data") == 3) {
                var mon1 = "S/.";
                var mon2 = "$";
            } else {
                var mon1 = "$";
                var mon2 = "S/.";
            }
            if ($(this).prop("checked")) {
                $("#mon").val($("#tipo_cambio").val());
                $(".mon").show();
                $("#labelmon").html(mon1);
                $("#labelmondes").html(mon1);
            } else {
                $(".mon").hide();
                $("#labelmon").html(mon2);
                $("#labelmondes").html(mon2);
                $("#mon").val(1);
            }

            $("#procedimiento_id").val("").trigger("chosen:updated");
            $("#ser").val("");
            $("#servicios").empty();
            $("#tot, #p1").val("");
            $("#descuento, #total_descuento, #total_cancelar, #vuelto").val("");
            $("#subtot, #igv").html("-");
            total = 0;
        });

        function borrar() {
            $("#ser").val("");
            $("#servicios").empty();
            $("#tot, #p1").val("");
            $("#porcentaje_descuento, #descuento, #total_descuento, #total_cancelar, #vuelto").val("");
            $("#subtot, #igv").html("-");
            $("#man_ini, #man_fin, #cadena").val("");
            $("#man_ini, #man_fin").prop("required", false);
            $(".mantenimiento").hide();
        }

        $("#id_empresa").change(function() {
            $("#id_sede").val(null).change();
            if($(this).val() == 5){
                document.documentElement.style.setProperty('--bginmater', '#a381aa');
                document.documentElement.style.setProperty('--bginmater1', '#cfa7d6');
                document.documentElement.style.setProperty('--bginmater2', '#f1cbfb');
                document.documentElement.style.setProperty('--bginmater3', '#ffffff');
                document.documentElement.style.setProperty('--bdinamter', '#000000');
                document.documentElement.style.setProperty('--clinamter', '#000000');
                document.documentElement.style.setProperty('--clinamter1', '#000000');

            }else if($(this).val() == 4){
                document.documentElement.style.setProperty('--bginmater', '#72a2aa');
                document.documentElement.style.setProperty('--bginmater1', '#d7e5e5');
                document.documentElement.style.setProperty('--bginmater2', '#a9d9d8');
                document.documentElement.style.setProperty('--bginmater3', '#ffffff');
                document.documentElement.style.setProperty('--bdinamter', '#72a2aa');
                document.documentElement.style.setProperty('--clinamter', '#72a2aa');
                document.documentElement.style.setProperty('--clinamter1', '#3b5554');
            }
        });

        $("#id_sede").change(function() {
            if($("#id_empresa").val() == 5 && $(this).val() == 16){
                document.documentElement.style.setProperty('--bginmater', '#c5ab5c');
                document.documentElement.style.setProperty('--bginmater1', '#e7cf86');
                document.documentElement.style.setProperty('--bginmater2', '#ede0b8');
                document.documentElement.style.setProperty('--bginmater3', '#ffffff');
                document.documentElement.style.setProperty('--bdinamter', '#000000');
                document.documentElement.style.setProperty('--clinamter', '#000000');
                document.documentElement.style.setProperty('--clinamter1', '#000000');
            }else if($("#id_empresa").val() == 5 && $(this).val() != 16){
                document.documentElement.style.setProperty('--bginmater', '#a381aa');
                document.documentElement.style.setProperty('--bginmater1', '#cfa7d6');
                document.documentElement.style.setProperty('--bginmater2', '#f1cbfb');
                document.documentElement.style.setProperty('--bginmater3', '#ffffff');
                document.documentElement.style.setProperty('--bdinamter', '#000000');
                document.documentElement.style.setProperty('--clinamter', '#000000');
                document.documentElement.style.setProperty('--clinamter1', '#000000');
            }else if($("#id_empresa").val() == 4){
                document.documentElement.style.setProperty('--bginmater', '#72a2aa');
                document.documentElement.style.setProperty('--bginmater1', '#d7e5e5');
                document.documentElement.style.setProperty('--bginmater2', '#a9d9d8');
                document.documentElement.style.setProperty('--bginmater3', '#ffffff');
                document.documentElement.style.setProperty('--bdinamter', '#72a2aa');
                document.documentElement.style.setProperty('--clinamter', '#72a2aa');
                document.documentElement.style.setProperty('--clinamter1', '#3b5554');
            }
        });

    });
</script>