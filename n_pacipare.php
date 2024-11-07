<!DOCTYPE HTML>
<html>

<head>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <title>Clínica Inmater | Nuevo Paciente</title>

    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" type="text/css" href="css/global.css">

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="n_paci" data-dialog="true">
        <style>
        .ui-dialog-contain {
            max-width: 900px;
            margin: 1% auto 1%;
            padding: 0;
            position: relative;
            top: -35px;
        }

        .enlinea {
            padding: 0 10px;
            border-radius: 5px;
        }

        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }
        </style>

        <script>
        $(document).ready(function() {
            $('.alfanumerico').keyup(function() {
                var $th = $(this);
                $th.val($th.val().replace(/[^a-zA-Z0-9]/g, function(str) {
                    return '';
                }));
            });

            $(".mujer, .hombre").hide();

            $("#paciente").change(function() {
                // paciente: 1=mujer, 2=hombre
                if ($(this).val() == 1) {
                    $(".mujer").show();
                    $(".hombre").hide();
                }

                if ($(this).val() == 2) {
                    $(".hombre").show();
                    $(".mujer").hide();
                }

                $("#medico_id").prop('selectedIndex', 0);
                $("#medico_id").selectmenu("refresh", true);
            });

            $("input[name='pareja']").change(function() {
                if ($(this).val() == 1) {
                    $(".hombre").show();

                }
                if ($(this).val() == 2) {
                    $(".hombre").hide();
                }
            });
        });
        function mostrarToastt(icon, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            Toast.fire({
                icon: icon,
                title: title
            });
        }

        </script>

        <div data-role="header" data-position="fixed">
            <a href="lista_facturacion.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
            <h1>Nuevo Paciente</h1>
        </div>

        <div class="ui-content" role="main">
            <?php
			// pareja
			if (isset($_POST['paciente']) && $_POST['paciente'] == 2) {
                // verificar el medico tratante
                $stmt = $db->prepare("SELECT  codigo from man_medico WHERE id=?;");
                $stmt->execute([$_POST['medico_id']]);
                $data_medico = $stmt->fetch(PDO::FETCH_ASSOC);
                $codigo_medicoP = $data_medico["codigo"];
				insertPareja('', $_POST['p_dni'], $_POST['p_validarDniValue'], $_POST['p_tip'], $_POST['p_nom'], $_POST['p_apeP'],$_POST['p_apeM'], $_POST['p_fnac'], '', '', '', '', '', '', '', '', $codigo_medicoP, '', '',$_POST['don2'],$_POST['medios_comunicacion_id_'],$_POST['sede_idP']);

            ?>
                <script>
                    mostrarToastt('success', 'Se creo el Paciente correctamente');
                </script>
        
            <?php
			}

			// paciente
			if (isset($_POST['paciente']) && $_POST['paciente'] == 1) {
				insertPaci($_POST['dni'], $_POST['validarDniValue'], $_POST['medios_comunicacion_id'], $_POST['medico_id'], $_POST['tip'], $_POST['nom'], $_POST['apeP'], $_POST['apeM'], $_POST['fnac'], $_POST['tcel'], '', '', $_POST['mai'], $_POST['dir'], $_POST['nac'], '', '', '', '', '', $_POST['don'], '', null, null, '', '', null, $_POST['sede_id'], null,'',$login);

				if ($_POST['pareja']==1 and isset($_POST['dni']) and !empty($_POST['dni'])) {
                    // verificar el medico tratante
                    $stmt = $db->prepare("SELECT id, codigo from man_medico WHERE id=?;");
                    $stmt->execute([$_POST['medico_id']]);
                    $data_medico = $stmt->fetch(PDO::FETCH_ASSOC);
                    $codigo_medico = $data_medico["codigo"];
					insertPareja($_POST['dni'], $_POST['p_dni'], $_POST['p_validarDniValue'], $_POST['p_tip'], $_POST['p_nom'], $_POST['p_apeP'], $_POST['p_apeM'], $_POST['p_fnac'],'', '', '', '', '', '', '', '', $codigo_medico, '', '',$_POST['don'],$_POST['medios_comunicacion_id'],$_POST['sede_id']);
				} 
                ?>
                <script>
                    mostrarToastt('success', 'Se creo el Paciente correctamente');
                </script>
        
            <?php
			} ?>

            <form action="" method="post" data-ajax="false">
                <div class="enlinea ui-bar-a">Paciente:
                    <select name="paciente" id="paciente" data-mini="true">
                        <option value="" selected>SELECCIONAR</option>
                        <option value=1>MUJER</option>
                        <option value=2>HOMBRE</option>
                    </select>Médico:

                    <select name="medico_id" id="medico_id" data-mini="true">
                        <option value="" selected>Seleccionar</option>
                        <?php
                        $mTratante = $db->prepare("SELECT id, upper(nombre)nombre ,codigo FROM man_medico where estado=1 order by nombre;");
                        $mTratante->execute();
                        while ($med = $mTratante->fetch(PDO::FETCH_ASSOC)) {
                            print("<option value=".$med['id'].">".$med['nombre']."</option>");
                        } ?>                   
                    </select>  
                </div>
                <table width="100%" align="center" style="margin: 0 auto;" class="mujer">
                        <tr>
                            <td class="color_red"style="text-align: left; width: 50%;" colspan="3">*Campos obligatorios</td>
                            <td style="text-align: right; width: 50%;color:green" id="msgValidacion" colspan="2"></td>
                        </tr> 
                        <tr>
                            <td>Tipo de Cliente<span class="color_red">*</span></td>
                            <td>Programa<span class="color_red">*</span></td>
                            <td style="padding-left: 11%; ">Procedencia<span class="color_red">*</span></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="don" id="don" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php
                                        $stmt = $db->prepare("SELECT codigo, nombre from tipo_cliente where eliminado = 0;");
                                        $stmt->execute();
                                        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value=" . $data['codigo'] . ">" . $data['nombre']."</option>");
                                        } ?>
                                </select>
                            </td>
                            <td colspan="2">
                                <select name="medios_comunicacion_id" id="medios_comunicacion_id" data-mini="true">
                                    <option value="">Seleccionar</option>
                                </select>
                            </td>    
                            <script>
                            $(document).ready(function() {
                                $("#don").on('change', function() {

                                    $("#don option:selected").each(function() {
                                        elegido = $(this).val();
                                        $.post("tipocliente.php", {
                                            elegido: elegido
                                        }, function(data) {
                                            $("#medios_comunicacion_id").html(data);
                                            $("#medios_comunicacion_id").selectmenu(
                                                "refresh");
                                        });
                                    });
                                });
                            });
                            </script>
                        <td colspan="2">
                            <select name="sede_id" id="sede_id" data-mini="true">
                                <option value="">Seleccionar</option>
                                <?php
									$rSedes = $db->prepare("SELECT id ,upper(trim(nombre)) nombre  FROM sedes WHERE estado=1 ORDER BY nombre;");
									$rSedes->execute();
									while ($sede = $rSedes->fetch(PDO::FETCH_ASSOC)) {
										print("<option value=" . $sede['id'] . ">" . $sede['nombre'] . "</option>");
									}
								?>
                            </select>
                        </td>
                        </tr>  
                    <tr>
                        <td>
                            <input type="hidden" name="validarDniValue" id="validarDniValue" value="1">
                                
                            <select name="tip" id="tip" data-mini="true">
                                <option value="DNI" selected>DNI</option>
                                <option value="PAS">PAS</option>
                                <option value="CEX">CEX</option>
                            </select>
                        </td>
                        <td style="display: flex; align-items: center;">
                            <input name="dni" type="text" id="dni" data-mini="true" />
                            <div id="validarDni">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" style="margin-right: 5px; cursor: pointer;fill: rgba(0, 0, 0, 1);transform: msFilter;"><path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z" fill="#44d51f"></path><path d="M11.412 8.586c.379.38.588.882.588 1.414h2a3.977 3.977 0 0 0-1.174-2.828c-1.514-1.512-4.139-1.512-5.652 0l1.412 1.416c.76-.758 2.07-.756 2.826-.002z" fill="#44d51f"></path></svg>
                            </div>

                        </td>
                        <td>F. Nac <span class="color_red">*</span></td>
                        <td><input name="fnac" type="date" id="fnac" data-mini="true" /></td>

                    </tr>
                    <tr>
                        <td >Nombres <span class="color_red">*</span></td>
                        <td><input name="nom" type="text" id="nom" data-mini="true" /></td>

                        <td ></td>
                        <td ></td>

                        <td>
                            <select name="nac" id="nac" data-mini="true">
                                <option value="">Nacionalidad *</option>
                                <option value="PE">Peru</option>
                                <?php
								$rPais = $db->prepare("SELECT * FROM countries ORDER by countryname ASC");
								$rPais->execute();
								while ($pais = $rPais->fetch(PDO::FETCH_ASSOC)) {
									echo "<option value=" . $pais['countrycode'] . ">" . $pais['countryname'] . "</option>";
								} ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td >Ape. Paterno <span class="color_red">*</span></td>
                        <td><input name="apeP" type="text" id="apeP" data-mini="true" /></td>

                        <td style="width: 10em;">Ape. Materno <span class="color_red">*</span></td>
                        <td ><input name="apeM" type="text" id="apeM" data-mini="true" /></td>
                    </tr>
                    <tr>
                        <td>Dirección <span class="color_red">*</span></td>
                        <td colspan="3"><input name="dir" type="text" id="dir" data-mini="true" /></td>
                    </tr>
                    <tr>
                        <td>Celular</td>
                        <td><input name="tcel" type="number" id="tcel" data-mini="true" />
                        </td>
                        <td>E-Mail</td>
                        <td><input name="mai" type="text" id="mai" data-mini="true"></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                <legend>Pareja</legend>
                                <input type="radio" name="pareja" id="parejaa" value=1> <label for="parejaa">Si</label>
                                <input type="radio" name="pareja" id="parejab" value=2> <label for="parejab">No</label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <table width="100%" align="center" style="margin: 0 auto;" class="hombre">
                        <tr>
                            <td class="color_red"style="text-align: left; width: 50%;" colspan="3">*Campos obligatorios</td>
                            <td style="text-align: right; width: 50%;color:green" id="p_msgValidacion" colspan="2"></td>
                        </tr> 
                <tr>
                            <td>Tipo de Cliente<span class="color_red">*</span></td>
                            <td style="padding-left: 2%; ">Programa<span class="color_red">*</span></td>
                            <td style="padding-left: 11%; ">Procedencia<span class="color_red">*</span></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="don2" id="don2" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php
                                        $stmt = $db->prepare("SELECT codigo, nombre from tipo_cliente where eliminado = 0;");
                                        $stmt->execute();
                                        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value=" . $data['codigo'] . ">" . $data['nombre']."</option>");
                                        } ?>
                                </select>
                            </td>
                            <td colspan="2">
                                <select name="medios_comunicacion_id_" id="medios_comunicacion_id_" data-mini="true">
                                    <option value="">Seleccionar</option>
                                </select>
                            </td>
                            <script>
                            $(document).ready(function() {
                                $("#don2").on('change', function() {

                                    $("#don2 option:selected").each(function() {
                                        elegido = $(this).val();
                                        $.post("tipocliente.php", {
                                            elegido: elegido
                                        }, function(data) {
                                            $("#medios_comunicacion_id_").html(data);
                                            $("#medios_comunicacion_id_").selectmenu(
                                                "refresh");
                                        });
                                    });
                                });
                            });
                            </script>
                            <td colspan="2">
                                <select name="sede_idP" id="sede_idP" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php
                                        $rSedes = $db->prepare("SELECT id, upper(trim(nombre)) nombre from sedes where estado = 1 order by nombre;");
                                        $rSedes->execute();
                                        while ($sede = $rSedes->fetch(PDO::FETCH_ASSOC)) {
                                           print("<option value=".$sede['id']." $selected>".$sede['nombre']."</option>");
                                        }
                                    ?>
                                </select>
                            </td>
                    </tr>    
                    </tr>    
                   
                        </tr>
                   
                    <tr>
                        <td>

                            <input type="hidden" name="p_validarDniValue" id="p_validarDniValue" value="1">
                            <select name="p_tip" id="p_tip" data-mini="true">
                                <option value="DNI" selected>DNI<span class="color_red">*</option>
                                <option value="PAS">PAS</option>
                                <option value="CEX">CEX</option>
                            </select>
                        </td>

                        <td style="display: flex; align-items: center;">
                            <input name="p_dni" type="text" id="p_dni" data-mini="true" />
                            <div id="p_validarDni">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" style="margin-right: 5px; cursor: pointer;fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z" fill="#44d51f"></path><path d="M11.412 8.586c.379.38.588.882.588 1.414h2a3.977 3.977 0 0 0-1.174-2.828c-1.514-1.512-4.139-1.512-5.652 0l1.412 1.416c.76-.758 2.07-.756 2.826-.002z" fill="#44d51f"></path></svg>
                            </div>

                        </td>
                        <td>F. Nac<span class="color_red">*</td>
                        <td><input name="p_fnac" type="date" id="p_fnac" data-mini="true" /></td>
                        <td>&nbsp;</td>
                    </tr>

                    <tr>
                        <td >Nombres<span class="color_red">*</span></td>
                        <td ><input name="p_nom" type="text" id="p_nom" data-mini="true" /></td>
                    </tr>
                    <tr>
                        <td >Apellido Paterno<span class="color_red">*</span></td>
                        <td ><input name="p_apeP" type="text" id="p_apeP" data-mini="true" /></td>
                        <td style="width: 10em;">Apellido Materno<span class="color_red">*</span></td>
                        <td><input name="p_apeM" type="text" id="p_apeM" data-mini="true" /></td>
                        <td style="width: 12em!important;" >&nbsp;</td>
                    </tr>
                </table>
                <input type="Submit" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-mini="true" 
                    data-theme="b" data-inline="true" class="show-page-loading-msg" />
            </form>
        </div>
    </div>
    <script>
    $(document).on("click", ".show-page-loading-msg", function() {
        if (document.getElementById("paciente").value == "" ) {
            alert("El paciente es un campo obligatorio");
            return false;
        }
        if (document.getElementById("medico_id").value == "") {
            alert("El medico es un campo obligatorio");
            return false;
        }
   
        // validaciones para la paciente mujer 
        if (document.getElementById("paciente").value == 1 ) {
            dato=document.getElementById("medios_comunicacion_id").value;
            if (document.getElementById("don").value == "") {
                alert("El tipo de cliente es obligatorio.");
                return false;
            }
            if (document.getElementById("medios_comunicacion_id").value == "") {
                alert("El programa por el que se entero de nosotros es un campo obligatorio");
                return false;
            }
            if (document.getElementById("sede_id").value == "" ) {
                alert("La sede es un campo obligatorio");
                return false;
            }
            if (document.getElementById("nom").value == "") {
                alert("El nombre es un campo obligatorio.");
                return false;
            }
            if (document.getElementById("ape").value == "" ) {
                alert("Los apellidos son un campo obligatorio.");
                return false;
            }
            var tipo_documento = document.getElementById("tip").value;
            var numero_documento = document.getElementById("dni").value;
            if (numero_documento == "") {
                alert("El número de documento es obligatorio.");
                return false;
            } else {
                switch (tipo_documento) {
                    case "DNI":
                        if (numero_documento.length != 8) {
                            alert("El campo DNI debe tener 8 digitos.");
                            return false;
                        }
                        break;
                    case "PAS":
                        if (numero_documento.length > 12) {
                            alert("El número de pasaporte debe tener máximo 12 digitos.");
                            return false;
                        }
                        break;
                    case "CEX":
                        if (numero_documento.length > 12) {
                            alert("El número de carnet de extranjería debe tener máximo 12 digitos.");
                            return false;
                        }
                        break;
                    default:
                        break;
                }
            }
            if (document.getElementById("fnac").value == "") {
                alert("La Fecha de Nacimiento es un campo obligatorio.");
                return false;
            } else {
                var diff = new Date(new Date() - new Date(jQuery("#fnac").val()));
                anios = diff / 1000 / 60 / 60 / 24 / 365.25;
                if (anios > 115 || anios < 12) {
                    alert("Verificar la edad del paciente, no puede ser menor a 12 años ni mayor a 115 años.");
                    return false;
                }
            }
            if (document.getElementById("nac").value == "") {
                alert("La Nacionalidad es un campo obligatorio.");
                return false;
            }
            if (document.getElementById("dir").value == "") {
                alert("La Dirección es un campo obligatorio");
                return false;
            }
    }
    });

    $("#validarDni").click(function() {
        dni = $('#dni').val()
        tipo = $('#tip').val()
        validarDniValue = $('#validarDniValue').val()
        tipDoc = 0
        switch (tipo) {
            case 'DNI':
                tipDoc = 1;
                break;

            case 'PAS':
                tipDoc = 4;
                break;

            case 'CEX':
                tipDoc = 2;
                break;
        }
        valDni = 'dni'
        nom = 'nom'
        apeP = 'apeP'
        apeM = 'apeM'
        fnac = 'fnac'
        selectTipDoc = 'tip'
        campDni = 'validarDni'
        msgVal = 'msgValidacion'
        valueDni = 'validarDniValue'
        sistema = window.location.pathname
        sistema = sistema.slice(1)
        usuario = '<?php echo $login;?>';

        if (validarDniValue == '1') {
            validarDocumento(valDni, nom, apeP, apeM, fnac, dni, tipDoc, selectTipDoc, campDni, msgVal, valueDni,sistema,usuario)
        } else if (validarDniValue == '2') {
            habilitarCampos(valDni, nom, apeP, apeM, fnac, selectTipDoc,campDni,msgVal,valueDni)
        }
    })

    $("#p_validarDni").click(function() {
        dni = $('#p_dni').val()
        tipo = $('#p_tip').val()
        validarDniValue = $('#p_validarDniValue').val()
        tipDoc = 0
        switch (tipo) {
            case 'DNI':
                tipDoc = 1;
                break;

            case 'PAS':
                tipDoc = 4;
                break;

            case 'CEX':
                tipDoc = 2;
                break;
        }
        valDni = 'p_dni'
        nom = 'p_nom'
        apeP = 'p_apeP'
        apeM = 'p_apeM'
        fnac = 'p_fnac'
        selectTipDoc = 'p_tip'
        campDni = 'p_validarDni'
        msgVal = 'p_msgValidacion'
        valueDni = 'p_validarDniValue'
        sistema = window.location.pathname
        sistema = sistema.slice(1)
        usuario = '<?php echo $login;?>';

        if (validarDniValue == '1') {
            validarDocumento(valDni, nom, apeP,apeM, fnac, dni, tipDoc, selectTipDoc, campDni, msgVal, valueDni,sistema,usuario)
        } else if (validarDniValue == '2') {
            habilitarCampos(valDni, nom, apeP,apeM, fnac, selectTipDoc,campDni,msgVal,valueDni)
        }
    })

    
    </script>
<script src="js/n_pacipare.js?v=1"></script>
<?php include ($_SERVER["DOCUMENT_ROOT"] . "/_componentes/n_paci/validacion_reniec.php"); ?>

</body>

</html>