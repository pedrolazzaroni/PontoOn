<footer class="site-footer bg-orange-500 p-4 mt-8 shadow transition duration-500 ease-in-out transform">
    <div class="container mx-auto text-center text-white">
        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos os direitos reservados.
    </div>
</footer>
<script src="{{ mix('js/app.js') }}" defer></script>
