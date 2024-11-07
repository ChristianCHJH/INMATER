$(".chosen-select").chosen();

$(document).on("click", "#agregar", function () {
  var SERVICE_MIFACT = $('#SERVICE_MIFACT').val();
  var TOKEN = $('#TOKEN').val();
  var COD_TIP_NIF_EMIS = $('#COD_TIP_NIF_EMIS').val();
  var NUM_NIF_EMIS = $('#NUM_NIF_EMIS').val();
  var NOM_COMER_EMIS = $('#NOM_COMER_EMIS').val();
  var COD_UBI_EMIS = $('#COD_UBI_EMIS').val();
  var TXT_DMCL_FISC_EMIS = $('#TXT_DMCL_FISC_EMIS').val();
  var ENVIAR_A_SUNAT = $('#ENVIAR_A_SUNAT').val();
  var estado = $('#estado').val();
  var NOM_RZN_SOC_EMIS = $('#NOM_RZN_SOC_EMIS').val();

  if (SERVICE_MIFACT == "") {
    alert("El campo de servicio mi fact no puede estar en blanco.");
    return;
  }

  if (TOKEN == "") {
    alert("El campo token no puede estar en blanco.");
    return;
  }

  if (COD_TIP_NIF_EMIS == "") {
    alert("El campo Código tipo nif emisor no puede estar en blanco.");
    return;
  }

  if (NUM_NIF_EMIS == "") {
    alert("El campo Número nif del emisor no puede estar en blanco.");
    return;
  }

  if (NOM_COMER_EMIS == "") {
    alert("El campo Nombre comercial emisor no puede estar en blanco.");
    return;
  }

  if (COD_UBI_EMIS == "") {
    alert("El campo Código ubicación emisor no puede estar en blanco.");
    return;
  }
  if (TXT_DMCL_FISC_EMIS == "") {
    alert("El campo Domicilio fiscal del emisor no puede estar en blanco.");
    return;
  }
  if (ENVIAR_A_SUNAT == "") {
    alert("El campo Enviar a sunat no puede estar en blanco.");
    return;
  }
  if (estado == "") {
    alert("El campo estadp no puede estar en blanco.");
    return;
  }
  if (NOM_RZN_SOC_EMIS == "") {
    alert("El campo Nombre razón social emisor no puede estar en blanco.");
    return;
  }

  $.ajax({
    type: 'POST',
    url: '_operaciones/empresas_man.php',
    async: false,
    data: {
      tipo_operacion: "agregar",
      SERVICE_MIFACT:SERVICE_MIFACT,
      TOKEN:TOKEN,
      COD_TIP_NIF_EMIS:COD_TIP_NIF_EMIS,
      NUM_NIF_EMIS:NUM_NIF_EMIS,
      NOM_COMER_EMIS:NOM_COMER_EMIS,
      COD_UBI_EMIS:COD_UBI_EMIS,
      TXT_DMCL_FISC_EMIS:TXT_DMCL_FISC_EMIS,
      ENVIAR_A_SUNAT:ENVIAR_A_SUNAT,
      estado:estado,
      NOM_RZN_SOC_EMIS:NOM_RZN_SOC_EMIS
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
  var id = $('#empresa_id').val();
  var SERVICE_MIFACT = $('#SERVICE_MIFACT').val();
  var TOKEN = $('#TOKEN').val();
  var COD_TIP_NIF_EMIS = $('#COD_TIP_NIF_EMIS').val();
  var NUM_NIF_EMIS = $('#NUM_NIF_EMIS').val();
  var NOM_COMER_EMIS = $('#NOM_COMER_EMIS').val();
  var COD_UBI_EMIS = $('#COD_UBI_EMIS').val();
  var TXT_DMCL_FISC_EMIS = $('#TXT_DMCL_FISC_EMIS').val();
  var ENVIAR_A_SUNAT = $('#ENVIAR_A_SUNAT').val();
  var estado = $('#estado').val();
  var NOM_RZN_SOC_EMIS = $('#NOM_RZN_SOC_EMIS').val();

  if (SERVICE_MIFACT == "") {
    alert("El campo de servicio mi fact no puede estar en blanco.");
    return;
  }

  if (TOKEN == "") {
    alert("El campo token no puede estar en blanco.");
    return;
  }

  if (COD_TIP_NIF_EMIS == "") {
    alert("El campo Código tipo nif emisor no puede estar en blanco.");
    return;
  }

  if (NUM_NIF_EMIS == "") {
    alert("El campo Número nif del emisor no puede estar en blanco.");
    return;
  }

  if (NOM_COMER_EMIS == "") {
    alert("El campo Nombre comercial emisor no puede estar en blanco.");
    return;
  }

  if (COD_UBI_EMIS == "") {
    alert("El campo Código ubicación emisor no puede estar en blanco.");
    return;
  }
  if (TXT_DMCL_FISC_EMIS == "") {
    alert("El campo Domicilio fiscal del emisor no puede estar en blanco.");
    return;
  }
  if (ENVIAR_A_SUNAT == "") {
    alert("El campo Enviar a sunat no puede estar en blanco.");
    return;
  }
  if (estado == "") {
    alert("El campo estadp no puede estar en blanco.");
    return;
  }
  if (NOM_RZN_SOC_EMIS == "") {
    alert("El campo Nombre razón social emisor no puede estar en blanco.");
    return;
  }
  $.ajax({
    type: 'POST',
    url: '_operaciones/empresas_man.php',
    async: false,
    data: {
      tipo_operacion: "actualizar",
      id: id,
      SERVICE_MIFACT:SERVICE_MIFACT,
      TOKEN:TOKEN,
      COD_TIP_NIF_EMIS:COD_TIP_NIF_EMIS,
      NUM_NIF_EMIS:NUM_NIF_EMIS,
      NOM_COMER_EMIS:NOM_COMER_EMIS,
      COD_UBI_EMIS:COD_UBI_EMIS,
      TXT_DMCL_FISC_EMIS:TXT_DMCL_FISC_EMIS,
      ENVIAR_A_SUNAT:ENVIAR_A_SUNAT,
      estado:estado,
      NOM_RZN_SOC_EMIS:NOM_RZN_SOC_EMIS
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

function cambiarestado(id,estado) {
  if (confirm('¿Realmente desea cambiar de estado?')) {
    $.ajax({
      type: 'POST',
      url: '_operaciones/empresas_man.php',
      async: false,
      data: {
        tipo_operacion: "cambiarestado",
        id: id,
        estado:estado,
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