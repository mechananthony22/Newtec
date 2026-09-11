<footer class="bg-black text-white">
    <!-- Franja roja superior -->
    <div class="w-full h-6 bg-red-700"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 items-start">
            <!-- Logo y dirección -->
            <div class="space-y-4 flex flex-col items-center md:items-start">
                <a href="/perunet/" class="inline-block">
                    <img src="/perunet/public/img/EMPRESA/newtec_logo.png?v=2.0" alt="Logo NewTec"
                        class="h-16 w-auto drop-shadow-lg mb-2 rounded-lg bg-black p-2 border-2 border-red-700" /perunet/>
                </a>
                <div class="space-y-2 text-sm text-gray-300 text-center md:text-left">
                    <p>AV PEDRO RUIZ GALLO NRO. 920 INT. 879<br>CERCADO DE CHICLAYO</p>
                </div>
            </div>

            <!-- Secciones -->
            <div class="space-y-4 flex flex-col items-center md:items-start">
                <h3 class="text-lg font-semibold">Secciones</h3>
                <ul class="space-y-1 text-sm">
                    <li><a id="nosotros-link"
                            class="hover:text-red-400 transition-colors hover:cursor-pointer">Nosotros</a></li>
                    <li><a id="servicios-link"
                            class="hover:text-red-400 transition-colors hover:cursor-pointer">Servicios</a></li>
                    <!-- <li><a href="/perunet/contacto" class="hover:text-red-400 transition-colors">Contáctenos</a></li> -->
                    <li><a id="terminos-link" class="hover:text-red-400 transition-colors hover:cursor-pointer">Términos
                            y condiciones</a></li>
                    <li><a href="/perunet/login" class="hover:text-red-400 transition-colors">Intranet</a></li>
                </ul>
                <!-- <a href="#" class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded shadow font-semibold text-xs">
                    <i class="fas fa-file-invoice mr-2"></i> CONSULTE SU COMPROBANTE ELECTRÓNICO
                </a> -->
            </div>

            <!-- Detalles de contacto -->
            <div class="space-y-4 flex flex-col items-center md:items-start">
                <h3 class="text-lg font-semibold">Detalles de Contacto</h3>
                <ul class="space-y-1 text-sm">
                    <li class="flex items-center space-x-2"><i
                            class="fas fa-envelope text-red-400"></i><span>servicioalcliente@newtec.pe</span></li>
                    <li class="flex items-center space-x-2"><i
                            class="fas fa-envelope text-red-400"></i><span>chiclayo01@newtec.pe</span></li>
                    <li class="flex items-center space-x-2"><i class="fas fa-phone text-red-400"></i><span>Atención al
                            cliente: 978997728</span></li>
                    <li class="flex items-center space-x-2"><i class="fas fa-phone text-red-400"></i><span>Ventas 02:
                            965941380</span></li>
                </ul>
            </div>

            <!-- Redes sociales -->
            <div class="space-y-4 flex flex-col items-center md:items-start">
                <h3 class="text-lg font-semibold">Redes Sociales</h3>
                <ul class="space-y-1 text-sm">
                    <li class="flex items-center space-x-2"><i
                            class="fab fa-facebook text-red-400"></i><span>Facebook</span></li>
                    <li class="flex items-center space-x-2"><i
                            class="fab fa-instagram text-red-400"></i><span>Instagram</span></li>
                    <li class="flex items-center space-x-2"><i
                            class="fab fa-whatsapp text-red-400"></i><span>Whatsapp</span></li>
                    <!-- <li class="flex items-center space-x-2"><i class="fab fa-tiktok text-red-400"></i><span>Tiktok</span></li> -->
                </ul>
                <!-- <a href="#" class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded shadow font-semibold text-xs">
                    <i class="fas fa-book mr-2"></i> Libro de Reclamaciones
                </a> -->
            </div>
        </div>
    </div>
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-sm text-gray-400">
                Copyright <?= date('Y') ?> © <span class="font-semibold text-red-400">NewTec</span> Todos los derechos
                reservados.
            </p>
        </div>
    </div>
</footer>

<!-- Overlay para contenido dinámico -->
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[80vh] overflow-y-auto relative">
            <!-- Botón de cierre -->
            <button id="close-overlay"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors z-10">
                <i class="fas fa-times text-xl"></i>
            </button>
            <!-- Contenido dinámico -->
            <div id="overlay-text" class="p-6">
                <!-- Aquí se insertará el contenido dinámico -->
            </div>
        </div>
    </div>
</div>