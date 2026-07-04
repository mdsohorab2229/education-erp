import Swal from 'sweetalert2';

export default function () {
    return {
        // ---- State ----
        examSubjectId: '',
        examSubject: null,
        students: [],
        loading: false,
        saving: false,
        saveStatusMap: {},
        saveTimers: {},

        // ---- Exam subject select ----
        async loadStudents() {
            if (!this.examSubjectId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please select an exam subject',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
                return;
            }

            this.loading = true;
            try {
                const res = await axios.get('/admin/marks/load-students', {
                    params: { exam_subject_id: this.examSubjectId },
                });
                const data = res.data.data;
                this.examSubject = data.exam_subject;
                this.students = data.marks.map(m => ({
                    id: m.id,
                    student_id: m.student?.id ?? null,
                    roll_no: m.student?.roll_no ?? '',
                    name: m.student?.name ?? 'Unknown',
                    photo: m.student?.photo ?? null,
                    obtained_mark: m.obtained_mark,
                    practical_mark: m.practical_mark,
                    viva_mark: m.viva_mark,
                    total_mark: m.total_mark,
                    grade: m.grade,
                    remark: m.remark ?? '',
                }));
                Object.values(this.saveTimers).forEach(t => clearTimeout(t));
                this.saveStatusMap = {};
                this.saveTimers = {};
            } catch (err) {
                const msg = err.response?.data?.message || 'Failed to load students';
                Swal.fire({
                    icon: 'error',
                    title: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } finally {
                this.loading = false;
            }
        },

        // ---- Auto-save with debounce ----
        onMarkChange(student) {
            if (this.saveTimers[student.id]) {
                clearTimeout(this.saveTimers[student.id]);
            }
            if (!this.saveStatusMap[student.id] || this.saveStatusMap[student.id] === 'saved') {
                this.saveStatusMap[student.id] = 'saving';
            }
            this.saveTimers[student.id] = setTimeout(() => {
                this.saveMark(student);
            }, 800);
        },

        async saveMark(student) {
            this.saving = true;
            this.saveStatusMap[student.id] = 'saving';
            try {
                const res = await axios.put(`/admin/marks/${student.id}`, {
                    obtained_mark: student.obtained_mark,
                    practical_mark: student.practical_mark,
                    viva_mark: student.viva_mark,
                    remark: student.remark,
                });
                const updated = res.data.data;
                student.total_mark = updated.total_mark;
                student.grade = updated.grade;
                this.saveStatusMap[student.id] = 'saved';
                clearTimeout(this.saveTimers[student.id]);
                this.saveTimers[student.id] = setTimeout(() => {
                    if (this.saveStatusMap[student.id] === 'saved') {
                        this.saveStatusMap[student.id] = null;
                    }
                    delete this.saveTimers[student.id];
                }, 3000);
            } catch (err) {
                this.saveStatusMap[student.id] = 'error';
                const msg = err.response?.data?.message || `Failed to save marks for ${student.name}`;
                Swal.fire({
                    icon: 'error',
                    title: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
                clearTimeout(this.saveTimers[student.id]);
                this.saveTimers[student.id] = setTimeout(() => {
                    if (this.saveStatusMap[student.id] === 'error') {
                        this.saveStatusMap[student.id] = null;
                    }
                    delete this.saveTimers[student.id];
                }, 5000);
            } finally {
                this.saving = false;
            }
        },

        // ---- Computed ----
        get summary() {
            const total = this.students.length;
            const entered = this.students.filter(s =>
                s.obtained_mark !== null || s.practical_mark !== null || s.viva_mark !== null
            ).length;
            const validMarks = this.students
                .map(s => this.calcStudentTotal(s))
                .filter(v => v > 0);
            const average = validMarks.length > 0
                ? (validMarks.reduce((a, b) => a + b, 0) / validMarks.length)
                : 0;
            const highest = validMarks.length > 0 ? Math.max(...validMarks) : 0;
            const completion = total > 0 ? Math.round((entered / total) * 100) : 0;
            return { total, entered, average, highest, completion };
        },

        // ---- Helpers ----
        calcStudentTotal(student) {
            const t = parseFloat(student.obtained_mark) || 0;
            const p = parseFloat(student.practical_mark) || 0;
            const v = parseFloat(student.viva_mark) || 0;
            return t + p + v;
        },

        getSaveStatusLabel(student) {
            const status = this.saveStatusMap[student.id];
            if (status === 'saving') return 'Saving...';
            if (status === 'saved') return 'Saved';
            if (status === 'error') return 'Failed';
            return '';
        },

        getAvatarUrl(student) {
            if (student.photo) return student.photo;
            return `https://ui-avatars.com/api/?name=${encodeURIComponent(student.name)}&size=40&rounded=true&background=f0f0f0&color=333`;
        },
    };
}
