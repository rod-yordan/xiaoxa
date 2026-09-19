@extends('admin.layout')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
    body { font-family: 'DM Sans', sans-serif; }
    .font-syne { font-family: 'Syne', sans-serif; }
    @keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    .fade-up { animation: fadeUp 0.45s ease forwards; }
    .d1 { animation-delay:0.05s; opacity:0; }
    .d2 { animation-delay:0.10s; opacity:0; }
    .d3 { animation-delay:0.15s; opacity:0; }
    .d4 { animation-delay:0.20s; opacity:0; }
    .d5 { animation-delay:0.25s; opacity:0; }
    .d6 { animation-delay:0.30s; opacity:0; }
</style>

{{-- ── HEADER con saludo del mockup ────────────────────────────── --}}
<div class="fade-up d1 mb-8 text-right">
    <p class="text-lg font-medium text-gray-900">¡Hola, Administrador!</p>
    <p class="text-2xl font-bold text-gray-900">¡Bienvenido de nuevo!</p>
</div>

{{-- ── KPIs ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="fade-up d1 bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Ventas este mes</p>
                <p class="font-syne text-2xl font-bold text-gray-950 mt-2">S/ {{ number_format($ventasMesActual, 0) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 flex items-center gap-2">
            @if($crecimientoVentas >= 0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">↑ {{ $crecimientoVentas }}%</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">↓ {{ abs($crecimientoVentas) }}%</span>
            @endif
            <span class="text-xs text-gray-400">vs mes anterior</span>
        </div>
    </div>

    <div class="fade-up d2 bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Pendientes</p>
                <p class="font-syne text-2xl font-bold text-gray-950 mt-2">{{ $pedidosPendientes }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <span class="text-xs text-gray-400">{{ $pedidosHoy }} nuevos hoy</span>
        </div>
    </div>

    <div class="fade-up d3 bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Clientes</p>
                <p class="font-syne text-2xl font-bold text-gray-950 mt-2">{{ number_format($totalClientes) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <span class="text-xs text-gray-400">Registrados en total</span>
        </div>
    </div>

    <div class="fade-up d4 bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Alertas stock</p>
                <p class="font-syne text-2xl font-bold text-gray-950 mt-2">{{ $sinStock + $stockBajo }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 flex gap-3">
            <span class="text-xs text-red-500">{{ $sinStock }} sin stock</span>
            <span class="text-xs text-yellow-600">{{ $stockBajo }} stock bajo</span>
        </div>
    </div>

</div>

{{-- ── GRÁFICOS PRINCIPALES ─────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    {{-- Línea — Ventas 30 días --}}
    <div class="fade-up d3 bg-white rounded-2xl border border-gray-100 p-6 lg:col-span-2 hover:shadow-lg transition-all"
         x-data x-init="
            new Chart($refs.ventasChart, {
                type: 'line',
                data: {
                    labels: {{ Js::from($diasCompletos->pluck('dia')) }},
                    datasets: [{
                        label: 'Ventas (S/)',
                        data: {{ Js::from($diasCompletos->pluck('total')) }},
                        borderColor: '#0a0a0f',
                        backgroundColor: (ctx) => {
                            const g = ctx.chart.ctx.createLinearGradient(0,0,0,260);
                            g.addColorStop(0, 'rgba(10,10,15,0.10)');
                            g.addColorStop(1, 'rgba(10,10,15,0)');
                            return g;
                        },
                        borderWidth: 2.5, fill: true, tension: 0.45,
                        pointRadius: 0, pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#bef264',
                        pointHoverBorderColor: '#0a0a0f', pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#0a0a0f', titleColor: '#9ca3af', bodyColor: '#bef264', padding: 12, cornerRadius: 10, callbacks: { label: c => ' S/ ' + c.parsed.y.toFixed(2) } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 11 }, color: '#94a3b8' } },
                        y: { grid: { color: '#f8fafc' }, ticks: { font: { size: 11 }, color: '#94a3b8', callback: v => 'S/'+v } }
                    }
                }
            });
         ">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-syne font-semibold text-base text-gray-900">Ventas — últimos 30 días</h3>
                <p class="text-xs text-gray-400 mt-0.5">Total acumulado: S/ {{ number_format($totalVentas, 2) }}</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span> En vivo
            </span>
        </div>
        <canvas x-ref="ventasChart" height="110"></canvas>
    </div>

    {{-- Donut — Estado pedidos --}}
    <div class="fade-up d4 bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg transition-all"
         x-data x-init="
            new Chart($refs.donutChart, {
                type: 'doughnut',
                data: {
                    labels: {{ Js::from($pedidosPorEstado->keys()) }},
                    datasets: [{
                        data: {{ Js::from($pedidosPorEstado->values()) }},
                        backgroundColor: ['#fbbf24','#60a5fa','#a78bfa','#f97316','#34d399','#f87171'],
                        borderColor: '#ffffff', borderWidth: 3, hoverOffset: 6
                    }]
                },
                options: {
                    cutout: '68%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11, family: 'DM Sans' }, padding: 12, usePointStyle: true, pointStyleWidth: 8 } },
                        tooltip: { backgroundColor: '#0a0a0f', bodyColor: '#fff', padding: 10, cornerRadius: 8 }
                    }
                }
            });
         ">
        <h3 class="font-syne font-semibold text-base text-gray-900 mb-1">Estado de pedidos</h3>
        <p class="text-xs text-gray-400 mb-5">Distribución actual</p>
        <canvas x-ref="donutChart"></canvas>
    </div>

</div>

{{-- ── SEGUNDA FILA ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    {{-- Barras — Ventas por categoría --}}
    <div class="fade-up d4 bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg transition-all"
         x-data x-init="
            new Chart($refs.catChart, {
                type: 'bar',
                data: {
                    labels: {{ Js::from($ventasPorCategoria->pluck('nombre_categoria')) }},
                    datasets: [{
                        data: {{ Js::from($ventasPorCategoria->pluck('total')) }},
                        backgroundColor: ['#0a0a0f','#1e293b','#334155','#475569','#64748b','#94a3b8'],
                        borderRadius: 8, borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y', responsive: true,
                    plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0a0a0f', bodyColor: '#bef264', padding:10, cornerRadius:8, callbacks: { label: c => ' S/ ' + c.parsed.x.toFixed(0) } } },
                    scales: {
                        x: { grid: { color: '#f8fafc' }, ticks: { font: { size:11 }, color:'#94a3b8', callback: v => 'S/'+v } },
                        y: { grid: { display:false }, ticks: { font: { size:11 }, color:'#374151' } }
                    }
                }
            });
         ">
        <h3 class="font-syne font-semibold text-base text-gray-900 mb-1">Ventas por categoría</h3>
        <p class="text-xs text-gray-400 mb-5">Acumulado histórico</p>
        <canvas x-ref="catChart"></canvas>
    </div>

    {{-- Top productos --}}
    <div class="fade-up d5 bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg transition-all">
        <h3 class="font-syne font-semibold text-base text-gray-900 mb-1">Top productos</h3>
        <p class="text-xs text-gray-400 mb-5">Más vendidos por unidades</p>

        @php
            $maxUnidades = $topProductos->max('unidades') ?: 1;
            $barColors = ['bg-gray-950','bg-gray-700','bg-gray-500','bg-gray-400','bg-gray-300'];
        @endphp

        <div class="space-y-4">
            @forelse($topProductos as $i => $prod)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="font-syne font-bold text-xs w-5 text-right shrink-0 text-gray-400">{{ $i+1 }}</span>
                        @if($prod->imagen)
                            <img src="{{ url('/api/imagen/' . $prod->imagen) }}"
                                 class="w-8 h-8 rounded-lg object-cover shrink-0 border border-gray-100"
                                 alt="{{ $prod->nombre_producto }}"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <div class="w-8 h-8 rounded-lg shrink-0 bg-gray-100 hidden items-center justify-center">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-lg shrink-0 bg-gray-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <span class="text-xs font-medium truncate text-gray-800">{{ $prod->nombre_producto }}</span>
                    </div>
                    <span class="text-xs font-bold shrink-0 ml-2 text-gray-900">{{ $prod->unidades }} uds</span>
                </div>
                <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden ml-7">
                    <div class="h-full rounded-full transition-all duration-1000 {{ $barColors[$i] ?? 'bg-gray-300' }}"
                         style="width:{{ ($prod->unidades / $maxUnidades) * 100 }}%"></div>
                </div>
            </div>
            @empty
                <p class="text-sm text-center py-8 text-gray-400">Sin datos aún</p>
            @endforelse
        </div>
    </div>

    {{-- Últimos pedidos --}}
    <div class="fade-up d6 bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-syne font-semibold text-base text-gray-900">Últimos pedidos</h3>
                <p class="text-xs text-gray-400 mt-0.5">8 más recientes</p>
            </div>
            <a href="{{ route('admin.pedidos.index') }}" class="text-xs font-semibold text-gray-900 hover:underline">
                Ver todos →
            </a>
        </div>

        <div class="space-y-2.5">
            @forelse($ultimosPedidos as $pedido)
            @php
                $badgeClass = match($pedido->estado_pedido) {
                    'Pendiente'  => 'bg-yellow-100 text-yellow-700',
                    'Pagado'     => 'bg-blue-100 text-blue-700',
                    'Enviado'    => 'bg-purple-100 text-purple-700',
                    'En Agencia' => 'bg-orange-100 text-orange-700',
                    'Entregado'  => 'bg-green-100 text-green-700',
                    'Cancelado'  => 'bg-red-100 text-red-700',
                    default      => 'bg-gray-100 text-gray-600'
                };
            @endphp
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-900 truncate">
                        #{{ $pedido->numero_pedido ?? $pedido->id_pedido }}
                    </p>
                    <p class="text-xs text-gray-400 truncate">
                        {{ $pedido->nombres ?? 'Cliente' }} · {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->diffForHumans() }}
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0 ml-3">
                    <span class="text-xs font-bold text-gray-900">S/ {{ number_format($pedido->total_pedido, 0) }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                        {{ $pedido->estado_pedido }}
                    </span>
                </div>
            </div>
            @empty
                <p class="text-sm text-center py-6 text-gray-400">Sin pedidos registrados</p>
            @endforelse
        </div>
    </div>

</div>

@endsection