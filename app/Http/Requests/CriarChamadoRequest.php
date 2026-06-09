<?php

namespace App\Http\Requests;

use App\Enums\ComplexidadeChamadoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CriarChamadoRequest extends FormRequest
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
            'nome_solicitante' => ['required', 'string', 'max:255'],
            'email_solicitante' => ['required', 'email', 'max:255'],
            'telefone_solicitante' => ['required', 'string', 'max:20'],
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string', 'min:10'],
            'complexidade' => ['required', Rule::enum(ComplexidadeChamadoEnum::class)],
            'setor_id' => ['required', 'exists:setores,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome_solicitante.required' => 'Informe o nome do solicitante.',
            'email_solicitante.required' => 'Informe o e-mail do solicitante.',
            'email_solicitante.email' => 'Informe um e-mail válido.',
            'telefone_solicitante.required' => 'Informe o telefone ou WhatsApp.',
            'titulo.required' => 'Informe o título do chamado.',
            'descricao.required' => 'Informe a descrição detalhada do chamado.',
            'descricao.min' => 'A descrição deve ter pelo menos 10 caracteres.',
            'complexidade.required' => 'Selecione a complexidade do chamado.',
            'setor_id.required' => 'Selecione o setor responsável.',
            'setor_id.exists' => 'O setor selecionado é inválido.',
        ];
    }
}
