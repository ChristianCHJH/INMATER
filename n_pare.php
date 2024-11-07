<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .scroll_h {
            overflow-x: scroll;
            overflow-y: hidden;
            white-space: nowrap;
        }

        #alerta {
            background-color: #FF9;
            margin: 0 auto;
            text-align: center;
            padding: 4px;
        }

        .ui-tabs-panel {
            background-color: #FFF;
            padding: 5px;
        }

        #unique-span.custom-label {
            padding: 0.2em 0.6em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25em;
            
            
        }
    </style>
    <script>
        $(document).ready(function () {
            // No close unsaved windows
            var unsaved = false;
            $(":input").change(function () {

                unsaved = true;

            });

            $(window).on('beforeunload', function () {
                if (unsaved) {
                    return 'UD. HA REALIZADO CAMBIOS';
                }
            });

            // Form Submit
            $(document).on("submit", "form", function (event) {
                // disable unload warning
                $(window).off('beforeunload');
            });

            $('.numeros').keyup(function () {
                var $th = $(this);
                $th.val($th.val().replace(/[^0-9]/g, function (str) {
                    return '';
                }));
            });

            $('.alfanumerico').keyup(function () {
                var $th = $(this);
                $th.val($th.val().replace(/[^a-zA-Z0-9]/g, function (str) {
                    return '';
                }));
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
</head>
<body>
    <?php
    $fec = date("Y-m-d");
    if (isset($_POST['dni']) && isset($_POST['boton_datos']) && $_POST['boton_datos'] == "GUARDAR DATOS") {
        insertPareja($_POST['dni'], $_POST['p_dni'], $_POST['p_validarDniValue'], $_POST['p_tip'], $_POST['p_nom'], $_POST['p_apeP'], $_POST['p_apeM'], $_POST['p_fnac'], $_POST['p_tcel'], $_POST['p_tcas'], $_POST['p_tofi'], $_POST['p_mai'], $_POST['p_dir'], $_POST['p_prof'], $_POST['p_san'], $_POST['p_raz'], $_POST['m_tratante'], '', '',$_POST['don'],$_POST['medios_comunicacion_id'],$_POST['sede'],$_POST['m_tratante']);
    ?>
        <script>
            mostrarToastt('success', 'Se creo el Paciente correctamente');
        </script>

    <?php
    }

    if (isset($_POST['dni']) && $_POST['graba_nota'] == 'GRABAR') {
        $stmt = $db->prepare("UPDATE hc_paciente SET nota=?, iduserupdate=?,updatex=? WHERE dni=?");
        $hora_actual = date("Y-m-d H:i:s");
        $stmt->execute(array($_POST['nota'],$login, $hora_actual, $_POST['dni']));
        $log_Paciente = $db->prepare(
            "INSERT INTO appinmater_log.hc_paciente (
                        dni, pass, sta, med, tip, nom, ape, fnac, tcel,
                        tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                        san, don, raz, talla, peso, rem, nota, fec, idsedes,
                        idusercreate, createdate,
                        action
                )
            SELECT 
                dni, pass, sta, med, tip, nom, ape, fnac, tcel, 
                tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                san, don, raz, talla, peso, rem, nota, fec, idsedes,
                iduserupdate,updatex, 'U'
            FROM appinmater_modulo.hc_paciente
            WHERE dni=?");
        $log_Paciente->execute(array($_POST['dni']));
    }

    if (isset($_GET['id']) and !empty($_GET['id'])) {
        $id = $_GET['id'];
        $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
        $rUser->execute(array($login));
        $user = $rUser->fetch(PDO::FETCH_ASSOC);

        $rPaci = $db->prepare("SELECT dni, nom, ape, fnac, nota, medios_comunicacion_id FROM hc_paciente WHERE dni=?");
        $rPaci->execute(array($id));
        $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

        $rPP = $db->prepare("
            select
            c.p_dni, c.p_nom, c.p_ape, b.actual,c.valid_reniec_api
            from hc_paciente a
            inner join hc_pare_paci b on b.estado = 1 and b.dni = a.dni
            inner join hc_pareja c on c.estado = 1 and c.p_dni = b.p_dni
            where a.dni=?
            order by b.actual desc");
        $rPP->execute(array($id));
        $rows = $rPP->fetchAll();

        if (!file_exists("paci/".$paci['dni']."/foto.jpg")) {
            $foto_url = "_images/foto.gif";
        } else {
            $foto_url = "paci/".$paci['dni']."/foto.jpg";
        } ?>
        <form action="" method="post" data-ajax="false">
            <div data-role="page" class="ui-responsive-panel" id="n_pare">
                <div data-role="panel" id="indice_paci">
                    <img src="_images/logo.jpg"/>
                    <?php require ('_includes/menu_paciente.php'); ?>
                </div>

                <?php
                $color_programa_inmater = '';
                if ($paci['medios_comunicacion_id'] == 2) {
                    $color_programa_inmater = ' class="programa_inmater"';
                } ?>

                <div data-role="header" data-position="fixed" <?php print($color_programa_inmater); ?>>
                    <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU <small>> Pareja</small></a>
                    <h2><?php echo $paci['ape']; ?>
                        <small>
                        <?php
                            echo $paci['nom'];
                            // alerta para la nota
                            $nota_color = "";
                            if ($paci['nota'] != "") {
                                $nota_color = "red";
                            }

                            if ($paci['fnac'] <> "1899-12-30") { echo ' <a href="#popupBasic" data-rel="popup" data-transition="pop" style="color:'.$nota_color.';">(' . date_diff(date_create($paci['fnac']), date_create('today'))->y . ')</a>'; } ?>
                        </small>
                    </h2>
                    <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external"> Salir</a>
                </div>
                <div data-role="popup" id="popupBasic" data-arrow="true">
                    <textarea name="nota" id="nota" data-mini="true"><?php echo $paci['nota']; ?></textarea>
                    <input type="Submit" value="GRABAR" name="graba_nota" data-mini="true"/>
                </div>
                <div class="ui-content" role="main">
                    <input type="hidden" name="dni" id="dni" value="<?php echo $paci['dni']; ?>">
                    <div data-role="tabs">
                        <div data-role="navbar">
                            <ul>
                                <li><a href="#one" data-ajax="false" class="ui-btn-active ui-btn-icon-left ui-icon-bullets">Historial de parejas</a></li>
                                <li><a href="#two" data-ajax="false" class="ui-btn-icon-left ui-icon-edit">Nueva Pareja</a></li>
                            </ul>
                        </div>
                        <div id="one" class="ui-body-d ui-content">
                            <legend>Pareja actual:</legend>
                            <fieldset data-role="controlgroup" data-type="horizontal">
                                <select name="pareja_actual" id="pareja_actual" data-mini="true">
                                    <option value="" selected>SELECCIONAR</option>
                                    <?php
                                    foreach ($rows as $data) {
                                        $selected='';
                                        if ($data['actual'] == 1) { $selected='selected'; }
                                        print("<option value=".$data['p_dni']." $selected>".mb_strtoupper($data['p_ape']).' '.mb_strtoupper($data['p_nom'])."</option>");
                                    } ?>
                                </select>
                                <input type="button" value="Actualizar" id="actualizar_pareja_actual" data-icon="check" data-iconpos="left" data-mini="true" data-textonly="false" data-textvisible="true" data-theme="b" data-inline="true"/>
                            </fieldset>
                            <ol data-role="listview" data-theme="a" data-inset="true">
                                <?php
                                foreach ($rows as $pp) { ?>
                                    <li>
                                        <a href="<?php echo "e_pare.php?id=".$paci['dni']."&ip=".$pp['p_dni']; ?>" rel="external">
                                            <?php print(mb_strtoupper($pp['p_ape']).' '.mb_strtoupper($pp['p_nom'])); ?>
                                            <small><?php print(' (N° Documento: '.$pp['p_dni'].')'); ?></small>
                                            <?php if ($pp['actual'] == 1) { ?><span class="ui-li-count">Actual</span><?php } ?>
                                            <?php 
                                            $textValid = '';
                                            if ($pp['valid_reniec_api'] == true) {
                                                $textValid = "<span id='unique-span' class='custom-label' style='margin-left: 20px;background-color: #C8E6C9;color: #256029;font-weight: 700;'>VALIDADO CON RENIEC</span>";
                                                echo $textValid;
                                            }elseif ($pp['valid_reniec_api'] == false) {
                                                $textValid = "<span id='unique-span' class='custom-label' style='margin-left: 20px;background-color: #FFCDD2;color: #c63737;font-weight: 700;'>NO VALIDADO CON RENIEC</span>";
                                                echo $textValid;
                                            }?>
                                            
                                        </a>
                                    </li>
                                <?php }
                                if ($rPP->rowCount() < 1) { echo '<p><h3>¡ No hay parejas !</h3></p>'; } ?>
                            </ol>
                        </div>
                        <div id="two">
                        <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="formapi" name="formapi">
                            <div class="scroll_h">
                                <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                                <tr>
                                    <td class="color_red"style="text-align: left; width: 50%;" colspan="4">*Campos obligatorios</td>
                                    <td style="text-align: right; width: 50%;color:green" id="p_msgValidacion" colspan="2"></td>
                                </tr>                        
                        <tr >
                            <td>Tipo de Cliente<span class="color_red">*</span></td>
                            <td>Programa<span class="color_red">*</span></td>
                            <td>Procedencia<span class="color_red">*</span></td>
                            <td></td>
                            <td>Medico Tratante<span class="color_red">*</span></td>
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
                            <td>
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
                                <select name="sede" id="sede" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php
                                        $rSedes = $db->prepare("SELECT id, upper(trim(nombre)) nombre from sedes where estado = 1 order by nombre;");
                                        $rSedes->execute();
                                        while ($sede = $rSedes->fetch(PDO::FETCH_ASSOC)) {
																					print("<option value=".$sede['id']." >".$sede['nombre']."</option>");
                                        }
                                    ?>
                                </select>
                                <td colspan="2" class="text-center">
                                <select name="m_tratante" id="m_tratante" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php
                                        $mTratante = $db->prepare("SELECT codigo, upper(trim(nombre)) nombre from man_medico where estado=1 order by nombre asc");
                                        $mTratante->execute();
                                        while ($med = $mTratante->fetch(PDO::FETCH_ASSOC)) {
										    print("<option value=".$med['codigo']." >".$med['nombre']."</option>");
                                        }
                                    ?>
                                </select>
                            </td>
                            </td>
                        </tr>
                        <tr>   
                        </tr>
                        <tr>   
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
                                            <input name="p_dni" type="text" id="p_dni" data-mini="true" class="alfanumerico"/>

                                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" id="p_validarDni" viewBox="0 0 24 24" style="margin-right: 5px; cursor: pointer;fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z" fill="#44d51f"></path><path d="M11.412 8.586c.379.38.588.882.588 1.414h2a3.977 3.977 0 0 0-1.174-2.828c-1.514-1.512-4.139-1.512-5.652 0l1.412 1.416c.76-.758 2.07-.756 2.826-.002z" fill="#44d51f"></path></svg>

                                        </td>
                                        <td></td>
                                        <td></td>

                                        <td>
                                            <select name="p_raz" id="p_raz" data-mini="true">
                                                <option value="">Raza:</option>
                                                <option value="Blanca">Blanca</option>
                                                <option value="Morena">Morena</option>
                                                <option value="Mestiza">Mestiza</option>
                                                <option value="Asiatica">Asiatica</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="p_san" id="p_san" data-mini="true">
                                                <option value="">G. Sangre:</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>

                                        <td width="10%">Nombre(s)<span class="color_red">*</td>
                                        <td width="40%"><input name="p_nom" type="text" id="p_nom" data-mini="true" readonly/></td>
                                        <td>F. Nac<span class="color_red">*</td>
                                        <td><input name="p_fnac" type="date" id="p_fnac" data-mini="true"/></td>


                                        <td>Ocupación</td>
                                        <td><input name="p_prof" type="text" id="p_prof" data-mini="true"/></td>
                                    </tr>
                                    <tr>
                                        <td width="10%">Ape. Paterno<span class="color_red">*</td>
                                        <td width="25%"><input name="p_apeP" type="text" id="p_apeP" data-mini="true" readonly/></td>

                                        <td width="10%">Ape. Materno<span class="color_red">*</td>
                                        <td width="25%"><input name="p_apeM" type="text" id="p_apeM" data-mini="true" readonly/></td>

                                    </tr>
                                    <tr>
                                        <td>Celular<span class="color_red">*</td>
                                        <td><input name="p_tcel" type="number" step="any" id="p_tcel" data-mini="true" class="numeros"/></td>
                                        <td>T. Casa</td>
                                        <td><input name="p_tcas" type="number" step="any" id="p_tcas" data-mini="true" class="numeros"/></td>
                                        <td width="6%">E-mail</td>
                                        <td><input name="p_mai" type="email" id="p_mai" data-mini="true"></td>
                                    </tr>
                                    <tr>
                                        <td>T. Oficina</td>
                                        <td><input name="p_tofi" type="number" step="any" id="p_tofi" data-mini="true"/></td>
                                        <td>Dirección</td>
                                        <td colspan="3"><input name="p_dir" type="text" id="p_dir" data-mini="true"/></td>
                                    </tr>
                                </table>
                                <?php if ($user['role'] == 1) { ?>
                                    <input type="Submit" value="GUARDAR DATOS" name="boton_datos" data-icon="check"
                                        data-iconpos="left" data-mini="true" class="show-page-loading-msg"
                                        data-textonly="false" data-textvisible="true" data-msgtext="Agregando datos.."
                                        data-theme="b" data-inline="true"/>
                                <?php } ?>
                            </div>
                       
                                </form>
                                </div>
                    </div>
                </div>
            </div>
        </form>
    <?php } ?>
    <script>
        $(document).on("click", "#actualizar_pareja_actual", function () {
            var p_dni = $('#pareja_actual').val();
            var dni = $('#dni').val();
            console.log(dni)
            console.log(p_dni)

            if (pareja_actual == "") {
                alert("Debes seleccionar una pareja.");
                return false;
            } else {
                $.post("_operaciones/man_pareja.php", { tipo_operacion: 1, dni: dni, p_dni: p_dni }, function (data) {
                    location.reload()
                });
            }
        });

        $(document).on("click", ".show-page-loading-msg", function () {
            if (document.getElementById("don").value == "") {
                alert("Debe llenar el campo Tipo de Paciente");
                return false;
            }
            if (document.getElementById("sede").value == "") {
                alert("Debe llenar el campo Procedencia.");
                return false;
            }
            if (document.getElementById("medios_comunicacion_id").value == "") {
                alert("Debe seleccionar el campo Programa.");
                return false;
            }
            if (document.getElementById("m_tratante").value == "") {
                alert("Debe llenar el campo Medico Tratante.");
                return false;
            }
            if (document.getElementById("p_nom").value == "") {
                alert("Debe llenar el campo 'Nombre'");
                return false;
            }
            if (document.getElementById("p_apeP").value == "") {
                alert("Debe llenar el campo 'Apellidos'");
                return false;
            }
            if (document.getElementById("p_apeM").value == "") {
                alert("Debe llenar el campo 'Apellidos'");
                return false;
            }
            if (document.getElementById("p_tip").value == "") {
                alert("Debe llenar el campo 'Tipo de Documento'");
                return false;
            }
            if (document.getElementById("p_fnac").value == "") {
                alert("Debe llenar el campo 'Fecha de nacimiento'");
                return false;
            }
            if (document.getElementById("p_dni").value == "") {
                alert("Debe llenar el campo 'DNI'");
                return false;
            }
            if (document.getElementById("p_fnac").value == "") {
                alert("Debe llenar el campo 'Fecha'");
                return false;
            }
            if (document.getElementById("p_tcel").value == "") {
                alert("Debe llenar el campo 'Celular'");
                return false;
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
        }).on("click", ".hide-page-loading-msg", function () {
            $.mobile.loading("hide");
        });

        $(function () {
            $('#alerta').delay(3000).fadeOut('slow');

        });

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
                    break;6
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
            } else if (validarDniValue == '2' || validarDniValue == '3') {
                habilitarCampos(valDni, nom, apeP,apeM, fnac, selectTipDoc,campDni, msgVal, valueDni)
            }
        })

    </script>

    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/_componentes/n_paci/validacion_reniec.php"); ?>

</body>
</html>