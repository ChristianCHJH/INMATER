<!DOCTYPE html>
<html lang="es">

<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <style>
        .bg-false{
            color:red;
            font-weight: 600;
        }
        .bg-true{
            color: black;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php
    if (!!$_GET && !!($_GET['dni'])) {
        $dni = $_GET['dni'];
    } else {
        print('no se encontraron datos del paciente'); exit();
    }

    $stmt = $db->prepare("SELECT upper(ltrim(rtrim(ape))) apellidos, upper(ltrim(rtrim(nom))) nombres FROM hc_paciente WHERE dni = ?;");
    $stmt->execute([$dni]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);    

    $stmt = $db->prepare("SELECT role, sede_id FROM usuario WHERE userx = ?;");
    $stmt->execute(array($login));
    $data_user = $stmt->fetch(PDO::FETCH_ASSOC); ?>

    <div data-role="page" class="ui-responsive-panel" id="lista">
        <div data-role="header" data-position="fixed">
            <?php
            print('<a href="e_paci.php?id=' . $_GET["dni"] . '" data-icon="back" class="ui-icon-alt" data-theme="a" rel="external">volver</a>');
            print("<h2>Informes de Procedimientos Médicos - " . $paciente['apellidos'] . " " . $paciente['nombres'] . "</h2>"); ?>
            <a href="salir.php"
                class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power"
                rel="external">Salir</a>
        </div>

        <div data-role="content">
            <form action="" method="post" data-ajax="false" name="form1" id="form1">
                <div data-role="tabs" id="tabs">
                    <div data-role="navbar">
                        <ul>
                            <li><a href="#ecografia" data-ajax="false" class="ui-btn-active">Ecografía</a></li>
                            <li><a href="#histeroscopias" data-ajax="false">Histeroscopias</a>
                            </li>
                            <li><a href="#hsg_hes" data-ajax="false">HSG - HES</a></li>
                        </ul>
                    </div>

                    <div id="ecografia">
                        <table data-role="table" data-filter="true" data-input="#filtro"
                            class="table-stripe ui-responsive">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Observación</th>
                                    <th>Informe</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $db->prepare("SELECT * FROM hc_analisis WHERE a_dni = ? AND lab = 'eco' ORDER BY a_fec DESC;");
                                $stmt->execute([$dni]);
                                
                                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $bgEstado= $data['estado']==0 ? 'bg-false' : 'bg-true';
                                    print('
                                    <tr class="'.$bgEstado.'">
                                        <td>' . $data['a_exa'] . '</td>
                                        <td>' . $data['a_obs'] . '</td>
                                        <td>' . (file_exists("analisis/" . $data['id'] . "_" . $data['a_dni'] . ".pdf") ? "<br><a href='archivos_hcpacientes.php?idArchivo=" . $data['id'] . "_" . $data['a_dni'] . "' target='new'>Descargar</a>" : "-") . '</td>
                                        <td>' . date("d-m-Y", strtotime($data['a_fec'])) . '</td>
                                        <td>' . ($data['estado'] == 1 ? 'Realizado' : 'Eliminado') .'</td>
                                    </tr>');
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <div id="histeroscopias">
                        <table data-role="table" data-filter="true" data-input="#filtro"
                            class="table-stripe ui-responsive">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>IDX</th>
                                    <th>Informe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $db->prepare("SELECT
                                    id, fecha, tipo_analisis, dni, upper(nombre) nombre, fnac
                                    , a_parrafo1, imagen1parr1, imagen2parr1, imagen3parr1, a_parrafo2, imagen1parr2, imagen2parr2, imagen3parr2
                                    , idx, comentario, estado, idusercreate, iduserupdate, createdate
                                    FROM analisis_histeroscopia
                                    WHERE dni = ?
                                    ORDER BY fecha DESC;");
                                $stmt->execute([$dni]);

                                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    print('
                                    <tr>
                                        <td>' . date("d-m-Y", strtotime($data['fecha'])) . '</td>
                                        <td>' . $data['idx'] . '</td>
                                        <td><a href="reportes_fpdf/reporte_histeroscopias.php?id=' . $data['id'] . '" target="_blank" rel="external">PDF</a></td>
                                    </tr>');
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <div id="hsg_hes">
                        <?php
                        $stmt = $db->prepare("SELECT * FROM hc_antece_hsghes WHERE dni = ? and estado = true ORDER BY fec DESC;");
                        $stmt->execute([$dni]);

                        print('<a href="e_ante_hsghes.php?path=n_procedimientos_medicos&dni=' . $dni . '&id=" class="ui-btn ui-mini ui-btn-inline" data-theme="a" rel="external">Nuevo</a>'); ?>

                        <table data-role="table" data-filter="true" data-input="#filtrongs"
                            class="table-stripe ui-responsive">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Conclusión</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    print(
                                        '<tr>
                                            <td>' . ($data['lab'] <> '' ? date("d-m-Y", strtotime($data['fec'])) . ' (' . $data['lab'] . ')' : '<a href="e_ante_hsghes.php?path=n_procedimientos_medicos&dni=' . $dni . '&id=' . $data['fec'] . '" rel="external">' . date("d-m-Y", strtotime($data['fec'])) . '</a>') . '<br>
                                                ' . (file_exists('analisis/hsghes_' . $dni . '_' . $data['fec'] . '.pdf') ? '<a href="archivos_hcpacientes.php?idArchivo=hsghes_' . $dni . '_' . $data['fec'] . '" target="new">Ver/Descargar</a>' : '') . '
                                            </td>
                                            <td>' . $data['tip'] . '</td>
                                            <td>' . ($data['con'] == 'P' ? 'En proceso..' : $data['con']) . '</td>
                                        </tr>'
                                    );
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>

        <div data-role="footer">
            <h4>Clínica Inmater</h4>
        </div>

    </div>
    <script>
    console.log('n_procedimientos_medicos.php')
    </script>
</body>

</html>