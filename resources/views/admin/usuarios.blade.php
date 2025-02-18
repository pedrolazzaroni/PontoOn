@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8 bg-white rounded-b-lg shadow-md" style="min-height: calc(100vh - 80px);">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-orange-500">Gerenciamento de Usuários</h1>
            <p class="text-gray-600">Gerencie os usuários da sua empresa</p>
        </div>
        <button id="openCreateModal"
                class="w-full md:w-auto bg-orange-400 text-white px-6 py-2 rounded-lg hover:bg-orange-400 transition-colors duration-200 flex items-center justify-center md:justify-start gap-2 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Novo Usuário
        </button>
    </div>

    <!-- Filtros e Busca -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.users') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 flex flex-col md:flex-row gap-4">
                <!-- Campo de busca -->
                <div class="flex-1">
                    <input type="text"
                           name="search"
                           placeholder="Buscar usuário..."
                           value="{{ request('search') }}"
                           class="w-full input-focus-effect p-3 rounded-lg outline outline-gray-300 outline-1">
                </div>

                <!-- Botões de ação -->
                <div class="flex gap-2">
                    <button type="submit"
                            class="w-32 bg-orange-400 text-white px-4 py-3 rounded-lg hover:bg-orange-500 transition-all duration-200 ease-in-out hover:shadow-lg">
                        Pesquisar
                    </button>
                    <button type="button"
                            onclick="window.location.href='{{ route('admin.users') }}'"
                            class="w-32 bg-gray-200 text-gray-800 px-4 py-3 rounded-lg hover:bg-gray-300 transition-all duration-200 ease-in-out hover:shadow-lg">
                        Limpar Filtros
                    </button>
                </div>

                <!-- Select de Status -->
                <div class="w-48">
                    <select name="status"
                            class="w-full input-focus-effect appearance-none p-3 rounded-lg outline outline-gray-400 outline-1 cursor-pointer bg-white">
                        <option value="" {{ request('status') === null ? 'selected' : '' }}>Todos os Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativos</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabela de Usuários (Desktop) / Cards (Mobile) -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden md:block">
            <table class="min-w-full">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/4">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/4">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/6 text-center">Data Cadastro</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-1/6">Expediente</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-1/6">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-1/6">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $user->expediente }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->status ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center space-x-2">
                                <button onclick="openEditModal({{ $user->id }})"
                                        class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-all duration-200 ease-in-out hover:shadow-md cursor-pointer">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Editar
                                </button>
                                <button onclick="toggleUserStatus({{ $user->id }})"
                                        class="inline-flex items-center px-3 py-1 {{ $user->status ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-md transition-all duration-200 ease-in-out hover:shadow-md cursor-pointer">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $user->status ? 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}" />
                                    </svg>
                                    {{ $user->status ? 'Desativar' : 'Ativar' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden space-y-4 p-4">
            @foreach($users as $user)
            <div class="bg-white rounded-lg shadow p-4 border border-gray-100">
                <div class="space-y-3">
                    <!-- Nome e Status -->
                    <div class="flex justify-between items-start">
                        <div class="space-y-1">
                            <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->status ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>

                    <!-- Informações adicionais -->
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="space-y-1">
                            <p class="text-gray-500">Data Cadastro</p>
                            <p class="font-medium">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-gray-500">Expediente</p>
                            <p class="font-medium">{{ $user->expediente }}</p>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="flex gap-2 pt-2">
                        <button onclick="openEditModal({{ $user->id }})"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-all duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Editar
                        </button>
                        <button onclick="toggleUserStatus({{ $user->id }})"
                                class="flex-1 inline-flex items-center justify-center px-3 py-2 {{ $user->status ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-md transition-all duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $user->status ? 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}" />
                            </svg>
                            {{ $user->status ? 'Desativar' : 'Ativar' }}
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- <!-- Paginação -->
    @if($users->hasPages())
    <div class="mt-4">
        {{ $users->links() }}
    </div>
    @endif --}}

    <!-- Modal de Confirmação -->
    <div id="confirmationModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden transition-opacity duration-300">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirmar Ação</h3>
                <p id="confirmationMessage" class="text-gray-600 mb-6">Tem certeza que deseja realizar esta ação?</p>
                <div class="flex justify-end space-x-3">
                    <button id="cancelConfirmation" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                        Cancelar
                    </button>
                    <button id="confirmAction" class="px-4 py-2 bg-orange-400 text-white text-sm font-medium rounded-md hover:bg-orange-500">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.partials.user-modals')
    @include('admin.partials.user-scripts')
@endsection

