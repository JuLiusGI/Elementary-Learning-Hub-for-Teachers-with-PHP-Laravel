import { queueAttendance, queueGrade } from './sync-manager';

/**
 * Register Alpine magic helpers for offline form submission.
 * These intercept form submits when offline and queue data to IndexedDB.
 */
export function initOfflineForms() {
    document.addEventListener('alpine:init', () => {
        /**
         * $offlineSubmitAttendance(data) — returns true if saved offline, false if online
         * data shape: { date: 'YYYY-MM-DD', attendance: [{ student_id, status, time_in, remarks }] }
         */
        Alpine.magic('offlineSubmitAttendance', () => {
            return async (data) => {
                if (navigator.onLine) return false;

                for (const entry of data.attendance) {
                    await queueAttendance({
                        student_id: entry.student_id,
                        date: data.date,
                        status: entry.status,
                        time_in: entry.time_in || null,
                        remarks: entry.remarks || null,
                        recorded_by: window.__APP_USER__?.id,
                    });
                }

                return true;
            };
        });

        /**
         * $offlineSubmitGrades(data) — returns true if saved offline, false if online
         * data shape: { subject_id, quarter, grades: [{ student_id, ww_total_score, ww_max_score, ... }] }
         */
        Alpine.magic('offlineSubmitGrades', () => {
            return async (data) => {
                if (navigator.onLine) return false;

                for (const entry of data.grades) {
                    // Skip empty entries
                    if (!entry.ww_total_score && !entry.pt_total_score && !entry.qa_score) continue;

                    await queueGrade({
                        student_id: entry.student_id,
                        subject_id: data.subject_id,
                        quarter: data.quarter,
                        ww_total_score: entry.ww_total_score || null,
                        ww_max_score: entry.ww_max_score || null,
                        pt_total_score: entry.pt_total_score || null,
                        pt_max_score: entry.pt_max_score || null,
                        qa_score: entry.qa_score || null,
                        qa_max_score: entry.qa_max_score || null,
                    });
                }

                return true;
            };
        });
    });
}
