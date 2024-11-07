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

      $log_Reprod = $db->prepare(
          "INSERT INTO appinmater_log.hc_reprod (
                      reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                      p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                      p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                      tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                      con_oi, con_end,
                      con1_med, 
                      con2_med, 
                      con3_med, 
                      con4_med, 
                      con5_med, 
                      con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                      complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                      pago_extras, pago_notas, pago_obs, repro, 
                      idusercreate, createdate, action
              )
          SELECT 
              id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
              p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
              p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
              tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
              con_oi, con_end,
              con1_med, 
              con2_med, 
              con3_med, 
              con4_med, 
              con5_med,
              con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
              complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
              pago_extras, pago_notas, pago_obs, repro, 
              ?, ?, 'D'
          FROM appinmater_modulo.hc_reprod
          WHERE id=?");
      $hora_actual = date("Y-m-d H:i:s");
      $log_Reprod->execute(array($login, $hora_actual, $_POST['borrar']));

      //$stmt = $db->prepare("DELETE from hc_reprod where id=?");
      $stmt = $db->prepare("update hc_reprod SET estado = false where id=?");
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
      hc_paciente.dni, ape, nom, don, san, m_ets, hc_reprod.med, lab_aspira.pro, lab_aspira.sta, lab_aspira.dias, lab_aspira.f_fin, hc_reprod.p_dni
      FROM hc_antece, hc_paciente, lab_aspira, hc_reprod
      WHERE hc_reprod.estado = true and lab_aspira.estado is true and hc_reprod.id=lab_aspira.rep and hc_paciente.dni = hc_antece.dni and hc_paciente.dni=lab_aspira.dni and lab_aspira.tip='T'
      ORDER by lab_aspira.fec DESC");
    $rPaci->execute(); ?>

    <style>
      .ui-dialog-contain {
        max-width: 1200px;
        margin: 1% auto 1%;
        padding: 0;
        position: relative;
        top: -35px;
      }
    
      .color { color:#F4062B !important; }
    </style>

    <div data-role="header" data-position="fixed">
      <h2>Traslados</h2>
    </div>
    <div class="ui-content" role="main">
      <form action="" name="form" method="post" data-ajax="false">
          <input id="filtro" data-type="search" placeholder="Filtro..">
          <input type="hidden" name="borrar" id="borrar">
          <table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive mayuscula">
            <thead>
              <tr>
                <th align="center" width="110">Protocolo</th>
                <th align="center">Paciente</th>
                <th align="center">Médico</th>
                <th align="center">Fecha</th>
                <th align="center">Operaciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
              while($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) { 
                $color='';

                if ($paci['f_fin']=='1899-12-30') {
                  $fecha = '';
                  $color='class="color"';
                } else {
                  $fecha = date("d-m-Y", strtotime($paci['f_fin']));
                  $paci['dias']=$paci['dias']-1;
                } ?>

                <tr <?php echo $color; ?>>
                  <td><a href='<?php echo "le_aspi".$paci['dias'].".php?id=".$paci['pro'];?>' rel="external"><?php echo '('.$paci['pro'].')';?></a></td>
                  <td>
                    <?php echo $paci['ape'].' '.$paci['nom'];?>
                    <?php if ($paci['san']=="O-" || $paci['san']=="A-" || $paci['san']=="B-" || $paci['san']=="AB-") echo  " <b>(SANGRE NEGATIVA) </b>";
                    if (strpos($paci['m_ets'],"VIH") !== false) echo  " <b>(VIH) </b>"; 
                    if (strpos($paci['m_ets'],"Hepatitis C") !== false) echo  " <b>(Hepatitis C) </b>"; 
                    if ($paci['don']=='D') echo  " <b>(DONANTE)</b>"; ?>
                  </td>
                  <td><?php echo $paci['med']; ?></td>
                  <td><?php echo $fecha; ?></td>
                  <td>
                    <small><i>Informe:</i></small> <?php echo '<a href="archivos_hcpacientes.php?idEmb=traslado_'.$paci['pro'].'.pdf" target="new"><i class="fas fa-file-pdf"></i></a>'; ?><br>
                    <small><i>Informe Inmater:</i></small> <a href="info_r.php?a=<?php echo $paci['pro'] . "&b=" . $paci['dni'] . "&c=" . $paci['p_dni']; ?>" target="new"><i class="fas fa-file-pdf"></i></a><br>
                    <?php print('<a href="javascript:anular('.$paci["id"].');"><i class="fas fa-trash-alt"></i></a>') ?>
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
        if (confirm("¿Está seguro que quiere eliminar el traslado?")) {
          document.form.borrar.value = id;
          document.form.submit();
        } else {
          return false;
        }
    }
  </script>
</body>
</html>