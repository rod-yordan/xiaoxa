@extends('admin.layout')

@section('content')

    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .flatpickr-calendar {
            width: 252px !important;
            padding: 0 !important;
            font-size: 11px !important;
            border-radius: 14px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12) !important;
        }
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
        .flatpickr-calendar .flatpickr-months { padding: 6px 0 !important; }
        .flatpickr-calendar .flatpickr-month { height: 28px !important; }
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
        }
        .flatpickr-calendar .flatpickr-current-month .numInputWrapper span.arrowUp,
        .flatpickr-calendar .flatpickr-current-month .numInputWrapper span.arrowDown {
            display: none !important;
        }
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
        .flatpickr-calendar .flatpickr-day.nextMonthDay { color: #d1d5db !important; }
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

    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Detalles del Pedido</h1>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.pedidos.index') }}"
                    class="px-14 py-3.5 bg-gray-100 text-gray-700 border border-gray-200 text-sm font-bold tracking-wider rounded-full transition-colors duration-200">
                    Cancelar
                </a>

                <button type="submit" form="form-actualizar-pedido"
                    class="px-14 py-3.5 bg-indigo-600 text-white text-sm font-bold tracking-wider rounded-full transition-colors duration-200">
                    Aceptar
                </button>
            </div>
        </div>

        <form id="form-actualizar-pedido" action="{{ route('admin.pedidos.update', $pedido->id_pedido) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ============================================= --}}
                {{-- COLUMNA IZQUIERDA: PEDIDO --}}
                {{-- ============================================= --}}
                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">

                        <div class="flex items-center justify-between gap-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                    <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Pedido</h2>
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-900 rounded-lg text-sm font-bold">{{ $pedido->numero_pedido }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <p class="text-[14px] font-bold text-gray-800">Fecha:</p>
                                <span class="text-[14px] text-gray-800">{{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        {{-- Datos del cliente --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="flex items-center gap-2">
                                <label class="text-[14px] font-bold text-gray-800 shrink-0">Cliente:</label>
                                <input type="text" readonly
                                    value="{{ $pedido->usuario->nombres }} {{ $pedido->usuario->apellidos }}"
                                    class="w-full px-3 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-[14px] text-gray-600 focus:outline-none">
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="text-[14px] font-bold text-gray-800 shrink-0">Teléfono:</label>
                                <input type="text" readonly
                                    value="{{ $pedido->usuario->telefono ?? 'N/A' }}"
                                    class="w-full px-3 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-[14px] text-gray-600 focus:outline-none">
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="text-[14px] font-bold text-gray-800 shrink-0">Tipo de documento:</label>
                                <input type="text" readonly
                                    value="{{ $pedido->usuario->tipoDocumento->nombre_tipo_documento ?? 'N/A' }}"
                                    class="w-full px-3 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-[14px] text-gray-600 focus:outline-none">
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="text-[14px] font-bold text-gray-800 shrink-0">Número de documento:</label>
                                <input type="text" readonly
                                    value="{{ $pedido->usuario->numero_documento ?? 'N/A' }}"
                                    class="w-full px-3 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-[14px] text-gray-600 focus:outline-none">
                            </div>
                        </div>

                        @if($pedido->id_tipo_entrega == 2)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="flex items-center gap-2">
                                    <label class="text-[14px] font-bold text-gray-800 shrink-0">Departamento:</label>
                                    <input type="text" readonly
                                        value="{{ $pedido->departamento?->nombre_departamento ?? '—' }}"
                                        class="w-full px-3 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-[14px] text-gray-600 focus:outline-none">
                                </div>

                                <div class="flex items-center gap-2">
                                    <label class="text-[14px] font-bold text-gray-800 shrink-0">Provincia:</label>
                                    <input type="text" readonly
                                        value="{{ $pedido->provincia ?? '—' }}"
                                        class="w-full px-3 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-[14px] text-gray-600 focus:outline-none">
                                </div>

                                <div class="flex items-center gap-2">
                                    <label class="text-[14px] font-bold text-gray-800 shrink-0">Distrito:</label>
                                    <input type="text" readonly
                                        value="{{ $pedido->distrito ?? '—' }}"
                                        class="w-full px-3 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-[14px] text-gray-600 focus:outline-none">
                                </div>
                            </div>
                        @endif

                        {{-- Tabla de productos --}}
                        <div class="overflow-hidden rounded-2xl border border-gray-100">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-[#f1f1f1]">
                                        <th class="px-6 py-4 text-base font-black text-gray-800 text-left border-b border-gray-200">Producto</th>
                                        <th class="px-6 py-4 text-base font-black text-gray-800 text-center border-b border-gray-200">Precio</th>
                                        <th class="px-6 py-4 text-base font-black text-gray-800 text-center border-b border-gray-200">Cantidad</th>
                                        <th class="px-6 py-4 text-base font-black text-gray-800 text-right border-b border-gray-200">Subtotal</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($pedido->detalles as $detalle)
                                        @php
                                            $variante = $detalle->variante;
                                            $producto = $variante?->producto;
                                        @endphp
                                        <tr class="border-b border-gray-200">
                                            <td class="px-6 py-4">
                                                <p class="font-normal text-gray-800 text-base leading-tight">
                                                    {{ $producto->nombre_producto ?? 'Producto eliminado' }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Talla: {{ $variante?->talla ?? '—' }} · Color: {{ $variante?->color ?? '—' }}
                                                </p>
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                <span class="font-normal text-gray-800 text-base">
                                                    S/ {{ number_format($detalle->precio_unitario, 2) }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                <span class="font-normal text-gray-800 text-base">
                                                    {{ $detalle->cantidad }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-right">
                                                <span class="font-normal text-gray-800 text-base">
                                                    S/ {{ number_format($detalle->subtotal, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr class="border-t border-gray-200">
                                        <td colspan="3" class="px-6 py-5 text-right text-base text-gray-800 uppercase tracking-wider">
                                            Total
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <span class="text-xl text-gray-900 tracking-tight">
                                                S/ {{ number_format($pedido->total_pedido, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>

                </div>

                {{-- ============================================= --}}
                {{-- COLUMNA DERECHA: ESTADO + ENTREGA --}}
                {{-- ============================================= --}}
                <div class="space-y-6">

                    {{-- ESTADO --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">

                        <div class="flex items-center justify-between gap-3 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                    <x-heroicon-o-arrow-path class="w-5 h-5" />
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Estado</h2>
                            </div>

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
                        </div>

                        @php
                            if ($pedido->id_tipo_entrega == 2) {
                                $pasos = [
                                    ['nombre' => 'Pendiente',          'icono' => 'clock'],
                                    ['nombre' => 'En camino',          'icono' => 'truck'],
                                    ['nombre' => 'Listo para recoger', 'icono' => 'building-storefront'],
                                    ['nombre' => 'Entregado',          'icono' => 'check-badge'],
                                ];
                            } else {
                                $pasos = [
                                    ['nombre' => 'Listo para recoger', 'icono' => 'building-storefront'],
                                    ['nombre' => 'Entregado',          'icono' => 'check-badge'],
                                ];
                            }
                            $nombresPasos = array_column($pasos, 'nombre');
                            $indiceActual = array_search($pedido->estado_pedido, $nombresPasos);
                        @endphp

                        <div class="flex items-start mb-6">
                            @foreach($pasos as $i => $paso)
                                @php
                                    $completado = $i <= $indiceActual;
                                @endphp

                                <div class="flex flex-col items-center" style="width: 36px;">
                                    @if($completado)
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-gray-900 text-white">
                                            @if($paso['icono'] === 'clock')
                                                <x-heroicon-s-clock class="w-4 h-4" />
                                            @elseif($paso['icono'] === 'truck')
                                                <x-heroicon-s-truck class="w-4 h-4" />
                                            @elseif($paso['icono'] === 'check-badge')
                                                <x-heroicon-s-check-badge class="w-4 h-4" />
                                            @elseif($paso['icono'] === 'building-storefront')
                                                <x-heroicon-s-building-storefront class="w-4 h-4" />
                                            @endif
                                        </div>
                                    @else
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-white border-2 border-gray-900 text-gray-900">
                                            @if($paso['icono'] === 'clock')
                                                <x-heroicon-s-clock class="w-4 h-4" />
                                            @elseif($paso['icono'] === 'truck')
                                                <x-heroicon-s-truck class="w-4 h-4" />
                                            @elseif($paso['icono'] === 'check-badge')
                                                <x-heroicon-s-check-badge class="w-4 h-4" />
                                            @elseif($paso['icono'] === 'building-storefront')
                                                <x-heroicon-s-building-storefront class="w-4 h-4" />
                                            @endif
                                        </div>
                                    @endif

                                    <span class="text-[10px] mt-1.5 text-center leading-tight
                                        {{ $completado ? 'font-bold text-gray-900' : 'font-normal text-gray-900' }}">
                                        {{ $paso['nombre'] }}
                                    </span>
                                </div>

                                @if($i < count($pasos) - 1)
                                    @if($i < $indiceActual)
                                        <div class="flex-1 h-0.5 mt-[17px] bg-gray-900"></div>
                                    @else
                                        <div class="flex-1 h-px mt-[18px] bg-gray-900"></div>
                                    @endif
                                @endif
                            @endforeach
                        </div>

                        @if($pedido->estado_pedido !== 'Entregado')
                            @php
                                $siguiente = $pasos[$indiceActual + 1]['nombre'] ?? null;
                            @endphp

                            @if($siguiente)
                                <form method="POST" action="{{ route('admin.pedidos.update', $pedido->id_pedido) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="estado_pedido" value="{{ $siguiente }}">
                                    <button type="submit"
                                        class="w-full py-3.5 bg-indigo-600 text-white text-sm font-bold tracking-wider rounded-full transition-colors duration-200">
                                        Actualizar estado
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl">
                                <x-heroicon-s-check-badge class="w-6 h-6 text-emerald-500 flex-shrink-0" />
                                <div>
                                    <p class="font-bold text-emerald-800 text-sm">Pedido completado</p>
                                    <p class="text-emerald-600 text-xs mt-0.5">No puede modificarse.</p>
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- DATOS DE ENTREGA --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">

                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                <x-heroicon-o-map-pin class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Datos de entrega</h2>
                        </div>

                        <div class="space-y-5">

                            <div class="flex items-center gap-2">
                                <label class="text-[14px] font-bold text-gray-800 shrink-0">Fecha:</label>

                                <input type="text" name="fecha_entrega_estimada" id="fechaFiltro"
                                    value="{{ $pedido->fecha_entrega_estimada ? \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('Y-m-d') : '' }}"
                                    class="sr-only" readonly>

                                <button type="button" id="fechaBtn"
                                    class="flex-1 flex items-center justify-between gap-2 pl-4 pr-3 py-1.5 border border-gray-200 rounded-full bg-gray-50 text-[14px] text-gray-600 cursor-pointer focus:outline-none focus:border-gray-400 transition-colors">
                                    <span id="fechaTexto">Fecha</span>
                                    <x-heroicon-o-chevron-down class="w-4 h-4 shrink-0" />
                                </button>
                            </div>

                            <div>
                                <label class="text-[14px] font-bold text-gray-800 block mb-2">Lugar de entrega:</label>
                                <textarea name="lugar_recojo" rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-2xl bg-gray-50 text-[14px] text-gray-600 focus:outline-none focus:border-gray-400 transition-colors resize-none">{{ $pedido->lugar_recojo ?? '' }}</textarea>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </form>
    </div>

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