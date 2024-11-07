<!DOCTYPE HTML>
<html>
<head>
    @include('seguridad_login')
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{ public_asset('css/global.css') }}">
    <link rel="icon" href="{{ public_asset('_images/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ public_asset('_themes/tema_inmater.min.css') }}"/>
    <link rel="stylesheet" href="{{ public_asset('_themes/jquery.mobile.icons.min.css') }}"/>
    <link rel="stylesheet" href="{{ public_asset('css/jquery.mobile.structure-1.4.5.min.css') }}"/>
    <script src="{{ public_asset('js/jquery-1.11.1.min.js') }}"></script>
    <script src="{{ public_asset('js/jquery.mobile-1.4.5.min.js') }}"></script>
    <style>
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

        .ui-tabs-panel {
            background-color: #FFF;
            padding: 5px;
        }
    </style>
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

            $(document).on("submit", "form", function (event) {
                $(window).off('beforeunload');
            });

            $('.numeros').keyup(function () {
                var $th = $(this);
                $th.val($th.val().replace(/[^0-9]/g, ''));
            });

            $('.alfanumerico').keyup(function () {
                var $th = $(this);
                $th.val($th.val().replace(/[^a-zA-Z0-9]/g, ''));
            });
        });
    </script>
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" id="n_pare">
        <div data-role="panel" id="indice_paci">
            <img src="{{ public_asset('_images/logo.jpg') }}"/>
            <?php require ('_includes/menu_paciente.php'); ?>
        </div>

        <?php
        $color_programa_inmater = '';
        if ($paci['medios_comunicacion_id'] == 2) {
            $color_programa_inmater = ' class="programa_inmater"';
        } ?>

        <div data-role="header" data-position="fixed" <?php print($color_programa_inmater); ?>>
            <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU <small>> Pareja</small></a>
            <h2>{{ $paci['ape'] }}
                <small>
                <?php
                    //echo $paci['nom'];
                    $nota_color = "";
                    if ($paci['nota'] != "") {
                        $nota_color = "red";
                    }

                    if ($paci['fnac'] <> "1899-12-30") { echo ' <a href="#popupBasic" data-rel="popup" data-transition="pop" style="color:'.$nota_color.';">(' . date_diff(date_create($paci['fnac']), date_create('today'))->y . ')</a>'; } ?>
                </small>
            </h2>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external"> Salir</a>
        </div>
    </div>    
    <form action="" method="post" data-ajax="false"> 
                <div data-role="popup" id="popupBasic" data-arrow="true">
                    <textarea name="nota" id="nota" data-mini="true">{{ $paci['nota'] }}</textarea>
                    <input type="Submit" value="GRABAR" name="graba_nota" data-mini="true"/>
                </div>
                <div class="ui-content" role="main">
                    <input type="hidden" name="dni" id="dni" value="{{ $paci['dni'] }}">
                    <div data-role="tabs">
                        <div data-role="navbar">
                            <ul>
                                <li><a href="#one" data-ajax="false" class="ui-btn-active ui-btn-icon-left ui-icon-bullets">Historial de parejas</a></li>
                                <li><a href="#two" data-ajax="false" class="ui-btn-icon-left ui-icon-edit">Nueva Pareja</a></li>
                            </ul>
                        </div>
                        <div id="one" class="ui-body-d ui-content">
                            <legend>Pareja actual:</legend>
                            <fieldset data-role="controlgroup" data-type="horizontal">
                                <select name="pareja_actual" id="pareja_actual" data-mini="true">
                                    <option value="" selected>SELECCIONAR</option>
                                    @foreach ($rows as $data)
                                        <option value="{{ $data['p_dni'] }}" {{ $data['actual'] == 1 ? 'selected' : '' }}>{{ mb_strtoupper($data['p_ape']) }} {{ mb_strtoupper($data['p_nom']) }}</option>
                                    @endforeach
                                </select>
                                <input type="button" value="Actualizar" id="actualizar_pareja" onclick="location='consulta_paciente_parejas.php?dni={{ $paci['dni'] }}&pareja='+$('#pareja_actual').val();"/>
                            </fieldset>
                            <div class="scroll_h">
                                <table data-role="table" class="ui-responsive" data-column-btn-theme="b" data-column-popup-theme="a" data-stroke="true">
                                    <thead>
                                    <tr>
                                        <th data-priority="1">Apellidos</th>
                                        <th data-priority="2">Nombres</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($rows as $data)
                                        <tr>
                                            <td>{{ $data['p_ape'] }}</td>
                                            <td>{{ $data['p_nom'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="two">
                            <div class="ui-field-contain">
                                <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <label for="p_dni">DNI</label>
                                    <input type="text" name="p_dni" id="p_dni" class="numeros" value="">

                                    <label for="p_tip">Tipo</label>
                                    <select name="p_tip" id="p_tip">
                                        <option value="" selected>Seleccionar</option>
                                        @foreach ($doc as $data)
                                            <option value="{{ $data['id'] }}">{{ mb_strtoupper($data['nom']) }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <label for="p_nom">Nombres</label>
                                    <input type="text" name="p_nom" id="p_nom" class="alfanumerico" value="">

                                    <label for="p_ape">Apellidos</label>
                                    <input type="text" name="p_ape" id="p_ape" class="alfanumerico" value="">
                                </fieldset>
                                <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <label for="p_fnac">Fecha Nacimiento</label>
                                    <input type="date" name="p_fnac" id="p_fnac" value="">

                                    <label for="p_tcel">Teléfono Celular</label>
                                    <input type="text" name="p_tcel" id="p_tcel" class="numeros" value="">
                                </fieldset>
                                <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <label for="p_tcas">Teléfono Casa</label>
                                    <input type="text" name="p_tcas" id="p_tcas" class="numeros" value="">

                                    <label for="p_tofi">Teléfono Oficina</label>
                                    <input type="text" name="p_tofi" id="p_tofi" class="numeros" value="">
                                </fieldset>
                                <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <label for="p_mai">Correo</label>
                                    <input type="email" name="p_mai" id="p_mai" value="">

                                    <label for="p_dir">Dirección</label>
                                    <input type="text" name="p_dir" id="p_dir" class="alfanumerico" value="">
                                </fieldset>
                                <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <label for="p_prof">Profesión</label>
                                    <input type="text" name="p_prof" id="p_prof" class="alfanumerico" value="">

                                    <label for="p_san">Grupo Sanguíneo</label>
                                    <select name="p_san" id="p_san">
                                        <option value="" selected>Seleccionar</option>
                                        @foreach ($gsan as $data)
                                            <option value="{{ $data['id'] }}">{{ mb_strtoupper($data['nom']) }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <label for="p_raz">Raza</label>
                                    <select name="p_raz" id="p_raz">
                                        <option value="" selected>Seleccionar</option>
                                        @foreach ($raz as $data)
                                            <option value="{{ $data['id'] }}">{{ mb_strtoupper($data['nom']) }}</option>
                                        @endforeach
                                    </select>

                                    <label for="m_tratante">Médico Tratante</label>
                                    <select name="m_tratante" id="m_tratante">
                                        <option value="" selected>Seleccionar</option>
                                        @foreach ($med as $data)
                                            <option value="{{ $data['id'] }}">{{ mb_strtoupper($data['nom']) }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <label for="don">Donante</label>
                                    <select name="don" id="don">
                                        <option value="" selected>Seleccionar</option>
                                        @foreach ($don as $data)
                                            <option value="{{ $data['id'] }}">{{ mb_strtoupper($data['nom']) }}</option>
                                        @endforeach
                                    </select>

                                    <label for="medios_comunicacion_id">Medios Comunicación</label>
                                    <select name="medios_comunicacion_id" id="medios_comunicacion_id">
                                        <option value="" selected>Seleccionar</option>
                                        @foreach ($medcom as $data)
                                            <option value="{{ $data['id'] }}">{{ mb_strtoupper($data['nom']) }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <label for="sede">Sede</label>
                                    <select name="sede" id="sede">
                                        <option value="" selected>Seleccionar</option>
                                        @foreach ($sed as $data)
                                            <option value="{{ $data['id'] }}">{{ mb_strtoupper($data['nom']) }}</option>
                                        @endforeach
                                    </select>

                                    <label for="m_tratante">Médico Tratante</label>
                                    <select name="m_tratante" id="m_tratante">
                                        <option value="" selected>Seleccionar</option>
                                        @foreach ($med as $data)
                                            <option value="{{ $data['id'] }}">{{ mb_strtoupper($data['nom']) }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <input type="submit" value="GUARDAR DATOS" name="boton_datos"/>
                            </div>
                        </div>
                    </div>
                </div> 
    </form> 
</body>
</html>