<?php
$stmt = $db->prepare("SELECT role FROM usuario WHERE userx=?");
$stmt->execute(array($login));
$data1 = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#"><small>INMATER</small></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <?php
      if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Facturación
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="lista_facturacion.php">Lista Facturación</a>');
              print('<a class="dropdown-item" href="lista.php">Lista Facturación<small>(antiguo)</small></a>');
            }

            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="pagos_agenda.php">Programación Sala</a>');
            }

            if ($data1["role"] == '3') {
              print('<a class="dropdown-item" href="traslado.php">Traslados</a>');
            }

            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="lista_nota_credito.php">Lista de Notas Creditos</a>');
            }
            ?>
                </div>
            </li>
            <?php } ?>
            <?php
      if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19') { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Nuevo
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
            if ($data1["role"] == '3' or $data1["role"] == '10') {
              print('<a class="dropdown-item" href="n_pacipare.php">Paciente</a>');
            }

            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19') {
              print('<a class="dropdown-item" href="pago.php?id=&t=&s=1">Recibo</a>');
            } ?>
                </div>
            </li>
            <?php } ?>
            <?php
      if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Reporte</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
            if ($data1["role"] == '19') {
              print('<a class="dropdown-item" href="repo-data.php?path=lista_facturacion">Reporte Data</a>');
              print('<a class="dropdown-item" href="r_data.php?path=lista_facturacion">DATA</a>');
            }

            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="repo_pacientes.php">Pacientes</a>');
            }

            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="r_tanque.php?path=lista_facturacion">Tanque semen</a>');
            }

            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="pago_veri.php?path=lista_facturacion&x=x">Ultimos 100 Procedimientos</a>');
            }

            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="repo_conta_consolidado.php">Ventas Consolidado</a>');
              print('<a class="dropdown-item" href="repo_conta.php">Ventas Detallado</a>');
            }

            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="repo_crio_01.php">Crio de embriones</a>');
            }

            if ($data1["role"] == '3') {
              print('<a class="dropdown-item" href="log-recibos.php">Log Recibos</a>');
            } ?>
                </div>
            </li>
            <?php } ?>
            <?php
      if ($data1["role"] == '3' or $data1["role"] == '20') { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Mantenimiento
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
            if ($data1["role"] == '3' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="sede_man.php">Sedes</a>');
              print('<a class="dropdown-item" href="conta_centrocosto.php">Centros de Costo</a>');
              print('<a class="dropdown-item" href="conta_subcentrocosto.php">Subcentros de Costo</a>');
              print('<a class="dropdown-item" href="man_ser.php?tiposervicio=1">Servicios</a>');
              print('<div class="dropdown-divider"></div>');
              print('<a class="dropdown-item" href="man-tipo-cambio.php">Tipo de cambio</a>');
            } ?>
                </div>
            </li>
            <?php } ?>
            <?php
      if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Configuración
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
            if ($data1["role"] == '3' or $data1["role"] == '10' or $data1["role"] == '19' or $data1["role"] == '20') {
              print('<a class="dropdown-item" href="perfil.php?path=lista_facturacion">Cambiar contraseña</a>');
            } ?>
                </div>
            </li>
            <?php } ?>
        </ul>
        <a class="navbar-brand" href="salir.php"><img src="_libraries/open-iconic/svg/account-logout.svg" height="18" width="18" alt="icon name"></a>
    </div>
</nav>