import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// PWA modules — must register before Alpine.start() so alpine:init listeners fire
import { registerServiceWorker } from './pwa/register-sw';
import { initNetworkStatus } from './pwa/network-status';
import { initInstallPrompt } from './pwa/install-prompt';
import { initOfflineForms } from './pwa/offline-forms';
import { initOfflineStore } from './pwa/offline-store';
import { updatePendingCount } from './pwa/sync-manager';
import { downloadMaterial, isMaterialDownloaded } from './pwa/material-cache';

// Initialize PWA features that hook into alpine:init
initNetworkStatus();
initInstallPrompt();
initOfflineForms();

// Expose material cache helpers globally for Blade templates
window.downloadMaterialForOffline = downloadMaterial;
window.isMaterialDownloaded = isMaterialDownloaded;

Alpine.start();

// Register Service Worker
registerServiceWorker();

// Populate IndexedDB with server data when online and authenticated
if (window.__APP_USER__) {
    initOfflineStore();
    updatePendingCount();
}
