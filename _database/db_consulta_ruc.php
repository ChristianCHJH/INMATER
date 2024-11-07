<?php
    if (isset($_POST['ruc']) && !empty($_POST['ruc'])) {
        require_once("../_libraries/consulta_ruc/autoload.php");
        
        $company = new \Sunat\Sunat( true, true );
        $ruc = $_POST['ruc'];
        $search = $company->search( $ruc );
        echo $search->json();
    }	
?>