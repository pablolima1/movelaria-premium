<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly DashboardService $service)
    {
    }

    public function index(Request $request): View
    {
        return view('pages.dashboard.home', $this->service->montar($this->resolveMes($request->string('mes')->toString())));
    }

    private function resolveMes(string $mes): CarbonImmutable
    {
        if (preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $mes, $m) && (int) $m[1] >= 2000 && (int) $m[1] <= 2100) {
            return CarbonImmutable::create((int) $m[1], (int) $m[2], 1);
        }

        return CarbonImmutable::now()->startOfMonth();
    }
}
