<?php
namespace App\Models\MedicalRecords;
use Illuminate\Database\Eloquent\Model;
use App\Core\DB;
use App\Helpers\Storage;

class Paciente extends Model
{
    // Relaciones y otros métodos 
    protected $fillable = ['dni', 'pass', 'sta', 'med', 'tip', 'nom', 'ape', 'fnac', 'tcel',
    'tcas', 'tofi', 'mai', 'dir', 'nac', 'depa', 'prov', 'dist', 'prof',
    'san', 'don', 'raz', 'talla', 'peso', 'rem', 'nota', 'fec', 'idsedes',
    'idusercreate', 'createdate', 'action'];

    public $timestamps = false;
    protected $primaryKey = 'dni';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'hc_paciente';

    public static function createPaciente($validatedData, $file, $userId, $medTratante)
    {
        DB::beginTransaction();

        try { 
            $base64_foto = '';
            //print_r($file); exit;
            $foto_tmp_path = $file['tmp_name'];
            echo  $foto_tmp_path; exit;
            if ($foto_tmp_path) { 
                if (!empty($foto_tmp_path) && file_exists($foto_tmp_path)) { 
                    $foto_path = 'paci/' . $validatedData['dni'] . '/';
                    $foto_content = file_get_contents($foto_tmp_path);
                    Storage::storeAs($foto_path, 'foto.jpg', $foto_content,true);
                    $base64_foto = base64_encode(Storage::get($foto_path.'foto.jpg'));
                } else {
                    throw new \Exception('El archivo de foto no es válido o no existe.');
                }
            }
            
            $estado_registro = $validatedData['query-counts'] == '' ? 'Manual' : 'Registrado';
 
            $paciente_data = [
                'dni' => $validatedData['dni'],
                'pass' => $validatedData['dni'],
                'medios_comunicacion_id' => $validatedData['medios_comunicacion_id'], 
                'med' => $medTratante,
                'tip' => $validatedData['tip'],
                'nom' => $validatedData['nom'],
                'ape' => $validatedData['ape'],
                'fnac' => $validatedData['fnac'],
                'tcel' => $validatedData['tcel'],
                'tcas' => $validatedData['tcas'],
                'tofi' => $validatedData['tofi'],
                'mai' => $validatedData['mai'],
                'dir' => $validatedData['dir'],
                'nac' => $validatedData['nac'],
                'depa' => $validatedData['depa'],
                'prov' => $validatedData['prov'],
                'dist' => $validatedData['dist'],
                'prof' => $validatedData['prof'],
                'san' => $validatedData['san'],
                'don' => $validatedData['don'],
                'raz' => $validatedData['raz'],
                'talla' => $validatedData['talla'],
                'peso' => $validatedData['peso'],
                'rem' => $validatedData['rem'],
                'foto_principal' => $base64_foto,
                'idsedes' => $validatedData['sede'],
                'idusercreate' => $userId,
                'medico_tratante_id' => $validatedData['m_tratante'],
                'asesor_medico_id' => $validatedData['asesora'],
                'medio_referencia_id' => $validatedData['medios_comunicacion_id'],
                'sta'                 => '',
                'estado_registro' => $estado_registro
            ];

            // Verificar si el paciente ya está registrado
            $paciente = self::where('dni', $validatedData['dni'])->first();
            if ($paciente) {
                throw new \Exception('EL PACIENTE YA SE ENCUENTRA REGISTRADO');
            }
 
            $paciente = new self($paciente_data);
            $paciente->save();

            PacienteLog::createPacienteLog($validatedData,$paciente_data, $userId, 'nuevo_paciente');
            $paciente_log = new PacienteLog($paciente_data);
            $paciente_log->save();
            DB::commit(); 
            return ['status' => true, 'message' => 'Paciente registrado exitosamente'];
        } catch (\Exception $e) { 
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
