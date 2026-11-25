<?php

namespace App\Http\Requests\Projeto;

use App\Enums\CondicaoPagamento;
use App\Models\Projeto;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Validator;

class UpdateProjetoRequest extends StoreProjetoRequest
{
    /**
     * Na edição, aceita manter um tipo de projeto que foi inativado depois.
     */
    protected function tipoProjetoRule(): Exists
    {
        $atual = $this->projeto()?->tipo_projeto_id;

        return Rule::exists('tipos_projeto', 'id')->where(function ($query) use ($atual) {
            $query->where('ativo', true)->when($atual, fn ($q) => $q->orWhere('id', $atual));
        });
    }

    public function after(): array
    {
        return [
            fn (Validator $validator) => $this->validarParcelasPagas($validator),
            function (Validator $validator) {
                $projeto = $this->projeto();
                $valor = $this->input('valor_final');

                if (! $projeto || $valor === null || $validator->errors()->has('valor_final')) {
                    return;
                }

                $recebido = (float) $projeto->recebimentos()->sum('valor');
                if ((float) $valor + 0.005 < $recebido) {
                    $validator->errors()->add(
                        'valor_final',
                        'O valor total não pode ser menor que o já recebido (R$ ' . number_format($recebido, 2, ',', '.') . ').',
                    );
                }
            },
        ];
    }

    /**
     * Parcelas já pagas não são refeitas: o novo plano precisa comportá-las.
     */
    private function validarParcelasPagas(Validator $validator): void
    {
        $projeto = $this->projeto();

        if (! $projeto || $this->input('condicao_pagamento') !== CondicaoPagamento::Parcelado->value || $validator->errors()->hasAny(['numero_parcelas', 'valor_final'])) {
            return;
        }

        $pagas = $projeto->parcelas()->pagas()->get(['valor']);
        if ($pagas->isEmpty()) {
            return;
        }

        $numero = (int) $this->input('numero_parcelas');
        $restante = (float) $this->input('valor_final') - (float) $pagas->sum('valor');

        if ($numero < $pagas->count() || ($numero === $pagas->count() && $restante > 0.005)) {
            $validator->errors()->add(
                'numero_parcelas',
                "Já existem {$pagas->count()} parcela(s) paga(s). Informe um número maior para dividir o restante.",
            );
        }
    }

    private function projeto(): ?Projeto
    {
        $projeto = $this->route('projeto');

        return $projeto instanceof Projeto ? $projeto : null;
    }
}
