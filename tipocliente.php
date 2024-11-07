<?php
 require("_database/db_tools.php"); 
$html='';
$tipoCliente= $_POST['elegido'];
if ($tipoCliente=="P" || $tipoCliente=="D") {
    $stmt = $db->prepare("SELECT id, nombre,grupo from man_medios_comunicacion where estado = 1");
    $stmt->execute();
    echo'<option value="">Seleccionar</option>';
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
       $grupo=$data['grupo'];
       if($grupo!='1'){
        echo"<option disabled value=".$data['id'].">".$data['nombre']."</option>";
       }else{
        echo"<option  value=".$data['id'].">".$data['nombre']."</option>";
       }
        
	
    }
}
else if ($tipoCliente=="EXT") {
    $stmt = $db->prepare("SELECT id, nombre,grupo from man_medios_comunicacion where estado = 1");
    $stmt->execute();
    
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $grupo=$data['grupo'];
        if($grupo!='3'){
         echo"<option disabled value=" . $data['id'] . ">" . $data['nombre']."</option>";
        }else{
         echo"<option  value=" . $data['id'] . ">" . $data['nombre']."</option>";
        }
    }
}
else if ($tipoCliente=="EMP") {
    $stmt = $db->prepare("SELECT id, nombre,grupo from man_medios_comunicacion where estado = 1");
    $stmt->execute();
    
    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $grupo=$data['grupo'];
        if($grupo!='2'){
         echo"<option disabled value=" . $data['id'] . ">" . $data['nombre']."</option>";
        }else{
         echo"<option  value=" . $data['id'] . ">" . $data['nombre']."</option>";
        }
    }
}
else{
    echo'<option value="">Seleccionar*</option>';
}
?>