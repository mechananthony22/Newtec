// Modal types and their configurations
const MODAL_TYPES = {
    SUCCESS: {
        icon: '✓',
        color: '#4CAF50'
    },
    ERROR: {
        icon: '✗',
        color: '#f44336'
    },
    INFO: {
        icon: 'ℹ',
        color: '#2196F3'
    },
    WARNING: {
        icon: '⚠',
        color: '#ff9800'
    }
};

// Modal class
class CustomModal {
    constructor() {
        this.modal = document.createElement('div');
        this.modal.className = 'custom-modal';
        this.modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-icon"></div>
                <h2 class="modal-title"></h2>
                <p class="modal-description"></p>
                <div class="modal-buttons">
                    <button class="modal-button modal-accept">Aceptar</button>
                </div>
            </div>
        `;
        
        // Buttons will be added dynamically in the show() method
    }

    // Show modal with specified type, title, and description
    show(type, title, description, time = null, showCancel = false, callback = null) {
        // Store the callback
        this.modal.callback = callback;
        
        // Update the modal content based on showCancel parameter
        const buttonsContainer = this.modal.querySelector('.modal-buttons');
        buttonsContainer.innerHTML = ''; // Clear existing buttons
        
        const config = MODAL_TYPES[type.toUpperCase()];
        if (!config) {
            console.error('Invalid modal type');
            if (callback) callback(false);
            return false;
        }
        
        // Create accept button
        const acceptButton = document.createElement('button');
        acceptButton.className = 'modal-button modal-accept';
        acceptButton.textContent = 'Aceptar';
        
        // Style button according to type
        acceptButton.style.backgroundColor = config.color;
        acceptButton.style.borderColor = config.color;
        acceptButton.style.color = '#fff';
        acceptButton.style.borderRadius = '5px';
        acceptButton.style.padding = '10px 20px';
        acceptButton.style.fontSize = '16px';
        acceptButton.style.cursor = 'pointer';
        acceptButton.style.transition = 'all 0.3s ease';
        
        // Add hover effect
        acceptButton.addEventListener('mouseenter', () => {
            acceptButton.style.transform = 'scale(1.05)';
        });
        
        acceptButton.addEventListener('mouseleave', () => {
            acceptButton.style.transform = 'scale(1)';
        });
        
        // Handle accept button click
        acceptButton.addEventListener('click', () => {
            this.close();
            if (this.modal.callback) {
                this.modal.callback(true);
            }
            return true;
        });
        
        buttonsContainer.appendChild(acceptButton);
        
        // Create cancel button if needed
        if (showCancel) {
            const cancelButton = document.createElement('button');
            cancelButton.className = 'modal-button modal-cancel';
            cancelButton.textContent = 'Cancelar';
            
            // Style cancel button
            cancelButton.style.backgroundColor = '#f44336';
            cancelButton.style.borderColor = '#f44336';
            cancelButton.style.color = '#fff';
            cancelButton.style.borderRadius = '5px';
            cancelButton.style.padding = '10px 20px';
            cancelButton.style.fontSize = '16px';
            cancelButton.style.cursor = 'pointer';
            cancelButton.style.marginLeft = '10px';
            cancelButton.style.transition = 'all 0.3s ease';
            
            // Add hover effect
            cancelButton.addEventListener('mouseenter', () => {
                cancelButton.style.transform = 'scale(1.05)';
                cancelButton.style.backgroundColor = '#d32f2f';
            });
            
            cancelButton.addEventListener('mouseleave', () => {
                cancelButton.style.transform = 'scale(1)';
                cancelButton.style.backgroundColor = '#f44336';
            });
            
            // Handle cancel button click
            cancelButton.addEventListener('click', () => {
                this.close();
                if (this.modal.callback) {
                    this.modal.callback(false);
                }
                return false;
            });
            
            buttonsContainer.prepend(cancelButton);
        }

        // Configure modal elements
        this.modal.querySelector('.modal-icon').innerHTML = config.icon;
        this.modal.querySelector('.modal-icon').style.color = config.color;
        this.modal.querySelector('.modal-title').textContent = title;
        this.modal.querySelector('.modal-description').textContent = description;

        // Add modal to body
        document.body.appendChild(this.modal);

        // Show animation
        setTimeout(() => {
            this.modal.style.display = 'flex';
            this.modal.style.opacity = '1';
        }, 100);

        // Close modal after time if no buttons are shown (auto-close)
        if (time && !showCancel) {
            setTimeout(() => {
                this.close();
                if (this.modal.callback) {
                    this.modal.callback(true);
                }
            }, time);
        }
        
        return false;
    }

    // Close modal
    close() {
        this.modal.style.opacity = '0';
        setTimeout(() => {
            this.modal.style.display = 'none';
            this.modal.remove();
            // Clean up callback
            this.modal.callback = null;
            this.modal.resolve = null;
            this.modal.reject = null;
        }, 300);
    }
}

// Export modal instance
export const modalAlert = new CustomModal();

// Example usage:
// modalAlert.show('success', 'Éxito', 'Producto agregado al carrito')
//     .then(result => console.log('Aceptado:', result))
//     .catch(result => console.log('Cancelado:', result));

// modalAlert.show('error', 'Error', 'Ocurrió un error', null, true)
//     .then(result => console.log('Aceptado:', result))
//     .catch(result => console.log('Cancelado:', result));

// Add CSS styles
const style = document.createElement('style');
style.textContent = `
    .custom-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        text-align: center;
        max-width: 500px;
        width: 90%;
        position: relative;
    }

    .modal-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .modal-title {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .modal-description {
        margin-bottom: 1.5rem;
        color: #666;
    }

    .modal-button {
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
        margin: 0 0.5rem;
    }

    .modal-accept {
        background: #4CAF50;
        color: white;
    }

    .modal-accept:hover {
        background: #45a049;
    }

    .modal-cancel {
        background: #f44336;
        color: white;
    }

    .modal-cancel:hover {
        background: #da190b;
    }
`;
document.head.appendChild(style);
