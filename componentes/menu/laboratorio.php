<?php
$stmt = $db->prepare("SELECT role FROM usuario WHERE userx=?");
$stmt->execute(array($login));
$data1 = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#"><small>INMATER</small></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <?php
            if ($data1["role"] == '2') { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Procedimientos
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    if ($data1["role"] == '2') {
                        print('<a class="dropdown-item" href="lista_pro.php?id=&t=&s=1">En curso</a>');
                    }
                    if ($data1["role"] == '2') {
                        print('<a class="dropdown-item" href="lista_pro_8.php?id=&t=&s=1">Próximos</a>');
                    }
                    if ($data1["role"] == '2') {
                        print('<a class="dropdown-item" href="lista_pro_f.php?id=&t=&s=1">Finalizados</a>');
                    } ?>
                </div>
            </li>
            <?php } ?>

            <?php
            if ($data1["role"] == '2') { ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Agenda
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php
                    if ($data1["role"] == '2') {
                        print('<a class="dropdown-item" href="lista.php?id=&t=&s=1">Programación</a>');
                    }
                    if ($data1["role"] == '2') {
                        print('<a class="dropdown-item" href="agenda_frame.php?id=&t=&s=1">Calendario</a>');
                    } ?>
                </div>
            </li>
            <?php } ?>

        </ul>
        <a class="navbar-brand" href="salir.php"><img src="_libraries/open-iconic/svg/account-logout.svg" height="18"
                width="18" alt="icon name"></a>
    </div>
</nav>