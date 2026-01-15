import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';

export function usePermissions() {
    const authStore = useAuthStore();

    /**
     * Check if the current user has a specific permission
     * @param {string} permission - The permission slug to check
     * @returns {boolean}
     */
    const can = (permission) => {
        return authStore.user?.permissions_list?.includes(permission) || false;
    };

    /**
     * Check if the current user has ANY of the specified permissions
     * @param {string[]} permissions - Array of permission slugs
     * @returns {boolean}
     */
    const canAny = (permissions) => {
        return permissions.some(p => can(p));
    };

    /**
     * Check if the current user has ALL of the specified permissions
     * @param {string[]} permissions - Array of permission slugs
     * @returns {boolean}
     */
    const canAll = (permissions) => {
        return permissions.every(p => can(p));
    };

    /**
     * Check if the current user has a specific role
     * @param {string} roleSlug - The role slug to check
     * @returns {boolean}
     */
    const hasRole = (roleSlug) => {
        return authStore.user?.roles?.some(r => r.slug === roleSlug) || false;
    };

    /**
     * Check if the current user is a super admin
     * @returns {boolean}
     */
    const isSuperAdmin = computed(() => {
        return hasRole('super_admin');
    });

    /**
     * Get all permissions for the current user
     * @returns {string[]}
     */
    const permissions = computed(() => {
        return authStore.user?.permissions_list || [];
    });

    /**
     * Ensure the user has a specific permission, otherwise notify and return false
     * @param {string} permission - The permission slug to check
     * @returns {boolean}
     */
    const ensure = (permission) => {
        if (can(permission)) return true;

        if (window.notify) {
            window.notify.accessDenied(permission);
        }
        return false;
    };

    return {
        can,
        ensure,
        canAny,
        canAll,
        hasRole,
        isSuperAdmin,
        permissions
    };
}
