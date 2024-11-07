<?php 
namespace App\Http\Requests;

use Exception; 
use Illuminate\Validation\Rule;

class PacienteRequest extends FormRequest
{
    protected $messages = [];
    protected $errors = [];
    protected $isUpdate = false;

    public function __construct()
    {
        parent::__construct(); 
    }

    public function authorize()
    {
        return true;  
    }

    public function rules(): array
    {
        $rules = [
            'dni' => [
                'required', 
                'max:20'
            ], 
            'medios_comunicacion_id' => 'required', 
            'med' => 'nullable|string|max:255',
            'tip' => 'required',
            'nom' => 'required|string|max:255',
            'ape' => 'required|string|max:255',
            'fnac' => 'required|date',
            'tcel' => 'required|max:20',
            'tcas' => 'nullable|string|max:20',
            'tofi' => 'nullable|string|max:20',
            'mai' => 'required|email|max:255',
            'dir' => 'required|string|max:255',
            'nac' => 'required|string|max:255', 
            'prof' => 'nullable|string|max:255',
            'san' => 'nullable|string|max:255',
            'don' => 'required|string|max:255',
            'raz' => 'nullable|string|max:255',
            'talla' => 'nullable|height_cm',
            'peso' => 'nullable|weight',
            'rem' => 'required',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sede' => 'required',
            'm_tratante' => 'required|integer',
            'asesora' => 'nullable|integer', 
            'nota' => 'nullable|integer',
        ];

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'dni.required' => 'El campo DNI es obligatorio.',
            'dni.string' => 'El DNI debe ser una cadena de texto.',
            'dni.max' => 'El DNI no debe tener más de 20 caracteres.',
            'dni.unique' => 'El DNI ya está registrado.', 
            'medios_comunicacion_id.exists' => 'El ID de medios de comunicación debe existir en la base de datos.',
            'fnac.required' => 'El campo fecha de nacimiento es obligatorio.',
            'fnac.date' => 'El campo fecha de nacimiento debe ser una fecha válida.',
            'foto.image' => 'El archivo de la foto debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser de tipo jpg, jpeg o png.',
            'foto.max' => 'La foto no debe superar los 2 MB.',
            'idsedes.required' => 'El campo sede es obligatorio.',
            'idsedes.exists' => 'El ID de la sede debe existir en la base de datos.',
            'm_tratante.required' => 'El campo médico tratante es obligatorio.',
            'medico_tratante_id.exists' => 'El ID del médico tratante debe existir en la base de datos.',
            'asesor_medico_id.exists' => 'El ID del asesor médico debe existir en la base de datos.',
            'medio_referencia_id.exists' => 'El ID del medio de referencia debe existir en la base de datos.',
            'nota.integer' => 'La nota debe ser un número entero.',
            'talla.height_cm' => 'La talla no es válida',
            'peso.weight' => 'El peso no es válido'
        ];
    }

    public function validate(&$validatemsj)
    {
        $class_css = ['border-green-500','border-red-500','text-green-500','text-red-500'];
        return $this->validated($this->rules(), $this->messages(), $validatemsj, $class_css);
    }

    public function setUpdate(bool $isUpdate)
    {
        $this->isUpdate = $isUpdate;
    }
}
