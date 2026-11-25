@php
    $categorias = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $entrou = array_values(array_map(fn ($m) => $m['entrou'], $serieMensal));
    $liquido = array_values(array_map(fn ($m) => $m['lucro_liquido'], $serieMensal));
@endphp

<div class="rounded-2xl border border-gray-200 bg-white px-4 pt-4 pb-2 dark:border-gray-800 dark:bg-white/[0.03] sm:px-5 sm:pt-5">
    <div class="flex items-center justify-between gap-3">
        <p class="text-sm font-medium text-gray-800 dark:text-white/90">Mês a mês</p>
        <div class="flex items-center gap-3 text-theme-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-brand-500"></span>Entrou</span>
            <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-success-500"></span>Líquido</span>
        </div>
    </div>

    <div
        x-data="{
            chart: null,
            options() {
                const dark = $store.theme.theme === 'dark';
                const money = v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                return {
                    chart: { type: 'bar', height: 220, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif', background: 'transparent' },
                    theme: { mode: dark ? 'dark' : 'light' },
                    series: [
                        { name: 'Entrou', data: @js($entrou) },
                        { name: 'Lucro líquido', data: @js($liquido) },
                    ],
                    colors: ['#465fff', '#12b76a'],
                    plotOptions: { bar: { columnWidth: '60%', borderRadius: 4, borderRadiusApplication: 'end' } },
                    dataLabels: { enabled: false },
                    legend: { show: false },
                    grid: { borderColor: dark ? '#1d2939' : '#f2f4f7', yaxis: { lines: { show: true } } },
                    xaxis: {
                        categories: @js($categorias),
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: dark ? '#98a2b3' : '#667085', fontSize: '11px' } },
                    },
                    yaxis: {
                        labels: {
                            style: { colors: dark ? '#98a2b3' : '#667085', fontSize: '11px' },
                            formatter: v => Math.abs(v) >= 1000 ? (v / 1000).toLocaleString('pt-BR', { maximumFractionDigits: 0 }) + 'k' : v.toFixed(0),
                        },
                    },
                    tooltip: { theme: dark ? 'dark' : 'light', y: { formatter: money } },
                };
            },
            init() {
                this.chart = new ApexCharts(this.$refs.chart, this.options());
                this.chart.render();
                this.$watch('$store.theme.theme', () => this.chart.updateOptions(this.options()));
            },
            destroy() {
                this.chart?.destroy();
            }
        }"
    >
        <div x-ref="chart" class="-ml-2 min-h-[220px]"></div>
    </div>
</div>
