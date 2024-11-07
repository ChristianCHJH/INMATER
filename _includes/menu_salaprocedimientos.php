<?php
  $dni=$dni_mujer="";
  if ( isset($_GET['dni']) && !empty($_GET['dni']) ) {
      $dni = $_GET['dni'];
  }
  if ( !isset($tipopaciente) ) {
    $tipopaciente="";
  }
  /*
  $rPaci = $db->prepare("SELECT * FROM hc_paciente WHERE hc_paciente.dni=?");
  $rPaci->execute( array($dni) );
  $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
  */
  // datos paciente
  switch ($tipopaciente) {
    case 1:
      $rPaci = $db->prepare("select dni, ape, nom from hc_paciente where dni=?");
      $rPaci->execute( array($dni) ); break;
    case 2:
      $rPaci = $db->prepare("select p_dni dni, p_ape ape, p_nom nom from hc_pareja where p_dni=?");
      $rPaci->execute( array($dni) );
      $paci = $rPaci->fetch(PDO::FETCH_ASSOC);break;
    default:
      $rPaci = $db->prepare("select dni, ape, nom from hc_paciente where dni=?");
      $rPaci->execute( array($dni) ); break;
  }
  $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Inmater</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="<?php print("n_legal.php?dni=".$paci['dni']); ?>">Legal</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php print("n_riesgo_quirurgico.php?dni=".$paci['dni']); ?>">Riesgo Quirúrgico</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php print("n_analisisclinico.php?dni=".$paci['dni']); ?>">Análisis Clínico</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php print("n_psicologia.php?dni=".$paci['dni']); ?>">Psicología</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php print("n_cariotipo.php?dni=".$paci['dni']); ?>">Cariotipo</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php print("n_enfermeria.php?dni=".$paci['dni']); ?>">Enfermería</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="ayuda.php">Ayuda</a>
      </li>
    </ul>
    <a class="navbar-brand" href="salir.php"><img src="_libraries/open-iconic/svg/account-logout.svg" height="18" width="18" alt="icon name"></a>
  </div>
</nav>