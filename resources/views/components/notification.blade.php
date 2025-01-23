@if (session('success') || session('error') || session('info'))
<div id="notification"
     class="fixed top-4 right-4 bg-orange-400 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 z-50 transform translate-x-full">
    <span class="flex items-center whitespace-nowrap">
        @if(session('success'))
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        @endif
        @if(session('error'))
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        @endif
        @if(session('info'))
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('info') }}
        @endif
    </span>
    <button onclick="closeNotification()" class="text-white hover:text-gray-200 transition-colors ml-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>

<style>
    #notification {
        transition: transform 0.5s ease-in-out, border-color 0.3s ease, box-shadow 0.3s ease;
        border: 2px solid gray-500;
    }

    #notification.show {
        transform: translateX(0);
        border-color: orange-400;
        box-shadow: 0 0 10px rgba(251, 146, 60, 0.5);
    }

    #notification.hide {
        transform: translateX(100%);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notification = document.getElementById('notification');

        if (notification) {
            // Mostrar notificação
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            // Esconder notificação após 2 segundos de visível
            setTimeout(() => {
                notification.classList.remove('show');
                notification.classList.add('hide');

                // Remover elemento após a animação
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 2100); // 100ms para mostrar + 2000ms visível
        }
    });

    function closeNotification() {
        const notification = document.getElementById('notification');
        notification.classList.remove('show');
        notification.classList.add('hide');

        setTimeout(() => {
            notification.remove();
        }, 500);
    }
</script>
@endif
