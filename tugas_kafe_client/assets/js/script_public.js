// Shopping Cart Management
class ShoppingCart {
    constructor() {
        this.items = this.loadFromStorage();
        this.updateCartUI();
    }

    loadFromStorage() {
        const stored = localStorage.getItem('kafe_cart');
        return stored ? JSON.parse(stored) : [];
    }

    saveToStorage() {
        localStorage.setItem('kafe_cart', JSON.stringify(this.items));
    }

    addItem(menu) {
        const existingItem = this.items.find(item => item.id_menu === menu.id_menu);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({
                id_menu: menu.id_menu,
                nama_menu: menu.nama_menu,
                harga: menu.harga,
                gambar: menu.gambar,
                quantity: 1
            });
        }

        this.saveToStorage();
        this.updateCartUI();
        this.showNotification(`${menu.nama_menu} ditambahkan ke keranjang`, 'success');
    }

    updateItemQuantity(id_menu, quantity) {
        const item = this.items.find(item => item.id_menu === id_menu);
        if (item) {
            if (quantity <= 0) {
                this.removeItem(id_menu);
            } else {
                item.quantity = quantity;
                this.saveToStorage();
                this.updateCartUI();
            }
        }
    }

    removeItem(id_menu) {
        this.items = this.items.filter(item => item.id_menu !== id_menu);
        this.saveToStorage();
        this.updateCartUI();
    }

    clearCart() {
        this.items = [];
        this.saveToStorage();
        this.updateCartUI();
    }

    getTotalItems() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    }

    getTotalPrice() {
        return this.items.reduce((total, item) => total + (item.harga * item.quantity), 0);
    }

    updateCartUI() {
        const cartCount = document.getElementById('cart-count');
        const checkoutBtn = document.getElementById('checkoutBtn');

        if (cartCount) {
            cartCount.textContent = this.getTotalItems();
        }

        if (checkoutBtn) {
            checkoutBtn.disabled = this.items.length === 0;
        }
    }

    showNotification(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        const toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        toastContainer.appendChild(toast);
        document.body.appendChild(toastContainer);

        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            toastContainer.remove();
        });
    }
}

// Initialize cart
const cart = new ShoppingCart();

// Menu Management
class MenuManager {
    constructor() {
        this.menus = [];
        this.loadMenus();
    }

    async loadMenus() {
        try {
            const result = await apiRequest(API_CONFIG.ENDPOINTS.MENUS.READ);

            console.log('API Response:', result);

            if (result.success) {
                this.menus = result.data;
                console.log('Menus loaded:', this.menus);
                this.renderMenus();
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('menu-container').classList.remove('d-none');
            } else {
                this.showError('Gagal memuat menu: ' + result.message);
            }
        } catch (error) {
            console.error('Error loading menus:', error);
            this.showError('Terjadi kesalahan saat memuat menu');
        }
    }

    renderMenus() {
        const container = document.getElementById('menu-container');
        container.innerHTML = '';

        this.menus.forEach(category => {
            const categorySection = this.createCategorySection(category);
            container.appendChild(categorySection);
        });
    }

    createCategorySection(category) {
        const section = document.createElement('div');
        section.className = 'mb-5';

        section.innerHTML = `
            <div class="d-flex align-items-center mb-3">
                <span class="display-6 me-3">${category.icon}</span>
                <div>
                    <h3 class="mb-0">${category.kategori}</h3>
                    <p class="text-muted mb-0">${category.items.length} menu tersedia</p>
                </div>
            </div>
            <div class="row" id="category-${category.kategori.replace(/\s+/g, '-').toLowerCase()}">
                ${category.items.map(menu => this.createMenuCard(menu)).join('')}
            </div>
        `;

        return section;
    }

    createMenuCard(menu) {
        return `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 menu-card">
                    <div class="position-relative">
                        <img src="${menu.gambar || 'https://via.placeholder.com/400x300?text=No+Image'}"
                             class="card-img-top menu-image"
                             alt="${menu.nama_menu}">
                        <span class="position-absolute top-0 end-0 m-2 badge bg-success">Tersedia</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${menu.nama_menu}</h5>
                        <div class="mb-auto">
                            <p class="card-text text-muted small">Menu lezat dan segar</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-success mb-0">${menu.harga_formatted}</span>
                            <button class="btn btn-success btn-sm" onclick="addToCart(${menu.id_menu})">
                                <i class="bi bi-cart-plus me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    showError(message) {
        const container = document.getElementById('menu-container');
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle text-warning display-1 mb-3"></i>
                <h4 class="text-danger">Error</h4>
                <p class="text-muted">${message}</p>
                <button class="btn btn-success" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                </button>
            </div>
        `;
    }
}

// Initialize menu manager
const menuManager = new MenuManager();

// Cart Functions
function addToCart(menuId) {
    console.log('addToCart called with menuId:', menuId);
    console.log('menuManager.menus:', menuManager.menus);

    // Find menu data from menu manager
    let menu = null;
    for (const category of menuManager.menus) {
        const found = category.items.find(item => item.id_menu === menuId);
        if (found) {
            menu = found;
            break;
        }
    }

    if (menu) {
        console.log('Menu found:', menu);
        cart.addItem(menu);
    } else {
        console.log('Menu not found for ID:', menuId);
    }
}

function showCart() {
    const modal = new bootstrap.Modal(document.getElementById('cartModal'));
    renderCartContent();
    modal.show();
}

