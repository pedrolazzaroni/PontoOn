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
</style>

<div class="min-h-screen flex">
    <!-- Lado Esquerdo - Formulário -->
    <div class="w-1/2 bg-white flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-orange-400">Bem-vindo de volta!</h2>
                <p class="text-gray-600 mt-2">Por favor, faça login na sua conta.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">E-mail</label>
                    <input id="email" type="email"
                        class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1"
                        name="email" required autofocus>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Senha</label>
                    <input id="password" type="password"
                        class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1"
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
                    <a href="{{ route('register') }}" class="text-orange-400 hover:text-orange-500 font-semibold">
                        Registre-se
                    </a>
                </p>
            </form>
        </div>
    </div>

    <!-- Lado Direito - Imagem -->
    <div class="w-1/2 bg-indigo-500">
        <!-- Espaço reservado para imagem -->
    </div>
</div>
@endsection
