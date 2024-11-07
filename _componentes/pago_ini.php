<script>
    $(document).ready(function() {
        $('#verificacion_reniec').hide();
        $('#ocultar').hide();
        multiSedeEmpresa($("#id_empresa").val());
        listarSedes($("#tipo_servicio").val());
        var dialog = $("#lista_codigo_atencion")
            .dialog()
            .on("keydown", function(e) {
                if (e.keyCode === $.ui.keyCode.ESCAPE) {
                    dialog.dialog("close");
                }
                e.stopPropagation();
            });

        if ($("#tot").val() > 0) {
            $("#igv").html(($("#tot").val() - $("#tot").val() / 1.18).toFixed(2));
            $("#subtot").html(($("#tot").val() / 1.18).toFixed(2));
        }

        $("#seleccionar_atencion").click(function(e) {
            $("#cli_atencion_unica_id").val(document.querySelector('input[name="seleccion_atenciones"]:checked').value);
            $("#lista_codigo_atencion").dialog("close");
        });

        $("#verificar").click(function(e) {

            tip_doc_fac = $('#tipo_documento_facturacion').val()
            rutaApi = 'https://nd-be-eva-id6qsbxfsa-uc.a.run.app/api/persona/validarpersona'

            tipDoc = 0
            switch (tip_doc_fac) {
                case '1':
                    tipDoc = 9;
                    break;
                case '2':
                    tipDoc = 1;
                    break;
                case '3':
                    tipDoc = 2;
                    break;
                case '4':
                    tipDoc = 3;
                    break;
                case '5':
                    tipDoc = 4;
                    break;
                case '6':
                    tipDoc = 8;
                    break;
            }

            sistema = window.location.pathname
            sistema = sistema.slice(1)
            usuario = '<?php echo $login;?>';
            
            $("#verificar_texto").text("verificando...");
            $("#razon").val("");
            $("#direccionfiscal").val("");
            $("#estado_contribuyente").val("");
            $("#condicion_contribuyente").val("");
            var numero_ruc = $("#ruc").val()
            if (numero_ruc !== null && numero_ruc !== '') {
                saveInteraction();
            }
            $.ajax({
                url: rutaApi,
                type: 'POST',
                data: {
                    documento: numero_ruc,
                    tipo_documento: tipDoc,
                    sistema: 'TM('+sistema+')',
                    usuario: usuario,
                    },
                contentType: 'application/x-www-form-urlencoded', 
                headers: {
                        'Authorization': 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZFVzZXIiOjEsInJvbFVzZXIiOjEsIm5hbWVVc2VyIjoiRU5ZRVJCRSBCQVJSSU9TIiwiZW1wcmVzYSI6MSwibmFtZUVtcHJlc2EiOiJGRVJUSUxJREFEIEVWQSBTLkEuQy4iLCJpYXQiOjE3MjM1NjY4MjN9.ICxsXAdf_gQeEWj3Sy0kIrA4me_qy-pIpvb1kD6qlzA'  
                    },
                success: function(data) {

                    if (data.message) {
                        alert(data.message)
                        $("#verificar_texto").text("Registro No Encontrado");
                    } else {
                        var valorD = $('#ruc').val();
                        if (tip_doc_fac == 4 && valorD.length == 11) {

                            $("#verificar_texto").text("Registro Encontrado");
                            $("#razon").val(data.razon);
                            $("#direccionfiscal").val(data.direccion);
                            $("#estado_contribuyente").val(data.estado);
                            $("#condicion_contribuyente").val(data.habido);
                            $("#razon,#ruc,#direccionfiscal").prop("readonly", true);
                            $(this).val("Verificado");
                            $('#verificacion_reniec').prop('checked', true);
                            $('#verificacion_reniec').show();
                            $("#verificar").prop("disabled", true);
                            $("#tipo_documento_facturacion").hide();

                        } else {
                            $("#verificar_texto").text("Registro Encontrado");
                            $("#razon").val(data.apellidoPaterno + " " + data.apellidoMaterno + " " + data.nombres);
                            $("#razon,#ruc,#direccionfiscal").prop("readonly", true);
                            $(this).val("Verificado");
                            $('#verificacion_reniec').prop('checked', true);
                            $('#verificacion_reniec').show();
                            $("#verificar").prop("disabled", true);
                            $("#tipo_documento_facturacion").hide();

                        }
                    }
                },
                error: function(jqXHR, exception) {
                    console.log(jqXHR, exception);
                    $("#verificar_texto").text("Registro No Encontrado");
                },
            });
        });

        $("#check_descuento").change(function() {
            if ($(this).prop("checked")) {
                $(".div_descuento").show();
            } else {
                $(".div_descuento").hide();
            }
        });

        $("#check_bolsa_plastico").change(function() {
            if ($(this).prop("checked")) {
                $(".div_bolsa").show();
            } else {
                $(".div_bolsa").hide();
            }
        });

        $("#condicion_pago_id").change(function() {
            if ($(this).val() == "2") {
                $(".contenido_fecha_vencimiento").show();
            } else {
                $(".contenido_fecha_vencimiento").hide();
            }
        });

        $("#tipo_servicio").change(function() {
            if ($(this).val() != "") {
                location.href = "pago.php?id=&t=&s=" + $(this).val();
            }
        });

        $("#verificacion_reniec").change(function() {

            $("#razon").prop("readonly", false);
            $("#ruc").prop("readonly", false);
            $("#direccionfiscal").prop("readonly", false);
            $(this).prop('checked', false);
            $(this).hide();
            $("#verificar").prop("disabled", false);
            $("#tipo_documento_facturacion").show();
            $("#verificar_texto").text("Verificar:");
        });


        $("#tip").change(function() {
            $('#direccionfiscal').val("");
            $('#condicion_contribuyente').val("");
            $('#estado_contribuyente').val("");
            $("#verificar_texto").text("Verificar:");

            $("#razon").prop("readonly", false);
            $("#ruc").prop("readonly", false);
            $("#direccionfiscal").prop("readonly", false);
            $('#verificacion_reniec').prop('checked', false);
            $('#verificacion_reniec').hide();
            if ($(this).val() == 2) {
                $('#ruc').val("");
                $('#razon').val("");
            }
            if ($(this).val() == 2 || $(this).val() == 4) {
                $('#ocultar').show();
            } else {
                $('#ocultar').hide();
            }
        });

        $("#tipo_documento_facturacion").change(function() {

            $('#ocultar').show();

        });

        $("#id_empresa").change(function() {
            multiSedeEmpresa($(this).val());
        });

        $("#t1").change(function() {
            listarPos($(this).val(),$("#id_sede").val(),1);
            limpiarPos1()
            if($(this).val() == 1){
                console.log($(this).val())
                $('#tipotarjeta1').val(3).change()
            }else{
                $('#tipotarjeta1').val(null).change();
            }
        });

        $("#t2").change(function() {
            listarPos($(this).val(),$("#id_sede").val(),2);
            limpiarPos2()
            if($(this).val() == 1){
                console.log($(this).val())
                $('#tipotarjeta2').val(3).change()
            }else{
                $('#tipotarjeta2').val(null).change();
            }

        });

        $("#t3").change(function() {
            listarPos($(this).val(),$("#id_sede").val(),3);
            limpiarPos3()
            if($(this).val() == 1){
                console.log($(this).val())
                $('#tipotarjeta3').val(3).change()
            }else{
                $('#tipotarjeta3').val(null).change();
            }

        });

        $("#id_sede").change(function() {
            limpiarPos()
            listarPos($("#t1").val(),$(this).val(),1);
            listarPos($("#t2").val(),$(this).val(),2);
            listarPos($("#t3").val(),$(this).val(),3);
        });

        $("#sede_contabilidad_id").change(function() {
            listarTarifarios($(this).val(), $("#tipo_servicio").val());
        });

        $("#tarifario_id").change(function() {
            listarProcediminetos($("#sede_contabilidad_id").val(), $(this).val(), $("#tipo_servicio").val());
        });

        function limpiarPos(){
            $("#poss1").val('0').change();
            $("#poss2").val('0').change();
            $("#poss3").val('0').change();
        }
        function limpiarPos1(){
            $("#poss1").val('0').change();
        }
        function limpiarPos2(){
            $("#poss2").val('0').change();
        }
        function limpiarPos3(){
            $("#poss3").val('0').change();
        }

        function multiSedeEmpresa(idEmpresa){
            limpiarPos()
            $.ajax({
                    type: "POST",
                    url: "_database/pago.php",
                    dataType: "json",
                    data: {
                        action: "sedeEmpresa",
                        idEmpresa: idEmpresa,
                    },
                    success: function (data) {
                        var select = $("#id_sede");

                        select.empty();

                        if(id_sede != 0){
                            select.append('<option value="" selected>SELECCIONAR</option>');
                        }else{
                            select.append('<option value="">SELECCIONAR</option>');
                        }

                        $.each(data, function (index, sede) {
                            if(id_sede == sede.id){
                                select.append('<option value="' + sede.id + '"selected>' + sede.nombre + '</option>');
                            }else{
                                select.append('<option value="' + sede.id + '">' + sede.nombre + '</option>');
                            }
                        });
                    },
                    error: function(jqXHR, exception) {
                        console.log(jqXHR, exception);
                        console.log('Error: '+exception);
                    },
                });
        }

        function listarSedes(id_tip){

            $.ajax({
                    type: "POST",
                    url: "_database/db_tools.php",
                    dataType: "json",
                    data: {
                        action: "listarSedes",
                        id_empresa: id_tip,
                    },
                    success: function (data) {
                        var select = $("#sede_contabilidad_id");
                        select.empty();
                        
                        select.append('<option value="" selected>SELECCIONAR SEDE</option>');

                        $.each(data, function (index, sede) {
                            select.append('<option value="' + sede.id + '">('+ sede.codigo +') ' + sede.nombre.toUpperCase() + '</option>');
                        });

                        $('#sede_contabilidad_id').trigger("chosen:updated");
                    },
                    error: function(jqXHR, exception) {
                        console.log(jqXHR, exception);
                        console.log('Error: '+exception);
                    },
                });

        }

        function listarTarifarios(id_sede, id_tip){

            $.ajax({
                    type: "POST",
                    url: "_database/db_tools.php",
                    dataType: "json",
                    data: {
                        action: "listarTarifarios",
                        id_sede: id_sede,
                        id_tip: id_tip
                    },
                    success: function (data) {
                        var select = $("#tarifario_id");
                        select.empty();

                        select.append('<option value="" selected>SELECCIONAR TARIFARIO</option>');

                        $.each(data, function (index, tarifario) {
                            select.append('<option value="' + tarifario.id + '">' + tarifario.nombre.toUpperCase() + '</option>');
                        });

                        $('#tarifario_id').trigger("chosen:updated");
                    },
                    error: function(jqXHR, exception) {
                        console.log(jqXHR, exception);
                        console.log('Error: '+exception);
                    },
                });
        }

        function listarProcediminetos(id_sede, id_tarifario, id_tip){

            $.ajax({
                    type: "POST",
                    url: "_database/db_tools.php",
                    dataType: "json",
                    data: {
                        action: "listarProcedimientos",
                        id_sede: id_sede,
                        id_tarifario: id_tarifario,
                        id_tip: id_tip
                    },
                    success: function (data) {
                        var select = $("#procedimiento_id");
                        select.empty();

                        select.append('<option value="" selected>SELECCIONAR PROCEDIMIENTO</option>');

                        $.each(data, function (index, procedimiento) {
                            select.append('<option value="' + procedimiento.id + '">' + procedimiento.nombre.toUpperCase() + '</option>');
                        });

                        $('#procedimiento_id').trigger("chosen:updated");
                    },
                    error: function(jqXHR, exception) {
                        console.log(jqXHR, exception);
                        console.log('Error: '+exception);
                    },
                });
        }

        function listarPos(tipoPago,sede,i){

            if(!sede){ sede = 0}

            if (!['4', '5', '6', '8'].includes(tipoPago) && tipoPago == "") {
                return false;
            }

            $.ajax({
                    type: "POST",
                    url: "_database/db_tools.php",
                    dataType: "json",
                    data: {
                        action: "listarPos",
                        idTipoTarjeta: tipoPago,
                        id_sede: sede,
                        accion: 0,
                    },
                    success: function (data) {
                        console.log(data)

                        var select = $("#poss"+i);

                        select.empty();

                        select.append('<optgroup label="Seleccionar">');
                        $.each(data, function (index, pos) {
                            select.append('<option value="' + pos.id + '" title = "' + pos.codigo + '">'+ pos.id + ' - ' + pos.nombrepos + ' - ' + pos.moneda + '</option>');
                        });

                        $("#poss"+i).val('').change();
                        
                    },
                    error: function(jqXHR, exception) {
                        console.log(jqXHR, exception);
                        console.log('Error: '+exception);
                    },
                });
        }

        $("#codigo_atencion_buscar").click(function(e) {
            var codigo_atencion_id = $("#codigo_atencion_id").val();
            if (codigo_atencion_id != "") {
                $.ajax({
                    type: "POST",
                    url: "_operaciones/cli_atencion_unica.php",
                    dataType: "json",
                    data: {
                        tipo: "buscar_atencion",
                        codigo: codigo_atencion_id,
                    },
                    success: function(data) {
                        if (data.message && data.message.content != "") {
                            data = data.message;
                            $.mobile.changePage("#lista_codigo_atencion", "pop", true, true);
                            $("#lista_codigo_atencion").focus();
                            $("#table_codigo_atencion tbody").html(data.content);
                            $("#cli_atencion_unica_id").val(data.cli_atencion_unica_id);
                            $(".carga_paci .ui-input-search input").attr("id", "paci_nom");
                            $("#paci_nom").prop("required", true);
                            $("#paci_nom").val(data.paciente);
                            $("#nom").val(data.paciente);
                            $("#dni").val(data.documento);
                            $("#ruc").val(data.documento);
                            $("#correo_electronico").val(data.correo_electronico);
                            $("#paci_nom").textinput("refresh");
                            $(".fil_paci li").addClass("ui-screen-hidden");
                            $("#paci_nom").focus();
                            $("#med").val(data.medico).change();
                            $("#sede").val(data.sede).change();
                            $("#usuario_encontrado").html(data.medios_comunicacion);
                            document.getElementById("usuario_encontrado").style.color = data.medios_comunicacion_color;
                        } else {
                            $("#codigo_atencion_id").val("");
                            $("#cli_atencion_unica_id").val("");
                            $("#usuario_encontrado").html("");
                        }
                    },
                    error: function(jqXHR, exception) {
                        console.log(jqXHR, exception);
                    },
                });
            }
        });

        $("#razon").change(function() {
            if ($(this).val() != "") {
                var data = $(this).val().split("|");

                if (data.length == 3) {
                    $(this).val(data[0]);
                    $("#ruc").val(data[1]);
                    $("#direccionfiscal").val(data[2]);
                }
            }
        });

        function saveInteraction() {
            var ruta = "pago.php";
            var usercreate = <?php echo json_encode($login); ?>;
            var tip_doc = $('#tipo_documento_facturacion').val();
            var documento = $('#ruc').val();

            $.ajax({
                url: "/_database/pago.php",
                type: "POST",
                data: {
                    action: "guardarUsuario",
                    ruta: ruta,
                    usercreate: usercreate,
                    tip_doc: tip_doc,
                    documento: documento
                },
                success: function(response) {
                    var jsonResponse = JSON.parse(response);

                    if (jsonResponse.status === "success") {
                        console.log("Éxito: " + jsonResponse.message);
                    } else {
                        console.log("Error: " + jsonResponse.message);
                    }
                }
            });
        }
    });
</script>