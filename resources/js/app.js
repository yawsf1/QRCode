import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { createPinia } from "pinia";
import MainLayout from "./Layouts/MainLayout.vue";
import "./echo";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const page = pages[`./Pages/${name}.vue`];

        page.default.layout = page.default.layout || MainLayout;

        if (!page) {
            console.error(`[Inertia] Page not found: "${name}"`);
            console.error("[Inertia] Available pages:", Object.keys(pages));
        }

        return page;
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .mount(el);
    },

    title: (title) => `${title} - Laravel`,
    progress: {
        color: "#4B5563",
    },
});
