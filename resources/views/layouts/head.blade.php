<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    {{-- @vite('resources/css/app.css') --}}
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    @include('layouts.styles')
</head>
<body class="bg-gray-100">
    @include('components.notification')
    @yield('content')
</body>
</html>

