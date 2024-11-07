<!DOCTYPE HTML>
<html>
<head>
<?php
     include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-1.11.1.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="jstickytableheaders.js" crossorigin="anonymous"></script>
</head>
<body>
    <script>
        $(document).ready(function () {
            $(".table-stripe").stickyTableHeaders();
        });
    </script>
    <?php
    // 
    $rPaci = $db->prepare("
    select
    b.pro, string_agg(c.ovo::text, ',') ovo, b.fec, c.adju transferido_dni, e.ape transferido_ape, e.nom transferido_nom, d.dni, d.ape, d.nom, a.med
    from hc_reprod a
    inner join hc_paciente d on d.dni=a.dni
    inner join lab_aspira b on b.rep=a.id and b.estado is true
    inner join lab_aspira_dias c on c.pro=b.pro and c.des=0 and c.d0f_cic='C' and c.estado is true
    inner join hc_paciente e on e.dni=c.adju
    where a.estado = true
    group by c.pro,b.pro,c.adju,e.ape,e.nom,d.dni,a.med
    order by b.fec");
    $rPaci->execute();
    require ('_includes/repolab_menu.php');
    ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Reportes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reserva de Óvulos</li>
            </ol>
        </nav>

        <div class="card mb-3">
            <?php print('<h5 class="card-header">Detalle: <small>'.$rPaci->rowcount().' registros encontrados</small></h5>'); ?>
            <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Buscar Paciente.." title="escribe nombre de paciente">
            <div class="card-body mx-auto">
                <table class="table table-responsive table-bordered align-middle table-stripe" id="myTable">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%" class="text-center align-middle">Item</th>
                            <th width="10%" class="text-center align-middle">Protocolo</th>
                            <th width="30%" class="text-center align-middle">N° Ovo</th>
                            <th width="10%" class="text-center align-middle">Médico</th>
                            <th width="15%" class="text-center align-middle">Paciente</th>
                            <th width="15%" class="text-center align-middle">Reservado a:</th>
                            <th width="15%" class="text-center align-middle">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $contador = 1;
                        while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr>
                                <?php
                                    print('
                                    <td width="5%" class="text-center">'.$contador++.'</td>
                                    <td width="10%" class="text-center">'.$paci['pro'].'</td>
                                    <td width="30%" class="text-center">'.$paci['ovo'].'</td>
                                    <td width="10%" class="text-center">'.$paci['med'].'</td>
                                    <td width="15%"><small>'.mb_strtoupper($paci['ape']).' '.mb_strtoupper($paci['nom']).'</small></td>
                                    <td width="15%"><small>'.mb_strtoupper($paci['transferido_ape']).' '.mb_strtoupper($paci['transferido_nom']).'</small></td>
                                    <td width="15%" class="text-center">'.$paci['fec'].'</td>');
                                ?>
                            </tr>
                        <?php }

                        if ($rPaci->rowCount() < 1) { echo '<p><h3>¡ No hay registros !</h3></p>'; }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function myFunction() {
          var input, filter, table, tr, td, i;
          input = document.getElementById("myInput");
          filter = input.value.toUpperCase();
          table = document.getElementById("myTable");
          tr = table.getElementsByTagName("tr");

          for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[4];

            if (td) {
              if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
              } else {
                tr[i].style.display = "none";
              }
            }       
          }
        }
    </script>
</body>
</html>