$(document).ready(function () {
    $(".loader-content").hide();
    $("#pro_descargar").val($("#protocolo").val());
    $(".mostrar").change(function () {
        $("#protocolo").val("");
        $("#ini").val($("#date_ini").val());
        $("#fin").val($("#date_fin").val());
    });
    $("#protocolo").change(function () {
        $(".mostrar").val("");
    });
    $(document).on("input paste", "#protocolo", function (e) {
        $("#pro_descargar").val($("#protocolo").val());
    });
    $("#btn_modal_cargardata").click(function (e) {
        $("#btn_cargar_data").removeAttr("disabled");
        $("#btn_cargar_data").text("Cargar Data");
    });

    $("#form-filters").submit(function (e) {
        e.preventDefault();
        var fecha_inicio = document.getElementById("form-filters").elements["fecha_inicio"].value;
        var fecha_fin = document.getElementById("form-filters").elements["fecha_fin"].value;
        var protocolo = document.getElementById("form-filters").elements["protocolo"].value;
        load_data(fecha_inicio, fecha_fin, protocolo);
    });

    function load_data(fecha_inicio, fecha_fin, protocolo) {
        $(".loader-content").show();
        $.ajax({
            type: "POST",
            url: "_operaciones/repo-base-general.php",
            dataType: "json",
            data: {
                tipo: "get-data",
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                protocolo: protocolo,
            },
            success: function (response) {
                jQuery(".loader").hide();
                if (response.message) {
                    $("#alert-error").show();
                    $("#alert-error label").html(response.message);
                    $("#report tbody").html("");
                } else {
                    $("#report tbody").html(response.table);
                }
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR, exception);
            },
            complete: function () {
                $(".loader-content").hide();
            },
        });
    }

    $("#btn-descargar-reporte").on("click", function () {
        var fecha_inicio = document.getElementById("form-filters").elements["fecha_inicio"].value;
        var fecha_fin = document.getElementById("form-filters").elements["fecha_fin"].value;
        var protocolo = document.getElementById("form-filters").elements["protocolo"].value;
        $(".loader-content").show();
        $.ajax({
            type: "POST",
            url: "_operaciones/repo-base-general.php",
            dataType: "json",
            data: {
                tipo: "descargar-reporte",
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                protocolo: protocolo,
            },
            success: function (data) {
                var $a = $("<a>");
                $a.attr("href", data.file);
                $("body").append($a);
                $a.attr("download", "reporte-base-general.xlsx");
                $a[0].click();
                $a.remove();
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR, exception);
            },
            complete: function () {
                $(".loader-content").hide();
            },
        });
    });
});
