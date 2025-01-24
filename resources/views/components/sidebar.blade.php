<div class="sidebar w-32 bg-gray-800 text-white fixed h-screen">
    <ul class="mt-10">
        <li class="mb-4"><a href="{{ route('dashboard') }}" class="block py-2 px-4 hover:bg-gray-700">Dashboard</a></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left py-2 px-4 hover:bg-gray-700">Sair</button>
            </form>
        </li>
    </ul>
</div>
