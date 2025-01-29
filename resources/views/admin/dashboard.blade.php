@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Welcome Section -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Olá, {{ Auth::user()->name }}</h1>
            <p class="text-gray-600 mt-2">Seus resumos atualizados</p>
        </div>

        <!-- Quick Access Card -->
        <div class="bg-white rounded-lg shadow-md p-6 w-64">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Usuários Gerenciados</h3>
                <p class="text-4xl font-bold text-orange-400 mb-4">{{ $users->count() }}</p>
                <a href="{{ route('admin.users') }}"
                   class="inline-block w-full bg-orange-400 text-white px-4 py-2 rounded-lg hover:bg-orange-500 transition-colors duration-200 text-sm">
                    Gerenciar Usuários
                </a>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-3 gap-6 mb-8">
        <!-- Card Histórico de Pontos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Histórico de Pontos</h2>
                <span class="text-sm text-gray-500">Últimos registros</span>
            </div>

            <div class="space-y-4">
                @foreach($recentPoints as $point)
                <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $point->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $point->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $point->type === 'entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($point->type) }}
                    </span>
                </div>
                @endforeach
            </div>

            <div class="mt-4 text-right">
                <a href="{{ route('admin.historico') }}" class="text-orange-400 hover:text-orange-500 font-semibold text-sm">
                    Ver mais →
                </a>
            </div>
        </div>

        <!-- Card Horas Extras -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Horas Extras</h2>
                <span class="text-sm text-gray-500">Últimos registros</span>
            </div>

            <div class="space-y-4">
                @foreach($overtimeUsers as $overtime)
                <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $overtime->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $overtime->total_hours }} horas</p>
                    </div>
                    <span class="text-orange-400 font-medium">
                        +{{ $overtime->extra_hours }}h
                    </span>
                </div>
                @endforeach
            </div>

            <div class="mt-4 text-right">
                <a href="{{ route('admin.hora-extra') }}" class="text-orange-400 hover:text-orange-500 font-semibold text-sm">
                    Ver mais →
                </a>
            </div>
        </div>

        <!-- Card Horas em Atraso -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Horas em Atraso</h2>
                <span class="text-sm text-gray-500">Últimos registros</span>
            </div>

            <div class="space-y-4">
                @foreach($lateUsers as $late)
                <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $late->user->name }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $late->date ? $late->date->format('d/m/Y') : '---' }}
                        </p>
                    </div>
                    <span class="text-red-500 font-medium">
                        -{{ $late->late_hours }}h
                    </span>
                </div>
                @endforeach
            </div>

            <div class="mt-4 text-right">
                <a href="{{ route('admin.hora-atraso') }}" class="text-orange-400 hover:text-orange-500 font-semibold text-sm">
                    Ver mais →
                </a>
            </div>
        </div>
    </div>

    <!-- User Management Section (can be toggled via button in quick access card) -->
    <div id="userManagementSection" class="hidden mt-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Gerenciamento de Usuários</h2>
            </div>
            <table class="min-w-full">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Cadastro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->status ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="openEditModal({{ $user->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                Editar
                            </button>
                            <button onclick="toggleUserStatus({{ $user->id }})" class="text-red-600 hover:text-red-900">
                                {{ $user->status ? 'Desativar' : 'Ativar' }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Criação de Usuário -->
    <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Adicionar Novo Usuário</h3>
                <form id="createUserForm" method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-400 focus:ring focus:ring-orange-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-400 focus:ring focus:ring-orange-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Senha</label>
                            <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-400 focus:ring focus:ring-orange-200">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" id="closeCreateModal" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-orange-400 text-white rounded-md hover:bg-orange-500">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Usuário -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Editar Usuário</h3>
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" name="name" id="edit_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-400 focus:ring focus:ring-orange-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="edit_email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-400 focus:ring focus:ring-orange-200">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" id="closeEditModal" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-orange-400 text-white rounded-md hover:bg-orange-500">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add toggle functionality for user management section
    const openCreateModal = document.getElementById('openCreateModal');
    const userManagementSection = document.getElementById('userManagementSection');

    openCreateModal.addEventListener('click', () => {
        userManagementSection.classList.remove('hidden');
        createModal.classList.remove('hidden');
    });

    // Funções para o modal de criação
    const createModal = document.getElementById('createModal');
    const closeCreateModal = document.getElementById('closeCreateModal');

    closeCreateModal.addEventListener('click', () => {
        createModal.classList.add('hidden');
    });

    // Funções para o modal de edição
    const editModal = document.getElementById('editModal');
    const closeEditModal = document.getElementById('closeEditModal');

    window.openEditModal = function(userId) {
        // Buscar dados do usuário e preencher o formulário
        fetch(`/admin/users/${userId}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_email').value = data.email;
                document.getElementById('editUserForm').action = `/admin/users/${userId}`;
                editModal.classList.remove('hidden');
            });
    };

    closeEditModal.addEventListener('click', () => {
        editModal.classList.add('hidden');
    });

    // Função para alternar status do usuário
    window.toggleUserStatus = function(userId) {
        if (confirm('Deseja realmente alterar o status deste usuário?')) {
            fetch(`/admin/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }
    };
});
</script>
@endsection
