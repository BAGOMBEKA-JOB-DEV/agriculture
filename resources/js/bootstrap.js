import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Set up axios defaults for API calls

axios.defaults.headers.common['Accept'] = 'application/json';

// Add request interceptor to include auth token
axios.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Add response interceptor to handle auth errors
axios.interceptors.response.use(
  response => response,
  error => {
    const isAuthRoute = error.config?.url?.includes('/login') || error.config?.url?.includes('/register');

    if (error.response?.status === 401 && !isAuthRoute) {
      // Clear invalid token and redirect to login
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];

      // Only redirect if not already on login/register to avoid loops and blinks
      if (!window.location.pathname.includes('/login') && !window.location.pathname.includes('/register')) {
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);
