<?php

namespace App\Http\Requests\TipoProjeto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoProjetoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome' => trim((string) $this->input('nome')),
            'ativo' => $this->boolean('ativo'),
        ]);
    }

    public function rules(): array
    {
        $tipoProjetoId = $this->route('tipoProjeto')?->id ?? $this->route('tipoProjeto');

        return [
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tipos_projeto', 'nome')->ignore($tipoProjetoId),
            ],
            'ativo' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'ativo' => 'situação',
        ];
    }
}
