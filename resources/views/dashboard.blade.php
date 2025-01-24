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

            <!-- Status Badge -->
            <div class="flex flex-col items-center space-y-4">
                <span id="status-badge" 
                    class="px-6 py-3 rounded-full text-lg font-semibold inline-flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full"></span>
                    <span class="status-text"></span>
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
                if (data.working) {
                    statusBadge.className = 'px-6 py-3 rounded-full text-lg font-semibold inline-flex items-center space-x-2 bg-green-100 text-green-800';
                    statusBadge.innerHTML = '<span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>Trabalhando';
                } else {
                    statusBadge.className = 'px-6 py-3 rounded-full text-lg font-semibold inline-flex items-center space-x-2 bg-red-100 text-red-800';
                    statusBadge.innerHTML = '<span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>Ausente';
                }
                if (data.last_record) {
                    document.getElementById('last-record').textContent = `Último registro: ${data.last_record}`;
                }
            });
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        toastMessage.textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    document.getElementById('ponto-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/ponto/register', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message);
                updateStatus();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erro ao registrar ponto');
        });
    });

    setInterval(updateDateTime, 1000);
    updateDateTime();
    updateStatus();
});
</script>
@endsection

