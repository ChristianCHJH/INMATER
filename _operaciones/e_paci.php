<?php
    require("../_database/database.php");
    $consulta = $db->prepare("SELECT
    variabledescripcion descripcion, resultado resultado, observacion observacion, fechavalidacion fecha
    from lab_anglo
    where ordencliente = ?");
    $consulta->execute( array($_POST["orden"]) );
    $contenido = "";

    while ($info = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $contenido .= "<tr>";
        $contenido .= "<td>" . $info["descripcion"] . "</td>";
        $contenido .= "<td>" . $info["resultado"] . "</td>";
        $contenido .= "<td>" . $info["observacion"] . "</td>";
        $contenido .= "<td style='text-align: center;'>" . $info["fecha"] . "</td>";
        $contenido .= "</tr>";
    }

    $html = '
    <div role="main" class="ui-content" data-role="content">
        <div class="card-body collapse show">
            <table data-role="table" class="ui-responsive table-stroke ui-table ui-table-reflow">
                <thead class="thead-dark">
                    <tr>
                        <th width="25%" class="text-center">Exámen</th>
                        <th width="25%">Resultado</th>
                        <th width="25%">Informe</th>
                        <th width="25%" style="text-align: center;">Fecha</th>
                    </tr>
                </thead>
                <tbody>' . $contenido . '</tbody>
            </table>
        </div>
    </div>';

    echo json_encode( array(
        "status" => true,
        "resultado" => $html,
    ));
?>