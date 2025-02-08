@extends('layouts.head')

@section('content')
    @include('layouts.header')

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-orange-500">Editar Perfil</h2>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                           class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                           class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Senha Atual</label>
                    <input type="password" name="current_password" id="current_password"
                           class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                    <input type="password" name="password" id="password"
                           class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="input-focus-effect w-full p-3 rounded-lg outline outline-gray-500 outline-1">
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="px-4 py-2 bg-orange-400 text-white text-sm font-medium rounded-md hover:bg-orange-500">
                        Atualizar Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
