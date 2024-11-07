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
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
  <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
</head>
<body>
  <?php require ('_includes/menu_facturacion.php'); ?>
  <div class="container">
    <?php
      $stmt = $db->prepare("SELECT * FROM usuario WHERE userx=?");
      $stmt->execute(array($login));
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      $Rpop = $db->prepare("select * from man_empresas");
      $Rpop->execute();
    ?>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Inicio</li>
        <li class="breadcrumb-item"><a href="lista_facturacion.php">Facturación</a></li>
        <li class="breadcrumb-item" aria-current="page">Mantenimiento</li>
        <li class="breadcrumb-item active" aria-current="page">Empresas</li>
      </ol>
    </nav>
    <div class="card mb-3">
      <h5 class="card-header">Información General</h5>
      <div class="card-body">
        <div class="row pb-2">
          <!-- centro de costo -->
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text">Servicio mi fact*</span>
                    <input class="form-control form-control-sm" type="text" name="SERVICE_MIFACT" id="SERVICE_MIFACT" required/>
                </div>
            </div>

            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text">Token*</span>
                    <input class="form-control form-control-sm" type="text" name="TOKEN" id="TOKEN" required/>
                </div>
            </div>
        </div>
        <div class="row pb-2">
          <!-- codigo -->
          <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
            <div class="input-group-prepend">
              <span class="input-group-text">Código tipo nif emisor*</span>
              <input class="form-control form-control-sm" type="text" name="COD_TIP_NIF_EMIS" id="COD_TIP_NIF_EMIS" required/>
            </div>
          </div>
          <!-- nombre -->
          <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
              <div class="input-group-prepend">
              <span class="input-group-text">Número nif del emisor*</span>
                  <input class="form-control form-control-sm" type="text" name="NUM_NIF_EMIS" id="NUM_NIF_EMIS" required/>
              </div>
          </div>
        </div>
          <div class="row pb-2">
              <!-- codigo -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Nombre comercial emisor*</span>
                      <input class="form-control form-control-sm" type="text" name="NOM_COMER_EMIS" id="NOM_COMER_EMIS" required/>
                  </div>
              </div>
              <!-- nombre -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Código ubicación emisor*</span>
                      <input class="form-control form-control-sm" type="text" name="COD_UBI_EMIS" id="COD_UBI_EMIS" required/>
                  </div>
              </div>
          </div>
          <div class="row pb-2">
              <!-- codigo -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Domicilio fiscal del emisor*</span>
                      <input class="form-control form-control-sm" type="text" name="TXT_DMCL_FISC_EMIS" id="TXT_DMCL_FISC_EMIS" required/>
                  </div>
              </div>
              <!-- nombre -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Enviar a sunat*</span>
                      <input class="form-control form-control-sm" type="text" name="ENVIAR_A_SUNAT" id="ENVIAR_A_SUNAT" required/>
                  </div>
              </div>
          </div>
          <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Nombre razón social emisor*</span>
                      <input class="form-control form-control-sm" type="text" name="NOM_RZN_SOC_EMIS" id="NOM_RZN_SOC_EMIS" required/>
                  </div>
              </div>
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Estado*</span>
                      <select name='estado' id="estado" class="form-control form-control-sm chosen-select" required>
                          <option value="">SELECCIONAR</option>
                          <option value="1">Alta</option>
                          <option value="0">Baja</option>
                      </select>
                  </div>
              </div>
          </div>
        <div class="row pb-2">
          <?php
          if($data1["role"] == "3" or $data1["role"] == "10") { ?>
            <!-- agregar -->
            <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                <input class="form-control btn btn-danger btn-sm" type="Submit" name="agregar" id="agregar" value="Agregar"/>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php
    print('<div><b>Total de Registros:</b> '.$Rpop->rowCount().'</div>'); ?>
    <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Buscar..." title="escribe un nombre">
    <div class="card mb-3">
      <div class="card-body collapse show" id="collapseExample">
        <table class="table table-responsive table-bordered align-middle" style="height: 50vh;" id="tb_servicios">
          <thead class="thead-dark">

            <th class="text-center">Id</th>
            <th class="text-center">Servicio mi fact</th>
            <th>Token</th>
            <th>Código tipo nif emisor</th>
            <th>Número nif del emisor</th>
            <th>Nombre comercial emisor</th>
            <th>Código ubicación emisor</th>
            <th>Domicilio fiscal del emisor</th>
            <th>Enviar a sunat</th>
            <th>Nombre razón social emisor</th>
            <th>Estado</th>
            <?php
            if($data1["role"] == "3" or $data1["role"] == "10") { ?>
              <th class="text-center">Acción</th>
            <?php } ?>
          </thead>
          <tbody>
            <?php
              while ($info = $Rpop->fetch(PDO::FETCH_ASSOC)) {
                  if ($info['estado']=='1'){
                      $estado='<span class="badge badge-secondary">Alta</span>';
                  }else{
                      $estado='<span class="badge badge-secondary">Baja</span>';
                  }
                print("<tr>
					<td class='text-center'>".$info['id']."</td>
				    <td class='text-center'>".$info['service_mifact']."</td>
                  <td>".mb_strtoupper($info['token'])."</td>
                  <td>".mb_strtoupper($info['cod_tip_nif_emis'])."</td>
                  <td>".mb_strtoupper($info['num_nif_emis'])."</td>
                  <td>".mb_strtoupper($info['nom_rzn_soc_emis'])."</td>
                  
                  <td>".mb_strtoupper($info['cod_ubi_emis'])."</td>
                  <td>".mb_strtoupper($info['txt_dmcl_fisc_emis'])."</td>
                  <td>".mb_strtoupper($info['ENVIAR_A_SUNAT'])."</td>
                  <td>".mb_strtoupper($info['nom_rzn_soc_emis'])."</td>
                  <td>".$estado."</td>");

                if($data1["role"] == "3" or $data1["role"] == "10") {
                  print("<td class='text-center'>
                        <a href='empresas_man_edit.php?id=".$info['id']."'><i class='far fa-edit'></i></a>
                        <a href='javascript:cambiarestado(".$info['id'].",".$info['estado'].");'><i class='fas fa-eye'></i></a></td>");
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
  <script src="js/empresas_man.js"></script>
</body>
</html>