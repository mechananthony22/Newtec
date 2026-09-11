<?php
$title = "Configurador PC y Setup Gamer";
$style = "builder";
ob_start();
?>

<div class="min-h-screen bg-white py-12">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-4">
                <i class="fa fa-cogs text-red-600 mr-3"></i>
                Configurador Gamer
            </h1>
            <p class="text-gray-600 text-lg">Arma tu PC o Setup perfecto paso a paso</p>
        </div>

        <!-- Mode Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- PC Builder Card -->
            <a href="/perunet/builder/perunet/pc?step=1" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 p-8 hover:scale-105 transition-all duration-300 shadow-2xl">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative z-10 text-center">
                    <div class="mb-6">
                        <i class="fa fa-desktop text-white text-7xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4">Armador de PC</h2>
                    <p class="text-blue-100 mb-6">Selecciona cada componente para tu PC gamer personalizada</p>
                    <ul class="text-left text-blue-100 space-y-2 mb-6">
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Procesador</li>
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Tarjeta Gráfica</li>
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> RAM, Storage y más</li>
                    </ul>
                    <div class="inline-block bg-white text-blue-700 font-bold px-6 py-3 rounded-full group-hover:bg-blue-100 transition">
                        Comenzar <i class="fa fa-arrow-right ml-2"></i>
                    </div>
                </div>
            </a>

            <!-- Setup Builder Card -->
            <a href="/perunet/builder/perunet/setup?step=1" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-600 to-red-800 p-8 hover:scale-105 transition-all duration-300 shadow-2xl">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative z-10 text-center">
                    <div class="mb-6">
                        <i class="fa fa-gamepad text-white text-7xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4">Armador de Setup</h2>
                    <p class="text-red-100 mb-6">Completa tu estación gamer con los mejores periféricos</p>
                    <ul class="text-left text-red-100 space-y-2 mb-6">
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Monitor</li>
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Mouse y Teclado</li>
                        <li><i class="fa fa-check-circle text-green-400 mr-2"></i> Silla, Audífonos y más</li>
                    </ul>
                    <div class="inline-block bg-white text-red-700 font-bold px-6 py-3 rounded-full group-hover:bg-red-100 transition">
                        Comenzar <i class="fa fa-arrow-right ml-2"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Features -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center hover:shadow-lg transition">
                <i class="fa fa-bolt text-yellow-500 text-4xl mb-4"></i>
                <h3 class="text-gray-900 font-bold text-xl mb-2">Rápido y Fácil</h3>
                <p class="text-gray-600">Selecciona componentes en pocos pasos</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center hover:shadow-lg transition">
                <i class="fa fa-dollar-sign text-green-500 text-4xl mb-4"></i>
                <h3 class="text-gray-900 font-bold text-xl mb-2">Precio en Tiempo Real</h3>
                <p class="text-gray-600">Ve el total actualizado al instante</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center hover:shadow-lg transition">
                <i class="fa fa-shopping-cart text-blue-500 text-4xl mb-4"></i>
                <h3 class="text-gray-900 font-bold text-xl mb-2">Agrega al Carrito</h3>
                <p class="text-gray-600">Compra todo junto con un click</p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>
