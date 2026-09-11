<?php
$title = "Armador de Setup Gamer";
$style = "builder";
ob_start();

$totalSteps = count($categories);
$selectedProducts = isset($_SESSION['builder_setup']) ? $_SESSION['builder_setup'] : [];
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 pb-32">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
                    <i class="fa fa-gamepad text-red-600 mr-3"></i>
                    Armador de Setup
                </h1>
                <p class="text-gray-600">Paso <?= $currentStep ?> de <?= $totalSteps ?>: <?= $currentCategory['nombre'] ?? '' ?></p>
            </div>
            <a href="/perunet/builder" class="bg-white border-2 border-gray-300 hover:border-red-600 text-gray-700 hover:text-red-600 px-6 py-3 rounded-lg transition">
                <i class="fa fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between mb-2">
                <?php foreach ($categories as $index => $cat): ?>
                    <div class="flex-1 text-center">
                        <div class="text-xs text-gray-400 mb-1"><?= $cat['nombre'] ?></div>
                        <div class="h-2 bg-gray-700 rounded-full mx-1 overflow-hidden">
                            <div class="h-full bg-red-500 transition-all duration-300" style="width: <?= ($index + 1) <= $currentStep ? '100%' : '0%' ?>"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Category Info -->
        <?php if ($currentCategory): ?>
            <div class="bg-white border-2 border-red-200 rounded-xl p-6 mb-4 shadow-sm">
                <div class="flex items-center">
                    <i class="fa <?= $currentCategory['icono'] ?> text-red-600 text-4xl mr-4"></i>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900"><?= $currentCategory['nombre'] ?></h2>
                        <p class="text-gray-600"><?= $currentCategory['descripcion'] ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Navigation Buttons (Top) -->
        <div class="flex justify-between mb-8">
            <?php if ($currentStep > 1): ?>
                <a href="/perunet/builder/setup?step=<?= $currentStep - 1 ?>" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-bold transition">
                    <i class="fa fa-arrow-left mr-2"></i> Anterior
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if ($currentStep < $totalSteps): ?>
                <a href="/perunet/builder/setup?step=<?= $currentStep + 1 ?>" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold transition">
                    Siguiente <i class="fa fa-arrow-right ml-2"></i>
                </a>
            <?php endif; ?>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden hover:shadow-xl hover:border-red-400 transition-all duration-300 product-card" data-product-id="<?= $product['id_pro'] ?>" data-product-name="<?= htmlspecialchars($product['nombre']) ?>" data-product-price="<?= $product['precio'] ?>" data-product-image="<?= htmlspecialchars($product['imagen']) ?>">
                        <div class="relative">
                            <img src="/perunet/public/img/<?= htmlspecialchars($product['imagen']) ?>" alt="<?= htmlspecialchars($product['nombre']) ?>" class="w-full h-48 object-cover">
                            <div class="absolute top-2 right-2 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                S/. <?= number_format($product['precio'], 2) ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-gray-900 font-bold text-lg mb-2 line-clamp-2"><?= htmlspecialchars($product['nombre']) ?></h3>
                            <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($product['marca_nombre'] ?? '') ?></p>
                            <p class="text-gray-500 text-xs mb-4 line-clamp-2"><?= htmlspecialchars($product['descripcion'] ?? '') ?></p>
                            <button class="select-product-btn w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition">
                                <i class="fa fa-check-circle mr-2"></i> Seleccionar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <i class="fa fa-box-open text-gray-600 text-6xl mb-4"></i>
                    <p class="text-gray-400 text-xl">No hay productos disponibles en esta categoría</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Floating Summary Panel -->
<div id="builder-summary" class="fixed bottom-4 right-4 bg-gray-900 border-2 border-red-500 rounded-xl shadow-2xl p-6 w-96 max-h-[80vh] overflow-y-auto z-50">
    <h3 class="text-white font-bold text-xl mb-4 flex items-center justify-between">
        <span><i class="fa fa-list-alt text-red-500 mr-2"></i> Tu Setup</span>
        <button id="toggle-summary" class="text-gray-400 hover:text-white">
            <i class="fa fa-minus"></i>
        </button>
    </h3>
    <div id="summary-content">
        <div id="selected-products" class="space-y-3 mb-4">
            <!-- Products will be added here dynamically -->
        </div>
        <div class="border-t border-gray-700 pt-4">
            <div class="flex justify-between text-white text-xl font-bold mb-4">
                <span>Total:</span>
                <span id="total-price">S/. 0.00</span>
            </div>
            <form id="add-to-cart-form" action="/perunet/builder/add-to-cart" method="POST">
                <input type="hidden" name="products" id="products-input" value="[]">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition">
                    <i class="fa fa-shopping-cart mr-2"></i> Agregar al Carrito
                </button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>

<script>
// Builder functionality with localStorage persistence
const STORAGE_KEY = 'builder_setup_products';
let selectedProducts = {};
let totalPrice = 0;

