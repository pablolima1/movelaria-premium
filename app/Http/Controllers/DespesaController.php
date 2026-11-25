<?php

namespace App\Http\Controllers;

use App\Http\Requests\Despesa\StoreDespesaRequest;
use App\Http\Requests\Despesa\UpdateDespesaRequest;
use App\Models\Despesa;
use App\Models\Projeto;
use App\Services\DespesaService;
use Illuminate\Http\RedirectResponse;

class DespesaController extends Controller
{
    public function __construct(private readonly DespesaService $service)
    {
    }

    public function store(StoreDespesaRequest $request, Projeto $projeto): RedirectResponse
    {
        $this->service->create($projeto, $request->validated());

        return back()->with('success', 'Despesa lançada em "' . $projeto->nome . '".');
    }

    public function update(UpdateDespesaRequest $request, Projeto $projeto, Despesa $despesa): RedirectResponse
    {
        $this->service->update($despesa, $request->validated());

        return back()->with('success', 'Despesa atualizada com sucesso.');
    }

    public function destroy(Projeto $projeto, Despesa $despesa): RedirectResponse
    {
        $this->service->delete($despesa);

        return back()->with('success', 'Despesa excluída com sucesso.');
    }
}
