/**
 * JavaScript para el panel de administración de PeruNet
 */

// Configuración del admin
const ADMIN_CONFIG = {
    baseUrl: window.location.origin + '/perunet',
    apiUrl: window.location.origin + '/perunet/api/admin',
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
};

// Utilidades del admin
const AdminUtils = {
    // Mostrar notificación del admin
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

    // Confirmar eliminación
    confirmDelete: function(message, callback) {
        if (confirm(message || '¿Estás seguro de que quieres eliminar este elemento?')) {
            callback();
        }
    },

    // Formatear fecha
    formatDate: function(date) {
        return new Date(date).toLocaleDateString('es-PE', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    },

    // Formatear moneda
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN'
        }).format(amount);
    },

    // Validar imagen
    validateImage: function(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!allowedTypes.includes(file.type)) {
            return 'Solo se permiten archivos JPG, PNG y WebP';
        }
        
        if (file.size > maxSize) {
            return 'El archivo no debe superar los 5MB';
        }
        
        return null;
    },

    // Previsualizar imagen
    previewImage: function(input, previewElement) {
        const file = input.files[0];
        if (file) {
            const error = this.validateImage(file);
            if (error) {
                this.showNotification(error, 'error');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewElement.src = e.target.result;
                previewElement.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
};

// Gestión de tablas de datos
const DataTable = {
    init: function(tableId, options = {}) {
        const table = document.getElementById(tableId);
        if (!table) return;
        
        this.setupPagination(table, options);
        this.setupSearch(table, options);
        this.setupSorting(table, options);
        this.setupActions(table, options);
    },

    setupPagination: function(table, options) {
        const paginationContainer = table.querySelector('.pagination');
        if (!paginationContainer) return;
        
        const itemsPerPage = options.itemsPerPage || 10;
        const currentPage = parseInt(paginationContainer.dataset.currentPage) || 1;
        const totalItems = parseInt(paginationContainer.dataset.totalItems) || 0;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        this.renderPagination(paginationContainer, currentPage, totalPages);
    },

    renderPagination: function(container, currentPage, totalPages) {
        let html = '';
        
        // Botón anterior
        html += `<a href="#" class="pagination-item ${currentPage === 1 ? 'disabled' : ''}" data-page="${currentPage - 1}">
            <i class="fas fa-chevron-left"></i>
        </a>`;
        
        // Páginas
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                html += `<a href="#" class="pagination-item ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</a>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                html += `<span class="pagination-item disabled">...</span>`;
            }
        }
        
        // Botón siguiente
        html += `<a href="#" class="pagination-item ${currentPage === totalPages ? 'disabled' : ''}" data-page="${currentPage + 1}">
            <i class="fas fa-chevron-right"></i>
        </a>`;
        
        container.innerHTML = html;
        
        // Eventos de paginación
        container.addEventListener('click', (e) => {
            e.preventDefault();
            if (e.target.closest('.pagination-item') && !e.target.closest('.pagination-item').classList.contains('disabled')) {
                const page = parseInt(e.target.closest('.pagination-item').dataset.page);
                this.loadPage(page);
            }
        });
    },

    setupSearch: function(table, options) {
        const searchInput = table.querySelector('.search-input');
        if (!searchInput) return;
        
        let timeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.performSearch(e.target.value);
            }, 300);
        });
    },

    performSearch: function(query) {
        const params = new URLSearchParams(window.location.search);
        params.set('search', query);
        params.set('page', '1');
        window.location.search = params.toString();
    },

    setupSorting: function(table, options) {
        const sortableHeaders = table.querySelectorAll('th[data-sort]');
        sortableHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const field = header.dataset.sort;
                const currentOrder = header.dataset.order || 'asc';
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
                
                // Actualizar indicadores visuales
                sortableHeaders.forEach(h => {
                    h.classList.remove('sort-asc', 'sort-desc');
                    h.dataset.order = '';
                });
                header.classList.add(`sort-${newOrder}`);
                header.dataset.order = newOrder;
                
                // Aplicar ordenamiento
                this.sortTable(field, newOrder);
            });
        });
    },

    sortTable: function(field, order) {
        const params = new URLSearchParams(window.location.search);
        params.set('sort', field);
        params.set('order', order);
        window.location.search = params.toString();
    },

    setupActions: function(table, options) {
        // Eventos para botones de acción
        table.addEventListener('click', (e) => {
            if (e.target.matches('.btn-edit')) {
                e.preventDefault();
                const id = e.target.dataset.id;
                this.editItem(id);
            }
            
            if (e.target.matches('.btn-delete')) {
                e.preventDefault();
                const id = e.target.dataset.id;
                this.deleteItem(id);
            }
            
            if (e.target.matches('.btn-view')) {
                e.preventDefault();
                const id = e.target.dataset.id;
                this.viewItem(id);
            }
        });
    },

    editItem: function(id) {
        window.location.href = `${ADMIN_CONFIG.baseUrl}/admin/edit/${id}`;
    },

    deleteItem: function(id) {
        AdminUtils.confirmDelete('¿Estás seguro de que quieres eliminar este elemento?', () => {
            fetch(`${ADMIN_CONFIG.apiUrl}/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': ADMIN_CONFIG.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    AdminUtils.showNotification('Elemento eliminado correctamente', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    AdminUtils.showNotification(data.message || 'Error al eliminar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                AdminUtils.showNotification('Error al eliminar el elemento', 'error');
            });
        });
    },

    viewItem: function(id) {
        window.location.href = `${ADMIN_CONFIG.baseUrl}/admin/view/${id}`;
    },

    loadPage: function(page) {
        const params = new URLSearchParams(window.location.search);
        params.set('page', page);
        window.location.search = params.toString();
    }
};

