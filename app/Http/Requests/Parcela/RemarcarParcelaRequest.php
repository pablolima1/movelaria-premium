<?php

namespace App\Http\Requests\Parcela;

use App\Models\Parcela;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RemarcarParcelaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vencimento' => ['required', 'date'],
        ];
    }

    public function attributes(): array
    {
        return ['vencimento' => 'vencimento'];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $parcela = $this->route('parcela');
                if ($parcela instanceof Parcela && $parcela->isPaga()) {
                    $validator->errors()->add('vencimento', 'Parcela já recebida não pode ser remarcada.');
                }
            },
        ];
    }
}
