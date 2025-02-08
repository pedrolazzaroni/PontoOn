@extends('layouts.head')

@section('content')
<style>
    .input-focus-effect {
        @apply border-2 border-gray-500 bg-white shadow;
        transition: border-color 0.3s ease, box-shadow 0.3s ease, outline-color 0.3s ease;
    }
    .input-focus-effect:hover {
        @apply border-gray-600;
    }
    .input-focus-effect:focus {
        @apply border-orange-400;
        box-shadow: 0 0 5px rgba(251, 146, 60, 0.5);
        outline: 1px solid orange;
    }

    .form-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-container {
        position: absolute;
        width: 100%;
        max-width: 400px;
        transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out;
        opacity: 0;
        transform: scale(0.8);
        pointer-events: none;
    }
    .form-container.active {
        opacity: 1;
        transform: scale(1);
        pointer-events: all;
    }
    .form-container.transitioning {
        transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out;
    }
</style>

<div class="min-h-screen flex relative">
    <!-- Lado Esquerdo - Formulário -->
    <div class="w-1/2 bg-white flex items-center justify-center p-4 relative">
        <div class="w-full max-w-md form-wrapper">
            <!-- Formulário de Login -->
            <div id="login-form" class="form-container active">

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-orange-400">Bem-vindo de volta!</h2>
                    <p class="text-gray-600 mt-2">Por favor, faça login na sua conta.</p>
                </div>

                <form method="POST" action="{{ route('login.submit') }}">  <!-- Atualizado para usar login.submit -->
                    @csrf
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">E-mail</label>
                        <input id="email" type="email"
                            class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-300 outline-1"
                            name="email" required autofocus>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Senha</label>
                        <input id="password" type="password"
                            class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-300 outline-1"
                            name="password" required>
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-400 text-white p-3 rounded-lg font-semibold
                               hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-opacity-50
                               transition duration-200">
                        Entrar
                    </button>

                    <p class="mt-6 text-center text-sm text-gray-600">
                        Não tem uma conta?
                        <a href="#" id="show-register" class="text-orange-400 hover:text-orange-500 font-semibold">
                            Registre-se
                        </a>
                    </p>
                </form>
            </div>

            <!-- Formulário de Registro -->
            <div id="register-form" class="form-container">

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-orange-400">Criar Conta</h2>
                    <p class="text-gray-600 mt-2">Preencha os dados para se registrar.</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-6">
                            <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Nome Completo</label>
                            <input id="name" type="text"
                                class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-400 outline-1"
                                name="name" required autofocus>
                        </div>

                        <div class="mb-6">
                            <label for="cpf" class="block text-gray-700 text-sm font-semibold mb-2">CPF</label>
                            <input id="cpf" type="text"
                                class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-400 outline-1"
                                name="cpf" required
                                pattern="\d{3}\.\d{3}\.\d{3}-\d{2}"
                                placeholder="000.000.000-00">
                        </div>

                        <div class="mb-6">
                            <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">E-mail</label>
                            <input id="email" type="email"
                                class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-400 outline-1"
                                name="email" required>
                        </div>

                        <div class="mb-6">
                            <label for="nome_empresa" class="block text-gray-700 text-sm font-semibold mb-2">Nome da Empresa</label>
                            <input id="nome_empresa" type="text"
                                class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-400 outline-1"
                                name="nome_empresa" required>
                        </div>

                        <div class="mb-6">
                            <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Senha</label>
                            <input id="password" type="password"
                                class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-400 outline-1"
                                name="password" required>
                        </div>

                        <div class="mb-6">
                            <label for="password-confirm" class="block text-gray-700 text-sm font-semibold mb-2">Confirmar Senha</label>
                            <input id="password-confirm" type="password"
                                class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-400 outline-1"
                                name="password_confirmation" required>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-400 text-white p-3 rounded-lg font-semibold
                               hover:bg-orange-500 transition duration-200">
                        Registrar
                    </button>

                    <p class="mt-6 text-center text-sm text-gray-600">
                        Já tem uma conta?
                        <a href="#" id="show-login" class="text-orange-400 hover:text-orange-500 font-semibold">
                            Faça login
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Lado Direito - Imagem -->
    <div class="w-1/2 bg-gray-400 flex flex-col items-center justify-center gap-12">
        <div class="flex items-center gap-6 bg-white px-12 py-8 rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-300">
            <svg class="w-24 h-24 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex flex-col justify-center">
                <span class="text-6xl font-bold text-orange-500 leading-none mb-2">PontoOn</span>
                <span class="text-2xl text-orange-400 leading-none">Controle de Ponto</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const showRegister = document.getElementById('show-register');
        const showLogin = document.getElementById('show-login');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        showRegister.addEventListener('click', function(e) {
            e.preventDefault();
            // Iniciar animação de contração do login
            loginForm.classList.add('transitioning');
            loginForm.classList.remove('active');

            // Após a animação, mostrar o registro
            setTimeout(() => {
                loginForm.classList.remove('transitioning');
                registerForm.classList.add('active');
            }, 600); // Duração da animação
        });

        showLogin.addEventListener('click', function(e) {
            e.preventDefault();
            // Iniciar animação de contração do registro
            registerForm.classList.add('transitioning');
            registerForm.classList.remove('active');

            // Após a animação, mostrar o login
            setTimeout(() => {
                registerForm.classList.remove('transitioning');
                loginForm.classList.add('active');
            }, 600); // Duração da animação
        });
    });
</script>
@endsection
