<?php
session_start();
error_reporting(0);
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/_database/igeno_informe.php");

$protocolo = $_GET["pro"];
$paciente = traer_paciente($protocolo);
$pareja = traer_pareja($paciente["dni"]);
$paciente_iniciales = mb_strtoupper(substr($paciente["nom"], 0, 1) . substr($paciente["ape"], 0, 1));
$informe = traer_informe($protocolo);
$biologo_biopsia = traer_biologo($informe["biologo_biopsia_id"]);
$biologo_tubing = traer_biologo($informe["biologo_tubing_id"]);
$biologo_biopsia_d6 = traer_biologo($informe["biologo_biopsia_d6_id"]);
$biologo_tubing_d6 = traer_biologo($informe["biologo_tubing_d6_id"]);

function explodeDate($date = "")
{
    return explode("-", $date);
}
$fecha_extraccionovulos = explodeDate($informe["fecha_extraccionovulos"]);
$fecha_autorizacion = explodeDate($informe["fecha_autorizacion"]);

$biopsia_realizada_por = '';
$tubing_realizado_por = '';
$fecha_biopsia_1 = '';

$biopsia_realizada_por_d6 = '';
$tubing_realizado_por_d6 = '';
$fecha_biopsia_2 = '';

if(isset($_POST['protocolo']) && isset($_POST['pdf'])) {
    $protocolo = $_POST['protocolo'];
    $pdf = $_POST['pdf'];
    guardar_informe_ignomix($protocolo, $pdf);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300..800&family=Oxygen:wght@300;400;700&display=swap&family=Josefin+Sans:wght@100..700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-html2canvas@latest/dist/jspdf-html2canvas.min.js"></script>
    <link rel="stylesheet" href="css/info_igenomix_new.css">
    <script src="https://kit.fontawesome.com/556bf0993e.js" crossorigin="anonymous"></script>
    <script>
        const igeno_analisis_ids_invertidos = {
            3: "pgt_a_smart",
            5: "pgt_a_smart_plus",
            4: "pgt_sr_smart",
            6: "pgt_sr_smart_plus",
            2: "pgt_m_smart",
            1: "pgt_m_smart_pgt_a",
            7: "pgt_m_smart_pgt_a_plus"
        };

        const mitoscore = {
            1: "positivo_mitoscore",
            2: "negativo_mitoscore"
        };

        const idiomas = {
            1: "espanol",
            2: "ingles",
            3: "portugues",
            4: "italiano"
        };

        const origenovocito = {
            1: "ovocitos_propios",
            2: "ovocitos_donados",
            3: "semen_propio",
            4: "semen_donado"
        }

        const tipobiopsia = {
            1: "dia_3",
            2: "dia_5"
        }

        const metodofecundacion = {
            1: "fiv",
            2: "icsi"
        }
        const tipotransferencia = {
            1: "ciclo_fresco",
            2: "ciclo_congelado"
        }

        document.addEventListener("DOMContentLoaded", function() {
            let resultados_origenovocito = <?= json_encode($informe["origenovocito"]) ?>;

            resultados_origenovocito.forEach(function(indice) {
                document.getElementById(origenovocito[indice]).checked = true;
            });
            
            document.getElementById("pgt_a_smart_plus").checked = true
            document.getElementById(mitoscore[<?= json_encode($informe["sino_mitoscore_id"]) ?>]).checked = true;
            document.getElementById(idiomas[1]).checked = true;
            document.getElementById(tipobiopsia[2]).checked = true;
            document.getElementById(tipotransferencia[<?= json_encode($informe["tipotransferencia"]) ?>]).checked = true;
            if (<?= json_encode($informe["igeno_metodofecundacion_fiv"]) ?>) {
                document.getElementById("fiv").checked = true;
            }
            if (<?= json_encode($informe["igeno_metodofecundacion_icsi"]) ?>) {
                document.getElementById("icsi").checked = true;
            }

            let change = true;
            document.getElementById("print").addEventListener("click", (e) => {
                document.getElementById('dropdown-content').classList.toggle('active');
                let currentTarget = e.currentTarget.children[0];
                if (change) {
                    setTimeout(() => {
                        currentTarget.classList.remove("fa-angle-up");
                        currentTarget.classList.add("fa-xmark");
                    }, 400,currentTarget);
                    change = false;

                } else {
                    setTimeout(() => {
                        currentTarget.classList.remove("fa-xmark");
                        currentTarget.classList.add("fa-angle-up");
                    }, 400,currentTarget);
                    change = true;
                }
            });
        });

        document.addEventListener('keydown', e =>{

            if (e.key === "p" && e.ctrlKey) {
                e.preventDefault();
                document.getElementById("print-container").style.display = "none";
                window.print();
                document.getElementById("print-container").style.display = "block";
            }

            if(e.key === "s" && e.ctrlKey){
                e.preventDefault();
                printSave(e);
            }
            if(e.key === "r" && e.ctrlKey){
                window.location.reload();
            }

        });

        async function fetchData(pdf) {
            const response = await fetch("https://nd-be-eva-id6qsbxfsa-uc.a.run.app/api/pacientes/igenomix", {
                method: "POST",
                headers :{
                    "Authorization": "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZFVzZXIiOjEsInJvbFVzZXIiOjEsIm5hbWVVc2VyIjoiRU5ZRVJCRSBCQVJSSU9TIiwiZW1wcmVzYSI6MSwibmFtZUVtcHJlc2EiOiJGRVJUSUxJREFEIEVWQSBTLkEuQy4iLCJpYXQiOjE3MDk5MTIwODB9.h-5M7SRpNk9946H-8vkYr0ZWbaxhKTmqiv35x3iO7SQ",
                },
                body: pdf
            })

            const data = await response.text();
            const cleanUrl = data.replace(/^"((?:https?:\/\/)?[^"]*)"$/, "$1");
            return cleanUrl
        }

        async function printSave() {
            document.getElementById("spinner").style.display = "flex";
            const doc = new jspdf.jsPDF({
                orientation: "p",
                unit: "px",
                format: "a4",
                putOnlyUsedFonts: true
            });

            const containers = document.querySelectorAll('.container');

            for (let i = 0; i < containers.length; i++) {
                const container = containers[i];
                container.style.margin = "20px 5px";
                const canvas = await html2canvas(container);
                const imgData = canvas.toDataURL('image/jpeg', 1.0);

                if (i > 0) {
                    doc.addPage();
                }

                const marginX = 10;
                const marginY = 20;
                const imgWidth = doc.internal.pageSize.width - 2 * marginX;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                const yPos = i > 0 ? marginY : 10;

                doc.addImage(imgData, 'JPEG', marginX, yPos, imgWidth, imgHeight);
            }
            document.getElementById("spinner").style.display = "none";
            doc.output('dataurlnewwindow');
        }

        function appear(e) {
            const inputElement = e.currentTarget.querySelector('input[type="checkbox"]');
            
            if (inputElement) {
                if (inputElement.checked === false && inputElement.style.display === "") {
                    inputElement.style.display = "block";
                    inputElement.checked = true;
                }else{
                    inputElement.style.display = "";
                    inputElement.checked = false;
                }
            }
        }
    </script>
    <title>IGENOMIX</title>
