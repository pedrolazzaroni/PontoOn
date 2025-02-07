<!-- Modal de Criação de Usuário -->
<div id="createModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden  transition-opacity duration-300">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Adicionar Novo Usuário</h3>
                <button type="button" id="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="createUserForm" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text" name="name" required
                            class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required
                            class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Senha</label>
                        <input type="password" name="password" required
                            class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                        <input type="password" name="password_confirmation" required
                            class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    </div>

                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" id="cancelCreateBtn"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-400 text-white text-sm font-medium rounded-md hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400">
                        Criar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Edição de Usuário -->
<div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden transition-opacity duration-300">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Editar Usuário</h3>
                <button type="button" id="closeEditModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text"
                               name="name"
                               id="edit_name"
                               required
                               class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email"
                               name="email"
                               id="edit_email"
                               required
                               class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Expediente (horas)</label>
                        <div class="flex items-center space-x-2">
                            <input type="number"
                                   name="expediente"
                                   id="edit_expediente"
                                   min="1"
                                   max="24"
                                   required
                                   class="flex-1 w-full p-3 rounded-lg outline outline-1 outline-gray-500 focus:outline-orange-400 focus:ring focus:ring-orange-200">
                            <span class="text-gray-600">horas</span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Defina as horas de trabalho deste usuário</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nova Senha</label>
                        <input type="password"
                               name="password"
                               class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                        <p class="text-xs text-gray-500 mt-1">Deixe em branco para manter a senha atual</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                            id="cancelEditBtn"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-400 text-white text-sm font-medium rounded-md hover:bg-orange-500">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
