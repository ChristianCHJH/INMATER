<div data-role="panel" id="indice_paci">
    <img src="_images/logo.jpg"/>
    <ul data-role="listview" data-inset="true" data-theme="a">
        <li data-role="list-divider"><h1><a href="lista_pro.php" rel="external" style="color:#000000">Lista de Procedimientos</a></h1></li>
        <li><a href="<?php echo "le_aspi0.php?id=" . $paci['pro']; ?>" rel="external">Dia 0</a></li>
        <li><a href="<?php echo "le_aspi1.php?id=" . $paci['pro']; ?>" rel="external">Dia 1</a></li>
        <?php if (isset($paci['dias']) and $paci['dias'] >= 2) { ?>
            <li><a href="<?php echo "le_aspi2.php?id=" . $paci['pro']; ?>" rel="external">Dia 2</a>
            </li> <?php } ?>
        <?php if (isset($paci['dias']) and $paci['dias'] >= 3) { ?>
            <li><a href="<?php echo "le_aspi3.php?id=" . $paci['pro']; ?>" rel="external">Dia 3</a>
            </li> <?php } ?>
        <?php if (isset($paci['dias']) and $paci['dias'] >= 4) { ?>
            <li><a href="<?php echo "le_aspi4.php?id=" . $paci['pro']; ?>" rel="external">Dia 4</a>
            </li> <?php } ?>
        <?php if (isset($paci['dias']) and $paci['dias'] >= 5) { ?>
            <li><a href="<?php echo "le_aspi5.php?id=" . $paci['pro']; ?>" rel="external">Dia 5</a>
            </li> <?php } ?>
        <?php if (isset($paci['dias']) and $paci['dias'] >= 6) { ?>
            <li><a href="<?php echo "le_aspi6.php?id=" . $paci['pro']; ?>" rel="external">Dia 6</a>
            </li> <?php } ?>
    </ul>
    <div data-role="collapsible" data-mini="true">
        <h3>Historia Clínica</h3>
        <ul data-role="listview">
            <li data-theme="b"><a href="<?php echo "e_paci.php?id=" . $paci['dni']; ?>" rel="external">Datos y Antecedentes</a></li>
            <li data-theme="b"><a href="<?php echo "n_pare.php?id=" . $paci['dni']; ?>" rel="external">Pareja</a></li>
            <li data-theme="b"><a href="<?php echo "n_gine.php?id=" . $paci['dni']; ?>" rel="external">Ginecología</a></li>
            <li data-theme="b"><a href="<?php echo "n_obst.php?id=" . $paci['dni']; ?>" rel="external">Obstetricia</a></li>
            <li data-theme="b"><a href="<?php echo "n_repro.php?id=" . $paci['dni']; ?>" rel="external">Repro. Asistida</a></li>
        </ul>
    </div>
</div>