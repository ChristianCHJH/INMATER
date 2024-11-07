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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <script src="js/jquery-1.11.1.min.js?v=1.0"></script>
    <script src="js/jquery.mobile-1.4.5.min.js?v=1.0"></script>
    <script>
        $(document).keydown('#listaproducto .ui-input-search', function(e){
            if(e.which == 13) {
                var producto = $('#listaproducto .ui-input-search :input')[0].value;
                // if (paciente.length > 3) {
                    $("#listaproducto .ui-input-search :input").prop("disabled", true);
                    $.post("le_tanque.php", {producto: producto}, function (data) {
                        $("#detalleproducto").html("");
                        $("#detalleproducto").append(data);
                        $('.ui-page').trigger('create');
                    })
                    .done(function() {
                        $("#listaproducto .ui-input-search :input").prop("disabled", false);
                        $("#listaproducto .ui-input-search :input").focus();
                    });
                // }
            }
        });

         function agregarProducto(id) {
            var idproducto = id;
                    $.post("le_tanque.php", {idproducto: idproducto}, function (data) {
                        console.log(typeof(data));
                        $("#productoregistro").append('<tr><td>'+data+'&nbsp;&nbsp;&nbsp;<input type="hidden" name="producto[]" value="'+id+'"/></td><td>Cantidad <input type="text" name="cantidad[]" value="" data-mini="true" class="numeros" required></td></tr>');
                    })
        }

        $(document).keydown('#listaproducto .ui-input-search', function(e){
            if(e.which == 13) {
                var producto = $('#listaproducto .ui-input-search :input')[0].value;
                // if (paciente.length > 3) {
                    $("#listaproducto .ui-input-search :input").prop("disabled", true);
                    $.post("le_tanque.php", {producto: producto}, function (data) {
                        $("#detalleproducto").html("");
                        $("#detalleproducto").append(data);
                        $('.ui-page').trigger('create');
                    })
                    .done(function() {
                        $("#listaproducto .ui-input-search :input").prop("disabled", false);
                        $("#listaproducto .ui-input-search :input").focus();
                    });
                // }
            }
        });

    </script>
    <style>
        .color {
            color: #F4062B;
        }

        .scroll_h {
            overflow-x: scroll;
            overflow-y: hidden;
            white-space: nowrap;
        }

        #alerta {
            background-color: #FF9;
            margin: 0 auto;
            text-align: center;
            padding: 4px;
        }

        /* Hide the number input */
        .ui-slider input[type=number] {
            display: none;
        }

        .ui-slider-track {
            margin-left: 0px;
        }

        .peke .ui-input-text {
            width: 70% !important

        }

        .peke2 .ui-input-text {
            width: 30px !important;
        }

        .peke2 span {
            float: left;
        }

        #ultimo {
            color: #9f2b1e;
        }

        .ui-tabs-panel {
            background-color: #FFF;
            padding: 5px;
        }

        .controlgroup-textinput {
                padding-top: .22em;
                padding-bottom: .22em;
        }
    </style>
</head>
<body>
<?php

function get_date_now() {
        return date("Y-m-d H:i:s");
    }
