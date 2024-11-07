<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php';
   require("_database/database_farmacia.php");
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script>
        function Beta(beta,pro) {
            document.form2.val_beta.value=beta.value;
            document.form2.pro_beta.value=pro;
            document.form2.submit();
        }

        $(document).keydown('#listapaciente .ui-input-search', function(e){
            if(e.which == 13) {
                var paciente = $('#listapaciente .ui-input-search :input')[0].value;
                // if (paciente.length > 3) {
                    $("#listapaciente .ui-input-search :input").prop("disabled", true);
                    $.post("le_tanque.php", {paciente: paciente,tipo:"pedido"}, function (data) {
                        $("#detallepaciente").html("");
                        $("#detallepaciente").append(data);
                        $('.ui-page').trigger('create');
                    })
                    .done(function() {
                        $("#listapaciente .ui-input-search :input").prop("disabled", false);
                        $("#listapaciente .ui-input-search :input").focus();
                    });
                // }
            }
        });
    </script>
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" data-dialog="true">
    <?php  
        $rUser = $db->prepare("SELECT userX,role FROM usuario WHERE userx=?");
        $rUser->execute(array($login));
        $user = $rUser->fetch(PDO::FETCH_ASSOC);

        $cerrar="lista.php";
        $porMed="";
        // 
    $between=$t_med=$t_emb=$tipa=$edesde=$ehasta=$ngs="";

    ?>
    <style>
        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }
        .ui-dialog-contain {
            max-width: 1000px;
            margin: 1% auto 1%;
            padding: 0;
            position: relative;
            top: -35px;
        }
        .col0 { background-color:#FFFF91 !important; }
        .col1 { background-color:#FFEBCD !important; }
        #alerta { background-color:#FF9;margin: 0 auto; text-align:center; padding:4px;}
    </style>
    <div data-role="header" data-position="fixed">
        <a href="<?php echo $cerrar; ?>" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
        <h2>Lista de Pedidos</h2>
    </div>
    <div class="ui-content" role="main">
        <!--<ul data-role="listview" data-theme="a" data-inset="true" class="analisis">
            <li data-role="list-divider" style="background-color: #FFFF91;"><a href="pedido.php" rel="external" style="text-decoration: none;"><h4><center>NUEVO PEDIDO</center></h4></a></li>
        </ul>-->
        <form action="" method="post" data-ajax="false" name="form2">
            <?php
            if ( $user['role'] == 1 || $user['role'] == 16)
            { ?>
            <div id="listapaciente">
            <?php
                echo '</ul>';
                ?>
            <ol id="detallepaciente" data-role="listview" data-theme="a" data-filter="true" data-filter-placeholder="Digite todo o parte de los datos del paciente, presione enter y seleccione un paciente."
                    data-inset="true">
                    <?php
                    } ?>
            </ol>
            </div>
            <div style="width:800px;display:inline-block;">
                    <ul data-role="listview" data-theme="a" data-inset="true">
                        <?php
                        
                        $rPedido = $farma->prepare("SELECT
                            id,idmedico, dnipaciente, fechapedido, fechaprocedimiento,estado
                            FROM tblpedido  where idmedico ilike '%".$login."%'
                            ORDER BY id asc");
                        $rPedido->execute();

                        $t_0=0; $t_1=0; $t_2=0; $t_3=0; $t_4=0; $i = 0;
                        while($pedido = $rPedido->fetch(PDO::FETCH_ASSOC)) {
                            $rPaci = $db->prepare("SELECT
                                ape, nom
                                FROM hc_paciente where dni ilike  '%".$pedido['dnipaciente']."%'");
                            $rPaci->execute();
                            $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

                            $beta = $pedido['fechapedido']; //la fecha del dia de transferencia
                            //$beta = date('d-m-Y', strtotime($beta.' + '.$dt.' days')); ?> 
                            <li>
                                <?php echo "<a href='pedido_detalle.php?id=".$pedido['id']."' rel='external' class='ui-btn'>"; ?>
                                <?php echo "Paciente: ".$paci['nom']." ".$paci['ape']." <span class='ui-li-count'> Fecha Procedimiento: ".$pedido['fechaprocedimiento']."</span>"; 
                                echo "</a>"; ?>
                            </li>
                            
                       <?php }?>
                    </ul>
                </div>
        </form>     
    </div>
    </div>
    <script>
        $(function(){
            $('#alerta').delay(3000).fadeOut('slow');
        });//]]> 
    </script>
</body>
</html>