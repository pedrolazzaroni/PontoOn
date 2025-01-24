<nav class="bg-orange-400 p-4 shadow ">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="text-white text-lg font-semibold">
            {{ config('app.name', 'Laravel') }}
        </a>
        <div>
            @guest
                <!-- Corrigido para usar a rota 'auth' -->
                <a href="{{ route('auth') }}" class="text-white mr-4 hover:bg-indigo-500 px-2 py-1 rounded transition duration-300">Entrar</a>
                <a href="{{ route('auth') }}" class="text-white hover:bg-indigo-500 px-2 py-1 rounded transition duration-300">Registrar</a>
            @else
                <span class="text-white mr-4">{{ Auth::user()->name }}</span>
                @if(Auth::user()->is_admin)
                    <a href="{{ route('historico.index') }}"
                       class="text-gray-600 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                        Histórico de Pontos
                    </a>
                @endif
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

