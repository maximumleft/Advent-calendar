import { ref, computed } from 'vue';
import { authService } from '../services/api';
import router from '../router';

const user = ref(null);
const loading = ref(false);
const initialized = ref(false);

export function useAuth() {
    const isAuthenticated = computed(() => !!user.value);

    const init = async () => {
        if (initialized.value) return;
        loading.value = true;
        try {
            const res = await authService.me();
            user.value = res.data;
        } catch (err) {
            user.value = null;
        } finally {
            loading.value = false;
            initialized.value = true;
        }
    };

    const login = async (credentials) => {
        loading.value = true;
        try {
            await authService.csrf();
            const res = await authService.login(credentials);
            user.value = res.data.user;
            router.push('/');
        } catch (err) {
            throw err.response?.data?.message || 'Login failed';
        } finally {
            loading.value = false;
        }
    };

    const register = async (data) => {
        loading.value = true;
        try {
            await authService.csrf();
            const res = await authService.register(data);
            user.value = res.data.user;
            router.push('/');
        } catch (err) {
            throw err.response?.data?.errors || { message: 'Registration failed' };
        } finally {
            loading.value = false;
        }
    };

    const logout = async () => {
        try {
            await authService.logout();
            user.value = null;
            router.push('/login');
        } catch (err) {
            console.error(err);
        }
    };

    return {
        user,
        loading,
        isAuthenticated,
        initialized,
        init,
        login,
        register,
        logout
    };
}
