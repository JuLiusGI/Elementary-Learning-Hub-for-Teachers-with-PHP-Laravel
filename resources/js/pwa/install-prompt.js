/**
 * PWA install prompt handler.
 * Captures the beforeinstallprompt event and exposes it
 * via Alpine store $store.pwa for the install button.
 */

let deferredPrompt = null;

export function initInstallPrompt() {
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;

        // Show install button via Alpine store
        if (typeof Alpine !== 'undefined' && Alpine.store('pwa')) {
            Alpine.store('pwa').canInstall = true;
        }
    });

    document.addEventListener('alpine:init', () => {
        Alpine.store('pwa', {
            canInstall: false,
            async install() {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                const result = await deferredPrompt.userChoice;
                if (result.outcome === 'accepted') {
                    this.canInstall = false;
                }
                deferredPrompt = null;
            },
        });
    });
}
