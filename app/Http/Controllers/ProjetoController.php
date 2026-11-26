<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projeto\StoreProjetoRequest;
use App\Http\Requests\Projeto\UpdateProjetoRequest;
use App\Models\Projeto;
use App\Services\ProjetoService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjetoController extends Controller
{
    public function __construct(private readonly ProjetoService $service)
    {
    }

    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->string('q')->toString(),
            'status' => $request->string('status')->toString(),
            'tipo' => $request->string('tipo')->toString(),
            // Padrão: ano atual. "todos" remove o filtro de período.
            'ano' => $request->string('ano', (string) now()->year)->toString(),
            'mes' => $request->string('mes')->toString(),
        ];

        return view('pages.projetos.index', [
            'projetos' => $this->service->list($filters),
            'filters' => $filters,
            'anos' => $this->service->anosOptions(),
            'anoPadrao' => (string) now()->year,
            ...$this->service->formOptions(),
        ]);
    }

    public function show(Projeto $projeto): View
    {
        return view('pages.projetos.show', [
            'projeto' => $this->service->detalhe($projeto),
            ...$this->service->formOptions(),
        ]);
    }

    public function store(StoreProjetoRequest $request): RedirectResponse
    {
        $projeto = $this->service->create($request->validated());

        return redirect()
            ->route('projetos.show', $projeto)
            ->with('success', 'Projeto cadastrado com sucesso. Agora você pode lançar recebimentos e despesas.');
    }

    public function update(UpdateProjetoRequest $request, Projeto $projeto): RedirectResponse
    {
        $this->service->update($projeto, $request->validated());

        return back()->with('success', 'Projeto atualizado com sucesso.');
    }

    public function destroy(Request $request, Projeto $projeto): RedirectResponse
    {
        try {
            $this->service->delete($projeto);
        } catch (QueryException $e) {
            return back()->with('error', 'Não foi possível excluir o projeto pois ele possui recebimentos ou despesas. Exclua os lançamentos ou altere a situação para "Cancelado".');
        }

        return redirect()
            ->route('projetos.index', $request->only('q', 'status', 'tipo', 'ano', 'mes'))
            ->with('success', 'Projeto excluído com sucesso.');
    }
}
