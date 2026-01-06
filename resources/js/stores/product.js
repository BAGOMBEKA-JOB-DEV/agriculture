import { defineStore } from 'pinia';
import axios from 'axios';

export const useProductStore = defineStore('product', {
    state: () => ({
        products: [],
        categories: [],
        currentProduct: null,
        loading: false,
        error: null,
    }),

    actions: {
        async fetchProducts(filters = {}) {
            this.loading = true;
            try {
                const response = await axios.get('/products', { params: filters });
                this.products = response.data.data || response.data;
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch products';
                return { success: false, message: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchProduct(id) {
            this.loading = true;
            try {
                const response = await axios.get(`/products/${id}`);
                this.currentProduct = response.data;
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch product';
                return { success: false, message: this.error };
            } finally {
                this.loading = false;
            }
        },

        async createProduct(productData) {
            try {
                const response = await axios.post('/vendor/products', productData);
                this.products.unshift(response.data);
                return { success: true, data: response.data };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Failed to create product',
                    errors: error.response?.data?.errors || {}
                };
            }
        },

        async updateProduct(id, productData) {
            try {
                const response = await axios.put(`/vendor/products/${id}`, productData);
                const index = this.products.findIndex(p => p.id === id);
                if (index !== -1) {
                    this.products[index] = response.data;
                }
                return { success: true, data: response.data };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Failed to update product',
                    errors: error.response?.data?.errors || {}
                };
            }
        },

        async deleteProduct(id) {
            try {
                await axios.delete(`/vendor/products/${id}`);
                this.products = this.products.filter(p => p.id !== id);
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Failed to delete product'
                };
            }
        },

        async fetchCategories() {
            try {
                const response = await axios.get('/categories');
                this.categories = response.data;
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Failed to fetch categories'
                };
            }
        },
    },
});
