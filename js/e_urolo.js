$(document).on("click", ".show-page-loading-msg", function () {
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
  if (document.getElementById("in_t").value != "") {
    if (document.getElementById("in_f2").value == "" || document.getElementById("in_hora").value == "") {
      alert("Debe ingresar la fecha Intervención");
      return false;
    }
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
  $('#alerta').delay(4000).fadeOut('slow');
});