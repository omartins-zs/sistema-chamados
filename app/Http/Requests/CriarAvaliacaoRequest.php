<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriarAvaliacaoRequest extends FormRequest
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
            'nota_satisfacao' => ['required', 'integer', 'min:1', 'max:5'],
            'nota_tempo_resolucao' => ['required', 'integer', 'min:1', 'max:5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nota_satisfacao.required' => 'Informe a nota de satisfação.',
            'nota_satisfacao.min' => 'A nota de satisfação deve ser entre 1 e 5.',
            'nota_satisfacao.max' => 'A nota de satisfação deve ser entre 1 e 5.',
            'nota_tempo_resolucao.required' => 'Informe a nota de tempo de resolução.',
            'nota_tempo_resolucao.min' => 'A nota de tempo deve ser entre 1 e 5.',
            'nota_tempo_resolucao.max' => 'A nota de tempo deve ser entre 1 e 5.',
        ];
    }
}
