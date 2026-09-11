<!-- Botón flotante de ayuda -->
<button id="help-toggle-btn" class="fixed bottom-6 right-6 z-50 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg w-14 h-14 flex items-center justify-center focus:outline-none transition-all">
    <i class="fas fa-headset text-2xl"></i>
</button>
<!-- Help Section flotante -->
<div id="help-section" class="fixed bottom-24 right-6 z-50 bg-white rounded-xl shadow-lg p-6 max-w-sm border border-gray-200 hidden animate__animated animate__fadeInUp">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
            <i class="fas fa-headset text-red-600"></i>
            <span>Atención al cliente</span>
        </h3>
        <button id="help-close-btn" class="text-gray-400 hover:text-red-600 text-xl focus:outline-none">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="space-y-3">
        <p class="flex items-center space-x-3 text-sm">
            <i class="fas fa-phone text-red-600 w-4"></i>
            <span class="text-gray-700">978997728</span>
        </p>
        <p class="flex items-center space-x-3 text-sm">
            <i class="fas fa-envelope text-red-600 w-4"></i>
            <span class="text-gray-700">servicioalcliente@perunet.pe</span>
        </p>
        <p class="flex items-start space-x-3 text-sm">
            <i class="fas fa-clock text-red-600 w-4 mt-1"></i>
            <span class="text-gray-700">
                <strong>Lunes a Viernes:</strong><br>
                8:30am - 1:30pm y 3:00pm - 6:30pm<br>
                <strong>Sábados:</strong><br>
                9:00am - 1:00pm
            </span>
        </p>
        <p class="flex items-center space-x-3 text-sm">
            <i class="fas fa-users text-red-600 w-4"></i>
            <span class="text-gray-700">Asesores Comerciales Especializados</span>
        </p>
        <p class="flex items-center space-x-3 text-sm">
            <i class="fas fa-map-marker-alt text-red-600 w-4"></i>
            <a href="/perunet/sedes" class="text-red-600 hover:text-red-800 transition-colors underline">
                Localizador de tiendas
            </a>
        </p>
    </div>
</div>
<script>
    // Mostrar/perunet/ocultar help section
    const helpBtn = document.getElementById('help-toggle-btn');
    const helpSection = document.getElementById('help-section');
    const helpClose = document.getElementById('help-close-btn');
    helpBtn.addEventListener('click', () => {
        helpSection.classList.toggle('hidden');
    });
    helpClose.addEventListener('click', () => {
        helpSection.classList.add('hidden');
    });
</script>