// Gestión de formularios del admin
const AdminForms = {
    init: function() {
        this.setupImageUploads();
        this.setupRichTextEditors();
        this.setupDatePickers();
        this.setupSelect2();
    },

    setupImageUploads: function() {
        const imageInputs = document.querySelectorAll('.image-upload');
        imageInputs.forEach(input => {
            const preview = input.parentElement.querySelector('.image-preview img');
            if (preview) {
                input.addEventListener('change', () => {
                    AdminUtils.previewImage(input, preview);
                });
            }
        });
    },

    setupRichTextEditors: function() {
        const textareas = document.querySelectorAll('.rich-editor');
        textareas.forEach(textarea => {
            // Aquí se podría integrar un editor como TinyMCE o CKEditor
            // Por ahora, solo agregamos clases de estilo
            textarea.classList.add('form-textarea');
        });
    },

    setupDatePickers: function() {
        const dateInputs = document.querySelectorAll('.date-picker');
        dateInputs.forEach(input => {
            input.type = 'date';
            input.classList.add('form-input');
        });
    },

    setupSelect2: function() {
        const selects = document.querySelectorAll('.select2');
        selects.forEach(select => {
            select.classList.add('form-select');
        });
    },

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

    clearError: function(input) {
        const errorElement = input.parentElement.querySelector('.form-error');
        if (errorElement) {
            errorElement.remove();
        }
        input.classList.remove('border-red-500');
    },

    submitAjax: function(form, callback) {
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': ADMIN_CONFIG.csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (callback) callback(data);
        })
        .catch(error => {
            console.error('Error:', error);
            AdminUtils.showNotification('Error al enviar el formulario', 'error');
        });
    }
};

// Gestión de gráficos y estadísticas
const AdminCharts = {
    init: function() {
        this.loadDashboardStats();
        this.setupCharts();
    },

    loadDashboardStats: function() {
        fetch(`${ADMIN_CONFIG.apiUrl}/stats`)
            .then(response => response.json())
            .then(data => {
                this.updateDashboardStats(data);
            })
            .catch(error => {
                console.error('Error cargando estadísticas:', error);
            });
    },

    updateDashboardStats: function(stats) {
        Object.keys(stats).forEach(key => {
            const element = document.getElementById(`stat-${key}`);
            if (element) {
                element.textContent = stats[key];
            }
        });
    },

    setupCharts: function() {
        // Aquí se podrían integrar librerías como Chart.js
        // Por ahora, solo un placeholder
        const chartContainers = document.querySelectorAll('.chart-container');
        chartContainers.forEach(container => {
            console.log('Chart container ready:', container);
        });
    }
};

// Gestión de notificaciones del admin
const AdminNotifications = {
    init: function() {
        this.loadNotifications();
        this.setupNotificationToggle();
    },

    loadNotifications: function() {
        fetch(`${ADMIN_CONFIG.apiUrl}/notifications`)
            .then(response => response.json())
            .then(data => {
                this.updateNotificationBadge(data.count);
                this.updateNotificationList(data.notifications);
            })
            .catch(error => {
                console.error('Error cargando notificaciones:', error);
            });
    },

    updateNotificationBadge: function(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    },

    updateNotificationList: function(notifications) {
        const list = document.querySelector('.notification-list');
        if (!list) return;
        
        let html = '';
        notifications.forEach(notification => {
            html += `
                <div class="notification-item p-3 border-b border-gray-200 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                            <p class="text-xs text-gray-500">${notification.message}</p>
                        </div>
                        <span class="text-xs text-gray-400">${AdminUtils.formatDate(notification.created_at)}</span>
                    </div>
                </div>
            `;
        });
        
        list.innerHTML = html;
    },

    setupNotificationToggle: function() {
        const toggle = document.querySelector('.notification-toggle');
        const dropdown = document.querySelector('.notification-dropdown');
        
        if (toggle && dropdown) {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                dropdown.classList.toggle('hidden');
            });
            
            // Cerrar al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.notification-container')) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    }
};

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes del admin
    DataTable.init('admin-table');
    AdminForms.init();
    AdminCharts.init();
    AdminNotifications.init();
    
    // Configurar eventos globales del admin
    setupAdminEvents();
});

// Configurar eventos globales del admin
function setupAdminEvents() {
    // Eventos para formularios AJAX
    document.addEventListener('submit', function(e) {
        if (e.target.matches('.admin-ajax-form')) {
            e.preventDefault();
            if (AdminForms.validate(e.target)) {
                AdminForms.submitAjax(e.target, function(response) {
                    if (response.success) {
                        AdminUtils.showNotification(response.message, 'success');
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        AdminUtils.showNotification(response.message, 'error');
                    }
                });
            }
        }
    });
    
    // Eventos para sidebar móvil
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('closed');
        });
    }
    
    // Eventos para dropdowns del admin
    document.addEventListener('click', function(e) {
        if (e.target.matches('.admin-dropdown-toggle')) {
            e.preventDefault();
            const dropdown = e.target.nextElementSibling;
            dropdown.classList.toggle('hidden');
        }
        
        // Cerrar dropdowns al hacer clic fuera
        if (!e.target.closest('.admin-dropdown')) {
            document.querySelectorAll('.admin-dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
    
    // Eventos para modales del admin
    document.addEventListener('click', function(e) {
        if (e.target.matches('.admin-modal-toggle')) {
            e.preventDefault();
            const modalId = e.target.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
            }
        }
        
        if (e.target.matches('.admin-modal-close')) {
            e.preventDefault();
            const modal = e.target.closest('.admin-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    });
}

// Exportar para uso global
window.AdminUtils = AdminUtils;
window.DataTable = DataTable;
window.AdminForms = AdminForms;
window.AdminCharts = AdminCharts;
window.AdminNotifications = AdminNotifications; 