<!DOCTYPE HTML>
<html>
<head>
    <?php
       include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <!-- <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script type="text/javascript">
        function PrintElem(elem) {
            var data = $(elem).html();
            var mywindow = window.open('', 'Imprimir', 'height=600,width=800');
            mywindow.document.write('<html><head><title>Imprimir</title>');
            mywindow.document.write('<style> @page {margin: 0px 0px 0px 5px;} table {border-collapse: collapse;font-size:10px;} .table-stripe td {border: 1px solid black;} .tablamas2 td {border: 1px solid white;} .mas2 {display: block !important;} .noVer, .ui-table-cell-label {display: none;} a:link {pointer-events: none; cursor: default;}</style>');
            mywindow.document.write("</head><body><p style='align: center'>Reporte Fecundación In Vitro</p>");
            mywindow.document.write(data);
            mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
            mywindow.document.write('</body></html>');
            return true;
        }
    </script>
</head>
<?php 
    if( isset( $_POST['tipo_vencimiento'] ) and !empty( $_POST['tipo_vencimiento'] ) )
    {
        foreach ($_POST['tipo_vencimiento'] as $id => $tipo_vencimiento) {
            $stmt = $db->prepare("UPDATE restricciones SET tipo_vencimiento = ?, vencimiento = ?, tipo_vencimiento_donante = ?, vencimiento_donante = ? WHERE id = ?");
            $stmt->execute([
                $_POST['tipo_vencimiento'][$id],
                $_POST['vencimiento'][$id],
                $_POST['tipo_vencimiento_donante'][$id],
                $_POST['vencimiento_donante'][$id],
                $id
            ]);
        }
    }

    $restricciones = $db->prepare("SELECT * FROM restricciones");
    $restricciones->execute();

    function print_tipo_vencimiento( $name, $id, $selected = '' ) {
        return '<select name="'.$name.'['.$id.']" data-id="'.$id.'" class="'.$name.' form-control" required="required">
            <option value="no_vence" '. ($selected == 'no_vence' ? 'selected' : '') .'>No vence</option>
            <option value="dias" '. ($selected == 'dias' ? 'selected' : '') .'>Por días</option>
            <option value="procedimientos" '. ($selected == 'procedimientos' ? 'selected' : '') .'>Por procedimiento</option>
        </select>';
    }
 ?>
<body>
	<?php require ('_includes/repolab_menu.php'); ?>
    <div class='container'>
        <h4>Restricciones para reproducción asistida</h4>
        <form action="" method="POST">
            <table class="table">
                <thead>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>PACIENTE: tipo de vencimiento</th>
                    <th>PACIENTE: vencimiento</th>
                    <th>DONANTE: tipo de vencimiento</th>
                    <th>DONANTE: vencimiento</th>
                </thead>
                <tbody>
                <?php while( $restriccion = $restricciones->fetch(PDO::FETCH_ASSOC ) ): ?>
                    
                    <tr>
                        <td><?php echo $restriccion['nombre'] ?></td>
                        <td><?php echo $restriccion['tipo'] ?></td>
                        <td><?php echo print_tipo_vencimiento('tipo_vencimiento', $restriccion['id'], $restriccion['tipo_vencimiento']) ?></td>
                        <td><input type="text" class="form-control" name="vencimiento[<?php echo $restriccion['id'] ?>]" value="<?php echo $restriccion['vencimiento'] ?>" placeholder="Cantidad límite para vencer" <?php echo $restriccion['tipo_vencimiento'] == 'no_vence' ? 'disabled' : '' ?>></td>
                        <td><?php echo print_tipo_vencimiento('tipo_vencimiento_donante', $restriccion['id'], $restriccion['tipo_vencimiento_donante']) ?></td>
                        <td><input type="text" class="form-control" name="vencimiento_donante[<?php echo $restriccion['id'] ?>]" value="<?php echo $restriccion['vencimiento_donante'] ?>" placeholder="Cantidad límite para vencer" <?php echo $restriccion['tipo_vencimiento_donante'] == 'no_vence' ? 'disabled' : '' ?>></td>
                    </tr>
                        
                <?php endwhile ?>
                </tbody>
            </table>

            <input type="submit" value="Guardar" class="btn btn-success">
        </form>

    </div>

    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>

    <script>
        jQuery(document).ready(function($) {
            $('.tipo_vencimiento').on('change', function(){
                $('input[name="vencimiento['+$(this).data('id')+']"]').prop('disabled', false);

                if( $(this).val() == 'no_vence' )
                {
                    $('input[name="vencimiento['+$(this).data('id')+']"]').prop('disabled', true);
                }
            });

            $('.tipo_vencimiento_donante').on('change', function(){
                $('input[name="vencimiento_donante['+$(this).data('id')+']"]').prop('disabled', false);

                if( $(this).val() == 'no_vence' )
                {
                    $('input[name="vencimiento_donante['+$(this).data('id')+']"]').prop('disabled', true);
                }
            });
        });
    </script>
</body>
</html>