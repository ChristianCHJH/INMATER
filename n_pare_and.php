<!DOCTYPE HTML>
<html>
  <head>
  <?php
  include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <title>Clínica Inmater |Nuevo Paciente</title>
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
<body>
  <div data-role="page" class="ui-responsive-panel" id="n_paci" data-dialog="true">
    <style>
      .ui-dialog-contain {

        max-width: 1200px;
        margin: 2% auto 15px;
        padding: 0;
        position: relative;
        top: -15px;

      }

      .scroll_h {
        overflow-x: scroll;
        overflow-y: hidden;
        white-space: nowrap;
      }
    </style>

    <script>
      $(document).ready(function () {
        // No close unsaved windows --------------------
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
            //$('#cod small').replaceWith('<small>Error: Porfavor ingrese solo letras y números</small>');

            return '';
          }));

          //$('#cod small').replaceWith('<small>Aqui ingrese siglas o un nombre corto de letras y números</small>');
        });

        $('.alfanumerico').keyup(function () {

          var $th = $(this);
          $th.val($th.val().replace(/[^a-zA-Z0-9]/g, function (str) {
            //$('#cod small').replaceWith('<small>Error: Porfavor ingrese solo letras y números</small>');

            return '';
          }));

          //$('#cod small').replaceWith('<small>Aqui ingrese siglas o un nombre corto de letras y números</small>');
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
            <a href="lista_and.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>'
            <h1>Nuevo Paciente</h1>
      </div>
    <div class="ui-content" role="main">
      <?php

      if (isset($_POST['p_dni'])) {

        insertPareja('', $_POST['p_dni'], $_POST['p_validarDniValue'], $_POST['p_tip'], $_POST['p_nom'], $_POST['p_apeP'],$_POST['p_apeM'], $_POST['p_fnac'], $_POST['p_tcel'], $_POST['p_tcas'], $_POST['p_tofi'], $_POST['p_mai'], $_POST['p_dir'], $_POST['p_prof'], $_POST['p_san'], $_POST['p_raz'], $_POST['p_med'], '', '', $_POST['don'], $_POST['medios_comunicacion_id'], $_POST['sede_id']);

      ?>
        <script>
            mostrarToastt('success', 'Se creo el Paciente correctamente');
        </script>

    <?php
      }

      ?>
      <form action="n_pare_and.php" method="post" enctype="multipart/form-data" data-ajax="false">
        <div class="scroll_h">
          <table width="100%" align="center" style="margin: 0 auto;">
          <tr>
                            <td class="color_red"style="text-align: left; width: 50%;" colspan="4">*Campos obligatorios</td>
                            <td style="text-align: right; width: 50%;color:green" id="p_msgValidacion" colspan="2"></td>
                        </tr> 
            <tr>
              <td>Tipo de Cliente<span class="color_red">*</span></td>
              <td colspan="2">Programa<span class="color_red">*</span></td>
              <td>Medico<span class="color_red">*</span></td>
              <td>Procedencia<span class="color_red">*</span></td>
            </tr>
            <tr>
              <td>
                <select name="don" id="don" required id="don" data-mini="true">
                  <option value="">Seleccionar</option>
                  <?php
                  $stmt = $db->prepare("SELECT codigo, nombre from tipo_cliente where eliminado = 0;");
                  $stmt->execute();
                  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    print("<option value=" . $data['codigo'] . ">" . $data['nombre'] . "</option>");
                  } ?>
                </select>
              </td>
              <td>
                <select name="medios_comunicacion_id" id="medios_comunicacion_id" required id="medios_comunicacion_id"
                  data-mini="true">
                  <option value="">Seleccionar</option>
                </select>
              </td>
              <td></td>
              <script>
                $(document).ready(function () {
                  $("#don").on('change', function () {

                    $("#don option:selected").each(function () {
                      elegido = $(this).val();
                      $.post("tipocliente.php", {
                        elegido: elegido
                      }, function (data) {
                        $("#medios_comunicacion_id").html(data);
                        $("#medios_comunicacion_id").selectmenu(
                          "refresh");
                      });
                    });
                  });
                });
              </script>
              <td>
                <select name="p_med" id="p_med" required id="p_med" data-mini="true">
                  <option value="" selected>Seleccionar</option>
                  <?php
                  $mTratante = $db->prepare("SELECT id, upper(nombre)nombre ,codigo FROM man_medico where estado=1 order by nombre;");
                  $mTratante->execute();
                  while ($med = $mTratante->fetch(PDO::FETCH_ASSOC)) {
                    print("<option value=" . $med['codigo'] . ">" . $med['nombre'] . "</option>");
                  } ?>
                </select>
              </td>
              <td colspan="2">
                <select name="sede_id" id="sede_id" required id="sede_id" data-mini="true">
                  <option value="">Seleccionar</option>
                  <?php
                  $rSedes = $db->prepare("SELECT id ,upper(trim(nombre)) nombre FROM sedes WHERE estado=1 ORDER BY nombre;");
                  $rSedes->execute();
                  while ($sede = $rSedes->fetch(PDO::FETCH_ASSOC)) {
                    print("<option value=" . $sede['id'] . ">" . $sede['nombre'] . "</option>");
                  }
                  ?>
                </select>

              </td>

            </tr>

              <td>
                <input type="hidden" name="p_validarDniValue" id="p_validarDniValue" value="1">
                <select name="p_tip" id="p_tip" data-mini="true">
                    <option value="DNI" selected>DNI<span class="color_red">*</option>
                    <option value="PAS">PAS</option>
                    <option value="CEX">CEX</option>
                </select>
              </td>
              <td style="display: flex; align-items: center;">
                <input name="p_dni" type="text" id="p_dni" data-mini="true" style="width: 25vh;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" id="p_validarDni" viewBox="0 0 24 24" style="margin-right: 5px; cursor: pointer;fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z" fill="#44d51f"></path><path d="M11.412 8.586c.379.38.588.882.588 1.414h2a3.977 3.977 0 0 0-1.174-2.828c-1.514-1.512-4.139-1.512-5.652 0l1.412 1.416c.76-.758 2.07-.756 2.826-.002z" fill="#44d51f"></path></svg>

              </td>
              <td>F. Nac<span class="color_red">*</span></td>
              <td><input name="p_fnac" type="date" id="p_fnac"  data-mini="true" required /></td>


            <td><select name="p_raz" id="p_raz" data-mini="true">
                <option value="">Raza:</option>
                <option value="Blanca">Blanca</option>
                <option value="Morena">Morena</option>
                <option value="Mestiza">Mestiza</option>
                <option value="Asiatica">Asiatica</option>
              </select></td>
            <td width="12%"><select name="p_san" id="p_san" data-mini="true">
                <option value="">G. Sangre:</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
              </select></td>
            </tr>
            <tr>
              <td width="8%">Nombre(s) <span class="color_red">*</span></td>
              <td ><input name="p_nom" type="text" required id="p_nom" data-mini="true" /></td>
              <td>Ocupación</td>
              <td><input name="p_prof" type="text" id="p_prof" data-mini="true" /></td>
            </tr>
            <tr>
              <td width="8%">Ape. Paterno <span class="color_red">*</span></td>
              <td ><input name="p_apeP" type="text" required id="p_apeP" data-mini="true" /></td>
              <td width="6%">Ape. Materno <span class="color_red">*</span></td>
              <td width="31%"><input name="p_apeM" type="text" required id="p_apeM" data-mini="true" /></td>

            </tr>
            <tr>
              <td>Celular<span class="color_red">*</span></td>
              <td><input name="p_tcel" type="number" step="any" class="numeros"  id="p_tcel"
                  data-mini="true" /></td>
              <td>T. Casa</td>
              <td><input name="p_tcas" type="number" step="any" id="p_tcas" data-mini="true" class="numeros" /></td>
              <td width="7%">E-mail</td>
              <td><input name="p_mai" type="email" id="p_mai" data-mini="true"></td>
            </tr>
            <tr>
              <td>T. Oficina</td>
              <td><input name="p_tofi" type="number" step="any" id="p_tofi" data-mini="true" /></td>
              <td>Dirección</td>
              <td colspan="3"><input name="p_dir" type="text" id="p_dir" data-mini="true" /></td>
            </tr>

          </table>
        </div>

        <input type="Submit" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b"
          class="show-page-loading-msg" data-inline="true" />

      </form>

    </div><!-- /content -->

  </div><!-- /page -->
  <script>
    $(document).on("click", ".show-page-loading-msg", function () {
      if (document.getElementById("don").value == "") {
        alert("El tipo de cliente es obligatorio.");
        return false;
    }
    if (document.getElementById("medios_comunicacion_id").value == "") {
            alert("El programa es un campo obligatorio");
            return false;
        }
    if (document.getElementById("p_med").value == "") {
        alert("El medico es un campo obligatorio");
        return false;
    }
    if (document.getElementById("sede_id").value == "" ) {
            alert("La Procedencia es un campo obligatorio");
            return false;
        }
    if (document.getElementById("p_nom").value == "") {
            alert("El nombre es un campo obligatorio.");
            return false;
        }
    if (document.getElementById("p_apeP").value == "" ) {
        alert("El pellido Paterno es un campo obligatorio.");
        return false;
    }
    if (document.getElementById("p_apeM").value == "" ) {
        alert("El pellido Materno es un campo obligatorio.");
        return false;
    }
        var tipo_documento = document.getElementById("p_tip").value;
        var numero_documento = document.getElementById("p_dni").value;
        if (numero_documento == "") {
            alert("El documento es un campo obligatorio.");
            return false;
        }else {
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
        if (document.getElementById("p_fnac").value == "") {
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
        if (document.getElementById("p_tcel").value == "" ) {
            alert("Los telefono es un campo obligatorio.");
            return false;
        }
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

  <?php include ($_SERVER["DOCUMENT_ROOT"] . "/_componentes/n_paci/validacion_reniec.php"); ?>

</body>

</html>