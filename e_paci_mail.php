<!DOCTYPE html>
<html lang="es">
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Inmater | Lista Ecografía</title>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" href="css/global.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="e_analisis" data-dialog="true">
        <div data-role="header" data-theme="b" data-position="fixed">
            <?php
            if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
                print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } else {
                print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            }
            
            $stmt = $db->prepare("SELECT * from hc_paciente where dni = ?");
            $stmt->execute([$_GET["id"]]);
            $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

            $nombres = ucwords(mb_strtolower($paciente["nom"] . " " . $paciente["ape"]));
            $email = mb_strtolower(trim($paciente["mai"]));
            print("<h1>$nombres</h1>");
            ?>
        </div>

        <div class="ui-content" role="main">
            <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
                <div class="ui-grid-a">
                    <div class="ui-block-d">
                        <label style="font-size: 13px; padding: .4em;">
                        <?php
                        if (empty($email)) {
                            print("Este paciente no tiene un correo registrado en la Historia Clínica, debe ingresarlo.");
                        } else {
                            print("Este es el correo registrado en la Historia Clínica: <b>$email</b><br><br>Sin embargo podrías indicarnos otro correo al cual enviaremos la información para poder acceder a tus ecografías.");
                        } ?>
                        </label>
                    </div>
                </div>

                <div class="ui-grid-a">
                    <?php print('<input id="dni" name="dni" type="hidden" value="' . $_GET["id"] . '">');  ?>
                    <form action="" id="form_acceso" style="text-align: center;">
                        <?php
                        print('
                        <div>
                            <input data-inline="true" data-mini="true" data-theme="b" id="correo_acceso" name="correo_acceso" placeholder="Escribir correo electrónico" type="email" value="' . $email . '" required/>
                        </div>

                        <div>
                            <input data-inline="true" data-mini="true" data-theme="b" id="nombres" name="nombres" placeholder="Escribir los nombres" type="text" value="' . $nombres . '" required/>
                        </div>'); ?>
                        <span id="message" style="color: green;"></span>
                        <div>
                            <input data-icon="mail" data-inline="true" data-mini="true" data-theme="b" id="enviar_acceso" name="enviar_acceso" type="submit" value="Enviar">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#form_acceso").submit(function(e){
                e.preventDefault();
                console.log("form_acceso");

                $.ajax({
                    type: 'POST',
                    url: '_operaciones/e_paci_mail.php',
                    async: false,
                    data: {
                        tipo: "enviar_acceso",
                        dni: $("#dni").val(),
                        nombres: $("#nombres").val(),
                        email: $("#correo_acceso").val(),
                    },
                    success: function (result) {
                        $("#message").text("Correo enviado satisfactoriamente!");
                    },
                    error: function (jqXHR, exception) {
                        var msg = '';
                        console.log(jqXHR);
                        console.log(exception);

                        if (jqXHR.status === 0) {
                            msg = 'Not connect.\n Verify Network.';
                        } else if (jqXHR.status == 404) {
                            msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                            msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                            msg = 'Time out error.';
                        } else if (exception === 'abort') {
                            msg = 'Ajax request aborted.';
                        } else {
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }

                        console.log(msg);
                    },
                });
            });
        });
    </script>
</body>
</html>