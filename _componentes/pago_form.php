<script>

    $("#form2").submit(function(e) {
        
        
        if ($("#id_empresa").val() == "") {
            alert("Debe ingresar la Empresa.");
            return false;
        }

        if ($("#id_sede").val() == "") {
            alert("Debe seleccionar una Sede.");
            return false;
        }

        if ($("#fec").val() == "") {
            alert("Debe ingresar la fecha del comprobante.");
            return false;
        }
        
        if ($("#tip").val() == "") {
            alert("Debe ingresar el tipo de comprobante.");
            return false;
        }

        if ($("#sede").val() == "") {
            alert("Debe ingresar la procedencia.");
            return false;
        }

        if ($("#med").val() == "") {
            alert("Debe ingresar el medico.");
            return false;
        }
        
        if ($("#tipo_documento_facturacion").val() == "" || $("#ruc").val() == "") {
            alert("Debe ingresar el tipo y número de documento de facturación.");
            return false;
        }
        
        if ($("#tipo_documento_facturacion").val() == "2" && $("#ruc").val().length != 8) {
            alert("El número de documento para el tipo de documento DNI debe tener 8 dígitos.");
            return false;
        }
        
        if ($("#tipo_documento_facturacion").val() == "4" && $("#ruc").val().length != 11) {
            alert("El número de documento para el tipo de documento RUC debe tener 11 dígitos.");
            return false;
        }
        
        if ($("#razon").val() == "") {
            alert("Debe ingresar el nombre o la razón social de facturación.");
            return false;
        }
        
        if ($("#dni").val() == "" || $("#nom").val() == "") {
            alert("Debe ingresar los datos del paciente.");
            return false;
        }
        
        if ($("#total_cancelar").val() == "") {
            alert("Debe ingresar el total a cancelar");
            return false;
        }
        if ($("#condicion_pago_id").val() == "") {
            alert("No has ingresado la condición de pago.");
            return false;
        }
        if ($("#condicion_pago_id").val() == "2" && $("#fecha_vencimiento").val() == "") {
            alert("No has ingresado la fecha de vencimiento.");
            return false;
        }


        if ($('#p1').val() != 0 || $('#t1').val() || $('#banco1').val() || $('#tipotarjeta1').val() || $('#numerocuotas1').val() || $('#m1').val() || $('#poss1').val()) {

            if (!$('#t1').val()) {
                alert("Debe seleccionar una forma de pago del pago 1.");
                return false;
            }
        
            if (!$('#banco1').val() && [4, 5, 6, 8, 9].includes(parseInt($('#t1').val()))) {
                alert("Debe seleccionar el banco del pago 1.");
                return false;
            }
        
            if (!$('#tipotarjeta1').val()) {
                alert("Debe seleccionar el tipo de tarjeta del pago 1.");
                return false;
            }
        
            if (!$('#numerocuotas1').val() && [4, 5, 6, 8, 9].includes(parseInt($('#t1').val()))) {
                alert("Debe indicar el numero de cuotas del pago 1.");
                return false;
            }
        
            if (!$('#m1').val()) {
                alert("Debe marcar la moneda del pago 1.");
                return false;
            }
        
            if (!$('#poss1').val() && [4, 5, 6, 8, 9].includes(parseInt($('#t1').val()))) {
                alert("Debe seleccionar el POS del pago 1.");
                return false;
            }
        }


        if ($('#p2').val() != 0 || $('#t2').val() || $('#banco2').val() || $('#tipotarjeta2').val() || $('#numerocuotas2').val() || $('#m2').val() || $('#poss2').val()) {

            if (!$('#t2').val()) {
                alert("Debe seleccionar una forma de pago del pago 2.");
                return false;
            }
        
            if (!$('#banco2').val() && [4, 5, 6, 8, 9].includes(parseInt($('#t2').val()))) {
                alert("Debe seleccionar el banco del pago 2.");
                return false;
            }
        
            if (!$('#tipotarjeta2').val()) {
                alert("Debe seleccionar el tipo de tarjeta del pago 2.");
                return false;
            }
        
            if (!$('#numerocuotas2').val() && [4, 5, 6, 8, 9].includes(parseInt($('#t2').val()))) {
                alert("Debe indicar el numero de cuotas del pago 2.");
                return false;
            }
        
            if (!$('#m2').val()) {
                alert("Debe marcar la moneda del pago 2.");
                return false;
            }

            if (!$('#p2').val()) {
                alert("Debe ingresar el monto a cancelar del pago 2.");
                return false;
            }
        
            if (!$('#poss2').val() && [4, 5, 6, 8, 9].includes(parseInt($('#t2').val()))) {
                alert("Debe seleccionar el POS del pago 2.");
                return false;
            }
        }

        if ($('#p3').val() != 0 || $('#t3').val() || $('#banco3').val() || $('#tipotarjeta3').val() || $('#numerocuotas3').val() || $('#m3').val() || $('#poss3').val()) {

            if (!$('#t3').val()) {
                alert("Debe seleccionar una forma de pago del pago 3.");
                return false;
            }
        
            if (!$('#banco3').val() && [4, 5, 6, 8, 9].includes(parseInt($('#t3').val()))) {
                alert("Debe seleccionar el banco del pago 3.");
                return false;
            }
        
            if (!$('#tipotarjeta3').val()) {
                alert("Debe seleccionar el tipo de tarjeta del pago 3.");
                return false;
            }
        
            if (!$('#numerocuotas3').val() && [4, 5, 6, 8, 9].includes(parseInt($('#t3').val()))) {
                alert("Debe indicar el numero de cuotas del pago 3.");
                return false;
            }
        
            if (!$('#m3').val()) {
                alert("Debe marcar la moneda del pago 3.");
                return false;
            }
            
            if (!$('#p3').val()) {
                alert("Debe ingresar el monto a cancelar del pago 3.");
                return false;
            }
        
            if (!$('#poss3').val() && [4, 5, 6, 8, 9].includes(parseInt($('#t3').val()))) {
                alert("Debe seleccionar el POS del pago 3.");
                return false;
            }
        }

        
        var empresaTexto = $("#id_empresa option:selected").text();
        var respuesta = window.confirm( "Esta facturando para: "+ empresaTexto +" \n ¿Deseas continuar?");
        
        if (respuesta) {
            $("#form2 [name='guardar']").attr("disabled", true);
        } else {
            return false;
        }
    });
</script>