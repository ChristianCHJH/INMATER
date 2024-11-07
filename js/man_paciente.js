$(document).ready(function () {
  var confirmar = function (callback) {
    $("#guardar").on("click", function (e) {
      e.preventDefault();
			$("#modal_editar").modal('show');
    });

    $("#modal-btn-si").on("click", function () {
      callback()
      $("#modal_editar").modal('hide')
    });

    $("#modal-btn-no").on("click", function () {
      callback()
      $("#modal_editar").modal('hide')
    });
  };

	function objectifyForm(formArray) {
    var returnArray = {};
    for (var i = 0; i < formArray.length; i++){
			returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
	}

  confirmar(function () {
    if (confirm) {
      $.ajax({
        type: 'POST',
        url: '_operaciones/man_paciente.php',
        async: false,
        data: {
          tipo: 'actualizar_sede',
					data: objectifyForm($('#form_paciente').serializeArray())
        },
        dataType: "JSON",
        success: function (result) {
          location.reload();
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
  
          console.log(msg)
        },
      });
    }
  });
});