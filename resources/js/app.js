import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';  // For routes
import { Ziggy } from './ziggy';

import { route as ziggyRoute } from 'ziggy-js';
window.route = (...args) => ziggyRoute(...args).url();

createInertiaApp({
    title: title => `${title} - Feminaluxe`,
    resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Передаем маршруты в Ziggy
        const ziggyConfig = {
            ...Ziggy,
            location: new URL(Ziggy.url),
        };

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, ziggyConfig)
            .mount(el);
    },
});
