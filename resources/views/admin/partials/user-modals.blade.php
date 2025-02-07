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
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
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
                        <label class="block text-sm font-medium text-gray-700">Senha</label>
                        <div class="relative">
                            <input type="password" name="password" id="edit_password"
                                   class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                            <button type="button" id="togglePassword"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Deixe em branco para manter a senha atual</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" id="cancelEditBtn"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-400 text-white text-sm font-medium rounded-md hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
