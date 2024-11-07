<!DOCTYPE HTML>
<html>

<head>
    <title>Inmater Clínica de Fertilidad | Transferencia Betas</title>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
        integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script>
    function Beta(beta, pro) {
        document.form2.val_beta.value = beta.value;
        document.form2.pro_beta.value = pro;
        document.form2.submit();
    }
    </script>

    <style>
    html,
    body {
        height: 100%;
        margin: 0;
    }

    .box {
        display: flex;
        flex-flow: column;
        height: 100%;
    }

    .box .row.header {
        flex: 0 1 auto;
    }

    .box .row.content {
        flex: 1 1 auto;
        overflow-y: scroll;
        margin: 0 0 15px 0;
    }

    .box .row.footer {
        flex: 0 1 40px;
        background-color: #72a2aa;
        padding: 0 1.25rem;
        margin: 0 0 15px 0;
        border-radius: .25rem;
    }

    @media (max-width: 768px) {
        .row.footer {
            font-size: 12px;
        }
    }
    </style>
</head>

<body>
    <?php
        $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
        $rUser->execute(array($login));
        $user = $rUser->fetch(PDO::FETCH_ASSOC);

        if ($user['role']==1 or $user['role']==2) {
            if (isset($_GET['med']) && !empty($_GET['med'])) {
                if ($_GET['med'] == 1) {
                    $porMed=" and lab_aspira_t.med='".$login."'";
                } else {
                    $porMed="";
                }
            } else {
                $porMed="";
            }
        }

        $between=$beta=$url=$ini=$fin=$t_med=$t_emb=$tipa=$edesde=$ehasta=$procedimiento=$ngs=$inc="";
        if (isset($_POST) && !empty($_POST)) {
            if (isset($_POST["beta"])) {
                if ($_POST["beta"] != "") {
                    $beta = $_POST["beta"];
                    $between.=" and lab_aspira_t.beta = $beta";
                }
            }

            if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                $ini=$_POST["ini"];
                $fin=$_POST["fin"];
                $between.=" and hc_reprod.f_iny between '".$_POST["ini"]."' and '".$_POST["fin"]."'";

                if ($url == "") {
                    $url .= "?ini=$ini&fin=$fin";
                } else {
                    $url .= "&ini=$ini&fin=$fin";
                }
            }

            if (isset($_POST["edesde"]) && isset($_POST["ehasta"])) {
                if ($_POST["edesde"] != '' &&  $_POST["ehasta"] != '') {
                    $edesde_date = new DateTime($_GET['edesde']);
                    $ehasta_date = new DateTime($_GET['ehasta']);

                    $today = new DateTime();

                    $edesde_diff = $today->diff($edesde_date);
                    $ehasta_diff = $today->diff($ehasta_date);

                    $edesde_days = $edesde_diff->days;
                    $ehasta_days = $ehasta_diff->days;
                    $between .= " AND EXTRACT(DAY FROM age(lab_aspira.fec, hc_paciente.fnac)) BETWEEN $edesde_days AND $ehasta_days";

                        if ($url == "") {
                            $url .= "?edesde=".$_POST['edesde']."&ehasta=".$_POST['ehasta'];
                        } else {
                            $url .= "&edesde=".$_POST['edesde']."&ehasta=".$_POST['ehasta'];
                        }
                    }
            }

            if ( isset($_POST["procedimiento"]) and !empty($_POST["procedimiento"]) ) {
                $procedimiento = $_POST["procedimiento"];
                $between .= " and unaccent(lab_aspira.pro) ilike ('%$procedimiento%')";
                if ($url == "") {
                    $url .= "?procedimiento=$procedimiento";
                } else {
                    $url .= "&procedimiento=$procedimiento";
                }
            }

            if (isset($_POST["ngs"]) and !empty($_POST["ngs"])) {
                $ngs=$_POST["ngs"];

                if ($ngs=="s") {
                    $between.=" and unaccent(rep.pago_extras) ilike '%ngs%'";
                } else {
                    $between.=" and unaccent(rep.pago_extras) not ilike '%ngs%'";
                }

                if ($url == "") {
                    $url .= "?ngs=$ngs";
                } else {
                    $url .= "&ngs=$ngs";
                }
            }

            if (isset($_POST["t_med"]) and !empty($_POST["t_med"])) {
                $t_med=$_POST["t_med"];
                $between.=" and lab_aspira_t.med = '".$t_med."'";

                if ($url == "") {
                    $url .= "?t_med=$t_med";
                } else {
                    $url .= "&t_med=$t_med";
                }
            }

            if (isset($_POST["t_emb"]) and !empty($_POST["t_emb"])) {
                $t_emb=$_POST["t_emb"];
                $between.=" and lab_aspira_t.emb = '".$t_emb."'";

                if ($url == "") {
                    $url .= "?t_emb=$t_emb";
                } else {
                    $url .= "&t_emb=$t_emb";
                }
            }

            if ( isset($_POST["tipa"]) && !empty($_POST["tipa"]) ) {
                $tipa = $_POST['tipa'];
                $between .= " and lab_aspira.tip = '$tipa'";

                if ($url == "") {
                    $url .= "?tipa=$tipa";
                } else {
                    $url .= "&tipa=$tipa";
                }
            }

            if (isset($_POST["inc"])) {
                if ($_POST["inc"] != "") {
                    $inc = $_POST['inc'];
                    // $between.= " and lab_aspira.inc = $inc";
                    $between.= " and (case when hc_reprod.des_don is null and hc_reprod.des_dia >= 1 then la.inc = $inc else lab_aspira.inc = $inc end)";

                    if ($url == "") {
                        $url .= "?inc=$inc";
                    } else {
                        $url .= "&inc=$inc";
                    }
                }
            }
        }

        // verificar para la beta
        if ($url == "") {
            $url.="?beta=";
        } else {
            $url.="&beta=";
        }

        $rPaci = $db->prepare("SELECT
            hc_reprod.id, hc_paciente.dni, ape, nom, hc_paciente.med,
            lab_aspira.pro, lab_aspira.tip, lab_aspira.dias,
            lab_aspira.fec2, lab_aspira.fec3, lab_aspira.fec4, lab_aspira.fec5, lab_aspira.fec6,
            lab_aspira_t.beta, lab_aspira_t.dia
            -- , lab_aspira_dias.pro, la.pro
            , rep.pago_extras
            from hc_reprod
            inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
            inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.dni=hc_paciente.dni and lab_aspira.estado is true
            inner join lab_aspira_t on lab_aspira_t.pro=lab_aspira.pro and lab_aspira_t.estado is true
            inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
            left join lab_aspira la on la.pro = lab_aspira_dias.pro_c and la.estado is true
            left join hc_reprod rep on rep.id = la.rep
            where hc_reprod.estado = true and 1=1".$porMed.$between."
            group by hc_reprod.id, hc_paciente.dni, ape, nom, hc_paciente.med,
            lab_aspira.pro, lab_aspira.tip, lab_aspira.dias,
            lab_aspira.fec2, lab_aspira.fec3, lab_aspira.fec4, lab_aspira.fec5, lab_aspira.fec6,
            lab_aspira_t.beta, lab_aspira_t.dia
            , lab_aspira_dias.pro
            , lab_aspira_dias.pro_c, rep.pago_extras
            order by hc_reprod.id desc");
        $rPaci->execute();
         ?>

    <div class="box container">
        <div class="row1 header1">
            <nav aria-label="breadcrumb">
                <a class="breadcrumb link_back_url" href="javascript:void(0)" style="background-color: #72a2aa;">
                    <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
                </a>
            </nav>

            <form action="" method="post" data-ajax="false" name="form2">
                <input type="hidden" name="val_beta">
                <input type="hidden" name="pro_beta">
                <div class="card mb-3">
                    <h5 class="card-header"><small><b>Información General</b></small></h5>
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Mostrar Desde</span>
                                    <input class="form-control" name="ini" type="date" id="ini"
                                        value="<?php if(isset($_POST['ini']))echo $_POST['ini']; ?>">
                                    <span class="input-group-addon">Hasta</span>
                                    <input class="form-control" name="fin" type="date" id="fin"
                                        value="<?php if(isset($_POST['fin']))echo $_POST['fin']; ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Edad cumplida</span>
                                    <input class="form-control" name="edesde" type="number"
                                        value="<?php if(isset($_POST['edesde']))echo $_POST['edesde']; ?>">
                                    <span class="input-group-addon">Y menor a</span>
                                    <input class="form-control" name="ehasta" type="number"
                                        value="<?php if(isset($_POST['ehasta']))echo $_POST['ehasta']; ?>">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">N° Proc.</span>
                                    <input class="form-control" name="procedimiento" type="number"
                                        value="<?php if(isset($_POST['procedimiento']))echo $_POST['procedimiento']; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Tipo Paciente</span>
                                    <select class="form-control" name='tipa'>
                                        <option value=''>TODOS</option>
                                        <option value='P' <?php if($tipa == "P") print("selected"); ?>>PACIENTE</option>
                                        <option value='R' <?php if($tipa == "R") print("selected"); ?>>RECEPTOR</option>
                                        <option value='D' <?php if($tipa == "D") print("selected"); ?>>DONANTE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">NGS</span>
                                    <select class="form-control" name='ngs'>
                                        <option value=''>SELECCIONAR</option>
                                        <option value='s' <?php if($ngs == "s") print("selected"); ?>>SI</option>
                                        <option value='n' <?php if($ngs == "n") print("selected"); ?>>NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Incubadora</span>
                                    <select name='inc' class="form-control">
                                        <option value="" selected>SELECCIONAR</option>
                                        <?php
                                            $data = $db->prepare("select id, codigo from incubadora where estado=1");
                                            $data->execute();
                                            while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                                print("<option value=".$info['codigo']);
                                            if ($inc === $info['codigo'])
                                                echo " selected";
                                            print(">".$info['codigo']."</option>");
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Médico Transferencia</span>
                                    <select class="form-control" name="t_med" id="t_med">
                                        <option value="">TODOS</option>
                                        <?php
                                            $data = $db->prepare("SELECT codigo, nombre from man_medico where estado=1 order by nombre");
                                            $data->execute();
                                            while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                                print("<option value=".$info['codigo']);
                                            if ($t_med === $info['codigo'])
                                                echo " selected";
                                            print(">".mb_strtoupper($info['nombre'])."</option>");
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Embriologo Transferencia</span>
                                    <select class="form-control" name="t_emb" id="t_emb">
                                        <option value="">TODOS</option>
                                        <?php
																					$data_emb = $db->prepare("SELECT id codigo, nom nombre from lab_user order by nom;");
																					$data_emb->execute();
																					while ($info = $data_emb->fetch(PDO::FETCH_ASSOC)) {
																						print("<option value=".$info['codigo'] . ($t_emb == $info['codigo'] ? " selected": "") .">".mb_strtoupper($info['nombre'])."</option>");
                                        	} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Resultado</span>
                                    <select class="form-control" name="beta">
                                        <option value="">TODOS</option>
                                        <option value="0" <?php if($beta=="0") { print('selected'); } ?>>PENDIENTE
                                        </option>
                                        <option value="2" <?php if($beta=="2") { print('selected'); } ?>>NEGATIVO
                                        </option>
                                        <option value="1" <?php if($beta=="1") { print('selected'); } ?>>POSITIVO
                                        </option>
                                        <option value="3" <?php if($beta=="3") { print('selected'); } ?>>BIOQUIMICO
                                        </option>
                                        <option value="4" <?php if($beta=="4") { print('selected'); } ?>>ABORTO</option>
                                        <option value="5" <?php if($beta=="5") { print('selected'); } ?>>ANEMBRIONADO
                                        </option>
                                        <option value="6" <?php if($beta=="6") { print('selected'); } ?>>ECTÓPICO
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center">
                                <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <span><a href="https://app.inmater.pe/genesis/#/dashboard/monitoring-betas" target="_blank"
                rel="noopener noreferrer">Ir a Seguimiento de Embarazo <i
                    class="fas fa-external-link-alt"></i></a></span>
        <div class="card row content">
            <?php
            $t_0=0; $t_1=0; $t_2=0; $t_3=0; $t_4=0; $t_5=0; $i = 0;
            $contador = 1;

            while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
                $color='';
                if ($paci['beta']==0) $color='class="col0"';
                if ($paci['beta']==1) $color='class="col1"';

                $beta = $paci['fec'.$paci['dia']]; //la fecha del dia de transferencia

                if($paci['dia']==2) $dt=15;
                if($paci['dia']==3) $dt=14;
                if($paci['dia']==4) $dt=13;
                if($paci['dia']==5) $dt=12;
                if($paci['dia']==6) $dt=11;

                $beta = date('d-m-Y', strtotime($beta.' + '.$dt.' days'));

                if ($paci['beta']==0) {
                    if($paci['beta']==0) { $t_0++; }
                    if($paci['beta']==1) { $t_1++; }
                    if($paci['beta']==2) { $t_2++; }
                    if($paci['beta']==3) { $t_3++; }
                    if($paci['beta']==4) { $t_4++; }
                    if($paci['beta']==5) { $t_5++; }
                } else {
                    $datos[$i] = array($beta, $color, '('.$paci['tip'].'-'.$paci['pro'].')', $paci['ape'].' '.$paci['nom'], $paci['med'], $paci['pro'],$paci['beta']);
                    $i++;
                }
            }

            if (isset($datos)) {
                foreach ($datos as $item) {
                    if($item[6]==0) { $t_0++; }
                    if($item[6]==1) { $t_1++; }
                    if($item[6]==2) { $t_2++; }
                    if($item[6]==3) { $t_3++; }
                    if($item[6]==4) { $t_4++; }
                    if($item[6]==5) { $t_5++; }
                }
            }

            if ($rPaci->rowCount()<1)  echo '<p><h5>¡No hay registros!</h5></p>'; ?>

            <div class="card-body mx-auto">
                <table class="table table-responsive table-bordered align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th>Item</th>
                            <th class="text-center align-middle">Resumen</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            print("
                            <tr>
                                <td class='text-center'>1</td>
                                <td><span class='col0'>Pendientes</span></td>
                                <td class='text-center'><a href='labo-betas-lista.php".$url."0'>".$t_0."</a></td></tr>
                            <tr>
                                <td class='text-center'>2</td>
                                <td>Negativos</td>
                                <td class='text-center'><a href='labo-betas-lista.php".$url."2'>".
                                    $t_2 . " (" . ($t_2 == 0 ? "0": number_format(($t_2*100/($t_1+$t_2+$t_3+$t_4)), 2)) . "%)</a>
                                </td>
                            </tr>
                            <tr>
                                <td class='text-center'>3</td>
                                <td>Positivos</td>
                                <td class='text-center'><a href='labo-betas-lista.php".$url."1'>".
                                    $t_1 . " (" . ($t_2 == 0 ? "0": number_format(($t_1*100/($t_1+$t_2+$t_3+$t_4)), 2)) . "%)</a>
                                </td>
                            </tr>
                            <tr>
                                <td class='text-center'>4</td>
                                <td>Bioquimicos</td>
                                <td class='text-center'><a href='labo-betas-lista.php".$url."3'>".
                                    $t_3 . " (" . ($t_2 == 0 ? "0": number_format(($t_3*100/($t_1+$t_2+$t_3+$t_4)), 2)) . "%)</a>
                                </td>
                            </tr>
                            <tr>
                                <td class='text-center'>5</td>
                                <td>Abortos</td>
                                <td class='text-center'><a href='labo-betas-lista.php".$url."4'>".
                                    $t_4 . " (" . ($t_2 == 0 ? "0": number_format(($t_4*100/($t_1+$t_2+$t_3+$t_4)), 2)) . "%)</a>
                                </td>
                            </tr>
                            <tr>
                                <td class='text-center'>6</td>
                                <td>Anembrionado</td>
                                <td class='text-center'><a href='labo-betas-lista.php".$url."5'>" . $t_5 . "</a></td>
                            </tr>
                            <tr>
                                <td colspan='2' class='text-center'>Total</td>
                                <td class='text-center'><a href='labo-betas-lista.php".$url."-1'>".($t_0+$t_1+$t_2+$t_3+$t_4)."</a></td>
                            <tr>");
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row footer">
            @2021 Clínica Inmater
        </div>
    </div>

    <script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", ".link_back_url", function() {
            var backUrl = localStorage.getItem('back_url');
            if (backUrl) {
                location.href = backUrl;
            }
        });
    });

    function myFunction() {
        var input, filter, table, tr, td, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
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
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>