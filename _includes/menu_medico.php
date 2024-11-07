<?php
    $dni="";
    if ( isset($_GET['dni']) && !empty($_GET['dni']) ) {
        $dni = $_GET['dni'];
    }
    $rPaci = $db->prepare("SELECT * FROM hc_antece,hc_paciente WHERE hc_paciente.dni=? AND hc_antece.dni=?");
    $rPaci->execute(array($dni, $dni));
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Inmater</a>
  <!-- <a id="logo_inmater" href="lista.php">
    <img src="_images/logo.png" alt="">
  </a> -->

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pacientes</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="lista.php">Lista</a>
          <a class="dropdown-item" href="n_paci.php">Nuevo Paciente</a>
          <!-- <div class="dropdown-divider"></div> -->
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Agenda</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="agenda_frame.php">Programación</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Betas</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="med-betas-lista.php">Lista</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ginecología</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="r_pap.php">Reporte PAP</a>
          <a class="dropdown-item" href="r_parto.php">Reporte Partos</a>
        </div>
      </li>
      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Sala de Procedimientos</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="<?php print("n_legal.php?dni=".$paci['dni']); ?>">Legal</a>
          <a class="dropdown-item" href="<?php print("n_riesgo_quirurgico_01.php?dni=".$paci['dni']); ?>">Riesgo Quirúrgico</a>
          <a class="dropdown-item" href="<?php print("n_analisisclinico.php?dni=".$paci['dni']); ?>">Análisis Clínico</a>
          <a class="dropdown-item" href="<?php print("n_psicologia.php?dni=".$paci['dni']); ?>">Psicología</a>
          <a class="dropdown-item" href="<?php print("n_cariotipo.php?dni=".$paci['dni']); ?>">Cariotipo</a>
          <a class="dropdown-item" href="<?php print("n_enfermeria.php?dni=".$paci['dni']); ?>">Enfermería</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="ayuda.php">Ayuda</a>
      </li>
    </ul>
    <a class="navbar-brand" href="salir.php"><img src="_libraries/open-iconic/svg/account-logout.svg" height="18" width="18" alt="icon name"></a>
  </div>
</nav>