<?php

namespace App\Http\Controllers;

use App\Http\Requests\Parcela\ReceberParcelaRequest;
use App\Http\Requests\Parcela\RemarcarParcelaRequest;
use App\Models\Parcela;
use App\Services\ParcelaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParcelaController extends Controller
{
    private const ABAS = ['abertas', 'atrasadas', 'pagas'];

    public function __construct(private readonly ParcelaService $service)
    {
    }

    /** Tela de cobranças. */
    public function index(Request $request): View
    {
        $aba = in_array($request->query('aba'), self::ABAS, true) ? $request->query('aba') : 'abertas';
        $busca = trim($request->string('q')->toString());

        return view('pages.cobrancas.index', [
            'aba' => $aba,
            'busca' => $busca,
            'parcelas' => $this->service->cobrancas($aba, $busca),
            'resumo' => $this->service->resumo(),
        ]);
    }

    public function receber(ReceberParcelaRequest $request, Parcela $parcela): RedirectResponse
    {
        $this->service->receber($parcela, $request->validated());

        return back()->with('success', "Parcela {$parcela->numero} de \"{$parcela->projeto->nome}\" recebida.");
    }

    public function update(RemarcarParcelaRequest $request, Parcela $parcela): RedirectResponse
    {
        $this->service->remarcar($parcela, $request->validated('vencimento'));

        return back()->with('success', "Vencimento da parcela {$parcela->numero} alterado.");
    }
}
