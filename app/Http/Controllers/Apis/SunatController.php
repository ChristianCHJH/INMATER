<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Usuario\UserLog;
use App\Helpers\ResponseHelper;
use App\Services\ApiSunatService;
use App\Core\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException; 

class SunatController extends Controller {

    public function apisunat() {    
        echo $this->blade->make('users.apisunat')->render();
        exit;
    }
    public function validarPersona($tipo = null, $numero = null)
    {
        //print_r($request); exit;
        $url = env('API_SUNAT_EVA');

        $client = new Client();
 
        $data = [
            "documento" => $numero,
            "tipo_documento" => $tipo
        ];

        $token = env('JWT_PUBLIC_EVA'); 

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $data
            ]);

            $responseBody = json_decode($response->getBody(), true);

            return ResponseHelper::json($responseBody);

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                $errorBody = json_decode($errorResponse->getBody(), true);
                return ResponseHelper::json($errorBody, $errorResponse->getStatusCode());
            } else {
                return ResponseHelper::json(['error' => 'Error en la solicitud'], 500);
            }
        }
    }

    public function showDocumentData($tipo,$numero) {
        //showDocumentData
        
        $tipo = strtolower($tipo); 

        $response = [
            'status' => false,
            'data' => [],
            'message' => ''
        ];

        try {
            // Mapeo de abreviaturas a tipos de documentos
            $tipoMap = [
                'dni' => 'dni',
                'passport' => 'passport',
                'pas' => 'passport',
                'pass' => 'passport',
                'card' => 'card',
                'cex' => 'card',
                'ruc' => 'ruc'
            ];

            // Validar si el tipo de documento es válido
            if (!array_key_exists($tipo, $tipoMap)) {
                throw new \Exception('Invalid document type');
            }

            $data = [];

            switch ($tipoMap[$tipo]) {
                case 'dni':
                    $data = $this->showDniData($numero);
                    break;
                case 'passport':
                    $data = $this->showPassportData($numero);
                    break;
                case 'card':
                    $data = $this->getResidencyCard($numero);
                    break;
                case 'ruc':
                    $data = $this->showRucData($numero);
                    break;
                default:
                    throw new \Exception('Invalid document type');
            }

            $response['data'] = $data;
            $response['status'] = true;
            $response['message'] = 'success';
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
            return ResponseHelper::json($response, 400);
        }

        return ResponseHelper::json($response);
    }

    /**
     * Muestra los datos del DNI consultados desde la API.
     * @param Request $request La solicitud que contiene el número de DNI a consultar.
     */
    public function showDniData($numero) {
        $data2['status'] = false;
        $request = new Request();

        try { 

            $user = UserLog::create([
                'nombre_modulo' => 'Api sunat',
                'ruta' => $request->getRequestUri(),
                'tipo_operacion' => 'consulta dni sunat',
                'clave' => 'DNI',
                'valor' => $numero,
                'idusercreate' => isset($_SESSION['login']) ? $_SESSION['login'] : null,
                'createdate' => date('Y-m-d')
            ]);

            $apiService = new ApiSunatService();
            $data2 = $apiService->getDni($numero);
            $data2['status'] = true;
        } catch (\Exception $e) {
            $data2['message'] = $e->getMessage();
        }

        return ResponseHelper::json($data2);
    }

    /**
     * Muestra los datos del pasaporte consultados desde la API.
     * @param Request $request La solicitud que contiene el número de pasaporte a consultar.
     */
    public function showPassportData($numero) {
        $data2 = ['status' => false];
        $request = new Request();
        try { 
            $user = UserLog::create([
                'nombre_modulo' => 'Api sunat',
                'ruta' => $request->getRequestUri(),
                'tipo_operacion' => 'consulta pasaporte sunat',
                'clave' => 'PAS',
                'valor' => $numero,
                'idusercreate' => isset($_SESSION['login']) ? $_SESSION['login'] : null,
                'createdate' => date('Y-m-d')
            ]);

            $apiService = new ApiSunatService();
            $data2 = $apiService->getPassport($numero);
            $data2['status'] = true;
        } catch (\Exception $e) {
            $data2['message'] = $e->getMessage();
        }

        return ResponseHelper::json($data2);
    }

    /**
     * Muestra los datos del Carnet de Extranjería consultados desde la API.
     * @param Request $request La solicitud que contiene el número del Carnet de Extranjería a consultar.
     */
    public function getResidencyCard($numero) {
        $data2 = ['status' => false];
        $request = new Request();

        try { 
            $user = UserLog::create([
                'nombre_modulo' => 'Api sunat',
                'ruta' => $request->getRequestUri(),
                'tipo_operacion' => 'consulta tarjeta de residencia sunat',
                'clave' => 'CE',
                'valor' => $numero,
                'idusercreate' => isset($_SESSION['login']) ? $_SESSION['login'] : null,
                'createdate' => date('Y-m-d')
            ]);

            $apiService = new ApiSunatService();
            $data2 = $apiService->getResidencyCard($numero);
            $data2['status'] = true;
        } catch (\Exception $e) {
            $data2['message'] = $e->getMessage();
        }

        return ResponseHelper::json($data2);
    }

    /**
     * Muestra los datos del RUC consultados desde la API.
     * @param Request $request La solicitud que contiene el número de RUC a consultar.
     */
    public function showRucData($numero) {
        $data2 = ['status' => false];
        $request = new Request();

        try { 
            $user = UserLog::create([
                'nombre_modulo' => 'Api sunat',
                'ruta' => $request->getRequestUri(),
                'tipo_operacion' => 'consulta RUC sunat',
                'clave' => 'RUC',
                'valor' => $numero,
                'idusercreate' => isset($_SESSION['login']) ? $_SESSION['login'] : null,
                'createdate' => date('Y-m-d')
            ]);

            $apiService = new ApiSunatService();
            $data2 = $apiService->getRuc($numero);
            $data2['status'] = true;
        } catch (\Exception $e) {
            $data2['message'] = $e->getMessage();
        }

        return ResponseHelper::json($data2);
    }
}
