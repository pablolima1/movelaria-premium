<?php

namespace App\Http\Requests\Recebimento;

class UpdateRecebimentoRequest extends StoreRecebimentoRequest
{
    protected function ignoredId(): ?int
    {
        return $this->route('recebimento')?->id;
    }
}
