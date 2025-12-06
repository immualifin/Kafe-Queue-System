<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Kafe - Pilih Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-cup-hot-fill me-2"></i>Warung Kafe
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="display_antrian.php">
                            <i class="bi bi-list-check me-1"></i>Lihat Antrian
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section py-5" style="background: linear-gradient(135deg, #006241 0%, #1e3932 100%); color: white !important;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3" style="color: white !important;">Selamat Datang di Warung Kafe</h1>
                    <p class="lead mb-4" style="color: white !important;">Nikmati berbagai pilihan menu lezat dengan harga terjangkau</p>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="bi bi-cup-hot display-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Badge -->
    <div class="container">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Menu Kami</h3>
                    <button class="btn btn-warning position-relative" onclick="showCart()">
                        <i class="bi bi-cart3 me-1"></i>Keranjang
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
                            0
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Grid -->
    <div class="container mb-5">
        <div id="loading" class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat menu...</p>
        </div>

        <div id="menu-container" class="d-none">
            <!-- Menu akan di-load dinamis via JavaScript -->
        </div>
    </div>

    <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-check me-2"></i>Konfirmasi Pesanan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm">
                        <div class="mb-3">
                            <label for="namaPelanggan" class="form-label">
                                <i class="bi bi-person me-1"></i>Nama Lengkap
                            </label>
                            <input type="text" class="form-control" id="namaPelanggan" required>
                        </div>
                        <div class="mb-3">
                            <label for="noMeja" class="form-label">
                                <i class="bi bi-grid-3x3-gap me-1"></i>Nomor Meja
                            </label>
                            <select class="form-control" id="noMeja" required>
                                <option value="">Pilih Meja</option>
                                <option value="Meja 1">Meja 1</option>
                                <option value="Meja 2">Meja 2</option>
                                <option value="Meja 3">Meja 3</option>
                                <option value="Meja 4">Meja 4</option>
                                <option value="Meja 5">Meja 5</option>
                                <option value="Meja 6">Meja 6</option>
                                <option value="Meja 7">Meja 7</option>
                                <option value="Meja 8">Meja 8</option>
                                <option value="Take Away">Take Away</option>
                            </select>
                        </div>
                        <div class="cart-summary mb-3 p-3 bg-light rounded">
                            <h6 class="mb-2">Ringkasan Pesanan:</h6>
                            <div id="orderSummary"></div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span id="totalOrder"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="submitOrder()">
                        <i class="bi bi-send me-1"></i>Pesan Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-cart3 me-2"></i>Keranjang Belanja
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="cartContent">
                        <!-- Cart content akan di-load dinamis -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" onclick="checkoutCart()" id="checkoutBtn" disabled>
                        <i class="bi bi-clipboard-check me-1"></i>Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-check-circle-fill text-success display-1 mb-3"></i>
                    <h4>Pesanan Berhasil!</h4>
                    <p class="mb-3">Pesanan Anda telah diterima dan sedang diproses.</p>
                    <div id="orderDetails" class="text-start mb-3">
                        <!-- Order details akan ditampilkan di sini -->
                    </div>
                    <button type="button" class="btn btn-success" onclick="redirectToAntrian()">
                        <i class="bi bi-arrow-right-circle me-1"></i>Lihat Status Pesanan
                    </button>
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
    <script src="assets/js/script_public.js"></script>
</body>
</html>