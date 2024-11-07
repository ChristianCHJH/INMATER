<!DOCTYPE HTML>
<html>

<head>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
        integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
    <title>Clínica Inmater | Mantenimiento de Sedes</title>
</head>

<body>
    <div class="container">
        <?php require ('_includes/menu_facturacion.php'); ?>
        <?php
      $stmt = $db->prepare("SELECT * FROM usuario WHERE userx=?");
      $stmt->execute(array($login));
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      $Rpop = $db->prepare("SELECT id, codigo, upper(nombre) nombre FROM sedes_contabilidad WHERE eliminado = 0 order by nombre;");
      $Rpop->execute();
    ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_facturacion.php">Inicio</a></li>
                <li class="breadcrumb-item" aria-current="page">Mantenimiento</li>
                <li class="breadcrumb-item active" aria-current="page">Sedes</li>
            </ol>
        </nav>
        <div class="card mb-3">
            <h5 class="card-header">Información General</h5>
            <div class="card-body">
                <div class="row pb-2">
                    <!-- codigo -->
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Código*</span>
                            <input class="form-control form-control-sm" type="text" name="codigo" id="codigo"
                                required />
                        </div>
                    </div>
                    <!-- nombre -->
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Nombre*</span>
                            <input class="form-control form-control-sm" type="text" name="nombre" id="nombre"
                                required />
                        </div>
                    </div>
                </div>
                <div class="row pb-2">
                    <?php
          if($data1["role"] == "3" or $data1["role"] == "10") { ?>
                    <!-- agregar -->
                    <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                        <input class="form-control btn btn-danger btn-sm" type="Submit" name="agregar" id="agregar"
                            value="Agregar" />
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    print('<div><b>Total de Registros:</b> '.$Rpop->rowCount().'</div>'); ?>
        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Buscar..."
            title="escribe un nombre">
        <div class="card mb-3">
            <div class="card-body collapse show" id="collapseExample">
                <table class="table table-responsive table-bordered align-middle" style="height: 50vh;"
                    id="tb_servicios">
                    <thead class="thead-dark">
                        <th class="text-center">Id</th>
                        <th class="text-center">Sede</th>
                        <th>Código</th>
                        <?php
            if($data1["role"] == "3" or $data1["role"] == "10") { ?>
                        <th class="text-center">Acción</th>
                        <?php } ?>
                    </thead>
                    <tbody>
                        <?php
              while ($info = $Rpop->fetch(PDO::FETCH_ASSOC)) {
                print("<tr>
					<td class='text-center'>".$info['id']."</td>
				    <td class='text-center'>".$info['nombre']."</td>
                  <td>".mb_strtoupper($info['codigo'])."</td>");

                if($data1["role"] == "3" or $data1["role"] == "10") {
                  print("<td class='text-center'>
                        <a href='sede_man_edit.php?id=".$info['id']."'><i class='far fa-edit'></i></a></td>");
                }

                print("</tr>");
              }

              if ($Rpop->rowCount() < 1) {echo '<p><h3 class="text_buscar">¡ No existen datos !</h3></p>';} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/sede_man.js?v=1"></script>
</body>

</html>