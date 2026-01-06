import { defineStore } from 'pinia';
import axios from 'axios';

export const useSubscriptionStore = defineStore('subscription', {
    state: () => ({
        plans: [],
        userSubscriptions: [],
        currentSubscription: null,
        loading: false,
    }),

    getters: {
        hasActiveSubscription: (state) => {
            return state.userSubscriptions.some(
                sub => sub.status === 'active' && new Date(sub.ends_at) > new Date()
            );
        },
        activeSubscription: (state) => {
            return state.userSubscriptions.find(
                sub => sub.status === 'active' && new Date(sub.ends_at) > new Date()
            );
        },
    },

    actions: {
        async fetchPlans() {
            this.loading = true;
            try {
                const response = await axios.get('/subscription-plans');
                this.plans = response.data;
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Failed to fetch plans'
                };
            } finally {
                this.loading = false;
            }
        },

        async fetchUserSubscriptions() {
            this.loading = true;
            try {
                const response = await axios.get('/subscriptions');
                this.userSubscriptions = response.data;
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Failed to fetch subscriptions'
                };
            } finally {
                this.loading = false;
            }
        },

        async subscribe(planId) {
            try {
                const response = await axios.post(`/subscriptions/${planId}/subscribe`);
                this.userSubscriptions.unshift(response.data);
                return { success: true, data: response.data };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Failed to subscribe'
                };
            }
        },

        async cancelSubscription(subscriptionId) {
            try {
                const response = await axios.post(`/subscriptions/${subscriptionId}/cancel`);
                const index = this.userSubscriptions.findIndex(s => s.id === subscriptionId);
                if (index !== -1) {
                    this.userSubscriptions[index] = response.data;
                }
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Failed to cancel subscription'
                };
            }
        },
    },
});
