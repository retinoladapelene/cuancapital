/**
 * API Service
 * Centralized HTTP client for Laravel API
 */


const API_URL = '/api';

const getHeaders = () => {
    const token = localStorage.getItem('auth_token');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!token) console.warn('[API] No Auth Token found in localStorage');
    // else console.log('[API] Attaching Token:', token.substring(0, 10) + '...');

    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
    };
};

const handleResponse = async (response, options = {}) => {
    const text = await response.text();
    let data;
    try {
        data = JSON.parse(text);
    } catch (e) {
        data = { message: text || response.statusText };
    }

    if (!response.ok) {
        if (response.status === 401 && !options.suppressAuthRedirect) {
            if (!window.location.pathname.includes('/login')) {
                console.warn("[API] 401 Unauthorized");
            }
        }

        let errorMessage = data.message || `API Request Failed (${response.status} ${response.statusText})`;

        // Handle Rate Limit specifically
        if (response.status === 429) {
            errorMessage = "Sistem sibuk karena terlalu banyak request. Mohon tunggu 1 menit.";
        }

        // Create error object with extra props
        const error = new Error(errorMessage);
        error.status = response.status;
        throw error;
    }
    return data;
};

const fetchWithAuth = async (endpoint, method, body = null, options = {}) => {
    const useApiPrefix = options.useApiPrefix === true; // Default false (Web Guard)
    const url = useApiPrefix ? `${API_URL}${endpoint}` : endpoint;

    const config = {
        method,
        headers: getHeaders(),
        credentials: 'same-origin' // Send cookies for Web Guard
    };

    if (body) config.body = JSON.stringify(body);

    const response = await fetch(url, config);
    return handleResponse(response, options);
};

export const api = {
    get: (endpoint, options = {}) => fetchWithAuth(endpoint, 'GET', null, options),
    post: (endpoint, body, options = {}) => fetchWithAuth(endpoint, 'POST', body, options),
    put: (endpoint, body, options = {}) => fetchWithAuth(endpoint, 'PUT', body, options),
    delete: (endpoint, options = {}) => fetchWithAuth(endpoint, 'DELETE', null, options)
};
