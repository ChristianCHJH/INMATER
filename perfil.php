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

        <style>
            .ui-dialog-contain {
                max-width: 700px;
                margin: 2% auto 15px;
                padding: 0;
                position: relative;
                top: -15px;
            }

            .scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap; } 
        </style>

      <script>
        $(document).ready(function () {
            // No close unsaved windows --------------------
            var unsaved = false;
            $(":input").change(function () {
            
            unsaved = true;
            
            });
          
          $(window).on('beforeunload', function(){
            if (unsaved) { 
                return 'UD. HA REALIZADO CAMBIOS';
            }
          });
          
          // Form Submit
          $(document).on("submit", "form", function(event){
            // disable unload warning
            $(window).off('beforeunload');
          });
          
            $('.numeros').keyup(function() {
                var $th = $(this);
                $th.val( $th.val().replace(/[^0-9]/g, function(str) { 
                    //$('#cod small').replaceWith('<small>Error: Porfavor ingrese solo letras y números</small>');
                    return ''; } ) );
              //$('#cod small').replaceWith('<small>Aqui ingrese siglas o un nombre corto de letras y números</small>');
            });
        });
      </script>

        <div data-role="header" data-position="fixed">
            <?php
            if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
                print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } else {
                print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } ?>
            <h1>Mi Perfil</h1>
        </div>

      <div class="ui-content" role="main">
        <?php
        $rPerfi = $db->prepare("SELECT * FROM usuario WHERE userx=?;");
        $rPerfi->execute(array($login));
        $perfi = $rPerfi->fetch(PDO::FETCH_ASSOC);
        class Password {
          public static function hash($password) {
              return hash('sha512', $password);
          }
          public static function verify($password, $hash) {
              return ($hash == self::hash($password));
          }
      }
        if (isset($_POST['nom'])) {
          $pass = Password::hash($_POST['pass']);

          updatePerfil($login, $pass, $_POST['nom'], $_POST['mail'], $_POST['cmp'], $_POST['sede']);
          session_destroy();
          header("location:salir.php");

        } ?>

        <form action="" method="post" data-ajax="false">
            <table width="100%" align="center" style="margin: 0 auto;">
                <tr>
                    <td width="22%">Nombre y Apellido</td>
                    <td width="41%"><input name="nom" type="text" required id="nom" value="<?php echo $perfi['nom']; ?>" data-mini="true"/></td>
                    <td width="18%">Nueva Contraseña<br>
                    </td>
                    <td width="19%"><input name="pass" type="password" id="pass" data-mini="true" required/></td>
                </tr>
                <tr>
                    <td>E-Mail</td>
                    <td><input name="mail" type="email" required id="mail" value="<?php echo $perfi['mail']; ?>" data-mini="true"></td>
                    <td>CMP</td>
                    <td><input name="cmp" type="text" id="cmp" class="numeros" data-mini="true" value="<?php echo $perfi['cmp']; ?>"/></td>
                </tr>
                <tr>
                    <td>Sede</td>
                    <td>
                    <select name="sede" id="sede" data-mini="true">
                    <option value="" selected>SELECCIONAR</option>
                    <?php
                    $stmt = $db->prepare("SELECT id, codigo_contabilidad codigo, nombre from sedes where estado = 1 and codigo_contabilidad is not null");
                    $stmt->execute();

                    while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = "";

                        if ($perfi['sede_id'] == $info['id']) $selected = "selected";
                        print('<option value="' . $info['id'] . '" '.$selected.'>'.mb_strtoupper($info['nombre']).' ('.$info['codigo'].')</option>');
                    } ?>
                    </select>
                    </td>
                </tr>
            </table>
            <input type="Submit" value="GUARDAR DATOS"  data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
        </form>
      </div>
    </div>
  </body>
</html>