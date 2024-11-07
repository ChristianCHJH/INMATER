$(document).on("click", ".show-page-loading-msg", function () {
  // cancelacion de procedimiento
  if (document.querySelector('#cancela:checked') && document.querySelector('#cancela:checked').value == 1 && document.getElementById("cancela_motivo").value == "") {
    alert("Debe ingresar el motivo de la cancelación.");
    return false;
  }

  if (document.getElementById("m_tratante").value == "") {
    alert("Debe agregar Medico Tratante");
    return false;
  }

  if (document.getElementById("asesora").value == "") {
    alert("Debe agregar Asesora");
    return false;
  }

  if (document.getElementById("cupon").value == 0) {
    alert("Debe Seleccionar una sede");
    return false;
  }

  if (document.getElementById("repro").value == "") {
    alert("Debe seleccionar la reproducción asistida.");
    return false;
  }

  if (document.getElementById("m_tratante").value == "") {
    alert("Debe llenar el campo 'Medico tratante'");
    return false;
  }

  if (document.getElementById("asesora").value == "") {
    alert("Debe llenar el campo 'Asesora'");
    return false;
  }

  if (document.getElementById("mot").value == "") {
    alert("Debe llenar el campo 'Motivo de consulta'");
    return false;
  }

  if (document.getElementById("fec").value == "") {
    alert("Debe llenar el campo 'Fecha'");
    return false;
  }

  if (document.getElementById("fec_h").value == "") {
    alert("Debe llenar el campo 'Hora'");
    return false;
  }

  if (document.getElementById("fec_m").value == "") {
    alert("Debe llenar el campo 'Minuto'");
    return false;
  }

  if (document.getElementById("in_c").value != "") {
    if (document.getElementById("in_f1").value == "" || document.getElementById("in_h1").value == "" || document.getElementById("in_m1").value == "" || document.getElementById("in_f2").value == "" || document.getElementById("in_hora").value == "") {
      alert("Debe ingresar las fechas de Internamiento e Intervención");
      return false;
    }

    if (document.getElementById("in_t").value == "") {
      alert("Debe ingresar el Tipo de Intervención");
      return false;
    }
  }

  if (document.getElementById("mam").value == "Anormal") {
    if (document.getElementById("mam1").value == "") {
      alert("Debe especificar el Ex. Mama Anormal");
      return false;
    }
  }

  if (document.getElementById("cer").value == "Anormal") {
    if (document.getElementById("cer1").value == "") {
      alert("Debe especificar el Cervix Anormal");
      return false;
    }
  }

	var cancela=document.querySelector('#cancela:checked').value;
  if(cancela=='1'){
		var nombre_modulo="ginecologia";
		var ruta="perfil_medico/busqueda_paciente/paciente/reproduccion_asistida.php";
		var tipo_operacion="baja";
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
  }else{
		var nombre_modulo="ginecologia";
		var ruta="perfil_medico/busqueda_paciente/paciente/reproduccion_asistida.php";
		var tipo_operacion="actualizacion";
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
  }

  var $this = $(this),
    theme = $this.jqmData("theme") || $.mobile.loader.prototype.options.theme,
    msgText = $this.jqmData("msgtext") || $.mobile.loader.prototype.options.text,
    textVisible = $this.jqmData("textvisible") || $.mobile.loader.prototype.options.textVisible,
    textonly = !!$this.jqmData("textonly");
    html = $this.jqmData("html") || "";
  $.mobile.loading("show", {
    text: msgText,
    textVisible: textVisible,
    theme: theme,
    textonly: textonly,
    html: html
  });
}).on("click", ".hide-page-loading-msg", function () {
  $.mobile.loading("hide");
});
$(function () {
  $('#alerta').delay(10000).fadeOut('slow');
});