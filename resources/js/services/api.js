import axios from 'axios';

const getApiBaseUrl = () => {
    let url = import.meta.env.VITE_API_BASE_URL;
    // Check if the variable wasn't expanded properly by Vite
    if (!url || url.startsWith('${')) {
        // Fallback for development if .env expansion failed
        return 'http://bcrypt-password-hash-verifier.test/api';
    }
    return url;
};

const API_BASE_URL = getApiBaseUrl();

const apiClient = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    timeout: 10000,
});

// Request interceptor
apiClient.interceptors.request.use(
    (config) => {
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor
apiClient.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        const message = error.response?.data?.message || 'An error occurred';
        return Promise.reject({
            message,
            status: error.response?.status,
            errors: error.response?.data?.data?.errors || {},
        });
    }
);

export default apiClient;

// Password API methods
export const passwordApi = {
    generate: (config) => apiClient.post('/password/generate', config),
    hash: (password, rounds = 10) => apiClient.post('/password/hash', { password, rounds }),
    verify: (password, hash) => apiClient.post('/password/verify', { password, hash }),
};
