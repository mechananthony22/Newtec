<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Subcategorías";
include __DIR__ . '/../../../components/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64 mt-13 min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row-reverse md:items-center md:justify-between gap-4 mb-3">
                <button id="openModalBtn" class="bg-yellow-100 text-yellow-700 font-semibold rounded-full px-6 py-2 hover:bg-yellow-200 transition">
                    + Añadir Subcategoría
                </button>
            </div>

            <!-- Mensaje de confirmación -->
            <?php if (isset($_GET['mensaje'])): ?>
                <div class="mb-6 p-4 rounded-xl border border-green-200 bg-green-50 text-green-800">
                    <?php
                    switch ($_GET['mensaje']) {
                        case 'guardado':
                            echo "✅ Subcategoría creada correctamente.";
                            break;
                        case 'actualizado':
                            echo "✏️ Subcategoría actualizada correctamente.";
                            break;
                        case 'eliminado':
                            echo "🗑️ Subcategoría eliminada correctamente.";
                            break;
                        default:
                            echo "Ocurrió un error.";
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Tabla de Subcategorías -->
            <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Categoría</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        <?php if (!empty($subCategorias)): ?>
                            <?php foreach ($subCategorias as $subCategoria): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($subCategoria['id']) ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($subCategoria['nombre']) ?></td>
                                    <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($subCategoria['categoria']) ?></td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <button class="bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition" onclick="editarSubCategoria(<?= htmlspecialchars(json_encode($subCategoria)) ?>)">Editar</button>
                                        <button class="bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="eliminarSubCategoria(<?= htmlspecialchars($subCategoria['id']) ?>)">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-6 text-gray-400">No hay subcategorías registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">
            <!-- Botón cerrar -->
            <button id="closeModalX" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>

            <h2 class="text-xl font-semibold text-gray-700 mb-4" id="modalTitle">Agregar nueva Subcategoría</h2>
            <form class="space-y-4">
                <!-- para almacenar el id cuando se edita -->
                <input type="hidden" id="subCategoria_id_form">

                <div>
                    <label for="nombre_form" class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                    <input type="text" id="nombre_form" name="nombre_form" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" required>
                </div>

                <div>
                    <label for="categoria_id_form" class="block text-sm font-medium text-gray-600 mb-1">Categoría</label>
                    <div id="categoria_select_container">
                        <select id="categoria_id_form" name="categoria_id_form" class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700" required>
                            <option value="">-- Selecciona una categoría --</option>
                            echo($categorias);
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id_cat'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 pt-4">
                    <button id="submitForm" type="button" class="bg-yellow-100 text-yellow-700 font-semibold rounded-full px-6 py-2 hover:bg-yellow-200 transition flex-1">
                        Guardar
                    </button>
                    <button id="closeModalBtn" type="button" class="bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition flex-1">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="../../public/js/subCategorias.js"></script>
</body>

</html>