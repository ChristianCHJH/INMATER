<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
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
    </script>
    <style>
        .controlgroup-textinput {
            padding-top: .10em;
            padding-bottom: .10em;
        }
        .positivo {
            background-color: #f8d7da!important;
            cursor: pointer;
        }

        .con-cita {
            background: #EEC584!important;
            cursor: pointer;
        }

        .con-ecografia {
            background: #B6CB9E!important;
            cursor: pointer;
        }

        .ui-icon-file:after{
            background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgaGVpZ2h0PSIzMnB4IiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAzMiAzMiIgd2lkdGg9IjMycHgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6c2tldGNoPSJodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2gvbnMiIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj48dGl0bGUvPjxkZXNjLz48ZGVmcy8+PGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIiBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSI+PGcgZmlsbD0iIzE1N0VGQiIgaWQ9Imljb24tNzAtZG9jdW1lbnQtZmlsZS1wZGYiPjxwYXRoIGQ9Ik0yMSwxMyBMMjEsMTAgTDIxLDEwIEwxNSwzIEw0LjAwMjc2MDEzLDMgQzIuODk2NjY2MjUsMyAyLDMuODk4MzM4MzIgMiw1LjAwNzMyOTk0IEwyLDI3Ljk5MjY3MDEgQzIsMjkuMTAxMjg3OCAyLjg5MDkyNTM5LDMwIDMuOTk3NDIxOTEsMzAgTDE5LjAwMjU3ODEsMzAgQzIwLjEwNTcyMzgsMzAgMjEsMjkuMTAxNzg3NiAyMSwyOC4wMDkyMDQ5IEwyMSwyNiBMMjguOTkzMTUxNywyNiBDMzAuNjUzNzg4MSwyNiAzMiwyNC42NTc3MzU3IDMyLDIzLjAwMTIxNDQgTDMyLDE1Ljk5ODc4NTYgQzMyLDE0LjM0MjYwMjEgMzAuNjY0MDA4NSwxMyAyOC45OTMxNTE3LDEzIEwyMSwxMyBMMjEsMTMgTDIxLDEzIFogTTIwLDI2IEwyMCwyOC4wMDY2MDIzIEMyMCwyOC41NTUwNTM3IDE5LjU1MjMwMjYsMjkgMTkuMDAwMDM5OCwyOSBMMy45OTk5NjAyLDI5IEMzLjQ1NDcwODkzLDI5IDMsMjguNTU0MzE4NyAzLDI4LjAwNDU0MyBMMyw0Ljk5NTQ1NzAzIEMzLDQuNDU1MjYyODggMy40NDU3MzUyMyw0IDMuOTk1NTc3NSw0IEwxNCw0IEwxNCw4Ljk5NDA4MDk1IEMxNCwxMC4xMTM0NDUyIDE0Ljg5NDQ5NjIsMTEgMTUuOTk3OTEzMSwxMSBMMjAsMTEgTDIwLDEzIEwxMi4wMDY4NDgzLDEzIEMxMC4zNDYyMTE5LDEzIDksMTQuMzQyMjY0MyA5LDE1Ljk5ODc4NTYgTDksMjMuMDAxMjE0NCBDOSwyNC42NTczOTc5IDEwLjMzNTk5MTUsMjYgMTIuMDA2ODQ4MywyNiBMMjAsMjYgTDIwLDI2IEwyMCwyNiBaIE0xNSw0LjUgTDE1LDguOTkxMjE1MjMgQzE1LDkuNTQ4MzUxNjcgMTUuNDUwNjUxMSwxMCAxNS45OTY3Mzg4LDEwIEwxOS42OTk5NTEyLDEwIEwxNSw0LjUgTDE1LDQuNSBaIE0xMS45OTQ1NjE1LDE0IEMxMC44OTI5OTU2LDE0IDEwLDE0LjkwMDE3NjIgMTAsMTUuOTkyMDE3IEwxMCwyMy4wMDc5ODMgQzEwLDI0LjEwODE0MzYgMTAuOTAyMzQzOCwyNSAxMS45OTQ1NjE1LDI1IEwyOS4wMDU0Mzg1LDI1IEMzMC4xMDcwMDQ0LDI1IDMxLDI0LjA5OTgyMzggMzEsMjMuMDA3OTgzIEwzMSwxNS45OTIwMTcgQzMxLDE0Ljg5MTg1NjQgMzAuMDk3NjU2MiwxNCAyOS4wMDU0Mzg1LDE0IEwxMS45OTQ1NjE1LDE0IEwxMS45OTQ1NjE1LDE0IFogTTI1LDE5IEwyNSwxNyBMMjksMTcgTDI5LDE2IEwyNCwxNiBMMjQsMjMgTDI1LDIzIEwyNSwyMCBMMjgsMjAgTDI4LDE5IEwyNSwxOSBMMjUsMTkgWiBNMTIsMTggTDEyLDIzIEwxMywyMyBMMTMsMjAgTDE0Ljk5NTExODUsMjAgQzE2LjEwMjM4NCwyMCAxNywxOS4xMTIyNzA0IDE3LDE4IEMxNywxNi44OTU0MzA1IDE2LjEwNjEwMDIsMTYgMTQuOTk1MTE4NSwxNiBMMTIsMTYgTDEyLDE4IEwxMiwxOCBaIE0xMywxNyBMMTMsMTkgTDE1LjAwMTA0MzQsMTkgQzE1LjU1Mjc1MTksMTkgMTYsMTguNTU2MTM1MiAxNiwxOCBDMTYsMTcuNDQ3NzE1MyAxNS41NTczMzk3LDE3IDE1LjAwMTA0MzQsMTcgTDEzLDE3IEwxMywxNyBaIE0xOCwxNiBMMTgsMjMgTDIwLjk5NTExODUsMjMgQzIyLjEwMjM4NCwyMyAyMywyMi4xMTM0NDUyIDIzLDIwLjk5NDA4MDkgTDIzLDE4LjAwNTkxOTEgQzIzLDE2Ljg5ODA4MDYgMjIuMTA2MTAwMiwxNiAyMC45OTUxMTg1LDE2IEwxOCwxNiBMMTgsMTYgWiBNMTksMTcgTDE5LDIyIEwyMS4wMDEwNDM0LDIyIEMyMS41NTI3NTE5LDIyIDIyLDIxLjU1NjI4MzQgMjIsMjEuMDAwMTkyNSBMMjIsMTcuOTk5ODA3NSBDMjIsMTcuNDQ3NjI5MSAyMS41NTczMzk3LDE3IDIxLjAwMTA0MzQsMTcgTDE5LDE3IEwxOSwxNyBaIiBpZD0iZG9jdW1lbnQtZmlsZS1wZGYiLz48L2c+PC9nPjwvc3ZnPg==');
            background-color: unset;
            -webkit-border-radius: 0;
            border-radius: 0;
            background-size: 95%;

        }
    </style>
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" data-dialog="true">
    <?php
        if(isset($_POST['boton_datos'])){if ($_POST['boton_datos'] == "AGENDAR CONSULTA" and isSet($_POST['dni']) and isSet($_POST['fec']) and isSet($_POST['fec_h'])) {

            insertBetaCitaEco($_POST['dni'], $_POST['pro'], $_POST['fec'], $login, $_POST['fec_h'], $_POST['fec_m'], $_POST['mot']);

        }}
        if (isset($_POST['val_beta']) && !empty($_POST['val_beta']) && isset($_POST['pro_beta']) && !empty($_POST['pro_beta'])) {
        	$stmt = $db->prepare("UPDATE lab_aspira_t SET beta=?, iduserupdate=? where pro=? and estado is true");
        	$stmt->execute(array($_POST['val_beta'], $login, $_POST['pro_beta']));
        	print("<div id='alerta'> BETA Guardado! </div>");
    	}	
        $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
        $rUser->execute(array($login));
        $user = $rUser->fetch(PDO::FETCH_ASSOC);

        if ($user['role']==1 or $user['role']==2)
            if (isset($_GET['med']) && !empty($_GET['med'])) {
                if ($_GET['med']==1) {
                    $cerrar="lista.php";
                    $porMed=" and lab_aspira_t.med='".$login."'";
                } else {
                    $cerrar="lista_pro.php";
                    $porMed="";
                }
            } else {
                $cerrar="lista_pro.php";
                $porMed="";
            }
        // 
    $between=$t_med=$t_emb=$tipa=$edesde=$ehasta=$ngs="";
    if (isset($_POST) && !empty($_POST)) {
        if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
            // var_dump($_POST);
            $between.="
            and case lab_aspira_t.dia
                when 6 then lab_aspira.fec6
                when 5 then lab_aspira.fec5
                when 4 then lab_aspira.fec4
                when 3 then lab_aspira.fec3
                when 2 then lab_aspira.fec2
                else null end between '".$_POST["ini"]."' and '".$_POST["fin"]."'
            ";
        }
        if ( isset($_POST["edesde"]) && !empty($_POST["edesde"]) && isset($_POST["ehasta"]) && !empty($_POST["ehasta"]) ) {
            $edesde = $_POST['edesde']*365;
            $ehasta = $_POST['ehasta']*365;
            $between = " and datediff(lab_aspira.fec, hc_paciente.fnac) between $edesde and $ehasta";
            $url = "?edesde=$edesde&ehasta=$ehasta";
        }
        if (isset($_POST["ngs"]) and !empty($_POST["ngs"])) {
            $ngs=$_POST["ngs"];
            if ($ngs=="s") {
                $between.=" and hc_reprod.pago_extras ilike '%ngs%'";
            } else {
                $between.=" and hc_reprod.pago_extras not ilike '%ngs%'";
            }
        }
        if (isset($_POST["t_med"]) and !empty($_POST["t_med"])) {
            $t_med=$_POST["t_med"];
            $between.=" and lab_aspira_t.med = '".$t_med."'";
        }
        if (isset($_POST["t_emb"]) and !empty($_POST["t_emb"])) {
            $t_emb=$_POST["t_emb"];
            $between.=" and lab_aspira_t.emb = '".$t_emb."'";
        }
        if (isset($_POST["tipa"]) && !empty($_POST["tipa"])) {
            $tipa = $_POST['tipa'];
            $between.= " and lab_aspira.tip = '$tipa'";
            if ($url == "") {
                $url .= "?tipa=$tipa";
            } else {
                $url .= "&tipa=$tipa";
            }
        }
        if( isset($_POST['estado_beta']) and !empty( $_POST['estado_beta'] ) ) {
            switch ($_POST['estado_beta']) {
                case 'nuevo':
                    $between .= " and hc_eco_beta_positivo.fec is null";
                    break;
                case 'con-cita':
                    $between .= " and hc_eco_beta_positivo.efec = '1899-12-30'";
                    break;
                case 'con-ecografia':
                    $between .= " and hc_eco_beta_positivo.efec != '1899-12-30' and hc_eco_beta_positivo.efec is not null";
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }
    // print($porMed);
    // print("<br>");
    // print($between);
    $rPaci = $db->prepare(" 
        SELECT hc_analisis.*, hc_pare_paci.dni, hc_paciente.nom, hc_paciente.ape FROM hc_analisis
        INNER JOIN hc_pare_paci ON hc_analisis.a_dni = hc_pare_paci.p_dni
        INNER JOIN hc_paciente ON hc_pare_paci.dni = hc_paciente.dni
        WHERE a_exa = 'ESPERMACULTIVO' and a_med=? 
        and a_mue >= CURRENT_DATE - INTERVAL '7 days' 
        ORDER BY a_mue DESC
    ");
    $rPaci->execute(array($login));

    $medico="";
    if (isset($_GET['med']) && !empty($_GET['med'])) {
        $medico=$_GET['med'];
    }
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
        <a href="<?php echo $cerrar ?>" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
        <h2>Resultados de Espermacultivo</h2>
    </div>
    
    <div class="ui-content" role="main">
                <input type="hidden" name="val_beta">
                <input type="hidden" name="pro_beta">
                
                <div class="demo" style="position: relative; padding-top: 70px">
                    <div style="width:100%;display:inline-block;">
                        <ul data-role="listview" data-theme="a" data-split-icon="file" data-inset="true" data-filter="true" data-filter-placeholder="Filtro...">
                            <?php while($paci = $rPaci->fetch(PDO::FETCH_ASSOC)): ?>
                                <li  >
                                    <a href="e_pare.php?id=<?php echo $paci['dni'] ?>&ip=<?php echo $paci['a_dni'] ?>" class="<?php if( $paci['a_sta'] == 'Positivo' ) echo 'positivo' ?>" rel="external" target="_blank"><small class="paciente">
                                        <?php echo $paci['a_nom'] ?>                                        
                                    </small>

                                    <span class="ui-li-count">
                                        <?php echo $paci['a_sta'] ?> | 
                                        P: <?php echo $paci['ape'] . ' ' . $paci['nom'] ?> |
                                        <?php echo $paci['a_med'] ?> 
                                           
                                        <span class="fecha"><?php echo $paci['a_mue'] ?></span>
                                    
                                    </span></a>
                                    <a href="<?php echo "archivos_hcpacientes.php?idArchivo=".$paci['id']."_".$paci['a_dni']; ?>" target="_blank" >Ver/Descargar</a>
                                </li>

                            <?php endwhile ?>

                            <?php if ($rPaci->rowCount()<1)  echo '<p><h3>¡ No hay registros !</h3></p>';
                        ?>
                        </ul>
                    </div>
                </div>
            
           
    </div>
    </div>
    <script>
        jQuery(document).ready(function($){
            $('#alerta').delay(3000).fadeOut('slow');

            $('#tab-lista').on('click', function(){
                $('#tab-agendar').addClass('ui-disabled');
            });

            $('.sin-cita').on('click', function(){
                $('#tab-agendar').removeClass('ui-disabled').click();

                $paciente = $(this).find('.paciente');

                $('#nombre-paciente').text($paciente.text());
                $('#fecha-beta').text($(this).find('.fecha').text());
                $('#pro').val($(this).find('.pro').data('pro'));
                $('#dni').val($paciente.data('dni'));
            });

            $('.con-cita, .con-ecografia').on('click', function(){
                window.location.href = 'eco_beta_positivo.php?med=<?php echo $medico ?>&id=' + $(this).data('idecobetapositivo');
            });

            $('#proestado, #proestado option').on('click', function(e){
                e.stopPropagation();
            })
        });
    </script>
</body>
</html>