@extends('admin.layout')

@section('content')

    <div x-data="{ deleteModal: false, activeId: null, errorStockModal: false }">

        {{-- Header de la Sección --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Productos</h1>
                <p class="text-gray-500 mt-2 text-lg font-medium">Gestiona tu inventario, precios y disponibilidad.</p>
            </div>

            <a href="{{ route('admin.productos.create') }}"
                class="inline-flex items-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1 active:scale-95">
                <x-heroicon-o-plus class="w-6 h-6" />
                Nuevo Producto
            </a>
        </div>

        {{-- Buscador --}}
        <form action="{{ route('admin.productos.index') }}" method="GET"
            class="mb-8 flex flex-col md:flex-row gap-4 items-center justify-between bg-white p-4 rounded-3xl border border-gray-100 shadow-sm">
            <div class="relative w-full md:max-w-md">
                <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                </span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre..."
                    class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all outline-none font-medium text-gray-600">
            </div>

            <div class="flex items-center gap-3">
                {{-- Botón opcional para ejecutar la busqueda --}}
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition">
                    Buscar
                </button>

                <div class="h-8 w-[1px] bg-gray-200 mx-2"></div>

                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Mostrar:</span>
                <select name="perPage" onchange="this.form.submit()"
                    class="bg-gray-50 border-none rounded-xl py-2 px-4 font-bold text-indigo-600 focus:ring-0 cursor-pointer">
                    <option value="5" {{ request('perPage') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('perPage') == 10 || !request('perPage') ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('perPage') == 20 ? 'selected' : '' }}>20</option>
                </select>
            </div>
        </form>

        {{-- Contenedor de la Tabla --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400">Producto</th>
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400 text-center">
                                Precio</th>
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400 text-center">
                                Stock</th>
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400 text-center">
                                Estado</th>
                            <th class="px-8 py-6 text-xs font-black uppercase tracking-widest text-gray-400 text-right">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($productos as $producto)
                            <tr class="group hover:bg-indigo-50/30 transition-colors">
                                {{-- Producto con Imagen --}}
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-14 h-14 rounded-2xl overflow-hidden border border-gray-100 shadow-sm bg-gray-50 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                            <img src="{{ url('/api/imagen/' . $producto->imagen) }}"
                                                alt="{{ $producto->nombre_producto }}" class="w-full h-full object-cover"
                                                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($producto->nombre_producto) }}&color=7F9CF5&background=EBF4FF'">
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-lg leading-tight">
                                                {{ $producto->nombre_producto }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Precio --}}
                                <td class="px-8 py-6 text-center">
                                    <span class="font-black text-gray-900 text-lg">
                                        S/ {{ number_format($producto->precio, 2) }}
                                    </span>
                                </td>

                                {{-- Stock --}}
                                <td class="px-8 py-6 text-center">
                                    @if($producto->stock <= 0)
                                        <span
                                            class="inline-flex items-center gap-1 text-rose-600 font-bold bg-rose-50 px-3 py-1 rounded-lg">
                                            <x-heroicon-s-x-circle class="w-4 h-4" /> Agotado
                                        </span>
                                    @elseif($producto->stock <= 10)
                                        <div class="flex flex-col items-center">
                                            <span class="text-amber-600 font-black text-lg">{{ $producto->stock }}</span>
                                            <span class="text-[10px] uppercase font-black text-amber-500 tracking-tighter">Bajo
                                                stock</span>
                                        </div>
                                    @else
                                        <span class="text-gray-600 font-bold text-lg">{{ $producto->stock }}</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-8 py-6 text-center">
                                    @if($producto->estado_producto)
                                        <span
                                            class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-50 text-gray-400 border border-gray-100">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.productos.edit', $producto->id_producto) }}"
                                            class="p-3 bg-gray-50 text-gray-400 hover:bg-indigo-600 hover:text-white rounded-xl transition-all group/edit shadow-sm">
                                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                                        </a>

                                        {{-- Lgica de validación de Stock en el click --}}
                                        <button
                                            @click="if({{ $producto->stock }} > 0) { errorStockModal = true } else { deleteModal = true; activeId = {{ $producto->id_producto }} }"
                                            class="p-3 bg-gray-50 text-gray-400 hover:bg-rose-600 hover:text-white rounded-xl transition-all shadow-sm">
                                            <x-heroicon-o-trash class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
                <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>

        {{-- MODAL Confirmación de Eliminación --}}
        <template x-if="deleteModal">
            <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                <div @click="deleteModal = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-md"></div>
                <div class="relative bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl text-center">
                    <div
                        class="mx-auto w-20 h-20 flex items-center justify-center rounded-full bg-rose-50 text-rose-500 mb-6 font-bold">
                        <x-heroicon-o-trash class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">¿Eliminar producto?</h3>
                    <p class="text-gray-500 mb-10 font-medium">Esta acción no se puede deshacer.</p>
                    <div class="flex flex-col gap-3">
                        <form :action="'{{ route('admin.productos.index') }}/' + activeId" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition">Eliminar
                                ahora</button>
                        </form>
                        <button @click="deleteModal = false"
                            class="w-full py-4 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition">Cancelar</button>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal Alerta de Stock --}}
        <template x-if="errorStockModal">
            <div class="fixed inset-0 z-[120] flex items-center justify-center p-4">
                <div @click="errorStockModal = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-md"></div>
                <div
                    class="relative bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl text-center animate-in zoom-in-95 duration-200">
                    <div
                        class="mx-auto w-20 h-20 flex items-center justify-center rounded-full bg-amber-50 text-amber-500 mb-6">
                        <x-heroicon-o-exclamation-circle class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Acción denegada</h3>
                    <p class="text-gray-500 mb-10 font-medium leading-relaxed">
                        No puedes eliminar un producto que aún tiene <span class="text-amber-600 font-bold">stock
                            disponible</span>. Debes agotar el inventario antes de retirarlo.
                    </p>
                    <button @click="errorStockModal = false"
                        class="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                        Entendido
                    </button>
                </div>
            </div>
        </template>

    </div>

@endsection