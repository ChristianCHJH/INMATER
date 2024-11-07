$(document).ready(function () {
  var confirmar = function (callback) {
    $("#agregar").on("click", function () {
      $("#modal_confirmar").modal('show')
    });
    $("#guardar").on("click", function () {
      $("#modal_confirmar").modal('show')
    });

    $("#modal-btn-si").on("click", function () {
      var data = {
        "id": $("#entidad_id").val(),
        "codigo": $("#codigo").val(),
        "nombre": $("#nombre").val()
      };
      callback(true, data)
      $("#modal_confirmar").modal('hide')
    });

    $("#modal-btn-no").on("click", function () {
      callback(false, []);
      $("#modal_confirmar").modal('hide')
    });
  };

  confirmar(function (confirm, data) {
    if (confirm) {
      $.ajax({
        type: 'POST',
        url: '_operaciones/transfer_ecografia.php',
        async: false,
        data: {
          tipo: 'guardar',
          data: data
        },
        dataType: "JSON",
        success: function (result) {
          console.log(result);
          if (result.message != "") {
           $("#mensaje div").html(result.message);
           jQuery("#mensaje").show();
          } else {
            location.href = "transfer_ecografia.php";
          }
        },
        error: function (jqXHR, exception) {
          var msg = '';
  
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
  
          console.log(jqXHR)
          console.log(exception)
          console.log(msg)
        },
      });
    }
  });
});