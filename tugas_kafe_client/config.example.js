// Konfigurasi koneksi ke server API
const API_CONFIG = {
    // Ganti dengan IP address atau domain server Anda
    // Jika server di komputer yang sama (localhost):
    // SERVER_URL: 'http://localhost/tugas_kafe_server',

    // Jika server di komputer lain, ganti dengan IP server:
    // SERVER_URL: 'http://192.168.1.XXX/tugas_kafe_server',
    // Contoh:
    SERVER_URL: 'http://192.168.1.10/tugas_kafe_server',  // Ganti 192.168.1.10 dengan IP server Anda

    // API Endpoints
    ENDPOINTS: {
        AUTH: {
            LOGIN: '/api/auth/login.php',
            CHECK_SESSION: '/api/auth/check_session.php'
        },
        MENUS: {
            READ: '/api/menus/read.php',
            CREATE: '/api/menus/create.php',
            UPDATE: '/api/menus/update.php',
            DELETE: '/api/menus/delete.php'
        },
        ORDERS: {
            READ: '/api/orders/read.php',
            CREATE: '/api/orders/create.php',
            UPDATE_STATUS: '/api/orders/update_status.php'
        }
    }
};

// Helper function untuk membuat API URL
function getApiUrl(endpoint) {
    return API_CONFIG.SERVER_URL + endpoint;
}

// Helper function untuk API request
async function apiRequest(endpoint, options = {}) {
    const url = getApiUrl(endpoint);
    const config = {
        headers: {
            'Content-Type': 'application/json',
            ...options.headers
        },
        ...options
    };

    // Add authorization header if session token exists
    const sessionToken = localStorage.getItem('session_token');
    if (sessionToken) {
        config.headers.Authorization = 'Bearer ' + sessionToken;
    }

    try {
        const response = await fetch(url, config);
        return await response.json();
    } catch (error) {
        console.error('API Request Error:', error);
        throw error;
    }
}

// Export untuk digunakan di script lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { API_CONFIG, getApiUrl, apiRequest };
} else {
    window.API_CONFIG = API_CONFIG;
    window.getApiUrl = getApiUrl;
    window.apiRequest = apiRequest;
}