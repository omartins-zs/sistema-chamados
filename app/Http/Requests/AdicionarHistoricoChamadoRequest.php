<?php

namespace App\Http\Requests;

use App\Enums\StatusChamadoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdicionarHistoricoChamadoRequest extends FormRequest
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
            'status' => ['required', Rule::enum(StatusChamadoEnum::class)],
            'descricao' => ['required', 'string', 'min:5'],
            'visivel_solicitante' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Informe o status da atualização.',
            'descricao.required' => 'Informe a descrição do histórico.',
            'descricao.min' => 'A descrição deve ter pelo menos 5 caracteres.',
        ];
    }
}
