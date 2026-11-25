<?php

namespace App\Http\Requests\Despesa;

use App\Enums\CategoriaDespesa;
use App\Enums\FormaPagamento;
use App\Enums\StatusDespesa;
use App\Http\Requests\Concerns\NormalizesMoney;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDespesaRequest extends FormRequest
{
    use NormalizesMoney;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [
            'valor' => $this->normalizeMoney($this->input('valor')),
            'status' => $this->input('status') ?: StatusDespesa::Pendente->value,
            ...$this->emptyToNull(['descricao', 'data_pagamento', 'forma_pagamento']),
        ];

        // Despesa paga sem data de pagamento: assume a própria data da despesa.
        if ($data['status'] === StatusDespesa::Pago->value && $data['data_pagamento'] === null) {
            $data['data_pagamento'] = $this->input('data_despesa');
        }

        // Despesa pendente não guarda dados de pagamento.
        if ($data['status'] === StatusDespesa::Pendente->value) {
            $data['data_pagamento'] = null;
            $data['forma_pagamento'] = null;
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'categoria' => ['required', Rule::enum(CategoriaDespesa::class)],
            'descricao' => ['nullable', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'data_despesa' => ['required', 'date'],
            'status' => ['required', Rule::enum(StatusDespesa::class)],
            'data_pagamento' => ['nullable', 'date'],
            'forma_pagamento' => ['nullable', Rule::enum(FormaPagamento::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'categoria' => 'categoria',
            'descricao' => 'descrição',
            'valor' => 'valor',
            'data_despesa' => 'data da despesa',
            'status' => 'situação',
            'data_pagamento' => 'data do pagamento',
            'forma_pagamento' => 'forma de pagamento',
        ];
    }

    public function messages(): array
    {
        return [
            'valor.gt' => 'Informe um valor maior que zero.',
        ];
    }
}
