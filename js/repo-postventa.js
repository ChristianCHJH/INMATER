$(document).ready(function() {
	$("#btn_descargar_reporte").on("click", function() {
		jQuery(".loader").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/repo-postventa.php',
			dataType: "json",
			data: {
				tipo: "descargar_reporte",
				ini: $("#ini").val(),
				fin: $("#fin").val(),
			},
			success: function (data) {
				jQuery(".loader").hide();
				var $a = $("<a>");
				$a.attr("href", data.file);
				$("body").append($a);
				$a.attr("download", "reporte-postventa.xlsx");
				$a[0].click();
				$a.remove();
			},
			error: function (jqXHR, exception) {
				jQuery(".loader").hide();
				console.log(jqXHR, exception);
			},
		});
	});

	$("#form").submit(function(e) {
		jQuery(".loader").show();
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: '_operaciones/repo-postventa.php',
			dataType: "json",
			data: {
				tipo: "obtener_reporte",
				ini: $("#ini").val(),
				fin: $("#fin").val(),
			},
			success: function (result) {
				jQuery(".loader").hide();
				$('#table_main tbody').html(result.message.content);
			},
			error: function (jqXHR, exception) {
				jQuery(".loader").hide();
				console.log(jqXHR, exception);
			},
		});
	});
});