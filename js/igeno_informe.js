$(document).ready(function () {
  jQuery(window).load(function (event) {
    jQuery('.loader').fadeOut(1000);
  });
  
  $(".chosen-select").chosen();

  $('#hora, #hora_transferencia').timepicker({
      timeFormat: 'h:mm p',
      interval: 60,
      minTime: '10',
      maxTime: '6:00pm',
      startTime: '10:00',
      dynamic: false,
      dropdown: true,
      scrollbar: true
  });
});