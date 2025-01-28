@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Toast Message -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg">
            <span id="toast-message"></span>
        </div>
    </div>

    <!-- Flex Container para o card de Registro de Ponto -->
    <div class="flex flex-col">
        <!-- Card Principal (Registro de Ponto) -->
        <div class="w-full bg-white rounded-xl shadow-lg p-8 mb-6">
            <!-- User Info Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Bem-vindo</h2>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Último acesso:</p>
                        <p class="text-orange-400 font-medium">{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Registro de Ponto -->
            <div class="bg-orange-50 rounded-lg p-8 mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Registro de Ponto</h1>
                <div class="text-center space-y-2">
                    <div id="current-time" class="text-6xl font-bold text-orange-400"></div>
                    <div id="current-date" class="text-2xl text-gray-600"></div>
                </div>
                <!-- Action Section -->
                <div class="flex justify-center mt-6">
                    <form id="ponto-form" method="POST" class="w-full max-w-md">
                        @csrf
                        <button type="submit" id="ponto-btn"
                            class="w-full py-4 px-6 bg-orange-400 text-white text-lg font-semibold rounded-lg
                                   hover:bg-orange-500 focus:outline-none focus:ring-4 focus:ring-orange-200
                                   transition-all duration-300 transform hover:scale-105 shadow-lg">
                            Marcar Ponto
                        </button>
                    </form>
                </div>
                <!-- Modal de Confirmação -->
                <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden opacity-0 transition-opacity duration-300">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full transform scale-95 opacity-0 transition-all duration-300"
                             id="modalContent">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirmar Registro de Ponto</h3>
                                <form id="confirmForm" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="email" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                                        <input type="password" name="password" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                    </div>
                                </form>
                            </div>
                            <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end space-x-3">
                                <button id="cancelBtn"
                                    class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200">
                                    Cancelar
                                </button>
                                <button id="confirmBtn"
                                    class="px-4 py-2 bg-orange-400 text-white rounded-md hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-400 transition-all duration-200">
                                    Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logs dos Últimos Usuários -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Últimos 5 Registros de Ponto</h2>
                <div id="logs-container" class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-orange-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saída</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tempo Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody id="logs-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Logs serão inseridos aqui via JavaScript -->
                        </tbody>
                    </table>
                    <!-- Estado vazio -->
                    <div id="empty-state" class="hidden text-center py-8">
                        <p class="text-gray-500">Nenhum registro de ponto encontrado.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para obter o CSRF token
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        }

        function updateDateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString();
            document.getElementById('current-date').textContent = now.toLocaleDateString();
        }

        function updateStatus() {
            const token = getCSRFToken();
            if (!token) {
                console.error('CSRF token não encontrado');
                return;
            }

            fetch('/ponto/status', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                credentials: 'same-origin'
            })
            .then(async response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const tbody = document.getElementById('logs-table-body');
                    const emptyState = document.getElementById('empty-state');
                    const table = tbody.closest('table');
                    
                    tbody.innerHTML = '';

                    if (Array.isArray(data.logs) && data.logs.length > 0) {
                        // Mostrar tabela e esconder estado vazio
                        table.classList.remove('hidden');
                        emptyState.classList.add('hidden');
                        
                        data.logs.forEach(log => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${log.user_name || 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.entrada || 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.saida || '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.tempo_total || '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        ${log.status === 'Entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${log.status || 'N/A'}
                                    </span>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    } else {
                        // Esconder tabela e mostrar estado vazio
                        table.classList.add('hidden');
                        emptyState.classList.remove('hidden');
                    }

                    // Atualiza o texto do botão
                    const button = document.getElementById('ponto-btn');
                    if (button) {
                        button.textContent = data.working ? 'Registrar Saída' : 'Registrar Entrada';
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar status:', error);
                // Removida a mensagem de toast de erro
            });
        }

        // Atualiza o toast para mostrar mais detalhes do erro
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            if (!toast || !toastMessage) return;

            toastMessage.textContent = message;
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            toast.querySelector('div').className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg`;

            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        const modal = document.getElementById('confirmModal');
        const modalContent = document.getElementById('modalContent');
        const confirmForm = document.getElementById('confirmForm');

        function openModal() {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeModal() {
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                confirmForm.reset();
            }, 300);
        }

        document.getElementById('ponto-form').addEventListener('submit', function(e) {
            e.preventDefault();
            openModal();
        });

        document.getElementById('cancelBtn').addEventListener('click', closeModal);

        document.getElementById('confirmBtn').addEventListener('click', function() {
            const formData = new FormData(confirmForm);
            formData.append('_token', document.querySelector('input[name="_token"]').value);


            const button = document.getElementById('ponto-btn');
            button.disabled = true;

            fetch('/ponto/register', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                closeModal();
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    updateStatus();
                    button.textContent = data.working ? 'Registrar Saída' : 'Registrar Entrada';
                } else {
                    showToast(data.message || 'Erro ao registrar ponto', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Erro ao registrar ponto', 'error');
            })
            .finally(() => {
                button.disabled = false;
            });
        });

        // Consultar Situação (removido)

        // Fechar modal clicando fora
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Fechar com tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Initialize with error handling
        try {
            setInterval(updateDateTime, 1000);
            updateDateTime();
            updateStatus();
            setInterval(updateStatus, 30000);
        } catch (error) {
            console.error('Erro na inicialização:', error);
            showToast('Erro ao inicializar a página. Por favor, recarregue.', 'error');
        }
    });
    </script>

    <style>
        /* Estilização da barra de rolagem */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #FFF1E6;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #FB923C;
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #F97316;
        }
    </style>
</div>
@endsection

