<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="_images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
  <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
  <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
  <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
  <script src="js/jquery-1.11.1.min.js"></script>
  <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
  <div data-role="page" class="ui-responsive-panel" id="lista_pro_t" data-dialog="true">
    <?php
    $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['borrar']) && !empty($_POST['borrar'])) {

      
      $hora_actual = date("Y-m-d H:i:s");
      $log_Reprod->execute(array($login, $hora_actual, $_POST['borrar']));

      $stmt = $db->prepare("DELETE from hc_reprod where id=?");
      $stmt->execute(array($_POST['borrar']));

      $stmt = $db->prepare("SELECT pro from lab_aspira where rep=? and estado is true");
      $stmt->execute(array($_POST['borrar']));
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      $stmt = $db->prepare("DELETE from lab_aspira where pro=?");
      $stmt->execute([$data["pro"]]);

      $stmt = $db->prepare("DELETE from lab_aspira_dias where pro=?");
      $stmt->execute([$data["pro"]]);
    }

    if ($user['role']==2)
    $rPaci = $db->prepare("SELECT
      hc_reprod.id,
      hc_paciente.dni, ape, hc_paciente.nom, don, san, m_ets, hc_reprod.med, lab_user.nom as nom_biologo, lab_aspira.pro, lab_aspira.sta, lab_aspira.dias, hc_reprod.fec, hc_reprod.p_dni,hc_antece_trata.tip_ret as tip_retiro
      FROM hc_antece, hc_paciente, lab_aspira, hc_reprod
      LEFT JOIN hc_antece_trata ON hc_reprod.id = hc_antece_trata.id_reprod
      left join lab_user on lab_user.id = hc_antece_trata.embriologo
      WHERE hc_reprod.id=lab_aspira.rep and lab_aspira.estado is true and hc_paciente.dni = hc_antece.dni and hc_paciente.dni=lab_aspira.dni and lab_aspira.tip='X'
      ORDER by hc_reprod.fec desc");
    $rPaci->execute(); ?>

    <style>
      .ui-dialog-contain {
        max-width: 1200px;
        margin: 1% auto 1%;
        padding: 0;
        position: relative;
        top: -35px;
      }
    </style>

    <div data-role="header" data-position="fixed">
      <h2>Retiros Ovulos / Embriones</h2>
    </div>
    <div class="ui-content" role="main">
      <div style="padding-bottom: 3em;">
        <a href="le_tanque_nuevo2.php" rel="external" class="ui-btn ui-btn-inline ui-mini" style="float:left">Agregar</a>
      </div>
      <form action="" name="form" method="post" data-ajax="false">
          <input id="filtro" data-type="search" placeholder="Filtro..">
          <input type="hidden" name="borrar" id="borrar">
          <table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive mayuscula">
            <thead>
              <tr>
                <th align="center" width="110">Protocolo</th>
                <th align="center"></th>
                <th align="center">Paciente</th>
                <th align="center">Médico</th>
                <th align="center">Embriologo</th>
                <th align="center">Fecha</th>
                <th align="center">Operaciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
              while($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) { 
                
                  $fecha = date("d-m-Y", strtotime($paci['fec']));
                  $paci['dias']=$paci['dias']-1;
                  $tipo_retiro = $paci['tip_retiro'] == 1 ? 'OVULO' : 'EMBRIONES'; 
                  $tipo_retiro_enlace = $paci['tip_retiro'] == 1 ? 'info_retiro_ovo.php?a=' : 'info_retiro.php?a='; 
                ?>

                <tr>
                  <td><a href='<?php echo "le_aspi".$paci['dias'].".php?id=".$paci['pro'];?>' rel="external"><?php echo $paci['pro'];?></a></td>
                  <td><strong><?php echo $tipo_retiro ?></strong></td>
                  <td>
                    <?php echo $paci['ape'].' '.$paci['nom'];?>
                    <?php if ($paci['san']=="O-" || $paci['san']=="A-" || $paci['san']=="B-" || $paci['san']=="AB-") echo  " <b>(SANGRE NEGATIVA) </b>";
                    if (strpos($paci['m_ets'],"VIH") !== false) echo  " <b>(VIH) </b>"; 
                    if (strpos($paci['m_ets'],"Hepatitis C") !== false) echo  " <b>(Hepatitis C) </b>"; 
                    if ($paci['don']=='D') echo  " <b>(DONANTE)</b>"; ?>
                  </td>
                  <td><?php echo $paci['med']; ?></td>
                  <td><?php echo $paci['nom_biologo']; ?></td>
                  <td><?php echo $fecha; ?></td>
                  <td>
                    <small><i>Informe:</i></small> <?php echo '<a href="archivos_hcpacientes.php?idRet=retiro_'.$paci['pro'].'.pdf" target="new"><i class="fas fa-file-pdf"></i></a>'; ?><br>
                    <small><i>Informe Inmater:</i></small> <a href="<?php echo $tipo_retiro_enlace.$paci['pro'] . "&b=" . $paci['dni'] . "&c=" . $paci['p_dni']; ?>" target="new"><i class="fas fa-file-pdf"></i></a><br>
                    <?php //print('<a href="javascript:anular('.$paci["id"].');"><i class="fas fa-trash-alt"></i></a>') ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
      </form>
    </div>
  </div>
  <script>
    function anular(id) {
        if (confirm("¿Está seguro que quiere eliminar el retiro?")) {
          document.form.borrar.value = id;
          document.form.submit();
        } else {
          return false;
        }
    }
  </script>
</body>
</html>