<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf_cnpj' => $this->sanitizeDigits($this->input('cpf_cnpj')),
            'telefone' => $this->sanitizeDigits($this->input('telefone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'cpf_cnpj' => ['nullable', 'string', 'max:30', Rule::unique('clientes', 'cpf_cnpj')],
            'telefone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'endereco' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'cpf_cnpj' => 'CPF/CNPJ',
            'telefone' => 'telefone',
            'email' => 'e-mail',
            'endereco' => 'endereço',
            'observacoes' => 'observações',
        ];
    }

    private function sanitizeDigits(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $value);

        return $digits === '' ? null : $digits;
    }
}
