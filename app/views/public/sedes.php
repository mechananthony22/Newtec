<?php
$title = "Perunet | Sedes";
ob_start();
?>
<div class="w-full max-w-5xl mx-auto py-10 px-4">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-red-700 mb-2 flex items-center justify-center gap-2">
                <i class="fa fa-map-marker-alt text-black"></i> Localizador de tiendas
            </h2>
            <p class="text-gray-700">Encuentra la tienda PeruNet más cercana</p>
        </div>
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <div class="w-full md:w-1/3 mb-6 md:mb-0">
                <div class="mb-4">
                    <label for="sede-select" class="block text-gray-700 font-semibold mb-2">Selecciona una sede:</label>
                    <select id="sede-select" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="default">Sedes</option>
                        <option value="colibri">Colibrí</option>
                        <option value="leguia">Leguía</option>
                        <option value="pedro-ruiz">Pedro Ruiz</option>
                    </select>
                </div>
            </div>
            <div class="w-full md:w-2/3 h-[400px]">
                <div id="map" class="w-full h-full rounded-lg border border-gray-200"></div>
            </div>
        </div>
    </div>
</div>
<!-- Chatbot y scripts específicos -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="/perunet/public/css/chatbot.css">
<div id="chatbot-container">
    <div id="chatbot-header">
        <h3>Chatbot de Ayuda</h3>
        <button id="close-chatbot-btn">&times;</button>
    </div>
    <div id="chatbot-body">
        <div class="chatbot-message">
            <p>Bienvenido al chatbot de PeruNet. ¿En qué puedo ayudarte?</p>
        </div>
    </div>
    <div id="chatbot-input">
        <input type="text" id="user-input" placeholder="Escribe tu mensaje...">
        <button id="send-btn">Enviar</button>
    </div>
</div>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="/perunet/public/js/sedes.js"></script>
<script src="/perunet/public/js/chatbot.js"></script>
<script src="/perunet/public/js/funciones.js" defer></script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>