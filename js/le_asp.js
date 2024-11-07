$(document).ready(function () {
  $("#volver_listaprocedimientos").on("click", function (e) {
    window.location.href = 'lista_pro.php';
  })
  //busco el primer crio de la lista
  $('.tanque').on('change', function (e) {
    crio = "0";

    for (var i = 1; i <= $('#total_embriones').val(); i++) {
      if ($('#' + i).val() == "C") {
        crio = i;
        break;
      }
    }

    if (crio != "0" && this.id == "T"+crio) {
      $('.tanque').val(this.value);
    }
  });

  $('.canister').on('change', function (e) {
    crio = "0";

    for (var i = 1; i <= $('#total_embriones').val(); i++) {
      if ($('#' + i).val() == "C") {
        crio = i;
        break;
      }
    }

    if (crio != "0" && this.id == "C" + crio) {
      $('.canister').val(this.value);
    }
  });

  $('.varilla').on('change', function (e) {
    crio = "0";

    for (var i = 1; i <= $('#total_embriones').val(); i++) {
      if ($('#' + i).val() == "C") {
        crio = i;
        break;
      }
    }

    if (crio != "0" && this.id == "G" + crio) {
      $('.varilla').val(this.value);
    }
  });
});