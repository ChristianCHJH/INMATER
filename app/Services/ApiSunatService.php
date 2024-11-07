<?php

namespace App\Services;
use Illuminate\Support\Env;
class ApiSunatService
{
    private $apiKey;
    private $url;

    public function __construct()
    {
        $this->url = 'https://api.json-pe.com/';
        $this->apiKey = env('SUNAT_API_KEY');  
    }

    private function makeRequest($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    public function getDni($dni)
    {
        $response['status'] = false;
        try {
                $url = $this->url.'dni/' . $dni . '?apikey=' . $this->apiKey;
                $response['data'] = $this->makeRequest($url);
                $response['status'] = true;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage(); 
        }
        
        return $response;
    }

    public function getPassport($passport)
    {
        $response['status'] = false;
        try {
                $url = $this->url.'pas/' . $passport . '?apikey=' . $this->apiKey;
                $response['data'] = $this->makeRequest($url);
                $response['status'] = true;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage(); 
        }
        
        return $response; 
    }

    public function getResidencyCard($card)
    {
        $response['status'] = false;
        try {
                $url = $this->url.'ce/' . $card . '?apikey=' . $this->apiKey;
                $response['data'] = $this->makeRequest($url);
                $response['status'] = true;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage(); 
        }
        
        return $response;  
    }

    public function getRuc($ruc)
    {
        $response['status'] = false;
        try {
                $url = $this->url.'ruc/' . $ruc . '?apikey=' . $this->apiKey;
                $response['data'] = $this->makeRequest($url);
                $response['status'] = true;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage(); 
        }
        
        return $response;   
    }
}
