<?php

namespace App\Http\Requests\AliquotaImposto;

class UpdateAliquotaImpostoRequest extends StoreAliquotaImpostoRequest
{
    protected function ignoredId(): ?int
    {
        $aliquota = $this->route('aliquota');

        return (int) ($aliquota?->id ?? $aliquota) ?: null;
    }
}
