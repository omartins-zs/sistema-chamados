<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultarChamadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'protocolo' => ['required', 'string', 'regex:/^CHM-\d{4}-\d{6}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'protocolo.required' => 'Informe o protocolo do chamado.',
            'protocolo.regex' => 'O protocolo deve estar no formato CHM-AAAA-NNNNNN.',
        ];
    }
}
