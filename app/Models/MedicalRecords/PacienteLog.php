<?php
namespace App\Models\MedicalRecords;
use Illuminate\Database\Eloquent\Model;

class PacienteLog extends Model
{
    // Relaciones y otros métodos 
    protected $fillable = ['dni', 'pass', 'sta', 'med', 'tip', 'nom', 'ape', 'fnac', 'tcel',
    'tcas', 'tofi', 'mai', 'dir', 'nac', 'depa', 'prov', 'dist', 'prof',
    'san', 'don', 'raz', 'talla', 'peso', 'rem', 'nota', 'fec', 'idsedes',
    'idusercreate', 'createdate', 
    'action'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_LOG .'.'.'hc_paciente';

    public static function createPacienteLog($validatedData,$paciente_data, $userId, $form)
    {
         
        try {
            $allowed_fields = [
                'dni', 'pass', 'sta', 'med', 'tip', 'nom', 'ape', 'fnac', 'tcel',
                'tcas', 'tofi', 'mai', 'dir', 'nac', 'depa', 'prov', 'dist', 'prof',
                'san', 'don', 'raz', 'talla', 'peso', 'rem', 'nota', 'fec', 'idsedes',
                'idusercreate', 'createdate', 'action', 'sunat_doc_type', 'sunat_doc_number', 
                'sunat_doc_datetime', 'sunat_count', 'sunat_cache_count'
            ];
            
            $query_counts = json_decode($validatedData['query-counts']);

            if (isset($query_counts->$userId->$form)) {
                $form_data = $query_counts->$userId->$form;
            
                // Obtener los datos del primer tipo y número disponible
                $tipo = reset($form_data); // Obtiene el primer tipo
                $numero = reset($tipo); // Obtiene el primer número
            
                if (isset($numero->ajax)) {  
                    $paciente_data['sunat_doc_type'] = $tipo; // last_doc_type
                    $paciente_data['sunat_doc_number'] = $numero;   // last_doc_number
                    $paciente_data['sunat_doc_datetime'] = date('Y-m-d H:i:s'); // current datetime
                    $paciente_data['sunat_count'] = $numero->ajax->success ?? 0; // ajax_count 
                }
                if (isset($numero->cache)) {
                    $paciente_data['sunat_cache_count'] = $numero->cache->success ?? 0;   // cache_count
                }
            }

            $paciente_data['action'] = 'I';
            // Filtrar los campos de $paciente_data para que solo contenga los campos permitidos
            $paciente_data = array_intersect_key($paciente_data, array_flip($allowed_fields));

            // Log de paciente
            $paciente_log = new PacienteLog($paciente_data);
            $paciente_log->save();
 
            return ['status' => true, 'message' => 'Paciente registrado exitosamente'];
        } catch (\Exception $e) { 
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
