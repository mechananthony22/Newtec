document.addEventListener('DOMContentLoaded', function() {
    const aplicarFiltrosBtn = document.getElementById('aplicar-filtros');
    const resetFiltrosBtn = document.getElementById('reset-filters');

    if (aplicarFiltrosBtn) {
        aplicarFiltrosBtn.addEventListener('click', function() {
            const marcasSeleccionadas = Array.from(document.querySelectorAll('.marca-checkbox:checked')).map(cb => cb.value);
            const precioMin = document.getElementById('precio-min').value;
            const precioMax = document.getElementById('precio-max').value;

            const url = new URL(window.location.href);
            url.searchParams.delete('marca[]');
            if (marcasSeleccionadas.length > 0) {
                marcasSeleccionadas.forEach(marca => {
                    url.searchParams.append('marca[]', marca);
                });
            }
            url.searchParams.set('precio_min', precioMin);
            url.searchParams.set('precio_max', precioMax);

            window.location.href = url.toString();
        });
    }

    if (resetFiltrosBtn) {
        resetFiltrosBtn.addEventListener('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.delete('marca[]');
            url.searchParams.delete('precio_min');
            url.searchParams.delete('precio_max');
            window.location.href = url.toString();
        });
    }
}); 