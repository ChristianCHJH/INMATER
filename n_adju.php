<!DOCTYPE HTML>
<html>
    <head>
    <?php
   include 'seguridad_login.php'
    ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="_images/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
        <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
        <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
        <link rel="stylesheet" href="css/n_adju.css"/>
    </head>
    <body>
        <div data-role="page" class="ui-responsive-panel" id="n_adju">
            <?php
            if (isset($_POST['dni']) && isset($_POST['guardar']) && $_POST['dni'] <> '' && $_POST['guardar'] == 'GUARDAR') {
                if ($_POST['cont'] >= 1) {
                    for ($p = 1; $p <= $_POST['cont']; $p++) {
                        $tan = explode("|", $_POST['c'.$p]);
                        $stmt2 = $db->prepare("UPDATE lab_aspira_dias SET adju=? WHERE pro=? and estado is true AND ovo=?");
                        $stmt2->execute(array($_POST['adju'.$p], $tan[0], $tan[1])); // Adjudica el dni de la paciente al ovo/embrion
                    } ?>

                    <script type="text/javascript">
                        var x = "<?php echo $_POST['dni']; ?>";
                        window.parent.location.href = "n_repro.php?id=" + x;
                    </script>
                <?php } else { echo "<div id='alerta'>NO hay nada para adjudicar</div>"; }
            }

            if ($_GET['id'] <> "") {
                $id = $_GET['id'];
                $rDon = $db->prepare("SELECT dni, nom, ape, don, med FROM hc_paciente WHERE don='D' ORDER BY ape ASC");
                $rDon->execute();
                $rDon->setFetchMode(PDO::FETCH_ASSOC);
                $rows = $rDon->fetchAll();
            ?>

            <div data-role="header" data-position="fixed"><h2>RESERVA OVULOS - EMBRIONES</h2></div>
            <div class="ui-content" role="main" style="overflow: auto">
                <form action="" method="post" data-ajax="false" name="form2">
                    <input type="hidden" name="dni" id="dni" value="<?php echo $id; ?>">
                    <div data-role="controlgroup" data-type="horizontal" class="ui-mini">
                        <select name="p_des" id="p_des" data-mini="true">
                            <option value="" selected>Seleccione Tipo:</option>
                            <option value=1>Embriones</option>
                            <option value=2>Ovulos</option>
                        </select>
                        <select name="donante" id="donante">
                            <option value="">Seleccione Donante:</option>
                            <?php foreach ($rows as $don) { ?>
                                <option value="<?php echo $don['dni']; ?>"><?php echo $don['ape'].' '.$don['nom'].' ('.$don['med'].')'; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="lista_des"></div>
                </form>
            </div>
            <?php } ?>
        </div>
        <script src="js/jquery-1.11.1.min.js"></script>
        <script src="js/jquery.mobile-1.4.5.min.js"></script>
        <script src="js/n_adju.js?v=1.0.0"></script>
        <script>
            $(function () {
                $('#alerta').delay(3000).fadeOut('slow');
            });
        </script>
    </body>
</html>