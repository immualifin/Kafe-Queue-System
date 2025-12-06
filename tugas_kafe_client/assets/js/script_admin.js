// Global variables
let currentOrders = [];
let currentMenus = [];
let currentEditingMenu = null;
let sessionToken = localStorage.getItem('session_token');
let refreshInterval;

// Check authentication
function checkAuth() {
    if (!sessionToken) {
        window.location.href = 'login.php';
        return false;
    }

    // Verify session
    apiRequest(API_CONFIG.ENDPOINTS.AUTH.CHECK_SESSION)
    .then(result => {
        if (!result.success || !result.data.is_valid) {
            logout();
        } else {
            // Update username in header
            document.getElementById('username').textContent = result.data.user.nama_lengkap;
        }
    })
    .catch(error => {
        console.error('Auth check error:', error);
        logout();
    });

    return true;
}

// Logout function
function logout() {
    localStorage.removeItem('user');
    localStorage.removeItem('session_token');
    localStorage.removeItem('login_time');
    window.location.href = 'login.php';
}

// Show alert notification
function showAlert(message, type = 'success', duration = 5000) {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = 'alert-' + Date.now();

    const alertHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" id="${alertId}">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-1"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

    alertContainer.innerHTML = alertHtml;

    const toast = new bootstrap.Toast(document.getElementById(alertId), {
        autohide: true,
        delay: duration
    });
    toast.show();

    // Auto remove after hiding
    document.getElementById(alertId).addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// Format currency
function formatRupiah(amount) {
    return "Rp " + Number(amount).toLocaleString('id-ID');
}

// Order Management
class OrderManager {
    constructor() {
        this.loadOrders();
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    setupEventListeners() {
        document.getElementById('orderSearch').addEventListener('input', () => this.filterOrders());
        document.getElementById('statusFilter').addEventListener('change', () => this.loadOrders());

        // Tab change listener
        document.getElementById('orders-tab').addEventListener('shown.bs.tab', () => {
            this.loadOrders();
        });

        document.getElementById('menus-tab').addEventListener('shown.bs.tab', () => {
            if (window.menuManager) {
                window.menuManager.loadMenus();
            }
        });

        document.getElementById('stats-tab').addEventListener('shown.bs.tab', () => {
            if (window.statisticsManager) {
                window.statisticsManager.loadStatistics();
            }
        });
    }

    startAutoRefresh() {
        refreshInterval = setInterval(() => {
            const activeTab = document.querySelector('.tab-pane.active').id;
            if (activeTab === 'orders') {
                this.loadOrders();
            }
        }, 10000); // Refresh every 10 seconds
    }

    async loadOrders() {
        try {
            const status = document.getElementById('statusFilter').value;
            let endpoint = API_CONFIG.ENDPOINTS.ORDERS.READ + '?limit=50';
            if (status) endpoint += `&status=${status}`;

            const result = await apiRequest(endpoint);

            if (result.success) {
                currentOrders = result.data.orders;
                this.renderOrders(result.data);
            } else {
                showAlert('Gagal memuat pesanan: ' + result.message, 'danger');
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            showAlert('Terjadi kesalahan saat memuat pesanan', 'danger');
        }
    }

    renderOrders(data) {
        this.updateStatistics(data.statistics);
        this.renderOrdersList(data.orders);

        // Show/hide empty state
        const emptyState = document.getElementById('ordersEmptyState');
        const container = document.getElementById('ordersContainer');

        if (data.orders.length === 0) {
            emptyState.style.display = 'block';
            container.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            container.style.display = 'block';
        }
    }

    updateStatistics(stats) {
        document.getElementById('pendingCount').textContent = stats.pending;
        document.getElementById('prosesCount').textContent = stats.proses;
        document.getElementById('siapCount').textContent = stats.siap;
        document.getElementById('selesaiCount').textContent = stats.selesai;
    }

    renderOrdersList(orders) {
        const container = document.getElementById('ordersContainer');
        let html = '<div class="row">';

        orders.forEach(order => {
            const statusColor = {
                pending: 'warning',
                proses: 'info',
                siap: 'success',
                selesai: 'secondary'
            };

            const statusIcon = {
                pending: 'clock-history',
                proses: 'fire',
                siap: 'check-circle',
                selesai: 'hand-thumbs-up'
            };

            // Prepare cashier info display
            let cashierInfo = '';
            if (order.kasir && order.kasir.display_name) {
                const cashierBadge = order.kasir.role === 'admin' ? 'bg-danger' : 'bg-primary';
                cashierInfo = `
                    <p class="text-muted mb-1">
                        <i class="bi bi-person-badge me-1"></i>
                        <span class="badge ${cashierBadge}">${order.kasir.display_name}</span>
                    </p>
                `;
            } else {
                cashierInfo = `
                    <p class="text-muted mb-1">
                        <i class="bi bi-person-dash me-1"></i>
                        <span class="badge bg-secondary">Tidak ada kasir</span>
                    </p>
                `;
            }

            html += `
                <div class="col-lg-6 mb-3 order-card" data-status="${order.status_pesanan}" data-search="${order.nama_pelanggan.toLowerCase()} ${order.no_meja.toLowerCase()}">
                    <div class="card border-${statusColor[order.status_pesanan]} h-100">
                        <div class="card-header bg-${statusColor[order.status_pesanan]} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-${statusIcon[order.status_pesanan]} me-1"></i>
                                    ${order.status_display}
                                </span>
                                <span class="badge bg-light text-dark">#${order.id_pesanan}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-1">${order.nama_pelanggan}</h6>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-grid-3x3-gap me-1"></i>${order.no_meja}
                                    </p>
                                    ${cashierInfo}
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-clock me-1"></i>${order.waktu_pesan_formatted}
                                    </p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <h5 class="text-success mb-3">${order.total_harga_formatted}</h5>
                                    <div class="btn-group-vertical d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="showOrderDetail(${order.id_pesanan})">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </button>
                                        ${this.getStatusButtons(order)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    getStatusButtons(order) {
        let buttons = '';

        switch(order.status_pesanan) {
            case 'pending':
                buttons = `
                    <button class="btn btn-outline-info btn-sm" onclick="updateOrderStatus(${order.id_pesanan}, 'proses')">
                        <i class="bi bi-fire me-1"></i>Proses
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="updateOrderStatus(${order.id_pesanan}, 'selesai')">
                        <i class="bi bi-check me-1">Selesai
                    </button>
                `;
                break;
            case 'proses':
                buttons = `
                    <button class="btn btn-outline-success btn-sm" onclick="updateOrderStatus(${order.id_pesanan}, 'siap')">
                        <i class="bi bi-check-circle me-1"></i>Siap
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="updateOrderStatus(${order.id_pesanan}, 'selesai')">
                        <i class="bi bi-hand-thumbs-up me-1"></i>Selesai
                    </button>
                `;
                break;
            case 'siap':
                buttons = `
                    <button class="btn btn-outline-secondary btn-sm" onclick="updateOrderStatus(${order.id_pesanan}, 'selesai')">
                        <i class="bi bi-hand-thumbs-up me-1"></i>Selesai
                    </button>
                `;
                break;
        }

        return buttons;
    }

    filterOrders() {
        const searchTerm = document.getElementById('orderSearch').value.toLowerCase();
        const cards = document.querySelectorAll('.order-card');

        cards.forEach(card => {
            const searchTerms = card.dataset.search;
            if (searchTerms.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
}

// Menu Management
class MenuManager {
    constructor() {
        this.loadMenus();
        this.loadCategories();
    }

    async loadCategories() {
        try {
            const result = await apiRequest(API_CONFIG.ENDPOINTS.MENUS.READ);
            if (result.success && result.data) {
                this.renderCategoryOptions(result.data);
            } else {
                this.loadFallbackCategories();
            }
        } catch (error) {
            this.loadFallbackCategories();
        }
    }

    loadFallbackCategories() {
        const fallbackCategories = [
            { kategori: 'Makanan Berat', id_kategori: 1 },
            { kategori: 'Minuman', id_kategori: 2 },
            { kategori: 'Cemilan', id_kategori: 3 },
            { kategori: 'Dessert', id_kategori: 4 }
        ];
        this.renderCategoryOptions(fallbackCategories);
    }

    renderCategoryOptions(categories) {
        const select = document.getElementById('menuCategory');
        if (!select) return;

        select.innerHTML = '<option value="">Pilih Kategori</option>';
        categories.forEach(category => {
            const option = document.createElement('option');
            const categoryId = category.id_kategori || this.getCategoryIdByName(category.kategori);
            option.value = categoryId;
            option.textContent = category.kategori;
            select.appendChild(option);
        });
    }

    getCategoryIdByName(categoryName) {
        const mapping = { 'Makanan Berat': 1, 'Minuman': 2, 'Cemilan': 3, 'Dessert': 4 };
        return mapping[categoryName] || 1;
    }

    getCategoryName(categoryId) {
        if (categoryId === null || categoryId === undefined || categoryId === '' || categoryId === 0) {
            return 'Tidak ada kategori';
        }
        const catId = parseInt(categoryId);
        const mapping = { 1: 'Makanan Berat', 2: 'Minuman', 3: 'Cemilan', 4: 'Dessert' };
        return mapping[catId] || `Kategori ID ${categoryId} tidak valid`;
    }

    async loadMenus() {
        try {
            const result = await apiRequest(API_CONFIG.ENDPOINTS.MENUS.READ);
            if (result.success) {
                currentMenus = [];
                result.data.forEach(category => {
                    currentMenus.push(...category.items);
                });
                this.renderMenus();
            } else {
                showAlert('Gagal memuat menu: ' + result.message, 'danger');
            }
        } catch (error) {
            console.error('Error loading menus:', error);
            showAlert('Terjadi kesalahan saat memuat menu', 'danger');
        }
    }

    renderMenus() {
        const container = document.getElementById('menusContainer');
        const emptyState = document.getElementById('menusEmptyState');

        if (currentMenus.length === 0) {
            container.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        container.style.display = 'block';
        emptyState.style.display = 'none';

        let html = '<div class="row">';
        currentMenus.forEach(menu => {
            html += `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="${menu.gambar || 'https://via.placeholder.com/400x300?text=No+Image'}"
                                 class="card-img-top menu-image" alt="${menu.nama_menu}"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Image+Error';">
                            <span class="position-absolute top-0 end-0 m-2 badge ${menu.status_tersedia ? 'bg-success' : 'bg-secondary'}">
                                ${menu.status_tersedia ? 'Tersedia' : 'Tidak Tersedia'}
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${menu.nama_menu}</h5>
                            <div class="mb-auto">
                                <p class="card-text text-muted">Kategori: <span id="category-${menu.id_menu}">Loading...</span></p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-success mb-0">${menu.harga_formatted}</span>
                                <div>
                                    <button class="btn btn-outline-primary btn-sm me-1" onclick="editMenu(${menu.id_menu})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteMenu(${menu.id_menu})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;

        currentMenus.forEach(menu => {
            const categoryElement = document.getElementById(`category-${menu.id_menu}`);
            if (categoryElement) {
                const categoryName = menu.nama_kategori || this.getCategoryName(menu.id_kategori);
                let categoryClass = 'text-muted';
                if (categoryName === 'Tidak ada kategori') categoryClass = 'text-warning';
                else if (categoryName.includes('tidak valid')) categoryClass = 'text-danger';
                categoryElement.innerHTML = `<span class="${categoryClass}">${categoryName}</span>`;
            }
        });
    }

    async saveMenu() {
        const nama_menu = document.getElementById('menuName').value;
        const harga = document.getElementById('menuPrice').value;
        const id_kategori = document.getElementById('menuCategory').value;
        const gambar = document.getElementById('menuImageURL').value;

        if (!nama_menu || !harga || !id_kategori) {
            showAlert('Mohon lengkapi semua field wajib (Nama, Harga, Kategori)', 'danger');
            return;
        }

        const hargaNumeric = parseFloat(harga);
        if (isNaN(hargaNumeric) || hargaNumeric <= 0) {
            showAlert('Harga harus berupa angka positif', 'danger');
            return;
        }

        const button = document.querySelector('#menuModal .btn-success');
        const originalText = button.innerHTML;

        try {
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            button.disabled = true;

            let endpoint = API_CONFIG.ENDPOINTS.MENUS.CREATE;
            let method = 'POST';

            if (currentEditingMenu) {
                endpoint = API_CONFIG.ENDPOINTS.MENUS.UPDATE.replace('{id}', currentEditingMenu);
                method = 'PUT';
            }

            const url = getApiUrl(endpoint);
            const jsonData = {
                nama_menu: nama_menu,
                harga: hargaNumeric.toString(),
                id_kategori: id_kategori,
                status_tersedia: document.getElementById('menuStatus').checked,
                gambar: gambar
            };

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + sessionToken
                },
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();

            if (result.success) {
                showAlert(`Menu berhasil ${currentEditingMenu ? 'diupdate' : 'ditambahkan'}`, 'success');
                this.closeMenuModal();
                this.loadMenus();
            } else {
                showAlert('Gagal menyimpan menu: ' + result.message, 'danger');
            }
        } catch (error) {
            console.error('Error saving menu:', error);
            showAlert('Terjadi kesalahan saat menyimpan menu', 'danger');
        } finally {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }

    closeMenuModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('menuModal'));
        modal.hide();
        document.getElementById('menuForm').reset();
        document.getElementById('imagePreview').innerHTML = '';
        currentEditingMenu = null;
    }
}

// Statistics Management
class StatisticsManager {
    async loadStatistics() {
        try {
            const result = await apiRequest(API_CONFIG.ENDPOINTS.ORDERS.READ + '?limit=1000');
            if (result.success) {
                this.renderStatistics(result.data);
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    renderStatistics(data) {
        const totalOrdersEl = document.getElementById('totalOrdersStat');
        const totalRevenueEl = document.getElementById('totalRevenueStat');
        const avgOrderEl = document.getElementById('avgOrderStat');

        if (totalOrdersEl) totalOrdersEl.textContent = data.statistics.total_orders || 0;
        if (totalRevenueEl) totalRevenueEl.textContent = formatRupiah(data.statistics.total_revenue || 0);

        const avgOrder = (data.statistics.total_orders > 0 && data.statistics.total_revenue > 0) ?
            data.statistics.total_revenue / data.statistics.total_orders : 0;
        if (avgOrderEl) avgOrderEl.textContent = formatRupiah(avgOrder);

        this.renderDailyChart(data.orders || []);
        this.renderPopularMenus(data.orders || []);
    }

    renderDailyChart(orders) {
        const ctx = document.getElementById('dailyOrdersChart').getContext('2d');
        const hourlyData = Array(24).fill(0);
        orders.forEach(order => {
            const hour = new Date(order.waktu_pesan).getHours();
            hourlyData[hour]++;
        });

        if (window.dailyChart) {
            window.dailyChart.destroy();
        }

        window.dailyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array.from({length: 24}, (_, i) => `${i}:00`),
                datasets: [{
                    label: 'Pesanan per Jam',
                    data: hourlyData,
                    borderColor: '#006241',
                    backgroundColor: 'rgba(0, 98, 65, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#006241',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(0, 0, 0, 0.05)', borderColor: '#f9f9f9' }, ticks: { color: '#1e3932' } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, color: '#1e3932' }, grid: { color: 'rgba(0, 0, 0, 0.05)', borderColor: '#f9f9f9' } }
                }
            }
        });
    }

    renderPopularMenus(orders) {
        const menuCount = {};
        orders.forEach(order => {
            order.items.forEach(item => {
                if (!menuCount[item.nama_menu]) menuCount[item.nama_menu] = 0;
                menuCount[item.nama_menu] += item.jumlah;
            });
        });

        const sortedMenus = Object.entries(menuCount).sort((a, b) => b[1] - a[1]).slice(0, 5);
        const container = document.getElementById('popularMenus');

        if (sortedMenus.length === 0) {
            container.innerHTML = '<p class="text-muted">Belum ada data menu terpopuler</p>';
            return;
        }

        let html = '<div class="list-group list-group-flush">';
        sortedMenus.forEach(([menuName, count], index) => {
            const badgeClass = ['success', 'warning', 'info', 'primary', 'secondary'][index];
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center" style="border-left: 4px solid var(--starbucks-green);">
                    <div>
                        <span class="badge bg-${badgeClass} me-2">${index + 1}</span>
                        <span style="color: var(--brown); font-weight: 500;">${menuName}</span>
                    </div>
                    <span class="badge" style="background: var(--accent-green); color: var(--starbucks-green);">${count} pesanan</span>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }
}

// Global functions
async function updateOrderStatus(orderId, newStatus) {
    if (!confirm(`Apakah Anda yakin ingin mengubah status pesanan ke "${newStatus}"?`)) return;

    try {
        const endpoint = API_CONFIG.ENDPOINTS.ORDERS.UPDATE_STATUS.replace('{id}', orderId);
        const result = await apiRequest(endpoint, {
            method: 'PUT',
            body: JSON.stringify({ status_pesanan: newStatus })
        });

        if (result.success) {
            showAlert('Status pesanan berhasil diupdate', 'success');
            orderManager.loadOrders();
        } else {
            showAlert('Gagal update status: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error updating order status:', error);
        showAlert('Terjadi kesalahan saat update status', 'danger');
    }
}

async function showOrderDetail(orderId) {
    try {
        const result = await apiRequest(API_CONFIG.ENDPOINTS.ORDERS.READ + `&id=${orderId}`);
        if (result.success && result.data.orders.length > 0) {
            const order = result.data.orders[0];
            let itemsHtml = '';
            order.items.forEach(item => {
                itemsHtml += `
                    <tr>
                        <td>${item.nama_menu}</td>
                        <td class="text-center">${item.jumlah}</td>
                        <td class="text-end">${item.harga_satuan_formatted}</td>
                        <td class="text-end">${item.subtotal_formatted}</td>
                    </tr>
                `;
            });

            document.getElementById('orderDetailContent').innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6"><strong>ID Pesanan:</strong> #${order.id_pesanan}</div>
                    <div class="col-md-6"><strong>Status:</strong> <span class="badge bg-${order.status_pesanan === 'pending' ? 'warning' : order.status_pesanan === 'proses' ? 'info' : order.status_pesanan === 'siap' ? 'success' : 'secondary'}">${order.status_display}</span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Pelanggan:</strong> ${order.nama_pelanggan}</div>
                    <div class="col-md-6"><strong>No Meja:</strong> ${order.no_meja}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Waktu Pesan:</strong> ${order.waktu_pesan_formatted}</div>
                    <div class="col-md-6"><strong>Update:</strong> ${order.updated_at_formatted}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Dilayani oleh:</strong>
                        ${order.kasir && order.kasir.display_name ?
                            `<span class="badge ${order.kasir.role === 'admin' ? 'bg-danger' : 'bg-primary'} ms-2"><i class="bi bi-person-badge me-1"></i>${order.kasir.display_name} <small>(${order.kasir.role})</small></span>` :
                            `<span class="badge bg-secondary ms-2"><i class="bi bi-person-dash me-1"></i>Tidak ada kasir</span>`
                        }
                    </div>
                </div>
                <hr>
                <h6>Detail Menu:</h6>
                <table class="table table-sm">
                    <thead><tr><th>Menu</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>${itemsHtml}</tbody>
                    <tfoot><tr><th colspan="3" class="text-end">Total:</th><th class="text-end">${order.total_harga_formatted}</th></tr></tfoot>
                </table>
            `;
            const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error loading order detail:', error);
        showAlert('Gagal memuat detail pesanan', 'danger');
    }
}

function showAddMenuModal() {
    currentEditingMenu = null;
    document.getElementById('menuModalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tambah Menu';
    document.getElementById('menuForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
    const modal = new bootstrap.Modal(document.getElementById('menuModal'));
    modal.show();
}

function editMenu(menuId) {
    const menu = currentMenus.find(m => m.id_menu === menuId);
    if (!menu) return;

    currentEditingMenu = menuId;
    document.getElementById('menuModalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Edit Menu';
    document.getElementById('menuName').value = menu.nama_menu;
    document.getElementById('menuPrice').value = parseFloat(menu.harga);
    document.getElementById('menuCategory').value = menu.id_kategori;
    document.getElementById('menuStatus').checked = menu.status_tersedia;
    document.getElementById('menuImageURL').value = menu.gambar || '';

    const preview = document.getElementById('imagePreview');
    if (menu.gambar) {
        preview.innerHTML = `
            <img src="${menu.gambar}" class="img-thumbnail" style="max-height: 80px;">
            <small class="text-muted d-block mt-1">Pratinjau gambar saat ini</small>
        `;
    } else {
        preview.innerHTML = '';
    }

    const modal = new bootstrap.Modal(document.getElementById('menuModal'));
    modal.show();
}

async function deleteMenu(menuId) {
    const menu = currentMenus.find(m => m.id_menu === menuId);
    if (!menu) return;

    if (!confirm(`Apakah Anda yakin ingin menghapus menu "${menu.nama_menu}"?`)) return;

    try {
        const endpoint = API_CONFIG.ENDPOINTS.MENUS.DELETE.replace('{id}', menuId);
        const result = await apiRequest(endpoint, { method: 'DELETE' });

        if (result.success) {
            showAlert('Menu berhasil dihapus', 'success');
            menuManager.loadMenus();
        } else {
            showAlert('Gagal menghapus menu: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error deleting menu:', error);
        showAlert('Terjadi kesalahan saat menghapus menu', 'danger');
    }
}

function saveMenu() {
    menuManager.saveMenu();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (!checkAuth()) return;

    window.orderManager = new OrderManager();
    window.menuManager = new MenuManager();
    window.statisticsManager = new StatisticsManager();

    setTimeout(() => {
        if (window.statisticsManager) {
            window.statisticsManager.loadStatistics();
        }
    }, 1000);

    document.getElementById('menuImageURL')?.addEventListener('input', function(e) {
        const url = e.target.value;
        const preview = document.getElementById('imagePreview');
        if (url) {
            preview.innerHTML = `
                <img src="${url}" class="img-thumbnail mb-2" style="max-height: 150px;" 
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Link+Tidak+Valid';">
            `;
        } else {
            preview.innerHTML = '';
        }
    });

    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
});