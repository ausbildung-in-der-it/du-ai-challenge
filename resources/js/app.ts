import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import posthog from 'posthog-js';

posthog.init(import.meta.env.VITE_POSTHOG_PROJECT_TOKEN || '', {
    api_host: import.meta.env.VITE_POSTHOG_HOST || '',
    cookieless_mode: 'always',
    defaults: '2026-01-30',
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) }).use(plugin);

        app.config.errorHandler = (err) => {
            posthog.captureException(err);
        };

        app.mount(el!);
    },
});
