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

        {{-- Buscador + Filtros --}}
        <form action="{{ route('admin.productos.index') }}" method="GET"
            x-data="{
                categoria: '',
                categoriaTexto: 'Categoría',
                marca: '',
                marcaTexto: 'Marca',
                stock: '',
                stockTexto: 'Stock',

                applyFilter() {
                    const form = $el;
                    const params = new URLSearchParams();

                    const buscar = form.querySelector('input[name=buscar]').value.trim();
                    if (buscar) params.append('buscar', buscar);
                    if (this.categoria) params.append('categoria', this.categoria);
                    if (this.marca) params.append('marca', this.marca);
                    if (this.stock) params.append('stock', this.stock);

                    form.querySelector('input[name=buscar]').value = '';
                    this.categoria = '';
                    this.categoriaTexto = 'Categoría';
                    this.marca = '';
                    this.marcaTexto = 'Marca';
                    this.stock = '';
                    this.stockTexto = 'Stock';

                    window.location.href = form.action + '?' + params.toString();
                }
            }"
            x-on:submit.prevent="applyFilter()"
            class="mb-8 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4 w-full flex-wrap">

                {{-- Buscador --}}
                <span class="text-sm font-bold text-gray-700 shrink-0">Buscar:</span>
                <div class="relative flex-1 min-w-[295px] max-w-[400px]">
                    <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                    </span>
                    <input type="text" name="buscar" value="" placeholder="Buscar por nombre..."
                        class="w-full pl-12 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-full focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none text-sm">
                </div>

                {{-- Etiqueta Filtros --}}
                <span class="text-sm font-bold text-gray-700 shrink-0">Filtros:</span>

                {{-- Select custom: Categoría --}}
                <div x-data="{ open: false }" class="relative w-full max-w-[180px]">
                    <input type="hidden" name="categoria" :value="categoria">

                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between gap-2 pl-4 pr-5 py-2.5 border border-gray-200 rounded-full bg-gray-50 text-sm text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                        <span x-text="categoriaTexto"></span>
                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 shrink-0" />
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
                            @foreach($categoriasFiltro ?? [] as $cat)
                                <button type="button" @click="categoria = '{{ $cat->id_categoria }}'; categoriaTexto = '{{ addslashes($cat->nombre_categoria) }}'; open = false"
                                    :class="categoria === '{{ $cat->id_categoria }}' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                                    class="w-full text-left px-4 py-2 text-sm transition">
                                    {{ $cat->nombre_categoria }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Select custom: Marca --}}
                <div x-data="{ open: false }" class="relative w-full max-w-[180px]">
                    <input type="hidden" name="marca" :value="marca">

                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between gap-2 pl-4 pr-5 py-2.5 border border-gray-200 rounded-full bg-gray-50 text-sm text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                        <span x-text="marcaTexto"></span>
                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 shrink-0" />
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
                            @foreach($marcasFiltro ?? [] as $marca)
                                <button type="button" @click="marca = '{{ addslashes($marca) }}'; marcaTexto = '{{ addslashes($marca) }}'; open = false"
                                    :class="marca === '{{ addslashes($marca) }}' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                                    class="w-full text-left px-4 py-2 text-sm transition">
                                    {{ $marca }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Select custom: Stock --}}
                <div x-data="{ open: false }" class="relative w-full max-w-[180px]">
                    <input type="hidden" name="stock" :value="stock">

                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between gap-2 pl-4 pr-5 py-2.5 border border-gray-200 rounded-full bg-gray-50 text-sm text-gray-600 hover:bg-gray-100 transition cursor-pointer">
                        <span x-text="stockTexto"></span>
                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 shrink-0" />
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
                            <button type="button" @click="stock = 'agotado'; stockTexto = 'Agotado'; open = false"
                                :class="stock === 'agotado' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                                class="w-full text-left px-4 py-2 text-sm transition">
                                Agotado
                            </button>
                            <button type="button" @click="stock = 'bajo'; stockTexto = 'Bajo stock (1-10)'; open = false"
                                :class="stock === 'bajo' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                                class="w-full text-left px-4 py-2 text-sm transition">
                                Bajo stock (1-10)
                            </button>
                            <button type="button" @click="stock = 'disponible'; stockTexto = 'Disponible (+10)'; open = false"
                                :class="stock === 'disponible' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                                class="w-full text-left px-4 py-2 text-sm transition">
                                Disponible (+10)
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Espaciador --}}
                <div class="flex-1"></div>

                {{-- Botón Filtrar --}}
                <button type="submit"
                    class="shrink-0 px-6 py-2.5 bg-indigo-600 text-white rounded-full font-bold text-sm hover:bg-indigo-700 transition shadow-md shadow-indigo-200">
                    Filtrar
                </button>

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
                                <td class="px-8 py-6">
                                    <p class="font-bold text-gray-900 text-lg leading-tight">
                                        {{ $producto->nombre_producto }}
                                    </p>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    <span class="font-black text-gray-900 text-lg">
                                        S/ {{ number_format($producto->precio, 2) }}
                                    </span>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    @if($producto->stock <= 0)
                                        <span class="inline-flex items-center gap-1 text-rose-600 font-bold bg-rose-50 px-3 py-1 rounded-lg">
                                            <x-heroicon-s-x-circle class="w-4 h-4" /> Agotado
                                        </span>
                                    @elseif($producto->stock <= 10)
                                        <div class="flex flex-col items-center">
                                            <span class="text-amber-600 font-black text-lg">{{ $producto->stock }}</span>
                                            <span class="text-[10px] uppercase font-black text-amber-500 tracking-tighter">Bajo stock</span>
                                        </div>
                                    @else
                                        <span class="text-gray-600 font-bold text-lg">{{ $producto->stock }}</span>
                                    @endif
                                </td>

                                <td class="px-8 py-6 text-center">
                                    @if($producto->estado_producto)
                                        <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-50 text-gray-400 border border-gray-100">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.productos.edit', $producto->id_producto) }}"
                                            class="p-3 bg-gray-50 text-gray-400 hover:bg-indigo-600 hover:text-white rounded-xl transition-all group/edit shadow-sm">
                                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                                        </a>

                                        <button
                                            @click="if({{ $producto->stock }} > 0) { errorStockModal = true } else { deleteModal = true; activeId = {{ $producto->id_producto }} }"
                                            class="p-3 bg-gray-50 text-gray-400 hover:bg-rose-600 hover:text-white rounded-xl transition-all shadow-sm">
                                            <x-heroicon-o-trash class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-gray-400 text-sm">
                                    No hay productos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Paginación --}}
                @if($productos->hasPages())
                <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                    {{ $productos->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- MODAL Confirmación de Eliminación --}}
        <template x-if="deleteModal">
            <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                <div @click="deleteModal = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-md"></div>
                <div class="relative bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl text-center">
                    <div class="mx-auto w-20 h-20 flex items-center justify-center rounded-full bg-rose-50 text-rose-500 mb-6 font-bold">
                        <x-heroicon-o-trash class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">¿Eliminar producto?</h3>
                    <p class="text-gray-500 mb-10 font-medium">Esta acción no se puede deshacer.</p>
                    <div class="flex flex-col gap-3">
                        <form :action="'{{ route('admin.productos.index') }}/' + activeId" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full py-4 bg-gray-900 text-white font-bold rounded-2xl hover:bg-black transition">Eliminar ahora</button>
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
                <div class="relative bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl text-center animate-in zoom-in-95 duration-200">
                    <div class="mx-auto w-20 h-20 flex items-center justify-center rounded-full bg-amber-50 text-amber-500 mb-6">
                        <x-heroicon-o-exclamation-circle class="w-10 h-10" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Acción denegada</h3>
                    <p class="text-gray-500 mb-10 font-medium leading-relaxed">
                        No puedes eliminar un producto que aún tiene <span class="text-amber-600 font-bold">stock disponible</span>. Debes agotar el inventario antes de retirarlo.
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