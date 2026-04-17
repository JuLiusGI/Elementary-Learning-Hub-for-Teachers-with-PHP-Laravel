import { Workbox } from 'workbox-window';

export function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) return;

    const wb = new Workbox('/sw.js');

    wb.addEventListener('waiting', () => {
        // New SW waiting — auto-activate (small user base, safe to reload)
        wb.messageSkipWaiting();
    });

    wb.addEventListener('controlling', () => {
        // New SW took control — reload to get fresh assets
        window.location.reload();
    });

    wb.register().catch((err) => {
        console.warn('SW registration failed:', err);
    });
}
