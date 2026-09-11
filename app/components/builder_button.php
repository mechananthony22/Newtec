<!-- Botón flotante para acceder al Configurador -->
<a href="/perunet/builder" class="fixed bottom-6 right-24 z-50 group">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full shadow-2xl p-4 flex items-center justify-center transition-all duration-300 hover:scale-110">
        <i class="fa fa-cogs text-2xl"></i>
    </div>
    <div class="absolute bottom-full right-0 mb-2 bg-gray-900 text-white text-xs px-3 py-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
        Configurador PC
    </div>
</a>

<style>
@keyframes pulse-glow-blue {
    0%, 100% {
        box-shadow: 0 0 20px rgba(37, 99, 235, 0.5);
    }
    50% {
        box-shadow: 0 0 40px rgba(37, 99, 235, 0.8);
    }
}

.fixed.bottom-6.right-24 > div {
    animation: pulse-glow-blue 2s infinite;
}
</style>
