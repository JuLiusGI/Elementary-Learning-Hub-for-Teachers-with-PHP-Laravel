import { getDb } from './db';

/**
 * Generate a UUID for client_id deduplication
 */
function uuid() {
    return crypto.randomUUID();
}

/**
 * Get CSRF token from meta tag
 */
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}

/**
 * Queue an attendance entry for offline sync
 */
export async function queueAttendance(entry) {
    const db = await getDb();

    // Save to local attendance store for immediate reading
    const localKey = `${entry.student_id}_${entry.date}`;
    await db.put('attendance', { ...entry, localKey });

    // Queue for sync to server
    await db.put('pendingSync', {
        clientId: uuid(),
        modelType: 'attendance',
        action: 'create',
        payload: entry,
        createdAt: new Date().toISOString(),
    });

    await updatePendingCount();
}

/**
 * Queue a grade entry for offline sync
 */
export async function queueGrade(entry) {
    const db = await getDb();

    const localKey = `${entry.student_id}_${entry.subject_id}_${entry.quarter}`;
    await db.put('grades', { ...entry, localKey });

    await db.put('pendingSync', {
        clientId: uuid(),
        modelType: 'grades',
        action: 'create',
        payload: entry,
        createdAt: new Date().toISOString(),
    });

    await updatePendingCount();
}

/**
 * Sync all pending items to server
 */
export async function syncPendingItems() {
    if (!navigator.onLine) return;

    const store = Alpine.store('network');
    if (!store || store.syncing) return;
    store.syncing = true;

    try {
        const db = await getDb();
        const pending = await db.getAll('pendingSync');

        if (pending.length === 0) {
            store.syncing = false;
            return;
        }

        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        };

        // Group by model type
        const attendanceItems = pending.filter((p) => p.modelType === 'attendance');
        const gradeItems = pending.filter((p) => p.modelType === 'grades');

        // Sync attendance
        if (attendanceItems.length > 0) {
            const resp = await fetch('/api/sync/attendance', {
                method: 'POST',
                headers,
                body: JSON.stringify({
                    items: attendanceItems.map((i) => ({
                        client_id: i.clientId,
                        payload: i.payload,
                        client_timestamp: i.createdAt,
                    })),
                }),
            });

            if (resp.ok) {
                const tx = db.transaction('pendingSync', 'readwrite');
                for (const item of attendanceItems) {
                    await tx.store.delete(item.clientId);
                }
                await tx.done;
            }
        }

        // Sync grades
        if (gradeItems.length > 0) {
            const resp = await fetch('/api/sync/grades', {
                method: 'POST',
                headers,
                body: JSON.stringify({
                    items: gradeItems.map((i) => ({
                        client_id: i.clientId,
                        payload: i.payload,
                        client_timestamp: i.createdAt,
                    })),
                }),
            });

            if (resp.ok) {
                const tx = db.transaction('pendingSync', 'readwrite');
                for (const item of gradeItems) {
                    await tx.store.delete(item.clientId);
                }
                await tx.done;
            }
        }
    } catch (e) {
        console.error('Sync failed:', e);
    } finally {
        store.syncing = false;
        await updatePendingCount();
    }
}

/**
 * Update the pending count in the Alpine store
 */
export async function updatePendingCount() {
    try {
        const db = await getDb();
        const count = await db.count('pendingSync');
        if (typeof Alpine !== 'undefined' && Alpine.store('network')) {
            Alpine.store('network').pendingCount = count;
        }
    } catch (e) {
        // Silently fail — store might not be initialized yet
    }
}
