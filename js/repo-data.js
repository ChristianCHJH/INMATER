$(document).ready(function() {
    $("#medico").chosen();

    $("#btn_descargar_reporte").on("click", function() {
        jQuery(".loader").show();
        $.ajax({
            type: 'POST',
            url: '_operaciones/repo-data.php',
            dataType: "json",
            data: {
                tipo: "descargar_reporte",
                ini: $("#ini").val(),
                fin: $("#fin").val(),
                medico: $("#medico").val(),
                medio_comunicacion: $("#medio_comunicacion").val(),
            },
            success: function (data) {
                var nombre_modulo="reporte_data";
                var ruta="perfil_laboratorio/reporte_data.php";
                var tipo_operacion="descarga_excel";
                var login=$('#login').val();
                var key=$('#key').val();
								var clave='';
								var valor='';
                $.ajax({
                    type: 'POST',
                    dataType: "json",
                    contentType: "application/json",
                    url: '_api_inmater/servicio.php',
                    data:JSON.stringify({ nombre_modulo: nombre_modulo, ruta: ruta,tipo_operacion:tipo_operacion,clave:clave,valor:valor,idusercreate:login,apikey:key }),
                    // processData: false,  // tell jQuery not to process the data
                    // contentType: false,   // tell jQuery not to set contentType
                    success: function(result) {
                        console.log(result);
                    }
                });
                jQuery(".loader").hide();
                var $a = $("<a>");
                $a.attr("href", data.file);
                $("body").append($a);
                $a.attr("download", "reporte-data.xlsx");
                $a[0].click();
                $a.remove();
            },
            error: function (jqXHR, exception) {
                jQuery(".loader").hide();
                console.log(jqXHR, exception);
            },
        });
    });

    $(document).on("click", ".obtener_data", function() {
        jQuery(".loader").show();
        $.ajax({
            type: 'POST',
            url: '_operaciones/repo-data.php',
            dataType: "json",
            data: {
                tipo: "obtener_data",
                ini: $("#ini").val(),
                fin: $("#fin").val(),
                condicion: $(this).data("condicion"),
                medico: $(this).data("medico"),
                medio_comunicacion: $(this).data("medio-comunicacion"),
            },
            success: function (result) {
                $('#ver_lista_pacientes tbody').html(result.message.content);
                jQuery(".loader").hide();
            },
            error: function (jqXHR, exception) {
                jQuery(".loader").hide();
                console.log(jqXHR, exception);
            },
        });
    });

    $("#form").submit(function(e) {

        jQuery(".loader").show();
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '_operaciones/repo-data.php',
            dataType: "json",
            data: {
                tipo: "obtener_reporte",
                ini: $("#ini").val(),
                fin: $("#fin").val(),
                medico: $("#medico").val(),
                medio_comunicacion: $("#medio_comunicacion").val(),
            },
            success: function (result) {
                var nombre_modulo="reporte_data";
                var ruta="perfil_laboratorio/reporte_data.php";
                var tipo_operacion="consulta";
                var login=$('#login').val();
                var key=$('#key').val();
								var clave='';
								var valor='';
                $.ajax({
                    type: 'POST',
                    dataType: "json",
                    contentType: "application/json",
                    url: '_api_inmater/servicio.php',
                    data:JSON.stringify({ nombre_modulo: nombre_modulo, ruta: ruta,tipo_operacion:tipo_operacion,clave:clave,valor:valor,idusercreate:login,apikey:key }),
                    // processData: false,  // tell jQuery not to process the data
                    // contentType: false,   // tell jQuery not to set contentType
                    success: function(result) {
                        console.log(result);
                    }
                });
                jQuery(".loader").hide();
                $('#table_main tbody').html(result.message.content);
            },
            error: function (jqXHR, exception) {
                jQuery(".loader").hide();
                console.log(jqXHR, exception);
            },
        });


    });
});