<nav class="bg-orange-400 p-4 shadow">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-md h-10">
            {{-- <img src="{{ asset('assets/images/clock.svg') }}" alt="Clock" class="w-5 h-5 text-orange-500"> --}}
            <div class="flex flex-col justify-center">
                <span class="text-sm font-bold text-orange-500">PontoOn</span>
                <span class="text-[10px] text-orange-400">Controle de Ponto</span>
            </div>
        </a>
        <div class="flex items-center space-x-4">
            @guest
                <a href="{{ route('login') }}" class="flex items-center space-x-2 bg-white text-orange-500 px-4 py-2 rounded-lg">
                    <img src="{{ asset('assets/images/login.svg') }}" alt="Login" class="w-5 h-5">
                    <span>Login</span>
                </a>
            @else
                <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white text-orange-500 px-4 py-2 rounded-lg">
                    <img src="{{ asset('assets/images/profile.svg') }}" alt="Profile" class="w-5 h-5">
                    <span>{{ Auth::user()->name }}</span>
                </a>

                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 bg-white text-orange-500 px-4 py-2 rounded-lg">
                        <img src="{{ asset('assets/images/admin.svg') }}" alt="Admin" class="w-5 h-5">
                        <span>Painel Administrativo</span>
                    </a>
                @endif

                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="flex items-center space-x-2 bg-white text-orange-500 px-4 py-2 rounded-lg">
                    <img src="{{ asset('assets/images/logout.svg') }}" alt="Logout" class="w-5 h-5">
                    <span>Sair</span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endguest
        </div>
    </div>
</nav>
