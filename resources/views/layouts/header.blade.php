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

    <!-- Mobile Menu (Sliding Panel) -->
    <div id="mobileMenuContainer" class="fixed inset-y-0 left-0 w-64 bg-orange-400 shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50 opacity-0 md:hidden">
        <div class="h-full flex flex-col">
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Login</span>
                    </a>
                @else
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/10 text-white px-4 py-3 rounded-lg hover:bg-white/20 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Perfil</span>
                    </a>

                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 bg-white/10 text-white px-4 py-3 rounded-lg hover:bg-white/20 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    @endif

                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="flex items-center space-x-2 bg-white/10 text-white px-4 py-3 rounded-lg hover:bg-white/20 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sair</span>
                    </a>
                @endguest
            </nav>
        </div>
    </div>

    <!-- Backdrop Overlay -->
    <div id="mobileMenuOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 z-40 md:hidden"></div>
</nav>

<script>
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    const mobileMenuContainer = document.getElementById('mobileMenuContainer');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    // Add this function to handle initial state
    function initializeMobileMenu() {
        // Set initial opacity after a small delay to prevent flash
        setTimeout(() => {
            mobileMenuContainer.style.opacity = '1';
        }, 100);
    }

    function openMenu() {
        mobileMenuContainer.classList.add('translate-x-0');
        mobileMenuContainer.classList.remove('-translate-x-full');
        mobileMenuOverlay.classList.add('opacity-100');
        mobileMenuOverlay.classList.remove('opacity-0', 'pointer-events-none');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        mobileMenuContainer.classList.remove('translate-x-0');
        mobileMenuContainer.classList.add('-translate-x-full');
        mobileMenuOverlay.classList.remove('opacity-100');
        mobileMenuOverlay.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = '';
    }

    // Initialize the menu after DOM is loaded
    document.addEventListener('DOMContentLoaded', initializeMobileMenu);
    mobileMenuToggle.addEventListener('click', openMenu);
    mobileMenuClose.addEventListener('click', closeMenu);
    mobileMenuOverlay.addEventListener('click', closeMenu);
</script>
