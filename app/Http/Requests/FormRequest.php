<?php
namespace App\Http\Requests;
use App\Core\Request; 
use Exception;
abstract class FormRequest extends Request
{
 
    protected $rules = [];
    protected $messages = [];
    protected $errors = [];
    protected $data = [];

    public function __construct()
    {
         
        $this->data = $_POST;
    }

    abstract public function rules(): array;

    public function validated($rules = null,$mensajes = null,&$validatemsj = null,$validateClass = ['is-valid', 'is-invalid', 'valid-feedback', 'invalid-feedback'])
    {
         
        $rules = $rules == null ? $this->rules() : $rules; 
        $mensajes = $mensajes == null ? $this->messages() : $mensajes; 
        $validatemsj2 = [];
        $rulesf = []; 
        $datosf = $this->data;  
        //print_r($rules); exit;

        foreach ($rules as $key => $item) { 
                if (is_array($rules[$key])) {
                        $rulesf[$key]['rule'] = $rules[$key];
                        $rulesf[$key]['value'] = isset($datosf[$key]) ? $datosf[$key] : '';
                        foreach ($rules[$key] as $key2 => $item2) {
                            if ($item2 != 'nullable') {
                                $msj = isset($mensajes[$key.'.'.$item2]) ? $mensajes[$key.'.'.$item2] : '';
                                $rulesf[$key]['messages'] = $msj;
                            } 
                        } 
                }else{
                        $rule = explode("|", $rules[$key]);
                        $rulesf[$key]['rule'] = $rule;
                        $rulesf[$key]['value'] = isset($datosf[$key]) ? $datosf[$key] : '';
                         
                        foreach ($rule as $key2 => $item2) {
                            if ($item2 != 'nullable') {
                                $msj = isset($mensajes[$key.'.'.$item2]) ? $mensajes[$key.'.'.$item2] : '';
                                $rulesf[$key]['messages'] = $msj;
                            }
                        }
                }  
        }
       
        $rulesValid = 0;
        $required = 0;
        //print_r($rulesf); exit;
        foreach ($rulesf as $key => $item) {   
           $valid = $this->validationFx($item['rule'],$item['value'],$item['messages'],$key,$validateClass); 

           if ((isset($valid[$key]['class']) && $valid[$key]['class'] == false) && (isset($valid[$key]['type']) && $valid[$key]['type'] == 'required')) {
                $required++;
           } 
           $validatemsj2[$key]['class'] = isset($valid[$key]['class']) && $valid[$key]['class'] == true ? $validateClass[0] : $validateClass[1];
           $validatemsj2[$key]['classmsj'] = isset($valid[$key]['class']) && $valid[$key]['class'] == true ? $validateClass[2] : $validateClass[3];
           $validatemsj2[$key]['msj'] = isset($valid[$key]['msj']) && $valid[$key]['msj'] != '' ? $valid[$key]['msj'] : $item['messages']; 
           $validatemsj2[$key]['status'] = isset($valid[$key]['class']) && $valid[$key]['class'] == true ? true : false;
        }

        //print_r($validatemsj2); exit;
        $validatemsj = $validatemsj2;
 
        foreach ($validatemsj as $key => $item) {
            if ($item['status'] == false) {
                $rulesValid ++;  
            }
        } 

        if ($rulesValid != 0) {
            if (0 < $required ) {
                throw new Exception("Completa los campos requeridos");
            }else{
                throw new Exception("Verifica los errores en el formulario");
            } 
        }
 
        return $datosf;
    }
    public function validationFx($rules, $value, $msj,$llave){
        $html = [];
        $isNullable = in_array('nullable', $rules);
 
        if ($isNullable && $value === '') {
            $html[$llave]['class'] = true;
            $html[$llave]['msj'] = '¡Se ve bien!';
            //return $html;
        }
        //print_r($rules); exit;
        foreach ($rules as $key => $item) { 
            if (trim($item) == 'required') { 
                $html[$llave]['class'] = trim($value) != '' ? true : false; 
                $html[$llave]['type'] = 'required';
                $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo es requerido!!!';
                $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj']; 
            }
            if (trim($item) == 'height_cm') { 
                //if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) { 
                    $html[$llave]['type'] = 'height_cm';
                    $html[$llave]['class'] = preg_match('/^(5[0-9]|[6-9][0-9]|1[0-9][0-9]|2[0-9][0-9]|300)$/', $value) ? true : false;
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El valor de la talla no es válido';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                //}
            }
            if (trim($item) == 'weight') {
                //if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'weight';
                    $html[$llave]['class'] = preg_match('/^([1-9]\d|[1-2]\d{2}|300)(\.\d{1,2})?$/', $value) ? true : false;
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El valor del peso no es válido';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                //}
            }
            if (trim($item) == 'numeric') {
                //if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'numeric';
                    $html[$llave]['class'] = is_numeric($value) ? true : false; 
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es numérico';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                //}
            }
            if (trim($item) == 'integer') { 
                //if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {  
                    $html[$llave]['type'] = 'integer';
                    $html[$llave]['class'] = filter_var($value, FILTER_VALIDATE_INT) !== false;
                    $html[$llave]['msj'] = trim($msj) != '' ? $msj : 'El campo no es un entero';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                //}
            }
            if (trim($item) == 'string') {
                //if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {  
                    $html[$llave]['type'] = 'string';
                    $html[$llave]['class'] = is_string($value) && !preg_match('/^\d+$/', $value) ? true : false;   
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'Por favor, ingrese solo texto';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                //} 
            }
            if (preg_match('/max:(\d+)/', $value)) {
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $v_max = explode(":", $value);
                    $html[$llave]['type'] = 'max';
                    $html[$llave]['class'] = strlen($value) <= $v_max[1] ? true : false;  
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es alfanumérico';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj']; 
                } 
            }
            if (preg_match('/min:(\d+)/', $value)) {
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $v_min = explode(":", $value);
                    $html[$llave]['type'] = 'min';
                    $html[$llave]['class'] = $v_min[1] <= strlen($value) ? true : false; 
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es alfanumérico';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                } 
            }
            if (trim($item) == 'email') { 
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'email';
                    $html[$llave]['class'] = filter_var($value, FILTER_VALIDATE_EMAIL) ? true : false; 
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es un correo';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj']; 
                } 
            }
            if (trim($item) == 'phone') { //Validación número de telefono 
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'phone';
                    $html[$llave]['class'] = preg_match('/^\+?[0-9\s\-\(\)]+$/', $value) ? true : false; 
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es un número telefonico';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj']; 
                } 
            }
            if (trim($item) == 'age') { // validación edad 
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'age';
                    $html[$llave]['class'] = preg_match('/^([1-9][0-9]{0,1}|1[01][0-9]|120)$/', $value) ? true : false;  
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es una edad válida';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                } 
            } 
            if (trim($item) == 'name') { // validación nombre 
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) { 
                    $html[$llave]['type'] = 'name';
                    $html[$llave]['class'] = preg_match('/^[a-zA-Z\s\'-]+$/', $value) ? true : false; 
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es un nombre válido';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                } 
            } 
            if (trim($item) == 'url') { // validación url 
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'url';
                    $html[$llave]['class'] = filter_var($value, FILTER_VALIDATE_URL) ? true : false; 
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es una url válida';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                } 
            } 
            if (trim($item) == 'date') { // validación fecha 
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'date';
                    $html[$llave]['class'] = preg_match('/^\d{4}-\d{2}-\d{2}$|^\d{2}-\d{2}-\d{4}$|^\d{2}\/\d{2}\/\d{4}$/', $value) ? true : false; 
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es una fecha válida';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                } 
            } 
            if (trim($item) == 'ip') { // validación ip 
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'ip';
                    $html[$llave]['class'] = filter_var($value, FILTER_VALIDATE_IP) ? true : false; 
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es una ip válida';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                } 
            } 
            if (trim($item) == 'postcode') { // validación código postal
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'postcode';
                    $html[$llave]['class'] = preg_match('/^\d{5}(-\d{4})?$/', $value) ? true : false;  
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es un código postal válido';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj']; 
                } 
            } 
            if (trim($item) == 'card') { // validación tarjeta
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'card';
                    $html[$llave]['class'] = preg_match('/^\d{13,19}$/', $value) && $this->luhnCheck($value) ? true : false;  
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es correcto';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj']; 
                } 
            }  
            if (trim($item) == 'image') {
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $html[$llave]['type'] = 'image';
                    $html[$llave]['class'] = isset($_FILES[$llave]['tmp_name']) && $_FILES[$llave]['tmp_name'] != '' && exif_imagetype($_FILES[$llave]['tmp_name']) !== false;
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El campo no es una imagen válida';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                }
            }
            if (preg_match('/mimes:([^,]+)(?:,([^,]+))*$/', $item, $matches)) {
                if (isset($html[$llave]['class']) && $html[$llave]['class'] == true) {
                    $allowedMimes = array_slice($matches, 1);
                    $fileMime = mime_content_type($_FILES[$llave]['tmp_name']);
                    $html[$llave]['type'] = 'mimes';
                    $html[$llave]['class'] = in_array($fileMime, $allowedMimes);
                    $html[$llave]['msj'] = isset($msj) && trim($msj) != '' ? $msj : 'El archivo no tiene un tipo MIME permitido';
                    $html[$llave]['msj'] = $html[$llave]['class'] == true ? '¡Se ve bien!' : $html[$llave]['msj'];
                }
            }
        }
        return $html;
    }

    public function hasRequiredValidationErrors($errors)
    {
        foreach ($errors as $error) {
            if (strpos($error, 'is required') !== false || strpos($error, 'es obligatorio') !== false) {
                return true;
            }
        }
        return false;
    }

    protected function messages(): array
    {
        return [];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function all()
    {
        return $this->data;
    }
    public function validatex(&$validatemsj)
    { 
        $class_css = ['border-green-500','border-red-500','text-green-500','text-red-500'];
        return $this->validated($this->rules(), $this->messages(), $validatemsj, $class_css);
    } 
    function luhnCheck($number) {
        $sum = 0;
        $alt = false;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $n = $number[$i];
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n = ($n % 10) + 1;
                }
            }
            $sum += $n;
            $alt = !$alt;
        }
        return ($sum % 10 == 0);
    }
    public function hasFile($key)
    {
        //print_r($_FILES['attachments']); exit;
        // Verifica si el índice existe en la superglobal $_FILES
        return isset($_FILES[$key]) && ($_FILES[$key]['error'] === UPLOAD_ERR_OK || (isset($_FILES[$key]['error'][0]) && $_FILES[$key]['error'][0] === UPLOAD_ERR_OK));
    }
    public function file($key)
    {
        // Verifica si el archivo existe en $_FILES
        if (isset($_FILES[$key])) {
            $file = $_FILES[$key];
            
            // Verifica si el archivo ha sido subido sin errores y si está en formato de array multidimensional
            if (is_array($file['error'])) {
                // Formato multidimensional: `$_FILES[$key]` contiene arrays anidados
                $files = [];
                for ($i = 0; $i < count($file['name']); $i++) {
                    if ($file['error'][$i] === UPLOAD_ERR_OK) {
                        $files[] = [
                            'name' => $file['name'][$i],
                            'type' => $file['type'][$i],
                            'tmp_name' => $file['tmp_name'][$i],
                            'error' => $file['error'][$i],
                            'size' => $file['size'][$i]
                        ];
                    }
                }
                return $files;
            } else {
                // Formato simple: `$_FILES[$key]` contiene información directa del archivo
                if ($file['error'] === UPLOAD_ERR_OK) {
                    return [
                        'name' => $file['name'],
                        'type' => $file['type'],
                        'tmp_name' => $file['tmp_name'],
                        'error' => $file['error'],
                        'size' => $file['size']
                    ];
                }
            }
        }

        // Si el archivo no existe o ha ocurrido un error, devuelve null o maneja el error según sea necesario
        return null;
    }
    
}

