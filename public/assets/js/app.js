/**
 * JavaScript principal para PeruNet
 */

// Configuración global
const APP_CONFIG = {
    baseUrl: window.location.origin + '/perunet',
    apiUrl: window.location.origin + '/perunet/api',
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
};

// Utilidades
const Utils = {
    // Mostrar notificación
    showNotification: function(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm animate-fade-in`;
        
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-white',
            info: 'bg-blue-500 text-white'
        };
        
        notification.className += ` ${colors[type] || colors.info}`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, duration);
    },

    // Confirmar acción
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },

    // Formatear precio
    formatPrice: function(price) {
        return new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN'
        }).format(price);
    },

    // Validar email
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    // Obtener parámetros de URL
    getUrlParams: function() {
        const params = new URLSearchParams(window.location.search);
        const result = {};
        for (const [key, value] of params) {
            result[key] = value;
        }
        return result;
    },

    // Establecer parámetros de URL
    setUrlParams: function(params) {
        const url = new URL(window.location);
        Object.keys(params).forEach(key => {
            url.searchParams.set(key, params[key]);
        });
        window.history.pushState({}, '', url);
    }
};

// Gestión del carrito
const Cart = {
    items: JSON.parse(localStorage.getItem('cart') || '[]'),

    addItem: function(product) {
        const existingItem = this.items.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: 1
            });
        }
        
        this.save();
        this.updateBadge();
        Utils.showNotification('Producto agregado al carrito', 'success');
    },

    removeItem: function(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.save();
        this.updateBadge();
        Utils.showNotification('Producto removido del carrito', 'info');
    },

    updateQuantity: function(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = Math.max(1, quantity);
            this.save();
            this.updateBadge();
        }
    },

    getTotal: function() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    getCount: function() {
        return this.items.reduce((count, item) => count + item.quantity, 0);
    },

    clear: function() {
        this.items = [];
        this.save();
        this.updateBadge();
    },

    save: function() {
        localStorage.setItem('cart', JSON.stringify(this.items));
    },

    updateBadge: function() {
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            const count = this.getCount();
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    }
};

// Gestión de formularios
const Forms = {
    // Validar formulario
    validate: function(form) {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showError(input, 'Este campo es requerido');
                isValid = false;
            } else {
                this.clearError(input);
            }
        });
        
        return isValid;
    },

    // Mostrar error
    showError: function(input, message) {
        const errorElement = input.parentElement.querySelector('.form-error') || 
                           document.createElement('div');
        errorElement.className = 'form-error';
        errorElement.textContent = message;
        
        if (!input.parentElement.querySelector('.form-error')) {
            input.parentElement.appendChild(errorElement);
        }
        
        input.classList.add('border-red-500');
    },

    // Limpiar error
    clearError: function(input) {
        const errorElement = input.parentElement.querySelector('.form-error');
        if (errorElement) {
            errorElement.remove();
        }
        input.classList.remove('border-red-500');
    },

    // Enviar formulario AJAX
    submitAjax: function(form, callback) {
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (callback) callback(data);
        })
        .catch(error => {
            console.error('Error:', error);
            Utils.showNotification('Error al enviar el formulario', 'error');
        });
    }
};

// Gestión de modales
const Modal = {
    show: function(content, options = {}) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold">${options.title || ''}</h3>
                    <button class="modal-close text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    ${content}
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Cerrar modal
        modal.querySelector('.modal-close').addEventListener('click', () => {
            this.hide(modal);
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.hide(modal);
            }
        });
        
        return modal;
    },

    hide: function(modal) {
        if (modal && modal.parentElement) {
            modal.remove();
        }
    }
};

// Gestión de búsqueda
const Search = {
    init: function() {
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let timeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.performSearch(e.target.value);
                }, 300);
            });
        }
    },

    performSearch: function(query) {
        if (query.length < 2) return;
        
        fetch(`${APP_CONFIG.apiUrl}/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                this.displayResults(data);
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
            });
    },

    displayResults: function(results) {
        // Implementar según necesidades
        console.log('Resultados de búsqueda:', results);
    }
};

// Gestión de filtros
const Filters = {
    init: function() {
        const filterInputs = document.querySelectorAll('.filter-input');
        filterInputs.forEach(input => {
            input.addEventListener('change', () => {
                this.applyFilters();
            });
        });
    },

    applyFilters: function() {
        const filters = {};
        document.querySelectorAll('.filter-input').forEach(input => {
            if (input.value) {
                filters[input.name] = input.value;
            }
        });
        
        Utils.setUrlParams(filters);
        this.loadFilteredResults(filters);
    },

    loadFilteredResults: function(filters) {
        const params = new URLSearchParams(filters);
        fetch(`${APP_CONFIG.apiUrl}/products?${params}`)
            .then(response => response.json())
            .then(data => {
                this.updateResults(data);
            })
            .catch(error => {
                console.error('Error al filtrar:', error);
            });
    },

    updateResults: function(data) {
        // Implementar según necesidades
        console.log('Resultados filtrados:', data);
    }
};

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes
    Cart.updateBadge();
    Search.init();
    Filters.init();
    
    // Configurar eventos globales
    setupGlobalEvents();
});

// Configurar eventos globales
function setupGlobalEvents() {
    // Eventos para botones de carrito
    document.addEventListener('click', function(e) {
        if (e.target.matches('.add-to-cart')) {
            e.preventDefault();
            const productId = e.target.dataset.productId;
            const productData = {
                id: productId,
                name: e.target.dataset.productName,
                price: parseFloat(e.target.dataset.productPrice),
                image: e.target.dataset.productImage
            };
            Cart.addItem(productData);
        }
        
        if (e.target.matches('.remove-from-cart')) {
            e.preventDefault();
            const productId = e.target.dataset.productId;
            Cart.removeItem(productId);
        }
    });
    
    // Eventos para formularios
    document.addEventListener('submit', function(e) {
        if (e.target.matches('.ajax-form')) {
            e.preventDefault();
            if (Forms.validate(e.target)) {
                Forms.submitAjax(e.target, function(response) {
                    if (response.success) {
                        Utils.showNotification(response.message, 'success');
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        Utils.showNotification(response.message, 'error');
                    }
                });
            }
        }
    });
    
    // Eventos para menús móviles
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Eventos para dropdowns
    document.addEventListener('click', function(e) {
        if (e.target.matches('.dropdown-toggle')) {
            e.preventDefault();
            const dropdown = e.target.nextElementSibling;
            dropdown.classList.toggle('hidden');
        }
        
        // Cerrar dropdowns al hacer clic fuera
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
}

// Exportar para uso global
window.Utils = Utils;
window.Cart = Cart;
window.Forms = Forms;
window.Modal = Modal;
window.Search = Search;
window.Filters = Filters; 