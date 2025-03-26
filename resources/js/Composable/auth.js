// composables/usePermissions.js
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';

export function useAuth() {
    const authStore = useAuthStore();

    const hasPermission = (permission) => {
        return computed(() => authStore.checkPermission(permission));
    };

    return {
        hasPermission
    };
}
