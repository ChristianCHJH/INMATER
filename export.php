<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="js/jquery-1.11.1.min.js"></script>

<script>
$(document).ready(function () {
$("#btnExport").click(function (e) {
    window.open('data:application/vnd.ms-excel,' + $('#dvData').html());
    e.preventDefault();
});
});	
</script>

</head>

<body>
<?php $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
$rUser->execute(array($login));
$user = $rUser->fetch(PDO::FETCH_ASSOC);
 
//if ($user["role"]==1) {
$rPaci = $db->prepare("SELECT * FROM hc_paciente ORDER by ape,nom ASC");
$rPaci->execute();
//}
?> 

<input type="button" id="btnExport" value="Exportar Excel" />
<br/>
<br/>
<div id="dvData">
    <table>
        <tr>
            <th>DNI</th>
            <th>NOMBRE</th>
            <th>APELLIDOS</th>
            <th>MAIL</th>
            <th>CELULAR</th>
            <th>DIRECCION</th>
            <th>F. NACIMIENTO</th>
            <th>F. INGRESO</th>
            <th>MEDICO</th>
        </tr>
        <?php while($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?php echo $paci["dni"];?></td>
            <td><?php echo $paci["nom"];?></td>
            <td><?php echo $paci["ape"];?></td>
            <td><?php echo $paci["mai"];?></td>
            <td><?php echo $paci["tcel"];?></td>
            <td><?php echo $paci["dir"];?></td>
            <td><?php echo $paci["fnac"];?></td>
            <td><?php echo $paci["fec"];?></td>
            <td><?php echo $paci["med"];?></td>
        </tr>
        <?php } ?>
    </table>
</div> 

</body>

</html>