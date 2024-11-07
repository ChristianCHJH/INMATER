let delete_id = 0;
$(document).on("click", ".form-confirm-delete", function (event) {
    delete_id = $(this).attr("data-id");
    $("#modal-confirm-delete").modal("show");
});

$(document).ready(function () {
    $("#close-edit").on("click", function () {
        location.href = "man-tipo-cambio.php";
    });
    $("#close-list").on("click", function () {
        location.href = "lista_facturacion.php";
    });

    $(window).load(function (e) {
        var id = getUrlParameter("id");
        if (id) {
            load_item(id);
        } else {
            load_data();
        }
    });

    $("#form-filtros").submit(function (e) {
        e.preventDefault();
        $(".loader-content").show();
        $(".card-body1").hide();
        $.ajax({
            type: "POST",
            url: "_operaciones/seguimiento-betas.php",
            dataType: "json",
            data: {
                tipo: "obtener_data",
                tipo_fecha: $("[name='tipo_fecha']:checked").val(),
                ini: $("#ini").val(),
                fin: $("#fin").val(),
            },
            success: function (response) {
                $("#table_main tbody").html(response.message.content);
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR, exception);
            },
            complete: function () {
                $(".loader-content").hide();
                $(".card-body1").show();
            },
        });
    });
    $("#form-confirm-edit").submit(function (e) {
        e.preventDefault();
        $("#modal-confirm-edit").modal("show");
    });
    $("#add").on("click", function () {
        $.ajax({
            type: "POST",
            url: "_operaciones/man-tipo-cambio.php",
            dataType: "json",
            data: {
                tipo: "add",
                fecha: document.getElementById("form-confirm-add").elements["fecha"].value,
                tipo_cambio_compra: document.getElementById("form-confirm-add").elements["tipo_cambio_compra"].value,
                tipo_cambio_venta: document.getElementById("form-confirm-add").elements["tipo_cambio_venta"].value,
            },
            success: function (response) {
                if (response.success) {
                    load_data();
                    $("#alert-success label").html(response.message);
                    $("#alert-success").show();
                } else {
                    $("#alert-deleted label").html(response.message);
                    $("#alert-deleted").show();
                }
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR, exception);
            },
            complete: function (e) {
                $("#modal-confirm-add").modal("hide");
            },
        });
    });
    $("#delete").on("click", function () {
        $(".loader-content").show();
        $.ajax({
            type: "POST",
            url: "_operaciones/man-tipo-cambio.php",
            dataType: "json",
            data: {
                tipo: "delete",
                id: delete_id,
            },
            success: function (response) {
                $("#alert-success").show();
                $("#alert-success label").html(response.message);
                load_data();
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR, exception);
            },
            complete: function (e) {
                $("#modal-confirm-delete").modal("hide");
                $(".loader-content").hide();
            },
        });
    });
    $("#update").on("click", function () {
        $(".loader-content").show();
        var id = getUrlParameter("id");
        $.ajax({
            type: "POST",
            url: "_operaciones/man-tipo-cambio.php",
            dataType: "json",
            data: {
                tipo: "update",
                fecha: document.getElementById("form-confirm-edit").elements["fecha"].value,
                tipo_cambio_compra: document.getElementById("form-confirm-edit").elements["tipo_cambio_compra"].value,
                tipo_cambio_venta: document.getElementById("form-confirm-edit").elements["tipo_cambio_venta"].value,
                id: id,
            },
            success: function (response) {
                /* location.href = 'man-tipo-cambio.php'; */
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR, exception);
            },
            complete: function (e) {
                $("#modal-confirm-edit").modal("hide");
                jQuery(".loader-content").hide();
            },
        });
    });

    function load_data() {
        $(".loader-content").show();
        $(".card-body1").hide();
        $.ajax({
            type: "POST",
            url: "_operaciones/seguimiento-betas.php",
            dataType: "json",
            data: {
                tipo: "obtener_data",
                tipo_fecha: $("[name='tipo_fecha']:checked").val(),
                ini: $("#ini").val(),
                fin: $("#fin").val(),
            },
            success: function (response) {
                $("#table_main tbody").html(response.message.content);
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR, exception);
            },
            complete: function () {
                $(".loader-content").hide();
                $(".card-body1").show();
            },
        });
    }
    function load_item(id) {
        $(".loader-content").show();
        $.ajax({
            type: "POST",
            url: "_operaciones/man-tipo-cambio.php",
            dataType: "json",
            data: {
                tipo: "get_item",
                id: id,
            },
            success: function (response) {
                var data = $.parseJSON(response.message.content);
                document.getElementById("form-confirm-edit").elements["fecha"].value = data.fecha;
                document.getElementById("form-confirm-edit").elements["tipo_cambio_compra"].value =
                    data.tipo_cambio_compra;
                document.getElementById("form-confirm-edit").elements["tipo_cambio_venta"].value =
                    data.tipo_cambio_venta;
            },
            error: function (jqXHR, exception) {
                console.log(jqXHR, exception);
            },
            complete() {
                $(".loader-content").hide();
            },
        });
    }

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split("&"),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split("=");
            if (sParameterName[0] === sParam) {
                return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };
});
