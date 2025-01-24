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

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8">
        <!-- User Info Section -->
        <div class="border-b border-gray-200 pb-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Bem-vindo, {{ Auth::user()->name }}</h2>
                    <p class="text-gray-600">{{ Auth::user()->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Último acesso:</p>
                    <p class="text-orange-400 font-medium">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Clock Section -->
        <div class="bg-orange-50 rounded-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Registro de Ponto</h1>
            <div class="text-center space-y-2">
                <div id="current-time" class="text-6xl font-bold text-orange-400"></div>
                <div id="current-date" class="text-2xl text-gray-600"></div>
            </div>
        </div>

        <!-- Action Section -->
        <div class="space-y-6">
            <div class="flex justify-center">
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

            <!-- Status Badge -->
            <div class="flex flex-col items-center space-y-4">
                <span id="status-badge"
                    class="px-6 py-3 rounded-full text-lg font-semibold inline-flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full status-indicator"></span>
                    <span id="status-text">Carregando...</span>
                </span>
                <div id="last-record" class="text-gray-600 font-medium text-lg"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateDateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString();
        document.getElementById('current-date').textContent = now.toLocaleDateString();
    }

    function updateStatus() {
        fetch('/ponto/status')
            .then(response => response.json())
            .then(data => {
                const statusBadge = document.getElementById('status-badge');
                const statusText = document.getElementById('status-text');
                const statusIndicator = statusBadge.querySelector('.status-indicator');

                if (data.working) {
                    statusBadge.className = 'px-6 py-3 rounded-full text-lg font-semibold inline-flex items-center space-x-2 bg-green-100 text-green-800';
                    statusIndicator.className = 'w-3 h-3 rounded-full bg-green-500';
                    statusText.textContent = 'Trabalhando';
                } else {
                    statusBadge.className = 'px-6 py-3 rounded-full text-lg font-semibold inline-flex items-center space-x-2 bg-red-100 text-red-800';
                    statusIndicator.className = 'w-3 h-3 rounded-full bg-red-500';
                    statusText.textContent = 'Ausente';
                }

                const lastRecord = document.getElementById('last-record');
                if (data.last_record) {
                    lastRecord.textContent = `Último registro: ${data.last_record}`;
                } else {
                    lastRecord.textContent = 'Nenhum registro encontrado';
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar status:', error);
            });
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
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

    // Initialize
    setInterval(updateDateTime, 1000);
    updateDateTime();
    updateStatus();
    // Atualiza o status a cada 30 segundos
    setInterval(updateStatus, 30000);
});
</script>
@endsection

