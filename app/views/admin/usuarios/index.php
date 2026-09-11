<!DOCTYPE html>
<html lang="es">

<!-- incluir head -->
<?php
$title = "Administrar Usuarios";
include __DIR__ . '/../../../components/perunet/adminHead.php';
?>

<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../../../components/perunet/adminNavBar.php'; ?>
    <?php include __DIR__ . '/../../../components/perunet/adminMenuNav.php'; ?>

    <main class="p-4 md:ml-64">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-700">Administrar Usuarios</h1>
            <form class="flex gap-2 w-full md:w-auto" method="get">
                <input type="text" name="buscar" placeholder="Buscar" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" class="rounded-full border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white text-gray-700 w-full md:w-64">
                <button type="submit" class="bg-blue-100 text-blue-700 font-semibold rounded-full px-4 py-2 hover:bg-blue-200 transition">Buscar</button>
                <button type="button" id="openModalUsuario" class="bg-green-100 text-green-700 font-semibold rounded-full px-4 py-2 hover:bg-green-200 transition whitespace-nowrap">+ Añadir Usuario</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Correo</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">DNI</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Rol</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Fecha Registro</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($usuario['id_us']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($usuario['correo']) ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($usuario['dni']) ?></td>
                                <td class="px-4 py-2">
                                    <?php 
                                        $rol = isset($usuario['rol']) ? $usuario['rol'] : (isset($usuario['rol_nombre']) ? $usuario['rol_nombre'] : '');
                                        if ($rol === 'admin'): ?>
                                        <span class="bg-blue-100 text-blue-700 rounded-full px-3 py-1 text-xs font-semibold">admin</span>
                                    <?php else: ?>
                                        <span class="bg-green-100 text-green-700 rounded-full px-3 py-1 text-xs font-semibold">cliente</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-gray-500 text-sm"><?= htmlspecialchars($usuario['fecha_registro']) ?></td>
                                <td class="px-4 py-2 flex gap-2">
                                    <button onclick="editarUsuario(<?= htmlspecialchars(json_encode($usuario)) ?>)" class="bg-yellow-100 text-yellow-800 rounded-full px-4 py-1 text-xs font-semibold hover:bg-yellow-200 transition">Editar</button>
                                    <a href="/perunet/admin/perunet/usuarios/perunet/eliminar/perunet/<?= $usuario['id_us'] ?>" class="bg-red-100 text-red-700 rounded-full px-4 py-1 text-xs font-semibold hover:bg-red-200 transition" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-400">No hay usuarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal para añadir usuario -->
    <div id="modalUsuario" class="fixed inset-0 bg-black/perunet/60 flex items-center justify-center hidden z-50 transition-all duration-300 p-4">
        <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 rounded-3xl shadow-xl p-8 w-full max-w-md relative border border-blue-100 animate-fadeIn max-h-[90vh] overflow-y-auto custom-scrollbar" style="box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);">
            <button id="closeModalUsuario" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 text-2xl font-bold transition">&times;</button>
            <div class="flex flex-col items-center mb-6">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mb-2 shadow-sm">
                    <svg xmlns='http://www.w3.org/perunet/2000/perunet/svg' class='h-8 w-8' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z' ></svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900 text-center tracking-tight" id="modalTitle">Nuevo Usuario</h2>
            </div>
            <form method="post" action="/perunet/admin/perunet/usuarios/perunet/guardar" class="space-y-5" id="usuarioForm">
                <input type="hidden" name="id_us" id="usuario_id_form">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Nombre</label>
                    <input type="text" name="nombre" id="nombre_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Correo</label>
                    <input type="email" name="correo" id="correo_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>
                <div class="flex gap-3">
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">DNI</label>
                        <input type="text" name="dni" id="dni_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400" maxlength="8" pattern="\d*" inputmode="numeric">
                    </div>
                    <div class="w-1/perunet/2">
                        <label class="block text-gray-700 font-semibold mb-1">Teléfono</label>
                        <input type="text" name="telefono" id="telefono_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400" maxlength="9" pattern="\d*" inputmode="numeric">
                    </div>
                </div>
                <div id="password_field">
                    <label class="block text-gray-700 font-semibold mb-1">Contraseña</label>
                    <input type="password" name="contrasena" id="contrasena_form" required class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition placeholder-gray-400">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Rol</label>
                    <select name="id_rol" id="id_rol_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition">
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Estado</label>
                    <select name="estado" id="estado_form" class="w-full border border-blue-100 rounded-lg px-4 py-2 bg-blue-50 text-gray-900 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none transition">
                        <option value="activo">Activo</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="suspendido">Suspendido</option>
                    </select>
                </div>
                <div class="flex justify-between mt-7 gap-4">
                    <button type="button" id="cancelarModalUsuario" class="flex-1 bg-gray-200 text-gray-700 font-semibold rounded-full px-6 py-2 hover:bg-gray-300 transition text-lg shadow">Cancelar</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold rounded-full px-6 py-2 hover:bg-blue-700 transition text-lg shadow-lg" id="submitBtn">Crear</button>
                </div>
            </form>
        </div>
    </div>
    <style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.4s cubic-bezier(.4,0,.2,1) both; }
    
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
    // Abrir modal al hacer click en '+ Añadir Usuario'
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalUsuario');
        const openBtn = document.getElementById('openModalUsuario');
        const closeBtn = document.getElementById('closeModalUsuario');
        const cancelarBtn = document.getElementById('cancelarModalUsuario');
        const form = document.getElementById('usuarioForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        const passwordField = document.getElementById('password_field');
        
        // Función para abrir modal en modo crear
        function openCreateModal() {
            modalTitle.textContent = 'Nuevo Usuario';
            form.action = '/perunet/admin/perunet/usuarios/perunet/guardar';
            submitBtn.textContent = 'Crear';
            passwordField.style.display = 'block';
            document.getElementById('contrasena_form').required = true;
            form.reset();
            modal.classList.remove('hidden');
        }
        
        // Función para abrir modal en modo editar
        function openEditModal(usuario) {
            modalTitle.textContent = 'Editar Usuario';
            form.action = '/perunet/admin/perunet/usuarios/perunet/actualizar';
            submitBtn.textContent = 'Actualizar';
            passwordField.style.display = 'none';
            document.getElementById('contrasena_form').required = false;
            
            // Llenar los campos con los datos del usuario
            document.getElementById('usuario_id_form').value = usuario.id_us;
            document.getElementById('nombre_form').value = usuario.nombre;
            document.getElementById('apellidos_form').value = usuario.apellidos;
            document.getElementById('correo_form').value = usuario.correo;
            document.getElementById('dni_form').value = usuario.dni;
            document.getElementById('telefono_form').value = usuario.telefono;
            document.getElementById('id_rol_form').value = usuario.id_rol;
            document.getElementById('estado_form').value = usuario.estado;
            
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
        
        // Función global para editar usuario
        window.editarUsuario = function(usuario) {
            openEditModal(usuario);
        };
    });
    </script>
</body>

</html>