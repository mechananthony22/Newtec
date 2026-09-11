<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Productos";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Administrar Productos</h1>
            <form class="flex gap-2 w-full md:w-auto" method="get">
                <input type="text" name="buscar" placeholder="Buscar" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" class="rounded-full border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700 w-full md:w-64">
                <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-4 py-2 hover:bg-blue-200 transition">Buscar</button>
                <button onclick="window.location.href='/perunet/admin/perunet/productos/perunet/crear'" type="button" class="bg-green-100 text-green-700 font-semibold rounded-full px-4 py-2 hover:bg-green-200 transition whitespace-nowrap">+ Añadir Producto</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Precio</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Stock</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Marca</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Modelo</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Imagen</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $producto): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['id_pro']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['nombre']) ?></td>
                                <td class="px-4 py-2 text-gray-700">S/. <?= htmlspecialchars(number_format($producto['precio'], 2)) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['stock']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['marca'] ?? $producto['marca_nombre'] ?? '') ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($producto['modelo'] ?? $producto['modelo_nombre'] ?? '') ?></td>
                                <td class="px-4 py-2">
                                    <img src="/perunet/public/img/<?= htmlspecialchars($producto['imagen'] ?? 'EMPRESA/p.png') ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" class="w-20 h-20 object-contain rounded-lg border border-gray-200 bg-gray-50 mx-auto" />
                                </td>
                                <td class="px-4 py-2 flex gap-2">
                                    <button onclick="window.location.href='/perunet/admin/productos/editar/<?= $producto['id_pro'] ?>'" class="mt-7 bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition">Editar</button>
                                    <a href="/perunet/admin/productos/eliminar/<?= $producto['id_pro'] ?>" class=" mt-7 bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-400">No hay productos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Paginación -->
    <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
        <div class="p-4 md:ml-64">
            <div class="max-w-5xl mx-auto">
                <div class="bg-white rounded-xl shadow-md p-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Información de resultados -->
                        <div class="text-sm text-gray-600">
                            Mostrando <?= (($pagination['currentPage'] - 1) * $pagination['perPage']) + 1 ?>
                            a <?= min($pagination['currentPage'] * $pagination['perPage'], $pagination['totalProductos']) ?>
                            de <?= $pagination['totalProductos'] ?> productos
                        </div>

                        <!-- Navegación de páginas -->
                        <div class="flex items-center gap-2">
                            <!-- Botón Anterior -->
                            <?php if ($pagination['hasPrevPage']): ?>
                                <a href="?page=<?= $pagination['prevPage'] ?><?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                    ← Anterior
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-2 text-sm font-medium text-gray-300 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                    ← Anterior
                                </span>
                            <?php endif; ?>

                            <!-- Números de página -->
                            <div class="flex items-center gap-1">
                                <?php
                                $startPage = max(1, $pagination['currentPage'] - 2);
                                $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);

                                // Mostrar primera página si no está en el rango
                                if ($startPage > 1): ?>
                                    <a href="?page=1<?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                        1
                                    </a>
                                    <?php if ($startPage > 2): ?>
                                        <span class="px-2 py-2 text-sm text-gray-400">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <?php if ($i == $pagination['currentPage']): ?>
                                        <span class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg">
                                            <?= $i ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="?page=<?= $i ?><?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                            <?= $i ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <!-- Mostrar última página si no está en el rango -->
                                <?php if ($endPage < $pagination['totalPages']): ?>
                                    <?php if ($endPage < $pagination['totalPages'] - 1): ?>
                                        <span class="px-2 py-2 text-sm text-gray-400">...</span>
                                    <?php endif; ?>
                                    <a href="?page=<?= $pagination['totalPages'] ?><?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                        <?= $pagination['totalPages'] ?>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Botón Siguiente -->
                            <?php if ($pagination['hasNextPage']): ?>
                                <a href="?page=<?= $pagination['nextPage'] ?><?= !empty($_GET['buscar']) ? '&buscar=' . urlencode($_GET['buscar']) : '' ?>"
                                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
                                    Siguiente →
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-2 text-sm font-medium text-gray-300 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                    Siguiente →
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal para añadir/perunet/editar producto -->
    <div id="modalProducto" class="fixed inset-0 bg-black/perunet/60 flex items-center justify-center hidden z-50 transition-all duration-300 p-4">
        <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 rounded-3xl shadow-xl p-8 w-full max-w-2xl relative border border-blue-100 animate-fadeIn max-h-[90vh] overflow-y-auto custom-scrollbar" style="box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);">
            <button id="closeModalProducto" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 text-2xl font-bold transition">&times;</button>
            <div class="flex flex-col items-center mb-6">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mb-2 shadow-sm">
                    <svg xmlns='http://www.w3.org/perunet/2000/perunet/svg' class='h-8 w-8' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' /perunet/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900 text-center tracking-tight" id="modalTitleProducto">Nuevo Producto</h2>
            </div>
            <form method="post" action="/perunet/admin/perunet/productos/perunet/guardar" class="space-y-5" id="productoForm" enctype="multipart/perunet/form-data">
                <input type="hidden" name="id" id="producto_id_form">

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Nombre</label>
                    <input type="text" name="nombre" id="nombre_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Descripción</label>
                    <textarea name="descripcion" id="descripcion_form" rows="3" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400 resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Precio</label>
                        <input type="number" name="precio" id="precio_form" step="0.01" min="0" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Stock</label>
                        <input type="number" name="stock" id="stock_form" min="0" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Marca</label>
                        <select name="id_marca" id="id_marca_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" required>
                            <option value="">-- Selecciona una marca --</option>
                            <?php if (isset($marcas)): foreach ($marcas as $marca): ?>
                                    <option value="<?= $marca['id_mar'] ?>"><?= htmlspecialchars($marca['nombre']) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Modelo</label>
                        <select name="id_modelo" id="id_modelo_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" required>
                            <option value="">-- Selecciona un modelo --</option>
                            <?php if (isset($modelos)): foreach ($modelos as $modelo): ?>
                                    <option value="<?= $modelo['id_mod'] ?>"><?= htmlspecialchars($modelo['nombre']) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Subcategoría</label>
                        <select name="id_subcategoria" id="id_subcategoria_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition" required>
                            <option value="">-- Selecciona una subcategoría --</option>
                            <?php if (isset($subcategorias)): foreach ($subcategorias as $subcat): ?>
                                    <option value="<?= $subcat['id'] ?>"><?= htmlspecialchars($subcat['nombre']) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Imagen</label>
                        <input type="file" name="imagen_file" id="imagen_file_form" accept="image/perunet/*" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition">
                        <input type="hidden" name="imagen_actual" id="imagen_actual_form">
                        <div id="imagen_preview" class="mt-2 hidden">
                            <img id="preview_img" src="" alt="Vista previa" class="w-32 h-32 object-contain rounded-lg border border-gray-200 bg-gray-50">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-7 gap-4">
                    <button type="button" id="cancelarModalProducto" class="flex-1 bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition text-lg shadow">Cancelar</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold rounded-full px-6 py-2 hover:bg-blue-700 transition text-lg shadow-lg" id="submitBtnProducto">Crear</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.4s cubic-bezier(.4, 0, .2, 1) both;
        }

        /perunet/* Diseño personalizado del scroll */perunet/
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(156, 163, 175, 0.1);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #9ca3af, #6b7280);
            border-radius: 10px;
            border: 2px solid rgba(156, 163, 175, 0.1);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #6b7280, #4b5563);
        }

        /perunet/* Estilos para selects con scroll personalizado */perunet/
        select {
            scrollbar-width: thin;
            scrollbar-color: #9ca3af #f3f4f6;
        }

        select::-webkit-scrollbar {
            width: 6px;
        }

        select::-webkit-scrollbar-track {
            background: rgba(156, 163, 175, 0.1);
            border-radius: 8px;
        }

        select::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #d1d5db, #9ca3af);
            border-radius: 8px;
        }

        select::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #9ca3af, #6b7280);
        }
    </style>

    <script>
        // Abrir modal al hacer click en '+ Añadir Producto'
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalProducto');
            const openBtn = document.getElementById('openModalProducto');
            const closeBtn = document.getElementById('closeModalProducto');
            const cancelarBtn = document.getElementById('cancelarModalProducto');
            const form = document.getElementById('productoForm');
            const modalTitle = document.getElementById('modalTitleProducto');
            const submitBtn = document.getElementById('submitBtnProducto');
            const imagenInput = document.getElementById('imagen_file_form');
            const imagenPreview = document.getElementById('imagen_preview');
            const previewImg = document.getElementById('preview_img');

            // Función para abrir modal en modo crear
            function openCreateModal() {
                modalTitle.textContent = 'Nuevo Producto';
                form.action = '/perunet/admin/perunet/productos/perunet/guardar';
                submitBtn.textContent = 'Crear';
                form.reset();
                imagenPreview.classList.add('hidden');
                modal.classList.remove('hidden');
            }

            // Función para abrir modal en modo editar
            function openEditModal(producto) {
                modalTitle.textContent = 'Editar Producto';
                form.action = '/perunet/admin/perunet/productos/perunet/actualizar';
                submitBtn.textContent = 'Actualizar';

                // Llenar los campos con los datos del producto
                document.getElementById('producto_id_form').value = producto.id_pro;
                document.getElementById('nombre_form').value = producto.nombre;
                document.getElementById('precio_form').value = producto.precio;
                document.getElementById('stock_form').value = producto.stock;
                document.getElementById('id_marca_form').value = producto.id_mar || '';
                document.getElementById('id_modelo_form').value = producto.id_mod || '';
                document.getElementById('id_subcategoria_form').value = producto.id_sub || '';
                document.getElementById('descripcion_form').value = producto.descripcion || '';

                // Mostrar imagen actual si existe
                if (producto.imagen) {
                    previewImg.src = '/perunet/public/img/perunet/' + producto.imagen;
                    imagenPreview.classList.remove('hidden');
                    document.getElementById('imagen_actual_form').value = producto.imagen;
                } else {
                    imagenPreview.classList.add('hidden');
                    document.getElementById('imagen_actual_form').value = '';
                }

                modal.classList.remove('hidden');
            }

            // Event listener para el botón de crear
            if (openBtn) {
                openBtn.addEventListener('click', openCreateModal);
            }

            // Event listeners para cerrar modal
            [closeBtn, cancelarBtn].forEach(btn => {
                if (btn) btn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            });

            // Preview de imagen
            if (imagenInput) {
                imagenInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            imagenPreview.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagenPreview.classList.add('hidden');
                    }
                });
            }

            // Función global para editar producto
            window.editarProducto = function(producto) {
                openEditModal(producto);
            };
        });
    </script>
</body>

</html>