<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <!DOCTYPE HTML>
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <title>Perfil</title>
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="perfil" data-dialog="true">
        <script>
            $(document).ready(function () {
                var unsaved = false;
                $(":input").change(function () {
                    unsaved = true;
                });

                $(window).on('beforeunload', function(){
                    if (unsaved) { 
                        return 'UD. HA REALIZADO CAMBIOS';
                    }
                });

                $(document).on("submit", "form", function(event) {
                    $(window).off('beforeunload');
                });
            });
        </script>

        <div data-role="header" data-position="fixed">
            <?php
            $stmt = $db->prepare("SELECT * FROM hc_paciente_accesos where estado = 1 and dni = ?;");
            $stmt->execute([$login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC); ?>
            <a href="lista_paciente.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
            <h1>Cambiar contraseña</h1>
        </div>

        <div class="ui-content" role="main">
            <form action="" method="post" data-ajax="false">
                <?php
                print('
                <div><span style="font-size: 14px;padding: .446em;">Mis nombres: <b><em>' . $user["nombres"] . '</em></b></span></div>
                <div><span style="font-size: 14px;padding: .446em;">Mi número de documento: <b><em>' . $user["dni"] . '</em></b></span></div>

                <div>
                    <input data-inline="true" data-mini="true" data-theme="b" id="old_password" name="old_password" placeholder="Escribir la contraseña antigua" type="password" required/>
                    <input data-inline="true" data-mini="true" data-theme="b" id="new_password" name="new_password" placeholder="Escribir la contraseña nueva" type="password" required/>
                </div>');

                if (isset($_POST['old_password']) && !empty($_POST['old_password']) && isset($_POST['new_password']) && !empty($_POST['new_password'])) {
                    $stmt = $db->prepare("UPDATE hc_paciente_accesos
                    set acceso=?, iduserupdate=?
                    where dni=? and acceso=?");
                    $stmt->execute([$_POST['new_password'], $login, $login, $_POST['old_password']]);
    
                    if ($stmt->rowCount()) {
                        print('<div><span id="message" style="font-size: 14px;padding: .446em; color: green;"><em>Se actualizó correctamente la nueva contraseña!</em></span></div>');
                    } else {
                        print('<div><span id="message" style="font-size: 14px;padding: .446em; color: red;"><em>No se indicó correctamente la contraseña anterior!</em></span></div>');
                    }
                } ?>
                <input type="Submit" value="Guardar datos"  data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
            </form>
        </div>
    </div>
</body>
</html>