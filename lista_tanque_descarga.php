<?php
session_start();
ini_set("display_errors","1");
error_reporting(E_ALL);
?>
<!DOCTYPE HTML>
<html>

<head>
    <?php
        $login = $_SESSION['login'];
        $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
        if ($_SESSION['role'] <> 2) {
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
        }
        require("_database/db_tools.php");
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
    <title>Inmater Clínica de Fertilidad | Estado de tanque</title>
</head>

<body>
    <?php require ('_includes/menu_andrologia.php'); ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <a class="breadcrumb" href="lista_and.php">
                <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
            </a>

            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="lista_and.php">Andrología</a></li>
                <li class="breadcrumb-item active" aria-current="page">Estado de Tanque</li>
            </ol>
        </nav>
        <?php
        $stmt = $db->prepare("SELECT t.n_tan tanque, d.canister, d.varilla, d.vial, d.documento, p.p_dni dni, p.p_ape apellidos, p.p_nom nombres, d.observacion
            from tanque_descarga d
            inner join lab_tanque t on t.tan = d.tanque and t.sta = 1
            left join hc_pareja p on p.p_dni = d.sta
            order by d.createdate desc;");
        $stmt->execute(); ?>

        <div class="card mb-3" id="resultados">
            <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                <small><b>Lista</b></small>
            </h5>
            <div>
                <?php
                print('
                    <table width="100%" class="table table-responsive table-bordered align-middle" style="margin-bottom: 0 !important;" id="lista_viales">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">Tanque</th>
                                <th class="text-center">Canister</th>
                                <th class="text-center">Varilla</th>
                                <th class="text-center">Vial</th>
                                <th class="text-center">DNI</th>
                                <th class="text-center">Apellidos y Nombres</th>
                                <th class="text-center">Documento</th>
                                <th class="text-center">Observación</th>
                            </tr>
                        </thead>
                        <tbody>');

                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $documento_path = "";

                    if (!!$data['documento']) {
                        // $documento_path = "tanque_descarga/" . $data['documento'];
                        $documento_path = '<a href="tanque_descarga/'.$data['documento'].'" target="_blank"><img src="_images/pdf.png" height="20" width="20" alt="icon name"></a>';
                    }

                    print('
                    <tr>
                        <td class="text-center">'.$data['tanque'].'</td>
                        <td class="text-center">'.$data['canister'].'</td>
                        <td class="text-center">'.$data['varilla'].'</td>
                        <td class="text-center">'.$data['vial'].'</td>
                        <td class="text-center">'.$data['dni'].'</td>
                        <td class="text-center">' . ucwords(mb_strtolower($data["apellidos"])) .  " " . ucwords(mb_strtolower($data["nombres"])) . '</td>
                        <td class="text-center">'.$documento_path.'</td>
                        <td class="text-center">'.mb_strtolower($data['observacion']).'</td>');
                    print('</tr>');
                }

                print('
                </tbody>
                    </table>'); ?>
            </div>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>