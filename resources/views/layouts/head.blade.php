<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Remover o CDN do Tailwind CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
    <!-- Reativar a inclusão do arquivo CSS compilado -->
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    <div id="app">
        <!-- Header -->
        @include('layouts.header')
        <!-- Main Content -->
        <main class="py-4">
            @yield('content')
        </main>
        <!-- Footer -->
        @include('layouts.footer')
    </div>
    <script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>
