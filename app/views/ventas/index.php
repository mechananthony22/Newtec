<?php
$title = "Confirmar Compra";
$style = "venta";
ob_start();
?>
<!-- SDK Mercado Pago -->
<script src="https://sdk.mercadopago.com/js/v2"></script>

<main class="min-h-screen flex flex-col items-center py-12 bg-gray-50">
    <!-- Indicador de Progreso (Stepper) -->
    <div class="w-full max-w-3xl mb-8 px-4">
        <div class="flex items-center justify-between relative">
            <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2 -z-10"></div>
            <div id="line-progress" class="absolute top-1/2 left-0 h-1 bg-red-600 -translate-y-1/2 -z-10 transition-all duration-500" style="width: 0%;"></div>
            
            <div class="step-item flex flex-col items-center gap-2 active" data-step="1">
                <div class="w-10 h-10 rounded-full border-2 border-red-600 bg-white flex items-center justify-center font-bold text-red-600 step-icon transition-all">1</div>
                <span class="text-sm font-semibold text-gray-700">Datos</span>
            </div>
            <div class="step-item flex flex-col items-center gap-2" data-step="2">
                <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center font-bold text-gray-400 step-icon transition-all">2</div>
                <span class="text-sm font-semibold text-gray-400">Entrega/Pago</span>
            </div>
            <div class="step-item flex flex-col items-center gap-2" data-step="3">
                <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center font-bold text-gray-400 step-icon transition-all">3</div>
                <span class="text-sm font-semibold text-gray-400">Confirmar</span>
            </div>
        </div>
    </div>

    <!-- PASO 1 -->
    <div id="paso1" class="paso-compra w-full max-w-3xl bg-white rounded-xl shadow-lg p-8 animate-fade-in">
        <h2 class="text-2xl font-bold text-red-700 mb-6 flex items-center gap-2">
            <i class="fa fa-user text-black"></i> Datos del Cliente
        </h2>
        <form id="formCliente" class="space-y-4">
            <input type="hidden" name="id" id="usuario_id" value="<?= htmlspecialchars(isset($usuario['id_us']) ? $usuario['id_us'] : '') ?>">
            <div>
                <label for="usuario_nombre" class="block text-gray-700 font-semibold mb-1">Nombres:</label>
                <input id="usuario_nombre" type="text" name="nombre" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['nombre']) ? $usuario['nombre'] : '') ?>" required>
            </div>
            <div>
                <label for="usuario_apellidos" class="block text-gray-700 font-semibold mb-1">Apellidos:</label>
                <input id="usuario_apellidos" type="text" name="apellidos" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['apellidos']) ? $usuario['apellidos'] : '') ?>" required>
            </div>
            <div>
                <label for="usuario_correo" class="block text-gray-700 font-semibold mb-1">Correo electrónico:</label>
                <input id="usuario_correo" type="email" name="correo" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['correo']) ? $usuario['correo'] : '') ?>" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="usuario_dni" class="block text-gray-700 font-semibold mb-1">DNI:</label>
                    <input id="usuario_dni" type="text" name="dni" maxlength="8" pattern="\d{8}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['dni']) ? $usuario['dni'] : '') ?>" required>
                </div>
                <div>
                    <label for="usuario_telefono" class="block text-gray-700 font-semibold mb-1">Teléfono:</label>
                    <input id="usuario_telefono" type="tel" name="telefono" maxlength="9" pattern="\d{9}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" value="<?= htmlspecialchars(isset($usuario['telefono']) ? $usuario['telefono'] : '') ?>" required>
                </div>
            </div>
        </form>
        <div class="flex justify-end mt-8">
            <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition" onclick="irPaso(2)">Siguiente</button>
        </div>
    </div>

    <!-- PASO 2 -->
    <div id="paso2" class="paso-compra w-full max-w-3xl bg-white rounded-xl shadow-lg p-8 hidden animate-fade-in">
        <h2 class="text-2xl font-bold text-red-700 mb-6 flex items-center gap-2">
            <i class="fa fa-truck text-black"></i> Tipo de Entrega
        </h2>
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <button id="domicilio-button" type="button" class="flex-1 bg-red-200 hover:bg-red-100 text-red-700 font-bold py-3 px-6 rounded-lg transition" onclick="seleccionarEntrega('domicilio')">Domicilio</button>
            <button id="tienda-button" type="button" class="flex-1 bg-gray-200 hover:bg-gray-100 text-gray-700 font-bold py-3 px-6 rounded-lg transition" onclick="seleccionarEntrega('tienda')">Recojo en Tienda</button>
        </div>

        <!-- FORMULARIO DE DOMICILIO - JUSTO DEBAJO DE LOS BOTONES -->
        <div id="domicilio-section" class="mb-6 hidden animate-fade-in border-t border-gray-100 pt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                 Dirección de Entrega
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="departamento" class="block text-gray-700 font-semibold mb-1">Departamento:</label>
                    <input id="departamento" type="text" name="departamento" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="provincia" class="block text-gray-700 font-semibold mb-1">Provincia:</label>
                    <input id="provincia" type="text" name="provincia" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="distrito" class="block text-gray-700 font-semibold mb-1">Distrito:</label>
                    <input id="distrito" type="text" name="distrito" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="calle" class="block text-gray-700 font-semibold mb-1">Calle:</label>
                    <input id="calle" type="text" name="calle" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="numero" class="block text-gray-700 font-semibold mb-1">Número:</label>
                    <input id="numero" type="text" name="numero" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label for="piso" class="block text-gray-700 font-semibold mb-1">Piso:</label>
                    <input id="piso" type="text" name="piso" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="md:col-span-2">
                    <label for="referencia" class="block text-gray-700 font-semibold mb-1">Referencia:</label>
                    <input id="referencia" type="text" name="referencia" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>
        </div>

        <!-- LISTA DE SEDES - JUSTO DEBAJO DE LOS BOTONES -->
        <div id="tienda-section" class="mb-6 hidden animate-fade-in border-t border-gray-100 pt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Sedes disponibles</h3>
            <div class="space-y-2">
                <?php foreach ($sucursales as $s): ?>
                    <label class="flex items-center gap-2 bg-gray-100 rounded-lg p-3 cursor-pointer hover:bg-red-100 transition border border-transparent hover:border-red-200">
                        <input type="radio" name="sucursal" value="<?= $s['id_sucur'] ?>" class="w-4 h-4 text-red-600 focus:ring-red-500">
                        <span class="text-gray-700 text-sm">📍 <?= $s['nombre'] ?> - <?= $s['direccion'] ?> - <?= $s['ciudad'] ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- PAGO SEGURO - AL FINAL DEL PASO -->
        <div class="mb-6 py-6 border-t border-gray-100">
            <h3 class="text-xl font-bold text-red-700 mb-4 flex items-center gap-2">
                <i class="fa fa-shield-alt text-black"></i> Pago Seguro
            </h3>
            <div id="paymentBrick_container" class="bg-gray-50 rounded-xl p-4 min-h-[300px]">
                <!-- El formulario de Mercado Pago aparecerá aquí -->
                <p id="mp_loading_text" class="text-center text-gray-500 py-10 animate-pulse">Cargando pasarela segura...</p>
            </div>
        </div>
        
        <div class="flex justify-start mt-8">
            <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition" onclick="irPaso(1)">Regresar</button>
        </div>
    </div>

    <!-- PASO 3 -->
    <div id="paso3" class="paso-compra w-full max-w-3xl bg-white rounded-xl shadow-lg p-8 hidden flex flex-col items-center animate-fade-in text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
            <i class="fa fa-shopping-bag text-3xl text-green-600"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">¡Compra en Proceso!</h2>
        <p class="text-gray-600 mb-8">Estamos procesando tu pago de forma segura...</p>
        
        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-green-500 animate-pulse" style="width: 70%"></div>
        </div>
    </div>
</main>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

<script type="module" src="/perunet/public/js/venta.js?v=13"></script>
