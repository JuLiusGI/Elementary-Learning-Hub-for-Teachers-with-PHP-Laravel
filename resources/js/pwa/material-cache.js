const CACHE_NAME = 'offline-materials';

/**
 * Download a learning material for offline access
 */
export async function downloadMaterial(materialId, url, title) {
    const cache = await caches.open(CACHE_NAME);
    const response = await fetch(url);
    if (!response.ok) throw new Error('Download failed');

    await cache.put(`/offline-material/${materialId}`, response.clone());

    // Track downloaded materials in localStorage
    const downloaded = getDownloadedMaterials();
    downloaded[materialId] = { title, url, cachedAt: new Date().toISOString() };
    localStorage.setItem('downloadedMaterials', JSON.stringify(downloaded));

    return true;
}

/**
 * Get a cached material response
 */
export async function getMaterial(materialId) {
    const cache = await caches.open(CACHE_NAME);
    return cache.match(`/offline-material/${materialId}`);
}

/**
 * Get list of all downloaded materials from localStorage
 */
export function getDownloadedMaterials() {
    return JSON.parse(localStorage.getItem('downloadedMaterials') || '{}');
}

/**
 * Check if a specific material is downloaded
 */
export function isMaterialDownloaded(materialId) {
    const downloaded = getDownloadedMaterials();
    return !!downloaded[materialId];
}

/**
 * Remove a cached material
 */
export async function removeMaterial(materialId) {
    const cache = await caches.open(CACHE_NAME);
    await cache.delete(`/offline-material/${materialId}`);

    const downloaded = getDownloadedMaterials();
    delete downloaded[materialId];
    localStorage.setItem('downloadedMaterials', JSON.stringify(downloaded));
}
