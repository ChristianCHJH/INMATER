let delete_id=0;
$(document).on("click", '.form-confirm-delete', function(event) { 
	delete_id = $(this).attr("data-id");
	$("#modal-confirm-delete").modal('show');
});

$(document).ready(function() {
	$("#close-edit").on("click", function () { location.href = 'man-cateter.php'; });
	$("#close-list").on("click", function () { location.href = 'lista-admin.php'; });
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
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-cateter.php',
			dataType: "json",
			data: {
				tipo: "add",
				nombre: document.getElementById("form-confirm-add").elements["nombre"].value,
			},
			success: function (response) {
				if (response.success) {
					load_data();
					$("#alert-success label").html(response.message);
					$('#alert-success').show();
				} else {
					$("#alert-deleted label").html(response.message);
					$('#alert-deleted').show();
				}
			},
			error: function (jqXHR, exception) { console.log(jqXHR, exception); },
			complete: function (e) { $("#modal-confirm-add").modal('hide'); }
		});
	});
	$("#delete").on("click", function () {
		$(".loader-content").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-cateter.php',
			dataType: "json",
			data: {
				tipo: "delete",
				id: delete_id
			},
			success: function (response) {
				$('#alert-success').show();
				$("#alert-success label").html(response.message);
				load_data();
			},
			error: function (jqXHR, exception) { console.log(jqXHR, exception); },
			complete: function (e) {
				$("#modal-confirm-delete").modal('hide');
				$(".loader-content").hide();
			}
		});
	});
	$("#update").on("click", function () {
		$(".loader-content").show();
		var id = getUrlParameter('id');
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-cateter.php',
			dataType: "json",
			data: {
				tipo: "update",
				nombre: document.getElementById("form-confirm-edit").elements["nombre"].value,
				id: id
			},
			success: function (response) { console.log(response); },
			error: function (jqXHR, exception) { console.log(jqXHR, exception); },
			complete: function (e) {
				$("#modal-confirm-edit").modal('hide');
				jQuery(".loader-content").hide();
			}
		});
	});

	function load_data() {
		$(".loader-content").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-cateter.php',
			dataType: "json",
			data: { tipo: "obtener_data" },
			success: function (response) {
				$('#table_main tbody').html(response.message.content);
			},
			error: function (jqXHR, exception) { console.log(jqXHR, exception); },
			complete: function() { $(".loader-content").hide(); }
		});
	}
	function load_item(id) {
		$(".loader-content").show();
		$.ajax({
			type: 'POST',
			url: '_operaciones/man-cateter.php',
			dataType: "json",
			data: {
				tipo: "get_item",
				id: id
			},
			success: function (response) {
				var data = $.parseJSON(response.message.content);
				document.getElementById("form-confirm-edit").elements["nombre"].value = data.nombre;
			},
			error: function (jqXHR, exception) { console.log(jqXHR, exception); },
			complete() { $(".loader-content").hide(); }
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
