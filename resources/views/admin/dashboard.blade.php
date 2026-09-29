@extends('admin.layout')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<style>
    .dash-panel { background:#fff; border:1px solid #f1e9ee; border-radius:20px; }
    .dash-hero  { background:radial-gradient(120% 140% at 0% 0%, #4a0e2e 0%, #2a0a1c 55%, #1b0713 100%); border-radius:24px; }
    .dash-row:hover { background:#fdf7fa; }
    .dash-kpi { transition: border-color .2s, box-shadow .2s; }
    .dash-kpi:hover { border-color:#f5c2d9; box-shadow:0 10px 30px -18px rgba(219,39,119,.45); }
    @keyframes dashIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:none; } }
    .dash-in { animation: dashIn .6s cubic-bezier(.22,1,.36,1) both; }
    @media (prefers-reduced-motion: reduce) { .dash-in { animation:none; } }
</style>

@php
    $totalPedidosEstado = $pedidosPorEstado->sum();
    $alertasStock = $sinStock + $stockBajo;
    $hora = now()->hour;
    $saludo = $hora < 12 ? '¡Buenos días' : ($hora < 19 ? '¡Buenas tardes' : '¡Buenas noches');

    // Categorías: paleta compartida entre gráfico y lista
    $paleta = ['#db2777', '#7c3aed', '#0ea5e9', '#f59e0b', '#10b981', '#f43f5e'];
    $totalCategorias = $ventasPorCategoria->sum('total') ?: 1;

    // Productos: el primero destacado
    $primero = $topProductos->first();
    $resto = $topProductos->slice(1)->values();
    $maxUnidades = $topProductos->max('unidades') ?: 1;
@endphp

{{-- ───────── Encabezado ───────── --}}
<div class="dash-in flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-7">
    <div>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-pink-50 text-pink-700 text-xs font-bold mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span>
            Panel de control
        </span>
        <h1 class="text-3xl md:text-4xl font-extrabold text-[#2a0a1c] tracking-tight">{{ $saludo }}, bienvenido de nuevo!</h1>
        <p class="text-[15px] text-gray-500 mt-1.5">
            Este es el resumen de tu tienda.
            @if($pedidosHoy > 0)
                Hoy llevas <span class="font-bold text-pink-600">{{ $pedidosHoy }} {{ $pedidosHoy == 1 ? 'pedido nuevo' : 'pedidos nuevos' }}</span>.
            @endif
        </p>
    </div>

    <div class="flex items-center gap-3 self-start sm:self-auto bg-white border border-[#f1e9ee] rounded-2xl pl-5 pr-2 py-2">
        <div class="text-right">
            <p class="text-xs font-semibold text-gray-400">{{ now()->translatedFormat('l') }}</p>
            <p class="text-sm font-extrabold text-[#2a0a1c]">{{ now()->translatedFormat('d \d\e F, Y') }}</p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center shadow-lg shadow-pink-200">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
    </div>
</div>

{{-- ───────── KPIs: 3 tarjetas horizontales ───────── --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">

    {{-- Pendientes --}}
    <div class="dash-panel dash-kpi dash-in p-6 flex items-center gap-5" style="animation-delay:.04s">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center shrink-0">
            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-gray-500">Pedidos pendientes</p>
            <p class="text-4xl font-extrabold text-[#2a0a1c] leading-none mt-1.5">{{ $pedidosPendientes }}</p>
            <p class="text-xs text-gray-400 mt-2">
                @if($pedidosHoy > 0) <span class="font-semibold text-amber-600">+{{ $pedidosHoy }} hoy</span> · @endif
                Por procesar
            </p>
        </div>
    </div>

    {{-- Clientes --}}
    <div class="dash-panel dash-kpi dash-in p-6 flex items-center gap-5" style="animation-delay:.08s">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500">Clientes registrados</p>
            <p class="text-4xl font-extrabold text-[#2a0a1c] leading-none mt-1.5">{{ number_format($totalClientes) }}</p>
            <p class="text-xs text-gray-400 mt-2">En total</p>
        </div>
    </div>

    {{-- Stock --}}
    <div class="dash-panel dash-kpi dash-in p-6 flex items-center gap-5" style="animation-delay:.12s">
        <div class="w-14 h-14 rounded-2xl {{ $alertasStock > 0 ? 'bg-rose-50' : 'bg-emerald-50' }} flex items-center justify-center shrink-0">
            <svg class="w-7 h-7 {{ $alertasStock > 0 ? 'text-rose-600' : 'text-emerald-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-gray-500">Productos con alerta</p>
            <p class="text-4xl font-extrabold text-[#2a0a1c] leading-none mt-1.5">{{ $alertasStock }}</p>
            <p class="text-xs text-gray-400 mt-2">
                <span class="font-semibold text-rose-500">{{ $sinStock }}</span> agotados ·
                <span class="font-semibold text-amber-600">{{ $stockBajo }}</span> con stock bajo
            </p>
        </div>
    </div>
</div>

{{-- ───────── Ventas + estado de pedidos ───────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    <div class="dash-hero dash-in xl:col-span-2 p-6 md:p-8 text-white" style="animation-delay:.16s">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-pink-200/80 font-medium">Ventas de este mes</p>
                <div class="flex items-end gap-3 mt-2">
                    <p class="text-4xl md:text-5xl font-extrabold tracking-tight">S/ {{ number_format($ventasMesActual, 0) }}</p>
                    @if($crecimientoVentas >= 0)
                        <span class="mb-1.5 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-400/15 text-emerald-300">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                            {{ $crecimientoVentas }}%
                        </span>
                    @else
                        <span class="mb-1.5 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-400/15 text-rose-300">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            {{ abs($crecimientoVentas) }}%
                        </span>
                    @endif
                </div>
                <p class="text-xs text-pink-200/60 mt-1.5">Comparado con el mes anterior</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-pink-200/80 font-medium">Últimos 30 días</p>
                <p class="text-xl font-bold mt-2">S/ {{ number_format($totalVentas, 2) }}</p>
            </div>
        </div>
        <div class="h-64 mt-6"><canvas id="ventasChart"></canvas></div>
    </div>

    <div class="dash-panel dash-in p-6" style="animation-delay:.2s">
        <h3 class="text-base font-extrabold text-[#2a0a1c]">Estado de pedidos</h3>
        <p class="text-xs text-gray-400 mt-0.5 mb-4">Distribución actual</p>
        <div class="relative h-56">
            <canvas id="donutChart"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-3xl font-extrabold text-[#2a0a1c]">{{ $totalPedidosEstado }}</span>
                <span class="text-xs text-gray-400">pedidos</span>
            </div>
        </div>
        <div id="donutLegend" class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2"></div>
    </div>
</div>

{{-- ───────── Ventas por categoría + más vendidos ───────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    <div class="dash-panel dash-in xl:col-span-2 p-6" style="animation-delay:.24s">
    <div class="mb-6">
        <h3 class="text-base font-extrabold text-[#2a0a1c]">Ventas por categoría</h3>
        <p class="text-xs text-gray-400 mt-0.5">Acumulado histórico, top 6</p>
    </div>

    @if($ventasPorCategoria->count())
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-center">
            <div class="md:col-span-2 h-64"><canvas id="catChart"></canvas></div>

            <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($ventasPorCategoria as $i => $cat)
                    @php $pct = round(($cat->total / $totalCategorias) * 100); @endphp
                    <div class="rounded-2xl border border-gray-100 p-4 hover:border-pink-200 transition-colors">
                        <div class="flex items-center justify-between gap-2">
                            <span class="flex items-center gap-2 min-w-0">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $paleta[$i % count($paleta)] }}"></span>
                                <span class="text-sm font-semibold text-gray-800 truncate">{{ $cat->nombre_categoria }}</span>
                            </span>
                            <span class="text-xs font-bold text-gray-400">{{ $pct }}%</span>
                        </div>
                        <p class="text-xl font-extrabold text-[#2a0a1c] mt-2">S/ {{ number_format($cat->total, 0) }}</p>
                        <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden mt-3">
                            <div class="h-full rounded-full" style="width: {{ $pct }}%; background: {{ $paleta[$i % count($paleta)] }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-10">
            <p class="text-sm font-semibold text-gray-500">Sin datos por categoría</p>
            <p class="text-xs text-gray-400 mt-1">Las categorías aparecerán cuando registres ventas.</p>
        </div>
    @endif
</div>

    {{-- Más vendidos: destacado + lista --}}
    <div class="dash-panel dash-in p-6" style="animation-delay:.28s">
        <h3 class="text-base font-extrabold text-[#2a0a1c]">Más vendidos</h3>
        <p class="text-xs text-gray-400 mt-0.5 mb-4">Por unidades vendidas</p>

        @if($primero)
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-pink-50 to-rose-50 mb-4">
                <div class="h-44 flex items-center justify-center">
                    @if($primero->imagen)
                        <img src="{{ url('/api/imagen/' . $primero->imagen) }}" alt="{{ $primero->nombre_producto }}"
                             class="w-full h-full object-cover" onerror="this.style.display='none'">
                    @endif
                </div>
                <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/95 text-[11px] font-extrabold text-pink-600 shadow-sm">
                    🏆 Número 1
                </span>
                <div class="absolute inset-x-0 bottom-0 p-4 pt-10 bg-gradient-to-t from-[#2a0a1c]/85 to-transparent">
                    <p class="text-sm font-bold text-white truncate">{{ $primero->nombre_producto }}</p>
                    <p class="text-xs text-pink-100/90">{{ $primero->unidades }} unidades vendidas</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($resto as $i => $prod)
                    <div class="flex items-center gap-3">
                        <span class="w-5 text-center text-xs font-extrabold text-gray-300">{{ $i + 2 }}</span>
                        @if($prod->imagen)
                            <img src="{{ url('/api/imagen/' . $prod->imagen) }}" alt="{{ $prod->nombre_producto }}"
                                 class="w-10 h-10 rounded-xl object-cover border border-gray-100 shrink-0" onerror="this.style.visibility='hidden'">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-gray-50 shrink-0"></div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-[13px] font-semibold text-gray-800 truncate">{{ $prod->nombre_producto }}</span>
                                <span class="text-xs font-extrabold text-gray-900 shrink-0">{{ $prod->unidades }}</span>
                            </div>
                            <div class="h-1 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full bg-pink-500" style="width: {{ ($prod->unidades / $maxUnidades) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10">
                <p class="text-sm font-semibold text-gray-500">Sin ventas registradas</p>
                <p class="text-xs text-gray-400 mt-1">Los productos más vendidos aparecerán aquí.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
    Chart.defaults.color = '#9ca3af';

    const tooltip = {
        backgroundColor: '#1b0713', titleColor: '#f9a8d4', bodyColor: '#fff',
        padding: 12, cornerRadius: 10, displayColors: false,
        titleFont: { size: 11, weight: '600' }, bodyFont: { size: 13, weight: '700' }
    };

    // ── Ventas 30 días
    new Chart(document.getElementById('ventasChart'), {
        type: 'line',
        data: {
            labels: @json($diasCompletos->pluck('dia')),
            datasets: [{
                data: @json($diasCompletos->pluck('total')),
                borderColor: '#f472b6',
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260);
                    g.addColorStop(0, 'rgba(244,114,182,.35)');
                    g.addColorStop(1, 'rgba(244,114,182,0)');
                    return g;
                },
                borderWidth: 2.5, fill: true, tension: .4, pointRadius: 0,
                pointHoverRadius: 6, pointHoverBackgroundColor: '#f472b6',
                pointHoverBorderColor: '#fff', pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: { ...tooltip, callbacks: { label: c => ' S/ ' + c.parsed.y.toFixed(2) } } },
            scales: {
                x: { grid: { display: false }, border: { display: false }, ticks: { maxTicksLimit: 7, color: 'rgba(251,207,232,.55)', font: { size: 11 } } },
                y: { grid: { color: 'rgba(255,255,255,.07)' }, border: { display: false }, beginAtZero: true, ticks: { color: 'rgba(251,207,232,.55)', font: { size: 11 }, callback: v => 'S/' + v } }
            }
        }
    });

    // ── Estado de pedidos
    const estadoColor = {
        'Pendiente': '#fbbf24', 'Pagado': '#60a5fa', 'Enviado': '#a78bfa',
        'En Agencia': '#fb923c', 'Entregado': '#34d399', 'Cancelado': '#f87171'
    };
    const labels = @json($pedidosPorEstado->keys());
    const values = @json($pedidosPorEstado->values());
    const colors = labels.map(l => estadoColor[l] || '#94a3b8');

    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: { labels, datasets: [{ data: values, backgroundColor: colors, borderColor: '#fff', borderWidth: 4, hoverOffset: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '74%', plugins: { legend: { display: false }, tooltip } }
    });

    document.getElementById('donutLegend').innerHTML = labels.map((l, i) => `
        <div class="flex items-center justify-between text-xs">
            <span class="flex items-center gap-2 text-gray-600">
                <span class="w-2 h-2 rounded-full" style="background:${colors[i]}"></span>${l}
            </span>
            <span class="font-bold text-gray-900">${values[i]}</span>
        </div>`).join('');

    // ── Ventas por categoría (área polar)
    const catCanvas = document.getElementById('catChart');
    if (catCanvas) {
        const paleta = @json($paleta);
        const catData = @json($ventasPorCategoria->pluck('total'));
        new Chart(catCanvas, {
            type: 'polarArea',
            data: {
                labels: @json($ventasPorCategoria->pluck('nombre_categoria')),
                datasets: [{
                    data: catData,
                    backgroundColor: catData.map((_, i) => paleta[i % paleta.length] + 'cc'),
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { ...tooltip, displayColors: true, callbacks: { label: c => ' S/ ' + Number(c.parsed.r).toFixed(0) } } },
                scales: { r: { ticks: { display: false }, grid: { color: '#f3f4f6' }, angleLines: { display: false }, border: { display: false } } }
            }
        });
    }
});
</script>

@endsection