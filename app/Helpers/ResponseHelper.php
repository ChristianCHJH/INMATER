<?php 
namespace App\Helpers;

class ResponseHelper
{
    public static function json($data, $status = 200)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        http_response_code($status);
        exit;
    }
}