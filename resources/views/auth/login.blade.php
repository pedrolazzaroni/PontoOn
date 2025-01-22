@extends('layouts.head')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-indigo-500">
    <div class="max-w-md w-full bg-white p-8 border border-gray-300 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-center text-orange-400">Entrar</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-gray-700">E-mail</label>
                <input id="email" type="email" class="w-full p-2 border border-gray-300 rounded mt-1" name="email" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Senha</label>
                <input id="password" type="password" class="w-full p-2 border border-gray-300 rounded mt-1" name="password" required>
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-gray-700">Lembrar-me</label>
            </div>
            <div class="mb-4">
                <button type="submit" class="w-full bg-orange-400 text-white p-2 rounded">Entrar</button>
            </div>
            @if (Route::has('password.request'))
                <div class="text-center">
                    <a class="text-orange-400" href="{{ route('password.request') }}">Esqueceu sua senha?</a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
