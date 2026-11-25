<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recebimento\StoreRecebimentoRequest;
use App\Http\Requests\Recebimento\UpdateRecebimentoRequest;
use App\Models\Projeto;
use App\Models\Recebimento;
use App\Services\RecebimentoService;
use Illuminate\Http\RedirectResponse;

class RecebimentoController extends Controller
{
    public function __construct(private readonly RecebimentoService $service)
    {
    }

    public function store(StoreRecebimentoRequest $request, Projeto $projeto): RedirectResponse
    {
        $this->service->create($projeto, $request->validated());

        return back()->with('success', 'Recebimento lançado em "' . $projeto->nome . '".');
    }

    public function update(UpdateRecebimentoRequest $request, Projeto $projeto, Recebimento $recebimento): RedirectResponse
    {
        $this->service->update($recebimento, $request->validated());

        return back()->with('success', 'Recebimento atualizado com sucesso.');
    }

    public function destroy(Projeto $projeto, Recebimento $recebimento): RedirectResponse
    {
        $this->service->delete($recebimento);

        return back()->with('success', 'Recebimento excluído com sucesso.');
    }
}
