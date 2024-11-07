<ul data-role="listview" data-inset="true" data-theme="a">
    <?php
    if ($user['role'] == 1 || $user['role'] == 11 || $user['role'] == 12 || $user['role'] == 15) { ?>
        <li data-icon="bars"><a href="lista.php" id="lista-pacientes" rel="external">Lista de Pacientes</a></li>
        <li data-icon="plus"><a href="n_paci.php" rel="external">Nuevo Paciente</a></li>

        <?php
        if ( $user['role'] == 1 || $user['role'] == 16 ) {
            echo '<li data-icon="plus"><a href="pedido.php?id='.$paci['dni'].'" rel="external">Nuevo Pedido</a></li>';
        }

        if ( $user['role'] == 1 || $user['role'] == 11 ) {
            print('<li data-icon="calendar"><a href="agenda_frame.php" rel="external">Agenda</a></li>');
        }

        if ( $user['role'] == 1 ) {
            print('
            <li data-icon="bullets"><a href="r_pap.php" rel="external">Reporte PAP</a></li>
            <li data-icon="bullets"><a href="r_parto.php" rel="external">Reporte Partos</a></li>
            <li data-icon="bullets"><a href="med-betas-lista.php" rel="external">Lista Betas</a></li>');
        } ?>

        <li data-icon="info"><a href="ayuda.php" rel="external">Ayuda</a></li>
    <?php }

    if( $user['role'] == 2 ) {
        print('<li data-icon="back"><a href="lista_pro.php" rel="external">Regresar</a></li>');
    } ?>

    <!-- foto -->
    <li data-role="list-divider" style="height:50px"><img src="<?php echo $foto_url; ?>">
        <div style="float:right;">
            <small><?php echo $paci['ape'] . "<br>" . $paci['nom']; ?></small>
        </div>
    </li>

    <?php
    if ($user['role'] == 1 || $user['role'] == 2 || $user['role'] == 11 || $user['role'] == 12 || $user['role'] == 15 || $user['role'] == 19) { ?>
        <li data-theme="b">
            <a href="<?php echo "e_paci.php?id=" . $paci['dni']; ?>" rel="external">Datos y Antecedentes</a></li>
        <li data-theme="b">
            <a href="<?php echo "n_pare.php?id=" . $paci['dni']; ?>" rel="external">Pareja</a>
        </li>
        <li data-theme="b">
            <a href="<?php echo "n_gine.php?id=" . $paci['dni']; ?>" rel="external">Ginecología</a>
        </li>
        <li data-theme="b">
            <a href="<?php echo "n_obst.php?id=" . $paci['dni']; ?>" rel="external">Obstetricia</a>
        </li>
        <li data-theme="b">
            <a href="<?php echo "n_repro.php?id=" . $paci['dni']; ?>" rel="external">Reproducción Asistida</a>
        </li>
        <li data-theme="b">
            <a href="<?php echo "n_analisis_clinico.php?dni=" . $paci['dni']; ?>" rel="external">Análisis Clínicos</a>
        </li>
        <li data-theme="b">
            <a href="<?php echo "n_procedimientos_medicos.php?dni=" . $paci['dni']; ?>" rel="external">Informes<br>Procedimientos<br>Médicos</a>
        </li>
        <li data-theme="b">
            <a href="<?php echo "n_interconsultas.php?dni=" . $paci['dni']; ?>" rel="external">Informes<br>Interconsultas</a>
        </li>
    <?php } ?>

    <?php
    if ($user['role'] == 1 || $user['role'] == 2 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 14 || $user['role'] == 15) { ?>
        <li data-theme="b">
            <a href="<?php echo "n_salaprocedimientos.php?dni=" . $paci['dni']; ?>" rel="external" target="_blank">Sala de Procedimientos</a>
        </li>
    <?php } ?>
</ul>