<script>
$(document).ready(function() {
	$('#medicamento_1').on('change', function() {
		if (this.value == 0) {
			$("#con1_med0").show();
		} else {
			$("#con1_med0").hide();
		}
	});

	$('#medicamento_2').on('change', function() {
		if (this.value == 0) {
			$("#con2_med0").show();
		} else {
			$("#con2_med0").hide();
		}
	});

	$('#medicamento_3').on('change', function() {
		if (this.value == 0) {
			$("#con3_med0").show();
		} else {
			$("#con3_med0").hide();
		}
	});

	$('#medicamento_4').on('change', function() {
		if (this.value == 0) {
			$("#con4_med0").show();
		} else {
			$("#con4_med0").hide();
		}
	});

	$('#medicamento_5').on('change', function() {
		if (this.value == 0) {
			$("#con5_med0").show();
		} else {
			$("#con5_med0").hide();
		}
	});

	$('#medicamento_6').on('change', function() {
		if (this.value == 0) {
			$("#con6_med0").show();
		} else {
			$("#con6_med0").hide();
		}
	});

	$('#medicamento_7').on('change', function() {
		if (this.value == 0) {
			$("#con7_med0").show();
		} else {
			$("#con7_med0").hide();
		}
	});

	if ($('#p_icsi').prop('checked')) {
		$('#p_fiv').checkboxradio("disable");
	}

	if ($('#p_fiv').prop('checked')) {
		$('#p_icsi').checkboxradio("disable");
	}

	$("#p_fiv").change(function() {
		if ($(this).prop('checked')) {
			$('#p_icsi').val("");
			$('#p_icsi').checkboxradio("disable");
		} else {
			$('#p_icsi').checkboxradio("enable");
		}
	});

	$("#p_icsi").change(function() {
		if ($(this).prop('checked')) {
			$('#p_fiv').val("");
			$('#p_fiv').checkboxradio("disable");
		} else {
			$('#p_fiv').checkboxradio("enable");
		}
	});

	$("#repro_lista").change(function() {
		var str = $('#repro').val();
		var items = $(this).val();
		var n = str.indexOf(items);

		if (n == -1) {
			// no agrega duplicados
			$('#repro').val(items + ", " + str);
			$('#repro').textinput('refresh');
		}

		if (items == "borrar_p") {
			$('#repro').val("");
		}

		if (items == "NINGUNA") {
			$('#repro').val("NINGUNA");
		}

		$(this).prop('selectedIndex', 0);
		$(this).selectmenu("refresh", true);
	});

	// Form Submit
	$(document).on("submit", "form", function(event) {
		$(window).off('beforeunload'); // disable unload warning
	});

	$('.numeros').keyup(function() {
		var $th = $(this);

		$th.val($th.val().replace(/[^0-9,.]/g, function(str) {
			return '';
		}));
	});

	$("#p_iiu").on('click', function() {
		$(this).prop('checked', !$(this).is(':checked'));
	});

	$(".med_insert").change(function() {
		var med = $(this).attr("title");
		var str = $('#' + med).val();
		var items = $(this).val();
		var n = str.indexOf(items);

		if (n == -1) { // no agrega duplicados
			$('#' + med).val(items + ", " + str);
			$('#' + med).textinput('refresh');
		}

		if (items == "borrar_p") {
			$('#' + med).val("");
			$("#f_asp").val(''); // fec descongelacion
		}

		$(this).prop('selectedIndex', 0);
		$(this).selectmenu("refresh", true);
	});

	// calculo de fechas de aspiracion o descogelacion
	$(".inyeccion").on("change", function() {
		var date_segunda_programacion = new Date(($("#f_iny").val() + " " + $("#h_iny").val()).replace(/-/g, "/"));

		if (!isNaN(date_segunda_programacion.getTime())) {
			var dia = parseInt($("#des_dia").val());
			if (isNaN(dia)) {
				date_segunda_programacion.setHours(date_segunda_programacion.getHours() + 36); // 36 horas
			} else {
				//if (dia==0 && $("#blasto").val()==1) date_segunda_programacion.setHours(date_segunda_programacion.getHours()-120); // -5 dias (5 x24) horas
				//if (dia==0 && $("#blasto").val()!=1) date_segunda_programacion.setHours(date_segunda_programacion.getHours()-72); // -3 dias (3 x24) horas
				if (dia == 0) date_segunda_programacion.setHours(date_segunda_programacion.getHours()); // El mismo dia Inseminacion (ocultar)
				if (dia == 1) date_segunda_programacion.setHours(date_segunda_programacion.getHours() - 96); // -4 dias (4 x24) horas
				if (dia == 2) date_segunda_programacion.setHours(date_segunda_programacion.getHours() - 72); // -3 dias (3 x24) horas
				if (dia == 3) date_segunda_programacion.setHours(date_segunda_programacion.getHours() - 48); // -2 dias (2 x24) horas
				if (dia == 4) date_segunda_programacion.setHours(date_segunda_programacion.getHours() - 24); // -1 dias (1 x24) horas
			}

			var hoy = new Date();
			hoy.setDate(hoy.getDate() + 1);
			var dia_next = hoy.getDate() + '-' + (hoy.getMonth() + 1) + '-' + hoy.getFullYear();
			var dia_aspi = date_segunda_programacion.getDate() + '-' + (date_segunda_programacion.getMonth() + 1) + '-' + date_segunda_programacion.getFullYear();
			var programacion = $("#fecha_programacion").val();

			if (hoy.getHours() >= programacion && dia_next == dia_aspi) {
				alert("Sólo puede agendar para mañana hasta las " + programacion + " horas de hoy.")
				$("#f_asp").val("")
				$("#f_asp1").val("")
				$("#h_asp1").val("")
			} else {
				$("#f_asp").val(date_segunda_programacion.toInputFormat());
				$("#f_asp1").val(date_segunda_programacion.toInputFormatDate());
				$("#h_asp1").val(date_segunda_programacion.toInputFormatHour());
			}
			$("#f_asp").focus();
		} else {
			$("#f_asp").val("")
			$("#f_asp1").val("")
			$("#h_asp1").val("")
		}
		$("#h_asp1").selectmenu("refresh");
	});

	$(".inyeccion1").on("change", function() {
		var date_primera_programacion = new Date(($("#f_asp1").val() + " " + $("#h_asp1").val()).replace(/-/g, "/"));

		if (!isNaN(date_primera_programacion.getTime())) {
			var dia = parseInt($("#des_dia").val());
			var date_segunda_programacion = new Date(date_primera_programacion)

			if (isNaN(dia)) {
				date_segunda_programacion.setHours(date_primera_programacion.getHours() - 36); // 36 horas
			} else {
				if (dia == 0) date_segunda_programacion.setHours(date_primera_programacion.getHours()); // El mismo dia Inseminacion/Ocultar
				if (dia == 1) date_segunda_programacion.setHours(date_primera_programacion.getHours() - 96); // -4 dias (4 x24) horas
				if (dia == 2) date_segunda_programacion.setHours(date_primera_programacion.getHours() - 72); // -3 dias (3 x24) horas
				if (dia == 3) date_segunda_programacion.setHours(date_primera_programacion.getHours() - 48); // -2 dias (2 x24) horas
				if (dia == 4) date_segunda_programacion.setHours(date_primera_programacion.getHours() - 24); // -1 dias (1 x24) horas
			}

			var dia_siguiente = new Date();
			dia_siguiente.setDate(dia_siguiente.getDate() + 1);
			var dia_next = dia_siguiente.getDate() + '-' + (dia_siguiente.getMonth() + 1) + '-' + dia_siguiente.getFullYear();
			var dia_aspi = date_primera_programacion.getDate() + '-' + (date_primera_programacion.getMonth() + 1) + '-' + date_primera_programacion.getFullYear();
			var programacion = $("#fecha_programacion").val();

			if (dia_siguiente.getHours() >= programacion && dia_next == dia_aspi) {
				alert("Sólo puede agendar para mañana hasta las " + programacion + " horas de hoy.")
				$("#f_asp").val("");
				$("#f_iny").val("");
				$("#h_iny").val("");
				$("#segunda_programacion").val("");
			} else {
				$("#f_asp").val(date_primera_programacion.toInputFormat());
				$("#f_iny").val(date_segunda_programacion.toInputFormatDate());
				$("#h_iny").val(date_segunda_programacion.toInputFormatHour());
				$("#segunda_programacion").val(date_segunda_programacion.toInputFormatDate() + " " + date_segunda_programacion.toInputFormatHour());
			}
		} else {
			$("#f_iny").val("");
			$("#h_iny").val("");
			$("#segunda_programacion").val("");
		}
		$("#h_iny").selectmenu("refresh");
	});

	Date.prototype.toInputFormatDate = function() {
		var yyyy = this.getFullYear().toString();
		var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
		var dd = this.getDate().toString();
		return yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0]);
	};

	Date.prototype.toInputFormatHour = function() {
		var hh = this.getHours().toString();
		var mi = this.getMinutes().toString();
		return (hh[1] ? hh : "0" + hh[0]) + ":" + (mi[1] ? mi : "0" + mi[0]);
	};

	Date.prototype.toInputFormat = function() {
		var yyyy = this.getFullYear().toString();
		var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
		var dd = this.getDate().toString();
		var hh = this.getHours().toString();
		var mi = this.getMinutes().toString();
		return yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0]) + "T" + (hh[1] ? hh : "0" + hh[0]) + ":" + (mi[1] ? mi : "0" + mi[0]);
	};

	// andrologia
	$(".hetes").hide();

	$("#hete_chk").change(function() {
		if ($(this).prop('checked')) {
			$(".hetes").show();
			$(".hetes2,.ui-input-search").show();
			$(".sel_het").show();
			$(this).val(1);
		} else {
			$(".hetes").hide();
			$("#p_dni_het").val("");
			$(this).val(0);
		}
	});

	$(".sel_het").click(function() {
		$(".hetes2,.ui-input-search").hide();
		hete_id = $(this).attr("title");
		$("#" + hete_id).show();
		$(this).hide();
		$("#p_dni_het").val(hete_id);
	});
});