function renderCartContent() {
    const container = document.getElementById('cartContent');

    if (cart.items.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="bi bi-cart-x text-muted display-1 mb-3"></i>
                <h5>Keranjang Kosong</h5>
                <p class="text-muted">Tambahkan menu ke keranjang terlebih dahulu</p>
            </div>
        `;
        return;
    }

    let itemsHtml = '';
    let total = 0;

    cart.items.forEach(item => {
        const subtotal = item.harga * item.quantity;
        total += subtotal;

        itemsHtml += `
            <div class="row align-items-center mb-3 pb-3 border-bottom">
                <div class="col-2">
                    <img src="${item.gambar || 'https://via.placeholder.com/60x60?text=No+Image'}"
                         class="img-fluid rounded" alt="${item.nama_menu}">
                </div>
                <div class="col-4">
                    <h6 class="mb-1">${item.nama_menu}</h6>
                    <small class="text-muted">${formatRupiah(item.harga)}</small>
                </div>
                <div class="col-3">
                    <div class="input-group input-group-sm">
                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${item.id_menu}, ${item.quantity - 1})">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${item.id_menu}, ${item.quantity + 1})">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="col-2 text-end">
                    <strong>${formatRupiah(subtotal)}</strong>
                </div>
                <div class="col-1">
                    <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id_menu})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = `
        ${itemsHtml}
        <div class="mt-4 pt-3 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Total:</h5>
                <h4 class="text-success">${formatRupiah(total)}</h4>
            </div>
            <button class="btn btn-outline-secondary btn-sm mt-2" onclick="clearCart()">
                <i class="bi bi-trash me-1"></i>Kosongkan Keranjang
            </button>
        </div>
    `;
}

function updateQuantity(menuId, quantity) {
    cart.updateItemQuantity(menuId, quantity);
    renderCartContent();
}

function removeFromCart(menuId) {
    cart.removeItem(menuId);
    renderCartContent();
}

function clearCart() {
    if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
        cart.clearCart();
        renderCartContent();
    }
}

function checkoutCart() {
    if (cart.items.length === 0) {
        alert('Keranjang masih kosong');
        return;
    }

    // Close cart modal
    const cartModal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
    cartModal.hide();

    // Show order modal
    const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
    updateOrderSummary();
    orderModal.show();
}

function updateOrderSummary() {
    const container = document.getElementById('orderSummary');
    const totalElement = document.getElementById('totalOrder');

    if (cart.items.length === 0) {
        container.innerHTML = '<p class="text-muted">Keranjang kosong</p>';
        totalElement.textContent = 'Rp 0';
        return;
    }

    let summaryHtml = '';
    cart.items.forEach(item => {
        const subtotal = item.harga * item.quantity;
        summaryHtml += `
            <div class="d-flex justify-content-between mb-1">
                <span>${item.nama_menu} x${item.quantity}</span>
                <span>${formatRupiah(subtotal)}</span>
            </div>
        `;
    });

    container.innerHTML = summaryHtml;
    totalElement.textContent = formatRupiah(cart.getTotalPrice());
}

async function submitOrder() {
    const namaPelanggan = document.getElementById('namaPelanggan').value.trim();
    const noMeja = document.getElementById('noMeja').value;

    // Validation
    if (!namaPelanggan || !noMeja) {
        alert('Mohon lengkapi semua field');
        return;
    }

    if (cart.items.length === 0) {
        alert('Keranjang belanja masih kosong');
        return;
    }

    // Prepare order data
    const orderData = {
        nama_pelanggan: namaPelanggan,
        no_meja: noMeja,
        items: cart.items.map(item => ({
            id_menu: item.id_menu,
            jumlah: item.quantity
        }))
    };

    try {
        // Show loading
        const orderBtn = document.querySelector('#orderModal .btn-success');
        const originalText = orderBtn.innerHTML;
        orderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memesan...';
        orderBtn.disabled = true;

        const result = await apiRequest(API_CONFIG.ENDPOINTS.ORDERS.CREATE, {
            method: 'POST',
            body: JSON.stringify(orderData)
        });

        if (result.success) {
            // Close order modal
            const orderModal = bootstrap.Modal.getInstance(document.getElementById('orderModal'));
            orderModal.hide();

            // Show success modal
            showOrderSuccess(result.data);

            // Clear cart
            cart.clearCart();

            // Reset form
            document.getElementById('orderForm').reset();
        } else {
            alert('Gagal membuat pesanan: ' + result.message);
        }
    } catch (error) {
        console.error('Error submitting order:', error);
        alert('Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.');
    } finally {
        // Restore button
        orderBtn.innerHTML = originalText;
        orderBtn.disabled = false;
    }
}

function showOrderSuccess(orderData) {
    const detailsContainer = document.getElementById('orderDetails');

    detailsContainer.innerHTML = `
        <div class="alert alert-success">
            <strong>Pesanan #${orderData.id_pesanan}</strong> telah diterima
        </div>
        <div class="row">
            <div class="col-6"><strong>Pelanggan:</strong></div>
            <div class="col-6">${orderData.nama_pelanggan}</div>
        </div>
        <div class="row">
            <div class="col-6"><strong>No Meja:</strong></div>
            <div class="col-6">${orderData.no_meja}</div>
        </div>
        <div class="row">
            <div class="col-6"><strong>Total:</strong></div>
            <div class="col-6">${orderData.total_harga_formatted}</div>
        </div>
        <div class="row">
            <div class="col-6"><strong>Status:</strong></div>
            <div class="col-6"><span class="badge bg-warning">Menunggu Diproses</span></div>
        </div>
        <div class="row">
            <div class="col-6"><strong>Waktu:</strong></div>
            <div class="col-6">${orderData.waktu_pesan_formatted}</div>
        </div>
    `;

    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
}

function redirectToAntrian() {
    window.location.href = 'display_antrian.php';
}

// Utility functions
function formatRupiah(amount) {
    return "Rp " + Number(amount).toLocaleString('id-ID');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add parallax effect to hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
});