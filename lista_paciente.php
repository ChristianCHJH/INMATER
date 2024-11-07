<?php
session_start();
error_reporting( error_reporting() & ~E_NOTICE );
?>
<!DOCTYPE HTML>
<html>
<head>
    <?php
    $login = $_SESSION['login_paciente'];
    $dir = $_SERVER['HTTP_HOST'].substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
    if (!$login) {
        header("Location: login.php");
    }
    require("_database/database.php"); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="lista">
        <?php
        // consulta medicos
        $stmt = $db->prepare("SELECT codigo, nombre FROM man_medico where estado = 1;");
        $stmt->execute();
        $medicos = $stmt->fetchAll();

        // 
        $stmt = $db->prepare("SELECT * FROM hc_paciente where dni = ?;");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); ?>

        <div data-role="panel" id="indice_paci">
            <img src="_images/logo_login_sinfondo.png" width="180" heigth="40" style="display: block; margin: auto;">

            <ul data-role="listview" data-inset="true" data-theme="a">
                <?php
                print('<li data-icon="info"><a href="paciente_perfil.php" rel="external" style="font-size: 14px;">Cambiar Contraseña</a></li>');  ?>
            </ul>
        </div>

        <div data-role="header" data-position="fixed">
            <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU</a>
            <h1 style="color: #fff;"><?php print("Bienvenido(a) " . ucwords(strtolower($user["ape"])) . " " . ucwords(strtolower($user["nom"]))); ?></h1>
            <a href="login.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div>

        <div data-role="content">
            <!-- <label for=""><em>Los informes presentados no llevan la firma oficial de los médicos.</em></label> -->
            <div id="one">
                <table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive">
                    <thead>
                        <tr>
                            <th>Ecografía</th>
                            <th>Médico</th>
                            <th>Ver/ Descargar</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $stmt = $db->prepare("SELECT a.*, coalesce(ma.nombre_base, '-') nombre_base, coalesce(ma.nombre_original, '-') nombre_original
                            FROM hc_analisis a
                            left join man_archivo ma on ma.id = a.archivo_id
                            where a.estado = 1 and a.lab = ? and a.a_dni = ?
                            order by a.a_mue desc;"
                        );
                        $stmt->execute(['eco', $user["dni"]]);

                        while ($anal = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $analisis='';
                            $video='';

                            if (file_exists('analisis/' . $anal['id'] . '_' . $anal['a_dni'] . '.pdf') && file_exists('storage/analisis_archivo/' . $anal['nombre_base'])) {
                                $analisis='<em><a href="archivos_hcpacientes.php?idArchivo=' . $anal['id'] . '_' . $anal['a_dni'] . '" target="new" style="font-size: 14px;">Informe</a></em> - ';
                                $video = '<em><a href="archivos_hcpacientes.php?idStorage=analisis_archivo/' . $anal['nombre_base'] . '" target="new" style="font-size: 14px;">Vídeo</a></em>';

                                $link_video = '';
                                $stmt = $db->prepare("SELECT * from google_drive_response where drive_id <> '0' and estado = 1 and tipo_procedimiento_id = 2 and procedimiento_id = ? order by id desc limit 1;");
                                $stmt->execute([$anal['id']]);

                                if ($stmt->rowCount() > 0) {
                                    $data = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $link_video = "<em><a href='https://drive.google.com/open?id=" . $data['drive_id'] . "' style='margin: .446em; font-size: 12px;' target='new'>Vídeo</a></em>";
                                }

                                print('<tr>
                                    <th><em>' . mb_strtoupper($anal['a_exa']) . '</em></th>
                                    <td>' . ucwords(mb_strtolower(array_search($anal['a_med'], array_column($medicos, 'codigo')) ? $medicos[array_search($anal['a_med'], array_column($medicos, 'codigo'))]["nombre"] : '-')) . '</td>
                                    <th>'. $analisis . $link_video . '</th>
                                    <td>' . date("d-m-Y", strtotime($anal['a_mue'])) . '</td>
                                </tr>');
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div data-role="footer"><h4>Clínica Inmater</h4></div>
    </div>
</body>
</html>