</head>

<body>
    <div class="container">
        <div class="img-box">
            <img src="_images/igenomix.png" alt="">
        </div>
        <div class="phrase">PART OF &nbsp; <span class="oxygen-bold">VITROLIFE GROUP</span></div>
        <div class="line-divider"></div>
        <span class="title-bold">Formulario para solicitud de pruebas Smart PGT-A, PGT-SR, PGT-M & MitoScore</span>
        <br>
        <span class="recomendation">Los campos señalados con (*) son de obligatoria cumplimentación para la realización del test.</span>
        <br>
        <br>
        <table>
            <thead>
                <tr>
                    <th colspan="3">
                        <span class="header">
                            Datos Peticionario del estudio
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3">
                        <label for="clinica_hospital">*Clinica/Hospital:</label>
                        <input type="text" id="clinica_hospital" value="<?= mb_strtoupper($informe["peticionario_clinica"]) ?>">
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <label for="medico_remitente">*Médico remitente:</label>
                        <input type="text" id="medico_remitente" value="<?= mb_strtoupper($informe["peticionario_medicoremitente"]) ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="ivf_lab_manager">IVF Lab Manager/Director:</label>
                        <input type="text" id="ivf_lab_manager" value="<?= mb_strtoupper($informe["peticionario_labmanager"]) ?>">
                    </td>
                    <td colspan="2">
                        <label for="persona_contacto">*Persona de contacto:</label>
                        <input type="text" id="persona_contacto" value="SILVANA SESSAREGO">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%">
                        <label for="contacto">Email o teléfono de contacto:</label>
                        <input type="text" id="contacto" value="+511 4762727 EXT.108">
                    </td>
                    <td colspan="2">
                        <label for="email_resultados">*Email para entrega de resultados:</label>
                        <input type="text" id="email_resultados" value="<?= $informe["peticionario_mailresultados"] ?>">
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <label for="direccion">Dirección:</label>
                        <input type="text" id="direccion" value="AV. GUARDIA CIVIL 655, SAN BORJA">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="ciudad">Ciudad:</label>
                        <input type="text" id="ciudad" value="<?= mb_strtoupper($informe["peticionario_ciudad"]) ?>">
                    </td>
                    <td>
                        <label for="pais">País/Provincia/Estado:</label>
                        <input type="text" id="pais" value="PERÚ - <?= mb_strtoupper($informe["peticionario_provincia"]) ?>">
                    </td>
                    <td>
                        <label for="cp">C.P:</label>
                        <input type="text" id="cp" value="15036">
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="3">
                        <span class="header">
                            Datos del paciente(s)
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3">
                        <label for="nhc">Nº de historia (NHC):</label>
                        <input type="text" id="nhc" value="<?= mb_strtoupper($paciente["dni"]) ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <label for="nombre_paciente">*Nombre del paciente:</label>
                            <input type="text" id="nombre_paciente" value="<?= mb_strtoupper($paciente["nom"]) ?>" />
                        </div>
                    </td>
                    <td>
                        <div>
                            <label for="apellido_paciente">*Apellido(s):</label>
                            <input type="text" id="apellido_paciente" value="<?= mb_strtoupper($paciente["ape"]) ?>" />
                        </div>
                    </td>
                    <td>
                        <div>
                            <label for="fecha_nacimiento_paciente">*Fecha de nacimiento:</label>
                            <input type="text" id="fecha_nacimiento_paciente" value="<?= mb_strtoupper($paciente["fnac"]) ?>" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="nombre_pareja">*Nombre de la pareja:</label>
                        <input type="text" id="nombre_pareja" value="<?= mb_strtoupper($pareja["p_nom"]) ?>" />
                    </td>
                    <td>
                        <label for="apellido_pareja">*Apellido(s):</label>
                        <input type="text" id="apellido_pareja" value="<?= mb_strtoupper($pareja["p_ape"]) ?>" />
                    </td>
                    <td>
                        <label for="fecha_nacimiento_pareja">*Fecha de nacimiento:</label>
                        <input type="text" id="fecha_nacimiento_pareja" value="<?= mb_strtoupper($pareja["p_fnac"]) ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="f-center-items">
                            <div style="flex-grow: 1;">
                                <label for="contacto-pareja">E-mail de contacto:</label>
                                <input type="text" id="contacto-pareja" value="<?= $paciente["mai"] ?>">
                            </div>
                            <div style="flex-grow: 1;border-left: 1px solid black;padding-left: 10px;">
                                <label for="email_resultados-pareja">Teléfono de contacto:</label>
                                <input type="text" id="email_resultados-pareja" value="<?= mb_strtoupper($paciente["tcel"]) ?>">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div style="display: flex;justify-content: space-between;">
                            <div>
                                <label for="cariotipo">*Cariotipo(s):</label>
                            </div>
                            <div>
                                <input type="checkbox" id="cariotipo_paciente" <?= !isset($informe["cariotipo_paciente"]) ? "checked" : "" ?> />
                                <label for="cariotipo_paciente">Paciente</label>
                                <input type="text" id="cariotipo_paciente_input" value="<?= mb_strtoupper($informe["cariotipo_paciente"]) ?>" />
                            </div>
                            <div>
                                <input type="checkbox" id="cariotipo_pareja" <?= !isset($informe["cariotipo_pareja"]) ? "checked" : "" ?> />
                                <label for="cariotipo_pareja">Pareja</label>
                                <input type="text" id="cariotipo_pareja_input" value="<?= mb_strtoupper($informe["cariotipo_pareja"]) ?>"/>
                            </div>
                            <div>
                                <span class="text">
                                    Solo obligatorio en el caso de seleccionar PGT-SR.
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="3">
                        <span class="header">
                            *Análisis solicitado:
                        </span>
                    </th>
                </tr>
                <tr>
                    <th class="sub-header">
                        <h3>PGT-A</h3>
                        <h4>Análisis de Aneuploidías</h4>
                    </th>
                    <th class="sub-header">
                        <h3>PGT-SR</h3>
                        <h4>Alteraciones estructurales</h4>
                    </th>
                    <th class="sub-header">
                        <h3>PGT-M</h3>
                        <h4>Anomalías Monogénicas</h4>
                    </th>
                </tr>
            </thead>
            <tbody style="background-color: #f2f2f2;">
                <tr>
                    <td>
                        <div class="g-1to4">
                            <div class="mg-left-25">
                                <input type="checkbox" id="pgt_a_smart" />
                                <label for="pgt_a_smart" class="big-options">Smart PGT-A</label>
                            </div>
                            <div class="mg-left-25">
                                <input type="checkbox" id="pgt_a_smart_plus" />
                                <label for="pgt_a_smart_plus" class="big-options">Smart PGT-A Plus</label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="g-1to4">
                            <div class="mg-left-25">
                                <input type="checkbox" id="pgt_sr_smart" />
                                <label for="pgt_sr_smart" class="big-options">PGT-SR</label>
                            </div>
                            <div class="mg-left-25">
                                <input type="checkbox" id="pgt_sr_smart_plus" />
                                <label for="pgt_sr_smart_plus" class="big-options">PGT-SR Plus</label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="g-1to4">
                            <div class="mg-left-25">
                                <input type="checkbox" id="pgt_m_smart" />
                                <label for="pgt_m_smart" class="big-options">PGT-M</label>
                            </div>
                            <div class="mg-left-25">
                                <input type="checkbox" id="pgt_m_smart_pgt_a" />
                                <label for="pgt_m_smart_pgt_a" class="big-options">PGT-M + Smart PGT-A</label>
                            </div>
                            <div class="mg-left-25">
                                <input type="checkbox" id="pgt_m_smart_pgt_a_plus" />
                                <label for="pgt_m_smart_pgt_a_plus" class="big-options">PGT-M + Smart PGT-A Plus</label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <span class="text">
                            Smart PGT-A Plus y PGT-SR Plus incluyen ploidía y control de calidad (CC) del embrión
                            (detección de contaminación con ADN externo y análisis de parentesco de embriones hermanos)
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="2">
                        <span class="header" style="justify-content: center;">
                            Análisis adicionales para los servicios de PGT-A y PGT-SR
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody style="background-color: #f2f2f2;">
                <tr>
                    <td style="width: 50%;">
                        <div class="g-2-1-3">
                            <span class="open-sans-regular big-options">MitoScore:</span>
                            <div>
                                <input type="checkbox" id="positivo_mitoscore" checked />
                                <label for="positivo_mitoscore">Sí</label>
                            </div>
                            <div>
                                <input type="checkbox" id="negativo_mitoscore" />
                                <label for="negativo_mitoscore">No</label>
                            </div>
                        </div>
                        <span class="text" style="font-size: 10px;">
                            En caso de no indicar ninguna opción se informará del valor del MitoScore. <br>
                            MitoScore no disponible para informes sin mosaicismo con umbral de euploidía al 50%
                        </span>
                    </td>
                    <td>
                        <div class="g-2-1-3">
                            <span class="open-sans-regular big-options">Mosaicismo:</span>
                            <div>
                                <input type="checkbox" id="positivo_mosaicismo" checked />
                                <label for="positivo_mosaicismo">Sí</label>
                            </div>
                            <div>
                                <input type="checkbox" id="negativo_mosaicismo" />
                                <label for="negativo_mosaicismo">No</label>
                            </div>
                        </div>
                        <br>
                        <span>
                            <br>
                        </span>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <div class="f-center-items" style="gap: 200px;">
                            <span class="text">
                                Idioma del informe:
                            </span>
                            <div class="space-around" style="width: 600px;">
                                <div class="f-center-items">
                                    <input type="checkbox" id="ingles" />
                                    <label for="ingles">Inglés</label>
                                </div>
                                <div class="f-center-items">
                                    <input type="checkbox" id="espanol" />
                                    <label for="espanol">Español</label>
                                </div>
                                <div class="f-center-items">
                                    <input type="checkbox" id="portugues" />
                                    <label for="portugues">Portugués</label>
                                </div>
                                <div class="f-center-items">
                                    <input type="checkbox" id="italiano" />
                                    <label for="italiano">Italiano</label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="2">
                        <span class="header">
                            Información del ciclo
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody class="no-td-border just-border-right input-line">
                <tr>
                    <td style="width: 50%;">
                        <div class="g-1to3">
                            <div>
                                <div class="f-center-items">
                                    <input type="checkbox" id="ovocitos_propios" />
                                    <label for="ovocitos_propios">Ovocitos propios</label>
                                </div>
                                <div class="f-center-items">
                                    <input type="checkbox" id="semen_propio" />
                                    <label for="semen_propio">Semen propio</label>
                                </div>
                            </div>
                            <div>
                                <div class="f-center-items">
                                    <input type="checkbox" id="ovocitos_donados" />
                                    <label for="ovocitos_donados">Ovocitos donados</label>
                                </div>
                                <div class="f-center-items">
                                    <input type="checkbox" id="semen_donado" />
                                    <label for="semen_donado">Semen donado</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="g-1-2-2">
                            <label>*Tipo de biopsia:</label>
                            <div>
                                <input type="checkbox" id="dia_3" />
                                <label for="dia_3">Día 3 Blastómera</label>
                            </div>
                            <div>
                                <input type="checkbox" id="dia_5" />
                                <label for="dia_5">Día 5/6/7 Trofoectodermo</label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="display: flex;gap: 25px;align-items: flex-end;">
                            <label for="dia_extraccion_ovulos"> Fecha extracción óvulos: </label>
                            <div>
                                <input type="text" class="small-input" id="dia_extraccion_ovulos" value="<?= $fecha_extraccionovulos[2] ?>" /> /
                                <input type="text" class="small-input" id="mes_extraccion_ovulos" value="<?= $fecha_extraccionovulos[1] ?>" /> /
                                <input type="text" class="small-input" id="año_extraccion_ovulos" value="<?= $fecha_extraccionovulos[0] ?>" />
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex;gap: 25px;align-items: flex-end;">
                            <label for="dia_prevista_biopsia">*Fecha prevista de biopsia: </label>
                            <?php 
                                $extraccion_dia =  $informe["fecha_extraccionovulos"];
                                $en_5_dias = strtotime('+5 day', strtotime($extraccion_dia));
                                $en_5_dias = date('d-m-Y', $en_5_dias);
                                $explode_en_5_dias = explode("-", $en_5_dias);
                            ?>
                            <div>
                                <input type="text" class="small-input" id="dia_prevista_biopsia" value="<?= $explode_en_5_dias[0] ?>" /> /
                                <input type="text" class="small-input" id="mes_prevista_biopsia" value="<?= $explode_en_5_dias[1] ?>" /> /
                                <input type="text" class="small-input" id="año_prevista_biopsia" value="<?= $explode_en_5_dias[2] ?>" />
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="display: grid;grid-template-columns: 1fr 1fr;">
                            <div>
                                <label for="ovulos_fecundados">Óvulos fecundados:</label>
                                <input type="text" class="small-input fiftypercent" id="ovulos_fecundados" value="<?= $informe["ovulos_fecundados"] ?>" />
                            </div>
                            <div>
                                <label for="embriones_biopsiados"> Embriones biopsiados:</label>
                                <input type="text" class="small-input fiftypercent" id="embriones_biopsiados" value="<?= $informe["embriones_biopsiados"] ?>" />
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="g-1x1">
                            <label for="dia_transferencia_embrionaria" style="align-self: end;">Fecha/hora prevista para la transferencia embrionaria: &nbsp;</label>
                            <div style="align-self: end;">
                                <input type="text" class="small-input" id="dia_transferencia_embrionaria" /> /
                                <input type="text" class="small-input" id="mes_transferencia_embrionaria" /> /
                                <input type="text" class="small-input" id="año_transferencia_embrionaria" />
                            </div>
                            <div></div>
                            <span class="text" style="font-size: 10px;">
                                (Sólo obligatorio en caso de transferencias en el mismo ciclo)
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="g-1to3">
                            <label>Método fecundación:</label>
                            <div>
                                <input type="checkbox" id="fiv" />
                                <label for="fiv">FIV</label>
                            </div>
                            <div>
                                <input type="checkbox" id="icsi" />
                                <label for="icsi">ICSI</label>
                            </div>
                        </div>
                    </td>
                    <td rowspan="2">
                        <div style="display: flex;">
                            <label>*Transferencia embrionaria:</label>
                            <div style="margin-left: 15px;">
                                <div>
                                    <input type="checkbox" id="ciclo_fresco" />
                                    <label for="ciclo_fresco">Ciclo fresco (transferencia en el mismo ciclo)</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="ciclo_congelado" checked />
                                    <label for="ciclo_congelado">Ciclo congelado (transferencia en otro ciclo)</label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="g-1to3">
                            <label>Incubador Time-lapse:</label>
                            <div>
                                <input type="checkbox" id="incubador_positivo" checked />
                                <label for="incubador_positivo">Sí</label>
                            </div>
                            <div>
                                <input type="checkbox" id="incubador_negativo" />
                                <label for="incubador_negativo">No</label>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="2">
                        <span class="header">
                            Autorización del médico
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody class="no-td-border">
                <tr>
                    <td colspan="2">
                        <div class="text">
                            Certifico que la información del paciente y del médico prescriptor en esta solicitud es correcta según mi conocimiento y que he solicitado el test arriba indicado con base en mi criterio profesional
                            de indicación clínica. He explicado las limitaciones de este test y he respondido cualquier pregunta con criterio médico. Entiendo que Igenomix pueda necesitar información adicional y acepto
                            proporcionar esta información si es necesario.
                        </div>
                    </td>
                </tr>
                <tr style="height: 120px;">
                    <td>
                        <div class="" style="padding:0 30px">
                            <label for="firma_medico">*Firma del médico:</label>
                            <input type="text" id="firma_medico" />
                        </div>
                    </td>
                    <td>
                        <div class="" style="padding:0 30px">
                            <label for="fecha_autorizacion">Fecha: </label>
                            <input type="text" class="small-input" id="dia_fecha_autorizacion" value="" /> /
                            <input type="text" class="small-input" id="mes_fecha_autorizacion" value="" /> /
                            <input type="text" class="small-input" id="año_fecha_autorizacion" value="" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <footer>
            <span>
                (01) 267 0094 / 980 029 360
            </span>&nbsp;|&nbsp;
            <span>
                infolatam@igenomix.com
            </span>&nbsp;|&nbsp;
            <span>
                www.latam.igenomix.com
            </span>
        </footer>
    </div>
    <div class="container">
        <div class="img-box">
            <img src="_images/igenomix.png" alt="">
        </div>
        <div class="phrase">PART OF &nbsp; <span class="oxygen-bold">VITROLIFE GROUP</span></div>
        <div class="line-divider"></div>
        <label class="title-bold">Formulario para solicitud de pruebas Smart PGT-A, PGT-SR, PGT-M & MitoScore</label>
        <div class="title-bold" style="color: #ffb59c;">Hoja de Biopsia</div>
        <br>
        <span class="open-sans-bold">Los campos señalados con (*) son de obligatoria cumplimentación para la realización del test.</span>
        <br>
        <br>
        <table>
            <thead>
                <tr>
                    <th colspan="4">
                        <span class="header">
                            Datos del paciente(s)
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <label for="nhc">*Nº de historia (NHC):</label>
                        <input type="text" id="nhc" value="<?= mb_strtoupper($paciente["dni"]) ?>" />
                    </td>
                    <td>
                        <label for="nombre_paciente">*Nombre:</label>
                        <input type="text" id="nombre_paciente" value="<?= mb_strtoupper($paciente["nom"]) ?>" />
                    </td>
                    <td>
                        <label for="apellido_paciente">*Apellidos:</label>
                        <input type="text" id="apellido_paciente" value="<?= mb_strtoupper($paciente["ape"]) ?>" />
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="3">
                        <span class="header">
                            Indicaciones
                        </span>
                    </th>
                </tr>
            </thead>
            
            <tbody class="input-line">
                <tr style="background-color: #f2f2f2;">
                    <td style="width: 50%;">
                        <div class="g-1-6">
                            <h3 style="color: var(--color-primary);">PGT-A:</h3>
                            <div style="display: grid;grid-template-columns: 1fr 1fr;">
                                <div>
                                    <div>
                                        <input type="checkbox" id="edad_materna" name="edad_materna">
                                        <label for="edad_materna">Edad materna avanzada</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="fallo_implantacion" name="fallo_implantacion">
                                        <label for="fallo_implantacion">Fallo de implantación ( # de fallos <input class="small-input" type="text"> )</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="aborto_recurrente" name="aborto_recurrente">
                                        <label for="aborto_recurrente">Aborto recurrente (# abortos <input class="small-input" type="text"> )</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="gest_aneuploide_previa" name="gest_aneuploide_previa">
                                        <label for="gest_aneuploide_previa">Gestación aneuploide previa</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="gest_triploide_previa" name="gest_triploide_previa">
                                        <label for="gest_triploide_previa">Gestación triploide previa</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="gest_molar_previa" name="gest_molar_previa">
                                        <label for="gest_molar_previa">Gestación molar previa</label>
                                    </div>
                                </div>
                                <div>
                                    <div class="f-center-items">
                                        <input type="checkbox" id="emb_anomalos" name="emb_anomalos">
                                        <label for="emb_anomalos">Embriones derivados de ovocitos anomalmente fecundados</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="factor_masculino" name="factor_masculino">
                                        <label for="factor_masculino">Factor masculino</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="fish_anormal" name="fish_anormal">
                                        <label for="fish_anormal">FISH anormal de espermatozoides</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="enf_ligada_sexo" name="enf_ligada_sexo">
                                        <label for="enf_ligada_sexo">Enfermedad ligada al sexo</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" id="mosaicismo_crom_sexual" name="mosaicismo_crom_sexual">
                                        <label for="mosaicismo_crom_sexual">Mosaicismo del cromosoma sexual</label>
                                    </div>
                                    <div class="f-center-items">
                                        <input type="checkbox" id="otro" name="otro">
                                        <label for="otro">Otro: </label> 
                                        <input type="text" id="otra">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 25%;">
                        <div class="g-1-6">
                            <h3 style="color: var(--color-primary);">PGT-SR:</h3>
                            <div style="display: grid;grid-template-rows: repeat(6, 1fr);">
                                <div>
                                    <input type="checkbox" id="cariotipo_alterado" name="cariotipo_alterado">
                                    <label for="cariotipo_alterado">Cariotipo alterado</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="transloc_rob" name="transloc_rob">
                                    <label for="transloc_rob">Translocación Robertsoniana</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="inversion" name="inversion">
                                    <label for="inversion">Inversión</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="otro_reordenamiento" name="otro_reordenamiento">
                                    <label for="otro_reordenamiento">Otro reordenamiento cromosómico</label>
                                </div>
                                <div>
                                    <label for="cariotipo">Cariotipo:</label>
                                    <input type="text" id="cariotipo" name="cariotipo">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 20%;">
                        <div class="g-1-6">
                            <h3 style="color: var(--color-primary);">PGT-M:</h3>
                            <div style="height: 100%;">
                                <div>
                                    <input type="checkbox" id="enf_monogenica" name="enf_monogenica">
                                    <label for="enf_monogenica">Enfermedad monogénica (indicar):</label>
                                </div>
                                <input type="text" id="enf_monogenica_text" name="enf_monogenica_text">
                                <input type="text" id="enf_monogenica_text" name="enf_monogenica_text">
                            </div>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="3">
                        <span class="header">
                            Información de la biopsia
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody class="input-line">
                <?php

                $rPaci = $db->prepare("SELECT pro, fec5, fec6 FROM lab_aspira WHERE lab_aspira.pro=? and lab_aspira.estado is true");
                $rPaci->execute(array($protocolo));
                $paci = $rPaci->fetch(PDO::FETCH_ASSOC);


                if (verificar_dia5($protocolo)) {
                    $biopsia_realizada_por = $biologo_biopsia["nombres"];
                    $tubing_realizado_por = $biologo_tubing["nombres"];
                    $fecha_biopsia_1 = explode("-", $paci["fec5"]);
                }

                if (verificar_dia6($protocolo)) {
                    $biopsia_realizada_por_d6 = $biologo_biopsia_d6["nombres"];
                    $tubing_realizado_por_d6 = $biologo_tubing_d6["nombres"];
                    $fecha_biopsia_2 = explode("-", $paci["fec6"]);
                }
                    
                ?>

                <tr>
                    <td>
                        <label for="biopsia_realizada_por">Biopsia realizada por:</label>
                        <input type="text" id="biopsia_realizada_por" value="<?= mb_strtoupper($biopsia_realizada_por) ?>"/>
                    </td>
                    <td>
                        <label for="tubing_realizado_por">Tubing realizado por:</label>
                        <input type="text" id="tubing_realizado_por" value="<?= mb_strtoupper($tubing_realizado_por) ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="dia_fecha_biopsia">*Fecha biopsia: </label>
                        <input class="small-input" type="text" id="dia_fecha_biopsia" value="<?= $fecha_biopsia_1[2]?>"/> /
                        <input class="small-input" type="text" id="mes_fecha_biopsia" value="<?= $fecha_biopsia_1[1]?>"/> /
                        <input class="small-input" type="text" id="anio_fecha_biopsia" value="<?= $fecha_biopsia_1[0]?>"/>
                    </td>
                    <td>
                        <label for="lote_medio_washing_loading">Lote medio washing/ loading:</label>
                        <input type="text" id="lote_medio_washing_loading" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="biopsia_realizada_por">Biopsia realizada por:</label>
                        <input type="text" id="biopsia_realizada_por" value="<?= mb_strtoupper($biopsia_realizada_por_d6) ?>"/>
                    </td>
                    <td>
                        <label for="tubing_realizado_por">Tubing realizado por:</label>
                        <input type="text" id="tubing_realizado_por" value="<?= mb_strtoupper($tubing_realizado_por_d6) ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="dia_fecha_biopsia_1">*Fecha biopsia: </label>
                        <input class="small-input" type="text" id="dia_fecha_biopsia_1" value="<?= $fecha_biopsia_2[2]?>"/> /
                        <input class="small-input" type="text" id="mes_fecha_biopsia_1" value="<?= $fecha_biopsia_2[1]?>"/> /
                        <input class="small-input" type="text" id="anio_fecha_biopsia_1" value="<?= $fecha_biopsia_2[0]?>"/>
                    </td>
                    <td>
                        <label for="lote_medio_washing_loading">Lote medio washing/ loading:</label>
                        <input type="text" id="lote_medio_washing_loading" value="<?= mb_strtoupper($informe["lote_medio_d6"]) ?>"/>
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="2">
                        <span>ID Embrión</span>
                    </th>
                    <th colspan="2" rowspan="3">
                        <span>Clasificación morfológica del embrión</span>
                    </th>
                    <th colspan="4">
                        <span>Origen de la muestra</span>
                    </th>
                    <th colspan="2">
                        <span>Fecundación Nº pronúcleos y corpúsculos polares</span>
                    </th>
                    <th colspan="5">
                        <span>Día de biopsia</span>
                    </th>
                    <th>
                        <span>Núcleo</span>
                    </th>
                    <th>
                        <span>Tubing</span>
                    </th>
                    <th colspan="4" rowspan="3">
                        <span>Obs</span>
                    </th>
                </tr>
                <tr>
                    <th>
                        <span>Iniciales paciente</span>
                    </th>
                    <th>
                        <span>Nº Embrión</span>
                    </th>
                    <th>
                        <span>Ovo <br> fresco</span>
                    </th>
                    <th>
                        <span>Ovo <br> vitri</span>
                    </th>
                    <th>
                        <span>Embrión vitri</span>
                    </th>
                    <th>
                        <span>Blasto vitri</span>
                    </th>
                    <th>
                        <span>PN</span>
                    </th>
                    <th>
                        <span>PB</span>
                    </th>
                    <th>
                        <span>D3</span>
                    </th>
                    <th>
                        <span>D5</span>
                    </th>
                    <th>
                        <span>D6</span>
                    </th>
                    <th>
                        <span>Rebiopsia</span>
                    </th>
                    <th>
                        <span>Embrace previo</span>
                    </th>
                    <th>
                        <span>SI</span>
                    </th>
                    <th>
                        <span>OK</span>
                    </th>
                </tr>
            </thead>
            <tbody style="background-color: #f2f2f2;">
                <?php
                $total_rows = 20;
                $stmt = $db->prepare("SELECT a.ovo, a.d5cel celula, a.d5mci mci, a.d5tro tro, 5 dia, a.d1c_pol, a.d1pron, coalesce(c.nombre, '') observacion
                from lab_aspira_dias a
                inner join lab_aspira b on b.pro = a.pro and b.estado is true
                left join lab_aspira_dias_observacion_biopsia c on c.idrepro = b.rep and c.ovo = a.ovo and c.estado = 1
                where a.analizar = 1 and a.pro=? and a.d5cel <> '' and a.d5cel <> 'Bloq' and a.d5f_cic = 'C' and (a.d5d_bio<>0) and a.estado is true
                union
                select
                a.ovo, a.d6cel celula, a.d6mci mci, a.d6tro tro, 6 dia, a.d1c_pol, a.d1pron, coalesce(c.nombre, '') observacion
                from lab_aspira_dias a
                inner join lab_aspira b on b.pro = a.pro and b.estado is true
                left join lab_aspira_dias_observacion_biopsia c on c.idrepro = b.rep and c.ovo = a.ovo and c.estado = 1
                where a.analizar = 1 and a.pro=? and a.d6cel <> '' and a.d6cel <> 'Bloq' and a.d6f_cic = 'C' and (a.d6d_bio<>0) and a.estado is true");
                    $stmt->execute([$protocolo, $protocolo]);
                $n_rows = $stmt->rowCount();
                if ($n_rows > 0) {
                    $observaciones = '';
                    $index = 0;
                    $row = '<tr>
                            <td><input class="small-input" type="text" value=""></td>
                            <td><input class="small-input" type="text" value=""></td>
                            <td colspan="2"><input class="small-input" type="text" value=""></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td><input class="small-input" type="text" value=""></td>
                            <td><input class="small-input" type="text" value=""></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                            <td><input class="small-input" type="text" value=""></td>
                        </tr>';

                    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $ovo_fres = '';
                        $ovo_vitri = '';
                        $vitri_d2 = '';
                        $vitri_d3 = '';
                        $blasto_vitri = '';
                        $d3 = '';
                        $d5 = '';
                        $d6 = '';
                        $rebiopsia = '';
                        $nucleo_visible = '';
                        $tubing = '';

                        if (in_array($data['ovo'], $informe["muestras"]["ovo_fres"], true)) $ovo_fres = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["ovo_vitri"], true)) $ovo_vitri = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["vitri_d2"], true)) $vitri_d2 = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["vitri_d3"], true)) $vitri_d3 = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["blasto_vitri"], true)) $blasto_vitri = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["d3"], true)) $d3 = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["d5"], true)) $d5 = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["d6"], true)) $d6 = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["rebiopsia"], true)) $rebiopsia = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["nucleo_visible"], true)) $nucleo_visible = 'checked';
                        if (in_array($data['ovo'], $informe["muestras"]["tubing"], true)) $tubing = 'checked';

                        $observaciones = '<input type="text" name="observaciones' . $data['ovo'] . '" value="' . mb_strtoupper($data['observacion']) . '">';

                        echo '
                            <tr>
                                <td><input class="small-input" type="text" value="' . $paciente_iniciales . '"></td>
                                <td><input class="small-input" type="text" value="' . $data['ovo'] . '"></td>
                                <td colspan="2"><input class="small-input" type="text" value="' . mb_strtoupper($data['celula']) . " " . mb_strtoupper($data['mci']) . mb_strtoupper($data['tro']) . '"></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $ovo_fres . '></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $ovo_vitri . '></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $vitri_d3 . '></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $blasto_vitri . '></td>
                                <td><input class="small-input" type="text" value="' . $data['d1pron'] . '"></td>
                                <td><input class="small-input" type="text" value="' . $data['d1c_pol'] . '"></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $d3 . '></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $d5 . '></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $d6 . '></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $rebiopsia . '></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox"></td>
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $nucleo_visible . '></td>    
                                <td onclick="appear(event)"><input class="small-input" type="checkbox" ' . $tubing . '></td>
                                <td><input class="small-input" type="text" value="' . (count($informe["muestras"]["observaciones"]) != 0 ? $informe["muestras"]["observaciones"][$index] : '') . '"></td>
                            </tr>';

                        
                        $index++;
                    }

                    echo str_repeat($row, $total_rows - $n_rows);
                }
                ?>
            </tbody>
        </table>

        <footer>
            <span>
                (01) 267 0094 / 980 029 360
            </span>&nbsp;|&nbsp;
            <span>
                infolatam@igenomix.com
            </span>&nbsp;|&nbsp;
            <span>
                www.latam.igenomix.com
            </span>
        </footer>
    </div>
    <div class="printer-container" id="print-container">            
        <div class="dropdown-content" id="dropdown-content">
            <div class="btn">
                <a href="https://app.inmater.pe/lista_pro_f.php" target="_blank">
                    <i class="fa-solid fa-list"></i>
                </a>
            </div>
            <div class="btn">
                <a href="<?= "https://app.inmater.pe/le_aspi6.php?id=" . $protocolo?>">
                    <i class="fa-solid fa-calendar-day"></i>
                </a>
            </div>
            <?php if(existe_informe_ignomix($protocolo)) : ?>
                <div class="btn">
                    <a class="pdf_guardado" href="<?= existe_informe_ignomix($protocolo) ?>" target="_blank">
                        <i class="fa-regular fa-file-pdf"></i>
                    </a>
                </div>
            <?php endif; ?>
            <button class="btn" onclick="printSave(event)">
                <i class="icon fa-solid fa-floppy-disk"></i> 
            </button>
        </div>
        <div class="dropdown-icon btn" id="print">
            <i class="fa-solid fa-angle-up"></i>
        </div>

    </div>
    <div class="loader-container" id="spinner" style="display: none;">
        <span class="loader"></span>
    </div>
</body>
</html>