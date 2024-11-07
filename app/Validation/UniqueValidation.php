<?php 

namespace App\Validation;

use Rakit\Validation\Rule;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UniqueValidation extends Rule
{
    protected $fillableParams = ['table', 'column', 'except'];

    protected $message = ':attribute ya está en uso.';

    public function fillParameters(array $params): Rule
    {
        $this->params = $params;
        return $this;
    }

    public function check($value): bool
    {
        $table = $this->parameter('table');
        $column = $this->parameter('column');
        $except = $this->parameter('except');
        $modelClass = 'App\\Models\\' . ucfirst($table);
        
        if (!class_exists($modelClass)) {
            throw new Exception("Modelo '$modelClass' no encontrado.");
        }
 
        $model = new $modelClass;

        $query = $model::where($column, $value);

        if ($except !== null) { 
            $primaryKey = $model->getKeyName();
            $query->where($primaryKey, '!=', $except);
        }

        $exists = $query->exists();

        return !$exists;
    }
}