// Load saved products from localStorage on page load
function loadSavedProducts() {
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            selectedProducts = JSON.parse(saved);
            updateSummary();
            
            // Highlight selected product in current category
            const currentCategoryId = <?= $currentCategory['id_cat'] ?? 0 ?>;
            if (selectedProducts[currentCategoryId]) {
                document.querySelectorAll('.product-card').forEach(card => {
                    if (card.dataset.productId === selectedProducts[currentCategoryId].id) {
                        card.classList.add('ring-4', 'ring-red-500');
                    }
                });
            }
        }
    } catch (e) {
        console.error('Error loading saved products:', e);
    }
}

// Save products to localStorage
function saveProducts() {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedProducts));
    } catch (e) {
        console.error('Error saving products:', e);
    }
}

// Select product
document.querySelectorAll('.select-product-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const card = this.closest('.product-card');
        const productId = card.dataset.productId;
        const productName = card.dataset.productName;
        const productPrice = parseFloat(card.dataset.productPrice);
        const productImage = card.dataset.productImage;
        const categoryId = <?= $currentCategory['id_cat'] ?? 0 ?>;
        const categoryName = '<?= $currentCategory['nombre'] ?? '' ?>';

        // Remove previous selection from this category
        if (selectedProducts[categoryId]) {
            totalPrice -= selectedProducts[categoryId].price * (selectedProducts[categoryId].quantity || 1);
        }

        // Add new selection
        selectedProducts[categoryId] = {
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            category: categoryName,
            quantity: selectedProducts[categoryId]?.quantity || 1 // Preserve quantity if exists
        };

        totalPrice += productPrice * (selectedProducts[categoryId].quantity || 1);

        // Save to localStorage
        saveProducts();

        // Update UI
        updateSummary();
        
        // Visual feedback
        document.querySelectorAll('.product-card').forEach(c => c.classList.remove('ring-4', 'ring-red-500'));
        card.classList.add('ring-4', 'ring-red-500');
    });
});

function updateSummary() {
    const container = document.getElementById('selected-products');
    const productsArray = Object.values(selectedProducts);
    
    container.innerHTML = productsArray.map((p, index) => `
        <div class="bg-gray-800 rounded-lg p-3" data-product-key="${Object.keys(selectedProducts).find(key => selectedProducts[key] === p)}">
            <div class="flex items-center gap-3 mb-2">
                <img src="/perunet/public/img/${p.image}" class="w-12 h-12 object-cover rounded" onerror="this.src='/perunet/public/img/EMPRESA/p.png'">
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-semibold truncate">${p.category}</p>
                    <p class="text-gray-400 text-xs truncate">${p.name}</p>
                </div>
                <button onclick="removeProduct('${Object.keys(selectedProducts).find(key => selectedProducts[key] === p)}')" class="text-red-400 hover:text-red-300">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <label class="text-gray-400 text-xs">Cant:</label>
                    <input type="number" min="1" max="10" value="${p.quantity || 1}" 
                           class="quantity-input w-16 bg-gray-700 text-white text-center rounded px-2 py-1 text-sm"
                           onchange="updateQuantity('${Object.keys(selectedProducts).find(key => selectedProducts[key] === p)}', this.value)">
                </div>
                <span class="text-red-400 font-bold text-sm whitespace-nowrap">S/. ${(p.price * (p.quantity || 1)).toFixed(2)}</span>
            </div>
        </div>
    `).join('');

    // Calculate total with quantities
    let total = 0;
    productsArray.forEach(p => {
        total += p.price * (p.quantity || 1);
    });

    document.getElementById('total-price').textContent = `S/. ${total.toFixed(2)}`;
    
    // Send products with quantities
    const productsData = productsArray.map(p => ({
        id: p.id,
        quantity: p.quantity || 1
    }));
    document.getElementById('products-input').value = JSON.stringify(productsData);
}

// Update quantity
function updateQuantity(categoryId, quantity) {
    if (selectedProducts[categoryId]) {
        selectedProducts[categoryId].quantity = parseInt(quantity) || 1;
        saveProducts(); // Save to localStorage
        updateSummary();
    }
}

// Remove product
function removeProduct(categoryId) {
    if (selectedProducts[categoryId]) {
        delete selectedProducts[categoryId];
        saveProducts(); // Save to localStorage
        updateSummary();
        
        // Remove visual selection
        document.querySelectorAll('.product-card').forEach(card => {
            if (card.dataset.productId === selectedProducts[categoryId]?.id) {
                card.classList.remove('ring-4', 'ring-red-500');
            }
        });
    }
}

// Toggle summary panel
document.getElementById('toggle-summary').addEventListener('click', function() {
    const content = document.getElementById('summary-content');
    const icon = this.querySelector('i');
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.className = 'fa fa-minus';
    } else {
        content.style.display = 'none';
        icon.className = 'fa fa-plus';
    }
});

// Clear localStorage when adding to cart
document.getElementById('add-to-cart-form').addEventListener('submit', function() {
    localStorage.removeItem(STORAGE_KEY);
});

// Load saved products on page load
loadSavedProducts();
</script>
