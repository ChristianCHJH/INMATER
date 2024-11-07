<?php
    include('../nusoap/lib/nusoap.php');
    // include('nusoap-master/src/nusoap.php');
    require("database.php");
    //
    $idenvio = $idconsulta = 0;
    global $db;
    $anglo = '';
    $cadena = "777091";
    //
    if (isset($_GET["idenvio"]) && !empty($_GET["idenvio"]) && isset($_GET["idconsulta"]) && !empty($_GET["idconsulta"])) {
        $idenvio = $_GET["idenvio"];
        $idconsulta = $_GET["idconsulta"];
    } else {
        print("No se enviaron datos."); exit();
    }
    //
    $consulta = $db->prepare("SELECT dni dnipaciente, med medico, tip tipodocumento, fec fechaenvio from recibos where id=?");
    $consulta->execute(array($idconsulta));
    if ($consulta->rowCount() == 1) {
        $dato = $consulta->fetch(PDO::FETCH_ASSOC);
        // var_dump($dato);
    } else {
        print("No existe informacion."); exit();
    }
    //consulta datos paciente
    $consulta = $db->prepare("SELECT tip,nom,ape,fnac FROM hc_paciente WHERE dni=?");
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
    //soapclient test - ini
    //Create the client object
    $soapclient = new SoapClient('http://www.anglolab.com:287/Service.svc?wsdl');
    var_dump($soapclient); exit();

    //Use the functions of the client, the params of the function are in 
    //the associative array
    // $params = array('CountryName' => 'Spain', 'CityName' => 'Alicante');
    // $response = $soapclient->getWeather($param);
    // var_dump($response);

    // Get the Cities By Country
    // $param = array('CountryName' => 'Spain');
    var_dump($soapclient->__getFunctions());
    var_dump($soapclient->__getTypes()); 
    // $response = $soapclient->Registrar_Orden_HIS_Inmater($param);

    // var_dump($response);
    exit();
    //soapclient test - fin
?>