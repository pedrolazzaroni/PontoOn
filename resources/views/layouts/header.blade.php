<nav class="bg-orange-400 p-4 shadow relative">
    <!-- Main Navigation Content -->
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-md h-10">
            <div class="flex flex-col justify-center">
                <span class="text-sm font-bold text-orange-500">PontoOn</span>
                <span class="text-[10px] text-orange-400">Controle de Ponto</span>
            </div>
        </a>

        <div class="hidden md:flex items-center space-x-4">
            @guest
                <a href="{{ route('login') }}" class="flex items-center space-x-2 bg-white text-orange-500 px-4 py-2 rounded-lg">
                     <div class="bg-orange-500 rounded-full p-1">
                        <img src="{{ asset('assets/images/login.svg') }}" alt="Login" class="w-5 h-5 invert">
                     </div>
                    <span>Login</span>
                </a>
            @else
                <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white text-orange-500 px-4 py-2 rounded-lg">
                    <div class="bg-orange-500 rounded-full p-1">
                        <img src="{{ asset('assets/images/profile.svg') }}" alt="Profile" class="w-5 h-5 invert">
                    </div>
                    <span>{{ Auth::user()->name }}</span>
                </a>

                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 bg-white text-orange-500 px-4 py-2 rounded-lg">
                        <div class="bg-orange-500 rounded-full p-1">
                            <img src="{{ asset('assets/images/admin.svg') }}" alt="Admin" class="w-5 h-5 invert">
                        </div>
                        <span>Painel Administrativo</span>
                    </a>
                @endif

                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="flex items-center space-x-2 bg-white text-orange-500 px-4 py-2 rounded-lg">
                    <div class="bg-orange-500 rounded-full p-1">
                        <img src="{{ asset('assets/images/logout.svg') }}" alt="Logout" class="w-5 h-5 invert">
                    </div>
                    <span>Sair</span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endguest
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="md:hidden">
            <button id="mobileMenuToggle">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu (Sliding Panel) wrapped in hidden by default -->
    <div id="mobileMenuContainer" class="hidden fixed inset-y-0 left-0 w-64 bg-orange-400 shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50 md:hidden">
        <!-- Menu Header -->
        <div class="p-4 flex justify-between items-center border-b border-orange-300">
            <span class="text-white font-bold">Menu</span>
            <button id="mobileMenuClose" class="p-1 hover:bg-orange-500 rounded">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <!-- Menu Links -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            @guest
                <a href="{{ route('login') }}" class="flex items-center space-x-2 bg-white/10 text-white px-4 py-3 rounded-lg hover:bg-white/20 transition-colors duration-200">
                    <!-- ...icon... -->
                    <span>Login</span>
                </a>
            @else
                <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/10 text-white px-4 py-3 rounded-lg hover:bg-white/20 transition-colors duration-200">
                    <!-- ...icon... -->
                    <span>Perfil</span>
                </a>
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 bg-white/10 text-white px-4 py-3 rounded-lg hover:bg-white/20 transition-colors duration-200">
                        <!-- ...icon... -->
                        <span>Painel Administrativo</span>
                    </a>
                @endif
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="flex items-center space-x-2 bg-white/10 text-white px-4 py-3 rounded-lg hover:bg-white/20 transition-colors duration-200">
                    <!-- ...icon... -->
                    <span>Sair</span>
                </a>
            @endguest
        </nav>
    </div>

    <!-- Backdrop Overlay wrapped in hidden by default -->
    <div id="mobileMenuOverlay" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40 md:hidden"></div>
</nav>

<script>
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    const mobileMenuContainer = document.getElementById('mobileMenuContainer');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    function openMenu() {
        mobileMenuContainer.classList.remove('hidden');
        setTimeout(() => {
            mobileMenuContainer.classList.remove('-translate-x-full');
        }, 10);
        mobileMenuOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        mobileMenuContainer.classList.add('-translate-x-full');
        mobileMenuOverlay.classList.add('hidden');
        setTimeout(() => {
            mobileMenuContainer.classList.add('hidden');
        }, 300);
        document.body.style.overflow = '';
    }

    mobileMenuToggle.addEventListener('click', openMenu);
    mobileMenuClose.addEventListener('click', closeMenu);
    mobileMenuOverlay.addEventListener('click', closeMenu);
</script>
