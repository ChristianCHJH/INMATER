<?php
     $url = "http://www.anglolab.com:287/Service.svc?wsdl";
     // URL contains http://jobs.github.com/positions.json?search=Project Manager&location=London
     $json = file_get_contents($url);
     // var_dump($json);
     $results = json_decode($json, TRUE);
     var_dump($results);
     exit;	
?>