<!DOCTYPE html>
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
</head>
<body>
    <?php
    // guardar datos
    if (isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['guardar'])) {
        updateLegal($_POST['idx'], $_POST['dni'], $_POST['a_mue'], $_POST['nom'], $_POST['med'], $_POST['a_exa'], $_POST['a_sta'], $_POST['a_obs'], $_POST['gin'], $login, $_FILES['informe']);
        if ($_POST['nota'] <> '') {
            $stmt = $db->prepare("UPDATE hc_paciente set nota=?, iduserupdate=?,updatex=? where dni=?");
            $hora_actual = date("Y-m-d H:i:s");
            $stmt->execute(array($_POST['nota'],$login, $hora_actual, $_POST['dni']));
            $log_Paciente = $db->prepare(
                "INSERT INTO appinmater_log.hc_paciente (
                            dni, pass, sta, med, tip, nom, ape, fnac, tcel,
                            tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                            san, don, raz, talla, peso, rem, nota, fec, idsedes,
                            idusercreate, createdate, 
                            action
                    )
                SELECT 
                    dni, pass, sta, med, tip, nom, ape, fnac, tcel, 
                    tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                    san, don, raz, talla, peso, rem, nota, fec, idsedes,
                    iduserupdate,updatex, 'U'
                FROM appinmater_modulo.hc_paciente
                WHERE dni=?");
            $log_Paciente->execute(array($_POST['dni']));
        }
    }

    $gin = "";
    if (isset($_GET['gin']) && !empty($_GET['gin'])) {
        $gin = $_GET['gin'];
    }
    $and = "";
    if (isset($_GET['andro']) && !empty($_GET['andro'])) {
        $and = $_GET['andro'];
    }
    $id = "";
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
    }

    $Rpop = $db->prepare("SELECT * from hc_legal where id=?");
    $Rpop->execute(array($id));
    $pop = $Rpop->fetch(PDO::FETCH_ASSOC);

    $edad = $nom = $lista = "";
    if ($and <> '') {
        $lista = "menu_legal_andro.php";
        $User = $db->prepare("SELECT p_dni,p_nom,p_ape,p_med FROM hc_pareja WHERE p_dni=?");
        $User->execute(array($and));
        $use = $User->fetch(PDO::FETCH_ASSOC);
        $nom = $use['p_ape'] . " " . $use['p_nom'];
        $dni = $use['p_dni'];
        $med = $use['p_med'];
    }

    if ($gin <> '') {
        $lista = "menu_legal_gineco.php";
        $User = $db->prepare("SELECT DISTINCT hc_paciente.nom,hc_paciente.ape,hc_paciente.fnac,hc_paciente.nota,hc_gineco.dni,hc_gineco.med FROM hc_gineco,hc_paciente WHERE hc_paciente.dni=hc_gineco.dni AND hc_gineco.id=?");
        $User->execute(array($gin));
        $use = $User->fetch(PDO::FETCH_ASSOC);
        if ($use['fnac'] <> "1899-12-30") $edad = ' <a href="#popupBasic" data-rel="popup" data-transition="pop">(' . date_diff(date_create($use['fnac']), date_create('today'))->y . ')</a>';
        $nom = $use['ape'] . " " . $use['nom'];
        $dni = $use['dni'];
        $med = $use['med'];
    }

    if ($id <> '') {
        $lista = "menu_legal_atenciones.php";
        $nom = $pop['a_nom'];
        $dni = $pop['a_dni'];
        $med = $pop['a_med'];
    }

    $rMed = $db->prepare("SELECT id, nom from hc_analisis_tip where lab=? order by nom asc");
    $rMed->execute(array($login));

    $resultados = $db->prepare("SELECT codigo, nombre from legal_resultado where estado = 1 order by nombre asc");
    $resultados->execute();
    ?>
    <div class="container">
        <div data-role="collapsible" id="Perfi">
            <!-- <h3>Consulta Ginecología</h3> -->
            <div class="card mb-3">
                <h5 class="card-header">Legal: <small><?php echo $nom.$edad; ?></small>
                <?php
                print('
                    <a class="navbar-brand float-right" href="'.$lista.'">
                        <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
                    </a>
                '); ?>
                </h5>
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
                        <input type="hidden" name="idx" value="<?php echo $id; ?>">
                        <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                        <input type="hidden" name="gin" value="<?php echo $gin; ?>">
                        <input type="hidden" name="nom" value="<?php echo $nom; ?>">
                        <input type="hidden" name="med" value="<?php echo $med; ?>">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-addon">Fecha</span>
                                    <input class="form-control" name="a_mue" type="date" value="<?php echo $pop['a_mue']; ?>"  id="a_mue" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-9 col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Tipo de documento</span>
                                    <select class="form-control" name="a_exa" id="a_exa" required data-mini="true">
                                        <option value="">SELECCIONAR</option>
                                        <?php
                                            while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value="<?php echo $med['nom']; ?>"
                                                <?php
                                                if ($med['nom'] == $pop['a_exa'])
                                                    echo 'selected'; ?>><?php echo mb_strtoupper($med['nom']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-addon">Resultado</span>
                                    <select class="form-control" name="a_sta" id="a_sta" required>
                                        <option value="">SELECCIONAR</option>
                                        <?php
                                            while ($data = $resultados->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value="<?php echo $data['codigo']; ?>"
                                                <?php
                                                if ($data['codigo'] == $pop['a_sta'])
                                                    echo 'selected'; ?>><?php echo mb_strtoupper($data['nombre']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-9 col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Informe</span>
                                    <input class="form-control" name="informe" type="file" id="informe" accept="application/pdf" data-mini="true"/>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="input-group">
                                    <span class="input-group-addon">Observación</span>
                                    <textarea class="form-control" name="a_obs" id="a_obs"><?php echo $pop['a_obs']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                                <input type="Submit" class="btn btn-danger" name="guardar" value="Guardar"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br><br>
        </div>
    </div>
</body>
</html>