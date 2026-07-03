import Swal from 'sweetalert2';

export default function () {
    return {
        // ---- Filter state ----
        academicYearId: '',
        semesterId: '',
        departmentId: '',
        programId: '',
        shiftId: '',
        groupId: '',
        sectionId: '',
        subjectId: '',
        date: '',

        // ---- Resolved session ----
        attendanceSessionId: null,

        // ---- Student data ----
        students: [],
        searchQuery: '',

        // ---- UI state ----
        loading: false,
        saving: false,
        saveStatusMap: {},
        previousStatusMap: {},
        saveTimeoutMap: {},

        // ---- Flatpickr instance ----
        datePicker: null,

        // ---- Tom Select instances ----
        tomSelectInstances: {},

        // ---- Init ----
        init() {
            this.$nextTick(() => {
                const selectors = [
                    'academicYearSelect', 'semesterSelect', 'departmentSelect',
                    'programSelect', 'shiftSelect', 'groupSelect',
                    'sectionSelect', 'subjectSelect',
                ];
                for (const ref of selectors) {
                    const el = this.$refs[ref];
                    if (!el) continue;
                    const ts = new TomSelect(el, {
                        plugins: ['dropdown_input'],
                        placeholder: el.options[0]?.text || 'Select...',
                    });
                    this.tomSelectInstances[ref] = ts;
                }
            });
        },

        // ---- Computed ----
        get summary() {
            const total = this.students.length;
            const present = this.students.filter(s => s.status === 'P').length;
            const absent = this.students.filter(s => s.status === 'A').length;
            const late = this.students.filter(s => s.status === 'L').length;
            const leave = this.students.filter(s => s.status === 'LV').length;
            const marked = present + absent + late + leave;
            const completionPercentage = total > 0 ? Math.round((marked / total) * 100) : 0;
            return { total, present, absent, late, leave, completionPercentage };
        },

        get filteredStudents() {
            if (!this.searchQuery) return this.students;
            const q = this.searchQuery.toLowerCase();
            return this.students.filter(s =>
                s.name.toLowerCase().includes(q) ||
                s.roll_no.toString().includes(q) ||
                s.id.toString().includes(q)
            );
        },

        get hasStudents() {
            return this.students.length > 0;
        },

        get isLoading() {
            return this.loading || this.saving;
        },

        // ---- Methods ----
        async loadStudents() {
            if (!this.academicYearId || !this.semesterId || !this.departmentId || !this.shiftId || !this.sectionId || !this.subjectId || !this.date) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please fill all required filters',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
                return;
            }

            this.loading = true;
            try {
                const res = await axios.post('/attendance/load-students', {
                    academic_year_id: this.academicYearId,
                    semester_id: this.semesterId,
                    department_id: this.departmentId,
                    program_id: this.programId || null,
                    shift_id: this.shiftId,
                    group_id: this.groupId || null,
                    section_id: this.sectionId,
                    subject_id: this.subjectId,
                    attendance_date: this.date,
                });

                const payload = res.data.data;
                const session = payload.session;
                const records = payload.records.data;

                this.attendanceSessionId = session.id;
                this.students = records.map(r => ({
                    id: r.student_id,
                    roll_no: r.roll_no ?? '',
                    name: r.student_name ?? 'Unknown',
                    photo: r.student_photo,
                    status: r.attendance_status || null,
                    remark: r.remark || '',
                    checked_at: r.checked_at,
                }));

                Object.values(this.saveTimeoutMap).forEach(t => clearTimeout(t));
                this.saveStatusMap = {};
                this.previousStatusMap = {};
                this.saveTimeoutMap = {};
                this.searchQuery = '';
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

        onStatusChange(student, newStatus) {
            if (this.saving) return;
            this.previousStatusMap[student.id] = student.status;
            student.status = newStatus;
            this.updateAttendance(student);
        },

        async updateAttendance(student) {
            this.saving = true;
            this.saveStatusMap[student.id] = 'saving';

            try {
                const res = await axios.post('/attendance/update', {
                    attendance_session_id: this.attendanceSessionId,
                    student_id: student.id,
                    attendance_status: student.status,
                    remark: student.remark || null,
                });

                const payload = res.data.data;

                clearTimeout(this.saveTimeoutMap[student.id]);
                this.saveStatusMap[student.id] = 'saved';
                student.checked_at = payload.attendance.checked_at;
                this.saveTimeoutMap[student.id] = setTimeout(() => {
                    if (this.saveStatusMap[student.id] === 'saved') {
                        this.saveStatusMap[student.id] = null;
                    }
                    delete this.saveTimeoutMap[student.id];
                }, 3000);
            } catch (err) {
                student.status = this.previousStatusMap[student.id] || null;
                this.saveStatusMap[student.id] = 'error';

                clearTimeout(this.saveTimeoutMap[student.id]);
                this.saveTimeoutMap[student.id] = setTimeout(() => {
                    if (this.saveStatusMap[student.id] === 'error') {
                        this.saveStatusMap[student.id] = null;
                    }
                    delete this.saveTimeoutMap[student.id];
                }, 5000);

                const msg = err.response?.data?.message || 'Failed to save attendance';
                Swal.fire({
                    icon: 'error',
                    title: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } finally {
                this.saving = false;
            }
        },

        async bulkUpdate(status) {
            if (this.students.length === 0 || this.saving) return;
            this.saving = true;
            try {
                const studentIds = this.students.map(s => s.id);
                await axios.post('/attendance/bulk-update', {
                    attendance_session_id: this.attendanceSessionId,
                    student_ids: studentIds,
                    attendance_status: status,
                });
                this.students.forEach(s => { s.status = status; });
                Object.values(this.saveTimeoutMap).forEach(t => clearTimeout(t));
                this.saveStatusMap = {};
                this.previousStatusMap = {};
                this.saveTimeoutMap = {};

                const labels = { P: 'Present', A: 'Absent', L: 'Late', LV: 'Leave' };
                Swal.fire({
                    icon: 'success',
                    title: `All marked ${labels[status]}`,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } catch (err) {
                const msg = err.response?.data?.message || 'Bulk update failed';
                Swal.fire({
                    icon: 'error',
                    title: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } finally {
                this.saving = false;
            }
        },

        async clearAttendance() {
            if (this.students.length === 0 || this.saving) return;

            const { isConfirmed } = await Swal.fire({
                title: 'Clear all?',
                text: 'All attendance marks and remarks will be cleared.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, clear all',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
            });

            if (!isConfirmed) return;

            this.students.forEach(s => {
                s.status = null;
                s.remark = '';
            });
            Object.values(this.saveTimeoutMap).forEach(t => clearTimeout(t));
            this.saveStatusMap = {};
            this.previousStatusMap = {};
            this.saveTimeoutMap = {};

            Swal.fire({
                icon: 'success',
                title: 'Cleared',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
            });
        },

        resetFilters() {
            for (const ts of Object.values(this.tomSelectInstances)) {
                ts.clear(true);
            }
            this.academicYearId = '';
            this.semesterId = '';
            this.departmentId = '';
            this.programId = '';
            this.shiftId = '';
            this.groupId = '';
            this.sectionId = '';
            this.subjectId = '';
            this.date = '';
            if (this.datePicker) this.datePicker.clear();
            this.attendanceSessionId = null;
            this.students = [];
            this.searchQuery = '';
            Object.values(this.saveTimeoutMap).forEach(t => clearTimeout(t));
            this.saveStatusMap = {};
            this.previousStatusMap = {};
            this.saveTimeoutMap = {};
        },

        getSaveStatusLabel(student) {
            const status = this.saveStatusMap[student.id];
            if (status === 'saving') return 'Saving...';
            if (status === 'saved') return 'Saved';
            if (status === 'error') return 'Error';
            if (!student.status) return 'Not Saved';
            return '';
        },
    };
}
