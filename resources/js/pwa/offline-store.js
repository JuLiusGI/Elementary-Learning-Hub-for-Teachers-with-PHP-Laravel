import { getDb } from './db';

/**
 * Populate IndexedDB stores from server when online.
 * Called on page load when authenticated.
 */
export async function initOfflineStore() {
    if (!navigator.onLine) return;

    try {
        await Promise.all([
            syncStudents(),
            syncSubjects(),
        ]);
    } catch (e) {
        console.warn('Offline store init failed:', e);
    }
}

async function syncStudents() {
    const resp = await fetch('/api/offline/students');
    if (!resp.ok) return;

    const students = await resp.json();
    const db = await getDb();
    const tx = db.transaction('students', 'readwrite');
    await tx.store.clear();
    for (const s of students) {
        await tx.store.put(s);
    }
    await tx.done;
}

async function syncSubjects() {
    const resp = await fetch('/api/offline/subjects');
    if (!resp.ok) return;

    const subjects = await resp.json();
    const db = await getDb();
    const tx = db.transaction('subjects', 'readwrite');
    await tx.store.clear();
    for (const s of subjects) {
        await tx.store.put(s);
    }
    await tx.done;
}

/**
 * Read students from IndexedDB (for offline use)
 */
export async function getStudentsFromDb(gradeLevel) {
    const db = await getDb();
    if (gradeLevel) {
        return db.getAllFromIndex('students', 'grade_level', gradeLevel);
    }
    return db.getAll('students');
}

/**
 * Read subjects from IndexedDB (for offline use)
 */
export async function getSubjectsFromDb() {
    const db = await getDb();
    return db.getAll('subjects');
}

/**
 * Read attendance from IndexedDB for a specific date
 */
export async function getAttendanceFromDb(date) {
    const db = await getDb();
    return db.getAllFromIndex('attendance', 'date', date);
}
