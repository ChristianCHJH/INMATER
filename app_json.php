<?php require("_database/db_tools.php");

$nombre = $_GET['nombre'];
$Paciente = $db->prepare("SELECT dni, nom, ape FROM hc_paciente WHERE unaccent(nom) ILIKE ? OR unaccent(ape) ILIKE ? OR dni ILIKE ?");
$Paciente->execute(array("%$nombre%", "%$nombre%", "%$nombre%"));

if ($Paciente->rowCount() > 0) {
    while ($paci = $Paciente->fetch(PDO::FETCH_ASSOC)) {
        $json_data = array('ape' => $paci['ape'], 'nom' => $paci['nom'], 'dni' => $paci['dni']);
        echo json_encode($json_data, JSON_UNESCAPED_UNICODE);
    }
}
?>
