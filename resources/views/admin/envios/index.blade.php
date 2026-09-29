@extends('admin.layout')

@section('content')

    <div x-data="{ editModal: false, departamento: {}, costoEdit: '' }">

        {{-- ============================================= --}}
        {{-- HEADER --}}
        {{-- ============================================= --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-6">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Costos de envío</h1>
                <p class="text-gray-500 mt-2 text-lg font-medium">Gestiona el costo de envío por departamento.</p>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- BUSCADOR --}}
        {{-- ============================================= --}}
        <div class="mb-6 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4 w-full">
                <span class="text-sm font-bold text-gray-700 shrink-0">Buscar:</span>
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-4 flex items-center text-gray-800">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                    </span>
                    <input type="text"
                           id="buscador"
                           placeholder="Buscar por nombre de departamento..."
                           class="w-full pl-12 pr-4 py-1.5 bg-[#f1f1f1] border border-gray-200 rounded-full text-gray-800 placeholder-gray-800 focus:outline-none focus:border-gray-400 transition-colors text-sm">
                </div>

                <button type="button"
                        id="btnBuscar"
                        class="shrink-0 px-6 py-1.5 bg-indigo-600 text-white rounded-full font-bold text-sm transition-colors duration-200">
                    Buscar
                </button>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- TABLA --}}
        {{-- ============================================= --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#f1f1f1]">
                            <th class="px-8 py-5 text-base font-black text-gray-800 text-left border-b border-gray-200">Departamento</th>
                            <th class="px-8 py-5 text-base font-black text-gray-800 text-center border-b border-gray-200">Costo de envío</th>
                            <th class="px-8 py-5 text-base font-black text-gray-800 text-right border-b border-gray-200">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200" id="tbodyDepartamentos">
                        @forelse($departamentos as $dep)
                            <tr class="odd:bg-white even:bg-[#f1f1f1]/40 fila-departamento"
                                data-nombre="{{ strtolower($dep->nombre_departamento) }}">
                                <td class="px-8 py-5">
                                    <p class="font-normal text-gray-800 text-base leading-tight">
                                        {{ $dep->nombre_departamento }}
                                    </p>
                                </td>

                                <td class="px-8 py-5 text-center">
                                    <span class="font-normal text-gray-800 text-base">
                                        @if($dep->costo_envio > 0)
                                            S/ {{ number_format($dep->costo_envio, 2) }}
                                        @else
                                            —
                                        @endif
                                    </span>
                                </td>

                                <td class="px-8 py-5 text-right">
                                    <button type="button"
                                            @click="$dispatch('open-edit-departamento', @js($dep))"
                                            class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 transition-colors">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-12 text-center text-gray-400 text-sm">
                                    No hay departamentos registrados.
                                </td>
                            </tr>
                        @endforelse

                        <tr id="sinResultados" class="hidden">
                            <td colspan="3" class="px-8 py-12 text-center text-gray-400 text-sm">
                                No se encontraron departamentos.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- MODAL EDITAR DEPARTAMENTO --}}
        {{-- ============================================= --}}
        <div x-data="{
                errores: {},
                submitEdit(e) {
                    this.errores = {};

                    const costo = e.target.querySelector('input[name=costo_envio]').value.trim();
                    if (costo === '' || isNaN(costo) || parseFloat(costo) < 0) {
                        this.errores.costo_envio = true;
                    }

                    if (Object.keys(this.errores).length > 0) {
                        e.preventDefault();
                        return;
                    }

                    e.target.submit();
                }
            }"
            @open-edit-departamento.window="
                departamento = $event.detail;
                costoEdit = Number(departamento.costo_envio).toFixed(2);
                errores = {};
                editModal = true;
            ">
            <template x-if="editModal">
                <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                    <div @click="editModal = false" class="absolute inset-0 bg-gray-900/40 backdrop-blur-md"></div>
                    <div class="relative bg-white rounded-[2.5rem] p-10 max-w-lg w-full shadow-2xl">

                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Editar costo de envío</h2>
                            </div>
                            <button @click="editModal = false" class="text-gray-500 transition -mt-1">
                                <x-heroicon-o-x-mark class="w-6 h-6" />
                            </button>
                        </div>

                        <form :action="`/admin/envios/${departamento.id_departamento}`" method="POST" class="space-y-4"
                              @submit.prevent="submitEdit($event)">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">

                            <div class="flex items-center gap-3">
                                <label class="text-[14px] font-bold text-gray-800 shrink-0">Costo de envío:</label>

                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-4 flex items-center text-[14px] text-gray-800 pointer-events-none">
                                        S/
                                    </span>
                                    <input type="text"
                                           name="costo_envio"
                                           inputmode="decimal"
                                           x-model="costoEdit"
                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                           class="w-full pl-10 pr-4 py-1.5 bg-gray-50 border rounded-full focus:outline-none focus:border-gray-400 text-[14px] font-medium transition-colors"
                                           :class="errores.costo_envio ? 'border-rose-500' : 'border-gray-200'">
                                </div>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button type="button" @click="editModal = false"
                                    class="flex-1 py-3.5 bg-gray-100 text-gray-700 font-bold rounded-full text-sm transition">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="flex-1 py-3.5 bg-black text-white font-bold rounded-full text-sm transition">
                                    Aceptar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>

    </div>

    {{-- ============================================= --}}
    {{-- BUSCADOR EN VIVO --}}
    {{-- ============================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buscador = document.getElementById('buscador');
            const btnBuscar = document.getElementById('btnBuscar');
            const filas = document.querySelectorAll('.fila-departamento');
            const sinResultados = document.getElementById('sinResultados');

            if (!buscador) return;

            function filtrar() {
                const query = buscador.value.trim().toLowerCase();
                let visibles = 0;

                filas.forEach(fila => {
                    const nombre = fila.dataset.nombre || '';
                    if (nombre.includes(query)) {
                        fila.style.display = '';
                        visibles++;
                    } else {
                        fila.style.display = 'none';
                    }
                });

                if (visibles === 0 && filas.length > 0) {
                    sinResultados.classList.remove('hidden');
                } else {
                    sinResultados.classList.add('hidden');
                }
            }

            buscador.addEventListener('input', filtrar);
            btnBuscar.addEventListener('click', filtrar);

            buscador.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    filtrar();
                }
            });
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

@endsection