<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
@media print{@page {size:landscape;margin: 0px 0px 0px 0px;}}
table, th, td {
    border: 0.5px solid black;
    border-collapse: collapse;
}
</style>
<script>
setTimeout(function () { window.print(); }, 500);
window.onfocus = function () { setTimeout(function () { window.close(); }, 500); }
</script>
</head>

<body>


<?php if ($_GET['id'] <> "") {
	
$id = $_GET['id'];
$rObst = $db->prepare("SELECT * FROM hc_obste WHERE id=?");
$rObst->execute(array($id));
$obst = $rObst->fetch(PDO::FETCH_ASSOC);

$rPaci = $db->prepare("SELECT nom,ape,fnac FROM hc_paciente WHERE dni=?");
$rPaci->execute(array($obst['dni']));
$paci = $rPaci->fetch(PDO::FETCH_ASSOC); ?>

<h2>Gestación #<small><?php echo $obst['g_ges']." ".$paci['ape']." ".$paci['nom']; if ($paci['fnac']<>"1899-12-30") echo '('.date_diff(date_create($paci['fnac']), date_create('today'))->y.')'; ?></small></h2>

<h3>Consultas Obstetricas</h3>

   <?php   $con_fec = explode("|", $obst['con_fec']);
	    $con_fec_h = explode("|", $obst['con_fec_h']);
	    $con_fec_m = explode("|", $obst['con_fec_m']);
		$con_sem = explode("|", $obst['con_sem']);
		$con_eg  = explode("|", $obst['con_eg']);
		$con_pes = explode("|", $obst['con_pes']);
		$con_pa  = explode("|", $obst['con_pa']);
		$con_mov = explode("|", $obst['con_mov']);
		$con_ede = explode("|", $obst['con_ede']);
		$con_la  = explode("|", $obst['con_la']);
		$con_pla = explode("|", $obst['con_pla']);
		$con_pre = explode("|", $obst['con_pre']);
		$con_fcf = explode("|", $obst['con_fcf']);
		$con_pc  = explode("|", $obst['con_pc']);
		$con_lcn = explode("|", $obst['con_lcn']);
		$con_vv  = explode("|", $obst['con_vv']);
		$con_eco = explode("|", $obst['con_eco']);
		$con_hb  = explode("|", $obst['con_hb']);
		$con_gi  = explode("|", $obst['con_gi']);
		$con_ori = explode("|", $obst['con_ori']);
		$con_obs = explode("|", $obst['con_obs']);
		?>

    <table align="center" cellpadding="0" cellspacing="0" style="margin: 0 auto;font-size: small;width:100%;">  
    	<colgroup>
        <col> 
        <col <?php if ($con_fec[0] <> "") echo 'style="background-color:#d7e5e5"'; ?>>    
        <col <?php if ($con_fec[1] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[2] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[3] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[4] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[5] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[6] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[7] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[8] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[9] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[10] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[11] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[12] <> "") echo 'style="background-color:#d7e5e5"'; ?>>
        <col <?php if ($con_fec[13] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[14] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        <col <?php if ($con_fec[15] <> "") echo 'style="background-color:#d7e5e5"'; ?>>  
        </colgroup>
    
        <tr>
          <th></th>
          <th>1º Consulta</th>
          <th>2º Consulta</th>
          <th>3º Consulta</th>
          <th>4º Consulta</th>
          <th>5º Consulta</th>
          <th>6º Consulta</th>
          <th>7º Consulta</th>
          <th>8º Consulta</th>
          <th>9º Consulta</th>
          <th>10º Consulta</th>
          <th>11º Consulta</th>
          <th>12º Consulta</th>
          <th>13º Consulta</th>
          <th>14º Consulta</th>
          <th>15º Consulta</th>
          <th>16º Consulta</th>
          </tr>
        <tr>
          <td width="30px">FECHA<br>
          HORA</td>
		  <td><?php if ($con_fec[0]) echo date("d-m-Y", strtotime($con_fec[0])).'<br>'.$con_fec_h[0].':'.$con_fec_m[0]; ?></td>
		  <td><?php if ($con_fec[1]) echo date("d-m-Y", strtotime($con_fec[1])).'<br>'.$con_fec_h[1].':'.$con_fec_m[1]; ?></td>
          <td><?php if ($con_fec[2]) echo date("d-m-Y", strtotime($con_fec[2])).'<br>'.$con_fec_h[2].':'.$con_fec_m[2]; ?></td>
		  <td><?php if ($con_fec[3]) echo date("d-m-Y", strtotime($con_fec[3])).'<br>'.$con_fec_h[3].':'.$con_fec_m[3]; ?></td>
          <td><?php if ($con_fec[4]) echo date("d-m-Y", strtotime($con_fec[4])).'<br>'.$con_fec_h[4].':'.$con_fec_m[4]; ?></td>
		  <td><?php if ($con_fec[5]) echo date("d-m-Y", strtotime($con_fec[5])).'<br>'.$con_fec_h[5].':'.$con_fec_m[5]; ?></td>
          <td><?php if ($con_fec[6]) echo date("d-m-Y", strtotime($con_fec[6])).'<br>'.$con_fec_h[6].':'.$con_fec_m[6]; ?></td>
		  <td><?php if ($con_fec[7]) echo date("d-m-Y", strtotime($con_fec[7])).'<br>'.$con_fec_h[7].':'.$con_fec_m[7]; ?></td>
          <td><?php if ($con_fec[8]) echo date("d-m-Y", strtotime($con_fec[8])).'<br>'.$con_fec_h[8].':'.$con_fec_m[8]; ?></td>
		  <td><?php if ($con_fec[9]) echo date("d-m-Y", strtotime($con_fec[9])).'<br>'.$con_fec_h[9].':'.$con_fec_m[9]; ?></td>
          <td><?php if ($con_fec[10]) echo date("d-m-Y", strtotime($con_fec[10])).'<br>'.$con_fec_h[10].':'.$con_fec_m[10]; ?></td>
		  <td><?php if ($con_fec[11]) echo date("d-m-Y", strtotime($con_fec[11])).'<br>'.$con_fec_h[11].':'.$con_fec_m[11]; ?></td>
          <td><?php if ($con_fec[12]) echo date("d-m-Y", strtotime($con_fec[12])).'<br>'.$con_fec_h[12].':'.$con_fec_m[12]; ?></td>
		  <td><?php if ($con_fec[13]) echo date("d-m-Y", strtotime($con_fec[13])).'<br>'.$con_fec_h[13].':'.$con_fec_m[13]; ?></td>
          <td><?php if ($con_fec[14]) echo date("d-m-Y", strtotime($con_fec[14])).'<br>'.$con_fec_h[14].':'.$con_fec_m[14]; ?></td>
		  <td><?php if ($con_fec[15]) echo date("d-m-Y", strtotime($con_fec[15])).'<br>'.$con_fec_h[15].':'.$con_fec_m[15]; ?></td>
	  </tr>
		<tr>
		  <td>SEM. AME.</td>
		  <td><?php echo $con_sem[0]; ?></td>
		  <td><?php echo $con_sem[1]; ?></td>
		  <td><?php echo $con_sem[2]; ?></td>
		  <td><?php echo $con_sem[3]; ?></td>
		  <td><?php echo $con_sem[4]; ?></td>
		  <td><?php echo $con_sem[5]; ?></td>
		  <td><?php echo $con_sem[6]; ?></td>
		  <td><?php echo $con_sem[7]; ?></td>
		  <td><?php echo $con_sem[8]; ?></td>
		  <td><?php echo $con_sem[9]; ?></td>
		  <td><?php echo $con_sem[10]; ?></td>
		  <td><?php echo $con_sem[11]; ?></td>
		  <td><?php echo $con_sem[12]; ?></td>
		  <td><?php echo $con_sem[13]; ?></td>
		  <td><?php echo $con_sem[14]; ?></td>
		  <td><?php echo $con_sem[15]; ?></td>
	  </tr>
          <tr>
          <td>EG x ECO</td>
          <td><?php echo $con_eg[0]; ?></td>
		  <td><?php echo $con_eg[1]; ?></td>
		  <td><?php echo $con_eg[2]; ?></td>
		  <td><?php echo $con_eg[3]; ?></td>
		  <td><?php echo $con_eg[4]; ?></td>
		  <td><?php echo $con_eg[5]; ?></td>
		  <td><?php echo $con_eg[6]; ?></td>
		  <td><?php echo $con_eg[7]; ?></td>
		  <td><?php echo $con_eg[8]; ?></td>
		  <td><?php echo $con_eg[9]; ?></td>
		  <td><?php echo $con_eg[10]; ?></td>
		  <td><?php echo $con_eg[11]; ?></td>
		  <td><?php echo $con_eg[12]; ?></td>
		  <td><?php echo $con_eg[13]; ?></td>
		  <td><?php echo $con_eg[14]; ?></td>
		  <td><?php echo $con_eg[15]; ?></td>
      </tr>
          <tr>
            <td>PESO (Kg.)</td>
              <td><?php echo $con_pes[0]; ?></td>
              <td><?php echo $con_pes[1]; ?></td>
              <td><?php echo $con_pes[2]; ?></td>
              <td><?php echo $con_pes[3]; ?></td>
              <td><?php echo $con_pes[4]; ?></td>
              <td><?php echo $con_pes[5]; ?></td>
              <td><?php echo $con_pes[6]; ?></td>
              <td><?php echo $con_pes[7]; ?></td>
              <td><?php echo $con_pes[8]; ?></td>
              <td><?php echo $con_pes[9]; ?></td>
              <td><?php echo $con_pes[10]; ?></td>
              <td><?php echo $con_pes[11]; ?></td>
              <td><?php echo $con_pes[12]; ?></td>
              <td><?php echo $con_pes[13]; ?></td>
              <td><?php echo $con_pes[14]; ?></td>
              <td><?php echo $con_pes[15]; ?></td>
            </tr>
          <tr>
            <td>P.A (max/min)</td>
            <td><?php echo $con_pa[0]; ?></td>
		  <td><?php echo $con_pa[1]; ?></td>
		  <td><?php echo $con_pa[2]; ?></td>
		  <td><?php echo $con_pa[3]; ?></td>
		  <td><?php echo $con_pa[4]; ?></td>
		  <td><?php echo $con_pa[5]; ?></td>
		  <td><?php echo $con_pa[6]; ?></td>
		  <td><?php echo $con_pa[7]; ?></td>
		  <td><?php echo $con_pa[8]; ?></td>
		  <td><?php echo $con_pa[9]; ?></td>
		  <td><?php echo $con_pa[10]; ?></td>
		  <td><?php echo $con_pa[11]; ?></td>
		  <td><?php echo $con_pa[12]; ?></td>
		  <td><?php echo $con_pa[13]; ?></td>
		  <td><?php echo $con_pa[14]; ?></td>
		  <td><?php echo $con_pa[15]; ?></td>
            </tr>
          <tr>
            <td>MOV. FETAL</td>
          <td><?php echo $con_mov[0]; ?></td>
		  <td><?php echo $con_mov[1]; ?></td>
		  <td><?php echo $con_mov[2]; ?></td>
		  <td><?php echo $con_mov[3]; ?></td>
		  <td><?php echo $con_mov[4]; ?></td>
		  <td><?php echo $con_mov[5]; ?></td>
		  <td><?php echo $con_mov[6]; ?></td>
		  <td><?php echo $con_mov[7]; ?></td>
		  <td><?php echo $con_mov[8]; ?></td>
		  <td><?php echo $con_mov[9]; ?></td>
		  <td><?php echo $con_mov[10]; ?></td>
		  <td><?php echo $con_mov[11]; ?></td>
		  <td><?php echo $con_mov[12]; ?></td>
		  <td><?php echo $con_mov[13]; ?></td>
		  <td><?php echo $con_mov[14]; ?></td>
		  <td><?php echo $con_mov[15]; ?></td>
            </tr>
          <tr>
            <td>EDEMA</td>
            <td><?php echo $con_ede[0]; ?></td>
		  <td><?php echo $con_ede[1]; ?></td>
		  <td><?php echo $con_ede[2]; ?></td>
		  <td><?php echo $con_ede[3]; ?></td>
		  <td><?php echo $con_ede[4]; ?></td>
		  <td><?php echo $con_ede[5]; ?></td>
		  <td><?php echo $con_ede[6]; ?></td>
		  <td><?php echo $con_ede[7]; ?></td>
		  <td><?php echo $con_ede[8]; ?></td>
		  <td><?php echo $con_ede[9]; ?></td>
		  <td><?php echo $con_ede[10]; ?></td>
		  <td><?php echo $con_ede[11]; ?></td>
		  <td><?php echo $con_ede[12]; ?></td>
		  <td><?php echo $con_ede[13]; ?></td>
		  <td><?php echo $con_ede[14]; ?></td>
		  <td><?php echo $con_ede[15]; ?></td>
            </tr>
          <tr>
            <td>L.A</td>
            <td><?php echo $con_la[0]; ?></td>
		  <td><?php echo $con_la[1]; ?></td>
		  <td><?php echo $con_la[2]; ?></td>
		  <td><?php echo $con_la[3]; ?></td>
		  <td><?php echo $con_la[4]; ?></td>
		  <td><?php echo $con_la[5]; ?></td>
		  <td><?php echo $con_la[6]; ?></td>
		  <td><?php echo $con_la[7]; ?></td>
		  <td><?php echo $con_la[8]; ?></td>
		  <td><?php echo $con_la[9]; ?></td>
		  <td><?php echo $con_la[10]; ?></td>
		  <td><?php echo $con_la[11]; ?></td>
		  <td><?php echo $con_la[12]; ?></td>
		  <td><?php echo $con_la[13]; ?></td>
		  <td><?php echo $con_la[14]; ?></td>
		  <td><?php echo $con_la[15]; ?></td>
            </tr>
          <tr>
            <td>PLACENTA</td>
            <td><?php echo $con_pla[0]; ?></td>
		  <td><?php echo $con_pla[1]; ?></td>
		  <td><?php echo $con_pla[2]; ?></td>
		  <td><?php echo $con_pla[3]; ?></td>
		  <td><?php echo $con_pla[4]; ?></td>
		  <td><?php echo $con_pla[5]; ?></td>
		  <td><?php echo $con_pla[6]; ?></td>
		  <td><?php echo $con_pla[7]; ?></td>
		  <td><?php echo $con_pla[8]; ?></td>
		  <td><?php echo $con_pla[9]; ?></td>
		  <td><?php echo $con_pla[10]; ?></td>
		  <td><?php echo $con_pla[11]; ?></td>
		  <td><?php echo $con_pla[12]; ?></td>
		  <td><?php echo $con_pla[13]; ?></td>
		  <td><?php echo $con_pla[14]; ?></td>
		  <td><?php echo $con_pla[15]; ?></td>
            </tr>
          <tr>
            <td>PRESENTACION</td>
            <td><?php echo $con_pre[0]; ?></td>
		  <td><?php echo $con_pre[1]; ?></td>
		  <td><?php echo $con_pre[2]; ?></td>
		  <td><?php echo $con_pre[3]; ?></td>
		  <td><?php echo $con_pre[4]; ?></td>
		  <td><?php echo $con_pre[5]; ?></td>
		  <td><?php echo $con_pre[6]; ?></td>
		  <td><?php echo $con_pre[7]; ?></td>
		  <td><?php echo $con_pre[8]; ?></td>
		  <td><?php echo $con_pre[9]; ?></td>
		  <td><?php echo $con_pre[10]; ?></td>
		  <td><?php echo $con_pre[11]; ?></td>
		  <td><?php echo $con_pre[12]; ?></td>
		  <td><?php echo $con_pre[13]; ?></td>
		  <td><?php echo $con_pre[14]; ?></td>
		  <td><?php echo $con_pre[15]; ?></td>
            </tr>
          <tr>
            <td>F.C.F (lat/min)</td>
            <td><?php echo $con_fcf[0]; ?></td>
		  <td><?php echo $con_fcf[1]; ?></td>
		  <td><?php echo $con_fcf[2]; ?></td>
		  <td><?php echo $con_fcf[3]; ?></td>
		  <td><?php echo $con_fcf[4]; ?></td>
		  <td><?php echo $con_fcf[5]; ?></td>
		  <td><?php echo $con_fcf[6]; ?></td>
		  <td><?php echo $con_fcf[7]; ?></td>
		  <td><?php echo $con_fcf[8]; ?></td>
		  <td><?php echo $con_fcf[9]; ?></td>
		  <td><?php echo $con_fcf[10]; ?></td>
		  <td><?php echo $con_fcf[11]; ?></td>
		  <td><?php echo $con_fcf[12]; ?></td>
		  <td><?php echo $con_fcf[13]; ?></td>
		  <td><?php echo $con_fcf[14]; ?></td>
		  <td><?php echo $con_fcf[15]; ?></td>
            </tr>
          <tr>
            <td>P.C</td>
            <td><?php echo $con_pc[0]; ?></td>
		  <td><?php echo $con_pc[1]; ?></td>
		  <td><?php echo $con_pc[2]; ?></td>
		  <td><?php echo $con_pc[3]; ?></td>
		  <td><?php echo $con_pc[4]; ?></td>
		  <td><?php echo $con_pc[5]; ?></td>
		  <td><?php echo $con_pc[6]; ?></td>
		  <td><?php echo $con_pc[7]; ?></td>
		  <td><?php echo $con_pc[8]; ?></td>
		  <td><?php echo $con_pc[9]; ?></td>
		  <td><?php echo $con_pc[10]; ?></td>
		  <td><?php echo $con_pc[11]; ?></td>
		  <td><?php echo $con_pc[12]; ?></td>
		  <td><?php echo $con_pc[13]; ?></td>
		  <td><?php echo $con_pc[14]; ?></td>
		  <td><?php echo $con_pc[15]; ?></td>
            </tr>
          <tr>
            <td>L.C.N</td>
            <td><?php echo $con_lcn[0]; ?></td>
		  <td><?php echo $con_lcn[1]; ?></td>
		  <td><?php echo $con_lcn[2]; ?></td>
		  <td><?php echo $con_lcn[3]; ?></td>
		  <td><?php echo $con_lcn[4]; ?></td>
		  <td><?php echo $con_lcn[5]; ?></td>
		  <td><?php echo $con_lcn[6]; ?></td>
		  <td><?php echo $con_lcn[7]; ?></td>
		  <td><?php echo $con_lcn[8]; ?></td>
		  <td><?php echo $con_lcn[9]; ?></td>
		  <td><?php echo $con_lcn[10]; ?></td>
		  <td><?php echo $con_lcn[11]; ?></td>
		  <td><?php echo $con_lcn[12]; ?></td>
		  <td><?php echo $con_lcn[13]; ?></td>
		  <td><?php echo $con_lcn[14]; ?></td>
		  <td><?php echo $con_lcn[15]; ?></td>
            </tr>
          <tr>
            <td>V.V</td>
            <td><?php echo $con_vv[0]; ?></td>
		  <td><?php echo $con_vv[1]; ?></td>
		  <td><?php echo $con_vv[2]; ?></td>
		  <td><?php echo $con_vv[3]; ?></td>
		  <td><?php echo $con_vv[4]; ?></td>
		  <td><?php echo $con_vv[5]; ?></td>
		  <td><?php echo $con_vv[6]; ?></td>
		  <td><?php echo $con_vv[7]; ?></td>
		  <td><?php echo $con_vv[8]; ?></td>
		  <td><?php echo $con_vv[9]; ?></td>
		  <td><?php echo $con_vv[10]; ?></td>
		  <td><?php echo $con_vv[11]; ?></td>
		  <td><?php echo $con_vv[12]; ?></td>
		  <td><?php echo $con_vv[13]; ?></td>
		  <td><?php echo $con_vv[14]; ?></td>
		  <td><?php echo $con_vv[15]; ?></td>
            </tr>
          <tr>
            <td>ECOGRAFIA</td>
            <td><?php echo $con_eco[0]; ?></td>
		  <td><?php echo $con_eco[1]; ?></td>
		  <td><?php echo $con_eco[2]; ?></td>
		  <td><?php echo $con_eco[3]; ?></td>
		  <td><?php echo $con_eco[4]; ?></td>
		  <td><?php echo $con_eco[5]; ?></td>
		  <td><?php echo $con_eco[6]; ?></td>
		  <td><?php echo $con_eco[7]; ?></td>
		  <td><?php echo $con_eco[8]; ?></td>
		  <td><?php echo $con_eco[9]; ?></td>
		  <td><?php echo $con_eco[10]; ?></td>
		  <td><?php echo $con_eco[11]; ?></td>
		  <td><?php echo $con_eco[12]; ?></td>
		  <td><?php echo $con_eco[13]; ?></td>
		  <td><?php echo $con_eco[14]; ?></td>
		  <td><?php echo $con_eco[15]; ?></td>
            </tr>
          <tr>
            <td>HB</td>
            <td><?php echo $con_hb[0]; ?></td>
		  <td><?php echo $con_hb[1]; ?></td>
		  <td><?php echo $con_hb[2]; ?></td>
		  <td><?php echo $con_hb[3]; ?></td>
		  <td><?php echo $con_hb[4]; ?></td>
		  <td><?php echo $con_hb[5]; ?></td>
		  <td><?php echo $con_hb[6]; ?></td>
		  <td><?php echo $con_hb[7]; ?></td>
		  <td><?php echo $con_hb[8]; ?></td>
		  <td><?php echo $con_hb[9]; ?></td>
		  <td><?php echo $con_hb[10]; ?></td>
		  <td><?php echo $con_hb[11]; ?></td>
		  <td><?php echo $con_hb[12]; ?></td>
		  <td><?php echo $con_hb[13]; ?></td>
		  <td><?php echo $con_hb[14]; ?></td>
		  <td><?php echo $con_hb[15]; ?></td>
            </tr>
          <tr>
            <td>GI</td>
            <td><?php echo $con_gi[0]; ?></td>
		  <td><?php echo $con_gi[1]; ?></td>
		  <td><?php echo $con_gi[2]; ?></td>
		  <td><?php echo $con_gi[3]; ?></td>
		  <td><?php echo $con_gi[4]; ?></td>
		  <td><?php echo $con_gi[5]; ?></td>
		  <td><?php echo $con_gi[6]; ?></td>
		  <td><?php echo $con_gi[7]; ?></td>
		  <td><?php echo $con_gi[8]; ?></td>
		  <td><?php echo $con_gi[9]; ?></td>
		  <td><?php echo $con_gi[10]; ?></td>
		  <td><?php echo $con_gi[11]; ?></td>
		  <td><?php echo $con_gi[12]; ?></td>
		  <td><?php echo $con_gi[13]; ?></td>
		  <td><?php echo $con_gi[14]; ?></td>
		  <td><?php echo $con_gi[15]; ?></td>
            </tr>
          <tr>
            <td>ORINA</td>
            <td><?php echo $con_ori[0]; ?></td>
		  <td><?php echo $con_ori[1]; ?></td>
		  <td><?php echo $con_ori[2]; ?></td>
		  <td><?php echo $con_ori[3]; ?></td>
		  <td><?php echo $con_ori[4]; ?></td>
		  <td><?php echo $con_ori[5]; ?></td>
		  <td><?php echo $con_ori[6]; ?></td>
		  <td><?php echo $con_ori[7]; ?></td>
		  <td><?php echo $con_ori[8]; ?></td>
		  <td><?php echo $con_ori[9]; ?></td>
		  <td><?php echo $con_ori[10]; ?></td>
		  <td><?php echo $con_ori[11]; ?></td>
		  <td><?php echo $con_ori[12]; ?></td>
		  <td><?php echo $con_ori[13]; ?></td>
		  <td><?php echo $con_ori[14]; ?></td>
		  <td><?php echo $con_ori[15]; ?></td>
            </tr>
          <tr>
            <td>OBSERVACION</td>
            <td><?php echo $con_obs[0]; ?></td>
		  <td><?php echo $con_obs[1]; ?></td>
		  <td><?php echo $con_obs[2]; ?></td>
		  <td><?php echo $con_obs[3]; ?></td>
		  <td><?php echo $con_obs[4]; ?></td>
		  <td><?php echo $con_obs[5]; ?></td>
		  <td><?php echo $con_obs[6]; ?></td>
		  <td><?php echo $con_obs[7]; ?></td>
		  <td><?php echo $con_obs[8]; ?></td>
		  <td><?php echo $con_obs[9]; ?></td>
		  <td><?php echo $con_obs[10]; ?></td>
		  <td><?php echo $con_obs[11]; ?></td>
		  <td><?php echo $con_obs[12]; ?></td>
		  <td><?php echo $con_obs[13]; ?></td>
		  <td><?php echo $con_obs[14]; ?></td>
		  <td><?php echo $con_obs[15]; ?></td>
            </tr>
    </table>
<?php } ?>

</body>
</html>
