import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { Head } from '@inertiajs/vue3';

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

        // Register Head globally
        app.component('Head', Head);

        // Define a global getter for authUser that always reads from the reactive $page property
        Object.defineProperty(app.config.globalProperties, 'authUser', {
            get() {
                return this.$page.props.auth?.user || null;
            },
        });

        // Define global functions that use this.$page so they update when auth changes
        app.config.globalProperties.userCan = function(permission) {
            return this.$page.props.auth?.user?.permissions?.includes(permission) || false;
        };

        app.config.globalProperties.userCanAny = function(permissions) {
            const userPermissions = this.$page.props.auth?.user?.permissions;
            return userPermissions
                ? permissions.some(permission => userPermissions.includes(permission))
                : false;
        };

        app.config.globalProperties.userCanAll = function(permissions) {
            const userPermissions = this.$page.props.auth?.user?.permissions;
            return userPermissions
                ? permissions.every(permission => userPermissions.includes(permission))
                : false;
        };

        app.config.globalProperties.hasRole = function(role) {
            return this.$page.props.auth?.user?.roles?.includes(role) || false;
        };

        app.config.globalProperties.hasAnyRole = function(roles) {
            const userRoles = this.$page.props.auth?.user?.roles;
            return userRoles
                ? roles.some(role => userRoles.includes(role))
                : false;
        };

        app.config.globalProperties.hasAllRoles = function(roles) {
            const userRoles = this.$page.props.auth?.user?.roles;
            return userRoles
                ? roles.every(role => userRoles.includes(role))
                : false;
        };

        return app.mount(el);
    },
    progress: {
        color: 'green',
    },
});
