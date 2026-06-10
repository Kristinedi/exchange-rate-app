import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export const useAuth = () => {
    const page = usePage();

    return {
        user: computed(() => page.props.auth.user),
        isAuthenticated: computed(() => !!page.props.auth.user),
    };
};
