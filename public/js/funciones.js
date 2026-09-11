// ARCHIVO ACTUALIZADO - VERSIÓN CORREGIDA
console.log('✅ funciones.js cargado correctamente - versión corregida');

// Función para verificar que todos los elementos estén presentes
function verificarElementos() {
    console.log('=== VERIFICACIÓN DE ELEMENTOS ===');
    
    // Verificar elementos del header
    const elementosHeader = [
        'busqueda',
        'buscar-btn',
        'menu-toggle',
        'menu'
    ];
    
    elementosHeader.forEach(id => {
        const elemento = document.getElementById(id);
        if (elemento) {
            console.log(`✓ ${id} encontrado`);
        } else {
            console.log(`✗ ${id} NO encontrado`);
        }
    });
    
    // Verificar secciones principales
    const secciones = [
        'categorias-section',
        'productos-section',
        'marcas-section',
        'servicios-section',
        'promociones-section',
        'opiniones-section',
        'ayuda-section'
    ];
    
    secciones.forEach(id => {
        const seccion = document.getElementById(id);
        if (seccion) {
            console.log(`✓ ${id} encontrado`);
        } else {
            console.log(`✗ ${id} NO encontrado`);
        }
    });
    
    // Solo logs de conteo para el slider, sin modificar nada
    const bannerSlides = document.querySelectorAll('.banner-slide');
    const bannerDots = document.querySelectorAll('.banner-dot');
    console.log(`Banner slides encontrados: ${bannerSlides.length}`);
    console.log(`Banner dots encontrados: ${bannerDots.length}`);
}

// Función de búsqueda de productos
function inicializarBusqueda() {
    const buscarBtn = document.getElementById('buscar-btn');
    if (buscarBtn) {
        buscarBtn.addEventListener('click', (e) => {
            e.preventDefault();
            
            const busqueda = document.getElementById('busqueda');
            const query = busqueda ? busqueda.value.toLowerCase() : '';
            
            if (!query) return;
            
            // Buscar productos en la página
            document.querySelectorAll('.producto').forEach(producto => {
                const nombreProducto = producto.querySelector('h3');
                const nombre = nombreProducto ? nombreProducto.textContent.toLowerCase() : '';
                
                if (nombre.includes(query)) {
                    producto.style.display = 'block';
                } else {
                    producto.style.display = 'none';
                }
            });
        });
    }
}

// Función para el menú móvil
function inicializarMenu() {
    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');
    
    if (menuToggle && menu) {
        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('active');
        });
    }
    
    // Manejar submenús
    const menuItems = document.querySelectorAll('nav ul li');
    menuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            const submenu = item.querySelector('.submenu');
            if (submenu) {
                e.stopPropagation();
                submenu.style.display = (submenu.style.display === 'block') ? 'none' : 'block';
            }
        });
    });
}

// Función para el menú lateral (si existe)
function inicializarMenuLateral() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebarMenu');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        
        document.addEventListener('click', (event) => {
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
        
        document.querySelectorAll('.sidebar .menu-item').forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
                item.classList.toggle('active');
            });
        });
    }
}

// Botón de ayuda
function toggleHelp() {
    // Buscar la sección de ayuda por diferentes IDs posibles
    const helpSection = document.getElementById('help-section') || 
                       document.getElementById('ayuda-section') ||
                       document.querySelector('.help-section');
    
    if (helpSection) {
        // Si existe la sección, alternar su visibilidad
        const isVisible = helpSection.style.display !== 'none' && 
                         helpSection.offsetParent !== null;
        
        if (isVisible) {
            helpSection.style.display = 'none';
        } else {
            helpSection.style.display = 'block';
            // Hacer scroll a la sección de ayuda
            helpSection.scrollIntoView({ behavior: 'smooth' });
        }
    } else {
        // Si no existe la sección de ayuda, redirigir a contacto
        console.log('Sección de ayuda no encontrada, redirigiendo a contacto...');
        window.location.href = '/perunet/contacto';
    }
}

// Hacer la función toggleHelp disponible globalmente
window.toggleHelp = toggleHelp;

// Inicializar todo cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    console.log('Inicializando funciones...');
    
    // Agregar un pequeño retraso para asegurar que todo esté cargado
    setTimeout(() => {
        // Ejecutar verificación
        verificarElementos();
        
        // Inicializar funcionalidades
        inicializarBusqueda();
        inicializarMenu();
        inicializarMenuLateral();
        
        console.log('Funciones inicializadas correctamente');
    }, 100);
});



