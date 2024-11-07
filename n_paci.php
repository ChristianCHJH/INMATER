<!DOCTYPE HTML>
<html>

<head>
    <title>Inmater Clínica de Fertilidad | Nuevo Datos Generales de Paciente</title>
   <?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
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
            max-width: 110vh;
            margin: 1% auto 1%;
            padding: 0;
            position: relative;
            top: -35px;
        }

        .scroll_h {
            overflow-x: scroll;
            overflow-y: hidden;
            white-space: nowrap;
        }
        </style>

        <script>
        $(document).ready(function() {
            var unsaved = false;

            $(":input").change(function() {
                unsaved = true;
            });

            $(window).on('beforeunload', function() {
                if (unsaved) {
                    return 'UD. HA REALIZADO CAMBIOS';
                }
            });

            // Form Submit
            $(document).on("submit", "form", function(event) {
                // disable unload warning
                $(window).off('beforeunload');
            });

            $('.numeros').keyup(function() {
                var $th = $(this);

                $th.val($th.val().replace(/[^0-9]/g, function(str) {
                    return '';
                }));
            });

            $('.alfanumerico').keyup(function() {
                var $th = $(this);

                $th.val($th.val().replace(/[^a-zA-Z0-9]/g, function(str) {
                    return '';
                }));
            });

            $("#depa").change(function() {
                $("#depa option:selected").each(function() {
                    var depa = $(this).val();

                    $.post("le_tanque.php", {
                        depa: depa
                    }, function(data) {
                        $("#prov").html(data);
                        $("#prov").selectmenu("refresh");
                    });
                });
            });

            $("#prov").change(function() {
                $("#prov option:selected").each(function() {
                    var prov = $(this).val();

                    $.post("le_tanque.php", {
                        prov: prov
                    }, function(data) {
                        $("#dist").html(data);
                        $("#dist").selectmenu("refresh");
                    });
                });
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
            <?php
            if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
                print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } else {
                print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } ?>
            <h1>Nuevo Paciente</h1>
        </div>

        <div class="ui-content" role="main">
            <?php
            if (isset($_POST['dni'])) {
                insertPaci($_POST['dni'],$_POST['validarDniValue'], $_POST['medios_comunicacion_id'], null, $_POST['tip'], $_POST['nom'], $_POST['apeP'],$_POST['apeM'], $_POST['fnac'], $_POST['tcel'], $_POST['tcas'], $_POST['tofi'], $_POST['mai'], $_POST['dir'], $_POST['nac'], $_POST['depa'], $_POST['prov'],$_POST['dist'], $_POST['prof'], $_POST['san'], $_POST['don'], $_POST['raz'],$_POST['talla'],$_POST['peso'], $_POST['rem'], $_POST['nota'],$_FILES['foto'],$_POST['sede'],$_POST['m_tratante'],$_POST['asesora'],$login);
            ?>

            <script>
                mostrarToastt('success', 'Se creo el Paciente correctamente');
            </script>

            <?php }
            $key=$_ENV["apikey"];
            ?>
            <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
            <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
            <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="formapi" name="formapi">
                <div class="scroll_h">
                    <table width="100%" align="center" style="margin: 0 auto; max-width:100vh;">
                        <tr>
                            <td class="color_red" style="text-align: left; width: 50%;" colspan="3">*Campos obligatorios</td>
                            <td style="text-align: right; width: 50%;color:green" id="msgValidacion" colspan="2"></td>
                        </tr>                        
                        <tr>
                            <td>Tipo de Cliente<span class="color_red">*</span></td>
                            <td>Programa<span class="color_red">*</span></td>
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
                            <td colspan="1">
                                <select name="medios_comunicacion_id" id="medios_comunicacion_id" data-mini="true">
                                    <option value="">Seleccionar</option>
                                </select>
                            </td>
                            <td></td>
                            <td class="text-center" style="position:relative; top:20px">Medico Tratante <span
                                    class="color_red">*</span></td>
                            <td class="text-center" style="position:relative; top:20px">Asesora</td>
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
                        </tr>
                        <tr>
                            <td>Procedencia <span class="color_red">*</span></td>
                            <td>
                                <select name="sede" id="sede" data-mini="true">
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
                            <td></td>
                            <td colspan="1" class="text-center">
                                <select name="m_tratante" id="m_tratante" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php
                                        $mTratante = $db->prepare("SELECT id, upper(trim(nombre)) nombre from man_medico where estado=1 order by nombre asc");
                                        $mTratante->execute();
                                        while ($med = $mTratante->fetch(PDO::FETCH_ASSOC)) {
										    print("<option value=".$med['id']." $selected>".$med['nombre']."</option>");
                                        }
                                    ?>
                                </select>
                            </td>
                            <td colspan="1" class="text-center">
                                <select name="asesora" id="asesora" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php
                                        $aMedico = $db->prepare("SELECT id, upper(trim(apellidos || ' ' || nombres)) nombre from asesor_medico where eliminado=0 order by nombre asc");
                                        $aMedico->execute();
                                        while ($asesor = $aMedico->fetch(PDO::FETCH_ASSOC)) {
										    print("<option value=".$asesor['id']." $selected>".$asesor['nombre']."</option>");
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>


                                <input type="hidden" name="validarDniValue" id="validarDniValue" value="1">

                                <select name="tip" id="tip" data-mini="true">
                                    <option value="DNI" selected>DNI *</option>
                                    <option value="PAS">PAS *</option>
                                    <option value="CEX">CEX *</option>
                                </select>
                            </td>
                            <td style="display: flex; align-items: center;">
                                <input name="dni" type="text" id="dni" data-mini="true" class="alfanumerico" />
                                <div id="validarDni">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" id="validarDni" viewBox="0 0 24 24" style=" cursor: pointer;fill: rgba(0, 0, 0, 1);transform: ;msFilter:;">
                                        <path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z" fill="#44d51f"></path>
                                        <path d="M11.412 8.586c.379.38.588.882.588 1.414h2a3.977 3.977 0 0 0-1.174-2.828c-1.514-1.512-4.139-1.512-5.652 0l1.412 1.416c.76-.758 2.07-.756 2.826-.002z" fill="#44d51f"></path>
                                    </svg>
                                </div>
                            </td>
                            <td>F. Nac <span class="color_red">*</span></td>
                            <td><input name="fnac" type="date" id="fnac" data-mini="true" /></td>

                            <td rowspan="8">
                                <fieldset data-role="controlgroup">
                                    <select name="nac" id="nac" data-mini="true">
                                        <option value="">Nacionalidad</option>
                                        <option value="PE">Peru</option>
                                        <?php $rPais = $db->prepare("SELECT * FROM countries ORDER by countryname ASC");
                                        $rPais->execute();
                                        while ($pais = $rPais->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value=" . $pais['countrycode'] . ">" . $pais['countryname'] . "</option>";
                                        } ?>
                                    </select>
                                    <select name="raz" id="raz" data-mini="true">
                                        <option value="">Raza:</option>
                                        <option value="Blanca">Blanca</option>
                                        <option value="Morena">Morena</option>
                                        <option value="Mestiza">Mestiza</option>
                                        <option value="Asiatica">Asiatica</option>
                                    </select>
                                    <select name="san" id="san" data-mini="true">
                                        <option value="">Grupo Sanguineo:</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                    <input name="talla" type="number" step="any" id="talla" data-mini="true"
                                        placeholder="Talla(Cm)" value="<?php echo $paci['talla']??''; ?>" />
                                    <input name="peso" type="number" step="any" id="peso" data-mini="true"
                                        placeholder="Peso(Kg)" value="<?php echo $paci['peso']??''; ?>" />
                                    <img src="_images/foto.gif" alt="" width="100px" height="100px" id="preview" />
                                    <input name="foto" type="file" onchange="previewImage(this)" accept="image/jpeg"
                                        id="foto" />

                                </fieldset>
                                <script type="text/javascript">
                                function previewImage(input) {
                                    var preview = document.getElementById('preview');
                                    if (input.files && input.files[0]) {
                                        var reader = new FileReader();
                                        reader.onload = function(e) {
                                            preview.setAttribute('src', e.target.result);
                                        }
                                        reader.readAsDataURL(input.files[0]);
                                    } else {
                                        preview.setAttribute('src', 'placeholder.png');
                                    }
                                }
                                </script>
                            </td>
                        </tr>
                        <tr>
                            <td width="9%">Nombres <span class="color_red">*</span></td>
                            <td width="29%"><input name="nom" type="text" id="nom" data-mini="true" readonly/></td>
                            <td width="9%">Ape. Paterno <span class="color_red">*</span></td>
                            <td width="29%"><input name="apeP" type="text" id="apeP" data-mini="true" readonly/></td>

                        </tr>

                        <tr>
                            <td width="9%">Ape. Materno <span class="color_red">*</span></td>
                            <td width="29%"><input name="apeM" type="text" id="apeM" data-mini="true" readonly/></td>
                        </tr>
                        <tr>
                            <td>Celular <span class="color_red">*</span></td>
                            <td><input name="tcel" type="number" step="any" id="tcel" data-mini="true"
                                    class="numeros" />
                            </td>
                            <td>E-Mail <span class="color_red">*</span></td>
                            <td><input name="mai" type="text" id="mai" data-mini="true"></td>
                        </tr>
                        <tr>
                            <td>T. Casa</td>
                            <td><input name="tcas" type="number" step="any" id="tcas" data-mini="true"
                                    class="numeros" />
                            </td>
                            <td>Profesión</td>
                            <td><input name="prof" type="text" id="prof" data-mini="true" /></td>
                        </tr>
                        <tr>
                            <td>T. Oficina</td>
                            <td><input name="tofi" type="text" id="tofi" data-mini="true" /></td>
                            <td>Referido por <span class="color_red">*</span></td>
                            <td><select name="rem" id="rem" data-mini="true" title="Referido Por">
                                    <option value="">Seleccionar:</option>
                                    <?php $mReferencia = $db->prepare("SELECT id, upper(nombre) nombre FROM medios_referencia WHERE eliminado=0 ORDER by nombre ASC");
                                    $mReferencia->execute();
                                    while ($referencia = $mReferencia->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $referencia['id']; ?>">
                                        <?php echo $referencia['nombre']; ?></option>
                                    <?php } ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td>Dep/Prov/Dis</td>
                            <td>
                                <select name="depa" id="depa" data-mini="true" title="Departamento">
                                    <option value="">Departamento:</option>
                                    <option value="150000">LIMA</option>
                                    <?php $rDepa = $db->prepare("SELECT * FROM departamentos ORDER by nomdepartamento ASC");
                                    $rDepa->execute();
                                    while ($depa = $rDepa->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?=$depa['iddepartamento']?>">
                                        <?php echo $depa['nomdepartamento']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td><select name="prov" id="prov" data-mini="true">
                                    <option value=""> Provincia</option>
                                </select></td>
                            <td><select name="dist" id="dist" data-mini="true">
                                    <option value=""> Distrito</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td>Dirección <span class="color_red">*</span></td>
                            <td colspan="3"><input name="dir" type="text" id="dir" data-mini="true" /></td>
                        </tr>
                        <tr>
                            <td>Observaciones</td>
                            <td colspan="3"><textarea name="nota" id="nota" data-mini="true"></textarea></td>
                        </tr>
                    </table>
                </div>
                <input type="Submit" value="AGREGAR DATOS" name="btn" data-icon="check" data-iconpos="left"
                    data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true"
                    data-msgtext="Agregando paciente.." data-theme="b" data-inline="true" />
            </form>
        </div>

        <script>
        $(document).on("click", ".show-page-loading-msg", function() {
            if (document.getElementById("don").value == "") {
                alert("Debe llenar el campo Tipo de Paciente");
                return false;
            }
            if (document.getElementById("medios_comunicacion_id").value == "") {
                alert("Debe seleccionar el campo Programa.");
                return false;
            }
            if (document.getElementById("sede").value == "") {
                alert("Debe llenar el campo Sede.");
                return false;
            }
            if (document.getElementById("m_tratante").value == "") {
                alert("Debe llenar el campo Medico Tratante.");
                return false;
            }
            if (document.getElementById("nom").value == "") {
                alert("Debe llenar el campo Nombre");
                return false;
            }
            if (document.getElementById("apeP").value == "") {
                alert("Debe llenar el campo Apellidos");
                return false;
            }
            if (document.getElementById("apeM").value == "") {
                alert("Debe llenar el campo Apellidos");
                return false;
            }
            if (document.getElementById("tcel").value == "") {
                alert("Debe llenar el campo Celular");
                return false;
            }
            if (document.getElementById("mai").value == "") {
                alert("Debe llenar el campo Email");
                return false;
            }
            if (document.getElementById("rem").value == "") {
                alert("Debe llenar el campo Referido por");
                return false;
            }

            var tipo_documento = $('#tip').val();
            if (tipo_documento == "") {
                alert("Debe llenar el campo Tipo de Documento");
                return false;
            }

            // validacion de numero de documento a partir del tipo de documento
            var numero_documento = $('#dni').val();
            if (numero_documento == "") {
                alert("Debe llenar el campo del número de documento.");
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
                alert("Debe llenar el campo Fecha de Nacimiento");
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
                alert("Debe llenar el campo: Nacionalidad");
                return false;
            }

            if (document.getElementById("dir").value == "") {
                alert("Debe llenar el campo: Dirección");
                return false;
            }

            var nombre_modulo = "historias_clinicas";
            var ruta = "perfil_medico/busqueda_paciente/paciente.php";
            var tipo_operacion = "ingreso";
            var login = $('#login').val();
            var key = $('#key').val();
            var clave = 'paciente';
            var valor = $('#dni').val();
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
            $(".show-page-loading-msg").hide();
        }).on("click", ".hide-page-loading-msg", function() {
            $.mobile.loading("hide");
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
                    validarDocumento(valDni, nom, apeP, apeM, fnac, dni, tipDoc, selectTipDoc, campDni, msgVal, valueDni, sistema, usuario)
                } else if (validarDniValue == '2' || validarDniValue == '3') {
                    habilitarCampos(valDni, nom, apeP, apeM, fnac, selectTipDoc, campDni, msgVal, valueDni)
                }
            })
        </script>
    </div>

    <?php include($_SERVER["DOCUMENT_ROOT"] . "/_componentes/n_paci/validacion_reniec.php"); ?>

</body>

</html>