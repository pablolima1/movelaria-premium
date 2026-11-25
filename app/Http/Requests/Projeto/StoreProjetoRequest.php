<?php

namespace App\Http\Requests\Projeto;

use App\Enums\CondicaoPagamento;
use App\Enums\StatusProjeto;
use App\Http\Requests\Concerns\NormalizesMoney;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjetoRequest extends FormRequest
{
    use NormalizesMoney;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $condicao = $this->input('condicao_pagamento') ?: CondicaoPagamento::AVista->value;

        $this->merge([
            'condicao_pagamento' => $condicao,
            'numero_parcelas' => $condicao === CondicaoPagamento::Parcelado->value && $this->filled('numero_parcelas')
                ? $this->input('numero_parcelas')
                : null,
            'primeiro_vencimento' => $condicao === CondicaoPagamento::Parcelado->value && $this->filled('primeiro_vencimento')
                ? $this->input('primeiro_vencimento')
                : null,
            'nome' => trim((string) $this->input('nome')),
            'valor_final' => $this->normalizeMoney($this->input('valor_final')),
            ...$this->emptyToNull(['data_inicio', 'data_conclusao', 'observacoes']),
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'integer', Rule::exists('clientes', 'id')],
            'tipo_projeto_id' => ['required', 'integer', $this->tipoProjetoRule()],
            'nome' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(StatusProjeto::class)],
            'valor_final' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'condicao_pagamento' => ['required', Rule::enum(CondicaoPagamento::class)],
            'numero_parcelas' => ['nullable', 'required_if:condicao_pagamento,parcelado', 'integer', 'min:2', 'max:' . CondicaoPagamento::MAX_PARCELAS],
            'primeiro_vencimento' => ['nullable', 'required_if:condicao_pagamento,parcelado', 'date'],
            'data_inicio' => ['nullable', 'date'],
            'data_conclusao' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'tipo_projeto_id' => 'tipo de projeto',
            'nome' => 'nome',
            'status' => 'situação',
            'valor_final' => 'valor total',
            'condicao_pagamento' => 'forma de pagamento',
            'numero_parcelas' => 'número de parcelas',
            'primeiro_vencimento' => '1º vencimento',
            'data_inicio' => 'data de início',
            'data_conclusao' => 'data de conclusão',
            'observacoes' => 'observações',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_projeto_id.exists' => 'Selecione um tipo de projeto ativo.',
            'numero_parcelas.required_if' => 'Informe em quantas parcelas o cliente vai pagar.',
            'primeiro_vencimento.required_if' => 'Informe a data de vencimento da 1ª parcela.',
            'data_conclusao.after_or_equal' => 'A data de conclusão deve ser igual ou posterior à data de início.',
        ];
    }

    protected function tipoProjetoRule(): \Illuminate\Validation\Rules\Exists
    {
        return Rule::exists('tipos_projeto', 'id')->where('ativo', true);
    }
}
