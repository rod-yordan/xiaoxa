@extends('layouts.app')

@section('title', 'Mi cuenta - Xiaoxa')

@section('content')
<div
    x-data="{ seccion: '{{ request('seccion', $errors->any() ? 'editar' : 'info') }}' }"
    class="min-h-[calc(100vh-150px)] bg-[#fbfaf8] py-8 px-4"
>

    <div class="max-w-5xl mx-auto">

        {{-- TÍTULO --}}
        <h1 class="text-2xl font-normal text-black text-center mb-8">Mi cuenta</h1>

        {{-- GRID: 1/3 IZQUIERDA + 2/3 DERECHA --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-16">

            {{-- CARD IZQUIERDA --}}
            <div class="md:col-span-1 bg-[#f1f1f1] border border-gray-300 rounded-lg p-4 h-[430px] flex flex-col w-full max-w-[260px]">

                <div class="flex items-center gap-3 pb-4 mb-3 border-b border-gray-300">
                    <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white text-sm font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()->nombres, 0, 1)) }}
                    </div>
                    <span class="text-sm text-black font-normal truncate">
                        {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
                    </span>
                </div>

                <nav class="space-y-1">

                    <a href="#"
                        @click.prevent="seccion = 'info'"
                        :class="seccion === 'info' ? 'bg-gray-300 text-black' : 'text-black hover:bg-gray-200'"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-md transition">
                        <x-heroicon-o-user class="w-5 h-5 shrink-0" />
                        <span class="text-sm">Información de cuenta</span>
                    </a>

                    <a href="#"
                        @click.prevent="seccion = 'editar'"
                        :class="seccion === 'editar' ? 'bg-gray-300 text-black' : 'text-black hover:bg-gray-200'"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-md transition">
                        <x-heroicon-o-pencil-square class="w-5 h-5 shrink-0" />
                        <span class="text-sm">Editar mis datos</span>
                    </a>

                    <a href="#"
                        @click.prevent="seccion = 'pedidos'"
                        :class="seccion === 'pedidos' ? 'bg-gray-300 text-black' : 'text-black hover:bg-gray-200'"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-md transition">
                        <x-heroicon-o-shopping-bag class="w-5 h-5 shrink-0" />
                        <span class="text-sm">Mis pedidos</span>
                    </a>

                </nav>

                <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-4">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2 text-black hover:bg-gray-200 rounded-md transition w-full">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 shrink-0" />
                        <span class="text-xs uppercase tracking-wider">Cerrar sesión</span>
                    </button>
                </form>

            </div>

            {{-- CARD DERECHA (SCROLL PEGADO A ARRIBA Y ABAJO) --}}
            <div class="md:col-span-2 bg-[#f1f1f1] border border-gray-300 rounded-lg pl-10 pr-0 py-0 h-[430px] flex flex-col overflow-hidden">

                {{-- ============ INFO CUENTA ============ --}}
                <div x-show="seccion === 'info'" class="w-full overflow-y-auto flex-1 pr-10 pt-7 pb-10">

                    <div class="pb-3 mb-6 border-b border-gray-300">
                        <h2 class="text-xl font-bold text-gray-800">Información de cuenta</h2>
                    </div>

                    <div class="w-full">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">

                            <div>
                                <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Nombres</label>
                                <input type="text" readonly
                                    value="{{ auth()->user()->nombres }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-full bg-gray-50 text-sm text-gray-600 focus:outline-none">
                            </div>

                            <div>
                                <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Apellidos</label>
                                <input type="text" readonly
                                    value="{{ auth()->user()->apellidos }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-full bg-gray-50 text-sm text-gray-600 focus:outline-none">
                            </div>

                            <div>
                                <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Teléfono</label>
                                <input type="text" readonly
                                    value="{{ auth()->user()->telefono ?? 'No registrado' }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-full bg-gray-50 text-sm {{ auth()->user()->telefono ? 'text-gray-600' : 'text-gray-400' }} focus:outline-none">
                            </div>

                            <div>
                                <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Correo electrónico</label>
                                <input type="text" readonly
                                    value="{{ auth()->user()->correo }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-full bg-gray-50 text-sm text-gray-600 focus:outline-none">
                            </div>

                            <div>
                                <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Tipo de documento</label>
                                <input type="text" readonly
                                    value="{{ auth()->user()->tipoDocumento->nombre_tipo_documento ?? 'No registrado' }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-full bg-gray-50 text-sm {{ auth()->user()->tipoDocumento ? 'text-gray-600' : 'text-gray-400' }} focus:outline-none">
                            </div>

                            <div>
                                <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Número de documento</label>
                                <input type="text" readonly
                                    value="{{ auth()->user()->numero_documento ?? 'No registrado' }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-full bg-gray-50 text-sm {{ auth()->user()->numero_documento ? 'text-gray-600' : 'text-gray-400' }} focus:outline-none">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ============ EDITAR DATOS ============ --}}
                <div x-show="seccion === 'editar'" x-cloak class="w-full overflow-y-auto flex-1 pr-10 pt-7 pb-10">

                    <div class="pb-3 mb-6 border-b border-gray-300">
                        <h2 class="text-xl font-bold text-gray-800">Editar mis datos</h2>
                    </div>

                    <div class="w-full">
                        <form method="POST" action="{{ route('perfil.update') }}" class="space-y-4 w-full">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">

                                <div>
                                    <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Nombres</label>
                                    <div class="relative">
                                        <input type="text" name="nombres" value="{{ old('nombres', auth()->user()->nombres) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm text-black focus:outline-none focus:border-black transition">
                                        <x-heroicon-o-pencil class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    </div>
                                    @error('nombres')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Apellidos</label>
                                    <div class="relative">
                                        <input type="text" name="apellidos" value="{{ old('apellidos', auth()->user()->apellidos) }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm text-black focus:outline-none focus:border-black transition">
                                        <x-heroicon-o-pencil class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    </div>
                                    @error('apellidos')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Teléfono móvil</label>
                                    <div class="relative">
                                        <input type="text" name="telefono"
                                            value="{{ old('telefono', auth()->user()->telefono) }}"
                                            maxlength="9"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            placeholder="Ej. 912345678"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm text-black focus:outline-none focus:border-black transition">
                                        <x-heroicon-o-pencil class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                                    </div>
                                    @error('telefono')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Correo electrónico</label>
                                    <input type="email" value="{{ auth()->user()->correo }}"
                                        disabled
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                                </div>

                                <div>
                                    <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Tipo de documento</label>
                                    <input type="text" value="{{ auth()->user()->tipoDocumento->nombre_tipo_documento ?? 'No registrado' }}"
                                        disabled
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                                </div>

                                <div>
                                    <label class="text-xs font-semibold tracking-widest text-gray-600 block mb-2">Número de documento</label>
                                    <input type="text" value="{{ auth()->user()->numero_documento ?? 'No registrado' }}"
                                        disabled
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                                </div>

                            </div>

                            <div class="pt-2 flex justify-center">
                                <button type="submit"
                                    class="bg-black text-white px-12 py-3 rounded-lg font-bold uppercase tracking-widest text-xs hover:bg-gray-800 transition-all">
                                    Guardar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- ============ MIS PEDIDOS ============ --}}
                <div x-show="seccion === 'pedidos'" x-cloak class="w-full overflow-y-auto flex-1 pr-10 pt-7 pb-10">

                    <div class="pb-3 mb-6 border-b border-gray-300">
                        <h2 class="text-xl font-bold text-gray-800">Mis pedidos</h2>
                    </div>

                    @if($pedidos->isEmpty())
                        <div class="bg-white rounded-lg border border-gray-300 p-12 text-center">
                            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-shopping-bag class="w-7 h-7 text-gray-300" />
                            </div>
                            <p class="text-gray-500 text-sm">Sin pedidos aún</p>
                        </div>
                    @else

                        @php
                            $colores = [
                                'Pendiente'          => 'bg-amber-50 text-amber-700 border-amber-200',
                                'En camino'          => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'Listo para recoger' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'Entregado'          => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            ];
                        @endphp

                        <div class="space-y-3">

                            @foreach($pedidos as $pedido)

                                @php
                                    $esRetiro = $pedido->id_tipo_entrega == 1;

                                    if ($esRetiro) {
                                        $pasos = [
                                            ['nombre' => 'Listo para recoger', 'icono' => 'building-storefront'],
                                            ['nombre' => 'Entregado',          'icono' => 'check-badge'],
                                        ];
                                    } else {
                                        $pasos = [
                                            ['nombre' => 'Pendiente',          'icono' => 'clock'],
                                            ['nombre' => 'En camino',          'icono' => 'truck'],
                                            ['nombre' => 'Listo para recoger', 'icono' => 'building-storefront'],
                                            ['nombre' => 'Entregado',          'icono' => 'check-badge'],
                                        ];
                                    }

                                    $nombresPasos = array_column($pasos, 'nombre');
                                    $indiceActual = array_search($pedido->estado_pedido, $nombresPasos);
                                @endphp

                                <div x-data="{ open: false }" class="bg-white rounded-lg border border-gray-300 overflow-hidden">

                                    {{-- HEADER --}}
                                    <button @click="open = !open" class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50 transition">

                                        <div class="flex items-center gap-4 min-w-0">
                                            <div class="flex flex-col items-start gap-0.5 min-w-0">
                                                <div class="flex items-baseline gap-2">
                                                    <span class="text-[14px] font-bold text-gray-700">Pedido</span>
                                                    <span class="text-[14px] font-bold text-gray-700">{{ $pedido->numero_pedido }}</span>
                                                </div>
                                                <span class="text-[11px] text-gray-700">
                                                    {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->translatedFormat('d \d\e F \d\e\l Y') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 shrink-0">
                                            <span class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide rounded-full border {{ $colores[$pedido->estado_pedido] ?? '' }}">
                                                {{ $pedido->estado_pedido }}
                                            </span>

                                            <svg :class="open ? 'rotate-180' : ''" class="w-3 h-3 text-gray-700 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </button>

                                    {{-- BODY --}}
                                    <div x-show="open" x-collapse class="border-t border-gray-200 px-12 py-10 space-y-7 bg-gray-50/50">

                                        {{-- SEGUIMIENTO --}}
                                        <div class="px-4">
                                            <div class="flex items-start">
                                                @if($esRetiro)
                                                    <div class="flex-1"></div>

                                                    <div class="flex flex-col items-center" style="width: 36px;">
                                                        @if($indiceActual >= 0)
                                                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-indigo-600 text-white">
                                                                <x-heroicon-s-building-storefront class="w-4 h-4" />
                                                            </div>
                                                        @else
                                                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-white border-2 border-indigo-600 text-indigo-600">
                                                                <x-heroicon-s-building-storefront class="w-4 h-4" />
                                                            </div>
                                                        @endif
                                                        <span class="text-[10px] mt-1.5 text-center leading-tight
                                                            {{ $indiceActual >= 0 ? 'font-bold text-indigo-600' : 'font-normal text-indigo-600' }}">
                                                            Listo para<br>recoger
                                                        </span>
                                                    </div>

                                                    <div class="flex-1 {{ $indiceActual >= 1 ? 'h-0.5 bg-indigo-600' : 'h-px bg-indigo-600' }} mt-[17px]"></div>

                                                    <div class="flex flex-col items-center" style="width: 36px;">
                                                        @if($indiceActual >= 1)
                                                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-indigo-600 text-white">
                                                                <x-heroicon-s-check-badge class="w-4 h-4" />
                                                            </div>
                                                        @else
                                                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-white border-2 border-indigo-600 text-indigo-600">
                                                                <x-heroicon-s-check-badge class="w-4 h-4" />
                                                            </div>
                                                        @endif
                                                        <span class="text-[10px] mt-1.5 text-center leading-tight
                                                            {{ $indiceActual >= 1 ? 'font-bold text-indigo-600' : 'font-normal text-indigo-600' }}">
                                                            Entregado
                                                        </span>
                                                    </div>

                                                    <div class="flex-1"></div>
                                                @else
                                                    @foreach($pasos as $i => $paso)
                                                        @php
                                                            $completado = $i <= $indiceActual;
                                                        @endphp

                                                        <div class="flex flex-col items-center" style="width: 36px;">
                                                            @if($completado)
                                                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-indigo-600 text-white">
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
                                                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-white border-2 border-indigo-600 text-indigo-600">
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
                                                                {{ $completado ? 'font-bold text-indigo-600' : 'font-normal text-indigo-600' }}">
                                                                @if($paso['nombre'] === 'Listo para recoger')
                                                                    Listo para<br>recoger
                                                                @else
                                                                    {{ $paso['nombre'] }}
                                                                @endif
                                                            </span>
                                                        </div>

                                                        @if($i < count($pasos) - 1)
                                                            @if($i < $indiceActual)
                                                                <div class="flex-1 h-0.5 mt-[17px] bg-indigo-600"></div>
                                                            @else
                                                                <div class="flex-1 h-px mt-[18px] bg-indigo-600"></div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        {{-- PRODUCTOS EN TABLA --}}
                                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                            <table class="w-full">
                                                <thead>
                                                    <tr class="bg-gray-50 border-b border-gray-200">
                                                        <th class="text-left px-4 py-3 text-[11px] font-bold text-black uppercase tracking-wider">Producto</th>
                                                        <th class="text-center px-4 py-3 text-[11px] font-bold text-black uppercase tracking-wider">Precio</th>
                                                        <th class="text-center px-4 py-3 text-[11px] font-bold text-black uppercase tracking-wider">Cant.</th>
                                                        <th class="text-right px-4 py-3 text-[11px] font-bold text-black uppercase tracking-wider">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pedido->detalles as $detalle)
                                                        @php
                                                            $variante = $detalle->variante;
                                                            $producto = $variante?->producto;
                                                        @endphp
                                                        <tr class="border-b border-gray-100 last:border-0">
                                                            <td class="px-4 py-3">
                                                                <p class="text-xs font-semibold text-black">
                                                                    {{ $producto->nombre_producto ?? 'Producto eliminado' }}
                                                                </p>
                                                                <p class="text-[10px] text-black mt-0.5">
                                                                    Talla: {{ $variante?->talla ?? '—' }} · Color: {{ $variante?->color ?? '—' }}
                                                                </p>
                                                            </td>
                                                            <td class="px-4 py-3 text-center text-xs font-medium text-black">
                                                                S/ {{ number_format($detalle->precio_unitario, 2) }}
                                                            </td>
                                                            <td class="px-4 py-3 text-center text-xs font-medium text-black">
                                                                {{ $detalle->cantidad }}
                                                            </td>
                                                            <td class="px-4 py-3 text-right text-xs font-medium text-black">
                                                                S/ {{ number_format($detalle->subtotal, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="border-t border-gray-100">
                                                        <td colspan="3" class="px-4 py-3 text-right text-xs font-bold text-black uppercase tracking-wider">
                                                            Total
                                                        </td>
                                                        <td class="px-4 py-3 text-right text-xs font-medium text-black">
                                                            S/ {{ number_format($pedido->total_pedido, 2) }}
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        {{-- ENTREGA --}}
                                        <div class="pt-2 border-t border-gray-200">
                                            <p class="text-[10px] font-black text-black uppercase tracking-widest mb-2">Entrega</p>

                                            <div class="space-y-1.5">
                                                <div class="flex justify-between">
                                                    <span class="text-xs text-black">Tipo de entrega</span>
                                                    <span class="text-xs font-semibold text-black">
                                                        {{ $pedido->tipoEntrega?->nombre_tipo_entrega ?? '—' }}
                                                    </span>
                                                </div>

                                                <div class="flex justify-between">
                                                    <span class="text-xs text-black">
                                                        {{ $esRetiro ? 'Lugar de recojo' : 'Dirección' }}
                                                    </span>
                                                    <span class="text-xs font-semibold text-black text-right max-w-[60%]">
                                                        @if($esRetiro)
                                                            {{ $pedido->lugar_recojo ?? 'Jr. Cajamarca N° 396 - Huancayo' }}
                                                        @else
                                                            {{ $pedido->departamento?->nombre_departamento ?? '—' }},
                                                            {{ $pedido->provincia ?? '—' }},
                                                            {{ $pedido->distrito ?? '—' }}
                                                        @endif
                                                    </span>
                                                </div>

                                                <div class="flex justify-between">
                                                    <span class="text-xs text-black">Fecha</span>
                                                    <span class="text-xs font-semibold text-black">
                                                        @if($pedido->fecha_entrega_real)
                                                            {{ \Carbon\Carbon::parse($pedido->fecha_entrega_real)->format('d/m/Y') }}
                                                        @elseif($pedido->fecha_entrega_estimada)
                                                            Est. {{ \Carbon\Carbon::parse($pedido->fecha_entrega_estimada)->format('d/m/Y') }}
                                                        @elseif($pedido->fecha_envio)
                                                            Enviado {{ \Carbon\Carbon::parse($pedido->fecha_envio)->format('d/m/Y') }}
                                                        @else
                                                            —
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            @endforeach

                        </div>

                        <div class="mt-4">
                            {{ $pedidos->links() }}
                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>
</div>
@endsection