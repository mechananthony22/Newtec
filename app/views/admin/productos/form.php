<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = isset($producto) ? "Editar Producto" : "Nuevo Producto";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<?php
$isEditing = isset($producto);
$action = $isEditing ? "/perunet/admin/perunet/productos/perunet/actualizar" : "/perunet/admin/perunet/productos/perunet/guardar";
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64 flex flex-col items-center justify-center min-h-[calc(100vh-4rem)]">
        <div class="bg-white rounded-xl shadow-md p-8 w-full max-w-lg mt-8">
            <h1 class="text-2xl font-bold text-gray-700 mb-6 text-center">
                <?= isset($producto) ? 'Editar Producto' : 'Nuevo Producto' ?>
            </h1>
            <form method="post" action="<?= isset($producto) ? '/perunet/admin/perunet/productos/perunet/actualizar' : '/perunet/admin/perunet/productos/perunet/guardar' ?>" enctype="multipart/perunet/form-data" class="space-y-5">
                <?php if (isset($producto)): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($producto['id_pro']) ?>">
                <?php endif; ?>

                <input type="hidden" id="isEditing" value="<?= $isEditing ?>">

                <div>
                    <label class="block text-gray-600 font-medium mb-1">Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 font-medium mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3" required class="w-full border border-gray-300 rounded-2xl px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                </div>
                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Precio</label>
                        <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($producto['precio'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Stock</label>
                        <input type="number" name="stock" value="<?= htmlspecialchars($producto['stock'] ?? '') ?>" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Categoría</label>
                        <select name="id_categoria" id="categoria" required
                            class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id_cat'] ?>"
                                    <?= ($isEditing && $producto['id_categoria'] == $categoria['id_cat']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Subcategoría</label>
                        <select name="id_subcategoria" id="subCategoria" required
                            class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                            <option value="">-- Seleccione una categoría primero --</option>
                            <?php if ($isEditing): ?>
                                <?php foreach ($subcategorias as $sub): ?>
                                    <option value="<?= $sub['id'] ?>"
                                        <?= ($producto['id_subcategoria'] == $sub['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sub['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Marca</label>
                        <select name="id_marca" id="marca" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                            <?php foreach ($marcas as $marca): ?>
                                <option value="<?= $marca['id_mar'] ?>" <?= (isset($producto) && $producto['id_marca'] == $marca['id_mar']) ? 'selected' : '' ?>><?= htmlspecialchars($marca['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Modelo</label>
                        <select name="id_modelo" id="modelo" required class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                            <?php foreach ($modelos as $modelo): ?>
                                <option value="<?= $modelo['id_mod'] ?>" <?= (isset($producto) && $producto['id_modelo'] == $modelo['id_mod']) ? 'selected' : '' ?>><?= htmlspecialchars($modelo['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-600 font-medium mb-1">Imagen</label>
                        <input type="file" name="imagen_file" accept="image/perunet/*" class="w-full border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <?php if (isset($producto) && !empty($producto['imagen'])): ?>
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600 mb-2">Imagen actual:</p>
                                <img src="/perunet/public/img/perunet/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen actual" class="w-32 h-32 object-contain rounded-lg border border-gray-200 bg-white mx-auto">
                            </div>
                            <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($producto['imagen']) ?>">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <a href="/perunet/admin/perunet/productos" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white font-semibold rounded-full px-6 py-2 hover:bg-blue-700 transition">
                        <?= isset($producto) ? 'Actualizar' : 'Crear' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
    <script src="/perunet/public/js/productos.js"></script>
</body>

</html>