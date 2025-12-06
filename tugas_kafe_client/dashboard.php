<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Warung Kafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-stat {
            border-left: 4px solid #667eea;
        }
        .order-pending { border-left-color: #ffc107; }
        .order-proses { border-left-color: #17a2b8; }
        .order-siap { border-left-color: #28a745; }
        .order-selesai { border-left-color: #6c757d; }
    </style>
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
                    <li class="nav-item">
                        <a class="nav-link" href="display_antrian.php">
                            <i class="bi bi-list-check me-1"></i>Antrian
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><span id="username">Admin</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="logout()">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="dashboard-header py-4 text-white">
        <div class="container">
            <h1 class="mb-0">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
            </h1>
            <p class="mb-0 mt-2">Kelola pesanan dan menu kafe</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-light border-bottom sticky-top" style="top: 76px;">
        <div class="container">
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button">
                        <i class="bi bi-receipt me-1"></i>Kelola Pesanan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="menus-tab" data-bs-toggle="tab" data-bs-target="#menus" type="button">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Kelola Menu
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button">
                        <i class="bi bi-graph-up me-1"></i>Statistik
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="container mt-4">
        <div class="tab-content" id="dashboardTabContent">

            <!-- Orders Tab -->
            <div class="tab-pane fade show active" id="orders" role="tabpanel">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card card-stat order-pending">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Menunggu</h6>
                                        <h3 class="mb-0" id="pendingCount">0</h3>
                                    </div>
                                    <div class="text-warning">
                                        <i class="bi bi-clock-history fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card card-stat order-proses">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Proses</h6>
                                        <h3 class="mb-0" id="prosesCount">0</h3>
                                    </div>
                                    <div class="text-info">
                                        <i class="bi bi-fire fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card card-stat order-siap">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Siap</h6>
                                        <h3 class="mb-0" id="siapCount">0</h3>
                                    </div>
                                    <div class="text-success">
                                        <i class="bi bi-check-circle fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card card-stat order-selesai">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Selesai</h6>
                                        <h3 class="mb-0" id="selesaiCount">0</h3>
                                    </div>
                                    <div class="text-secondary">
                                        <i class="bi bi-hand-thumbs-up fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="orderSearch" placeholder="Cari nama pelanggan atau nomor meja...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="pending">Menunggu</option>
                            <option value="proses">Proses</option>
                            <option value="siap">Siap</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                </div>

                <!-- Orders Container -->
                <div id="ordersContainer">
                    <!-- Orders akan di-load dinamis -->
                </div>

                <!-- Empty State -->
                <div id="ordersEmptyState" class="text-center py-5" style="display: none;">
                    <i class="bi bi-inbox text-muted display-1"></i>
                    <h4 class="text-muted mt-3">Belum Ada Pesanan</h4>
                    <p class="text-muted">Pesanan akan muncul di sini ketika ada yang dibuat</p>
                </div>
            </div>

            <!-- Menus Tab -->
            <div class="tab-pane fade" id="menus" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>
                        <i class="bi bi-grid-3x3-gap me-2"></i>Kelola Menu
                    </h4>
                    <button class="btn btn-success" onclick="showAddMenuModal()">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Menu
                    </button>
                </div>

                <div id="menusContainer">
                    <!-- Menus akan di-load dinamis -->
                </div>

                <!-- Empty State -->
                <div id="menusEmptyState" class="text-center py-5" style="display: none;">
                    <i class="bi bi-grid-3x3-gap text-muted display-1"></i>
                    <h4 class="text-muted mt-3">Belum Ada Menu</h4>
                    <p class="text-muted">Mulai dengan menambahkan menu baru</p>
                </div>
            </div>

            <!-- Statistics Tab -->
            <div class="tab-pane fade" id="stats" role="tabpanel">
                <h4 class="mb-4">
                    <i class="bi bi-graph-up me-2"></i>Statistik & Laporan
                </h4>

                <!-- Revenue Summary -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Pesanan</h6>
                                <h3 id="totalOrdersStat">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Pendapatan</h6>
                                <h3 id="totalRevenueStat">Rp 0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title">Rata-rata per Pesanan</h6>
                                <h3 id="avgOrderStat">Rp 0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Orders Chart -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-day me-1"></i>Pesanan Hari Ini
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyOrdersChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Popular Menus -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-star me-1"></i>Menu Terpopuler
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="popularMenus">
                            <!-- Popular menus akan di-load dinamis -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt me-2"></i>Detail Pesanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailContent">
                    <!-- Content akan di-load dinamis -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Menu Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalTitle">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Menu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="menuForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="menuName" class="form-label">Nama Menu</label>
                                    <input type="text" class="form-control" id="menuName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="menuPrice" class="form-label">Harga</label>
                                    <input type="number" class="form-control" id="menuPrice" min="0" step="100" required>
                                </div>
                                <div class="mb-3">
                                    <label for="menuCategory" class="form-label">Kategori</label>
                                    <select class="form-select" id="menuCategory" required>
                                        <option value="">Pilih Kategori</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="menuImageURL" class="form-label">Link Gambar Menu</label>
                                    <input type="text" class="form-control" id="menuImageURL" placeholder="https://example.com/gambar.jpg">
                                    <div id="imagePreview" class="mt-2"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="menuStatus" checked>
                                        <label class="form-check-label" for="menuStatus">
                                            Tersedia
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="saveMenu()">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <!-- Alerts akan muncul di sini -->
    </div>

    <!-- Footer -->
    <footer class="bg-light border-top py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-cup-hot-fill me-2"></i>Warung Kafe</h5>
                    <p class="mb-0">Sistem antrian dan pesanan digital</p>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="config.js"></script>
    <script src="assets/js/script_admin.js"></script>
    <script>
        // Image preview handler
        document.getElementById('menuImage')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');

            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <div class="border rounded p-2 bg-light">
                                <h6 class="mb-2">Gambar Baru:</h6>
                                <img src="${e.target.result}" class="img-thumbnail mb-2" style="max-height: 100px; max-width: 200px;">
                                <br><small class="text-success">
                                    <i class="bi bi-check-circle"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)
                                </small>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '<small class="text-danger"><i class="bi bi-x-circle"></i> File harus berupa gambar</small>';
                }
            } else {
                // Jika file dihapus, tampilkan kembali gambar lama jika ada
                if (window.currentMenuImage) {
                    preview.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <img src="${window.currentMenuImage}" class="img-thumbnail" style="max-height: 80px;">
                            <div>
                                <small class="text-muted">Gambar saat ini</small><br>
                                <small class="text-info">Pilih file baru untuk mengubah</small>
                            </div>
                        </div>
                    `;
                } else {
                    preview.innerHTML = '<small class="text-muted">Tidak ada gambar</small>';
                }
            }
        });
    </script>
</body>
</html>