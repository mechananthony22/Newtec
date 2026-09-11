<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = isset($usuario) ? "Editar Usuario" : "Nuevo Usuario";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>
<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64 flex flex-col items-center justify-center min-h-[calc(100vh-4rem)]">
        <div class="bg-white rounded-xl shadow-md p-8 w-full max-w-lg mt-8">
            <h1 class="text-2xl font-bold text-gray-700 mb-6 text-center">
                <?= isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario' ?>
            </h1>
            <form method="post" action="<?= isset($usuario) ? '/perunet/admin/perunet/usuarios/perunet/actualizar' : '/perunet/admin/perunet/usuarios/perunet/guardar' ?>" class="space-y-5">
                <?php if (isset($usuario)): ?>
                    <input type="hidden" name="id_us" value="<?= htmlspecialchars($usuario['id_us']) ?>">
                <?php endif; ?>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Apellidos</label>
                    <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Correo</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">DNI</label>
                        <input type="text" name="dni" value="<?= htmlspecialchars($usuario['dni'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Teléfono</label>
                        <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                    </div>
                </div>
                <?php if (!isset($usuario)): ?>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Contraseña</label>
                    <input type="password" name="contrasena" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Rol</label>
                    <select name="id_rol" class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id_rol'] ?>" <?= (isset($usuario) && $usuario['id_rol'] == $rol['id_rol']) ? 'selected' : '' ?>><?= htmlspecialchars($rol['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Estado</label>
                    <select name="estado" class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <option value="activo" <?= (isset($usuario) && $usuario['estado'] == 'activo') ? 'selected' : '' ?>>Activo</option>
                        <option value="pendiente" <?= (isset($usuario) && $usuario['estado'] == 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="suspendido" <?= (isset($usuario) && $usuario['estado'] == 'suspendido') ? 'selected' : '' ?>>Suspendido</option>
                    </select>
                </div>
                <div class="flex justify-between mt-6">
                    <a href="/perunet/admin/perunet/usuarios" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white font-semibold rounded-full px-6 py-2 hover:bg-blue-700 transition">
                        <?= isset($usuario) ? 'Actualizar' : 'Crear' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>