<?php
include '../seguridad_login.php';

//acciones:

$rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC);
    
    //print_r($_POST); exit;

    if (isset($_GET['pro']) and !empty($_GET['pro'])) {
        $pro=$_GET['pro'];
        
        
        # code...
        if (isset($_GET['accion']) && $_GET['accion'] == 1) {
            $hora_actual = date("H:i:s");
            $proN = $pro.'-DEL-'.$hora_actual;
            $stmt = $db->prepare("UPDATE  lab_aspira SET pro = ?,estado = false WHERE pro=?");
            $stmt->execute(array($proN,$pro));
            
            $stmt = $db->prepare("UPDATE lab_aspira_t SET estado = false WHERE pro=? and estado is true");
            $stmt->execute(array($pro));
            $stmt = $db->prepare("UPDATE lab_andro_cap SET eliminado = true WHERE pro=? and eliminado is false");
            $stmt->execute(array($pro));
            
            $rPro = $db->prepare("SELECT pro_c,ovo_c FROM lab_aspira_dias WHERE pro=? and estado is true");
            $rPro->execute(array($pro));

            if($rPro->rowCount()>0) { while($pro_c = $rPro->fetch(PDO::FETCH_ASSOC)) { 
                $stmt = $db->prepare("UPDATE lab_aspira_dias SET des=0 where pro=? and ovo=? and estado is true"); // vuelve a congelar los ovo/emb q fueron descongelados
                $stmt->execute(array($pro_c['pro_c'],$pro_c['ovo_c']));
            }}
            $stmt = $db->prepare("UPDATE lab_aspira_dias SET pro = ?, estado = false WHERE pro=? and estado is true"); // tiene q ir al final para que primero congele ovo/emb si es necesario
            $stmt->execute(array($proN,$pro));
        }
        # code...
        if (isset($_GET['accion']) && $_GET['accion'] == 2) {  
            if ($_GET['nombre'] !== '') { 
                $new_pro=trim(preg_replace('/\s+/',' ', $_GET['nombre']));
                $rPro = $db->prepare("SELECT pro FROM lab_aspira WHERE pro=? and estado is true");
                $rPro->execute(array($new_pro??''));
                if($rPro->rowCount()==1) { ?>
                    <script> var x = "<?php echo $new_pro; ?>"; alert('Este protocolo '+x+' ya existe! Debe renombrarlo por uno vacio'); </script>
                <?php } else {
                    $stmt = $db->prepare("UPDATE lab_aspira SET pro=? where pro=? and estado is true");
                    $stmt->execute(array($new_pro,$pro));
                    $stmt = $db->prepare("UPDATE lab_aspira_dias SET pro=? where pro=? and estado is true");
                    $stmt->execute(array($new_pro,$pro));
                    $stmt = $db->prepare("UPDATE lab_aspira_dias SET pro_c=? where pro_c=? and estado is true");
                    $stmt->execute(array($new_pro,$pro));
                    $stmt = $db->prepare("UPDATE lab_aspira_t SET pro=?, iduserupdate=? where pro=? and estado is true");
                    $stmt->execute(array($new_pro, $login, $pro));
                    $stmt = $db->prepare("UPDATE lab_andro_cap SET pro=? where pro=? and eliminado is false");
                    $stmt->execute(array($new_pro,$pro));
                }
            } 
        }
    }

    


// Parámetros de paginación y búsqueda
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$pageSize = 10; // Número de registros por página
$offset = ($page - 1) * $pageSize;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Contar el total de registros con búsqueda
$searchQuery = "%$search%";
$rTotal = $db->prepare("SELECT COUNT(*) 
    FROM hc_paciente
    JOIN lab_aspira ON hc_paciente.dni = lab_aspira.dni and lab_aspira.estado is true
    INNER JOIN hc_reprod h on h.estado = true and h.id = lab_aspira.rep
    WHERE lab_aspira.tip NOT IN ('T','X')
    AND (unaccent(hc_paciente.ape) ILIKE :search 
         OR unaccent(hc_paciente.nom) ILIKE :search 
         OR unaccent(lab_aspira.pro) ILIKE :search)");
$rTotal->bindParam(':search', $searchQuery, PDO::PARAM_STR);
$rTotal->execute();
$totalRows = $rTotal->fetchColumn();
$totalPages = ceil($totalRows / $pageSize);

// Obtener los registros para la página actual con búsqueda
$rPaci = $db->prepare("SELECT
    h.id As id_hc_reprod,
    (select count(*) from lab_aspira_dias ela where ela.pro_c = lab_aspira.pro) as eliminar,
    h.dni,
    split_part(lab_aspira.pro,'-',1) AS p1,
    split_part(lab_aspira.pro,'-',-1) AS p2,
    hc_paciente.dni, ape, nom, lab_aspira.pro, lab_aspira.tip, lab_aspira.vec,
    CASE WHEN lab_aspira.dias > 0 THEN lab_aspira.dias - 1 ELSE 0 END AS dias
FROM hc_paciente
JOIN lab_aspira ON hc_paciente.dni = lab_aspira.dni and lab_aspira.estado is true
INNER JOIN hc_reprod h on h.estado = true and h.id = lab_aspira.rep
WHERE lab_aspira.tip NOT IN ('T','X')
AND (hc_paciente.ape ILIKE :search 
     OR unaccent(hc_paciente.nom) ILIKE :search 
     OR unaccent(lab_aspira.pro) ILIKE :search)
ORDER BY CAST(split_part(lab_aspira.pro,'-',-1) AS INTEGER) DESC, 
         CAST(split_part(lab_aspira.pro,'-',1) AS INTEGER) DESC
LIMIT :pageSize OFFSET :offset");
$rPaci->bindParam(':search', $searchQuery, PDO::PARAM_STR);
$rPaci->bindParam(':pageSize', $pageSize, PDO::PARAM_INT);
$rPaci->bindParam(':offset', $offset, PDO::PARAM_INT);
$rPaci->execute();

$rows = $rPaci->fetchAll(PDO::FETCH_ASSOC);

// Crear respuesta JSON
$response = [
    'rows' => $rows,
    'currentPage' => $page,
    'totalPages' => $totalPages
];

header('Content-Type: application/json');
echo json_encode($response);
?>
