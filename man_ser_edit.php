<?php session_start(); ?>
<!DOCTYPE HTML>
<html>

<head>
    <?php
		$login = $_SESSION['login'];
		$dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
		if (!$login) {
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://".$dir."'>";
		}
		require($_SERVER["DOCUMENT_ROOT"]."/_database/database.php");
	?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/shared.css">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <title>Clínica Inmater | Editar Servicio</title>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
</head>

<body>
    <?php
		//
		if (isset($_GET["id"]) && !empty($_GET["id"])) {
			if(isset($_POST["guardar"]) && !empty($_POST["guardar"])) {
	            $codigo="";
	            if (isset($_POST["codigo"]) && !empty($_POST["codigo"])) {
	                $codigo=$_POST["codigo"];
	            }
	            $paquete="";
	            if (isset($_POST["paquete"]) && !empty($_POST["paquete"])) {
	                $paquete=$_POST["paquete"];
	            }
                global $db;
                $stmt = $db->prepare("UPDATE recibo_serv set procedimiento_id=?, tarifario_id=?, conta_sub_centro_costo_id=?, cod=?, nom=?, pak=?, idmoneda=?, costo=?, tip=? where id=?");
                $stmt->execute([
                    intval($_POST["procedimiento_id"]),
                    intval($_POST["tarifario_id"]),
                    $_POST["subcentrocosto"],
                    $codigo,
                    mb_strtoupper(trim($_POST["nombreservicio"])),
                    $paquete,
                    $_POST["moneda"],
                    $_POST["costo"],
                    $_POST["tiposervicio"],
                    $_GET["id"]
                ]);
                print("<div id='alerta'>Servicio guardado!</div>");
			}
			$stmt = $db->prepare("SELECT
                r.*, m.id idmoneda, m.codigo moneda
                from recibo_serv r
                left join moneda m on m.id=r.idmoneda
                where r.id = ?
                order by r.pak asc;");
			$stmt->execute(array($_GET["id"]));
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($data['idmoneda']) && !empty($data['idmoneda'])) {
                $mon = $data['idmoneda'];
            } else {
                if ($data['tip'] == 1 or $data['tip'] == 2 or $data['tip'] == 3)
                    $mon = "2";
                else $mon = "1";
            }
		} else {
			print("No existe el Insumo.");
			exit;
		}
	?>
    <div class="container">
        <?php require ('_includes/menu_facturacion.php'); ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_facturacion.php">Inicio</a></li>
                <li class="breadcrumb-item" aria-current="page">Mantenimiento</li>
                <li class="breadcrumb-item active" aria-current="page"><a
                        href="man_ser.php?tiposervicio=<?php print($data['tip']); ?>">Servicios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Servicio</li>
            </ol>
        </nav>
        <script>
        $(document).ready(function() {
            var unsaved = false;
            $(":input").change(function() {
                unsaved = true;
            });
            $(window).on('beforeunload', function() {
                if (unsaved) {
                    return 'UD. HA REALIZADO CAMBIOS';
                }
            });
            $(document).on("submit", "form", function(event) {
                $(window).off('beforeunload');
            });
        });
        </script>
        <form action="" method="post" data-ajax="false">
            <div class="card mb-3" id="info_general">
                <h5 class="card-header">Información General</h5>
                <div class="card-body">
                    <div class="row pb-2">
                        <!-- tipo de servicio -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tipo de Servicio*</span>
                                <select name='tiposervicio' id="idservicio"
                                    class="form-control form-control-sm chosen-select" required>
                                    <option value="">Todos</option>
                                    <option value='0' <?php if($data["tip"] == "0") print("selected"); ?>>Admin</option>
                                    <option value='1' <?php if($data["tip"] == "1") print("selected"); ?>>Reproducción
                                        Asistida</option>
                                    <option value='2' <?php if($data["tip"] == "2") print("selected"); ?>>Andrología
                                    </option>
                                    <option value='3' <?php if($data["tip"] == "3") print("selected"); ?>>Procedimientos
                                        de Sala</option>
                                    <option value='4' <?php if($data["tip"] == "4") print("selected"); ?>>Análisis de
                                        Sangre</option>
                                    <option value='5' <?php if($data["tip"] == "5") print("selected"); ?>>Perfiles
                                    </option>
                                    <option value='6' <?php if($data["tip"] == "6") print("selected"); ?>>Ecografía
                                    </option>
                                    <option value='7' <?php if($data["tip"] == "7") print("selected"); ?>>Adicionales
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!-- servicio -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Nombre de Servicio</span>
                                <input class="form-control form-control-sm" type="text" name="nombreservicio"
                                    id="nombreservicio" value="<?php print($data["nom"]); ?>" required />
                            </div>
                        </div>
                        <!-- moneda y costo -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Costo*</span>
                                <select class="form-control form-control-sm" name="moneda" id="idmoneda" required>
                                    <option value="" selected>Seleccionar</option>
                                    <option value="1" <?php if ($mon=="1") { print("selected"); } ?>>MN</option>
                                    <option value="2" <?php if ($mon=="2") { print("selected"); } ?>>US</option>
                                </select>
                                <input class="form-control form-control-sm" type="number" step="any" min="1"
                                    name="costo" id="idcosto" data-mini="true"
                                    value="<?php print(number_format($data["costo"], 2)); ?>" required />
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <!-- sub centro de costo -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Sub Centro de Costo*</span>
                                <select class="form-control form-control-sm chosen-select" name="subcentrocosto"
                                    id="subcentrocosto">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    $stmt = $db->prepare("SELECT id, codigo, descripcion nombre from conta_sub_centro_costo where estado = 1;");
                                    $stmt->execute();

                                    while ($subcentrocosto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = "";
                                        if ($subcentrocosto['id'] == $data['conta_sub_centro_costo_id']) $selected = "selected";
                                        print('<option value="' . $subcentrocosto['id'] . '" '.$selected.'>'.$subcentrocosto['nombre'].' ('.$subcentrocosto['codigo'].')</option>');
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!-- procedimiento -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Procedimiento</span>
                                <select class="form-control form-control-sm chosen-select" name="procedimiento_id">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    $stmt = $db->prepare("SELECT id, nombre from servicios_procedimiento sp where estado = 1 order by nombre;");
                                    $stmt->execute();
                                    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = "";
                                        if ($item['id'] == $data['procedimiento_id']) $selected = "selected";
                                        print('<option value="' . $item['id'] . '" '.$selected.'>'.$item['nombre'].'</option>');
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- tipo de paciente -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tarifario</span>
                                <select class="form-control form-control-sm chosen-select" name="tarifario_id">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    //$stmt = $db->prepare("SELECT id, abreviatura nombre from man_medios_comunicacion mmc where estado = 1 order by abreviatura;");
                                    $stmt = $db->prepare("SELECT id, upper(nombre) nombre from tarifario order by nombre;");
                                    $stmt->execute();

                                    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = "";
                                        if ($item['id'] == $data['tarifario_id']) $selected = "selected";
                                        print('<option value="' . $item['id'] . '" '.$selected.'>'.$item['nombre'].'</option>');
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <!-- paquete -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Paquete</span>
                                <input class="form-control form-control-sm" name="paquete" id="idpaquete"
                                    autocomplete="off" value="<?php print($data["pak"]); ?>">
                                <select class="form-control form-control-sm chosen-select" id="paqueteselect">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    $Rpak = $db->prepare("SELECT distinct pak from recibo_serv where pak is not null order by pak asc");
                                    $Rpak->execute();
                                    while ($pak = $Rpak->fetch(PDO::FETCH_ASSOC)) {
                                            print('<option value="' . $pak['pak'] . '">'.$pak['pak'].'</option>');
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- codigo secundario -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Código Anglolab</span>
                                <input class="form-control form-control-sm" type="text" name="codigo" id="idcodigo"
                                    value="<?php print($data["cod"]); ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pb-2">
                <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                    <input type="Submit" value="Guardar" name="guardar" class="form-control btn btn-danger btn-sm" />
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                    <?php print('<a href="man_ser.php?tiposervicio='.$data["tip"].'" class="form-control btn btn-dark btn-sm">Cancelar</a>'); ?>
                </div>
            </div>
        </form>
    </div>
    <script>
    $(".chosen-select").chosen();
    $("#nombreservicioselect").change(function() {
        var e = document.getElementById("nombreservicioselect");
        var strUser = e.options[e.selectedIndex].value;
        var demo = e.options[e.selectedIndex].text;
        $('#nombreservicio').val(strUser);
        var fields = demo.split("-");
        $('#idcosto').val(fields[fields.length - 1]);
    });
    $("#paqueteselect").change(function() {
        var obj = document.getElementById("paqueteselect");
        // var strUser = e.options[e.selectedIndex].value;
        var texto = obj.options[obj.selectedIndex].text;
        $('#idpaquete').val(texto);
        // var fields = demo.split("-");
        // $('#idcosto').val(fields[fields.length-1]);
    });
    </script>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>