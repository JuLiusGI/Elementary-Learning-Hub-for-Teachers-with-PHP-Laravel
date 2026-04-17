/**
 * Network status detection + Alpine.js store
 * Provides $store.network with online/offline state,
 * pending sync count, and syncing indicator.
 */
export function initNetworkStatus() {
    document.addEventListener('alpine:init', () => {
        Alpine.store('network', {
            online: navigator.onLine,
            pendingCount: 0,
            syncing: false,
        });

        window.addEventListener('online', () => {
            Alpine.store('network').online = true;
            // Trigger sync when coming back online
            import('./sync-manager').then((m) => m.syncPendingItems());
        });

        window.addEventListener('offline', () => {
            Alpine.store('network').online = false;
        });
    });
}
