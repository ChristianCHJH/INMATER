<?php
namespace App\Http\Requests;

class EmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to' => 'required|string',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'cc' => 'nullable|string',
            'bcc' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'to.required' => 'El campo Para es obligatorio.',
            'subject.required' => 'El campo Asunto es obligatorio.',
            'body.required' => 'El campo Mensaje es obligatorio.',
        ];
    }

    public function validate(&$validatemsj)
    {
        $class_css = ['border-green-500','border-red-500','text-green-500','text-red-500'];
        return $this->validated($this->rules(), $this->messages(), $validatemsj, $class_css);
    }
}
