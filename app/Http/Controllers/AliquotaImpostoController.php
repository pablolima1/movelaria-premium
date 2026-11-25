<?php

namespace App\Http\Controllers;

use App\Http\Requests\AliquotaImposto\StoreAliquotaImpostoRequest;
use App\Http\Requests\AliquotaImposto\UpdateAliquotaImpostoRequest;
use App\Models\AliquotaImposto;
use App\Services\AliquotaImpostoService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AliquotaImpostoController extends Controller
{
    public function __construct(private readonly AliquotaImpostoService $service)
    {
    }

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->string('status')->toString(),
        ];

        $aliquotas = $this->service->list($filters);

        return view('pages.aliquotas-imposto.index', [
            'aliquotas' => $aliquotas,
            'aliquotaVigente' => $this->service->vigente(),
            'filters' => $filters,
        ]);
    }

    public function store(StoreAliquotaImpostoRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('aliquotas-imposto.index', $request->only('status'))
            ->with('success', 'Alíquota cadastrada com sucesso.');
    }

    public function update(UpdateAliquotaImpostoRequest $request, AliquotaImposto $aliquota): RedirectResponse
    {
        $this->service->update($aliquota, $request->validated());

        return redirect()
            ->route('aliquotas-imposto.index', $request->only('status'))
            ->with('success', 'Alíquota atualizada com sucesso.');
    }

    public function destroy(Request $request, AliquotaImposto $aliquota): RedirectResponse
    {
        try {
            $this->service->delete($aliquota);
        } catch (QueryException $e) {
            return redirect()
                ->route('aliquotas-imposto.index', $request->only('status'))
                ->with('error', 'Não foi possível excluir a alíquota pois ela possui vínculos no sistema.');
        }

        return redirect()
            ->route('aliquotas-imposto.index', $request->only('status'))
            ->with('success', 'Alíquota excluída com sucesso.');
    }
}
