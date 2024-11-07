<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="lista-admin.php"><small>INMATER</small></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mantenimiento</a>
        <div class="dropdown-menu">
					<a class="dropdown-item" href="man-biopsia.php">Biopsia</a>
          <a class="dropdown-item" href="man_configuracion.php">Configuraciones</a>
          <a class="dropdown-item" href="man_paciente.php">Datos Generales</a>
          <a class="dropdown-item" href="man_sede.php">Sede</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Médico</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <!-- reproduccion asistida -->
          <a class="dropdown-item" href="man_horario.php">RA - Disponibilidad de horarios</a>
          <a class="dropdown-item" href="man_horario_bloqueo.php">RA - Bloqueo de horarios</a>
          <a class="dropdown-item" href="man_extras_medico.php">RA - Extras</a>
          <a class="dropdown-item" href="man_poseidon.php">RA - Poseidon</a>
          <!-- ginecologia -->
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="man_gine_tipo_intervencion.php">GINE - Tipo Intervención</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Laboratorio</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="man-cateter.php">Cateter</a>
          <a class="dropdown-item" href="lab_celulas.php">Células</a>
          <a class="dropdown-item" href="lab_contraccion.php">Contracción</a>
          <a class="dropdown-item" href="lab_incubadora.php">Incubadora</a>
          <a class="dropdown-item" href="m_extras.php">Extras</a>
          <a class="dropdown-item" href="m_notas.php">Notas</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="man_testigo_biopsia.php">ANDRO - Testigo de Biopsia</a>
          <a class="dropdown-item" href="man_prueba_biopsia.php">ANDRO - Prueba de Biopsia</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="transfer_ecografia.php">TRANSFER - Ecografía</a>
        </div>
      </li>
    </ul>
    <a class="navbar-brand" href="salir.php"><img src="_libraries/open-iconic/svg/account-logout.svg" height="18" width="18" alt="icon name"></a>
  </div>
</nav>