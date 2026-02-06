import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router  } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';  // For routes

function trackGA4PageView(url) {
    if (window.gtag && typeof window.gtag === 'function') {
        window.gtag('event', 'page_view', {
            page_title: document.title,
            page_location: window.location.href,
            page_path: url,
            send_to: window.GOOGLE_ANALYTICS_ID
        });
    }
}

createInertiaApp({
    title: title => `${title} - Feminaluxe`,
    resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        router.on('navigate', (event) => {
            // GA4
            if (window.gtag) {
                window.gtag('config', window.GOOGLE_ANALYTICS_ID, {
                    page_path: event.detail.page.url,
                    page_title: document.title
                });
            }

            // Яндекс.Метрика
            if (window.ym) {
                window.ym(window.YANDEX_METRIKA_ID, 'hit', event.detail.page.url);
            }
        });
        
        vueApp.mount(el);
        
        return vueApp;
    },
});
