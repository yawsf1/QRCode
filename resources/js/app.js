import { createApp, h } from "vue";
import { createInertiaApp, router } from "@inertiajs/vue3";
import { createPinia } from "pinia";
import MainLayout from "./Layouts/MainLayout.vue";
import DashboardLayout from "./Layouts/DashboardLayout.vue";
import "./echo";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import { useUiStore } from "./stores/ui";
import {
    initFlashToasts,
    registerFlashRouter,
} from "./utils/flashToasts";

const DASHBOARD_LAYOUT_PAGES = new Set([
    "Employe/Dashboard",
    "Employe/Scanner",
    "Admin/Dashboard",
    "Admin/QrDashboard",
    "SuperAdmin/Dashboard",
]);

const toastOptions = {
    position: "bottom-left",
    transition: "Vue-Toastification__fade",
    maxToasts: 5,
    newestOnTop: true,
    timeout: 4000,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
};

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const page = pages[`./Pages/${name}.vue`];

        if (!page) {
            throw new Error(
                `[Inertia] Page not found: "${name}". Available: ${Object.keys(pages).join(", ")}`,
            );
        }

        if (!page.default.layout) {
            page.default.layout = DASHBOARD_LAYOUT_PAGES.has(name)
                ? DashboardLayout
                : MainLayout;
        }

        return page;
    },

    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(Toast, toastOptions);

        app.mount(el);

        initFlashToasts(toastOptions);
        registerFlashRouter();

        const ui = useUiStore(pinia);
        router.on("start", () => ui.setLoading(true));
        router.on("finish", () => ui.setLoading(false));
    },

    title: (title) => (title ? `${title} - QRCoded` : "QRCoded"),
    progress: {
        color: "#4f7cff",
        showSpinner: false,
    },
});
