@extends('layouts.head')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-indigo-500">
    <div class="max-w-md w-full bg-white p-8 border border-gray-300 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-center text-orange-400">Registrar</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nome</label>
                <input id="name" type="text" class="w-full p-2 border border-gray-300 rounded mt-1" name="name" required autofocus>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">E-mail</label>
                <input id="email" type="email" class="w-full p-2 border border-gray-300 rounded mt-1" name="email" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Senha</label>
                <input id="password" type="password" class="w-full p-2 border border-gray-300 rounded mt-1" name="password" required>
            </div>
            <div class="mb-4">
                <label for="password-confirm" class="block text-gray-700">Confirmar Senha</label>
                <input id="password-confirm" type="password" class="w-full p-2 border border-gray-300 rounded mt-1" name="password_confirmation" required>
            </div>
            <div class="mb-4">
                <button type="submit" class="w-full bg-orange-400 text-white p-2 rounded">Registrar</button>
            </div>
        </form>
    </div>
</div>
@endsection
