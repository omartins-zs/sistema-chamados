<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalizarChamadoRequest extends FormRequest
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
            'motivo' => ['required', 'string', 'min:3', 'max:255'],
            'descricao' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'motivo.required' => 'Informe o motivo da finalização.',
            'motivo.min' => 'O motivo deve ter pelo menos 3 caracteres.',
            'descricao.required' => 'Informe o texto da finalização.',
            'descricao.min' => 'O texto da finalização deve ter pelo menos 10 caracteres.',
        ];
    }
}
