<!DOCTYPE HTML>
<html>
<head>
<?php
     include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <?php require ('_includes/menu_medico.php'); ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista.php">Inicio</a></li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Reporte Poseidon</li>
            </ol>
        </nav>
        <div class="card mb-3">
            <?php
                $fecha = $between = $med = $embins = $ovo = $ini = $fin = $ini_fivicsi = $fin_fivicsi = $tipo_transferencia = "";
                if (isset($_POST) && !empty($_POST)) {
                    if (isset($_POST["med"]) && !empty($_POST["med"])) {
                        $med = $_POST['med'];
                        $between .= " and r.med = '$med'";
                    }
                } else {
                    $ini = date('Y-m-01');
                    $fin = date('Y-m-t');
                    $fecha = 'ftra';
                    $between = " and (
                        (r.f_tra is not null and CAST(r.f_tra as date) between '$ini' and '$fin') or
                        (r.f_tra is null and CAST(r.f_iny as date) between '$ini' and '$fin')
                    )";
                } ?>
            <h5 class="card-header">Filtros</h5>
            <div class="card-body">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Médico</label>
                            <select name='med' class="form-control form-control-sm">
                                <option value="">TODOS</option>
                                <?php
                                    $rEmb = $db->prepare("SELECT codigo, nombre FROM man_medico WHERE estado=1");
                                    $rEmb->execute();
                                    $rEmb->setFetchMode(PDO::FETCH_ASSOC);
                                    $datos1 = $rEmb->fetchAll();
                                    foreach ($datos1 as $row) {
                                        if ($med == $row['codigo']) {$selected="selected";}
                                        else {$selected="";}
                                        print("<option value='".$row['codigo']."' $selected>".mb_strtoupper($row['nombre'])."</option>");
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-3">
            <?php
                $consulta = $db->prepare("SELECT id, nom from lab_user where sta=0");
                $consulta->execute();
                $consulta->setFetchMode(PDO::FETCH_ASSOC);
                $datos = $consulta->fetchAll();

                $item = $ambos =  $fiv = $icsi = $recep = $crio = $ted = $dgp = $piiu = $emb = $od = $don = $tedngs = 0;
                //
                $consulta_poseidon = $db->prepare("SELECT
                    coalesce(r.poseidon, 0) poseidon, coalesce(p.nombre, 'no registrado') categoria, count(*) total
                    from hc_reprod r
                    left join man_poseidon p on p.id = r.poseidon
                    where r.estado = true and r.des_don is not null or coalesce(r.des_dia, 0) < 1
                    group by coalesce(r.poseidon, 0), p.nombre");
                $consulta_poseidon->execute(); ?>
                <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                    <?php print('<small><b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'</small>'); ?>
                </h5>
                <?php
                print('
                <div class="mx-auto">
                    <table class="table table-responsive table-bordered align-middle" style="margin-bottom: 0 !important;">
                        <thead class="thead-dark">
                            <tr>
                                <th>Item</th>
                                <th class="text-center align-middle">Categoría</th>
                                <th class="text-center align-middle">Total</th>');

                if (!empty($med)) {
                    print('<th class="text-center align-middle">'.mb_strtoupper($med).'</th>');
                    $consulta_poseidon_medico = $db->prepare("SELECT
                        coalesce(r.poseidon, 0) poseidon, coalesce(p.nombre, 'no registrado') categoria, count(*) total
                        from hc_reprod r
                        left join man_poseidon p on p.id = r.poseidon
                        where r.estado = true and (r.des_don is not null or coalesce(r.des_dia, 0) < 1) and r.med='".$med."'
                        group by coalesce(r.poseidon, 0), p.nombre");
                    $consulta_poseidon_medico->execute();
                    $consulta_poseidon_medico->setFetchMode(PDO::FETCH_ASSOC);
                    $datos_poseidon_medico = $consulta_poseidon_medico->fetchAll();
                }

                print('</tr></thead><tbody>');
                $item = 1;
                while ($data = $consulta_poseidon->fetch(PDO::FETCH_ASSOC)) {
                    print('<tr>
                        <td class="text-center">'.$item++.'</td>
                        <td>'.mb_strtoupper($data['categoria']).'</td>
                        <td class="text-center">'.mb_strtoupper($data['total']).'</td>');

                    if (isset($consulta_poseidon_medico)) {
                        $band = false;
                        foreach ($datos_poseidon_medico as $key => $value) {
                            if ($data['poseidon'] == $value['poseidon']) {
                                print('<td class="text-center">'.$value['total'].'</td>');
                                $band = true;
                            }
                        }

                        if (!$band) {
                            print('<td class="text-center">0</td>');
                        }
                    }

                    print('</tr>');
                }

                print('</tbody></table></div>'); ?>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $("#guardar").on("click", function () {
                $("#modal_editar").modal('show')
            });
        });
    </script>
</body>
</html>