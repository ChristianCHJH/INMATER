<?php
require($_SERVER["DOCUMENT_ROOT"]."/_database/database.php");
global $db;
$id = $_GET['id'];

// consulta de datos de histeroscopias
$Rpop = $db->prepare("SELECT
    analisis_histeroscopia.*,man_procedimientos_cli.descripcion,
    (SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen1parr1) AS imagen1parr1,
    (SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen2parr1) AS imagen2parr1,
    (SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen3parr1) AS imagen3parr1,
    (SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen1parr2) AS imagen1parr2,
    (SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen2parr2) AS imagen2parr2,
    (SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen3parr2) AS imagen3parr2
    FROM analisis_histeroscopia inner join man_procedimientos_cli on analisis_histeroscopia.tipo_analisis=man_procedimientos_cli.id
    where analisis_histeroscopia.id = ?;"
);
$Rpop->execute(array($id));
$pop = $Rpop->fetch(PDO::FETCH_ASSOC);

$dia= date("j",strtotime($pop['fecha']));
$mes= date("n",strtotime($pop['fecha']));
$anio= date("Y",strtotime($pop['fecha']));
$hist_path = "/storage/analisis_archivo/histeroscopia/";

$arrayLargoMes = array(
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto',  9 => 'Septiembre',  10 => 'Octubre',  11 => 'Noviembre', 12 => 'Diciembre'
);

$arrayLargoDia = array(
    1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado',
    7 => 'Domingo'
);

$estilo = '<style>
    @page {
        margin-header: 0mm;
        margin-footer: 0mm;
        margin-left: 0cm;
        margin-right: 0cm;
        header: html_myHTMLHeader;
        footer: html_myHTMLFooter;
    }
    .fecha {
        width: 100%;
        text-align: right;
    }
    .titulo {
        font-size: 14px;
        font-weight: bold;
        text-align: center;
    }
    .imagenes-parrafo {
        text-align: center;
        margin: 10px;
    }
    .imagen-parrafo {
        margin: 10px;
    }
    .xxx {margin-left: 2.3cm; margin-right: 1.7cm;}
    .tabla table {border-collapse: collapse;}
    .tabla table, .tabla th, .tabla td {border: 1px solid #72a2aa;}
</style>';

$html = '<div class="fecha">Lima, ' . $arrayLargoDia[$dia] . ' ' . $dia . ' de ' . $arrayLargoMes[$mes] . ' del ' . $anio . "</div><br>";
$html .= '<div class="titulo">' . utf8_decode($pop['descripcion']) . "</div><br>";
$html .= $pop['a_parrafo1'] . "<br>";

$html .= "<div class='imagenes-parrafo'>";
if(file_exists($_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen1parr1']) != '' && isset($pop['imagen1parr1'])) {
    $html .= '<img class="imagen-parrafo" src="'.$_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen1parr1'].'" width="250px" height="220px">';
}
if(file_exists($_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen2parr1']) != '' && isset($pop['imagen2parr1'])) {
    $html .= '<img class="imagen-parrafo" src="'.$_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen2parr1'].'" width="250px" height="220px">';
}
if(file_exists($_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen3parr1']) != '' && isset($pop['imagen3parr1'])) {
    $html .= '<img class="imagen-parrafo" src="'.$_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen3parr1'].'" width="250px" height="220px">';
}
$html .= "</div>";

$html .= $pop['a_parrafo2'] . "<br>";

$html .= "<div class='imagenes-parrafo'>";
if(file_exists($_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen1parr2']) != '' && isset($pop['imagen1parr2'])) {
    $html .= '<img class="imagen-parrafo" src="'.$_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen1parr2'].'" width="250px" height="220px">';
}
if(file_exists($_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen2parr2']) != '' && isset($pop['imagen2parr2'])) {
    $html .= '<img class="imagen-parrafo" src="'.$_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen2parr2'].'" width="250px" height="220px">';
}
if(file_exists($_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen3parr3']) != '' && isset($pop['imagen3parr3'])) {
    $html .= '<img class="imagen-parrafo" src="'.$_SERVER["DOCUMENT_ROOT"].$hist_path.$pop['imagen3parr3'].'" width="250px" height="220px">';
}
$html .= "</div>";

if (isset($pop['idx']) and !empty($pop['idx'])) {
    $html .= 'IDX: ' . $pop['idx'] . "<br>";
}
if (isset($pop['comentario']) and !empty($pop['comentario'])) {
    $html .= 'Comentarios: ' . $pop['comentario'] . "<br>";
}

$head_foot = '<!--mpdf
<htmlpageheader name="myHTMLHeader"><img src="../_images/info_head.jpg" width="100%"></htmlpageheader>
<htmlpagefooter name="myHTMLFooter"><img src="../_images/info_foot.jpg" width="100%"></htmlpagefooter>
mpdf-->';

require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
require_once __DIR__ . '/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular"]);

$mpdf->WriteHTML($estilo . '<body><div class="xxx">' . $head_foot . $html . '</div></body>');
$mpdf->Output();
/* print($estilo . '<body><div class="xxx">' . $head_foot . $html . '</div></body>'); */
?>