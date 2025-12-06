<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Antrian - Warung Kafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-cup-hot-fill me-2"></i>Warung Kafe
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house me-1"></i>Beranda
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="bg-gradient py-4 text-white">
        <div class="container">
            <h1 class="mb-0 text-success">
                <i class="bi bi-list-check me-2 text-success"></i>Status Antrian Pesanan
            </h1>
            <p class="mb-0 mt-2 text-dark">Monitor status pesanan Anda secara real-time</p>
        </div>
    </div>

    <!-- Live Time -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="text-muted mb-1">Waktu Sekarang</h5>
                        <h3 class="text-success mb-0" id="liveTime"></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Queue Statistics -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="badge bg-warning p-3 mb-2">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <h4 class="mb-0" id="pendingCount">0</h4>
                        <small class="text-muted">Menunggu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="badge bg-info p-3 mb-2">
                            <i class="bi bi-fire fs-4"></i>
                        </div>
                        <h4 class="mb-0" id="prosesCount">0</h4>
                        <small class="text-muted">Dimasak</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="badge bg-success p-3 mb-2">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                        <h4 class="mb-0" id="siapCount">0</h4>
                        <small class="text-muted">Siap Diambil</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="badge bg-secondary p-3 mb-2">
                            <i class="bi bi-hand-thumbs-up fs-4"></i>
                        </div>
                        <h4 class="mb-0" id="selesaiCount">0</h4>
                        <small class="text-muted">Selesai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama pelanggan atau nomor meja...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Semua Status</option>
                                    <option value="pending">Menunggu</option>
                                    <option value="proses">Dimasak</option>
                                    <option value="siap">Siap Diambil</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Container -->
    <div class="container mt-4 mb-5">
        <!-- Pending Orders -->
        <div class="mb-4">
            <h5 class="text-warning mb-3">
                <i class="bi bi-clock-history me-2"></i>Menunggu Diproses
            </h5>
            <div id="pendingOrders" class="row">
                <!-- Pending orders akan di-load dinamis -->
            </div>
        </div>

        <!-- Processing Orders -->
        <div class="mb-4">
            <h5 class="text-info mb-3">
                <i class="bi bi-fire me-2"></i>Sedang Dimasak
            </h5>
            <div id="prosesOrders" class="row">
                <!-- Processing orders akan di-load dinamis -->
            </div>
        </div>

        <!-- Ready Orders -->
        <div class="mb-4">
            <h5 class="text-success mb-3">
                <i class="bi bi-check-circle me-2"></i>Siap Diambil
            </h5>
            <div id="siapOrders" class="row">
                <!-- Ready orders akan di-load dinamis -->
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="mb-4">
            <h5 class="text-secondary mb-3">
                <i class="bi bi-hand-thumbs-up me-2"></i>Selesai
            </h5>
            <div id="selesaiOrders" class="row">
                <!-- Completed orders akan di-load dinamis -->
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5">
            <i class="bi bi-inbox text-muted display-1"></i>
            <h4 class="text-muted mt-3">Belum Ada Pesanan</h4>
            <p class="text-muted">Pesanan akan muncul di sini ketika ada yang dibuat</p>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt me-2"></i>Detail Pesanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailContent">
                    <!-- Order detail akan di-load dinamis -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light border-top py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-cup-hot-fill me-2"></i>Warung Kafe</h5>
                    <p class="mb-0">Nikmati berbagai menu lezat dengan harga terjangkau.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="bi bi-clock me-1"></i>Buka: 08:00 - 22:00<br>
                        <i class="bi bi-telephone me-1"></i>Hubungi: (021) 123-4567
                    </p>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <small>&copy; 2024 Warung Kafe. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="config.js"></script>
    <script>
        // Auto-refresh data
        let refreshInterval;
        const refreshTime = 5000; // 5 seconds

        // Update live time
        function updateLiveTime() {
            const now = new Date();
            const timeString = now.toLocaleString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('liveTime').textContent = timeString;
        }

        // Load orders
        async function loadOrders() {
            try {
                const status = document.getElementById('statusFilter').value;
                const search = document.getElementById('searchInput').value;

                let endpoint = API_CONFIG.ENDPOINTS.ORDERS.READ + '?limit=20';
                if (status) endpoint += `&status=${status}`;

                const result = await apiRequest(endpoint);

                if (result.success) {
                    displayOrders(result.data.orders);
                    updateStatistics(result.data.statistics);

                    // Apply search filter
                    if (search) {
                        applySearchFilter(search);
                    }
                } else {
                    console.error('Error loading orders:', result.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Display orders
        function displayOrders(orders) {
            const containers = {
                pending: document.getElementById('pendingOrders'),
                proses: document.getElementById('prosesOrders'),
                siap: document.getElementById('siapOrders'),
                selesai: document.getElementById('selesaiOrders')
            };

            // Clear all containers
            Object.values(containers).forEach(container => {
                container.innerHTML = '';
            });

            if (orders.length === 0) {
                document.getElementById('emptyState').style.display = 'block';
                Object.values(containers).forEach(container => {
                    container.parentElement.style.display = 'none';
                });
                return;
            }

            document.getElementById('emptyState').style.display = 'none';
            Object.values(containers).forEach(container => {
                container.parentElement.style.display = 'block';
            });

            orders.forEach(order => {
                const orderCard = createOrderCard(order);
                if (containers[order.status_pesanan]) {
                    containers[order.status_pesanan].appendChild(orderCard);
                }
            });
        }

        // Create order card
        function createOrderCard(order) {
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4 mb-3';

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

            col.innerHTML = `
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
                        <h6 class="card-title">${order.nama_pelanggan}</h6>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="bi bi-grid-3x3-gap me-1"></i>${order.no_meja}<br>
                                <i class="bi bi-clock me-1"></i>${order.waktu_pesan_formatted}<br>
                                <strong>${order.total_harga_formatted}</strong>
                            </small>
                        </p>
                        <button class="btn btn-sm btn-outline-primary" onclick="showOrderDetail(${order.id_pesanan})">
                            <i class="bi bi-eye me-1"></i>Detail
                        </button>
                    </div>
                </div>
            `;

            return col;
        }

        // Update statistics
        function updateStatistics(stats) {
            document.getElementById('pendingCount').textContent = stats.pending;
            document.getElementById('prosesCount').textContent = stats.proses;
            document.getElementById('siapCount').textContent = stats.siap;
            document.getElementById('selesaiCount').textContent = stats.selesai;
        }

        // Apply search filter
        function applySearchFilter(searchTerm) {
            const cards = document.querySelectorAll('.card');
            const lowerSearch = searchTerm.toLowerCase();

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const cardCol = card.closest('.col-md-6, .col-lg-4');
                if (text.includes(lowerSearch)) {
                    cardCol.style.display = 'block';
                } else {
                    cardCol.style.display = 'none';
                }
            });
        }

        // Show order detail
        async function showOrderDetail(orderId) {
            try {
                console.log('Loading order detail for ID:', orderId);
                const result = await apiRequest(API_CONFIG.ENDPOINTS.ORDERS.READ + `?id=${orderId}`);
                console.log('API Response:', result);

                if (result.success && result.data && result.data.orders && result.data.orders.length > 0) {
                    const order = result.data.orders[0];
                    console.log('Order data:', order);

                    let itemsHtml = '';
                    if (order.items && order.items.length > 0) {
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
                    } else {
                        itemsHtml = '<tr><td colspan="4" class="text-center">Tidak ada item</td></tr>';
                    }

                    // Prepare cashier info
                    let cashierInfo = '';
                    if (order.kasir && order.kasir.display_name) {
                        cashierInfo = `
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>Dilayani oleh:</strong>
                                    <span class="badge ${order.kasir.role === 'admin' ? 'bg-danger' : 'bg-primary'} ms-2">
                                        <i class="bi bi-person-badge me-1"></i>${order.kasir.display_name}
                                        <small>(${order.kasir.role})</small>
                                    </span>
                                </div>
                            </div>
                        `;
                    } else {
                        cashierInfo = `
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <strong>Dilayani oleh:</strong>
                                    <span class="badge bg-secondary ms-2">
                                        <i class="bi bi-person-dash me-1"></i>Tidak ada kasir
                                    </span>
                                </div>
                            </div>
                        `;
                    }

                    document.getElementById('orderDetailContent').innerHTML = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>ID Pesanan:</strong> #${order.id_pesanan}
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <span class="badge bg-${order.status_pesanan === 'pending' ? 'warning' : order.status_pesanan === 'proses' ? 'info' : order.status_pesanan === 'siap' ? 'success' : 'secondary'}">
                                    ${order.status_display}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Pelanggan:</strong> ${order.nama_pelanggan}
                            </div>
                            <div class="col-md-6">
                                <strong>No Meja:</strong> ${order.no_meja}
                            </div>
                        </div>
                        ${cashierInfo}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Waktu Pesan:</strong> ${order.waktu_pesan_formatted}
                            </div>
                            <div class="col-md-6">
                                <strong>Update:</strong> ${order.updated_at_formatted}
                            </div>
                        </div>
                        <hr>
                        <h6>Detail Menu:</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end">${order.total_harga_formatted}</th>
                                </tr>
                            </tfoot>
                        </table>
                    `;

                    const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
                    modal.show();
                } else {
                    console.error('Invalid response or no orders found:', result);
                    alert('Detail pesanan tidak ditemukan: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error loading order detail:', error);
                alert('Gagal memuat detail pesanan. Error: ' + error.message);
            }
        }

        // Event listeners
        document.getElementById('statusFilter').addEventListener('change', loadOrders);
        document.getElementById('searchInput').addEventListener('input', () => {
            const search = document.getElementById('searchInput').value;
            applySearchFilter(search);
        });

        // Initialize
        updateLiveTime();
        loadOrders();

        // Set intervals
        setInterval(updateLiveTime, 1000);
        refreshInterval = setInterval(loadOrders, refreshTime);

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
</body>
</html>
