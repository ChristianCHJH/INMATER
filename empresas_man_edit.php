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
      if (isset($_GET["id"]) && !empty($_GET["id"])) {
        $stmt = $db->prepare("select * from man_empresas s where id= ? order by id");
        $stmt->execute([$_GET["id"]]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
      }
    ?>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Inicio</li>
        <li class="breadcrumb-item"><a href="lista_facturacion.php">Facturación</a></li>
        <li class="breadcrumb-item" aria-current="page">Mantenimiento</li>
        <li class="breadcrumb-item"><a href="empresas_man.php">Empresas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
      </ol>
    </nav>
    <div class="card mb-3">
      <input type="hidden" name="conf">
      <h5 class="card-header">Información General</h5>
      <div class="card-body">
        <input type="hidden" name="empresa_id" id="empresa_id" value="<?php print($_GET["id"]); ?>">

          <div class="row pb-2">
              <!-- centro de costo -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Servicio mi fact*</span>
                      <input class="form-control form-control-sm" type="text" name="SERVICE_MIFACT" id="SERVICE_MIFACT" value="<?php print($data["service_mifact"]); ?>" required/>
                  </div>
              </div>

              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Token*</span>
                      <input class="form-control form-control-sm" type="text" name="TOKEN" id="TOKEN" value="<?php print($data["TOKEN"]); ?>" required/>
                  </div>
              </div>
          </div>
          <div class="row pb-2">
              <!-- codigo -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Código tipo nif emisor*</span>
                      <input class="form-control form-control-sm" type="text" name="COD_TIP_NIF_EMIS" id="COD_TIP_NIF_EMIS" value="<?php print($data["cod_tip_nif_emis"]); ?>" required/>
                  </div>
              </div>
              <!-- nombre -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Número nif del emisor*</span>
                      <input class="form-control form-control-sm" type="text" name="NUM_NIF_EMIS" id="NUM_NIF_EMIS" value="<?php print($data["num_nif_emis"]); ?>" required/>
                  </div>
              </div>
          </div>
          <div class="row pb-2">
              <!-- codigo -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Nombre comercial emisor*</span>
                      <input class="form-control form-control-sm" type="text" name="NOM_COMER_EMIS" id="NOM_COMER_EMIS" value="<?php print($data["nom_comer_emis"]); ?>" required/>
                  </div>
              </div>
              <!-- nombre -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Código ubicación emisor*</span>
                      <input class="form-control form-control-sm" type="text" name="COD_UBI_EMIS" id="COD_UBI_EMIS" value="<?php print($data["cod_ubi_emis"]); ?>" required/>
                  </div>
              </div>
          </div>
          <div class="row pb-2">
              <!-- codigo -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Domicilio fiscal del emisor*</span>
                      <input class="form-control form-control-sm" type="text" name="TXT_DMCL_FISC_EMIS" id="TXT_DMCL_FISC_EMIS" value="<?php print($data["txt_dmcl_fisc_emis"]); ?>" required/>
                  </div>
              </div>
              <!-- nombre -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Enviar a sunat*</span>
                      <input class="form-control form-control-sm" type="text" name="ENVIAR_A_SUNAT" id="ENVIAR_A_SUNAT" value="<?php print($data["enviar_a_sunat"]); ?>" required/>
                  </div>
              </div>
          </div>
        <div class="row pb-2">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text">Nombre razón social emisor*</span>
                    <input class="form-control form-control-sm" type="text" name="NOM_RZN_SOC_EMIS" id="NOM_RZN_SOC_EMIS" value="<?php print($data["nom_rzn_soc_emis"]); ?>" required/>
                </div>
            </div>
          <!-- nombre -->
          <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text">Estado*</span>
                <select name='estado' id="estado" class="form-control form-control-sm chosen-select" required>
                    <option value="">SELECCIONAR</option>
                    <?php

                        if ($data['estado']=='1'){
                            $selected = "selected";
                            print('<option value="1" '.$selected.'>Alta</option>');
                            print('<option value="0"  >Baja</option>');
                        }
                        else{
                            $selected = "selected";
                            print('<option value="1" >Alta</option>');
                            print('<option value="0" '.$selected.'>Baja</option>');
                        }
                    ?>
                </select>
            </div>
          </div>
        </div>
        <div class="row pb-2">
          <!-- agregar -->
          <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
              <input class="form-control btn btn-danger btn-sm" type="Submit" name="actualizar" id="actualizar" value="Actualizar"/>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="js/jquery-1.11.1.min.js"></script>
  <script src="js/chosen.jquery.min.js"></script>
  <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script src="js/empresas_man.js"></script>
</body>
</html>