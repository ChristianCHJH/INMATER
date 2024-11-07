<?php
/**************** PACIENTE *******************/
namespace App\Http\Controllers\MedicalRecords;
use App\Http\Controllers\Controller;
use App\Models\MedicalRecords\ClientType; 
use App\Models\MedicalRecords\Paciente;  
use App\Models\Ubigeo\Country;
use App\Models\Medico\MedicalAdvisor;
use App\Models\Medico\AttendingPhysician;
use App\Models\Location;
use App\Models\People\ReferenceMedium;
use Jenssegers\Blade\Blade;
use App\Core\Request; 
use App\Http\Requests\PacienteRequest;
use App\Helpers\ResponseHelper; 
class PatientController extends Controller
{
    protected $blade;
    protected $apiService;

    public function __construct(Blade $blade)
    {
        $this->blade = $blade;
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); 
        } 
    }
    
    public function index(){}
    public function show(Request $request){}

    public function create()
    {  
        $Key = env('SUNAT_API_KEY'); 
        $clientTypes = ClientType::where('eliminado', 0)->get();
        $countries = Country::all(); 
        $locations = Location::where('estado', 1)->orderBy('nombre')->get();
        $medicalAdvisors = MedicalAdvisor::where('eliminado', 0)->orderBy('nombre')->get();
        $referenceMediums = ReferenceMedium::where('eliminado', 0)->orderBy('nombre')->get();
        $attendingPhysician = AttendingPhysician::where('estado', 1)->orderBy('nombre')->get();
        echo $this->blade->make('medicalRecords.paciente.create', compact('Key','attendingPhysician','clientTypes', 'countries', 'locations', 'medicalAdvisors','referenceMediums'))->render(); 
        exit;
    }
 
    public function store()
    { 
        $request = new PacienteRequest();
        $response['status'] = false; 
        $validatemsj = [];

        try { 
            $validatedData = $request->validate($validatemsj); 
            $medTratante = $validatedData['m_tratante'];
            $medico = AttendingPhysician::findOrFail($medTratante); 
            print_r($_FILES); exit;
            $result = Paciente::createPaciente($validatedData, $_FILES['foto'], $_SESSION['login'], $medico->codigo);
            
            if ($result['status'] == false) {
                throw new \Exception($result['message']); 
            }

            $response['message'] ='Paciente registrado correctamente';
            $response['status'] = true;
        } catch (\Exception $e) { 
            $response['message'] = $e->getMessage(); 
        }

        return ResponseHelper::json([
            'success' => $response['status'],
            'message' => $response['message'],
            'html' => $validatemsj
        ]); 
    }

    public function edit(Request $request)
    {
         
    }

    public function update($req)
    {
        
    } 
}