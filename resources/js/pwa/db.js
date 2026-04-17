import { openDB } from 'idb';

const DB_NAME = 'elementary-learning-hub';
const DB_VERSION = 1;

let dbPromise = null;

export function getDb() {
    if (!dbPromise) {
        dbPromise = openDB(DB_NAME, DB_VERSION, {
            upgrade(db) {
                // Students store
                if (!db.objectStoreNames.contains('students')) {
                    const store = db.createObjectStore('students', { keyPath: 'id' });
                    store.createIndex('grade_level', 'grade_level');
                    store.createIndex('last_name', 'last_name');
                }

                // Subjects store
                if (!db.objectStoreNames.contains('subjects')) {
                    db.createObjectStore('subjects', { keyPath: 'id' });
                }

                // Attendance store (composite key: student_id + date)
                if (!db.objectStoreNames.contains('attendance')) {
                    const store = db.createObjectStore('attendance', { keyPath: 'localKey' });
                    store.createIndex('date', 'date');
                    store.createIndex('student_id', 'student_id');
                }

                // Grades store (composite key: student_id + subject_id + quarter)
                if (!db.objectStoreNames.contains('grades')) {
                    const store = db.createObjectStore('grades', { keyPath: 'localKey' });
                    store.createIndex('student_id', 'student_id');
                    store.createIndex('quarter', 'quarter');
                }

                // Pending sync queue
                if (!db.objectStoreNames.contains('pendingSync')) {
                    const store = db.createObjectStore('pendingSync', { keyPath: 'clientId' });
                    store.createIndex('modelType', 'modelType');
                    store.createIndex('createdAt', 'createdAt');
                }
            },
        });
    }
    return dbPromise;
}
