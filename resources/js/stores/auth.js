import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        initialized: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,
        permissions: (state) => {
            const perms = new Set();
            state.user?.roles?.forEach(role => {
                role.permissions?.forEach(p => perms.add(p.slug));
            });
            return perms;
        },
        // Legacy getters now using Dynamic Permissions
        isVendor: (state) => {
            // Check if user has vendor permission OR legacy role string as fallback (during transition)
            const perms = new Set();
            state.user?.roles?.forEach(role => {
                role.permissions?.forEach(p => perms.add(p.slug));
            });
            return perms.has('access_vendor') || state.user?.role === 'vendor';
        },
        isAdmin: (state) => {
            const perms = new Set();
            state.user?.roles?.forEach(role => {
                role.permissions?.forEach(p => perms.add(p.slug));
            });
            return perms.has('access_admin') || state.user?.role === 'admin';
        },
        isBuyer: (state) => state.user?.role === 'user', // Basic user role usually doesn't have special permissions yet
        hasActiveSubscription: (state) => {
            if (!state.user) return false;
            return state.user.has_active_subscription || false;
        },
    },

    actions: {
        async checkAuth() {
            if (!this.token) {
                this.initialized = true;
                return false;
            }

            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
                this.initialized = true;
                return true;
            } catch (error) {
                this.logout();
                this.initialized = true;
                return false;
            }
        },

        async login(credentials) {
            try {
                const response = await axios.post('/api/login', credentials);
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('token', this.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Login failed',
                    errors: error.response?.data?.errors || {}
                };
            }
        },

        async register(userData) {
            try {
                const response = await axios.post('/api/register', userData);
                // We no longer log the user in automatically after registration
                return { success: true, message: response.data.message };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Registration failed',
                    errors: error.response?.data?.errors || {}
                };
            }
        },

        async logout() {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem('token');
                delete axios.defaults.headers.common['Authorization'];
            }
        },

        async updateProfile(profileData) {
            try {
                const response = await axios.put('/api/user/profile', profileData);
                this.user = response.data;
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Profile update failed',
                    errors: error.response?.data?.errors || {}
                };
            }
        },

        can(permission) {
            return this.permissions.has(permission);
        }
    },
});
