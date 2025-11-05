import './bootstrap';

import Echo from 'laravel-echo';

window.Pusher = undefined;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
    wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws','wss'],
});

// Listen for contribution updates on pages that include a contribution container
document.addEventListener('DOMContentLoaded', () => {
    const el = document.querySelector('[data-contribution-id]');
    if (!el) return;
    const id = el.getAttribute('data-contribution-id');
    if (!id) return;

    window.Echo.private(`contribution.${id}`)
        .listen('.ContributionPaymentCreated', (e) => {
            const totalEl = document.querySelector('[data-collected-amount]');
            if (totalEl && e.total !== undefined) {
                totalEl.textContent = Number(e.total).toFixed(2);
            }
        });
});
