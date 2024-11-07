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
        var productosagregados=[];

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
                        for (var i = 0; i<productosagregados.length ; i++) {
                            if(productosagregados[i]==idproducto){
                                $("#cantidad"+idproducto)[0].value++; 
                                return;
                            }
                        }
                        productosagregados.push(idproducto);
                        $("#productoregistro").append(data);
                    })
        }

        function checkstock(id) {
                cantidad=Number($("#cantidad"+id)[0].value);
                stock=Number($("#stock"+id)[0].value);
                if(cantidad>stock){
                    $("#alerta"+id)[0].hidden=false;
                }else{
                    $("#alerta"+id)[0].hidden=true;
                }
        }

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
$date=get_date_now();
if ($_GET['id'] <> "") {
    $id = $_GET['id'];
    $rUser = $db->prepare("SELECT role, userx FROM usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC);

    $rPaci = $db->prepare("SELECT * FROM hc_antece,hc_paciente WHERE hc_paciente.dni=? AND hc_antece.dni=?");
    $rPaci->execute(array($id, $id));
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

    $rEco = $db->prepare("SELECT * FROM hc_analisis WHERE a_dni=? AND lab='eco' ORDER BY a_fec DESC");
    $rEco->execute(array($id));
    if (isset($_POST['producto'])) {
        $idpedido=pedido_insertar(mb_strtolower($login),$date,$_POST['procedimiento'],'PENDIENTE', mb_strtolower($paci['dni']));
        for ($i=0; $i < sizeof($_POST['producto']); $i++) { 
            $idpedidodetalle=pedido_detalle_insertar($idpedido,$_POST['producto'][$i],0,$_POST['cantidad'][$i], 1);
        }
    }
 ?>

<form action="pedido.php?id=<?php echo $paci['dni']; ?>" method="post" enctype="multipart/form-data" data-ajax="false" name="form2">
    <input type="hidden" name="dni" value="<?php echo $paci['dni']; ?>">
    <input type="hidden" name="login" value="<?php print($login); ?>">
    <div data-role="page" class="ui-responsive-panel" id="e_paci">
        <?php if ((isset($_GET['pop']) && $_GET['pop'] <> 1) || !isset($_GET['pop'])) { 
            ?>
            <div data-role="header" data-position="fixed">
                <a href="<?php echo $cerrar; ?>" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
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
                <a href="salir.php"
                    id="salir"
                   class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power"
                   rel="external">Salir</a>
            </div><!-- /header -->
        <?php } ?>
        <div class="ui-content" role="main">
            <?php // $older_than_yesterday = strtotime('yesterday') > strtotime($paci['update']) ?>
            <?php // $must_update = !$paci['update'] || @$older_than_yesterday ?>
            <?php //$must_update = !$paci['idsedes'] && $user['role'] == 1 ?>

            <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
                <div data-role="collapsible" data-collapsed="false"><h3>Datos Generales</h3>
                    <div class="scroll_h">
                        <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                            <tr>
                                <td width="9%">Nombre(s)</td>
                                <td width="19%"><input name="nom" type="text" id="nom" data-mini="true"
                                                       value="<?php echo $paci['nom']; ?>" readonly /></td>
                                <td width="13%">Apellidos</td>
                                <td width="29%"><input name="ape" type="text" id="ape" data-mini="true"
                                                       value="<?php echo $paci['ape']; ?>" readonly /></td>
                            </tr>
                            <tr>
                                <td><?php echo $paci['tip']; ?></td>
                                <td><input name="tip" type="text" id="tip" data-mini="true"
                                           value="<?php echo $paci['dni']; ?>" readonly /></td>
                                <td>F. Nac</td>
                                <td><input name="fnac" type="date" id="fnac" data-mini="true"
                                           value="<?php echo $paci['fnac']; ?>" readonly /></td>
                            </tr>
                            <tr>
                                <td>Celular</td>
                                <td><input name="tcel" type="text" id="tcel" data-mini="true" class="numeros"
                                           value="<?php echo $paci['tcel']; ?>" readonly /></td>
                                <td>E-Mail</td>
                                <td><input name="mai" type="text" id="mai" data-mini="true"
                                           value="<?php echo $paci['mai']; ?>" readonly ></td>
                            </tr>
                            <tr>
                                <td>Productos:</td>
                                <td colspan="3">
                                    <table id="productoregistro" width='100%' style="margin: 0 auto;">
                                        <tr>
                                            <td width='70%'>Producto</td>
                                            <td width='15%'>Cantidad</td>
                                            <td width='15%'>Stock</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                             <tr>
                                <td>Ingresar Fecha de Procedimiento</td>
                                <td><input name="procedimiento" type="date" id="procedimiento" value="" data-mini="true" required></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php
                                    if ($user['role'] == 1 || $user['role'] == 16) { ?>
                                        <input type="Submit" value="GUARDAR PEDIDO" name="boton_datos" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Agregando pedido..." data-theme="b" data-inline="true"/>
                                    <?php } ?>
                                    
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            <div id="listaproducto">
            <?php

                
                $rProd= pedidosInmaterApp();
                echo '</ul>';
                ?>
            <ol id="detalleproducto" data-role="listview" data-theme="a" data-filter="true" data-filter-placeholder="Digite todo o parte de los datos del producto y presione enter."
                    data-inset="true">
                    <?php 
                    if ($rProd < 1) echo '<p><h3>¡ No hay Productos !</h3></p>'; ?>
            </ol>
            </div>

            </div>
            
        </div>
    </div>
    <?php } ?>
</form>
    <script>
        $(document).ready(function () {
            var unsaved = false;
            $(":input").change(function () {

                unsaved = true;

            });
            
            <?php /*
            $rProducto = $dbfarmacia->prepare("SELECT
                producto.id
                                FROM tblproducto producto
                                join tblproductounidadventa puv on producto.id=puv.idproducto
                                where puv.stock>1 order by producto.producto asc ");
                $rProducto->execute();
                //cambio la manera de traer datos
                while ($producto = $rProducto->fetch(PDO::FETCH_ASSOC)) {
                    //$nombre=$producto['producto'];
                    $lista
                    print("
                        <td width='70%' align='left'><span id=id".$idproducto.">".$producto['producto']."</span>
                        <input type='hidden' name='producto[]' value='".$idproducto."'/></td><td width='15%'><input type='number' name='cantidad[]' value='1' data-mini='true' class='numeros ' min='1'  required></td>
                        <td width='15%' align='right'><span class='col0'>".$producto['stock']."</span></td>
                        </td>
                        ");//max='".$producto['stock']."'
                }*/
            ?>

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

            $(".med_insert").change(function () {
                var med = $(this).attr("title");
                var str = $('#' + med).val();
                var items = $(this).val();

                var n = str.indexOf(items);

                if (n == -1) {  // no agrega duplicados
                    $('#' + med).val(items + ", " + str);
                    if (items == "Borrar") $('#' + med).val("");
                    $('#' + med).textinput('refresh');
                }

                $(this).prop('selectedIndex', 0);
                $(this).selectmenu("refresh", true);
            });

            <?php if (isset($_GET['pop']) && !empty($_GET['pop'])): ?>
                $(document).ready(function () {

                    var x = "<?php echo $_GET['pop']; ?>";
                    $("#" + x).collapsible({collapsed: false});

                });
            <?php endif ?>

            /*<?php //if($must_update): ?>
                $('a').on('click', function(e){
                    const id = $(this).attr('id');
                    if( id == 'b_indice' || id == 'lista-pacientes' || id == 'salir' ) return;

                    /*if( confirm("Desea continuar sin guardar la sede?") )
                    {
                        return;
                    }*/
                   // e.preventDefault();
                   
                   // e.stopPropagation();
                    // alert('Debe registrar la sede y guardar los datos');
                //});
            //<?php //endif ?>*/
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