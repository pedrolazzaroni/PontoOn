<nav class="bg-orange-400 p-4 shadow transition duration-500 ease-in-out transform hover:bg-indigo-500">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="text-white text-lg font-semibold">
            {{ config('app.name', 'Laravel') }}
        </a>
        <div>
            @guest
                <a href="{{ route('login') }}" class="text-white mr-4 hover:bg-indigo-500 px-2 py-1 rounded transition duration-300">Entrar</a>
                <a href="{{ route('register') }}" class="text-white hover:bg-indigo-500 px-2 py-1 rounded transition duration-300">Registrar</a>
            @else
                <span class="text-white mr-4">{{ Auth::user()->name }}</span>
                <a href="{{ route('logout') }}" class="text-white hover:bg-indigo-500 px-2 py-1 rounded transition duration-300"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sair
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endguest
        </div>
    </div>
</nav>
