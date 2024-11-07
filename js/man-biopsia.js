let delete_id=0;
$(document).on("click", '.form-confirm-delete', function(event) { 
	delete_id = $(this).attr("data-id");
	$("#modal-confirm-delete").modal('show');
});

$(document).ready(function() {
	$("#close").on("click", function () {
		location.href = 'lista-admin.php';
	});
	$(window).load(function (e) {
		var id = getUrlParameter('id');
		if (id) {
			load_item(id);
		} else {
			load_data();
		}
	});

	$("#form-confirm-add").submit(function (e) {
		e.preventDefault();
		$("#modal-confirm-add").modal('show');
	});
	$("#form-confirm-edit").submit(function (e) {
		e.preventDefault();
		$("#modal-confirm-edit").modal('show');
	});
	$("#add").on("click", function () {
		jQuery(".loader").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-biopsia.php',
			dataType: "json",
			data: {
				tipo: "add",
				nombre: document.getElementById("form-confirm-add").elements["nombre"].value,
			},
			success: function (response) {
				load_data();
				$('#alert-success').show();
			},
			error: function (jqXHR, exception) {
				console.log(jqXHR, exception);
			},
			complete: function (e) {
				$("#modal-confirm-add").modal('hide');
				jQuery(".loader").hide();
			}
		});
	});
	$("#delete").on("click", function () {
		jQuery(".loader").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-biopsia.php',
			dataType: "json",
			data: {
				tipo: "delete",
				id: delete_id
			},
			success: function (response) {
				load_data();
				$('#alert-deleted').show();
			},
			error: function (jqXHR, exception) {
				console.log(jqXHR, exception);
			},
			complete: function (e) {
				$("#modal-confirm-delete").modal('hide');
				jQuery(".loader").hide();
			}
		});
	});
	$("#update").on("click", function () {
		$(".loader").show();
		var id = getUrlParameter('id');
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-biopsia.php',
			dataType: "json",
			data: {
				tipo: "update",
				nombre: document.getElementById("form-confirm-edit").elements["nombre"].value,
				id: id
			},
			success: function (response) {
				location.href = 'man-biopsia.php';
			},
			error: function (jqXHR, exception) {
				console.log(jqXHR, exception);
			},
			complete: function (e) {
				$("#modal-confirm-edit").modal('hide');
				jQuery(".loader").hide();
			}
		});
	});

	$("#btn_descargar_reporte").on("click", function() {
		jQuery(".loader").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-biopsia.php',
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

	function load_data() {
		$(".loader").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-biopsia.php',
			dataType: "json",
			data: {
				tipo: "obtener_data",
			},
			success: function (response) {
				$(".loader").hide();
				$('#table_main tbody').html(response.message.content);
			},
			error: function (jqXHR, exception) {
				console.log(jqXHR, exception);
				$(".loader").hide();
			},
		});
	}
	function load_item(id) {
		$(".loader").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-biopsia.php',
			dataType: "json",
			data: {
				tipo: "get_item",
				id: id
			},
			success: function (response) {
				var data = $.parseJSON(response.message.content);
				document.getElementById("form-confirm-edit").elements["nombre"].value = data.nombre;
			},
			error: function (jqXHR, exception) {
				console.log(jqXHR, exception);
			},
			complete() {
				$(".loader").hide();
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