$cerrar="lista.php";
if ($_GET['id'] <> "") {
    $id = $_GET['id'];
    $date=get_date_now();
    $rUser = $db->prepare("SELECT role, userx FROM usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC);
    
    $rPedido = $farma->prepare("SELECT
     pedido.id,pedido.idmedico, pedido.dnipaciente, pedido.fechapedido, pedido.fechaprocedimiento,pedido.estado, detalle.cantidad,producto.producto,unidad.unidad
     FROM tblpedido pedido
     join tblpedidodetalle detalle on pedido.id = detalle.idpedido
     join tblproducto producto on detalle.idproductoventa = producto.id
     join tblunidad unidad on unidad.id = producto.idunidadcompra
     WHERE pedido.id=?");
    $rPedido->execute(array($id));
    $i=0;
    while($pedido = $rPedido->fetch(PDO::FETCH_ASSOC)) {
        $detallepedido[$i]=$pedido;
        $i++;
    }
    $idpaciente=$detallepedido[$i-1]['dnipaciente'];

    $rPaci = $db->prepare("SELECT * FROM hc_antece,hc_paciente WHERE hc_paciente.dni=? AND hc_antece.dni=?");
    $rPaci->execute(array($idpaciente, $idpaciente));
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

    /*if (isset($_POST['producto'])) {
        //var_dump($_POST['procedimiento']);
        $date=get_date_now();
        $idpedido=pedido_insertar(mb_strtolower($login),$date,$_POST['procedimiento'],'PENDIENTE', mb_strtolower($paci['dni']));
        for ($i=0; $i < sizeof($_POST['producto']); $i++) { 
            $idpedidodetalle=pedido_detalle_insertar($idpedido,$_POST['producto'][$i],0,$_POST['cantidad'][$i], 1);
        }
    }*/
 ?>
    <input type="hidden" name="dni" value="<?php echo $paci['dni']; ?>">
    <input type="hidden" name="login" value="<?php print($login); ?>">
    <div data-role="page" class="ui-responsive-panel">
            <div data-role="header" data-position="fixed">
                <a href="lista_pedido.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete" id="salir">Cerrar</a>
                <h2><?php echo $paci['ape']; ?>
                    <small>
                        <?php
                            echo $paci['nom'];
                            //alerta para la nota
                            $nota_color = "";
                            if ($paci['nota'] != "") {
                                $nota_color = "red";
                            }
                            if ($paci['fnac'] <> "1899-12-30")
                                echo ' <a href="#popupBasic" data-rel="popup" data-transition="pop" style="color:'.$nota_color.';">(' . date_diff(date_create($paci['fnac']), date_create('today'))->y . ')</a>';
                        ?>
                    </small>
                </h2>
            </div><!-- /header -->
        <div class="ui-content" role="main">
            <?php $must_update = !$paci['idsedes'] && $user['role'] == 1 ?>
            <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
                <div data-role="collapsible" data-collapsed="false"><h3>Datos del Pedido</h3>
                    <div class="scroll_h">
                        <table width="100%" align="center" style="margin: 0 auto;">
                            <tr>
                                <td>Numero de Pedido</td>
                                <td><input type="text" data-mini="true" value="<?php echo $detallepedido[$i-1]['id']; ?>" readonly /></td>
                                <td>Estado</td>
                                <td><input type="text" data-mini="true" value="<?php echo $detallepedido[$i-1]['estado']; ?>" readonly /></td>
                                <td>Fecha de Procedimiento</td>
                                <td><input type="text" data-mini="true" value="<?php echo $detallepedido[$i-1]['fechaprocedimiento']; ?>" readonly /></td>
                            </tr>
                        </table>
                        <br>
                        <table width="100%" align="center" style="margin: 0 auto; border: 1px;">
                            <tr>
                                <td align="center" width="55%">Producto</td>
                                <td align="center" width="30%">Unidad</td>
                                <td align="center" width="15%">Cantidad</td>
                            </tr>
                            <?php
                                    foreach ($detallepedido as $detalle) {
                                        print("<tr>
                                                <td width='55%'>".$detalle['producto']."</td>
                                                <td width='30%'>".$detalle['unidad']."</td>
                                                <td width='15%' align='right'>".$detalle['cantidad']."</td>
                                              </tr>");
                                    } ?>
                        </table>
                        <br>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
    <?php } ?>

    <script>
        $(document).ready(function () {
            var unsaved = false;
            $(":input").change(function () {

                unsaved = true;

            });

            $(window).on('beforeunload', function () {
                if (unsaved) {
                    return 'UD. HA REALIZADO CAMBIOS';
                }
            });

            // Form Submit
            $(document).on("submit", "form", function (event) {
                // disable unload warning
                $(window).off('beforeunload');
            });

            $('.numeros').keyup(function () {

                var $th = $(this);
                $th.val($th.val().replace(/[^0-9]/g, function (str) {
                    //$('#cod small').replaceWith('<small>Error: Porfavor ingrese solo letras y números</small>');

                    return '';
                }));

                //$('#cod small').replaceWith('<small>Aqui ingrese siglas o un nombre corto de letras y números</small>');
            });

            $('.chekes').change(function () {

                var temp = '#' + $(this).attr("id") + '1';

                if ($(this).prop('checked') || $(this).val() == "Medicamentada" || $(this).val() == "Otra" || $(this).val() == "Anormal") {

                    $(temp).prop('readonly', false);
                    //$(temp).placeholder=$(this).val();

                } else {
                    $(temp).prop('readonly', true);
                    $(temp).val('');
                }

            });

            <?php if (isset($_GET['pop']) && !empty($_GET['pop'])): ?>
                $(document).ready(function () {

                    var x = "<?php echo $_GET['pop']; ?>";
                    $("#" + x).collapsible({collapsed: false});

                });
            <?php endif ?>

            <?php if($must_update): ?>
                $('a').on('click', function(e){
                    const id = $(this).attr('id');
                    if( id == 'b_indice' || id == 'lista-pacientes' || id == 'salir' ) return;

                    /*if( confirm("Desea continuar sin guardar la sede?") )
                    {
                        return;
                    }*/
                    e.preventDefault();
                    e.stopPropagation();
                    // alert('Debe registrar la sede y guardar los datos');
                });
            <?php endif ?>
        });

        $(document).on("click", ".show-page-loading-msg", function () {
           /* if (document.getElementById("nom").value == "") {
                alert("Debe llenar el campo 'Nombre'");
                return false;
            }
            if (document.getElementById("ape").value == "") {
                alert("Debe llenar el campo 'Apellidos'");
                return false;
            }
            if (document.getElementById("fnac").value == "") {
                alert("Debe llenar el campo: Fecha de Nacimiento (Datos Generales)");
                return false;
            }
            if ($('#m_inf').prop('checked')) {

                if (document.getElementById("m_inf1").value == "") {
                    alert("Debe especificar la Infección");
                    return false;
                }
            }
            if (document.getElementById("m_ale").value == "Medicamentada" || document.getElementById("m_ale").value == "Otra") {

                if (document.getElementById("m_ale1").value == "") {
                    alert("Debe especificar la alergia");
                    return false;
                }
            }

            if( $('#sedes').val() == "" ) {
                alert("Debe seleccionar una sede");
                return false;
            }

            var $this = $(this),
                theme = $this.jqmData("theme") || $.mobile.loader.prototype.options.theme,
                msgText = $this.jqmData("msgtext") || $.mobile.loader.prototype.options.text,
                textVisible = $this.jqmData("textvisible") || $.mobile.loader.prototype.options.textVisible,
                textonly = !!$this.jqmData("textonly");
            html = $this.jqmData("html") || "";
            $.mobile.loading("show", {
                text: msgText,
                textVisible: textVisible,
                theme: theme,
                textonly: textonly,
                html: html
            });*/
        }).on("click", ".hide-page-loading-msg", function () {
            $.mobile.loading("hide");
        });

        $(function () {
            $("#alerta").prependTo(".ui-content");
            $('#alerta').delay(3000).fadeOut('slow');
        });
    </script>
    <script src="js/e_paci.js?v=1" crossorigin="anonymous"></script>
</body>
</html>