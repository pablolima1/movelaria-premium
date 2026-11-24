<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\ClienteService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function __construct(private readonly ClienteService $service)
    {
    }

    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->string('q')->toString(),
        ];

        $clientes = $this->service->list($filters);

        return view('pages.clientes.index', [
            'clientes' => $clientes,
            'filters' => $filters,
        ]);
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('clientes.index', $request->only('q'))
            ->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $this->service->update($cliente, $request->validated());

        return redirect()
            ->route('clientes.index', $request->only('q'))
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Request $request, Cliente $cliente): RedirectResponse
    {
        try {
            $this->service->delete($cliente);
        } catch (QueryException $e) {
            return redirect()
                ->route('clientes.index', $request->only('q'))
                ->with('error', 'Não foi possível excluir o cliente pois ele possui vínculos no sistema.');
        }

        return redirect()
            ->route('clientes.index', $request->only('q'))
            ->with('success', 'Cliente excluído com sucesso.');
    }
}
