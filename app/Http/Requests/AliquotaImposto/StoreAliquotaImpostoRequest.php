<?php

namespace App\Http\Requests\AliquotaImposto;

use App\Models\AliquotaImposto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAliquotaImpostoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'percentual' => $this->sanitizeDecimal($this->input('percentual')),
            'vigente_ate' => $this->input('vigente_ate') ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'percentual' => ['required', 'numeric', 'min:0', 'max:100'],
            'vigente_desde' => ['required', 'date'],
            'vigente_ate' => ['nullable', 'date', 'after_or_equal:vigente_desde'],
        ];
    }

    public function attributes(): array
    {
        return [
            'percentual' => 'percentual',
            'vigente_desde' => 'vigente desde',
            'vigente_ate' => 'vigente até',
        ];
    }

    public function messages(): array
    {
        return [
            'vigente_ate.after_or_equal' => 'A data final da vigência deve ser igual ou posterior à data inicial.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['vigente_desde', 'vigente_ate'])) {
                    return;
                }

                $conflito = $this->findOverlap(
                    (string) $this->input('vigente_desde'),
                    $this->input('vigente_ate'),
                );

                if ($conflito) {
                    $validator->errors()->add(
                        'vigente_desde',
                        sprintf(
                            'O período informado conflita com a alíquota de %s%% vigente de %s %s.',
                            number_format((float) $conflito->percentual, 2, ',', '.'),
                            $conflito->vigente_desde->format('d/m/Y'),
                            $conflito->vigente_ate ? 'até ' . $conflito->vigente_ate->format('d/m/Y') : 'em diante',
                        ),
                    );
                }
            },
        ];
    }

    protected function ignoredId(): ?int
    {
        return null;
    }

    private function findOverlap(string $desde, ?string $ate): ?AliquotaImposto
    {
        return AliquotaImposto::query()
            ->when($this->ignoredId(), fn ($query, $id) => $query->whereKeyNot($id))
            ->when($ate, fn ($query) => $query->whereDate('vigente_desde', '<=', $ate))
            ->where(function ($query) use ($desde) {
                $query->whereNull('vigente_ate')
                    ->orWhereDate('vigente_ate', '>=', $desde);
            })
            ->orderBy('vigente_desde')
            ->first();
    }

    private function sanitizeDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        if (str_contains($value, ',')) {
            $value = str_replace(['.', ','], ['', '.'], $value);
        }

        return $value;
    }
}
