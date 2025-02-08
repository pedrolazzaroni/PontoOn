<nav class="bg-orange-400 p-4 shadow ">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="text-white text-lg font-semibold">
            {{ config('app.name', 'Laravel') }}
        </a>
        <div>
            @guest
                <a href="{{ route('login') }}" class="text-white hover:bg-orange-300 px-2 py-1 rounded transition duration-300">Login</a>
            @else
                <a href="{{ route('profile') }}" class="text-white mr-4 hover:underline">{{ Auth::user()->name }}</a>
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}"
                       class="text-gray-600 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                        Painel Administrativo
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

