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
<title>Embriologo</title>
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
  	
  	max-width: 600px;
	margin: 2% auto 15px;
	padding: 0;
	position: relative;
	top: -15px;
	
}
.scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap; } 
</style>
<?php 
if (isSet($_POST['nom'])) {
updateEmbrio($_POST['idx'],$_POST['nom'],$_POST['mai'],$_POST['cbp'],$_POST['cel'],$_FILES['foto']);
}

$rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
$rUser->execute(array($login));
$user = $rUser->fetch(PDO::FETCH_ASSOC);

if ($user["role"]==2) {
$id = $_GET['id'];
$rEmb = $db->prepare("SELECT * FROM lab_user WHERE id=?");
$rEmb->execute(array($id));
$embrio = $rEmb->fetch(PDO::FETCH_ASSOC); ?>
<div data-role="header" data-position="fixed">
<h1><?php if($id<>"") echo $embrio['nom']; else echo "Nuevo Embriologo"; ?></h1>
</div><!-- /header -->
<div class="ui-content" role="main">

<form action="e_emb.php" method="post" enctype="multipart/form-data" data-ajax="false">
<input type="hidden" name="idx" value="<?php echo $id;?>">  
    <table width="100%" align="center" style="margin: 0 auto;">
      <tr>
        <td width="18%">Nombre y Apellido</td>
        <td width="42%"><input name="nom" type="text" required id="nom" value="<?php echo $embrio['nom']; ?>" data-mini="true"/></td>
        <td width="40%" rowspan="4"><img src="<?php echo "emb_pic/emb_".$id.".jpg"; ?>" alt="" width="200px" height="100px" id="preview" />
		    <small>Adjunte aqui su firma en formato jpg, Ancho:200px, Altura:100px</small><input name="foto" type="file" id="foto" onchange="previewImage(this)" accept="image/jpeg"/ <?php if($id=="") echo "required"; ?>>
          <script type="text/javascript">      
              function previewImage(input) {
                var preview = document.getElementById('preview');
                if (input.files && input.files[0]) {
                  var reader = new FileReader();
                  reader.onload = function (e) {
                    preview.setAttribute('src', e.target.result);
                  }
                  reader.readAsDataURL(input.files[0]);
                } else {
                  preview.setAttribute('src', 'placeholder.png');
                }
              }
            </script></td>
        </tr>
      <tr>
        <td>E-Mail</td>
        <td><input name="mai" type="email" required id="mai" value="<?php echo $embrio['mai']; ?>" data-mini="true"></td>
        </tr>
      <tr>
        <td>CBP</td>
        <td><input name="cbp" type="number" required id="cbp" value="<?php echo $embrio['cbp']; ?>" data-mini="true"/></td>
        </tr>
      <tr>
        <td>Celular</td>
        <td><input name="cel" type="number" id="cel" data-mini="true" value="<?php echo $embrio['cel']; ?>"/></td>
        </tr>
    </table>
     
<input type="Submit" value="GUARDAR DATOS"  data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
</form>
</div><!-- /content -->
<?php } ?>
</div><!-- /page -->

</body>
</html>