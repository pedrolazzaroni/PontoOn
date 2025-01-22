@extends('layouts.head')

@section('content')
<div class="container mx-auto">
    <div class="bg-white p-8 border border-gray-300 rounded-lg shadow-lg transition duration-500 ease-in-out transform hover:scale-105">
        <h2 class="text-2xl font-bold mb-6 text-center text-orange-400">Painel de Controle</h2>
        <p class="text-center text-indigo-500">Bem-vindo, {{ Auth::user()->name }}!</p>
        <div id="app">
            <example-component></example-component>
        </div>
    </div>
</div>
@endsection
