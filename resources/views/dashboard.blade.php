@extends('layouts.head')
@extends('layouts.header')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <!-- Toast Message -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg">
            <span id="toast-message"></span>
        </div>
    </div>

    <!-- Flex Container para o card de Registro de Ponto -->
    <div class="flex flex-col">
        <!-- Card Principal (Registro de Ponto) -->
        <div class="w-full bg-white rounded-xl shadow-lg p-4 sm:p-8 mb-4 sm:mb-6">
            <!-- User Info Section -->
            <div class="border-b border-gray-200 pb-4 sm:pb-6 mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold text-orange-500">Bem-vindo</h2>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs sm:text-sm text-gray-600">Data atual:</p>
                        <p class="text-orange-400 font-medium text-sm sm:text-base">{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Registro de Ponto -->
            <div class="bg-orange-50 rounded-lg p-4 sm:p-8 mb-4 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">Registro de Ponto</h1>
                <div class="text-center space-y-1 sm:space-y-2">
                    <div id="current-time" class="text-4xl sm:text-6xl font-bold text-orange-400"></div>
                    <div id="current-date" class="text-lg sm:text-2xl text-gray-600"></div>
                </div>

                <!-- Action Section -->
                <div class="flex justify-center mt-4 sm:mt-6">
                    <form id="ponto-form" method="POST" class="w-full max-w-md">
                        @csrf
                        <button type="submit" id="ponto-btn"
                            class="w-full py-3 sm:py-4 px-4 sm:px-6 bg-orange-400 text-white text-base sm:text-lg font-semibold rounded-lg
                                   hover:bg-orange-500 focus:outline-none focus:ring-4 focus:ring-orange-200
                                   transition-all duration-300 transform hover:scale-105 shadow-lg">
                            Marcar Ponto
                        </button>
                    </form>
                </div>
                <!-- Modal de Confirmação -->
                <div id="confirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300">
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
                                            class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                                        <input type="password" name="password" required
                                            class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
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
            @if(Auth::check())
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 overflow-x-auto">
                <h2 class="text-xl sm:text-2xl font-semibold text-orange-500 mb-4">Últimos Registros de Ponto</h2>
                <div class="min-w-full">
                    <!-- Versão Mobile da Tabela (visible apenas em mobile) -->
                    <div class="sm:hidden space-y-4">
                        <div id="logs-cards" class="space-y-3">
                            <!-- Cards serão inseridos aqui via JavaScript para mobile -->
                        </div>
                    </div>

                    <!-- Versão Desktop da Tabela (hidden em mobile) -->
                    <div class="hidden sm:block">
                        <table class="min-w-full">
                            <thead class="bg-orange-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Situação</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tempo Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody id="logs-table-body" class="bg-white divide-y divide-gray-200">
                                <!-- Logs serão inseridos aqui via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Estado vazio -->
                    <div id="empty-state" class="hidden text-center py-6 sm:py-8">
                        <p class="text-gray-500 text-sm sm:text-base">Nenhum registro de ponto encontrado.</p>
                    </div>
                </div>
            </div>
            @endif
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
            const options = { timeZone: 'America/Sao_Paulo', hour12: false };
            const timeString = now.toLocaleTimeString('pt-BR', options);
            const dateString = now.toLocaleDateString('pt-BR', options);
            document.getElementById('current-time').textContent = timeString;
            document.getElementById('current-date').textContent = dateString;
            document.querySelector('.text-orange-400.font-medium').textContent = `${dateString} ${timeString}`;
        }

        // Atualizar a função fetch para incluir o token CSRF e credentials
        function fetchWithAuth(url, options = {}) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            return fetch(url, {
                ...options,
                headers: {
                    ...options.headers,
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'include'
            });
        }

        function updateStatus() {
            @if(Auth::check())
            fetchWithAuth('{{ route("ponto.status") }}')
                .then(response => response.json())
                .then(data => {
                    if (data && data.status === 'success') {
                        const tbody = document.getElementById('logs-table-body');
                        const logsCards = document.getElementById('logs-cards');
                        const emptyState = document.getElementById('empty-state');

                        // Limpar conteúdo existente
                        if (tbody) tbody.innerHTML = '';
                        if (logsCards) logsCards.innerHTML = '';

                        if (Array.isArray(data.logs) && data.logs.length > 0) {
                            if (emptyState) emptyState.classList.add('hidden');

                            data.logs.forEach(log => {
                                const isWorking = log.status === 'Trabalhando';
                                const isLunch = log.status === 'Almoço';
                                const statusClass = isWorking ? 'bg-green-100 text-green-800' :
                                                 isLunch ? 'bg-orange-100 text-orange-800' :
                                                 'bg-red-100 text-red-800';
                                const tempoClass = isWorking ? 'text-green-600 font-medium' :
                                                 isLunch ? 'text-red-600 font-medium' :
                                                 'text-gray-500';

                                // Versão Desktop
                                if (tbody) {
                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${log.user_name || 'N/A'}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.entrada || 'N/A'}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            ${log.entrada_almoco ? 'Almoço ' + log.entrada_almoco : '-'}<br>
                                            ${log.saida_almoco ? 'Retorno ' + log.saida_almoco : ''}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm ${tempoClass} text-center"
                                            data-user-id="${log.user_id}"
                                            data-entrada="${log.entrada}"
                                            data-status="${log.status}"
                                            data-working="${isWorking}">
                                            ${log.tempo_total}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass} ">
                                                ${log.status}
                                            </span>
                                        </td>
                                    `;
                                    tbody.appendChild(row);
                                }

                                // Versão Mobile atualizada
                                if (logsCards) {
                                    const card = document.createElement('div');
                                    card.className = 'bg-white p-4 rounded-lg shadow border border-gray-100';
                                    card.innerHTML = `
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-medium text-gray-900">${log.user_name || 'N/A'}</span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                                                ${log.status}
                                            </span>
                                        </div>
                                        <div class="space-y-1 text-sm">
                                            <p class="text-gray-600">Entrada: ${log.entrada || 'N/A'}</p>
                                            <p class="text-gray-600">Almoço: ${log.entrada_almoco || '-'} - ${log.saida_almoco || '-'}</p>
                                            <p class="text-gray-600">Saída: ${log.saida || '-'}</p>
                                            <p class="${tempoClass}"
                                               data-user-id="${log.user_id}"
                                               data-entrada="${log.entrada}"
                                               data-status="${log.status}"
                                               data-working="${isWorking}">
                                                Total: ${log.tempo_total}
                                            </p>
                                        </div>
                                    `;
                                    logsCards.appendChild(card);
                                }
                            });

                            // Iniciar atualização em tempo real para registros ativos
                            startRealTimeUpdates();
                        } else {
                            if (emptyState) emptyState.classList.remove('hidden');
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao atualizar status:', error);
                    showToast('Erro ao atualizar status', 'error');
                });
            @endif
        }

        function startRealTimeUpdates() {
            const workingElements = document.querySelectorAll('[data-working="true"]');
            workingElements.forEach(element => {
                const userId = element.getAttribute('data-user-id');
                updateTempoTotalAjax(element, userId);
            });
        }

        function updateTempoTotalAjax(element, userId) {
            let isUpdating = true;

            function update() {
                if (!isUpdating) return;

                // Correção da rota para incluir o userId corretamente
                fetchWithAuth(`{{ route("ponto.current-time", ":userId") }}`.replace(':userId', userId))
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'almoco' || data.status === 'Almoço') {
                            element.textContent = data.tempo_total;
                            isUpdating = false; // Para a atualização durante o almoço
                            element.classList.remove('text-green-600', 'font-medium');
                            element.classList.add('text-red-600', 'font-medium');
                            return;
                        }

                        if (data.status === 'Trabalhando') {
                            element.textContent = formatarTempo(data.tempo_total);
                            element.classList.add('text-green-600', 'font-medium');
                            element.classList.remove('text-orange-400');
                        } else if (data.status === 'Finalizado') {
                            element.textContent = formatarTempo(data.tempo_total);
                            isUpdating = false;
                            element.classList.remove('text-green-600', 'font-medium', 'text-orange-400');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao atualizar tempo:', error);
                        isUpdating = false;
                    });
            }

            update();
            const intervalId = setInterval(() => {
                if (isUpdating) {
                    update();
                } else {
                    clearInterval(intervalId);
                }
            }, 1000);
        }

        // Função para formatar o tempo no padrão H:i:s
        function formatarTempo(tempo) {
            if (!tempo) return '00:00:00';

            // Se já estiver no formato correto, retorna o próprio tempo
            if (tempo.match(/^\d{2}:\d{2}:\d{2}$/)) {
                return tempo;
            }

            // Se for número, converte para o formato correto
            const segundos = parseInt(tempo);
            if (isNaN(segundos)) return '00:00:00';

            const horas = Math.floor(segundos / 3600);
            const minutos = Math.floor((segundos % 3600) / 60);
            const segs = segundos % 60;

            return `${String(horas).padStart(2, '0')}:${String(minutos).padStart(2, '0')}:${String(segs).padStart(2, '0')}`;
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

            // Converter FormData para objeto JSON
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            fetchWithAuth('{{ route("ponto.register") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                closeModal();
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    updateStatus();
                } else {
                    showToast(data.message || 'Erro ao registrar ponto', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Erro ao registrar ponto', 'error');
                closeModal();
            });
        });

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
            @if(Auth::check())
            updateStatus();
            setInterval(updateStatus, 30000);
            @endif
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