$(document).on("click", ".show-page-loading-msg", function() {
	if (document.getElementById("des_dia").value == "" && document.getElementById("anestesia") && document.getElementById("anestesia").value == "") {
		alert("Debe indicar si es un procedimiento bajo anestesia.");
		return false;
	}

	if (document.getElementById("con_iny0") && !$('#p_iiu').prop('checked')) {
		if (document.getElementById("con_iny0").value != "" && (document.getElementById("idturno").value == "" || document.getElementById("f_iny").value == "" || document.getElementById("h_iny").value == "")) {
			alert("Debe colocar el turno, la fecha y hora de inyección.");
			return false;
		}

		if (document.getElementById("n_fol") && (document.getElementById("n_fol").value == "0" || document.getElementById("n_fol").value == "") && document.getElementById("con_iny0").value != "") {
			alert("El número de folicules no puede estar en blanco ni puede ser 0.");
			return false;
		}

		/* if ((document.getElementById("idturno").value == "") && (document.getElementById("h_asp1").value != "" || document.getElementById("f_asp1").value != "")) {
		    alert("Debe ingresar el turno de la sala.");
		    return false;
		} */
	}

	if (document.getElementById("p_extras").value == "") {
		alert("Debe ingresar EXTRAS.");
		return false;
	}

	if (document.getElementById("repro").value == "") {
		alert("Debe ingresar la Reproducción Asistida");
		return false;
	}

	if (document.getElementById("p_dnix").value == "") {
		alert("Debe ingresar la Pareja");
		return false;
	}

	if (document.getElementById("t_muex").value == "") {
		alert("Debe ingresar el tipo de Muestra");
		return false;
	}

	if (document.getElementById("poseidon").value == "") {
		alert("Debe seleccionar el campo de Poseidon.");
		return false;
	}

	if (document.getElementById("cancela").checked == true && document.getElementById("motivo_cancelacion").value == "") {
		alert("Debe ingresar el motivo de la cancelación");
		return false;
	}

	if (document.getElementById("eda").value == 0) {
		alert("Debe llenar el campo EDAD (en Mujer Antecedentes)");
		return false;
	}

	if (document.getElementById("recep_num") && document.getElementById("recep_num").value == 0) {
		alert("Debe asociar al menos 1 Receptora!");
		return false;
	}

	if (document.getElementById("des_dia").value === "") {
		if (document.getElementById("hete_chk").value == 1) { // esto se repite mas abajo porque si se ponen arriba genera error porque el valor hete_chk no existe cuando des_dia mayor que 1
			if (document.getElementById("p_dni_het").value === "") {
				alert("Debe ingresar un Donante (Heterólogo)");
				return false;
			}
		}

		if (document.getElementById("fec_iny_activo").value == 1) {
			if (document.getElementById("n_fol").value == 0) {
				alert("Debe llenar Número de Folículos");
				return false;
			}
		}
	} else {
		if (document.getElementById("des_dia").value == 0) {
			if (document.getElementById("hete_chk").value == 1) {
				if (document.getElementById("p_dni_het").value === "") {
					alert("Debe ingresar un Donante (Heterólogo)");
					return false;
				}
			}
		}
	}
	var cancela = document.querySelector('#cancela:checked').value;

	if (cancela == '1') {
		var nombre_modulo = "reproduccion_asistida";
		var ruta = "perfil_medico/busqueda_paciente/paciente/reproduccion_asistida.php";
		var tipo_operacion = "baja";
		var login = $('#login').val();
		var key = $('#key').val();
		var clave = '';
		var valor = '';
		$.ajax({
			type: 'POST',
			dataType: "json",
			contentType: "application/json",
			url: '_api_inmater/servicio.php',
			data: JSON.stringify({
				nombre_modulo: nombre_modulo,
				ruta: ruta,
				tipo_operacion: tipo_operacion,
				clave: clave,
				valor: valor,
				idusercreate: login,
				apikey: key
			}),
			// processData: false,  // tell jQuery not to process the data
			// contentType: false,   // tell jQuery not to set contentType
			success: function(result) {
				console.log(result);
			}
		});
	} else {
		var nombre_modulo = "reproduccion_asistida";
		var ruta = "perfil_medico/busqueda_paciente/paciente/reproduccion_asistida.php";
		var tipo_operacion = "actualizacion";
		var login = $('#login').val();
		var key = $('#key').val();
		var clave = '';
		var valor = '';
		$.ajax({
			type: 'POST',
			dataType: "json",
			contentType: "application/json",
			url: '_api_inmater/servicio.php',
			data: JSON.stringify({
				nombre_modulo: nombre_modulo,
				ruta: ruta,
				tipo_operacion: tipo_operacion,
				clave: clave,
				valor: valor,
				idusercreate: login,
				apikey: key
			}),
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
}).on("click", ".hide-page-loading-msg", function() {
	$.mobile.loading("hide");
});
</script>