<!DOCTYPE html>
<html lang="en">
<head>
<?php
     require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
     include 'seguridad_login.php';
     require "_database/database.php"; ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/shared.css">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <title>Clínica Inmater | Reporte Data</title>
	<script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
</head>
<body>
    <div class="loader"><img src="_images/load.gif" alt="Inmater Loading"><label>Cargando...</label></div>
    <div class="box container">
        <div>
            <nav aria-label="breadcrumb">
							<?php
							if (isset($_GET["path"]) && !empty($_GET["path"])) {
								print('
								<a class="breadcrumb" href="'.$_GET["path"].'.php" style="background-color: #72a2aa;">
									<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
								</a>');
							} else {
								print('
								<a class="breadcrumb" href="lista.php" style="background-color: #72a2aa;">
									<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
								</a>');
							}
                            $key=$_ENV["apikey"];
                            ?>
            </nav>
            <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
            <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
            <form action="" method="post" name="form" id="form">
                <div class="card mb-3">
                    <h5 class="card-header"><small><b>Reporte Data</b></small></h5>
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Médico</span>
                                    <select class="form-control form-control-sm chosen-select" name='medico' id="medico" multiple>
                                        <option value=''>TODOS</option>
                                        <?php
                                        $stmt=$db->prepare("SELECT userX, upper(nom) nom from usuario where role=1 order by nom;");
                                        $stmt->execute();
                                        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value='".$user["userx"]."'>".$user["nom"]."</option>");
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Programa</span>
                                    <select class="form-control form-control-sm" name='medio_comunicacion' id="medio_comunicacion">
                                        <option value='' >TODOS</option>
                                        <?php
                                        $stmt=$db->prepare("SELECT id, upper(nombre) nombre from man_medios_comunicacion mmc;");
                                        $stmt->execute();
                                        while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value='".$item["id"]."'>".$item["nombre"]."</option>");
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Mostrar desde</span>
                                    <input class="form-control form-control-sm" name="ini" type="date" id="ini"style="width: 150px;">
                                    <span class="input-group-text">hasta</span>
                                    <input class="form-control form-control-sm" name="fin" type="date" id="fin"style="width: 150px;">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <input type="Submit" class="btn btn-danger" value="Mostrar"/>
                                    <a href="javascript:void(0)" style="margin: 6px 10px 0;" id="btn_descargar_reporte"><img src="_images/excel.png" height="18" width="18" alt="icon name"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card row content">
            <div class="card-body">
                <table class="table table-bordered" id="table_main">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="min-width: 250px;" rowspan="2">Médico</th>
                            <th class="text-center" colspan="5">Aspiraciones/ Pacientes Aspirados</th>
                            <th class="text-center" colspan="2">Inseminación</th>
                            <th class="text-center" colspan="2">Desarrollo embrionario Extras</th>
                            <th class="text-center" colspan="2">Crio preservación</th>
                            <th class="text-center" colspan="2">Transferencia</th>
                        </tr>
                        <tr>
                            <th class="text-center">Paciente</th>
                            <th class="text-center">Donante</th>
                            <th class="text-center">Receptora</th>
                            <th class="text-center">Crio ovos Paciente</th>
                            <th class="text-center">Crio ovos Donante</th>
                            <th class="text-center">FIV</th>
                            <th class="text-center">ICSI</th>
                            <th class="text-center">NGS</th>
                            <th class="text-center">Embryoscope</th>
                            <th class="text-center">Ovulos</th>
                            <th class="text-center">Embriones</th>
                            <th class="text-center">Propios</th>
                            <th class="text-center">Embriodonación</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="row footer"><b>@2021 Clínica Inmater</b></div>
    </div>
    
    <div class="modal fade"
        id="exampleModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Lista de Pacientes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="mx-auto">
                    <table class="table table-responsive table-bordered align-middle"
                        id="ver_lista_pacientes">
                        <thead class="thead-dark">
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Documento de Identidad</th>
                            <th class="text-center">Paciente</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- <div class="modal-body">
                </div> -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/shared.js"></script>
    <script src="js/repo-data.js"></script>
</body>
</html>