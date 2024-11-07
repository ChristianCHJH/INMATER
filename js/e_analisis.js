$(document).ready(function () {
  $("#a_sta option").hide();
  $("#a_sta option[value=Positivo]").show();
  $("#a_sta option[value=Negativo]").show();

  $('#a_exa').on('change', function () {
    if (this.value == "Fragmentación de ADN espermático") {
      $(".idf_resultado").show();
    } else {
      $(".idf_resultado").hide();
    }

    if (this.value == "ERA") {
      $("#a_sta option").show();
      $("#a_sta option[value=Positivo]").hide();
      $("#a_sta option[value=Negativo]").hide();
    } else {
      $("#a_sta option").hide();
      $("#a_sta option[value=Positivo]").show();
      $("#a_sta option[value=Negativo]").show();
    }
  });

});