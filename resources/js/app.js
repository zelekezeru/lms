import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, computed } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin).use(ZiggyVue);

        // Define a global computed property for authUser so that it is easily accessible everywhere
        app.config.globalProperties.authUser = computed(() => props.initialPage.props.auth?.user || null);
        
        // CHECK FOR PERMISSIONS

        // Check for the existence of ONLY one permission in the auth.user permissions list
        app.config.globalProperties.userCan = (permission) => {
            return props.initialPage.props.auth?.user.permissions.includes(permission);
        };
        
        // Check for the existence of AT LEAST ONE permission in the auth.user permissions list from a given lists of permissions
        app.config.globalProperties.userCanAny = (permissions) => {
            return permissions.some(permission => props.initialPage.props.auth?.user.permissions.includes(permission));
        };
        
        // Check for the existence of ALL permission in the auth.user permissions list from a given lists of permissions
        app.config.globalProperties.userCanAll = (permissions) => {
            return permissions.every(permission => props.initialPage.props.auth?.user.permissions.includes(permission));
        };

        // Check for the existence of ONLY one ROLE in the auth.user ROLEs list
        app.config.globalProperties.hasRole = (role) => {
            return props.initialPage.props.auth?.user.roles.includes(role);
        };

        // Check for the existence of AT LEAST ONE ROLE in the auth.user ROLEs list from a given lists of ROLEs
        app.config.globalProperties.hasAnyRole = (roles) => {
            return roles.some(role => props.initialPage.props.auth?.user.roles.includes(role));
        };

        // Check for the existence of AT LEAST ONE ROLE in the auth.user ROLEs list from a given lists of ROLEs
        app.config.globalProperties.hasAllRoles = (roles) => {
            return roles.every(role => props.initialPage.props.auth?.user.roles.includes(role));
        };

        return app.mount(el);
    },  
    progress: {
        color: 'green',
    },
});
