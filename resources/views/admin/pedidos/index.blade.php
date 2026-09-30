@extends('admin.layout')

@section('content')

    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* ===== Calendario compacto, en español, comienza en domingo, 6 filas fijas ===== */
        .flatpickr-calendar {
            width: 252px !important;
            padding: 0 !important;
            font-size: 11px !important;
            border-radius: 14px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12) !important;
        }

        /* Quitar la flechita decorativa (bocadillo) del calendario */
        .flatpickr-calendar::before,
        .flatpickr-calendar::after,
        .flatpickr-calendar.arrowTop::before,
        .flatpickr-calendar.arrowTop::after,
        .flatpickr-calendar.arrowBottom::before,
        .flatpickr-calendar.arrowBottom::after,
        .flatpickr-calendar.arrowLeft::before,
        .flatpickr-calendar.arrowLeft::after,
        .flatpickr-calendar.arrowRight::before,
        .flatpickr-calendar.arrowRight::after {
            display: none !important;
            border: none !important;
        }

        /* Cabecera */
        .flatpickr-calendar .flatpickr-months {
            padding: 6px 0 !important;
        }
        .flatpickr-calendar .flatpickr-month {
            height: 28px !important;
        }
        .flatpickr-calendar .flatpickr-current-month {
            font-size: 12px !important;
            padding: 0 !important;
            height: 28px !important;
            line-height: 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            color: #374151 !important;
            font-weight: 600 !important;
            pointer-events: none !important;
            cursor: default !important;
        }
        .flatpickr-calendar .flatpickr-current-month:hover,
        .flatpickr-calendar .flatpickr-current-month *:hover {
            background: transparent !important;
            color: #374151 !important;
        }

        /* Mes y año como texto plano */
        .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-calendar .flatpickr-current-month input.cur-year,
        .flatpickr-calendar .flatpickr-current-month .numInputWrapper {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #374151 !important;
            padding: 0 !important;
            margin: 0 !important;
            pointer-events: none !important;
            cursor: default !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
        }
        .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months:hover,
        .flatpickr-calendar .flatpickr-current-month input.cur-year:hover,
        .flatpickr-calendar .flatpickr-current-month .numInputWrapper:hover {
            background: transparent !important;
            color: #374151 !important;
        }

        /* Ocultar flechitas del input de año */
        .flatpickr-calendar .flatpickr-current-month .numInputWrapper span.arrowUp,
        .flatpickr-calendar .flatpickr-current-month .numInputWrapper span.arrowDown {
            display: none !important;
        }

        /* Flechas prev/next del calendario */
        .flatpickr-calendar .flatpickr-prev-month,
        .flatpickr-calendar .flatpickr-next-month {
            padding: 6px !important;
            height: 28px !important;
            line-height: 28px !important;
            top: 6px !important;
            color: #6366f1 !important;
        }
        .flatpickr-calendar .flatpickr-prev-month svg,
        .flatpickr-calendar .flatpickr-next-month svg {
            width: 12px !important;
            height: 12px !important;
            fill: #6366f1 !important;
        }
        .flatpickr-calendar .flatpickr-prev-month:hover svg,
        .flatpickr-calendar .flatpickr-next-month:hover svg {
            fill: #4338ca !important;
        }

        /* Días de la semana */
        .flatpickr-calendar .flatpickr-weekdays {
            height: 24px !important;
            background: #f9fafb !important;
        }
        .flatpickr-calendar .flatpickr-weekday {
            font-size: 10px !important;
            font-weight: 600 !important;
            line-height: 24px !important;
            color: #6b7280 !important;
        }

        /* Contenedor: 6 filas fijas (6 × 30px = 180px) */
        .flatpickr-calendar .flatpickr-days {
            width: 252px !important;
            height: 180px !important;
            overflow: hidden !important;
        }
        .flatpickr-calendar .dayContainer {
            width: 252px !important;
            min-width: 252px !important;
            max-width: 252px !important;
            height: 180px !important;
            overflow: hidden !important;
        }

        /* Días: círculo perfecto de 28×28 */
        .flatpickr-calendar .flatpickr-day {
            width: 28px !important;
            height: 28px !important;
            max-width: 28px !important;
            flex-basis: 28px !important;
            line-height: 28px !important;
            font-size: 11px !important;
            margin: 1px 4px !important;
            border-radius: 50% !important;
            color: #374151 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .flatpickr-calendar .flatpickr-day.prevMonthDay,
        .flatpickr-calendar .flatpickr-day.nextMonthDay {
            color: #d1d5db !important;
        }
        .flatpickr-calendar .flatpickr-day.today {
            border-color: #6366f1 !important;
            color: #6366f1 !important;
            font-weight: 700 !important;
        }
        .flatpickr-calendar .flatpickr-day.selected {
            background: #6366f1 !important;
            border-color: #6366f1 !important;
            color: white !important;
            font-weight: 700 !important;
            border-radius: 50% !important;
        }
        .flatpickr-calendar .flatpickr-day:hover {
            background: #eef2ff !important;
            border-radius: 50% !important;
        }
    </style>

    <div>

        {{-- Header de la Sección --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Pedidos</h1>
                <p class="text-gray-500 mt-2 text-lg font-medium">Gestiona y supervisa el flujo de ventas de tu tienda.</p>
            </div>
        </div>

        {{-- Buscador + Filtros --}}
        <form action="{{ route('admin.pedidos.index') }}" method="GET"
            x-data="{
                estado: '',
                estadoTexto: 'Estado',
                tipoEntrega: '',
                tipoEntregaTexto: 'Tipo de entrega',

                applyFilter() {
                    const form = $el;
                    const params = new URLSearchParams();

                    const buscar = form.querySelector('input[name=buscar]').value.trim();
                    if (buscar) params.append('buscar', buscar);
                    if (this.estado) params.append('estado', this.estado);
                    if (this.tipoEntrega) params.append('tipo_entrega', this.tipoEntrega);

                    const fecha = form.querySelector('input[name=fecha]').value;
                    if (fecha) params.append('fecha', fecha);

                    // Limpiar filtros
                    form.querySelector('input[name=buscar]').value = '';
                    this.estado = '';
                    this.estadoTexto = 'Estado';
                    this.tipoEntrega = '';
                    this.tipoEntregaTexto = 'Tipo de entrega';
                    form.querySelector('input[name=fecha]').value = '';
                    document.getElementById('fechaTexto').textContent = 'Fecha';

                    window.location.href = form.action + '?' + params.toString();
                }
            }"
            x-on:submit.prevent="applyFilter()"
            class="mb-6 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4 w-full flex-wrap">

                {{-- Buscador --}}
                <span class="text-sm font-bold text-gray-700 shrink-0">Buscar:</span>
                <div class="relative flex-1 min-w-[295px] max-w-[400px]">
                    <span class="absolute inset-y-0 left-4 flex items-center text-gray-800">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                    </span>
                    <input type="text" name="buscar" value="" placeholder="Buscar por código o cliente..."
                        class="w-full pl-12 pr-4 py-1.5 bg-[#f1f1f1] border border-gray-200 rounded-full text-gray-800 placeholder-gray-800 focus:outline-none focus:border-gray-400 transition-colors text-sm">
                </div>

                {{-- Etiqueta Filtros --}}
                <span class="text-sm font-bold text-gray-700 shrink-0">Filtros:</span>

                {{-- Select custom: Estado --}}
                <div x-data="{ open: false }" class="relative w-full max-w-[180px]">
                    <input type="hidden" name="estado" :value="estado">

                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between gap-2 pl-4 pr-4 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-sm text-gray-600 cursor-pointer focus:outline-none focus:border-gray-400 transition-colors">
                        <span x-text="estadoTexto"></span>
                        <x-heroicon-o-chevron-down class="w-4 h-4 shrink-0" />
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">
                        <div class="max-h-64 overflow-y-auto py-1">
                            <button type="button" @click="estado = 'Pendiente'; estadoTexto = 'Pendiente'; open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-600 transition">
                                Pendiente
                            </button>
                            <button type="button" @click="estado = 'En camino'; estadoTexto = 'En camino'; open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-600 transition">
                                En camino
                            </button>
                            <button type="button" @click="estado = 'Listo para recoger'; estadoTexto = 'Listo para recoger'; open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-600 transition">
                                Listo para recoger
                            </button>
                            <button type="button" @click="estado = 'Entregado'; estadoTexto = 'Entregado'; open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-600 transition">
                                Entregado
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Select custom: Tipo de entrega --}}
                <div x-data="{ open: false }" class="relative w-full max-w-[180px]">
                    <input type="hidden" name="tipo_entrega" :value="tipoEntrega">

                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between gap-2 pl-4 pr-4 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-sm text-gray-600 cursor-pointer focus:outline-none focus:border-gray-400 transition-colors">
                        <span x-text="tipoEntregaTexto"></span>
                        <x-heroicon-o-chevron-down class="w-4 h-4 shrink-0" />
                    </button>

                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">
                        <div class="max-h-64 overflow-y-auto py-1">
                            <button type="button" @click="tipoEntrega = '1'; tipoEntregaTexto = 'Retiro en tienda'; open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-600 transition">
                                Retiro en tienda
                            </button>
                            <button type="button" @click="tipoEntrega = '2'; tipoEntregaTexto = 'Envío a provincia'; open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-600 transition">
                                Envío a provincia
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Filtro: Fecha (calendario) --}}
                <div class="relative w-full max-w-[180px]">
                    <input type="text" name="fecha" id="fechaFiltro"
                           value=""
                           class="sr-only" readonly>

                    <button type="button" id="fechaBtn"
                        class="w-full flex items-center justify-between gap-2 pl-4 pr-4 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-sm text-gray-600 cursor-pointer focus:outline-none focus:border-gray-400 transition-colors">
                        <span id="fechaTexto">Fecha</span>
                        <x-heroicon-o-chevron-down class="w-4 h-4 shrink-0" />
                    </button>
                </div>

                {{-- Espaciador --}}
                <div class="flex-1"></div>

                {{-- Botón Filtrar --}}
                <button type="submit"
                    class="shrink-0 px-6 py-1.5 bg-indigo-600 text-white rounded-full font-bold text-sm transition-colors duration-200">
                    Filtrar
                </button>

            </div>
        </form>

        {{-- Contenedor de la Tabla --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#f1f1f1]">
                            <th class="px-8 py-5 text-base font-black text-gray-800 text-left border-b border-gray-200">Código</th>
                            <th class="px-8 py-5 text-base font-black text-gray-800 text-left border-b border-gray-200">Cliente</th>
                            <th class="px-8 py-5 text-base font-black text-gray-800 text-center border-b border-gray-200">Fecha</th>
                            <th class="px-8 py-5 text-base font-black text-gray-800 text-center border-b border-gray-200">Tipo de entrega</th>
                            <th class="px-8 py-5 text-base font-black text-gray-800 text-center border-b border-gray-200">Estado</th>
                            <th class="px-8 py-5 text-base font-black text-gray-800 text-right border-b border-gray-200">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($pedidos as $pedido)
                            <tr class="odd:bg-white even:bg-[#f1f1f1]/40">
                                <td class="px-8 py-5">
                                    <p class="font-normal text-gray-800 text-base leading-tight">
                                        {{ $pedido->numero_pedido }}
                                    </p>
                                </td>

                                <td class="px-8 py-5">
                                    <p class="font-normal text-gray-800 text-base leading-tight">
                                        {{ $pedido->usuario->nombres }} {{ $pedido->usuario->apellidos }}
                                    </p>
                                </td>

                                <td class="px-8 py-5 text-center">
                                    <span class="font-normal text-gray-800 text-base">
                                        {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}
                                    </span>
                                </td>

                                <td class="px-8 py-5 text-center">
                                    <span class="font-normal text-gray-800 text-base">
                                        {{ $pedido->tipoEntrega?->nombre_tipo_entrega ?? '—' }}
                                    </span>
                                </td>

                                <td class="px-8 py-5 text-center">
                                    @php
                                        $estilos = [
                                            'Pendiente'          => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'En camino'          => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'Listo para recoger' => 'bg-violet-50 text-violet-600 border-violet-100',
                                            'Entregado'          => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        ];
                                        $estilo = $estilos[$pedido->estado_pedido] ?? 'bg-gray-100 text-gray-500 border-gray-200';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $estilo }}">
                                        {{ $pedido->estado_pedido }}
                                    </span>
                                </td>

                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('admin.pedidos.show', $pedido->id_pedido) }}"
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                        Ver detalles
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-12 text-center text-gray-400 text-sm">
                                    No hay pedidos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($pedidos->hasPages())
                <div class="px-8 py-6 bg-[#f1f1f1]/50 border-t border-gray-200">
                    {{ $pedidos->links() }}
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Flatpickr JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputFecha = document.getElementById('fechaFiltro');
            const botonFecha = document.getElementById('fechaBtn');
            const textoFecha = document.getElementById('fechaTexto');

            flatpickr.localize(flatpickr.l10ns.es);
            flatpickr.l10ns.es.firstDayOfWeek = 0;

            const picker = flatpickr(inputFecha, {
                dateFormat: 'Y-m-d',
                defaultDate: inputFecha.value || null,
                positionElement: botonFecha,
                locale: 'es',
                firstDayOfWeek: 0,
                monthSelectorType: 'static',
                yearSelectorType: 'input',
                onChange: function (selectedDates, dateStr) {
                    if (dateStr) {
                        const [y, m, d] = dateStr.split('-');
                        textoFecha.textContent = `${d}/${m}/${y}`;
                    } else {
                        textoFecha.textContent = 'Fecha';
                    }
                },
                onOpen: function () {
                    botonFecha.classList.add('border-indigo-400', 'ring-2', 'ring-indigo-100');
                },
                onClose: function () {
                    botonFecha.classList.remove('border-indigo-400', 'ring-2', 'ring-indigo-100');
                }
            });

            botonFecha.addEventListener('click', function (e) {
                e.preventDefault();
                picker.open();
            });
        });
    </script>

@endsection