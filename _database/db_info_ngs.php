<?php
    date_default_timezone_set('America/Lima');
    require("_database/db_comunes.php");
    require("_database/db_mantenimiento.php");
    require("database.php");

    if (isset($_GET['repro']) and !empty($_GET['repro'])) {
        $repro = $_GET['repro'];
    } else {
        print("La reproducción no existe"); exit();
    }

    $datarepro = consultaDatosReproduccion($repro);
    if (empty($datarepro)) { print("Datos de Reproducción no encontrado"); exit();}

    $datadesarrollo = consultaDesarrolloEmbrionario($repro);
    if (empty($datadesarrollo)) { print("Datos de Desarrollo Embrionaro no encontrado"); exit(); }

    $datapaciente = consultaDatosPaciente("DNI", $datarepro['numerodocumentopaciente']);
    if (empty($datapaciente)) { print("Datos de Paciente no encontrado"); exit();}

    $datapareja = consultaDatosPareja("DNI", $datarepro['numerodocumentopareja']);

    $datamedico = consultaDatosMedico($datarepro['codigomedico']);
    if (empty($datamedico)) { print("Datos de Medico no encontrado"); exit(); }

    $dataembriologodia5 = consultaDatosEmbriologo($datadesarrollo['embriologodia5']);
    $dataembriologodia6 = consultaDatosEmbriologo($datadesarrollo['embriologodia6']);

    $datatestigo = testigoBiopsiaListar();
    $dataprueba = pruebaBiopsiaListar();

    function inicialesNombres($nombres)
    {
        $iniciales='';
        $total=0;
        $porciones=explode(" ", trim($nombres));
        $total=count($porciones);

        if ($total > 0) {
            for ($i=0; $i < $total; $i++) { 
                $iniciales.=strtoupper(substr($porciones[$i], 0, 1));
            }
        }

        return $iniciales;
    }

    function metodoFecundacion($data)
    {
        $nombrereproduccion="-";

        if (isset($data) and !empty($data)) {
            if (!empty($data["fiv"]) and $data["fiv"] == 1) {
                $nombrereproduccion = "FIV";
            } else {
                $nombrereproduccion = "ICSI / PIEZO - ICSI";
            }
        }
        
        return $nombrereproduccion;
    }

    function detalleBiopsia($repro, $nombres, $validaObservaciones=false)
    {
        global $db;
        $html='';

        $stmt = $db->prepare("
        select
        a.ovo, a.d5cel celula, a.d5mci mci, a.d5tro tro, 5 dia, coalesce(c.nombre, '') observacion
        from lab_aspira_dias a
        inner join lab_aspira b on b.pro = a.pro and b.estado is true
        left join lab_aspira_dias_observacion_biopsia c on c.idrepro = b.rep and c.ovo = a.ovo and c.estado = 1 
        where a.pro=? and a.d5cel <> '' and a.d5cel <> 'Bloq' and a.d5f_cic = 'C' and a.estado is true
        union
        select
        a.ovo, a.d6cel celula, a.d6mci mci, a.d6tro tro, 6 dia, coalesce(c.nombre, '') observacion
        from lab_aspira_dias a
        inner join lab_aspira b on b.pro = a.pro and b.estado is true
        left join lab_aspira_dias_observacion_biopsia c on c.idrepro = b.rep and c.ovo = a.ovo and c.estado = 1 
        where a.pro=? and a.d6cel <> '' and a.d6cel <> 'Bloq' and a.d6f_cic = 'C' and a.estado is true");
        $stmt->execute(array($repro, $repro));
        if ($stmt->rowCount() > 0)
        {
            $observaciones='';
            $data = null;
            $i=0;
            $ovos='';
            // $data = $stmt->fetch(PDO::FETCH_ASSOC);
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $ovos.=$data['ovo'].'|';
                $observaciones=mb_strtoupper($data['observacion']);
                if ($validaObservaciones) {
                    $observaciones='<input type="text" name="observaciones'.$data['ovo'].'" value="'.mb_strtoupper($data['observacion']).'">';
                }

                $html.='
                <tr>
                    <td align="center">'.inicialesNombres($nombres).$data['ovo'].'</td>
                    <td align="center">'.mb_strtoupper($data['celula']).mb_strtoupper($data['mci']).mb_strtoupper($data['tro']).'</td>
                    <td align="center">'.mb_strtoupper($data['dia']).'</td>
                    <td>'.$observaciones.'</td>
                </tr>';
            }
            $html.='
            <input type="hidden" name="novos" value="'.$stmt->rowCount().'">
            <input type="hidden" name="ovos" value="'.$ovos.'">';
        }

        return $html;
    }

    function testigoBiopsiaDesarrolloListar($idrepro, $dia)
    {
        global $db;
        $data = null;

        $stmt = $db->prepare("
        select a.idtestigobiopsia id, b.nombre
        from lab_aspira_testigo_biopsia a
        left join labo_testigo_biopsia b on b.id = a.idtestigobiopsia
        where a.estado=1 and a.idrepro=? and a.dia=?");
        $stmt->execute(array($idrepro, $dia));
        if ($stmt->rowCount() > 0)
        {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    function pruebaBiopsiaDesarrolloListar($idrepro)
    {
        global $db;
        $data = [];

        $stmt = $db->prepare("
        select
        a.idpruebabiopsia id, b.nombre, a.correlativo, coalesce(a.observacion, '') observacion
        from lab_aspira_prueba_biopsia a
        left join labo_prueba_biopsia b on b.id = a.idpruebabiopsia
        where a.estado=1 and a.idrepro=?");
        $stmt->execute(array($idrepro));
        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $data;
    }

    function donacionOvulos($tipoReproduccion, $tipoPaciente)
    {
        $respuesta="NO";

        if ($tipoReproduccion == "R" || $tipoPaciente == 'D') {
            $respuesta="SI";
        }

        return $respuesta;
    }

    function donacionEspermatozoides($repro)
    {
        global $db;
        $respuesta="NO";

        $stmt = $db->prepare("select id from lab_andro_cap where mue in (2, 4) and pro=? and eliminado is false");
        $stmt->execute(array($repro));

        if ($stmt->rowCount() > 0)
        {
            $respuesta="SI";
        }
        return $respuesta;
    }
?>