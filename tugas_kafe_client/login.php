<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Warung Kafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin: 20px;
        }

        .brand-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-icon">
                    <i class="bi bi-cup-hot-fill text-white display-4"></i>
                </div>
                <h2 class="fw-bold text-dark">Warung Kafe</h2>
                <p class="text-muted">Panel Administrator</p>
            </div>

            <form id="loginForm">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-person me-1"></i>Username
                    </label>
                    <input type="text" class="form-control form-control-lg" id="username" required>
                    <div class="invalid-feedback">Username harus diisi</div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i>Password
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-lg" id="password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">Password harus diisi</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login text-white" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </button>
                </div>
            </form>

            <div id="alertContainer" class="mt-3"></div>

            <hr class="my-4">

            <div class="text-center">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Demo: admin / admin123 atau kasir1 / kasir123
                </small>
            </div>

            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-link text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
         style="background: rgba(0,0,0,0.5); z-index: 9999; display: none !important;">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5>Sedang login...</h5>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="config.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        });

        // Show alert
        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alertContainer');
            const alertId = 'alert-' + Date.now();

            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert" id="${alertId}">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-1"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            alertContainer.innerHTML = alertHtml;

            // Auto remove after 5 seconds
            setTimeout(() => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }

        // Show/hide loading
        function toggleLoading(show) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            const loginBtn = document.getElementById('loginBtn');

            if (show) {
                loadingOverlay.style.display = 'flex !important';
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
            } else {
                loadingOverlay.style.display = 'none !important';
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i>Login';
            }
        }

        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            // Validate form
            let isValid = true;

            if (!username) {
                document.getElementById('username').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('username').classList.remove('is-invalid');
            }

            if (!password) {
                document.getElementById('password').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('password').classList.remove('is-invalid');
            }

            if (!isValid) {
                return;
            }

            toggleLoading(true);

            try {
                const result = await apiRequest(API_CONFIG.ENDPOINTS.AUTH.LOGIN, {
                    method: 'POST',
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });

                if (result.success) {
                    // Store session data
                    localStorage.setItem('user', JSON.stringify(result.data.user));
                    localStorage.setItem('session_token', result.data.session.token);
                    localStorage.setItem('login_time', result.data.login_time);

                    showAlert('Login berhasil! Mengarahkan ke dashboard...', 'success');

                    // Redirect to dashboard after delay
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1500);
                } else {
                    showAlert(result.message || 'Login gagal');
                }
            } catch (error) {
                console.error('Login error:', error);
                showAlert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                toggleLoading(false);
            }
        });

        // Check if already logged in
        window.addEventListener('load', function() {
            const sessionToken = localStorage.getItem('session_token');
            if (sessionToken) {
                // Verify session is still valid
                apiRequest(API_CONFIG.ENDPOINTS.AUTH.CHECK_SESSION)
                .then(result => {
                    if (result.success && result.data.is_valid) {
                        window.location.href = 'dashboard.php';
                    } else {
                        // Clear invalid session
                        localStorage.removeItem('user');
                        localStorage.removeItem('session_token');
                        localStorage.removeItem('login_time');
                    }
                })
                .catch(error => {
                    console.error('Session check error:', error);
                    // Clear session on error
                    localStorage.removeItem('user');
                    localStorage.removeItem('session_token');
                    localStorage.removeItem('login_time');
                });
            }
        });

        // Clear form on page load
        document.getElementById('loginForm').reset();
    </script>
</body>
</html>