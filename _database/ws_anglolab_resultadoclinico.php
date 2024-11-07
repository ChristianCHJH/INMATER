<?php
    //
    if (isset($_GET["id"]) && !empty($_GET["id"]) && isset($_GET["tip"]) && !empty($_GET["tip"])) {
        $id = $_GET["id"];
        $tip = $_GET["tip"];
    } else {
        print("No se enviaron datos."); exit();
    }
    // include('../nusoap/lib/nusoap.php');
    include('nusoap-master/src/nusoap.php');
    $client = new nusoap_client('http://www.anglolab.com:287/Service.svc?wsdl', 'wsdl');
    $client->soap_defencoding = 'UTF-8';
    $err = $client->getError();

    if ($err) {
        echo '<h4>Error conexion anglolab:</h4><pre>' . $err . '</pre>';
    }

    $param = array('dato' => $id."-".$tip);

    $result = $client->call('Consulta_Resultado_Laboratorio_Inmater', $param);
    var_dump($result); exit();
    // Check for a fault
    if ($client->fault) {
        echo '<h4>FALLO 1 anglolab:</h4><pre>';
        print_r($result);
        echo '</pre>';
    } else {
        // Check for errors
        $err = $client->getError();
        var_dump($err); exit();
        if ($err) {
            // Display the error
            echo '<h2>FALLO 2 anglolab:</h2><pre>' . $err . '</pre>';
        } else { // Display the result
        	//$result['Consulta_Resultado_Laboratorio_InmaterResult']['diffgram']
            $tablas = $result['Consulta_Resultado_Laboratorio_InmaterResult']['diffgram']['NewDataSet']['Table'];
            //var_dump($tablas); exit();
            foreach ($tablas as $k => $v) {
                echo $v['ordencliente']."-".$k;
                try {
                	/*
                    $stmt = $db->prepare("INSERT INTO lab_anglo (id,ordencliente,ID_Sucursal,Orden,IDExterno,ProfesionalNombre,ID_Ninterno,Paciente,id_tipdoc,numdoc,FecNac,Sexo,Ubicacion,Edad,Orden1,variabledescripcion,Resultado,Unidad,RangoMinimo,RangoMaximo,Rango,ID_Estudio,TipoTiempo,CantidadTiempo,VariableCapacidad,ObservacionFirma,observacion,ID_CodigoUsuarioValidacion,ID_Sector,CMI,Microorganismo,ObsResultado,SerieFuncional,SerieHistorico,SerieHistograma,Antibiotico,Letra,fechavalidacion,Obs_Rango,EstudioDesc,Microbiologia,SectorDescripcion,Confidencial,Alias,Metodo,Fe_Resultado,Posicion,Medico,UbicacionP,Firma,IMPRESION,Validador,Colegio,CODIGOEXTERNO,Area,Cursiva,PERFIL,PERFIL_CODIGO,TIPO1,ESTADO,UbicHistCli) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $stmt->execute(array($k, $v['ordencliente'], $v['ID_Sucursal'], $v['orden'], $v['IDExterno'], $v['ProfesionalNombre'], $v['ID_Ninterno'], $v['Paciente'], $v['id_tipdoc'], $v['numdoc'], $v['FecNac'], $v['Sexo'], $v['Ubicacion'], $v['Edad'], $v['Orden1'], $v['variabledescripcion'], $v['resultado'], $v['unidad'], $v['RangoMinimo'], $v['RangoMaximo'], $v['Rango'], $v['ID_Estudio'], $v['TipoTiempo'], $v['CantidadTiempo'], $v['VariableCapacidad'], $v['ObservacionFirma'], $v['observacion'], $v['ID_CodigoUsuarioValidacion'], $v['ID_Sector'], $v['CMI'], $v['Microorganismo'], $v['ObsResultado'], $v['SerieFuncional'], $v['SerieHistorico'], $v['SerieHistograma'], $v['Antibiotico'], $v['Letra'], $v['fechavalidacion'], $v['Obs_Rango'], $v['EstudioDesc'], $v['Microbiologia'], $v['SectorDescripcion'], $v['Confidencial'], $v['Alias'], $v['Metodo'], $v['Fe_Resultado'], $v['Posicion'], $v['Medico'], $v['UbicacionP'], $v['Firma'], $v['IMPRESION'], $v['Validador'], $v['Colegio'], $v['CODIGOEXTERNO'], $v['Area'], $v['Cursiva'], $v['PERFIL'], $v['PERFIL_CODIGO'], $v['TIPO1'], $v['ESTADO'], $v['UbicHistCli']));
                    */

                } catch (PDOException $e) {
                }

            }
            /*
            $stmt = $db->prepare("UPDATE recibos SET anglo='ok',updatex=? WHERE id=? AND tip=?");
            $hora_actual = date("Y-m-d H:i:s");
            $stmt->execute(array($hora_actual,$pago['id'], $pago['tip']));
            */
        }
    }
?>	