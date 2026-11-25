<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoProjeto\StoreTipoProjetoRequest;
use App\Http\Requests\TipoProjeto\UpdateTipoProjetoRequest;
use App\Models\TipoProjeto;
use App\Services\TipoProjetoService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoProjetoController extends Controller
{
    public function __construct(private readonly TipoProjetoService $service)
    {
    }

    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->string('q')->toString(),
            'status' => $request->string('status')->toString(),
        ];

        $tiposProjeto = $this->service->list($filters);

        return view('pages.tipos-projeto.index', [
            'tiposProjeto' => $tiposProjeto,
            'filters' => $filters,
        ]);
    }

    public function store(StoreTipoProjetoRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('tipos-projeto.index', $request->only('q', 'status'))
            ->with('success', 'Tipo de projeto cadastrado com sucesso.');
    }

    public function update(UpdateTipoProjetoRequest $request, TipoProjeto $tipoProjeto): RedirectResponse
    {
        $this->service->update($tipoProjeto, $request->validated());

        return redirect()
            ->route('tipos-projeto.index', $request->only('q', 'status'))
            ->with('success', 'Tipo de projeto atualizado com sucesso.');
    }

    public function destroy(Request $request, TipoProjeto $tipoProjeto): RedirectResponse
    {
        try {
            $this->service->delete($tipoProjeto);
        } catch (QueryException $e) {
            return redirect()
                ->route('tipos-projeto.index', $request->only('q', 'status'))
                ->with('error', 'Não foi possível excluir o tipo de projeto pois ele possui projetos vinculados. Você pode inativá-lo.');
        }

        return redirect()
            ->route('tipos-projeto.index', $request->only('q', 'status'))
            ->with('success', 'Tipo de projeto excluído com sucesso.');
    }
}
