$(".chosen-select").chosen();

$(document).on("click", "#agregar_centrocosto", function () {
  console.log("agregar_centrocosto")
  var sede = $('#sede').val();
  var codigo = $('#codigo').val();
  var nombre = $('#nombre').val();

  if (sede == "") {
    alert("Debe seleccionar una sede.");
    return;
  }

  if (codigo == "") {
    alert("El campo código no puede estar en blanco.");
    return;
  }

  if (nombre == "") {
    alert("El campo nombre no puede estar en blanco.");
    return;
  }

  $.ajax({
    type: 'POST',
    url: '_operaciones/conta_centrocosto.php',
    async: false,
    data: {
      tipo_operacion: "agregar",
      sede: sede,
      codigo: codigo,
      nombre: nombre
    },
    dataType: "JSON",
    success: function (result) {
      alert(result.message)
      location.reload()
    },
    error: function (jqXHR, exception) {
      var msg = '';
      /* console.log(jqXHR)
      console.log(exception) */

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

      /* console.log(msg) */
    },
  });
});

$(document).on("click", "#actualizar", function () {
  var id = $('#centrocosto_id').val();
  var sede = $('#sede').val();
  var codigo = $('#codigo').val();
  var nombre = $('#nombre').val();

  if (sede == "") {
    alert("Debe seleccionar una sede.");
    return;
  }

  if (codigo == "") {
    alert("El campo código no puede estar en blanco.");
    return;
  }

  if (nombre == "") {
    alert("El campo nombre no puede estar en blanco.");
    return;
  }

  $.ajax({
    type: 'POST',
    url: '_operaciones/conta_centrocosto.php',
    async: false,
    data: {
      tipo_operacion: "actualizar",
      id: id,
      sede: sede,
      codigo: codigo,
      nombre: nombre
    },
    dataType: "JSON",
    success: function (result) {
      alert(result.message)
      location.reload()
    },
    error: function (jqXHR, exception) {
      var msg = '';
      /* console.log(jqXHR)
      console.log(exception) */

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

      /* console.log(msg) */
    },
  });
});

function myFunction() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("tb_servicios");
  tr = table.getElementsByTagName("tr");

  for (i = 1; i < tr.length; i++) {
    var encontro = false;

    for (var j = 0; j < 10; j++) {
      td = tr[i].getElementsByTagName("td")[j];

      if (td) {
        if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
          encontro = true; break;
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

function eliminar(id) {
  if (confirm('¿Realmente desea eliminar esta información?')) {
    $.ajax({
      type: 'POST',
      url: '_operaciones/conta_centrocosto.php',
      async: false,
      data: {
        tipo_operacion: "eliminar",
        id: id,
      },
      dataType: "JSON",
      success: function (result) {
        alert(result.message)
        location.reload()
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
}