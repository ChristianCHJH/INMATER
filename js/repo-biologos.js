$(document).ready(function() {
	$("#close-edit").on("click", function () {
		location.href = 'repo-biologos.php';
	});
	$("#close-list").on("click", function () {
		location.href = 'lista_facturacion.php';
	});

	$(window).load(function (e) {
		load_data();
	});

	$("#form-filters").submit(function (e) {
		e.preventDefault();
		var fecha_inicio = document.getElementById("form-filters").elements["ini"].value;
		var fecha_fin = document.getElementById("form-filters").elements["fin"].value;
		var protocolo = document.getElementById("form-filters").elements["protocolo"].value;
		let dias_semana = $("#dias_semana").chosen().val();
		load_data(fecha_inicio, fecha_fin, protocolo, dias_semana);
	});

	function load_data(fecha_inicio="", fecha_fin="", protocolo="", dias_semana=[]) {
		$(".loader-content").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/repo-biologos.php',
			dataType: "json",
			data: {
				tipo: "get-data",
				fecha_inicio: fecha_inicio,
				fecha_fin: fecha_fin,
				protocolo: protocolo,
				dias_semana: dias_semana
			},
			success: function (response) {
				$('#table-main tbody').html(response.message.content.table_main);
				$('#table-modal tbody').html(response.message.content.table_modal);
				$('#table-modal-procedimiento tbody').html(response.message.content.table_modal_procedimiento);
			},
			error: function (jqXHR, exception) {
				console.log(jqXHR, exception);
			},
			complete: function() {
				$(".loader-content").hide();
			}
		});
	}

	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');
			if (sParameterName[0] === sParam) {
				return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
		return false;
	};
});
