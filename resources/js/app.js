import "../css/app.css";
import "./bootstrap";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { Head } from "@inertiajs/vue3";
import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";

import { createI18n } from "vue-i18n";
import en from "./lang/en.json";
import am from "./lang/am.json";
import ar from "./lang/ar.json";
import es from "./lang/es.json";
import om from "./lang/om.json";
import sw from "./lang/sw.json";
import ur from "./lang/ur.json";
import zh from "./lang/zh.json";
import fr from "./lang/fr.json";
import ti from "./lang/ti.json";

const savedLocale = localStorage.getItem("locale") || "en";

const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: savedLocale,
    fallbackLocale: "en",
    messages: {
        en,
        am,
        ar,
        es,
        om,
        sw,
        ur,
        zh,
        fr,
        ti,
    },
});

const appName = import.meta.env.VITE_APP_NAME || "LMS";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: ".dark",
                    },
                },
            })
            .use(i18n);

        app.component("Head", Head);

        Object.defineProperty(app.config.globalProperties, "authUser", {
            get() {
                return this.$page.props.auth?.user || null;
            },
        });

        app.config.globalProperties.userCan = function (permission) {
            return (
                this.$page.props.auth?.user?.permissions?.includes(
                    permission
                ) || false
            );
        };

        app.config.globalProperties.userCanAny = function (permissions) {
            const userPermissions = this.$page.props.auth?.user?.permissions;
            return userPermissions
                ? permissions.some((permission) =>
                      userPermissions.includes(permission)
                  )
                : false;
        };

        app.config.globalProperties.userCanAll = function (permissions) {
            const userPermissions = this.$page.props.auth?.user?.permissions;
            return userPermissions
                ? permissions.every((permission) =>
                      userPermissions.includes(permission)
                  )
                : false;
        };

        app.config.globalProperties.hasRole = function (role) {
            return this.$page.props.auth?.user?.roles?.includes(role) || false;
        };

        app.config.globalProperties.hasAnyRole = function (roles) {
            const userRoles = this.$page.props.auth?.user?.roles;
            return userRoles
                ? roles.some((role) => userRoles.includes(role))
                : false;
        };

        app.config.globalProperties.hasAllRoles = function (roles) {
            const userRoles = this.$page.props.auth?.user?.roles;
            return userRoles
                ? roles.every((role) => userRoles.includes(role))
                : false;
        };

        return app.mount(el);
    },
    progress: {
        color: "green",
    },
});
