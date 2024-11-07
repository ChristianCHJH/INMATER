$(document).ready(function () {
    console.log('demo')
});

$(document).on('click', '#tanque_descarga', function (e) {
    /* e.preventDefault(); */
    var data = $('#tanque_descarga').serializeArray();

    $.ajax({
        type: 'POST',
        url: '_operaciones/tanque_descarga.php',
        async: false,
        data: {
            tipo: "guardar_tanque",
            data: data
        },
        dataType: "JSON",
        success: function (result) {
            /* $("#buscar_tanque").after(result.message) */
            console.log(result)
        },
        error: function (jqXHR, exception) {
            var msg = '';
            console.log(jqXHR)
            console.log(exception)

            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            /* $('#post').html(msg); */
        },
    });
});