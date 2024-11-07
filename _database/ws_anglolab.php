<?php
    include('nusoap-master/src/nusoap.php');
    require("database.php");
    //
    $tipconsulta = $idconsulta = 0;
    global $db;
    $anglo = '';
    $cadena = "330230";

    //
    if (isset($_GET["tipconsulta"]) && !empty($_GET["tipconsulta"]) && isset($_GET["idconsulta"]) && !empty($_GET["idconsulta"])) {
        // $idenvio = $_GET["idenvio"];
        $tipconsulta = $_GET["tipconsulta"];
        $idconsulta = $_GET["idconsulta"];
    } else {
        print("No se enviaron datos."); exit();
    }

    // datos del comprobante
    $consulta = $db->prepare("SELECT
        dni dnipaciente, med medico, tip tipodocumento, fec fechaenvio
    from recibos
    where id=? and tip=?");
    $consulta->execute(array($idconsulta, $tipconsulta));
    if ($consulta->rowCount() == 1) {
        $dato = $consulta->fetch(PDO::FETCH_ASSOC);
    } else {
        print("No existe informacion."); exit();
    }

    //consulta datos paciente
    $consulta = $db->prepare("SELECT tip, nom, ape, fnac FROM hc_paciente WHERE dni=?");
    $consulta->execute(array($dato["dnipaciente"]));
    if ($consulta->rowCount() == 1) {
        $sexo = 'F';
    } else {
        $sexo = 'M';
        $consulta = $db->prepare("SELECT p_tip AS tip,p_nom AS nom,p_ape AS ape,p_fnac AS fnac FROM hc_pareja WHERE p_dni=?");
        $consulta->execute(array($dni));
    }
    $paci = $consulta->fetch(PDO::FETCH_ASSOC);
    $ape = explode(' ', $paci['ape'], 2);
    $apepaterno = $apematerno = "";
    if (isset($ape[0]) && !empty($ape[0])) {
        $apepaterno=$ape[0];
    }
    if (isset($ape[1]) && !empty($ape[1])) {
        $apematerno=$ape[1];
    }
    //consulta datos medico
    $medico = explode(' ', $dato["medico"], 2);
    $medpaterno = $medmaterno = "";
    if (isset($medico[0]) && !empty($medico[0])) {
        $medpaterno=$medico[0];
    }
    if (isset($medico[1]) && !empty($medico[1])) {
        $medmaterno=$medico[1];
    }
    //
    $client = new nusoap_client('http://www.anglolab.com:287/Service.svc?wsdl', true);
    $client->soap_defencoding = 'UTF-8';
    $client->decode_utf8 = FALSE;
    $err = $client->getError();
    if ($err) {
        $anglo = 4;
    }
    // var_dump($medico);
    $param = array('Dato' =>
        "|" . $idenvio . "-" . $dato["tipodocumento"] .
        "|" . $idenvio . "-" . $dato["tipodocumento"] .
        "|" . date("d/m/Y", strtotime($dato["fechaenvio"])) . "|S00036|CC0151|PT2290||||AMBULATORIO|NORMAL||R|" . $dato["dnipaciente"] .
        "|" . $paci['tip'].
        "|" . $dato["dnipaciente"] .
        "|" . $paci['nom'] .
        "|" . $apepaterno .
        "|" . $apematerno .
        "|" . $sexo .
        "|" . date("d/m/Y", strtotime($paci['fnac'])) . "|20544478096|INMATER||" . $medpaterno .
        "|" . $medmaterno . "||" . $cadena .
        "|"
    );
    // var_dump($param); exit();
    $result = $client->call('Registrar_Orden_HIS_Inmater', $param);
    // var_dump($client);
    // Check for a fault
    if ($client->fault) {
        //echo '<h2>FALLO:</h2><pre>';
        $anglo = 3;
        //print_r($result);
        $err = $result['Registrar_Orden_HIS_Inmater'];
        print("demo0");
        print($err);
        //echo '</pre>';
    } else {
        // Check for errors
        $err = $client->getError();
        if ($err) {
            // Display the error
            //echo '<h2>ERROR:</h2><pre>' . $err . '</pre>';
            print("demo1");
            print($err);
            $anglo = 2;
        } else {
            // Display the result
            //echo '<h2>RESULTADO:</h2><pre>' . $result['Registrar_Orden_HIS_ProvidenciaResult'] . '</pre>';
            $anglo = $result['Registrar_Orden_HIS_InmaterResult'];
            print("demo2");
            print($anglo);
            //echo "|".$id."-".$tip."|".$id."-".$tip."|".date("d/m/Y", strtotime($fec))."|S00036|CC0151|PT2290||||AMBULATORIO|NORMAL||R|".$dni."|PAS|".$dni."|".$paci['nom']."|".$ape[0]."|".$ape[1]."|".$sex."|".date("d/m/Y", strtotime($paci['fnac']))."|20544478096|INMATER|||||".$cadena."|";
        }
    }
    echo '<h2>'.$param['Dato'].'</h2>';
    print_r($result);
    // exit();
    if (strpos($anglo, "Correcto") === false) {
        $anglo = $anglo . '=' . $err;
        // print($anglo); exit();
        print('<script type="text/javascript">alert("ERROR al enviar solicitud a Anglolab: '.$anglo.'");</script>');
    }

?>