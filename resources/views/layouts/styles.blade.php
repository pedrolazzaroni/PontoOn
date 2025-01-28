<style>
    .input-focus-effect {
        @apply border-2 border-gray-500 bg-white shadow px-4 py-2 rounded-lg;
        transition: border-color 0.3s ease, box-shadow 0.3s ease, outline-color 0.3s ease;
    }
    .input-focus-effect:hover {
        @apply border-gray-600;
    }
    .input-focus-effect:focus {
        @apply border-orange-400;
        box-shadow: 0 0 5px rgba(251, 146, 60, 0.5);
        outline: 1px solid orange;
    }
</style>
