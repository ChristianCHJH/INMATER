@extends('layouts.paciente')

@section('title', 'Crear paciente')

@section('content')
<style>
    .ui-dialog-contain {
        max-width: 900px;
        margin: 1% auto 1%;
        padding: 0;
        position: relative;
        top: -35px!important;
    }

    .scroll_h {
        overflow-x: scroll;
        overflow-y: hidden;
        white-space: nowrap;
    }
    .ui-dialog{
        background: #fff !important;
    }
    .select-container {
        display: flex;
        align-items: center;
    }
    .select-container select {
        flex: 1;
    }
    .select-container button {
        margin-left: 10px; /* Ajusta el espacio entre el select y el botón si es necesario */
    }
</style>
<script src="{{ asset('js/jquery-1.11.1.min.js') }}"></script>
<script src="{{ asset('js/jquery.mobile-1.4.5.min.js') }}"></script> 

<div data-role="page" class="ui-responsive-panel" id="n_paci" data-dialog="true">
    <div data-role="header" data-position="fixed">
        <?php
            if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
                print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } else {
                print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } ?>
        <h1>Nuevo Paciente</h1>
    </div> 

    <div class="ui-content" role="main">
        <form action=paciente/guardar" method="post" enctype="multipart/form-data" data-ajax="false" id="formapi" name="formapi">
            @csrf
            <input type="hidden" name="query-counts" id="query-counts"> 
            <div class="scroll_h">
                <table width="100%" align="center" style="margin: 0 5px;max-width:860px;">
                    <tr>
                        <td class="color_red">*Campos obligatorios</td>
                    </tr>
                    <tr>
                        <td>Tipo de Cliente<span class="color_red">*</span></td>
                        <td>Programa<span class="color_red">*</span></td>
                    </tr>
                    <tr>
                        <td>
                            <select name="don" id="don" data-mini="true">
                                <option value="">Seleccionar</option>
                                @foreach($clientTypes as $cliente)
                                    <option value="{{ $cliente->codigo }}">{{ $cliente->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td colspan="2">
                            <select name="medios_comunicacion_id" id="medios_comunicacion_id" data-mini="true">
                                <option value="">Seleccionar</option>
                            </select>
                        </td>
                        <td class="text-center" style="position:relative; top:20px">Medico Tratante <span class="color_red">*</span></td>
                        <td class="text-center" style="position:relative; top:20px">Asesora</td> 
                    </tr>
                    <tr>
                        <td>Procedencia <span class="color_red">*</span></td>
                        <td colspan="2">
                            <select name="sede" id="sede" data-mini="true">
                                <option value="">Seleccionar</option>
                                @foreach($locations as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td colspan="1" class="text-center">
                            <select name="m_tratante" id="m_tratante" data-mini="true">
                                <option value="">Seleccionar</option>
                                @foreach($attendingPhysician as $medico)
                                    <option value="{{ $medico->id }}">{{ $medico->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td colspan="1" class="text-center">
                            <select name="asesora" id="asesora" data-mini="true">
                                <option value="">Seleccionar</option>
                                @foreach($medicalAdvisors as $asesora)
                                    <option value="{{ $asesora->id }}">{{ $asesora->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="9%">Nombres <span class="color_red">*</span></td>
                        <td width="19%"><input name="nom" type="text" id="nom" data-mini="true" /></td>
                        <td width="13%">Apellidos <span class="color_red">*</span></td>
                        <td width="29%"><input name="ape" type="text" id="ape" data-mini="true" /></td>
                        <td rowspan="8">
                            <fieldset data-role="controlgroup">
                                <select name="nac" id="nac" data-mini="true">
                                    <option value="">Nacionalidad</option>
                                    <option value="PE">Peru</option>
                                    @foreach($countries as $pais)
                                        <option value="{{ $pais->countrycode }}">{{ $pais->countryname }}</option>
                                    @endforeach 
                                </select>
                                <select name="raz" id="raz" data-mini="true">
                                    <option value="">Raza:</option>
                                    <option value="Blanca">Blanca</option>
                                    <option value="Morena">Morena</option>
                                    <option value="Mestiza">Mestiza</option>
                                    <option value="Asiatica">Asiatica</option>
                                </select>
                                <select name="san" id="san" data-mini="true">
                                    <option value="">Grupo Sanguineo:</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
                                <input name="talla" type="number" step="any" id="talla" data-mini="true" placeholder="Talla(Cm)" />
                                <input name="peso" type="number" step="any" id="peso" data-mini="true" placeholder="Peso(Kg)" />
                                <img src="{{  asset('images/foto.gif') }}" alt="" width="100px" height="100px" id="preview" />
                                <input name="foto" type="file" onchange="previewImage(this)" accept="image/jpeg" id="foto" />
                            </fieldset> 
                        </td>
                    </tr>
                    <tr>
                        <td> 
                            <div class="select-container">
                                <select name="tip" id="tip" data-mini="true">
                                    <option value="DNI" selected>DNI *</option>
                                    <option value="PAS">PAS *</option>
                                    <option value="CEX">CEX *</option>
                                </select>
                                <div class="feedback text-red-500 text-sm mt-1"></div>
                                <button id="validate-icon" type="button" data-mini="true"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                        </td>
                        <td><input name="dni" type="text" id="dni" data-mini="true" class="alfanumerico" />
                            <div class="feedback text-red-500 text-sm mt-1"></div>
                        </td>
                        <td>F. Nac <span class="color_red">*</span></td>
                        <td><input name="fnac" type="date" id="fnac" data-mini="true" /></td>
                    </tr>
                    <tr>
                        <td>Celular <span class="color_red">*</span></td>
                        <td><input name="tcel" type="text" id="tcel" data-mini="true" class="numeros" />
                            <div class="feedback text-red-500 text-sm mt-1"></div>
                        </td>
                        <td>E-Mail <span class="color_red">*</span></td>
                        <td><input name="mai" type="email" id="mai" data-mini="true"></td>
                    </tr>
                    <tr>
                        <td>T. Casa</td>
                        <td><input name="tcas" type="number" step="any" id="tcas" data-mini="true"
                                class="numeros" />
                        </td>
                        <td>Profesión</td>
                        <td><input name="prof" type="text" id="prof" data-mini="true" /></td>
                    </tr>
                    <tr>
                        <td>T. Oficina</td>
                        <td><input name="tofi" type="text" id="tofi" data-mini="true" /></td>
                        <td>Referido por <span class="color_red">*</span></td>
                        <td><select name="rem" id="rem" data-mini="true" title="Referido Por">
                                <option value="">Seleccionar:</option>
                                @foreach($referenceMediums as $ref)
                                    <option value="{{ $ref->id }}">{{ $ref->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Dep/Prov/Dis</td>
                        <td>
                            <select name="depa" id="depa" data-mini="true">
                                <option value="">Seleccionar</option> 
                            </select>
                        </td> 
                        <td>
                            <select name="prov" id="prov" data-mini="true">
                                <option value="">Seleccionar</option>
                            </select>
                        </td> 
                        <td>
                            <select name="dist" id="dist" data-mini="true">
                                <option value="">Seleccionar</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Dirección <span class="color_red">*</span></td>
                        <td colspan="3">
                            <input name="dir" type="text" id="dir" data-mini="true" />
                            <div class="feedback text-red-500 text-sm mt-1"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>Observación</td>
                        <td colspan="3">
                            <textarea name="obs" id="obs" data-mini="true" rows="4"></textarea>
                        </td>
                    </tr> 
                </table>
            </div>
            <input type="Submit" value="AGREGAR DATOS" name="btn" data-icon="check" data-iconpos="left"
                    data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true"
                    data-msgtext="Agregando paciente.." data-theme="b" data-inline="true" />
        </form>
    </div>
</div>
<script src="{{ public_asset('js/utils_fx.js') }}"></script> 
<script src="{{ asset('js/paciente.js') }}"></script> 
@endsection
