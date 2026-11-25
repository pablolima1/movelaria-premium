<?php

namespace App\Http\Requests\Recebimento;

use App\Enums\FormaPagamento;
use App\Http\Requests\Concerns\NormalizesMoney;
use App\Models\Projeto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRecebimentoRequest extends FormRequest
{
    use NormalizesMoney;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'valor' => $this->normalizeMoney($this->input('valor')),
            ...$this->emptyToNull(['observacoes']),
        ]);
    }

    public function rules(): array
    {
        return [
            'valor' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'forma_pagamento' => ['required', Rule::enum(FormaPagamento::class)],
            'data_recebimento' => ['required', 'date'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'valor' => 'valor',
            'forma_pagamento' => 'forma de pagamento',
            'data_recebimento' => 'data do recebimento',
            'observacoes' => 'observações',
        ];
    }

    public function messages(): array
    {
        return [
            'valor.gt' => 'Informe um valor maior que zero.',
        ];
    }

    /**
     * Não deixa o total recebido passar do valor total do projeto.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $projeto = $this->route('projeto');

                if (! $projeto instanceof Projeto || $projeto->valor_final === null || $validator->errors()->has('valor')) {
                    return;
                }

                $outros = $projeto->recebimentos()
                    ->when($this->ignoredId(), fn ($q, $id) => $q->whereKeyNot($id))
                    ->sum('valor');

                $saldo = round((float) $projeto->valor_final - (float) $outros, 2);

                if ((float) $this->input('valor') > $saldo + 0.005) {
                    $validator->errors()->add(
                        'valor',
                        $saldo > 0
                            ? 'O valor ultrapassa o saldo a receber do projeto (R$ ' . number_format($saldo, 2, ',', '.') . ').'
                            : 'Este projeto já foi totalmente recebido. Ajuste o valor total do projeto se necessário.',
                    );
                }
            },
        ];
    }

    protected function ignoredId(): ?int
    {
        return null;
    }
}
