@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Cabeçalho com botão de adicionar usuário -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gerenciamento de Usuários</h1>
        <button id="openCreateModal" class="bg-orange-400 text-white px-4 py-2 rounded-lg hover:bg-orange-500 transition-colors duration-200">
            Adicionar Usuário
        </button>
    </div>

    <!-- Tabela de Usuários -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
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
    // Funções para o modal de criação
    const createModal = document.getElementById('createModal');
    const openCreateModal = document.getElementById('openCreateModal');
    const closeCreateModal = document.getElementById('closeCreateModal');

    openCreateModal.addEventListener('click', () => {
        createModal.classList.remove('hidden');
    });

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
