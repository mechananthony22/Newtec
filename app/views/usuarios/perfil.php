<?php
$title = "Mi Perfil";
$style = "perfil";
ob_start();
?>
<div class="min-h-screen flex flex-col items-center bg-gray-50 py-8">
    <div class="w-full max-w-4xl flex flex-col md:flex-row gap-8 mb-8">
        <!-- Tarjeta de perfil -->
        <div class="flex-1 bg-white rounded-xl shadow-lg p-8 flex flex-col items-center">
            <img src="https://ui-avatars.com/perunet/api/perunet/?name=<?= urlencode($usuario['nombre'].' '.$usuario['apellidos']) ?>&background=dc2626&color=fff&size=128" alt="Avatar" class="w-32 h-32 rounded-full mb-4 border-4 border-red-100 shadow">
            <h2 class="text-2xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($usuario['nombre'].' '.$usuario['apellidos']) ?></h2>
            <p class="text-gray-600 mb-2"><i class="fa fa-envelope mr-1"></i> <?= htmlspecialchars($usuario['correo']) ?></p>
            <p class="text-gray-600 mb-2"><i class="fa fa-phone mr-1"></i> <?= htmlspecialchars($usuario['telefono']) ?></p>
            <p class="text-gray-600 mb-2"><i class="fa fa-id-card mr-1"></i> DNI: <?= htmlspecialchars($usuario['dni']) ?></p>
            <button type="button" id="editarPerfil" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition">Editar perfil</button>
        </div>
        <!-- Fin tarjeta de perfil -->
    </div>
    <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-black mb-6 flex items-center gap-2">
            <i class="fa fa-chart-bar text-red-700"></i> Resumen de Compras
        </h2>
        <canvas id="graficoCompras" height="100"></canvas>
    </div>
    <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-black mb-6 flex items-center gap-2">
            <i class="fa fa-shopping-bag text-red-700"></i> Historial de Compras
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-left">
                        <th class="py-2 px-4"># Pedido</th>
                        <th class="py-2 px-4">Fecha</th>
                        <th class="py-2 px-4">Total</th>
                        <th class="py-2 px-4">Estado</th>
                        <th class="py-2 px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $compra): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4 font-semibold">#<?= $compra['id_ven'] ?></td>
                            <td class="py-2 px-4"><?= date('d/m/Y', strtotime($compra['fecha_venta'])) ?></td>
                            <td class="py-2 px-4 text-red-700 font-bold">S/. <?= number_format($compra['total'], 2) ?></td>
                            <td class="py-2 px-4">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                    <?php
                                    switch($compra['estado']) {
                                        case 'pendiente': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'preparando': echo 'bg-orange-100 text-orange-800'; break;
                                        case 'enviado': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'entregado': echo 'bg-green-100 text-green-800'; break;
                                        case 'cancelado': echo 'bg-red-100 text-red-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>
                                "><?= ucfirst($compra['estado']) ?></span>
                            </td>
                            <td class="py-2 px-4 flex gap-2">
                                <?php if (!empty($compra['id_ven'])): ?>
                                    <a href="/perunet/usuarios/perunet/compra/perunet/<?= $compra['id_ven'] ?>" class="text-blue-600 hover:underline font-semibold">Ver Detalle</a>
                                    <a href="/perunet/usuarios/perunet/tracking/perunet/<?= $compra['id_ven'] ?>" class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold px-3 py-1 rounded transition">Ver Seguimiento</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal de edición de perfil (HTML y JS puro) -->
<div id="modalPerfil" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative animate-fade-in">
    <button id="cerrarModalPerfil" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
    <h2 class="text-3xl font-extrabold text-gray-900 mb-6 text-center">Editar Perfil</h2>
    <form id="formEditarPerfil" class="space-y-5">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold mb-1">Nombres:</label>
          <input type="text" name="nombre" id="edit_nombre" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Apellidos:</label>
          <input type="text" name="apellidos" id="edit_apellidos" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold mb-1">Correo electrónico:</label>
          <input type="email" name="correo" id="edit_correo" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Teléfono:</label>
          <input type="tel" name="telefono" id="edit_telefono" maxlength="9" pattern="\d{9}" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold mb-1">DNI:</label>
          <input type="text" name="dni" id="edit_dni" maxlength="8" pattern="\d{8}" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" required>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Nueva Contraseña:</label>
          <input type="password" name="password" id="edit_password" class="w-full rounded-lg border border-gray-300 focus:border-red-600 focus:ring-1 focus:ring-red-400 px-4 py-2 shadow-sm transition" placeholder="••••••••">
        </div>
      </div>
      <div class="flex justify-center gap-4 mt-8">
        <button type="submit" class="bg-[#dc2626] hover:bg-red-700 text-white font-bold px-8 py-2 rounded-lg shadow transition">Guardar Cambios</button>
        <button type="button" id="cancelarModalPerfil" class="bg-gray-200 text-gray-700 font-bold px-8 py-2 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
      </div>
    </form>
  </div>
</div>
<style>
@keyframes fade-in { from { opacity: 0; transform: scale(0.97);} to { opacity: 1; transform: scale(1);} }
.animate-fade-in { animation: fade-in 0.2s; }
</style>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>
<script>
// Mostrar modal al hacer clic en "Editar perfil"
document.getElementById('editarPerfil').addEventListener('click', function() {
  const modal = document.getElementById('modalPerfil');
  modal.classList.remove('hidden');
  // Rellenar los campos con los valores actuales
  document.getElementById('edit_nombre').value = formPerfil.nombre.value;
  document.getElementById('edit_apellidos').value = formPerfil.apellidos.value;
  document.getElementById('edit_correo').value = formPerfil.correo.value;
  document.getElementById('edit_telefono').value = formPerfil.telefono.value;
  document.getElementById('edit_dni').value = formPerfil.dni.value;
  document.getElementById('edit_password').value = '';
});
// Cerrar modal
function cerrarModalPerfil() {
  document.getElementById('modalPerfil').classList.add('hidden');
}
document.getElementById('cerrarModalPerfil').onclick = cerrarModalPerfil;
document.getElementById('cancelarModalPerfil').onclick = cerrarModalPerfil;
// Cerrar al hacer clic fuera del modal
window.addEventListener('mousedown', function(e) {
  const modal = document.getElementById('modalPerfil');
  const box = modal.querySelector('form');
  if (!modal.classList.contains('hidden') && !box.contains(e.target) && !e.target.closest('#editarPerfil')) {
    cerrarModalPerfil();
  }
});
// Enviar formulario por AJAX
const formEditarPerfil = document.getElementById('formEditarPerfil');
formEditarPerfil.onsubmit = function(e) {
  e.preventDefault();
  const data = new FormData(formEditarPerfil);
  fetch('/perunet/public/php/usuario_actualizar.php', {
    method: 'POST',
    body: data
  })
  .then(r => r.json())
  .then(res => {
    if (res.status === 'success') {
      // Actualizar los campos del perfil en la página
      formPerfil.nombre.value = data.get('nombre');
      formPerfil.apellidos.value = data.get('apellidos');
      formPerfil.correo.value = data.get('correo');
      formPerfil.telefono.value = data.get('telefono');
      formPerfil.dni.value = data.get('dni');
      cerrarModalPerfil();
      alert('Perfil actualizado correctamente.');
    } else if (res.status === 'info') {
      alert(res.message);
    } else {
      alert('Error: ' + res.message);
    }
  });
};

// Gráfico de compras (igual que antes)
const compras = <?php echo json_encode($compras); ?>;
const ctx = document.getElementById('graficoCompras').getContext('2d');
const labels = compras.map(c => new Date(c.fecha_venta).toLocaleDateString());
const data = compras.map(c => parseFloat(c.total));
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total de compras (S/.)',
            data: data,
            backgroundColor: 'rgba(220, 38, 38, 0.7)',
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Compras realizadas' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>