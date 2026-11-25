<?php

namespace App\Http\Requests\Parcela;

use App\Enums\FormaPagamento;
use App\Models\Parcela;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReceberParcelaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data_recebimento' => ['required', 'date'],
            'forma_pagamento' => ['required', Rule::enum(FormaPagamento::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'data_recebimento' => 'data do recebimento',
            'forma_pagamento' => 'forma de pagamento',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $parcela = $this->route('parcela');

                if (! $parcela instanceof Parcela) {
                    return;
                }

                if ($parcela->isPaga()) {
                    $validator->errors()->add('parcela', 'Esta parcela já foi recebida.');

                    return;
                }

                // Não deixa o total recebido passar do valor do projeto (ex.: lançamentos avulsos).
                $projeto = $parcela->projeto;
                if ($projeto->valor_final !== null) {
                    $saldo = round((float) $projeto->valor_final - (float) $projeto->recebimentos()->sum('valor'), 2);
                    if ((float) $parcela->valor > $saldo + 0.005) {
                        $validator->errors()->add(
                            'parcela',
                            'O valor da parcela ultrapassa o saldo a receber do projeto (R$ ' . number_format(max($saldo, 0), 2, ',', '.') . '). Confira os recebimentos lançados.',
                        );
                    }
                }
            },
        ];
    }
}
