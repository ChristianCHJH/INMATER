<?php

namespace App\Helpers;

class RouteSelector
{
    public static function selectRoute($data)
    {
        $route = '';

        switch ($data["role"]) {
            case '2':
                $route = 'lista.php';
                break;
            case '3':
                $route = 'lista_facturacion.php';
                break;
            case '4': // analisis clinico
                switch ($data["subrole_id"]) {
                    case "2": $route = 'lista_genomics.php'; break;
                    case "4": $route = 'lista_ecografia.php'; break;
                    case "5": $route = 'lista_histeroscopias.php'; break;
                    default: $route = 'lista.php'; break;
                }
                break;
            case '6': $route = 'lista_consulta.php'; break;
            case '9': $route = 'lista_adminlab.php'; break;
            case '10': $route = 'lista_facturacion.php'; break;
            case '16': $route = 'lista-admin.php'; break;
            case '17': $route = 'lista_sistemas.php'; break;
            case '19': $route = 'lista_facturacion.php'; break;
            case '20': $route = 'lista_facturacion.php'; break;
            case '21': $route = 'auditoria-facturacion.php'; break;
            case '22': $route = 'lista-transferencias.php'; break;
            case '23': $route = 'lista-marketing.php'; break;
            default: $route = 'lista.php'; break;
        }

        return $route;
    }
     
}
