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
    if (!$login) {
        echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
    }
    require("_database/db_tools.php");
    $dni = $tipoinforme = "";
    // verificar dni paciente
    if (isset($_GET["id"]) && !empty($_GET["id"])) {
        $id = $_GET["id"];
        
        $consulta = $db->prepare("SELECT a_dni, a_sta, a_mue, a_obs, lab, a_exa from hc_analisis WHERE id = ?");
        $consulta->execute(array($id));
        $data = $consulta->fetch(PDO::FETCH_ASSOC);
    } else {
        print("No seleccionó a ningún informe.");
        exit();
    }

    // datos paciente
    $consulta_paciente = $db->prepare("SELECT
        p.ape apellidos, p.nom nombres
        FROM hc_antece a, hc_paciente p
        WHERE a.dni = p.dni and p.dni=?");
    $consulta_paciente->execute(array($data['a_dni']));

    $url="";
    
    if ($consulta_paciente->rowCount() > 0) {
        $paciente = $consulta_paciente->fetch(PDO::FETCH_ASSOC);
        $url = "e_paci.php?id=".$data['a_dni'];
    } else {
        $consulta_paciente = $db->prepare("SELECT
            par.p_ape apellidos, par.p_nom nombres, pac.dni
            FROM hc_pareja par
            INNER JOIN hc_pare_paci pp on pp.p_dni = par.p_dni
            INNER JOIN hc_paciente pac on pac.dni = pp.dni
            WHERE par.p_dni = ?");
        $consulta_paciente->execute(array($data['a_dni']));

        if ($consulta_paciente->rowCount() > 0) {
            $paciente = $consulta_paciente->fetch(PDO::FETCH_ASSOC);
            $url = "e_pare.php?id=".$paciente['dni']."&ip=".$data['a_dni'];
        } else {
            print("No seleccionó a ningún paciente");
            exit();
        }
    }

    // datos usuario
    $rUser = $db->prepare("select role from usuario where userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC); ?>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css?v=1.01"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="_libraries/open-iconic/font/css/open-iconic.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <link rel="stylesheet" href="css/e_repro.css?v=1.00"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
    <div data-role="page" class="page-restriccion" data-dialog="true">
        <div data-role="header" data-theme="b">
            <a href="<?php print($url); ?>" rel="external" class="ui-btn">Ver Historia Clínica</a>
            <h1>Análisis Clínico: <small><?php echo mb_strtoupper($paciente['apellidos'])." ".mb_strtoupper($paciente['nombres']) ?></small></h1>
            <a href="lista.php" rel="external" class="ui-btn">Cerrar</a>
        </div>
        <div role="main" class="ui-content">
            <?php if ( !$data || !!@$data['vencido'] ): ?>
                <div class="alarma vencida">
                    <span class="oi" data-glyph="warning"></span>
                    
                    <?php if ( !$data ): ?>
                        No hay documento de riesgo quirúrgico
                    <?php else: ?>
                        Documento vencido, reemplazarlo con documento vigente
                    <?php endif ?>
                </div>
            <?php endif ?>

            <?php if( $data ): ?>
                <table data-role="table" class="ui-responsive table-stroke">
                    <thead class="thead-dark">
                        <tr>
                            <th width="15%" class="text-center">Fecha de toma de muestra</th>
                            <th width="25%">Observación</th>
                            <th width="15%" class="text-center">Laboratorio</th>
                            <th width="15%" class="text-center">Examen</th>
                            <th width="15%" class="text-center">Descargar</th>
                            <?php
                                if ($user['role'] == 12 || $user['role'] == 15) {
                            ?>
                                <th width="10%">Operaciones</th>
                            <?php
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center"><?php echo date("d-m-Y", strtotime($data['a_mue'])); ?></td>
                            <td><?php print(mb_strtoupper($data['a_obs'])); ?></td>
                            <td><?php print(mb_strtoupper($data['lab'])); ?></td>
                            <td><?php print(mb_strtoupper($data['a_exa'])); ?></td>
                            <td class="text-center">
                                <a href='<?php print("archivos_hcpacientes.php?idArchivo=".$id."_".$data['a_dni']); ?>' target="_blank">Ver/ Descargar</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php else: echo '<h5>¡Aún no hay documentos cargados!</h5>' ?>
                <?php endif ?>
        </div>
    </div>
</body>
</html>